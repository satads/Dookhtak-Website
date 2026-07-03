<?php
/**
 * Site settings — grouped form over the settings table (contact /
 * socials with enable toggles dimming the URL field / Enamad / general)
 * + cache-flush button. CSRF on save; changes reflect immediately on
 * the public site (footer reads the same table).
 */
require_once __DIR__ . '/../includes/admin_layout.php';
require_once __DIR__ . '/../includes/cache.php';

require_admin();

$SETTING_KEYS = [
    'support_phone', 'support_email', 'working_hours',
    'instagram_url', 'instagram_enabled',
    'telegram_url', 'telegram_enabled',
    'bale_url', 'bale_enabled',
    'enamad_code', 'app_url', 'trial_days',
];

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    if (!csrf_verify($_POST['csrf_token'] ?? null)) {
        http_response_code(403);
        admin_page_start('تنظیمات سایت', 'settings', ['toast' => 'درخواست نامعتبر بود؛ دوباره تلاش کن.', 'toast_kind' => 'err']);
        echo '<div class="a-alert">درخواست نامعتبر (CSRF). صفحه را از نو باز کن.</div>';
        admin_page_end('settings');
        exit;
    }

    if (isset($_POST['clear_cache'])) {
        $n = cache_flush();
        header('Location: /admin/settings.php?toast=' . rawurlencode('کش پاک شد (' . fa_digits($n) . ' فایل)'));
        exit;
    }

    $stmt = db()->prepare('UPDATE settings SET `value` = ? WHERE `key` = ?');
    foreach ($SETTING_KEYS as $key) {
        if (!array_key_exists($key, $_POST)) {
            continue; // disabled URL inputs do not submit — keep stored value
        }
        $value = trim((string) $_POST[$key]);
        if (str_ends_with($key, '_enabled')) {
            $value = $value === '1' ? '1' : '0';
        }
        if ($key === 'trial_days') {
            $value = (string) max(0, (int) en_digits($value));
        }
        $stmt->execute([$value, $key]);
    }
    cache_flush(); // future-proof: settings affect every public page
    header('Location: /admin/settings.php?toast=' . rawurlencode('تنظیمات ذخیره شد'));
    exit;
}

// Current values for the form.
$val = fn (string $k, string $d = '') => setting($k, $d);

$socials = [
    ['instagram', 'اینستاگرام'],
    ['telegram', 'تلگرام'],
    ['bale', 'بله'],
];

admin_page_start('تنظیمات سایت', 'settings');
?>
<div style="max-width:680px;">
  <form method="post" action="/admin/settings.php">
    <?= csrf_field() ?>

    <div class="a-card a-card-pad" style="margin-bottom:12px;">
      <b class="a-card-title">اطلاعات تماس</b>
      <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:12px;">
        <div><label class="a-label" for="s-phone">تلفن پشتیبانی</label><input id="s-phone" class="a-input" type="text" dir="ltr" name="support_phone" value="<?= e($val('support_phone')) ?>"></div>
        <div><label class="a-label" for="s-email">ایمیل</label><input id="s-email" class="a-input" type="text" dir="ltr" name="support_email" value="<?= e($val('support_email')) ?>"></div>
        <div><label class="a-label" for="s-hours">ساعت پاسخ‌گویی</label><input id="s-hours" class="a-input" type="text" name="working_hours" value="<?= e($val('working_hours')) ?>"></div>
      </div>
    </div>

    <div class="a-card a-card-pad" style="margin-bottom:12px;">
      <b class="a-card-title">شبکه‌های اجتماعی</b>
      <?php foreach ($socials as [$key, $label]):
          $on = $val($key . '_enabled', '0') === '1';
      ?>
      <div style="display:flex;align-items:center;gap:12px;padding:8px 0;border-bottom:1px solid #F6F4EF;">
        <b style="flex:0 0 84px;font-size:13px;color:#1F2A44;"><?= e($label) ?></b>
        <input type="hidden" name="<?= e($key) ?>_enabled" value="<?= $on ? '1' : '0' ?>">
        <button type="button" class="a-switch <?= $on ? 'on' : '' ?>" role="switch" aria-checked="<?= $on ? 'true' : 'false' ?>" aria-label="<?= e($label) ?>" data-switch-input="<?= e($key) ?>_enabled" data-switch-dims="#s-<?= e($key) ?>-url">
          <span class="knob"></span>
        </button>
        <input id="s-<?= e($key) ?>-url" class="a-input a-dimmable <?= $on ? '' : 'dim' ?>" type="text" dir="ltr" placeholder="https://…" name="<?= e($key) ?>_url" value="<?= e($val($key . '_url')) ?>" <?= $on ? '' : 'disabled' ?> style="flex:1;font-size:12.5px;">
      </div>
      <?php endforeach; ?>
    </div>

    <div class="a-card a-card-pad" style="margin-bottom:12px;">
      <b class="a-card-title">اینماد</b>
      <textarea class="a-input" rows="3" dir="ltr" placeholder="کد اینماد…" name="enamad_code" style="min-height:0;resize:vertical;font-size:12px;"><?= e($val('enamad_code')) ?></textarea>
    </div>

    <div class="a-card a-card-pad" style="margin-bottom:12px;">
      <b class="a-card-title">عمومی</b>
      <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:12px;">
        <div><label class="a-label" for="s-app">آدرس اپ</label><input id="s-app" class="a-input" type="text" dir="ltr" name="app_url" value="<?= e($val('app_url')) ?>" style="font-size:12.5px;"></div>
        <div><label class="a-label" for="s-trial">روزهای تست رایگان</label><input id="s-trial" class="a-input" type="text" name="trial_days" value="<?= e(fa_digits($val('trial_days', '10'))) ?>" style="text-align:center;"></div>
      </div>
    </div>

    <button type="submit" class="a-btn a-btn-primary" style="min-height:46px;padding:11px 30px;font-size:14px;">ذخیره تنظیمات</button>
  </form>

  <div class="a-card a-card-pad" style="margin-top:12px;">
    <b class="a-card-title">کش صفحات</b>
    <p style="font-size:12.5px;color:#6B7280;margin:0 0 12px;">اگر تغییری در سایت دیده نمی‌شود، کش را پاک کن. (کش صفحات از فاز ۶ فعال می‌شود؛ این دکمه از همین حالا کار می‌کند.)</p>
    <form method="post" action="/admin/settings.php" style="margin:0;">
      <?= csrf_field() ?>
      <button type="submit" name="clear_cache" value="1" class="a-btn a-btn-outline">پاک‌کردن کش</button>
    </form>
  </div>
</div>
<?php admin_page_end('settings');
