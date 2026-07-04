<?php
/**
 * Testimonials admin — card grid + modal CRUD per the approved admin
 * design. Only published testimonials appear on the home page.
 */
require_once __DIR__ . '/../includes/admin_layout.php';
require_once __DIR__ . '/../includes/cache.php';

require_admin();

$error = '';

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    if (!csrf_verify($_POST['csrf_token'] ?? null)) {
        http_response_code(403);
        $error = 'درخواست نامعتبر (CSRF). صفحه را از نو باز کن.';
    } else {
        $action = $_POST['action'] ?? '';
        if ($action === 'save') {
            $id = (int) ($_POST['id'] ?? 0);
            $quote = trim((string) ($_POST['quote'] ?? ''));
            $name = trim((string) ($_POST['person_name'] ?? ''));
            $city = trim((string) ($_POST['city'] ?? ''));
            $business = trim((string) ($_POST['business_type'] ?? ''));
            $order = (int) en_digits((string) ($_POST['sort_order'] ?? '0'));
            $status = ($_POST['status'] ?? '') === 'draft' ? 'draft' : 'published';
            if ($quote === '') {
                $error = 'نقل‌قول را بنویس.';
            } elseif ($name === '') {
                $error = 'نام را بنویس.';
            } else {
                if ($id > 0) {
                    db()->prepare('UPDATE testimonials SET quote = ?, person_name = ?, city = ?, business_type = ?, sort_order = ?, status = ? WHERE id = ?')
                        ->execute([$quote, $name, $city, $business, $order, $status, $id]);
                } else {
                    db()->prepare('INSERT INTO testimonials (quote, person_name, city, business_type, sort_order, status) VALUES (?, ?, ?, ?, ?, ?)')
                        ->execute([$quote, $name, $city, $business, $order, $status]);
                }
                cache_flush();
                header('Location: /admin/testimonials.php?toast=' . rawurlencode('ذخیره شد'));
                exit;
            }
        } elseif ($action === 'delete') {
            db()->prepare('DELETE FROM testimonials WHERE id = ?')->execute([(int) ($_POST['id'] ?? 0)]);
            cache_flush();
            header('Location: /admin/testimonials.php?toast=' . rawurlencode('حذف شد'));
            exit;
        }
    }
}

$items = db()->query('SELECT * FROM testimonials ORDER BY sort_order, id')->fetchAll();

admin_page_start('نظر مشتریان', 'testi', $error !== '' ? ['toast' => $error, 'toast_kind' => 'err'] : []);
?>
<div>
  <div style="display:flex;justify-content:flex-end;margin-bottom:14px;">
    <button type="button" class="a-btn a-btn-primary" data-testi-new>+ نظر جدید</button>
  </div>

  <?php if (!$items): ?>
  <div class="a-empty">
    <svg width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="#c9c3b6" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M7.9 20A9 9 0 1 0 4 16.1L2 22Z"/></svg>
    <div class="a-empty-title">هنوز نظری ثبت نشده است.</div>
    <button type="button" class="a-btn a-btn-primary" data-testi-new>+ نظر جدید</button>
  </div>
  <?php else: ?>
  <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(260px,1fr));gap:14px;">
    <?php foreach ($items as $t): ?>
    <?php $published = $t['status'] === 'published'; ?>
    <div class="a-card" style="display:flex;flex-direction:column;padding:16px 18px;">
      <p style="font-size:13.5px;line-height:1.9;color:#3D4A6B;margin:0 0 12px;flex:1;"><?= e($t['quote']) ?></p>
      <div style="font-size:13.5px;font-weight:700;color:#1F2A44;"><?= e($t['person_name']) ?></div>
      <div style="font-size:12px;color:#6B7280;margin-top:2px;"><?= e(implode(' — ', array_filter([$t['city'], $t['business_type']], fn ($v) => $v !== null && trim((string) $v) !== ''))) ?></div>
      <div style="display:flex;align-items:center;justify-content:space-between;gap:8px;border-top:1px solid #F0EDE6;padding-top:10px;margin-top:12px;">
        <span class="a-badge" style="<?= $published ? 'background:#E4F4EC;color:#30A669;' : 'background:#EFEDE8;color:#6B7280;' ?>"><?= $published ? 'فعال' : 'غیرفعال' ?></span>
        <span style="display:flex;gap:4px;">
          <button type="button" class="a-iconbtn edit" aria-label="ویرایش" data-testi-edit data-testi='<?= e(json_encode(['id' => (int) $t['id'], 'quote' => $t['quote'], 'person_name' => $t['person_name'], 'city' => (string) $t['city'], 'business_type' => (string) $t['business_type'], 'sort_order' => (int) $t['sort_order'], 'status' => $t['status']], JSON_UNESCAPED_UNICODE)) ?>'>
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/></svg>
          </button>
          <form method="post" action="/admin/testimonials.php" data-confirm="<?= e($t['person_name']) ?>" style="margin:0;display:inline-flex;">
            <?= csrf_field() ?>
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="id" value="<?= (int) $t['id'] ?>">
            <button type="submit" class="a-iconbtn del" aria-label="حذف">
              <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6"/><path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
            </button>
          </form>
        </span>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>
