<?php
/**
 * Tutorials center (client-side SPA). The static shell renders here;
 * assets/js/page-tutorials.js (ES module) renders the sidebar, welcome
 * panel and tutorial view from assets/js/tutorials-data.js and handles
 * hash routing, live search, fragment loading and the mobile drawer.
 * Phase 1 keeps the data static on the client; Phase 4 rewires it to
 * the API. Markup is the approved design, converted 1:1.
 */
require_once __DIR__ . '/includes/layout.php';
public_boot(); // redirect middleware + page cache

render_head([
    'page_key' => 'tutorials',
    'css' => '/assets/css/page-tutorials.css',
    'js' => '/assets/js/page-tutorials.js',
    'js_module' => true,
]);
?>
<div dir="rtl" lang="fa" style="position:relative;min-height:100vh;background-color:#FAF8F4;background-image:linear-gradient(#EDEAE3 1px,transparent 1px),linear-gradient(90deg,#EDEAE3 1px,transparent 1px);background-size:46px 46px;overflow-x:hidden;line-height:1.6;">

<?php include __DIR__ . '/includes/site_header.php'; ?>

  <!-- عنوان صفحه -->
  <header data-screen-label="سر صفحه آموزش" style="max-width:1240px;margin-inline:auto;padding:118px 24px 18px;">
    <h1 id="pageTitle" style="font-size:32px;line-height:1.5;font-weight:700;color:#1F2A44;margin:0;letter-spacing:-.4px;">
      <span style="position:relative;white-space:nowrap;">آموزش<svg width="100%" height="12" viewBox="0 0 140 14" preserveAspectRatio="none" style="position:absolute;inset-inline-start:0;bottom:-5px;overflow:visible;"><path d="M3 8 Q40 2 72 7 T137 6" stroke="#E76F51" stroke-width="6" fill="none" stroke-linecap="round" opacity=".85"/></svg></span>
      کار با دوختک
    </h1>
    <p style="font-size:15px;color:#6B7280;margin:14px 0 0;">قدم‌به‌قدم، با فیلم و تصویر — از نصب روی گوشی تا حرفه‌ای‌شدن</p>
  </header>

  <!-- نوار موبایل: عنوان فعلی + دکمه فهرست -->
  <div id="mobileBar" style="display:none;position:sticky;top:72px;z-index:45;padding:8px 14px;">
    <div style="display:flex;align-items:center;justify-content:space-between;gap:10px;max-width:1240px;margin-inline:auto;background:rgba(255,255,255,.92);backdrop-filter:blur(8px);-webkit-backdrop-filter:blur(8px);border:1px solid var(--color-border);border-radius:14px;padding:8px 8px 8px 14px;box-shadow:0 6px 20px -14px rgba(31,42,68,.25);">
      <b id="mobileBarTitle" style="font-size:13.5px;color:#1F2A44;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">آموزش کار با دوختک</b>
      <button class="hv480c7e" data-list-open style="flex:none;display:inline-flex;align-items:center;gap:7px;min-height:44px;background:#FCEFEA;color:#D45A3D;border:none;font-weight:700;font-size:13.5px;padding:9px 15px;border-radius:11px;">
        <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="8" x2="21" y1="6" y2="6"/><line x1="8" x2="21" y1="12" y2="12"/><line x1="8" x2="21" y1="18" y2="18"/><line x1="3" x2="3.01" y1="6" y2="6"/><line x1="3" x2="3.01" y1="12" y2="12"/><line x1="3" x2="3.01" y1="18" y2="18"/></svg>
        فهرست آموزش‌ها
      </button>
    </div>
  </div>

  <!-- بدنه دو ستونه -->
  <div id="layoutRow" style="max-width:1240px;margin-inline:auto;padding:6px 24px 60px;display:flex;gap:28px;align-items:flex-start;">

    <!-- سایدبار دسکتاپ (راست) — فهرست دسته‌ها را page-tutorials.js می‌سازد -->
    <aside id="sideDesk" data-screen-label="سایدبار آموزش" style="flex:0 0 310px;position:sticky;top:96px;max-height:calc(100vh - 120px);overflow-y:auto;background:#fff;border:1px solid var(--color-border);border-radius:16px;padding:16px;box-shadow:var(--shadow-card);scrollbar-width:thin;">
      <div style="position:relative;margin-bottom:12px;">
        <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="#9a9587" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="position:absolute;top:50%;inset-inline-start:13px;transform:translateY(-50%);pointer-events:none;"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
        <input type="search" class="tut-search" data-tut-search aria-label="جستجوی آموزش" placeholder="جستجو در آموزش‌ها…" style="width:100%;min-height:44px;padding:10px 38px 10px 12px;font-family:inherit;font-size:14px;color:#1F2A44;background:#FAF8F4;border:1px solid var(--color-border);border-radius:11px;outline:none;transition:border-color .2s ease,box-shadow .2s ease;">
      </div>
      <div id="sideList"></div>
    </aside>

    <!-- ناحیه محتوا (چپ) — پنل خوش‌آمد/آموزش را page-tutorials.js می‌سازد -->
    <main id="contentArea" style="flex:1 1 0;min-width:0;max-width:760px;"></main>
  </div>

  <!-- درایور فهرست موبایل (راست) -->
  <div id="listOverlay" style="position:fixed;inset:0;z-index:80;background:rgba(31,42,68,.45);opacity:0;pointer-events:none;transition:opacity .3s ease;"></div>
  <div id="listDrawer" data-screen-label="درایور فهرست" style="position:fixed;top:0;bottom:0;inset-inline-start:0;width:320px;max-width:88vw;background:#fff;z-index:81;transform:translateX(110%);transition:transform .3s ease;box-shadow:0 0 44px rgba(31,42,68,.22);display:flex;flex-direction:column;padding:16px;overflow-y:auto;">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px;">
      <b style="font-size:16px;color:#1F2A44;">فهرست آموزش‌ها</b>
      <button class="hv006c9d" data-list-close aria-label="بستن" style="display:inline-flex;align-items:center;justify-content:center;width:44px;height:44px;background:none;border:none;color:#1F2A44;border-radius:50%;">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
      </button>
    </div>
    <div style="position:relative;margin-bottom:12px;">
      <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="#9a9587" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="position:absolute;top:50%;inset-inline-start:13px;transform:translateY(-50%);pointer-events:none;"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
      <input type="search" class="tut-search" data-tut-search aria-label="جستجوی آموزش" placeholder="جستجو در آموزش‌ها…" style="width:100%;min-height:46px;padding:10px 38px 10px 12px;font-family:inherit;font-size:15px;color:#1F2A44;background:#FAF8F4;border:1px solid var(--color-border);border-radius:11px;outline:none;">
    </div>
    <div id="drawerList"></div>
  </div>

  <div id="footer" style="scroll-margin-top:96px;"><?php include __DIR__ . '/includes/site_footer.php'; ?></div>

</div>
<?php render_foot();
