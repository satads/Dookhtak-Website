/* Tutorial structured-builder behaviors (screen-specific).
   Shared admin components (toast, switches, modal) live in admin.js. */
document.addEventListener('DOMContentLoaded', function () {
  'use strict';

  var dirty = false;
  var dirtyBar = document.getElementById('dirtyBar');
  function markDirty() { if (!dirty) { dirty = true; dirtyBar.style.display = 'flex'; } }
  function faNum(n) { return String(n).replace(/\d/g, function (d) { return '۰۱۲۳۴۵۶۷۸۹'[d]; }); }
  function esc(s) {
    return String(s == null ? '' : s).replace(/&/g, '&amp;').replace(/</g, '&lt;')
      .replace(/>/g, '&gt;').replace(/"/g, '&quot;');
  }

  /* ---------- Steps repeater ---------- */
  var stepList = document.getElementById('stepList');
  var stepEmpty = document.getElementById('stepEmpty');

  function stepCard(s) {
    var d = document.createElement('div');
    d.className = 'a-card';
    d.setAttribute('data-step', '');
    d.style.cssText = 'padding:14px;margin-bottom:10px;background:#FBFAF7;';
    d.innerHTML =
      '<div style="display:flex;align-items:center;justify-content:space-between;gap:8px;margin-bottom:10px;">' +
      '  <b data-step-n style="display:inline-flex;align-items:center;justify-content:center;min-width:30px;height:30px;border-radius:50%;background:#E76F51;color:#fff;font-size:13px;"></b>' +
      '  <span style="display:flex;gap:2px;">' +
      '    <button type="button" class="a-iconbtn edit" data-mv-up title="بالا"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m18 15-6-6-6 6"/></svg></button>' +
      '    <button type="button" class="a-iconbtn edit" data-mv-down title="پایین"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg></button>' +
      '    <button type="button" class="a-iconbtn del" data-rm title="حذف قدم"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6"/><path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg></button>' +
      '  </span>' +
      '</div>' +
      '<label class="a-label">تیتر قدم (یک جمله اکشن)</label>' +
      '<input class="a-input" data-f="title" type="text" value="' + esc(s.title) + '" style="margin-bottom:10px;background:#fff;">' +
      '<label class="a-label">متن قدم</label>' +
      '<textarea class="a-input" data-f="text" rows="2" style="min-height:0;resize:vertical;margin-bottom:10px;background:#fff;">' + esc(s.text) + '</textarea>' +
      '<div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:10px;">' +
      '  <div><label class="a-label">نکته (اختیاری)</label><textarea class="a-input" data-f="tip" rows="2" style="min-height:0;resize:vertical;background:#fff;">' + esc(s.tip) + '</textarea></div>' +
      '  <div><label class="a-label">مواظب باش (اختیاری)</label><textarea class="a-input" data-f="warning" rows="2" style="min-height:0;resize:vertical;background:#fff;">' + esc(s.warning) + '</textarea></div>' +
      '</div>' +
      '<div style="display:flex;flex-wrap:wrap;gap:10px;align-items:flex-start;">' +
      '  <div style="flex:0 0 130px;">' +
      '    <label class="a-label">اسکرین‌شات</label>' +
      '    <input type="hidden" data-f="image" value="' + esc(s.image) + '">' +
      '    <div data-img-wrap style="' + (s.image ? '' : 'display:none;') + 'position:relative;border-radius:9px;overflow:hidden;margin-bottom:6px;">' +
      '      <img data-img src="' + (s.image ? '/' + esc(s.image) : '') + '" alt="" style="display:block;width:100%;aspect-ratio:4/5;object-fit:cover;background:#F7F6F3;">' +
      '      <button type="button" data-img-rm aria-label="حذف تصویر" style="position:absolute;top:5px;inset-inline-start:5px;display:inline-flex;align-items:center;justify-content:center;width:24px;height:24px;background:rgba(31,42,68,.75);border:none;border-radius:6px;color:#fff;cursor:pointer;"><svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg></button>' +
      '    </div>' +
      '    <button type="button" data-img-up class="a-cover-btn" style="' + (s.image ? 'display:none;' : '') + 'width:100%;min-height:52px;background:#fff;border:1px dashed #d9d3c6;border-radius:9px;font-size:12px;color:#6B7280;cursor:pointer;">+ تصویر</button>' +
      '  </div>' +
      '  <div style="flex:1;min-width:180px;">' +
      '    <label class="a-label">متن جایگزین تصویر</label>' +
      '    <input class="a-input" data-f="image_alt" type="text" value="' + esc(s.image_alt) + '" style="margin-bottom:10px;background:#fff;">' +
      '    <label class="a-label">توضیح تصویر (annotation)</label>' +
      '    <input class="a-input" data-f="annotation" type="text" value="' + esc(s.annotation) + '" placeholder="مثلاً: صفحه اصلی اپ با منوی «سفارش‌ها»" style="background:#fff;">' +
      '  </div>' +
      '</div>';
    return d;
  }

  function renumber() {
    var cards = stepList.querySelectorAll('[data-step]');
    cards.forEach(function (c, i) { c.querySelector('[data-step-n]').textContent = faNum(i + 1); });
    stepEmpty.style.display = cards.length ? 'none' : 'block';
  }

  function addStep(s, focus) {
    var card = stepCard(s || {});
    stepList.appendChild(card);
    renumber();
    if (focus) card.querySelector('[data-f="title"]').focus();
  }

  (window.TUT_STEPS || []).forEach(function (s) { addStep(s, false); });
  renumber();

  document.querySelector('[data-step-add]').addEventListener('click', function () { addStep(null, true); markDirty(); });

  stepList.addEventListener('click', function (e) {
    var card = e.target.closest && e.target.closest('[data-step]');
    if (!card) return;
    if (e.target.closest('[data-rm]')) {
      DKA.confirmDelete('این قدم', function () { card.remove(); renumber(); markDirty(); });
    } else if (e.target.closest('[data-mv-up]')) {
      if (card.previousElementSibling) { stepList.insertBefore(card, card.previousElementSibling); renumber(); markDirty(); }
    } else if (e.target.closest('[data-mv-down]')) {
      if (card.nextElementSibling) { stepList.insertBefore(card.nextElementSibling, card); renumber(); markDirty(); }
    } else if (e.target.closest('[data-img-up]')) {
      var input = document.createElement('input');
      input.type = 'file';
      input.accept = 'image/jpeg,image/png,image/webp';
      input.onchange = function () {
        if (!input.files.length) return;
        var fd = new FormData();
        fd.append('image', input.files[0]);
        fd.append('csrf_token', document.querySelector('meta[name="csrf-token"]').content);
        fetch('/admin/media_upload.php', { method: 'POST', body: fd })
          .then(function (r) { return r.json(); })
          .then(function (res) {
            if (!res.ok) { DKA.toast(res.error || 'آپلود ناموفق بود.', 'err'); return; }
            card.querySelector('[data-f="image"]').value = res.media.file_path;
            card.querySelector('[data-img]').src = '/' + res.media.file_path;
            card.querySelector('[data-img-wrap]').style.display = '';
            card.querySelector('[data-img-up]').style.display = 'none';
            markDirty();
          })
          .catch(function () { DKA.toast('خطا در ارتباط با سرور.', 'err'); });
      };
      input.click();
    } else if (e.target.closest('[data-img-rm]')) {
      card.querySelector('[data-f="image"]').value = '';
      card.querySelector('[data-img-wrap]').style.display = 'none';
      card.querySelector('[data-img-up]').style.display = '';
      markDirty();
    }
  });

  /* ---------- Troubleshooting repeater ---------- */
  var trList = document.getElementById('trList');
  function trCard(x) {
    var d = document.createElement('div');
    d.className = 'a-card';
    d.setAttribute('data-tr', '');
    d.style.cssText = 'padding:12px;margin-bottom:8px;background:#FBFAF7;';
    d.innerHTML =
      '<div style="display:flex;gap:8px;align-items:flex-start;">' +
      '  <div style="flex:1;">' +
      '    <input class="a-input" data-f="q" type="text" placeholder="پرسش…" value="' + esc(x.q) + '" style="margin-bottom:8px;background:#fff;">' +
      '    <textarea class="a-input" data-f="a" rows="2" placeholder="پاسخ…" style="min-height:0;resize:vertical;background:#fff;">' + esc(x.a) + '</textarea>' +
      '  </div>' +
      '  <span style="display:flex;flex-direction:column;gap:2px;">' +
      '    <button type="button" class="a-iconbtn edit" data-mv-up title="بالا"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m18 15-6-6-6 6"/></svg></button>' +
      '    <button type="button" class="a-iconbtn edit" data-mv-down title="پایین"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg></button>' +
      '    <button type="button" class="a-iconbtn del" data-rm title="حذف"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6"/><path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg></button>' +
      '  </span>' +
      '</div>';
    return d;
  }
  (window.TUT_TROUBLE || []).forEach(function (x) { trList.appendChild(trCard(x)); });
  document.querySelector('[data-tr-add]').addEventListener('click', function () {
    var c = trCard({});
    trList.appendChild(c);
    c.querySelector('[data-f="q"]').focus();
    markDirty();
  });
  trList.addEventListener('click', function (e) {
    var card = e.target.closest && e.target.closest('[data-tr]');
    if (!card) return;
    if (e.target.closest('[data-rm]')) { card.remove(); markDirty(); }
    else if (e.target.closest('[data-mv-up]') && card.previousElementSibling) { trList.insertBefore(card, card.previousElementSibling); markDirty(); }
    else if (e.target.closest('[data-mv-down]') && card.nextElementSibling) { trList.insertBefore(card.nextElementSibling, card); markDirty(); }
  });

  /* ---------- Video switch ---------- */
  var vSwitch = document.getElementById('videoSwitch');
  var vType = document.getElementById('videoType');
  var vEmbed = document.getElementById('videoEmbed');
  vSwitch.addEventListener('click', function () {
    var on = !vSwitch.classList.contains('on');
    vSwitch.classList.toggle('on', on);
    vSwitch.setAttribute('aria-checked', on ? 'true' : 'false');
    vType.value = on ? 'aparat' : 'none';
    vEmbed.style.display = on ? '' : 'none';
    markDirty();
  });

  /* ---------- Slug auto (latin) ---------- */
  var title = document.getElementById('edTitle');
  var slug = document.getElementById('edSlug');
  var slugTouched = slug.value !== '';
  slug.addEventListener('input', function () { slugTouched = true; markDirty(); });
  title.addEventListener('input', function () {
    markDirty();
    if (slugTouched) return;
    slug.value = title.value.trim().toLowerCase()
      .replace(/[^a-z0-9\s-]/g, '').replace(/\s+/g, '-').replace(/-+/g, '-').replace(/^-|-$/g, '');
  });

  /* ---------- SEO counters ---------- */
  function counter(inputId, countId, limit) {
    var el = document.getElementById(inputId), out = document.getElementById(countId);
    function update() {
      out.textContent = faNum(el.value.length);
      out.parentElement.style.color = el.value.length > limit ? '#BB2D3B' : '#9a9587';
    }
    el.addEventListener('input', update);
    update();
  }
  counter('seoTitle', 'seoTitleCount', 60);
  counter('seoDesc', 'seoDescCount', 160);

  /* ---------- Dirty tracking + serialize on submit ---------- */
  document.querySelectorAll('#tutForm input, #tutForm select, #tutForm textarea').forEach(function (el) {
    el.addEventListener('input', markDirty);
    el.addEventListener('change', markDirty);
  });

  function serialize() {
    var steps = [];
    stepList.querySelectorAll('[data-step]').forEach(function (c) {
      var g = function (f) { return c.querySelector('[data-f="' + f + '"]').value; };
      steps.push({ title: g('title'), text: g('text'), tip: g('tip'), warning: g('warning'),
        image: g('image'), image_alt: g('image_alt'), annotation: g('annotation') });
    });
    document.getElementById('contentJson').value = JSON.stringify(steps);
    var tr = [];
    trList.querySelectorAll('[data-tr]').forEach(function (c) {
      tr.push({ q: c.querySelector('[data-f="q"]').value, a: c.querySelector('[data-f="a"]').value });
    });
    document.getElementById('troubleJson').value = JSON.stringify(tr);
  }

  document.getElementById('tutForm').addEventListener('submit', function (e) {
    serialize();
    // Preview opens in a new tab and must NOT clear the dirty guard.
    var isPreview = e.submitter && e.submitter.getAttribute('formaction');
    if (!isPreview) dirty = false;
  });
  window.addEventListener('beforeunload', function (e) {
    if (dirty) { e.preventDefault(); e.returnValue = ''; }
  });
});
