<?php
/**
 * Admin panel shell (single definition): sidebar + topbar + mobile
 * drawer, per the approved admin design. Every admin screen calls
 * admin_page_start() / admin_page_end().
 */

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/auth.php';

/**
 * Sidebar config — the plan's exact item order. Items for future
 * phases render disabled with a Persian "coming soon" tooltip.
 * [key, label, lucide-path, href, enabled]
 */
function admin_nav(): array
{
    $icons = [
        'dash'     => 'M3 13h8V3H3z M13 21h8V11h-8z M13 3v4h8V3z M3 21h8v-4H3z',
        'blog'     => 'M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z M14 2v6h6 M8 13h8 M8 17h5',
        'cats'     => 'M3 6h18 M7 12h13 M11 18h9',
        'tut'      => 'M22 10v6M2 10l10-5 10 5-10 5z M6 12v5c3 3 9 3 12 0v-5',
        'faq'      => 'M12 22a10 10 0 1 0 0-20 10 10 0 0 0 0 20z M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3 M12 17h.01',
        'testi'    => 'M7.9 20A9 9 0 1 0 4 16.1L2 22Z',
        'price'    => 'M12 2v20 M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6',
        'inbox'    => 'M22 12h-6l-2 3h-4l-2-3H2 M5.45 5.11 2 12v6a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-6l-3.45-6.89A2 2 0 0 0 16.76 4H7.24a2 2 0 0 0-1.79 1.11z',
        'media'    => 'M3 5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z M9 11a2 2 0 1 0 0-4 2 2 0 0 0 0 4z M21 15l-3.09-3.09a2 2 0 0 0-2.82 0L6 21',
        'seo'      => 'M11 19a8 8 0 1 0 0-16 8 8 0 0 0 0 16z M21 21l-4.3-4.3',
        'settings' => 'M12 15a3 3 0 1 0 0-6 3 3 0 0 0 0 6z M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 1 1-4 0v-.09a1.65 1.65 0 0 0-1-1.51 1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 1 1 0-4h.09a1.65 1.65 0 0 0 1.51-1 1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06a1.65 1.65 0 0 0 1.82.33h.01a1.65 1.65 0 0 0 1-1.51V3a2 2 0 1 1 4 0v.09a1.65 1.65 0 0 0 1 1.51h.01a1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82v.01a1.65 1.65 0 0 0 1.51 1H21a2 2 0 1 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z',
        'user'     => 'M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2 M12 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8z',
    ];
    return [
        ['dash',     'داشبورد',           $icons['dash'],     '/admin/dashboard.php', true],
        ['blog',     'بلاگ',              $icons['blog'],     '/admin/blog.php',      true],
        ['blogcats', 'دسته‌های بلاگ',      $icons['cats'],     '/admin/blog-categories.php', true],
        ['tuts',     'آموزش‌ها',           $icons['tut'],      '#',                    false],
        ['tutcats',  'دسته‌های آموزش',     $icons['cats'],     '#',                    false],
        ['faq',      'سؤالات متداول',      $icons['faq'],      '#',                    false],
        ['testi',    'نظر مشتریان',        $icons['testi'],    '#',                    false],
        ['pricing',  'تعرفه‌ها',           $icons['price'],    '#',                    false],
        ['inbox',    'پیام‌های دریافتی',   $icons['inbox'],    '#',                    false],
        ['media',    'رسانه‌ها',           $icons['media'],    '/admin/media.php',     true],
        ['seo',      'سئو',               $icons['seo'],      '#',                    false],
        ['settings', 'تنظیمات سایت',      $icons['settings'], '/admin/settings.php',  true],
        ['account',  'حساب کاربری',       $icons['user'],     '/admin/account.php',   true],
    ];
}

/** Unread-submissions badge (the design's badge slot on the inbox item). */
function admin_unread_count(): int
{
    try {
        return (int) db()->query('SELECT COUNT(*) AS n FROM submissions WHERE is_read = 0')->fetch()['n'];
    } catch (Throwable $e) {
        return 0;
    }
}

/** Render one sidebar/drawer item. */
function admin_nav_item(array $item, string $active, int $unread, bool $drawer = false): void
{
    [$key, $label, $icon, $href, $enabled] = $item;
    $is_active = $key === $active;
    $cls = 'a-side-item' . ($is_active ? ' active' : '') . ($enabled ? '' : ' disabled');
    $tag = $enabled ? 'a' : 'span';
    $attrs = $enabled ? ' href="' . e($href) . '"' : ' title="به‌زودی" aria-disabled="true"';
    ?>
    <<?= $tag ?> class="<?= $cls ?>"<?= $attrs ?>>
      <?php if ($is_active): ?><span class="a-side-stitch"></span><?php endif; ?>
      <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex:none;"><path d="<?= e($icon) ?>"/></svg>
      <span class="a-side-label" style="flex:1;white-space:nowrap;"><?= e($label) ?></span>
      <?php if ($key === 'inbox' && $unread > 0): ?><span class="a-side-badge"><?= fa_digits($unread) ?></span><?php endif; ?>
    </<?= $tag ?>>
    <?php
}

/**
 * Open the admin page: <head>, sidebar, topbar, and the work area.
 * $active = nav key; $opts: toast (flash message), toast_kind.
 */
