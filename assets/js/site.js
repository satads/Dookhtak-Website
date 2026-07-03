/* ============================================================
   Dookhtak — central site behaviors (single source of truth).
   Header drawer, scroll-reveal engine, hero stagger, FAQ
   accordions, toasts. Pages add only their unique behaviors in
   small page-specific files that use this API.
   ============================================================ */
window.DK = (function () {
  'use strict';

  var reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  if (reduce) document.documentElement.style.scrollBehavior = 'auto';

  function faNum(n) {
    return String(n).replace(/\d/g, function (d) { return '۰۱۲۳۴۵۶۷۸۹'[d]; });
  }

  /* ---- Show every motion target immediately (reduced motion) ---- */
  function showAll() {
    document.querySelectorAll('[data-rv],[data-hero]').forEach(function (e) {
      e.style.transition = 'none';
      e.style.opacity = '1';
      e.style.transform = 'none';
      e.setAttribute('data-rv-done', '1');
    });
  }

  /* ---- Hero stagger: data-hero elements fade in sequentially ---- */
  function hero(start, step) {
    if (reduce) { showAll(); return; }
    start = start == null ? 160 : start;
    step = step == null ? 80 : step;
    document.querySelectorAll('[data-hero]').forEach(function (e, i) {
      setTimeout(function () {
        e.style.opacity = '1';
        e.style.transform = 'none';
      }, start + i * step);
    });
  }

  /* ---- Scroll-reveal engine for data-rv elements ----
     opts: threshold (fraction of viewport height, default .9),
           tick (safety interval ms, default 600),
           initialDelay (ms, default 150),
           onScroll (extra per-scroll callback),
           onReveal (callback per revealed element). */
  function reveal(opts) {
    opts = opts || {};
    var threshold = opts.threshold == null ? 0.9 : opts.threshold;

    function revealNow() {
      if (reduce) { showAll(); return; }
      var vh = window.innerHeight;
      document.querySelectorAll('[data-rv]').forEach(function (e) {
        if (e.getAttribute('data-rv-done')) return;
        var r = e.getBoundingClientRect();
        if (r.top < vh * threshold && r.bottom > 0) {
          var delay = parseInt(e.getAttribute('data-rv-delay') || '0', 10);
          e.style.transitionDelay = delay + 'ms';
          e.style.opacity = '1';
          e.style.transform = 'none';
          e.setAttribute('data-rv-done', '1');
          if (opts.onReveal) opts.onReveal(e);
        }
      });
    }

    if (reduce) showAll();
    var onScroll = function () {
      revealNow();
      if (opts.onScroll) opts.onScroll();
    };
    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll();
    setTimeout(onScroll, opts.initialDelay == null ? 150 : opts.initialDelay);
    setInterval(revealNow, opts.tick == null ? 600 : opts.tick);
    return revealNow;
  }

  /* ---- Header: mobile drawer + floating pill shadow ---- */
  function initHeader() {
    var pill = document.getElementById('navPill');
    var drawer = document.getElementById('dkDrawer');
    var overlay = document.getElementById('dkDrawerOverlay');

    function setDrawer(open) {
      if (!drawer || !overlay) return;
      drawer.style.transform = 'translateX(' + (open ? '0%' : '110%') + ')';
      overlay.style.opacity = open ? '1' : '0';
      overlay.style.pointerEvents = open ? 'auto' : 'none';
      document.body.style.overflow = open ? 'hidden' : '';
    }

    document.querySelectorAll('[data-drawer-open]').forEach(function (b) {
      b.addEventListener('click', function () { setDrawer(true); });
    });
    document.querySelectorAll('[data-drawer-close]').forEach(function (b) {
      b.addEventListener('click', function () { setDrawer(false); });
    });
    if (overlay) overlay.addEventListener('click', function () { setDrawer(false); });

    window.addEventListener('resize', function () {
      if (window.innerWidth >= 1100) setDrawer(false);
    });

    var onScroll = function () {
      if (!pill) return;
      var top = document.documentElement.scrollTop || document.body.scrollTop || 0;
      pill.style.boxShadow = top > 8
        ? '0 10px 30px -10px rgba(31,42,68,.24)'
        : '0 4px 18px -8px rgba(31,42,68,.14)';
    };
    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll();
  }

  /* ---- FAQ accordion (dashed rows, single-open) ----
     Markup contract: [data-faq-item] > [data-faq-btn] + [data-faq-panel],
     the +/x icon is [data-faq-icon]. Initial open item is rendered
     server-side with grid-template-rows:1fr and icon rotate(45deg). */
  function faq(rootSel) {
    var root = typeof rootSel === 'string' ? document.querySelector(rootSel) : rootSel;
    if (!root) return;
    var items = root.querySelectorAll('[data-faq-item]');
    items.forEach(function (item) {
      var btn = item.querySelector('[data-faq-btn]');
      if (!btn) return;
      btn.addEventListener('click', function () {
        var panel = item.querySelector('[data-faq-panel]');
        var icon = item.querySelector('[data-faq-icon]');
        var isOpen = panel && panel.style.gridTemplateRows === '1fr';
        items.forEach(function (other) {
          var p = other.querySelector('[data-faq-panel]');
          var ic = other.querySelector('[data-faq-icon]');
          if (p) p.style.gridTemplateRows = '0fr';
          if (ic) ic.style.transform = 'rotate(0deg)';
        });
        if (!isOpen && panel) {
          panel.style.gridTemplateRows = '1fr';
          if (icon) icon.style.transform = 'rotate(45deg)';
        }
      });
    });
  }

  /* ---- Toast (Persian UI feedback) ---- */
  var toastEl = null, toastTimer = null;
  function toast(msg) {
    if (!toastEl) {
      toastEl = document.createElement('div');
      toastEl.className = 'dk-toast';
      document.body.appendChild(toastEl);
    }
    toastEl.textContent = msg;
    requestAnimationFrame(function () { toastEl.classList.add('show'); });
    clearTimeout(toastTimer);
    toastTimer = setTimeout(function () { toastEl.classList.remove('show'); }, 2600);
  }

  /* ---- Resize helper: run now + on every resize ---- */
  function onResize(fn) {
    window.addEventListener('resize', fn);
    fn();
  }

  document.addEventListener('DOMContentLoaded', initHeader);

  return {
    reduce: reduce,
    faNum: faNum,
    showAll: showAll,
    hero: hero,
    reveal: reveal,
    faq: faq,
    toast: toast,
    onResize: onResize
  };
})();
