/* Contact page behaviors — everything shared lives in site.js (DK). */

/* ============================================
   Consultation form backend contract
   Phase 5: wired to the real endpoint.
   ============================================ */
const CONTACT_ENDPOINT = '/api/contact.php';
const DEMO_MODE = false;

/* Single submission point — no other send logic in this file. */
async function submitContactForm(data) {
  if (DEMO_MODE) {
    await new Promise(function (r) { setTimeout(r, 900); });
    return { ok: true };
  }
  const res = await fetch(CONTACT_ENDPOINT, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(data) // { name, phone, business_type, subject, message }
  });
  return { ok: res.ok };
}

document.addEventListener('DOMContentLoaded', function () {
  'use strict';

  DK.hero(160, 80);
  DK.reveal({ threshold: 0.9, tick: 600 });

  /* ---- "Request free consultation" card button: scroll to the form ---- */
  document.querySelectorAll('[data-go-form]').forEach(function (b) {
    b.addEventListener('click', function () {
      var el = document.getElementById('consult');
      if (!el) return;
      var top = el.getBoundingClientRect().top + (document.documentElement.scrollTop || 0) - 90;
      window.scrollTo({ top: top, behavior: DK.reduce ? 'auto' : 'smooth' });
      setTimeout(function () {
        var f = document.getElementById('f-name');
        if (f) f.focus({ preventScroll: true });
      }, DK.reduce ? 50 : 550);
    });
  });

  /* ---- Consultation form ---- */
  var form = document.getElementById('consultForm');
  if (!form) return;

  var nameInput = document.getElementById('f-name');
  var phoneInput = document.getElementById('f-phone');
  var bizSelect = document.getElementById('f-biz');
  var subjSelect = document.getElementById('f-subject');
  var msgArea = document.getElementById('f-msg');
  var hpInput = form.querySelector('input[name="company"]');
  var nameErrEl = document.getElementById('e-name');
  var phoneErrEl = document.getElementById('e-phone');
  var sendErrEl = document.getElementById('sendErr');
  var btn = document.getElementById('consultSubmit');
  var btnLabel = document.getElementById('btnLabel');
  var spinner = document.getElementById('btnSpinner');
  var successEl = document.getElementById('consultSuccess');

  var sending = false;
  var sent = false;

  // Normalize Persian/Arabic digits to Latin.
  function normalizeDigits(s) {
    var fa = '۰۱۲۳۴۵۶۷۸۹', ar = '٠١٢٣٤٥٦٧٨٩';
    return (s || '').replace(/[۰-۹٠-٩]/g, function (ch) {
      var i = fa.indexOf(ch);
      if (i > -1) return String(i);
      i = ar.indexOf(ch);
      return i > -1 ? String(i) : ch;
    });
  }

  // Show/clear an inline field error (text + #E4A3AB border).
  function setFieldError(input, errEl, msg) {
    input.style.borderColor = msg ? '#E4A3AB' : 'var(--color-border)';
    errEl.textContent = msg;
    errEl.style.display = msg ? '' : 'none';
  }

  // Errors clear as soon as the user types again.
  nameInput.addEventListener('input', function () { setFieldError(nameInput, nameErrEl, ''); });
  phoneInput.addEventListener('input', function () { setFieldError(phoneInput, phoneErrEl, ''); });

  function validate() {
    var ok = true;
    if (!nameInput.value.trim()) {
      setFieldError(nameInput, nameErrEl, 'نام و نام خانوادگی را بنویس.');
      ok = false;
    } else {
      setFieldError(nameInput, nameErrEl, '');
    }
    var phone = normalizeDigits(phoneInput.value).replace(/[\s-]/g, '');
    if (!/^09\d{9}$/.test(phone)) {
      setFieldError(phoneInput, phoneErrEl, 'شماره موبایل باید با ۰۹ شروع شود و ۱۱ رقم باشد.');
      ok = false;
    } else {
      setFieldError(phoneInput, phoneErrEl, '');
    }
    return { ok: ok, phone: phone };
  }

  function setSending(on) {
    sending = on;
    btn.disabled = on;
    btn.style.opacity = on ? '.75' : '1';
    btn.style.cursor = on ? 'default' : 'pointer';
    btnLabel.textContent = on ? 'در حال ارسال…' : 'ثبت درخواست مشاوره';
    spinner.style.display = on ? 'inline-block' : 'none';
  }

  // Swap the form for the success panel (.96 → 1 scale, fade in).
  function showSuccess() {
    sent = true;
    form.style.display = 'none';
    successEl.style.display = '';
    var reveal = function () {
      successEl.style.opacity = '1';
      successEl.style.transform = 'scale(1)';
    };
    if (DK.reduce) reveal();
    else setTimeout(reveal, 40);
  }

  form.addEventListener('submit', async function (e) {
    e.preventDefault();
    if (sending || sent) return;
    if (hpInput && hpInput.value) return; // honeypot — bot, silently do nothing
    var v = validate();
    if (!v.ok) return;
    setSending(true);
    sendErrEl.style.display = 'none';
    try {
      var res = await submitContactForm({
        name: nameInput.value.trim(),
        phone: v.phone,
        business_type: bizSelect.value,
        subject: subjSelect.value,
        message: msgArea.value.trim()
      });
      if (res.ok) {
        setSending(false);
        showSuccess();
      } else {
        setSending(false);
        sendErrEl.style.display = '';
      }
    } catch (err) {
      setSending(false);
      sendErrEl.style.display = '';
    }
  });
});
