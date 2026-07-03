<?php
/**
 * Blog posts list — search, category/status filters, pagination,
 * status badges, Jalali dates, edit/delete actions. Per the approved
 * admin design (list screen, blog columns).
 */
require_once __DIR__ . '/../includes/admin_layout.php';
require_once __DIR__ . '/../includes/cache.php';

require_admin();

// Delete action (from row button, confirm modal in front).
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST' && ($_POST['action'] ?? '') === 'delete') {
    if (!csrf_verify($_POST['csrf_token'] ?? null)) {
        http_response_code(403);
        exit('CSRF');
    }
    db()->prepare('DELETE FROM blog_posts WHERE id = ?')->execute([(int) ($_POST['id'] ?? 0)]);
    cache_flush();
    header('Location: /admin/blog.php?toast=' . rawurlencode('حذف شد'));
    exit;
}

$q = trim((string) ($_GET['q'] ?? ''));
$cat_f = (int) ($_GET['cat'] ?? 0);
$status_f = in_array($_GET['status'] ?? '', ['published', 'draft'], true) ? $_GET['status'] : '';
$page = max(1, (int) ($_GET['page'] ?? 1));
$per_page = 10;

$where = '1=1';
$args = [];
if ($q !== '') {
    $where .= ' AND p.title LIKE ?';
    $args[] = '%' . $q . '%';
}
if ($cat_f > 0) {
    $where .= ' AND p.category_id = ?';
    $args[] = $cat_f;
}
if ($status_f !== '') {
    $where .= ' AND p.status = ?';
    $args[] = $status_f;
}

$stmt = db()->prepare("SELECT COUNT(*) AS n FROM blog_posts p WHERE $where");
$stmt->execute($args);
$total = (int) $stmt->fetch()['n'];
$pages = max(1, (int) ceil($total / $per_page));
$page = min($page, $pages);
$offset = ($page - 1) * $per_page;

$stmt = db()->prepare(
    "SELECT p.*, c.title AS cat_title FROM blog_posts p
     LEFT JOIN blog_categories c ON c.id = p.category_id
     WHERE $where ORDER BY COALESCE(p.published_at, p.updated_at) DESC, p.id DESC
     LIMIT $per_page OFFSET $offset"
);
$stmt->execute($args);
$rows = $stmt->fetchAll();

$cats = db()->query('SELECT id, title FROM blog_categories ORDER BY sort_order, id')->fetchAll();
$filtered = $q !== '' || $cat_f > 0 || $status_f !== '';

/** Keep current filters in pagination links. */
function blog_list_url(int $p): string
{
    $args = array_filter([
        'q' => $_GET['q'] ?? '',
        'cat' => $_GET['cat'] ?? '',
        'status' => $_GET['status'] ?? '',
        'page' => $p > 1 ? $p : '',
    ], fn ($v) => $v !== '' && $v !== 0);
    return '/admin/blog.php' . ($args ? '?' . http_build_query($args) : '');
}

