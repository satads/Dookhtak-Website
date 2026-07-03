<?php
/**
 * THE site header (single definition) — floating pill navbar + mobile
 * drawer, built from the central nav config with server-side active state.
 * Behavior (drawer, scroll shadow) lives in assets/js/site.js.
 */
require_once __DIR__ . '/nav.php';
require_once __DIR__ . '/helpers.php';

$dk_menu = site_nav();
$dk_current = nav_current();
$dk_count = count($dk_menu);
?>
<!-- FLOATING PILL NAVBAR (central component — the only site header definition) -->
<div style="position:fixed;top:16px;inset-inline:0;z-index:60;display:flex;justify-content:center;padding-inline:14px;pointer-events:none;">
  <nav id="navPill" style="pointer-events:auto;display:flex;align-items:center;justify-content:space-between;gap:14px;width:100%;max-width:1000px;background:rgba(255,255,255,.78);backdrop-filter:blur(9px);-webkit-backdrop-filter:blur(9px);border:1px solid var(--color-border);border-radius:9999px;padding:8px 12px;box-shadow:0 4px 18px -8px rgba(31,42,68,.14);transition:box-shadow .3s ease;">
    <a href="/" style="display:flex;align-items:center;gap:8px;flex:none;">
      <img src="/assets/images/Logo01-NEW.png" alt="دوختک" style="height:32px;width:auto;display:block;">
      <span style="font-weight:700;font-size:19px;color:var(--color-navy);">دوختک</span>
    </a>
    <div class="dk-nav-menu">
      <?php foreach ($dk_menu as $m): $act = $m['href'] === $dk_current; ?>
      <a class="dk-nav-a" href="<?= e($m['href']) ?>" style="padding:9px 8px;font-weight:<?= $act ? '700' : '400' ?>;color:<?= $act ? 'var(--color-navy)' : 'var(--color-body)' ?>;background-image:repeating-linear-gradient(90deg,var(--color-primary) 0 6px,transparent 6px 11px);background-repeat:no-repeat;background-size:<?= $act ? '100%' : '0%' ?> 2px;background-position:100% calc(100% - 4px);transition:background-size .25s ease,color .2s ease;"><?= e($m['label']) ?></a>
      <?php endforeach; ?>
    </div>
    <div style="display:flex;align-items:center;gap:8px;flex:none;">
      <a class="dk-cta" href="#cta" style="display:inline-flex;align-items:center;min-height:44px;background:var(--color-primary);color:#fff;font-weight:700;font-size:14.5px;padding:9px 18px;border-radius:999px;box-shadow:0 4px 14px rgba(231,111,81,.28);transition:background .2s ease,transform .2s ease;">شروع رایگان</a>
      <button data-drawer-open class="dk-ham dk-iconbtn" aria-label="منو" style="align-items:center;justify-content:center;width:44px;height:44px;background:none;border:none;color:var(--color-navy);border-radius:50%;">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="4" x2="20" y1="6" y2="6"/><line x1="4" x2="20" y1="12" y2="12"/><line x1="4" x2="20" y1="18" y2="18"/></svg>
      </button>
    </div>
  </nav>
</div>

<!-- MOBILE DRAWER -->
<div id="dkDrawerOverlay" style="position:fixed;inset:0;z-index:80;background:rgba(31,42,68,.45);opacity:0;pointer-events:none;transition:opacity .3s ease;"></div>
<div id="dkDrawer" style="position:fixed;top:0;bottom:0;inset-inline-start:0;width:290px;max-width:85vw;background:#fff;z-index:81;transform:translateX(110%);transition:transform .3s ease;box-shadow:0 0 44px rgba(31,42,68,.22);display:flex;flex-direction:column;padding:18px;">
  <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:18px;">
    <span style="display:flex;align-items:center;gap:8px;"><img src="/assets/images/Logo01-NEW.png" style="height:30px;" alt=""><b style="font-size:18px;color:var(--color-navy);">دوختک</b></span>
    <button data-drawer-close class="dk-iconbtn" aria-label="بستن" style="display:inline-flex;align-items:center;justify-content:center;width:44px;height:44px;background:none;border:none;color:var(--color-navy);border-radius:50%;">
      <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
    </button>
  </div>
  <div style="display:flex;flex-direction:column;">
    <?php foreach ($dk_menu as $i => $m):
        $act = $m['href'] === $dk_current;
        $last = $i === $dk_count - 1;
    ?>
    <a data-drawer-close href="<?= e($m['href']) ?>" style="display:flex;align-items:center;min-height:50px;padding:0 12px;font-size:16.5px;font-weight:700;color:<?= $act ? 'var(--color-primary-hover)' : 'var(--color-navy)' ?>;background:<?= $act ? 'var(--color-primary-soft)' : 'transparent' ?>;border-radius:12px;border-bottom:<?= ($act || $last) ? 'none' : '2px dashed #F0EBE1' ?>;margin-bottom:<?= $act ? '4px' : '0' ?>;"><?= e($m['label']) ?></a>
    <?php endforeach; ?>
  </div>
  <a data-drawer-close href="#cta" class="dk-drawer-cta" style="margin-top:auto;display:flex;align-items:center;justify-content:center;min-height:50px;background:var(--color-primary);color:#fff;font-weight:700;font-size:16px;border-radius:14px;">شروع رایگان</a>
</div>
