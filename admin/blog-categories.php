<?php
/**
 * Blog categories — modal CRUD (title, slug, sort_order) per the
 * approved admin design; per-category content counts; delete detaches
 * posts (FK ON DELETE SET NULL).
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
            $title = trim((string) ($_POST['title'] ?? ''));
            $slug = slugify((string) ($_POST['slug'] ?? ''));
            $order = (int) en_digits((string) ($_POST['sort_order'] ?? '0'));
            if ($title === '') {
                $error = 'عنوان دسته را بنویس.';
            } elseif ($slug === '') {
                $error = 'نامک لاتین معتبر وارد کن.';
            } else {
                $stmt = db()->prepare('SELECT id FROM blog_categories WHERE slug = ? AND id != ?');
                $stmt->execute([$slug, $id]);
                if ($stmt->fetch()) {
                    $error = 'این نامک قبلاً استفاده شده است.';
                } else {
                    if ($id > 0) {
                        db()->prepare('UPDATE blog_categories SET title = ?, slug = ?, sort_order = ? WHERE id = ?')
                            ->execute([$title, $slug, $order, $id]);
                    } else {
                        db()->prepare('INSERT INTO blog_categories (title, slug, sort_order) VALUES (?, ?, ?)')
                            ->execute([$title, $slug, $order]);
                    }
                    cache_flush();
                    header('Location: /admin/blog-categories.php?toast=' . rawurlencode('ذخیره شد'));
                    exit;
                }
            }
        } elseif ($action === 'delete') {
            db()->prepare('DELETE FROM blog_categories WHERE id = ?')->execute([(int) ($_POST['id'] ?? 0)]);
            cache_flush();
            header('Location: /admin/blog-categories.php?toast=' . rawurlencode('حذف شد'));
            exit;
        }
    }
}

$cats = db()->query(
    'SELECT c.*, (SELECT COUNT(*) FROM blog_posts p WHERE p.category_id = c.id) AS cnt
     FROM blog_categories c ORDER BY c.sort_order, c.id'
)->fetchAll();

admin_page_start('دسته‌های بلاگ', 'blogcats', $error !== '' ? ['toast' => $error, 'toast_kind' => 'err'] : []);
?>
<div>
  <div style="display:flex;justify-content:flex-end;margin-bottom:14px;">
    <button type="button" class="a-btn a-btn-primary" data-cat-new>+ دسته جدید</button>
  </div>

  <?php if (!$cats): ?>
  <div class="a-empty">
    <svg width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="#c9c3b6" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M7 12h13"/><path d="M11 18h9"/></svg>
    <div class="a-empty-title">هنوز دسته‌ای ساخته نشده است.</div>
    <button type="button" class="a-btn a-btn-primary" data-cat-new>+ دسته جدید</button>
  </div>
  <?php else: ?>
  <div class="a-card" style="overflow:hidden;">
    <div data-adm-desktop style="display:grid;grid-template-columns:1.4fr 1fr .5fr .7fr .7fr;gap:10px;padding:11px 18px;border-bottom:1px solid #F0EDE6;font-size:12px;color:#9a9587;font-weight:700;">
      <span>عنوان</span><span>نامک</span><span>ترتیب</span><span>تعداد محتوا</span><span style="text-align:end;">اکشن‌ها</span>
    </div>
    <?php foreach ($cats as $c): ?>
    <div data-rowgrid class="a-row" style="display:grid;grid-template-columns:1.4fr 1fr .5fr .7fr .7fr;gap:10px;align-items:center;padding:12px 18px;border-bottom:1px solid #F6F4EF;font-size:13px;transition:background .15s ease;">
      <b style="color:#1F2A44;"><?= e($c['title']) ?></b>
      <span dir="ltr" style="color:#6B7280;font-size:12px;text-align:end;"><?= e($c['slug']) ?></span>
      <span style="color:#6B7280;"><?= fa_digits($c['sort_order']) ?></span>
      <span style="color:#6B7280;"><?= fa_digits($c['cnt']) ?></span>
      <span style="display:flex;justify-content:flex-end;gap:4px;">
        <button type="button" class="a-iconbtn edit" aria-label="ویرایش" data-cat-edit data-cat='<?= e(json_encode(['id' => (int) $c['id'], 'title' => $c['title'], 'slug' => $c['slug'], 'sort_order' => (int) $c['sort_order']], JSON_UNESCAPED_UNICODE)) ?>'>
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/></svg>
        </button>
        <form method="post" action="/admin/blog-categories.php" data-confirm="<?= e($c['title']) ?>" style="margin:0;display:inline-flex;">
          <?= csrf_field() ?>
          <input type="hidden" name="action" value="delete">
          <input type="hidden" name="id" value="<?= (int) $c['id'] ?>">
          <button type="submit" class="a-iconbtn del" aria-label="حذف">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6"/><path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
          </button>
        </form>
      </span>
    </div>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>
</div>

<!-- Category form modal (per approved design) -->
<div id="catModal" class="a-modal-overlay" style="display:none;">
  <div role="dialog" aria-modal="true" style="width:100%;max-width:460px;background:#fff;border-radius:14px;padding:24px;box-shadow:0 24px 60px -20px rgba(31,42,68,.5);max-height:86vh;overflow-y:auto;text-align:start;">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;">
      <b id="catModalTitle" style="font-size:15px;color:#1F2A44;">دسته جدید</b>
      <button type="button" data-cat-close aria-label="بستن" style="display:inline-flex;align-items:center;justify-content:center;width:38px;height:38px;background:none;border:none;color:#1F2A44;border-radius:8px;cursor:pointer;">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
      </button>
    </div>
    <form method="post" action="/admin/blog-categories.php">
      <?= csrf_field() ?>
      <input type="hidden" name="action" value="save">
      <input type="hidden" name="id" id="cat-id" value="0">
      <div style="margin-bottom:13px;">
        <label class="a-label-strong" for="cat-title">عنوان</label>
        <input class="a-input" type="text" id="cat-title" name="title" style="min-height:42px;font-size:13.5px;">
      </div>
      <div style="margin-bottom:13px;">
        <label class="a-label-strong" for="cat-slug">نامک (لاتین)</label>
        <input class="a-input" type="text" dir="ltr" id="cat-slug" name="slug" style="min-height:42px;font-size:13.5px;">
      </div>
      <div style="margin-bottom:13px;">
        <label class="a-label-strong" for="cat-order">ترتیب</label>
        <input class="a-input" type="text" id="cat-order" name="sort_order" style="min-height:42px;font-size:13.5px;">
      </div>
      <div style="display:flex;gap:10px;justify-content:flex-end;margin-top:6px;">
        <button type="button" data-cat-close class="a-btn a-btn-cancel" style="padding:9px 20px;font-size:13px;">انصراف</button>
        <button type="submit" class="a-btn a-btn-primary" style="padding:9px 24px;font-size:13px;">ذخیره</button>
      </div>
    </form>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
  'use strict';
  var modal = document.getElementById('catModal');
  function open(data) {
    document.getElementById('catModalTitle').textContent = data ? 'ویرایش دسته' : 'دسته جدید';
    document.getElementById('cat-id').value = data ? data.id : 0;
    document.getElementById('cat-title').value = data ? data.title : '';
    document.getElementById('cat-slug').value = data ? data.slug : '';
    document.getElementById('cat-order').value = data ? data.sort_order : '';
    modal.style.display = 'flex';
    document.getElementById('cat-title').focus();
  }
  document.querySelectorAll('[data-cat-new]').forEach(function (b) {
    b.addEventListener('click', function () { open(null); });
  });
  document.querySelectorAll('[data-cat-edit]').forEach(function (b) {
    b.addEventListener('click', function () { open(JSON.parse(b.getAttribute('data-cat'))); });
  });
  document.querySelectorAll('[data-cat-close]').forEach(function (b) {
    b.addEventListener('click', function () { modal.style.display = 'none'; });
  });
  modal.addEventListener('click', function (e) { if (e.target === modal) modal.style.display = 'none'; });
});
</script>
<?php admin_page_end('blogcats');
