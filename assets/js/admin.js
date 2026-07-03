/* ============================================================
   Dookhtak — central ADMIN behaviors (single source of truth).
   Drawer, user menu, sidebar collapse, toast, delete-confirm
   modal, switches, copy-to-clipboard. No per-screen duplication.
   ============================================================ */
window.DKA = (function () {
  'use strict';

  /* ---- Toast (green ok / red err, per the approved design) ---- */
  var toastEl = null, toastTimer = null;
  function toast(msg, kind) {
    if (!toastEl) {
      toastEl = document.createElement('div');
      toastEl.className = 'a-toast';
      toastEl.setAttribute('role', 'status');
      toastEl.innerHTML = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg><span></span>';
      document.body.appendChild(toastEl);
    }
    toastEl.querySelector('span').textContent = msg;
    toastEl.classList.toggle('err', kind === 'err');
    requestAnimationFrame(function () { toastEl.classList.add('show'); });
    clearTimeout(toastTimer);
    toastTimer = setTimeout(function () { toastEl.classList.remove('show'); }, 2400);
  }

  /* ---- Delete-confirm modal (label + callback) ---- */
  function confirmDelete(label, onYes) {
    var overlay = document.createElement('div');
    overlay.className = 'a-modal-overlay';
    overlay.innerHTML =
      '<div class="a-modal" role="dialog" aria-modal="true">' +
      '<span style="display:inline-flex;width:52px;height:52px;align-items:center;justify-content:center;background:#FDF0F1;border-radius:50%;color:#BB2D3B;margin-bottom:14px;">' +
      '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/><path d="M12 9v4"/><path d="M12 17h.01"/></svg></span>' +
      '<div style="font-size:15.5px;font-weight:700;color:#1F2A44;margin-bottom:6px;">حذف «<span data-lbl></span>»؟</div>' +
      '<p data-note style="font-size:13px;color:#6B7280;margin:0 0 20px;">این کار قابل بازگشت نیست.</p>' +
      '<div style="display:flex;gap:10px;justify-content:center;">' +
      '<button type="button" class="a-btn a-btn-danger" data-yes style="min-height:44px;padding:10px 24px;">حذف</button>' +
      '<button type="button" class="a-btn a-btn-cancel" data-no style="min-height:44px;padding:10px 24px;">انصراف</button>' +
      '</div></div>';
    overlay.querySelector('[data-lbl]').textContent = label;
    document.body.appendChild(overlay);
    function close() { overlay.remove(); }
    overlay.querySelector('[data-no]').addEventListener('click', close);
    overlay.addEventListener('click', function (e) { if (e.target === overlay) close(); });
    overlay.querySelector('[data-yes]').addEventListener('click', function () { close(); onYes(); });
    return overlay;
  }

  /* ---- Copy to clipboard ---- */
  function copy(text, doneMsg) {
    var done = function () { toast(doneMsg || 'کپی شد'); };
    if (navigator.clipboard && navigator.clipboard.writeText) {
      navigator.clipboard.writeText(text).then(done).catch(done);
    } else { done(); }
  }

  /* ---- POST helper with CSRF (JSON responses) ---- */
  function post(url, data) {
    var body = new FormData();
    Object.keys(data || {}).forEach(function (k) { body.append(k, data[k]); });
    var csrf = document.querySelector('meta[name="csrf-token"]');
    if (csrf) body.append('csrf_token', csrf.content);
    return fetch(url, { method: 'POST', body: body }).then(function (r) { return r.json(); });
  }

  document.addEventListener('DOMContentLoaded', function () {
    /* Sidebar collapse (desktop) — remembered per browser */
    var side = document.getElementById('adminSide');
    var collapseBtn = document.querySelector('.a-side-collapse');
    if (side && localStorage.getItem('dk_admin_side') === 'closed') side.classList.add('collapsed');
    if (collapseBtn) collapseBtn.addEventListener('click', function () {
      side.classList.toggle('collapsed');
      localStorage.setItem('dk_admin_side', side.classList.contains('collapsed') ? 'closed' : 'open');
    });

    /* Mobile drawer */
    var drawer = document.getElementById('admDrawer');
    var overlay = document.getElementById('admDrawerOverlay');
    function setDrawer(open) {
      if (!drawer || !overlay) return;
      drawer.style.transform = 'translateX(' + (open ? '0%' : '110%') + ')';
      overlay.style.opacity = open ? '1' : '0';
      overlay.style.pointerEvents = open ? 'auto' : 'none';
      document.body.style.overflow = open ? 'hidden' : '';
    }
    document.querySelectorAll('[data-adm-drawer-open]').forEach(function (b) {
      b.addEventListener('click', function () { setDrawer(true); });
    });
    document.querySelectorAll('[data-adm-drawer-close]').forEach(function (b) {
      b.addEventListener('click', function () { setDrawer(false); });
    });
    if (overlay) overlay.addEventListener('click', function () { setDrawer(false); });
    window.addEventListener('resize', function () { if (window.innerWidth >= 1024) setDrawer(false); });

    /* User menu */
    var userBtn = document.querySelector('[data-user-menu-btn]');
    var userMenu = document.querySelector('[data-user-menu]');
    if (userBtn && userMenu) {
      userBtn.addEventListener('click', function (e) {
        e.stopPropagation();
        userMenu.style.display = userMenu.style.display === 'block' ? 'none' : 'block';
      });
      document.addEventListener('click', function () { userMenu.style.display = 'none'; });
      userMenu.addEventListener('click', function (e) { e.stopPropagation(); });
    }

    /* Switches: button.a-switch toggles itself + hidden input + dims target */
    document.querySelectorAll('.a-switch[data-switch-input]').forEach(function (sw) {
      sw.addEventListener('click', function () {
        var on = !sw.classList.contains('on');
        sw.classList.toggle('on', on);
        sw.setAttribute('aria-checked', on ? 'true' : 'false');
        var input = document.querySelector('input[name="' + sw.getAttribute('data-switch-input') + '"][type="hidden"]');
        if (input) input.value = on ? '1' : '0';
        var target = sw.getAttribute('data-switch-dims');
        if (target) {
          var el = document.querySelector(target);
          if (el) { el.classList.toggle('dim', !on); el.disabled = !on; }
        }
      });
    });

    /* Forms asking for delete confirmation */
    document.querySelectorAll('form[data-confirm]').forEach(function (f) {
      f.addEventListener('submit', function (e) {
        if (f.getAttribute('data-confirmed')) return;
        e.preventDefault();
        confirmDelete(f.getAttribute('data-confirm'), function () {
          f.setAttribute('data-confirmed', '1');
          f.submit();
        });
      });
    });

    /* Copy buttons */
    document.querySelectorAll('[data-copy]').forEach(function (b) {
      b.addEventListener('click', function () { copy(b.getAttribute('data-copy'), 'آدرس کپی شد'); });
    });

    /* Server-side flash message (?toast=... set by redirects) */
    var flash = document.querySelector('meta[name="admin-toast"]');
    if (flash && flash.content) toast(flash.content, flash.getAttribute('data-kind') || 'ok');
  });

  return { toast: toast, confirmDelete: confirmDelete, copy: copy, post: post };
})();
