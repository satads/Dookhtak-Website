<?php
/**
 * THE site footer (single definition). Quick links come from the central
 * nav config; contact info, enabled socials and the Enamad code come
 * from the settings table.
 */
require_once __DIR__ . '/nav.php';
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/db.php';

$dk_links = array_merge(site_nav(), footer_extra());
$dk_phone = setting('support_phone', '۰۲۱-۱۲۳۴۵۶۷۸');
$dk_email = setting('support_email', 'hello@dookhtak.ir');
$dk_enamad = trim(setting('enamad_code', ''));

// Social rows: only enabled networks are printed, handle derived from URL.
$dk_socials = [];
foreach ([
    'instagram' => 'اینستاگرام',
    'telegram'  => 'تلگرام',
    'bale'      => 'بله',
] as $dk_key => $dk_label) {
    if (setting($dk_key . '_enabled', $dk_key === 'instagram' ? '1' : '0') === '1') {
        $dk_url = setting($dk_key . '_url', $dk_key === 'instagram' ? 'https://instagram.com/dookhtak' : '');
        $dk_handle = trim(basename(parse_url($dk_url, PHP_URL_PATH) ?? ''), '@/');
        if ($dk_handle !== '') {
            $dk_socials[] = [$dk_label, $dk_handle];
        }
    }
}

// Jalali year for the copyright line.
[$dk_jy] = gregorian_to_jalali((int) date('Y'), (int) date('n'), (int) date('j'));
?>
<footer dir="rtl" lang="fa" id="footer" style="scroll-margin-top:96px;background:var(--color-navy);color:#b7bed0;">
  <div style="max-width:1180px;margin-inline:auto;padding:54px 24px 26px;">
    <div style="display:flex;flex-wrap:wrap;gap:40px;justify-content:space-between;">
      <div style="flex:1 1 260px;max-width:320px;">
        <div style="display:flex;align-items:center;gap:10px;margin-bottom:14px;"><img src="/assets/images/Logo01-NEW.png" loading="lazy" style="height:36px;filter:brightness(0) invert(1);" alt=""><span style="font-size:22px;font-weight:700;color:#fff;">دوختک</span></div>
        <p style="font-size:14px;line-height:1.9;color:#8b95af;margin:0;">سامانه ابری مدیریت خیاطی و مزون؛ سفارش، مشتری، گالری و حساب، همه روی یک میز.</p>
      </div>
      <div style="flex:1 1 140px;">
        <div style="font-size:14px;font-weight:700;color:#fff;margin-bottom:14px;">دسترسی سریع</div>
        <div style="display:flex;flex-direction:column;gap:10px;font-size:14px;">
          <?php foreach ($dk_links as $l): ?>
          <a class="dk-foot-a" href="<?= e($l['href']) ?>"><?= e($l['label']) ?></a>
          <?php endforeach; ?>
        </div>
      </div>
      <div style="flex:1 1 200px;">
        <div style="font-size:14px;font-weight:700;color:#fff;margin-bottom:14px;">ارتباط با ما</div>
        <div style="display:flex;flex-direction:column;gap:10px;font-size:14px;">
          <span>پشتیبانی: <?= e($dk_phone) ?></span>
          <span>ایمیل: <?= e($dk_email) ?></span>
          <?php foreach ($dk_socials as [$dk_label, $dk_handle]): ?>
          <span><?= e($dk_label) ?>: <?= e($dk_handle) ?>@</span>
          <?php endforeach; ?>
        </div>
      </div>
      <div style="flex:0 0 auto;">
        <?php if ($dk_enamad !== ''): ?>
        <?= $dk_enamad /* trusted admin-provided Enamad embed code */ ?>
        <?php else: ?>
        <div style="width:96px;height:110px;border:1px dashed #46557d;border-radius:10px;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:6px;color:#6b7492;font-size:11px;text-align:center;">
          <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#6b7492" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10Z"/><path d="m9 12 2 2 4-4"/></svg>
          نماد اعتماد<br>[ENAMAD]
        </div>
        <?php endif; ?>
      </div>
    </div>
    <div style="margin-top:40px;padding-top:20px;border-top:1px solid #2c3653;display:flex;flex-wrap:wrap;gap:12px;justify-content:space-between;font-size:13px;color:#6b7492;">
      <span>© <?= fa_digits($dk_jy) ?> دوختک — همه حقوق محفوظ است.</span>
      <span>محصولی از آتین نگار مانا</span>
    </div>
  </div>
</footer>
