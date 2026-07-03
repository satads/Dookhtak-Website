<?php
/**
 * Media library (images only) — upload + grid with thumbnail, size,
 * alt-text edit, copy-URL and delete-with-caution. The design export
 * has no media screen; this screen is composed strictly from the
 * approved admin design system (cards, inputs, buttons, modal, toast).
 */
require_once __DIR__ . '/../includes/admin_layout.php';
require_once __DIR__ . '/../includes/media_lib.php';

require_admin();

$items = db()->query('SELECT * FROM media ORDER BY created_at DESC, id DESC')->fetchAll();

/** Prefer the 400px WebP variant for grid thumbnails. */
function media_thumb(array $m): string
{
    $dir = dirname($m['file_path']);
    $name = pathinfo($m['file_path'], PATHINFO_FILENAME);
    $thumb = $dir . '/' . $name . '-400.webp';
    return '/' . (is_file(media_root() . '/' . $thumb) ? $thumb : $m['file_path']);
}

function fa_filesize(int $bytes): string
{
    if ($bytes >= 1048576) {
        return fa_digits(number_format($bytes / 1048576, 1)) . ' مگابایت';
    }
    return fa_digits((int) round($bytes / 1024)) . ' کیلوبایت';
}

admin_page_start('رسانه‌ها', 'media');
?>
<div>
  <div style="display:flex;flex-wrap:wrap;align-items:center;gap:10px;margin-bottom:14px;">
    <input id="mediaFile" type="file" accept="image/jpeg,image/png,image/webp" style="display:none;">
    <button type="button" id="mediaUploadBtn" class="a-btn a-btn-primary">+ آپلود تصویر</button>
    <span class="a-hint" style="margin:0;">JPG ، PNG یا WebP — حداکثر ۲ مگابایت</span>
    <span id="mediaBusy" class="a-skel" style="display:none;width:120px;height:20px;"></span>
  </div>

  <?php if (!$items): ?>
  <div class="a-empty" id="mediaEmpty">
    <svg width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="#c9c3b6" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M3 5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><path d="M9 11a2 2 0 1 0 0-4 2 2 0 0 0 0 4z"/><path d="M21 15l-3.09-3.09a2 2 0 0 0-2.82 0L6 21"/></svg>
    <div class="a-empty-title">هنوز تصویری آپلود نشده است.</div>
  </div>
  <?php endif; ?>

  <div class="a-media-grid" id="mediaGrid">
    <?php foreach ($items as $m): ?>
    <div class="a-card" style="overflow:hidden;" data-media-id="<?= (int) $m['id'] ?>">
      <img class="a-media-thumb" src="<?= e(media_thumb($m)) ?>" alt="<?= e($m['alt_text'] ?? '') ?>" loading="lazy">
      <div style="padding:10px 12px;">
        <div style="display:flex;align-items:center;justify-content:space-between;gap:6px;font-size:11.5px;color:#9a9587;margin-bottom:8px;">
          <span dir="ltr" style="overflow:hidden;text-overflow:ellipsis;white-space:nowrap;"><?= e(basename($m['file_path'])) ?></span>
          <span style="flex:none;"><?= e(fa_filesize((int) $m['size_bytes'])) ?> · <?= fa_digits($m['width']) ?>×<?= fa_digits($m['height']) ?></span>
        </div>
        <input class="a-input" type="text" placeholder="متن جایگزین (alt)…" value="<?= e($m['alt_text'] ?? '') ?>" data-media-alt style="min-height:36px;font-size:12.5px;margin-bottom:8px;">
        <div style="display:flex;gap:4px;justify-content:flex-end;">
          <button type="button" class="a-iconbtn edit" title="کپی آدرس" data-copy="<?= e(rtrim(BASE_URL, '/') . '/' . $m['file_path']) ?>">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="14" height="14" x="8" y="8" rx="2"/><path d="M4 16c-1.1 0-2-.9-2-2V4c0-1.1.9-2 2-2h10c1.1 0 2 .9 2 2"/></svg>
          </button>
          <button type="button" class="a-iconbtn del" title="حذف" data-media-del data-label="<?= e(basename($m['file_path'])) ?>">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6"/><path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
          </button>
        </div>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
</div>

<script>
/* Page-specific wiring for the media grid (uses central DKA helpers). */
document.addEventListener('DOMContentLoaded', function () {
  'use strict';
  var fileInput = document.getElementById('mediaFile');
  var uploadBtn = document.getElementById('mediaUploadBtn');
  var busy = document.getElementById('mediaBusy');

  uploadBtn.addEventListener('click', function () { fileInput.click(); });
  fileInput.addEventListener('change', function () {
    if (!fileInput.files.length) return;
    var fd = new FormData();
    fd.append('image', fileInput.files[0]);
    fd.append('csrf_token', document.querySelector('meta[name="csrf-token"]').content);
    busy.style.display = 'inline-block';
    uploadBtn.disabled = true;
    fetch('/admin/media_upload.php', { method: 'POST', body: fd })
      .then(function (r) { return r.json(); })
      .then(function (res) {
        if (res.ok) { location.href = '/admin/media.php?toast=' + encodeURIComponent('تصویر آپلود شد'); }
        else { DKA.toast(res.error || 'آپلود ناموفق بود.', 'err'); }
      })
      .catch(function () { DKA.toast('خطا در ارتباط با سرور.', 'err'); })
      .finally(function () { busy.style.display = 'none'; uploadBtn.disabled = false; fileInput.value = ''; });
  });

  /* alt-text: save on change */
  document.querySelectorAll('[data-media-alt]').forEach(function (inp) {
    inp.addEventListener('change', function () {
      var id = inp.closest('[data-media-id]').getAttribute('data-media-id');
      DKA.post('/admin/media_action.php', { action: 'alt', id: id, alt: inp.value })
        .then(function (res) { DKA.toast(res.ok ? 'متن جایگزین ذخیره شد' : 'ذخیره ناموفق بود.', res.ok ? 'ok' : 'err'); });
    });
  });

  /* delete with in-use caution */
  document.querySelectorAll('[data-media-del]').forEach(function (btn) {
    btn.addEventListener('click', function () {
      var card = btn.closest('[data-media-id]');
      var overlay = DKA.confirmDelete(btn.getAttribute('data-label'), function () {
        DKA.post('/admin/media_action.php', { action: 'delete', id: card.getAttribute('data-media-id') })
          .then(function (res) {
            if (res.ok) { card.remove(); DKA.toast('حذف شد'); }
            else { DKA.toast('حذف ناموفق بود.', 'err'); }
          });
      });
      var note = overlay.querySelector('[data-note]');
      if (note) note.textContent = 'اگر این تصویر جایی از سایت استفاده شده باشد، همان‌جا خراب نمایش داده می‌شود. این کار قابل بازگشت نیست.';
    });
  });
});
</script>
<?php admin_page_end('media');