function admin_page_start(string $title, string $active, array $opts = []): void
{
    require_admin();
    $unread = admin_unread_count();
    $username = $_SESSION['admin_username'] ?? 'مدیر';
    $initial = mb_substr($username, 0, 1, 'UTF-8');
    $flash = $opts['toast'] ?? ($_GET['toast'] ?? '');
    ?><!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex, nofollow">
<title><?= e($title) ?> — پنل مدیریت دوختک</title>
<meta name="csrf-token" content="<?= e(csrf_token()) ?>">
<?php if ($flash !== ''): ?><meta name="admin-toast" content="<?= e($flash) ?>" data-kind="<?= e($opts['toast_kind'] ?? ($_GET['toast_kind'] ?? 'ok')) ?>"><?php endif; ?>
<link rel="icon" type="image/png" href="/assets/images/Logo01-NEW.png">
<link rel="stylesheet" href="/assets/css/site.css">
<link rel="stylesheet" href="/assets/css/admin.css">
<script defer src="/assets/js/admin.js"></script>
</head>
<body class="admin">
<div dir="rtl" lang="fa" style="min-height:100vh;background:#F7F6F3;color:#3D4A6B;line-height:1.6;font-size:14px;">
<div style="display:flex;min-height:100vh;align-items:stretch;">

  <!-- Sidebar (desktop) -->
  <aside id="adminSide">
    <div style="display:flex;align-items:center;gap:9px;padding:4px 8px 16px;border-bottom:1px solid #2c3653;margin-bottom:10px;">
      <img src="/assets/images/Logo01-NEW.png" style="height:28px;filter:brightness(0) invert(1);flex:none;" alt="">
      <b class="a-side-label" style="font-size:16px;color:#fff;white-space:nowrap;">دوختک</b>
    </div>
    <?php foreach (admin_nav() as $item) { admin_nav_item($item, $active, $unread); } ?>
    <button type="button" class="a-side-item a-side-collapse" style="margin-top:auto;color:#8b95af;font-size:13px;">
      <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex:none;"><path d="m9 18 6-6-6-6"/></svg>
      <span class="a-side-label">جمع‌کردن منو</span>
    </button>
  </aside>
  <script>/* apply remembered collapse state before first paint */
  if (localStorage.getItem('dk_admin_side') === 'closed') document.getElementById('adminSide').classList.add('collapsed');
  </script>

  <!-- Main column -->
  <div style="flex:1;min-width:0;display:flex;flex-direction:column;">
    <div class="a-topbar">
      <div style="display:flex;align-items:center;gap:10px;">
        <button type="button" data-adm-mobile data-adm-drawer-open class="a-burger" aria-label="منو" style="align-items:center;justify-content:center;width:42px;height:42px;background:none;border:none;color:#1F2A44;border-radius:9px;cursor:pointer;">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="4" x2="20" y1="6" y2="6"/><line x1="4" x2="20" y1="12" y2="12"/><line x1="4" x2="20" y1="18" y2="18"/></svg>
        </button>
        <b style="font-size:16px;color:#1F2A44;"><?= e($title) ?></b>
      </div>
      <div style="display:flex;align-items:center;gap:8px;position:relative;">
        <a href="/" target="_blank" class="a-top-link">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 3h6v6"/><path d="M10 14 21 3"/><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/></svg>
          <span data-adm-desktop>مشاهده سایت</span>
        </a>
        <button type="button" class="a-user-btn" data-user-menu-btn>
          <span style="width:26px;height:26px;border-radius:50%;background:#FCEFEA;color:#D45A3D;display:inline-flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;"><?= e($initial) ?></span>
          <span data-adm-desktop><?= e($username) ?></span>
          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg>
        </button>
        <div data-user-menu class="a-user-menu" style="display:none;">
          <a class="a-menu-item" href="/admin/account.php">حساب کاربری</a>
          <form method="post" action="/admin/logout.php" style="margin:0;">
            <?= csrf_field() ?>
            <button type="submit" class="a-menu-item danger" style="width:100%;">خروج</button>
          </form>
        </div>
      </div>
    </div>

    <div style="flex:1;padding:20px;max-width:1160px;width:100%;">
<?php
}

/** Close the admin page (work area, drawer, html). */
function admin_page_end(string $active = ''): void
{
    $unread = admin_unread_count();
    ?>
    </div>
  </div>
</div>

<!-- Mobile sidebar drawer -->
<div id="admDrawerOverlay"></div>
<div id="admDrawer">
  <div style="display:flex;align-items:center;justify-content:space-between;padding:0 4px 14px;border-bottom:1px solid #2c3653;margin-bottom:10px;">
    <span style="display:flex;align-items:center;gap:8px;"><img src="/assets/images/Logo01-NEW.png" style="height:26px;filter:brightness(0) invert(1);" alt=""><b style="font-size:15px;color:#fff;">دوختک</b></span>
    <button type="button" data-adm-drawer-close class="a-drawer-close" aria-label="بستن" style="display:inline-flex;align-items:center;justify-content:center;width:42px;height:42px;background:none;border:none;color:#fff;border-radius:9px;cursor:pointer;">
      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
    </button>
  </div>
  <?php foreach (admin_nav() as $item) { admin_nav_item($item, $active, $unread, true); } ?>
</div>
</div>
</body>
</html>
<?php
}
