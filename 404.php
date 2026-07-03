<?php
/** Styled Persian 404 page. */
require_once __DIR__ . '/includes/layout.php';

http_response_code(404);
render_head([
    'title' => 'صفحه پیدا نشد | دوختک',
    'description' => 'صفحه‌ای که دنبالش بودید پیدا نشد.',
]);
?>
<div dir="rtl" lang="fa" style="position:relative;min-height:100vh;background-color:#FAF8F4;background-image:linear-gradient(#EDEAE3 1px,transparent 1px),linear-gradient(90deg,#EDEAE3 1px,transparent 1px);background-size:46px 46px;overflow-x:hidden;line-height:1.6;">
<?php include __DIR__ . '/includes/site_header.php'; ?>

  <section style="max-width:760px;margin-inline:auto;padding:170px 24px 90px;text-align:center;">
    <div style="font-size:72px;font-weight:700;color:var(--color-primary);letter-spacing:2px;">۴۰۴</div>
    <div style="max-width:230px;margin:10px auto 26px;height:2px;background-image:repeating-linear-gradient(90deg,#E76F51 0 11px,transparent 11px 20px);opacity:.6;"></div>
    <h1 style="font-size:28px;font-weight:700;color:var(--color-navy);margin:0 0 12px;">این صفحه پیدا نشد</h1>
    <p style="font-size:16px;color:var(--color-body);margin:0 0 30px;">صفحه‌ای که دنبالش بودید وجود ندارد یا جابه‌جا شده است.</p>
    <a class="hv6ef100" href="/" style="display:inline-flex;align-items:center;justify-content:center;min-height:52px;background:var(--color-primary);color:#fff;font-weight:700;font-size:16px;padding:13px 30px;border-radius:13px;box-shadow:0 8px 22px rgba(231,111,81,.3);transition:background .2s ease,transform .2s ease,box-shadow .2s ease;">بازگشت به صفحه اصلی</a>
  </section>

<?php include __DIR__ . '/includes/site_footer.php'; ?>
</div>
<?php render_foot();