</div>

<!-- Testimonial form modal (per approved design) -->
<div id="testiModal" class="a-modal-overlay" style="display:none;">
  <div role="dialog" aria-modal="true" style="width:100%;max-width:460px;background:#fff;border-radius:14px;padding:24px;box-shadow:0 24px 60px -20px rgba(31,42,68,.5);max-height:86vh;overflow-y:auto;text-align:start;">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;">
      <b id="testiModalTitle" style="font-size:15px;color:#1F2A44;">نظر جدید</b>
      <button type="button" data-testi-close aria-label="بستن" style="display:inline-flex;align-items:center;justify-content:center;width:38px;height:38px;background:none;border:none;color:#1F2A44;border-radius:8px;cursor:pointer;">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
      </button>
    </div>
    <form method="post" action="/admin/testimonials.php">
      <?= csrf_field() ?>
      <input type="hidden" name="action" value="save">
      <input type="hidden" name="id" id="testi-id" value="0">
      <div style="margin-bottom:13px;">
        <label class="a-label-strong" for="testi-quote">نقل‌قول</label>
        <textarea class="a-input" id="testi-quote" name="quote" rows="4" style="font-size:13.5px;resize:vertical;"></textarea>
      </div>
      <div style="margin-bottom:13px;">
        <label class="a-label-strong" for="testi-name">نام</label>
        <input class="a-input" type="text" id="testi-name" name="person_name" style="min-height:42px;font-size:13.5px;">
      </div>
      <div style="margin-bottom:13px;">
        <label class="a-label-strong" for="testi-city">شهر</label>
        <input class="a-input" type="text" id="testi-city" name="city" style="min-height:42px;font-size:13.5px;">
      </div>
      <div style="margin-bottom:13px;">
        <label class="a-label-strong" for="testi-business">نوع فعالیت</label>
        <input class="a-input" type="text" id="testi-business" name="business_type" style="min-height:42px;font-size:13.5px;">
      </div>
      <div style="margin-bottom:13px;">
        <label class="a-label-strong" for="testi-order">ترتیب</label>
        <input class="a-input" type="text" id="testi-order" name="sort_order" style="min-height:42px;font-size:13.5px;">
      </div>
      <div style="margin-bottom:13px;">
        <label class="a-label-strong" for="testi-status">وضعیت</label>
        <select class="a-input" id="testi-status" name="status" style="min-height:42px;font-size:13.5px;">
          <option value="published">فعال</option>
          <option value="draft">غیرفعال</option>
        </select>
      </div>
      <div style="display:flex;gap:10px;justify-content:flex-end;margin-top:6px;">
        <button type="button" data-testi-close class="a-btn a-btn-cancel" style="padding:9px 20px;font-size:13px;">انصراف</button>
        <button type="submit" class="a-btn a-btn-primary" style="padding:9px 24px;font-size:13px;">ذخیره</button>
      </div>
    </form>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
  'use strict';
  var modal = document.getElementById('testiModal');
  function open(data) {
    document.getElementById('testiModalTitle').textContent = data ? 'ویرایش نظر' : 'نظر جدید';
    document.getElementById('testi-id').value = data ? data.id : 0;
    document.getElementById('testi-quote').value = data ? data.quote : '';
    document.getElementById('testi-name').value = data ? data.person_name : '';
    document.getElementById('testi-city').value = data ? data.city : '';
    document.getElementById('testi-business').value = data ? data.business_type : '';
    document.getElementById('testi-order').value = data ? data.sort_order : '';
    document.getElementById('testi-status').value = data ? data.status : 'published';
    modal.style.display = 'flex';
    document.getElementById('testi-quote').focus();
  }
  document.querySelectorAll('[data-testi-new]').forEach(function (b) {
    b.addEventListener('click', function () { open(null); });
  });
  document.querySelectorAll('[data-testi-edit]').forEach(function (b) {
    b.addEventListener('click', function () { open(JSON.parse(b.getAttribute('data-testi'))); });
  });
  document.querySelectorAll('[data-testi-close]').forEach(function (b) {
    b.addEventListener('click', function () { modal.style.display = 'none'; });
  });
  modal.addEventListener('click', function (e) { if (e.target === modal) modal.style.display = 'none'; });
});
</script>
<?php admin_page_end('testi');
