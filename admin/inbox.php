<?php
/**
 * Inbox — two-pane per the approved admin design (mobile: list -> detail
 * with back), unread bold + coral dot + sidebar badge, all/unread filter,
 * detail with tel: link and Jalali datetime, read/unread toggle, delete,
 * and the Phase-5 mother-forward status badge + retry.
 */
require_once __DIR__ . '/../includes/admin_layout.php';
require_once __DIR__ . '/../includes/MotherApiClient.php';

require_admin();

$filter = ($_GET['f'] ?? '') === 'unread' ? 'unread' : 'all';
$sel_id = (int) ($_GET['id'] ?? 0);

/** Inbox URL keeping the current filter. */
function inbox_url(?int $id = null, ?string $f = null): string
{
    $f = $f ?? ($_GET['f'] ?? '');
    $args = array_filter(['f' => $f === 'unread' ? 'unread' : null, 'id' => $id ?: null]);
    return '/admin/inbox.php' . ($args ? '?' . http_build_query($args) : '');
}

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    if (!csrf_verify($_POST['csrf_token'] ?? null)) {
        http_response_code(403);
        exit('CSRF');
    }
    $id = (int) ($_POST['id'] ?? 0);
    switch ($_POST['action'] ?? '') {
        case 'toggle_read':
            db()->prepare('UPDATE submissions SET is_read = 1 - is_read WHERE id = ?')->execute([$id]);
            header('Location: ' . inbox_url($id));
            exit;
        case 'delete':
            db()->prepare('DELETE FROM submissions WHERE id = ?')->execute([$id]);
            header('Location: ' . inbox_url(null) . (str_contains(inbox_url(null), '?') ? '&' : '?') . 'toast=' . rawurlencode('حذف شد'));
            exit;
        case 'retry_forward':
            $result = mother_forward_submission($id);
            $msg = $result['ok'] ? 'به سامانه مادر ارسال شد' : 'ارسال ناموفق بود';
            $kind = $result['ok'] ? 'ok' : 'err';
            header('Location: ' . inbox_url($id) . (str_contains(inbox_url($id), '?') ? '&' : '?') . 'toast=' . rawurlencode($msg) . '&toast_kind=' . $kind);
            exit;
    }
    header('Location: ' . inbox_url());
    exit;
}

// Open a message: mark read (like the design's open handler).
if ($sel_id > 0) {
    db()->prepare('UPDATE submissions SET is_read = 1 WHERE id = ?')->execute([$sel_id]);
}

$where = $filter === 'unread' ? 'WHERE is_read = 0' : '';
$rows = db()->query("SELECT * FROM submissions $where ORDER BY created_at DESC, id DESC")->fetchAll();

$sel = null;
if ($sel_id > 0) {
    $stmt = db()->prepare('SELECT * FROM submissions WHERE id = ?');
    $stmt->execute([$sel_id]);
    $sel = $stmt->fetch() ?: null;
}

$FWD = [
    'pending' => ['در انتظار', '#EFEDE8', '#6B7280'],
    'sent'    => ['ارسال شد', '#E4F4EC', '#30A669'],
    'failed'  => ['ناموفق', '#FDF0F1', '#BB2D3B'],
];