admin_page_start('بلاگ', 'blog');
?>
<div>
  <form method="get" action="/admin/blog.php" style="display:flex;flex-wrap:wrap;align-items:center;gap:10px;margin-bottom:14px;">
    <div style="position:relative;flex:1 1 200px;max-width:300px;">
      <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#9a9587" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="position:absolute;top:50%;inset-inline-start:11px;transform:translateY(-50%);"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
      <input class="a-input" type="search" name="q" placeholder="جستجو…" value="<?= e($q) ?>" style="background:#fff;padding-inline-start:34px;" onchange="this.form.submit()">
    </div>
    <select class="a-input" name="cat" style="width:auto;background:#fff;" onchange="this.form.submit()">
      <option value="">همه دسته‌ها</option>
      <?php foreach ($cats as $c): ?>
      <option value="<?= (int) $c['id'] ?>" <?= $cat_f === (int) $c['id'] ? 'selected' : '' ?>><?= e($c['title']) ?></option>
      <?php endforeach; ?>
    </select>
    <select class="a-input" name="status" style="width:auto;background:#fff;" onchange="this.form.submit()">
      <option value="">همه وضعیت‌ها</option>
      <option value="published" <?= $status_f === 'published' ? 'selected' : '' ?>>منتشرشده</option>
      <option value="draft" <?= $status_f === 'draft' ? 'selected' : '' ?>>پیش‌نویس</option>
    </select>
    <a href="/admin/blog-edit.php" class="a-btn a-btn-primary" style="margin-inline-start:auto;text-decoration:none;">+ مقاله جدید</a>
  </form>

  <?php if (!$rows): ?>
  <div class="a-empty">
    <svg width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="#c9c3b6" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/></svg>
    <div class="a-empty-title"><?= $filtered ? 'با این فیلترها چیزی پیدا نشد' : 'هنوز مقاله‌ای ننوشته‌ای' ?></div>
    <a href="/admin/blog-edit.php" class="a-btn a-btn-primary" style="text-decoration:none;">+ مقاله جدید</a>
  </div>
  <?php else: ?>
  <div class="a-card" style="overflow:hidden;">
    <div data-adm-desktop style="display:grid;grid-template-columns:2.2fr 1fr .9fr .9fr .8fr;gap:10px;padding:11px 18px;border-bottom:1px solid #F0EDE6;font-size:12px;color:#9a9587;font-weight:700;">
      <span>عنوان</span><span>دسته</span><span>وضعیت</span><span>تاریخ</span><span style="text-align:end;">اکشن‌ها</span>
    </div>
    <?php foreach ($rows as $r): $pub = $r['status'] === 'published'; ?>
    <div data-rowgrid class="a-row" style="display:grid;grid-template-columns:2.2fr 1fr .9fr .9fr .8fr;gap:10px;align-items:center;padding:12px 18px;border-bottom:1px solid #F6F4EF;font-size:13px;transition:background .15s ease;">
      <b style="color:#1F2A44;line-height:1.6;"><?= e($r['title']) ?></b>
      <span style="color:#6B7280;"><?= e($r['cat_title'] ?? '—') ?></span>
      <span><span class="a-badge" style="background:<?= $pub ? '#E4F4EC' : '#EFEDE8' ?>;color:<?= $pub ? '#30A669' : '#6B7280' ?>;"><?= $pub ? 'منتشرشده' : 'پیش‌نویس' ?></span></span>
      <span style="color:#6B7280;font-size:12.5px;"><?= e(jalali_date($r['published_at'] ?? $r['updated_at'])) ?></span>
      <span style="display:flex;justify-content:flex-end;gap:4px;">
        <a href="/admin/blog-edit.php?id=<?= (int) $r['id'] ?>" class="a-iconbtn edit" aria-label="ویرایش">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/></svg>
        </a>
        <form method="post" action="/admin/blog.php" data-confirm="<?= e($r['title']) ?>" style="margin:0;display:inline-flex;">
          <?= csrf_field() ?>
          <input type="hidden" name="action" value="delete">
          <input type="hidden" name="id" value="<?= (int) $r['id'] ?>">
          <button type="submit" class="a-iconbtn del" aria-label="حذف">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6"/><path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
          </button>
        </form>
      </span>
    </div>
    <?php endforeach; ?>
    <?php if ($pages > 1): ?>
    <div style="display:flex;justify-content:center;gap:6px;padding:14px;">
      <?php for ($p = 1; $p <= $pages; $p++): ?>
        <?php if ($p === $page): ?>
        <span style="display:inline-flex;align-items:center;justify-content:center;min-width:36px;min-height:36px;border-radius:9px;background:#E76F51;color:#fff;font-weight:700;font-size:13px;"><?= fa_digits($p) ?></span>
        <?php else: ?>
        <a href="<?= e(blog_list_url($p)) ?>" style="display:inline-flex;align-items:center;justify-content:center;min-width:36px;min-height:36px;border-radius:9px;background:#fff;border:1px solid #E8E6E1;color:#3D4A6B;font-weight:700;font-size:13px;"><?= fa_digits($p) ?></a>
        <?php endif; ?>
      <?php endfor; ?>
    </div>
    <?php endif; ?>
  </div>
  <?php endif; ?>
</div>
<?php admin_page_end('blog');
