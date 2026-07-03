<?php
/**
 * Admin dashboard — stat cards from real queries, latest 5 submissions
 * (empty state until Phase 5 delivers the contact backend), quick-action
 * buttons disabled until their phases.
 */
require_once __DIR__ . '/../includes/admin_layout.php';

$stats_q = [
    'posts'  => "SELECT COUNT(*) AS n FROM blog_posts WHERE status = 'published'",
    'tuts'   => "SELECT COUNT(*) AS n FROM tutorials WHERE status = 'published'",
    'unread' => 'SELECT COUNT(*) AS n FROM submissions WHERE is_read = 0',
    'testis' => "SELECT COUNT(*) AS n FROM testimonials WHERE status = 'published'",
];
$counts = [];
foreach ($stats_q as $k => $sql) {
    $counts[$k] = (int) db()->query($sql)->fetch()['n'];
}

$recent = db()->query(
    'SELECT id, name, subject, created_at, is_read FROM submissions ORDER BY created_at DESC, id DESC LIMIT 5'
)->fetchAll();

$icons = [
    'blog'  => 'M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z M14 2v6h6 M8 13h8 M8 17h5',
    'tut'   => 'M22 10v6M2 10l10-5 10 5-10 5z M6 12v5c3 3 9 3 12 0v-5',
    'inbox' => 'M22 12h-6l-2 3h-4l-2-3H2 M5.45 5.11 2 12v6a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-6l-3.45-6.89A2 2 0 0 0 16.76 4H7.24a2 2 0 0 0-1.79 1.11z',
    'testi' => 'M7.9 20A9 9 0 1 0 4 16.1L2 22Z',
];
$stat_cards = [
    ['مقاله‌های منتشرشده', $counts['posts'], $icons['blog'], '#EEF1F7', '#1F2A44', '#1F2A44'],
    ['آموزش‌های منتشرشده', $counts['tuts'], $icons['tut'], '#EEF1F7', '#1F2A44', '#1F2A44'],
    ['پیام‌های خوانده‌نشده', $counts['unread'], $icons['inbox'], '#FCEFEA', '#D45A3D', '#E76F51'],
    ['نظرات فعال', $counts['testis'], $icons['testi'], '#EEF1F7', '#1F2A44', '#1F2A44'],
];

admin_page_start('داشبورد', 'dash');
?>
<div>
  <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:14px;margin-bottom:18px;">
    <?php foreach ($stat_cards as [$label, $value, $icon, $icon_bg, $icon_color, $num_color]): ?>
    <div class="a-card" style="padding:16px 18px;">
      <div style="display:flex;align-items:center;gap:9px;font-size:12.5px;color:#6B7280;margin-bottom:8px;">
        <span style="display:inline-flex;width:30px;height:30px;align-items:center;justify-content:center;background:<?= e($icon_bg) ?>;border-radius:8px;color:<?= e($icon_color) ?>;"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="<?= e($icon) ?>"/></svg></span>
        <?= e($label) ?>
      </div>
      <b style="font-size:26px;color:<?= e($num_color) ?>;"><?= fa_digits($value) ?></b>
    </div>
    <?php endforeach; ?>
  </div>

  <div style="display:flex;gap:10px;margin-bottom:18px;">
    <button type="button" class="a-btn a-btn-primary" disabled title="به‌زودی">+ مقاله جدید</button>
    <button type="button" class="a-btn a-btn-outline" disabled title="به‌زودی">+ آموزش جدید</button>
  </div>

  <div class="a-card" style="overflow:hidden;">
    <div style="display:flex;align-items:center;justify-content:space-between;padding:13px 18px;border-bottom:1px solid #F0EDE6;">
      <b style="font-size:14px;color:#1F2A44;">آخرین پیام‌های دریافتی</b>
      <button type="button" class="a-link-coral" disabled title="به‌زودی">همه پیام‌ها ←</button>
    </div>
    <?php if (!$recent): ?>
    <div style="padding:40px 20px;text-align:center;">
      <svg width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="#c9c3b6" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" style="margin-bottom:10px;"><path d="M22 12h-6l-2 3h-4l-2-3H2"/><path d="M5.45 5.11 2 12v6a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-6l-3.45-6.89A2 2 0 0 0 16.76 4H7.24a2 2 0 0 0-1.79 1.11z"/></svg>
      <div style="font-size:14px;color:#6B7280;">هنوز پیامی دریافت نشده است.</div>
    </div>
    <?php else: foreach ($recent as $m): ?>
    <div style="display:flex;align-items:center;gap:10px;border-bottom:1px solid #F6F4EF;padding:11px 18px;font-size:13px;">
      <?php if (!$m['is_read']): ?><span style="flex:none;width:8px;height:8px;border-radius:50%;background:#E76F51;"></span><?php endif; ?>
      <span style="flex:1;color:#1F2A44;font-weight:<?= $m['is_read'] ? '400' : '700' ?>;"><?= e($m['name']) ?></span>
      <span style="flex:2;color:#6B7280;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;"><?= e($m['subject'] ?? '') ?></span>
      <span style="flex:none;color:#9a9587;font-size:12px;"><?= e(fa_time_ago($m['created_at'])) ?></span>
    </div>
    <?php endforeach; endif; ?>
  </div>
</div>
<?php admin_page_end('dash');
