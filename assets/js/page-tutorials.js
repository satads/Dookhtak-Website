/* ============================================================
   Tutorials SPA — Phase 4: data comes from the site API
   (/api/tutorials.php index + /api/tutorial.php fragments), with
   every Phase-1 behavior preserved: hash deep links, accordion
   sidebar, live search, quick-start path, fragment loading with
   cache/skeleton/error-retry, prev-next nav, delegated fragment
   clicks and the mobile list drawer.
   ============================================================ */
let CATEGORIES = [];
let TUTORIALS = [];
let QUICK_START = [];

const state = { q: '', current: null, open: {}, listDrawer: false };

const cache = {};        // loaded fragment cache
let loadToken = 0;       // guards against out-of-order paints
let contentMode = null;  // 'welcome' | 'tut'

const faNum = DK.faNum;

function esc(s) {
  return String(s == null ? '' : s)
    .replace(/&/g, '&amp;').replace(/</g, '&lt;')
    .replace(/>/g, '&gt;').replace(/"/g, '&quot;');
}

/* Search normalization: ZWNJ -> space, lowercase. */
function norm(s) { return (s || '').replace(/\u200c/g, ' ').toLowerCase(); }

function findTut(id) { return TUTORIALS.find(t => t.id === id); }

const catTitle = {};
const bySlug = {};
function indexData() {
  CATEGORIES.forEach(c => { catTitle[c.id] = c.title; });
  TUTORIALS.forEach(t => { bySlug[t.id] = t; });
}

/* ---- element refs (static shell) ---- */
const sideList = document.getElementById('sideList');
const drawerList = document.getElementById('drawerList');
const contentArea = document.getElementById('contentArea');
const mobileBarTitle = document.getElementById('mobileBarTitle');
const listOverlay = document.getElementById('listOverlay');
const listDrawer = document.getElementById('listDrawer');
const searchInputs = Array.from(document.querySelectorAll('[data-tut-search]'));

/* ============================================================
   View computation (port of the DC renderVals())
   ============================================================ */
function computeCats() {
  const q = norm(state.q.trim());
  const searching = q.length > 0;
  const match = t => !searching || norm(t.title).includes(q);

  const cats = CATEGORIES.map(c => {
    const items = TUTORIALS.filter(t => t.category === c.id && match(t)).map(t => {
      const active = t.id === state.current;
      return {
        title: t.title, hash: '#' + t.id, hasVideo: t.hasVideo, active,
        color: active ? '#D45A3D' : '#3D4A6B',
        weight: active ? '700' : '400',
        bg: active ? '#FCEFEA' : 'transparent'
      };
    });
    // While searching, categories with matches are forced open.
    const isOpen = searching ? true : !!state.open[c.id];
    return {
      id: c.id, title: c.title, icon: c.icon, count: faNum(items.length), items,
      rows: isOpen ? '1fr' : '0fr',
      rot: isOpen ? '180deg' : '0deg'
    };
  }).filter(c => c.items.length > 0); // empty categories hidden during search

  return { cats, noResults: searching && cats.length === 0 };
}

/* ============================================================
   HTML templates (byte-identical to the design markup)
   ============================================================ */
const ACTIVE_BAR = '<span style="position:absolute;inset-inline-start:0;top:9px;bottom:9px;width:3px;border-radius:3px;background-image:repeating-linear-gradient(180deg,#E76F51 0 6px,transparent 6px 10px);"></span>';
const VIDEO_ICON = '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#E76F51" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex:none;opacity:.85;"><polygon points="6 3 20 12 6 21 6 3"/></svg>';

function itemHTML(t, drawer) {
  const inner = (t.active ? ACTIVE_BAR : '')
    + '<span style="flex:1;">' + esc(t.title) + '</span>'
    + (t.hasVideo ? VIDEO_ICON : '');
  if (drawer) {
    return '<a class="hv3907de" href="' + t.hash + '" style="position:relative;display:flex;align-items:center;gap:8px;min-height:46px;padding:10px 14px 10px 10px;border-radius:10px;font-size:14px;line-height:1.7;color:' + t.color + ';font-weight:' + t.weight + ';background:' + t.bg + ';">' + inner + '</a>';
  }
  return '<a class="hve1c063" href="' + t.hash + '" style="position:relative;display:flex;align-items:center;gap:8px;min-height:44px;padding:9px 14px 9px 10px;border-radius:10px;font-size:13.5px;line-height:1.7;color:' + t.color + ';font-weight:' + t.weight + ';background:' + t.bg + ';transition:background .2s ease;">' + inner + '</a>';
}

function catHTML(c, drawer) {
  return '<div style="border-bottom:2px dashed #F0EBE1;">'
    + '<button data-cat-toggle="' + c.id + '" style="width:100%;min-height:48px;background:none;border:none;display:flex;align-items:center;gap:10px;padding:11px 4px;text-align:start;">'
    + '<span style="flex:none;display:inline-flex;width:32px;height:32px;align-items:center;justify-content:center;background:#FCEFEA;border-radius:50%;color:#E76F51;"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">' + c.icon + '</svg></span>'
    + '<b style="flex:1;font-size:' + (drawer ? '14.5px' : '14px') + ';color:#1F2A44;">' + esc(c.title) + '</b>'
    + '<span style="flex:none;font-size:11.5px;color:#9a9587;background:#FAF8F4;border-radius:999px;padding:2px 9px;">' + c.count + '</span>'
    + '<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#6B7280" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="flex:none;transform:rotate(' + c.rot + ');transition:transform .3s ease;"><path d="m6 9 6 6 6-6"/></svg>'
    + '</button>'
    + '<div style="display:grid;grid-template-rows:' + c.rows + ';transition:grid-template-rows .3s ease;">'
    + '<div style="overflow:hidden;">'
    + '<div style="display:flex;flex-direction:column;gap:2px;padding:0 2px 10px;">'
    + c.items.map(t => itemHTML(t, drawer)).join('')
    + '</div></div></div></div>';
}

function noResultsHTML(drawer) {
  if (drawer) {
    return '<div style="text-align:center;padding:20px 8px;">'
      + '<div style="font-size:14.5px;font-weight:700;color:#1F2A44;margin-bottom:5px;">چیزی پیدا نشد</div>'
      + '<a href="/contact" style="font-size:13px;color:#E76F51;font-weight:700;">از خودمان بپرس ←</a>'
      + '</div>';
  }
  return '<div style="text-align:center;padding:22px 8px;">'
    + '<div style="font-size:14.5px;font-weight:700;color:#1F2A44;margin-bottom:5px;">چیزی پیدا نشد</div>'
    + '<a class="hv59f862" href="/contact" style="font-size:13px;color:#E76F51;font-weight:700;">از خودمان بپرس ←</a>'
    + '</div>';
}

function welcomeHTML() {
  const quickSteps = QUICK_START.map((id, i) => ({
    n: faNum(i + 1),
    title: (bySlug[id] || { title: id }).title,
    hash: '#' + id
  }));
  return '<div data-screen-label="پنل خوش‌آمد" style="background:#fff;border:1px solid var(--color-border);border-radius:18px;padding:30px 28px;box-shadow:var(--shadow-card);">'
    + '<span style="font-size:13px;font-weight:700;color:#D45A3D;letter-spacing:1px;">از کجا شروع کنم؟</span>'
    + '<h2 style="font-size:25px;font-weight:700;color:#1F2A44;margin:10px 0 6px;">از صفر تا اولین سفارش — ۵ قدم</h2>'
    + '<p style="font-size:15px;line-height:1.9;color:#6B7280;margin:0 0 24px;">این مسیر را دنبال کن تا مزونت روی دوختک راه بیفتد؛ یا از فهرست، هر آموزشی را باز کن.</p>'
    + '<div style="position:relative;padding-inline-start:44px;">'
    + '<div style="position:absolute;top:12px;bottom:12px;inset-inline-start:15px;width:2px;background-image:repeating-linear-gradient(180deg,#E76F51 0 9px,transparent 9px 16px);opacity:.5;"></div>'
    + quickSteps.map(s =>
      '<a class="hv7d4772" href="' + s.hash + '" style="position:relative;display:flex;align-items:center;gap:12px;min-height:56px;margin-bottom:10px;background:#FAF8F4;border:1px solid #EDEAE3;border-radius:13px;padding:12px 16px;transition:border-color .2s ease,transform .2s ease;">'
      + '<span style="position:absolute;inset-inline-start:-44px;top:50%;transform:translateY(-50%);display:inline-flex;width:32px;height:32px;align-items:center;justify-content:center;border-radius:50%;background:var(--color-primary);color:#fff;font-size:14px;font-weight:700;box-shadow:0 5px 12px rgba(231,111,81,.35);">' + s.n + '</span>'
      + '<b style="flex:1;font-size:14.5px;color:#1F2A44;line-height:1.7;">' + esc(s.title) + '</b>'
      + '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#E76F51" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="flex:none;"><path d="m15 18-6-6 6-6"/></svg>'
      + '</a>').join('')
    + '</div></div>';
}

function metaChipsHTML(cur) {
  return '<span style="background:#FCEFEA;color:#D45A3D;font-weight:700;padding:4px 12px;border-radius:999px;">' + esc(catTitle[cur.category] || '') + '</span>'
    + '<span style="display:inline-flex;align-items:center;gap:5px;"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>' + faNum(cur.duration) + ' دقیقه</span>'
    + (cur.hasVideo
      ? '<span style="display:inline-flex;align-items:center;gap:5px;color:#D45A3D;font-weight:700;"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="6 3 20 12 6 21 6 3"/></svg>ویدئو دارد</span>'
      : '')
    + '<span>·</span>'
    + '<span>به‌روز شده در ' + esc(cur.updated) + '</span>';
}

function navHTML(prev, next) {
  let out = '';
  if (prev) {
    out += '<a class="hvd7433b" href="#' + prev.id + '" style="flex:1 1 240px;display:flex;align-items:center;gap:11px;background:#fff;border:1px solid var(--color-border);border-radius:13px;padding:14px 16px;transition:border-color .2s ease;">'
      + '<svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="#E76F51" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="flex:none;"><path d="m9 18 6-6-6-6"/></svg>'
      + '<span><span style="display:block;font-size:11px;color:#6B7280;margin-bottom:2px;">آموزش قبلی</span><span style="display:block;font-size:13.5px;font-weight:700;color:#1F2A44;line-height:1.6;">' + esc(prev.title) + '</span></span>'
      + '</a>';
  }
  if (next) {
    out += '<a class="hvd7433b" href="#' + next.id + '" style="flex:1 1 240px;display:flex;align-items:center;justify-content:flex-end;gap:11px;background:#fff;border:1px solid var(--color-border);border-radius:13px;padding:14px 16px;text-align:end;transition:border-color .2s ease;">'
      + '<span><span style="display:block;font-size:11px;color:#6B7280;margin-bottom:2px;">آموزش بعدی</span><span style="display:block;font-size:13.5px;font-weight:700;color:#1F2A44;line-height:1.6;">' + esc(next.title) + '</span></span>'
      + '<svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="#E76F51" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="flex:none;"><path d="m15 18-6-6 6-6"/></svg>'
      + '</a>';
  }
  return out;
}

function articleHTML(cur, prev, next) {
  return '<article data-screen-label="محتوای آموزش">'
    + '<header style="margin-bottom:20px;">'
    + '<h2 id="tutHeading" style="font-size:29px;line-height:1.5;font-weight:700;color:#1F2A44;margin:0 0 12px;letter-spacing:-.3px;">' + esc(cur.title) + '</h2>'
    + '<div style="display:flex;align-items:center;flex-wrap:wrap;gap:10px;font-size:13px;color:#6B7280;">' + metaChipsHTML(cur) + '</div>'
    + '</header>'
    + '<div id="tutContent" style="transition:opacity .2s ease;"></div>'
    + '<nav aria-label="آموزش قبلی و بعدی" style="display:flex;flex-wrap:wrap;gap:12px;justify-content:space-between;margin-top:28px;">' + navHTML(prev, next) + '</nav>'
    + '</article>';
}

/* ============================================================
   Renderers
   ============================================================ */
function renderSidebars() {
  const v = computeCats();
  const desk = (v.noResults ? noResultsHTML(false) : '') + v.cats.map(c => catHTML(c, false)).join('');
  const draw = (v.noResults ? noResultsHTML(true) : '') + v.cats.map(c => catHTML(c, true)).join('');
  sideList.innerHTML = desk;
  drawerList.innerHTML = draw;
}

function renderMobileBar() {
  const cur = state.current ? findTut(state.current) : null;
  mobileBarTitle.textContent = cur ? cur.title : 'آموزش کار با دوختک';
}

function renderContent() {
  const cur = state.current ? findTut(state.current) : null;
  if (!cur) {
    contentArea.innerHTML = welcomeHTML();
    contentMode = 'welcome';
    return;
  }
  const idx = TUTORIALS.findIndex(t => t.id === cur.id);
  const prev = idx > 0 ? TUTORIALS[idx - 1] : null;
  const next = idx < TUTORIALS.length - 1 ? TUTORIALS[idx + 1] : null;
  if (contentMode !== 'tut') {
    contentArea.innerHTML = articleHTML(cur, prev, next);
    contentMode = 'tut';
  } else {
    // Keep #tutContent alive across tutorial switches (fade handles the swap).
    document.getElementById('tutHeading').textContent = cur.title;
    contentArea.querySelector('article > header > div').innerHTML = metaChipsHTML(cur);
    contentArea.querySelector('article > nav').innerHTML = navHTML(prev, next);
  }
}

/* ============================================================
   Hash routing + selection (port of applyHash/select)
   ============================================================ */
function applyHash(initial) {
  const slug = decodeURIComponent((location.hash || '').slice(1));
  const t = findTut(slug);
  if (t) {
    select(t.id, initial);
  } else if (!initial) {
    // hash cleared -> welcome panel
    state.current = null;
    renderContent();
    renderSidebars();
    renderMobileBar();
  }
}

function select(id, skipScroll) {
  const t = findTut(id);
  if (!t) return;
  // open the parent category + activate the item, close the drawer
  state.current = id;
  state.open[t.category] = true;
  setListDrawer(false);
  renderContent();
  renderSidebars();
  renderMobileBar();
  loadContent(t);
  if (!skipScroll) {
    const el = document.getElementById('contentArea');
    if (el) {
      const top = el.getBoundingClientRect().top + (document.documentElement.scrollTop || 0) - 100;
      window.scrollTo({ top: Math.max(0, top), behavior: DK.reduce ? 'auto' : 'smooth' });
    }
  }
}

/* ============================================================
   Fragment loading: cache, skeleton, error + retry, 200ms fade
   ============================================================ */
async function loadContent(t) {
  loadToken += 1;
  const token = loadToken;
  const paint = (html) => {
    const box = document.getElementById('tutContent');
    if (!box || token !== loadToken) return;
    if (DK.reduce) { box.innerHTML = html; return; }
    box.style.opacity = '0';
    setTimeout(() => { if (token === loadToken) { box.innerHTML = html; box.style.opacity = '1'; } }, 200);
  };

  if (cache[t.id]) { paint(cache[t.id]); return; }

  // loading skeleton
  paint(`<div style="display:flex;flex-direction:column;gap:14px;padding:8px 0;">
      <div style="height:190px;border-radius:16px;background:#EFEBE2;"></div>
      <div style="height:16px;width:70%;border-radius:8px;background:#EFEBE2;"></div>
      <div style="height:16px;width:90%;border-radius:8px;background:#EFEBE2;"></div>
      <div style="height:16px;width:55%;border-radius:8px;background:#EFEBE2;"></div>
    </div>`);

  try {
    const res = await fetch('/api/tutorial.php?slug=' + encodeURIComponent(t.id));
    if (!res.ok) throw new Error('http ' + res.status);
    const data = await res.json();
    if (!data.ok) throw new Error('api');
    cache[t.id] = data.html;
    paint(data.html);
  } catch (e) {
    paint(`<div style="text-align:center;background:#fff;border:1px solid #E8E6E1;border-radius:16px;padding:36px 20px;">
        <div style="font-size:16px;font-weight:700;color:#1F2A44;margin-bottom:14px;">بارگذاری این آموزش ناموفق بود</div>
        <button data-retry style="display:inline-flex;align-items:center;justify-content:center;min-height:46px;background:#E76F51;color:#fff;border:none;font-family:inherit;font-weight:700;font-size:14.5px;padding:11px 26px;border-radius:12px;cursor:pointer;">تلاش دوباره</button>
      </div>`);
    setTimeout(() => {
      const btn = document.querySelector('#tutContent [data-retry]');
      if (btn) btn.addEventListener('click', () => loadContent(t));
    }, 260);
  }
}

/* ============================================================
   Mobile list drawer
   ============================================================ */
function setListDrawer(open) {
  state.listDrawer = open;
  listDrawer.style.transform = 'translateX(' + (open ? '0%' : '110%') + ')';
  listOverlay.style.opacity = open ? '1' : '0';
  listOverlay.style.pointerEvents = open ? 'auto' : 'none';
  document.body.style.overflow = open ? 'hidden' : '';
}

document.querySelectorAll('[data-list-open]').forEach(b => {
  b.addEventListener('click', () => setListDrawer(true));
});
document.querySelectorAll('[data-list-close]').forEach(b => {
  b.addEventListener('click', () => setListDrawer(false));
});
listOverlay.addEventListener('click', () => setListDrawer(false));

// auto-close the drawer when the desktop sidebar takes over
window.addEventListener('resize', () => {
  if (window.innerWidth >= 1024 && state.listDrawer) setListDrawer(false);
});

/* ============================================================
   Sidebar interactions (delegated — the lists re-render)
   ============================================================ */
function onListClick(e) {
  const btn = e.target.closest('[data-cat-toggle]');
  if (btn) {
    const id = btn.getAttribute('data-cat-toggle');
    state.open[id] = !state.open[id];
    renderSidebars();
    return;
  }
  // drawer items close the drawer even when the hash does not change
  if (e.currentTarget === drawerList && e.target.closest('a[href^="#"]')) {
    setListDrawer(false);
  }
}
sideList.addEventListener('click', onListClick);
drawerList.addEventListener('click', onListClick);

/* ---- live search (both inputs mirror one query) ---- */
searchInputs.forEach(inp => {
  inp.addEventListener('input', (e) => {
    state.q = e.target.value;
    searchInputs.forEach(o => { if (o !== e.target && o.value !== state.q) o.value = state.q; });
    renderSidebars();
  });
});

/* ============================================================
   Delegated clicks inside fragments (video cover, accordions)
   ============================================================ */
document.addEventListener('click', (e) => {
  const cover = e.target.closest && e.target.closest('[data-video-cover]');
  if (cover) {
    const tpl = cover.parentElement.querySelector('template[data-video-embed]');
    if (tpl) { cover.replaceWith(tpl.content.cloneNode(true)); }
    return;
  }
  const accBtn = e.target.closest && e.target.closest('[data-acc-btn]');
  if (accBtn) {
    const panel = accBtn.parentElement.querySelector('[data-acc-panel]');
    const icon = accBtn.querySelector('[data-acc-icon]');
    if (panel) {
      const openNow = panel.style.gridTemplateRows === '1fr';
      if (DK.reduce) panel.style.transition = 'none';
      panel.style.gridTemplateRows = openNow ? '0fr' : '1fr';
      if (icon) icon.style.transform = openNow ? 'rotate(0deg)' : 'rotate(45deg)';
    }
  }
});

/* ============================================================
   Boot: fetch the index from the API, then render + hash routing
   ============================================================ */
async function init() {
  try {
    const res = await fetch('/api/tutorials.php');
    if (!res.ok) throw new Error('http ' + res.status);
    const data = await res.json();
    CATEGORIES = data.categories || [];
    TUTORIALS = data.tutorials || [];
    QUICK_START = data.quick_start || [];
    indexData();
    if (CATEGORIES[0] && Object.keys(state.open).length === 0) state.open[CATEGORIES[0].id] = true;

    renderContent();
    renderSidebars();
    renderMobileBar();

    window.addEventListener('hashchange', () => applyHash(false));
    setTimeout(() => applyHash(true), 60);
  } catch (e) {
    contentArea.innerHTML = '<div style="text-align:center;background:#fff;border:1px solid #E8E6E1;border-radius:16px;padding:36px 20px;">'
      + '<div style="font-size:16px;font-weight:700;color:#1F2A44;margin-bottom:14px;">بارگذاری فهرست آموزش‌ها ناموفق بود</div>'
      + '<button data-boot-retry style="display:inline-flex;align-items:center;justify-content:center;min-height:46px;background:#E76F51;color:#fff;border:none;font-family:inherit;font-weight:700;font-size:14.5px;padding:11px 26px;border-radius:12px;cursor:pointer;">تلاش دوباره</button>'
      + '</div>';
    const btn = contentArea.querySelector('[data-boot-retry]');
    if (btn) btn.addEventListener('click', init);
  }
}
init();