admin_page_start('پیام‌های دریافتی', 'inbox');
?>
<div class="<?= $sel ? 'inbox-detail-open' : '' ?>">
  <div style="display:flex;gap:6px;margin-bottom:14px;">
    <a href="<?= e(inbox_url(null, 'all')) ?>" style="display:inline-flex;align-items:center;min-height:40px;padding:8px 16px;border-radius:9px;font-size:13px;font-weight:700;text-decoration:none;background:<?= $filter === 'all' ? '#E76F51' : '#fff' ?>;color:<?= $filter === 'all' ? '#fff' : '#3D4A6B' ?>;">همه</a>
    <a href="<?= e(inbox_url(null, 'unread')) ?>" style="display:inline-flex;align-items:center;min-height:40px;padding:8px 16px;border-radius:9px;font-size:13px;font-weight:700;text-decoration:none;background:<?= $filter === 'unread' ? '#E76F51' : '#fff' ?>;color:<?= $filter === 'unread' ? '#fff' : '#3D4A6B' ?>;">خوانده‌نشده</a>
  </div>

  <?php if (!$rows && !$sel): ?>
  <div class="a-empty">
    <svg width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="#c9c3b6" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
    <div class="a-empty-title"><?= $filter === 'unread' ? 'پیام خوانده‌نشده‌ای نداری' : 'هنوز پیامی نداری' ?></div>
  </div>
  <?php else: ?>
  <div id="inboxRow" style="display:flex;gap:14px;align-items:flex-start;">
    <!-- List -->
    <div id="inboxList" class="a-card" style="flex:0 0 320px;overflow:hidden;">
      <?php if (!$rows): ?>
      <div style="padding:32px 16px;text-align:center;color:#9a9587;font-size:13px;">پیام خوانده‌نشده‌ای نیست.</div>
      <?php endif; ?>
      <?php foreach ($rows as $m): $is_sel = $sel && (int) $sel['id'] === (int) $m['id']; ?>
      <a href="<?= e(inbox_url((int) $m['id'])) ?>" class="a-row" style="display:flex;flex-direction:column;gap:3px;width:100%;background:<?= $is_sel ? '#FCEFEA' : 'transparent' ?>;border-bottom:1px solid #F6F4EF;padding:12px 16px;text-align:start;text-decoration:none;">
        <span style="display:flex;align-items:center;gap:8px;width:100%;">
          <?php if (!$m['is_read']): ?><span style="flex:none;width:8px;height:8px;border-radius:50%;background:#E76F51;"></span><?php endif; ?>
          <b style="flex:1;font-size:13px;color:#1F2A44;font-weight:<?= $m['is_read'] ? '400' : '700' ?>;"><?= e($m['name']) ?></b>
          <span style="flex:none;font-size:11px;color:#9a9587;"><?= e(fa_time_ago($m['created_at'])) ?></span>
        </span>
        <span style="font-size:12px;color:#6B7280;"><?= e($m['subject'] ?? '') ?></span>
      </a>
      <?php endforeach; ?>
    </div>

    <!-- Detail -->
    <div id="inboxDetail" class="a-card" style="flex:1;min-width:0;padding:20px;">
      <?php if (!$sel): ?>
      <div style="text-align:center;color:#9a9587;font-size:13px;padding:60px 0;">یک پیام را از لیست انتخاب کن</div>
      <?php else:
          [$fw_label, $fw_bg, $fw_color] = $FWD[$sel['forward_status']] ?? $FWD['pending'];
      ?>
      <a data-adm-mobile href="<?= e(inbox_url(null)) ?>" style="align-items:center;gap:7px;min-height:40px;background:none;border:none;font-size:13px;font-weight:700;color:#E76F51;text-decoration:none;margin-bottom:10px;padding:0;">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
        بازگشت به لیست
      </a>
      <div style="display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;gap:10px;border-bottom:1px solid #F0EDE6;padding-bottom:14px;margin-bottom:14px;">
        <div>
          <b style="display:block;font-size:16px;color:#1F2A44;"><?= e($sel['name']) ?></b>
          <span style="font-size:12px;color:#9a9587;"><?= e(jalali_date($sel['created_at'], true)) ?></span>
        </div>
        <div style="display:flex;gap:6px;flex-wrap:wrap;">
          <form method="post" action="<?= e(inbox_url((int) $sel['id'])) ?>" style="margin:0;">
            <?= csrf_field() ?>
            <input type="hidden" name="action" value="toggle_read">
            <input type="hidden" name="id" value="<?= (int) $sel['id'] ?>">
            <button type="submit" class="a-btn a-btn-outline" style="min-height:38px;padding:7px 13px;font-size:12.5px;"><?= $sel['is_read'] ? 'علامت‌گذاری به‌عنوان خوانده‌نشده' : 'علامت‌گذاری به‌عنوان خوانده‌شده' ?></button>
          </form>
          <form method="post" action="<?= e(inbox_url()) ?>" data-confirm="پیام <?= e($sel['name']) ?>" style="margin:0;">
            <?= csrf_field() ?>
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="id" value="<?= (int) $sel['id'] ?>">
            <button type="submit" style="display:inline-flex;align-items:center;gap:6px;min-height:38px;background:#fff;border:1px solid #F3CDD1;border-radius:9px;padding:7px 13px;font-size:12.5px;font-weight:700;color:#BB2D3B;cursor:pointer;">حذف</button>
          </form>
        </div>
      </div>
      <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:10px;margin-bottom:16px;font-size:13px;">
        <div style="background:#F7F6F3;border-radius:9px;padding:10px 13px;">
          <span style="display:block;font-size:11px;color:#9a9587;margin-bottom:2px;">شماره موبایل</span>
          <a href="tel:<?= e($sel['phone']) ?>" dir="ltr" style="display:inline-flex;align-items:center;gap:6px;font-weight:700;color:#E76F51;text-decoration:none;"><?= e(fa_digits($sel['phone'])) ?><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.13.96.36 1.9.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.91.34 1.85.57 2.81.7A2 2 0 0 1 22 16.92z"/></svg></a>
        </div>
        <div style="background:#F7F6F3;border-radius:9px;padding:10px 13px;">
          <span style="display:block;font-size:11px;color:#9a9587;margin-bottom:2px;">نوع فعالیت</span>
          <b style="color:#1F2A44;"><?= e($sel['business_type'] ?: '—') ?></b>
        </div>
        <div style="background:#F7F6F3;border-radius:9px;padding:10px 13px;">
          <span style="display:block;font-size:11px;color:#9a9587;margin-bottom:2px;">موضوع</span>
          <b style="color:#1F2A44;"><?= e($sel['subject'] ?: '—') ?></b>
        </div>
        <div style="background:#F7F6F3;border-radius:9px;padding:10px 13px;">
          <span style="display:block;font-size:11px;color:#9a9587;margin-bottom:2px;">ارسال به سامانه مادر</span>
          <span style="display:inline-flex;align-items:center;gap:8px;flex-wrap:wrap;">
            <span class="a-badge" style="background:<?= e($fw_bg) ?>;color:<?= e($fw_color) ?>;"><?= e($fw_label) ?></span>
            <?php if ((int) $sel['forward_attempts'] > 0): ?>
            <span style="font-size:11px;color:#9a9587;"><?= fa_digits($sel['forward_attempts']) ?> تلاش</span>
            <?php endif; ?>
            <form method="post" action="<?= e(inbox_url((int) $sel['id'])) ?>" style="margin:0;display:inline-flex;">
              <?= csrf_field() ?>
              <input type="hidden" name="action" value="retry_forward">
              <input type="hidden" name="id" value="<?= (int) $sel['id'] ?>">
              <button type="submit" class="a-link-coral" style="font-size:12px;">ارسال مجدد به سامانه مادر</button>
            </form>
          </span>
          <?php if ($sel['forward_status'] === 'failed' && $sel['last_forward_error']): ?>
          <span style="display:block;font-size:11px;color:#BB2D3B;margin-top:4px;" dir="ltr"><?= e(mb_substr($sel['last_forward_error'], 0, 160)) ?></span>
          <?php endif; ?>
        </div>
      </div>
      <p style="font-size:14px;line-height:2;color:#3D4A6B;margin:0;"><?= nl2br(e($sel['message'] ?? '')) ?></p>
      <?php endif; ?>
    </div>
  </div>
  <?php endif; ?>
</div>
<?php admin_page_end('inbox');
