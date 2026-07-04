<?php
/**
 * Pricing admin — grouped form over the pricing_values table (setup
 * fee / base-plan subscriptions + discount percents / VIP / modules).
 * Inputs show Persian thousand-separated numbers; inline JS keeps the
 * grouping live while typing. CSRF on save; cache flushed so the
 * public /pricing page reflects changes immediately.
 */
require_once __DIR__ . '/../includes/admin_layout.php';
require_once __DIR__ . '/../includes/cache.php';

require_admin();

// Whitelisted editable keys; percents are capped 0-99 on save.
$PRICING_KEYS = [
    'setup_fee',
    'sub_1m', 'sub_3m', 'sub_6m', 'sub_12m',
    'discount_3m_percent', 'discount_6m_percent', 'discount_12m_percent',
    'vip_price',
    'module_gallery', 'module_sms', 'module_gateway', 'module_domain', 'module_dedicated_line',
];
$PERCENT_KEYS = ['discount_3m_percent', 'discount_6m_percent', 'discount_12m_percent'];

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    if (!csrf_verify($_POST['csrf_token'] ?? null)) {
        http_response_code(403);
        admin_page_start('تعرفه‌ها', 'pricing', ['toast' => 'درخواست نامعتبر بود؛ دوباره تلاش کن.', 'toast_kind' => 'err']);
        echo '<div class="a-alert">درخواست نامعتبر (CSRF). صفحه را از نو باز کن.</div>';
        admin_page_end('pricing');
        exit;
    }

    $stmt = db()->prepare('UPDATE pricing_values SET `value` = ? WHERE `key` = ?');
    foreach ($PRICING_KEYS as $key) {
        if (!array_key_exists($key, $_POST)) {
            continue;
        }
        // Normalize: Persian digits -> latin, drop separators -> non-negative int.
        $raw = preg_replace('/[^0-9]/', '', en_digits((string) $_POST[$key]));
        $n = max(0, (int) $raw);
        if (in_array($key, $PERCENT_KEYS, true)) {
            $n = min(99, $n);
        }
        $stmt->execute([(string) $n, $key]);
    }
    cache_flush(); // pricing shows on the public /pricing page
    header('Location: /admin/pricing.php?toast=' . rawurlencode('تعرفه‌ها ذخیره شد'));
    exit;
}

// Current values + labels for the form.
$rows = [];
foreach (db()->query('SELECT `key`, `value`, `label` FROM pricing_values') as $r) {
    $rows[$r['key']] = $r;
}

// Groups: [card title, fields]; field = [key, label override (null = DB label), type].
$groups = [
    ['هزینه راه‌اندازی', [
        ['setup_fee', null, 'toman'],
    ]],
    ['پلن پایه (اشتراک دوره‌ای)', [
        ['sub_1m',  'اشتراک یک‌ماهه', 'toman'],
        ['sub_3m',  'اشتراک سه‌ماهه', 'toman'],
        ['sub_6m',  'اشتراک شش‌ماهه', 'toman'],
        ['sub_12m', 'اشتراک یک‌ساله', 'toman'],
        ['discount_3m_percent',  'صرفه‌جویی سه‌ماهه', 'percent'],
        ['discount_6m_percent',  'صرفه‌جویی شش‌ماهه', 'percent'],
        ['discount_12m_percent', 'صرفه‌جویی یک‌ساله', 'percent'],
    ]],
    ['پلن ویژه', [
        ['vip_price', null, 'toman'],
    ]],
    ['ماژول‌ها', [
        ['module_gallery', null, 'toman'],
        ['module_sms', null, 'toman'],
        ['module_gateway', null, 'toman'],
        ['module_domain', null, 'toman'],
        ['module_dedicated_line', null, 'toman'],
    ]],
];

admin_page_start('تعرفه‌ها', 'pricing');
?>
<div style="max-width:680px;">
  <div style="display:flex;align-items:center;gap:9px;background:#FDF6E7;border:1px solid #F3E2B8;border-radius:10px;padding:11px 14px;font-size:12.5px;color:#8a6d1f;margin-bottom:14px;">
    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#EEA62B" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex:none;"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3"/><path d="M12 9v4"/><path d="M12 17h.01"/></svg>
    تغییرات بعد از ذخیره، بلافاصله روی سایت نمایش داده می‌شود.
  </div>

  <form method="post" action="/admin/pricing.php">
    <?= csrf_field() ?>

    <?php foreach ($groups as [$title, $fields]): ?>
    <div class="a-card a-card-pad" style="margin-bottom:12px;">
      <b class="a-card-title"><?= e($title) ?></b>
      <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(190px,1fr));gap:12px;">
        <?php foreach ($fields as [$key, $label, $type]):
            $row = $rows[$key] ?? ['value' => '0', 'label' => $key];
            $val = (int) $row['value'];
            $display = $type === 'percent' ? fa_digits($val) : fa_number_format($val);
        ?>
        <div>
          <label class="a-label" for="p-<?= e($key) ?>"><?= e($label ?? $row['label']) ?></label>
          <div style="display:flex;align-items:center;gap:8px;">
            <input id="p-<?= e($key) ?>" class="a-input" type="text" inputmode="numeric" name="<?= e($key) ?>" value="<?= e($display) ?>" data-price-fmt="<?= e($type) ?>" style="flex:1;text-align:end;font-weight:700;min-height:40px;background:#F7F6F3;">
            <span style="font-size:12px;color:#9a9587;"><?= $type === 'percent' ? '٪' : 'تومان' ?></span>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
    <?php endforeach; ?>

    <div style="position:sticky;bottom:0;background:rgba(247,246,243,.94);backdrop-filter:blur(6px);padding:12px 0;">
      <button type="submit" class="a-btn a-btn-primary" style="min-height:46px;padding:11px 30px;font-size:14px;">ذخیره تعرفه‌ها</button>
    </div>
  </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
  'use strict';
  // Live Persian formatting: digits only, thousand-grouped with U+066C
  // for toman fields; plain Persian digits for percent fields.
  var FA = '۰۱۲۳۴۵۶۷۸۹';
  function normalize(s) {
    return s
      .replace(/[۰-۹]/g, function (d) { return String.fromCharCode(d.charCodeAt(0) - 1728); })
      .replace(/[٠-٩]/g, function (d) { return String.fromCharCode(d.charCodeAt(0) - 1584); })
      .replace(/[^0-9]/g, '');
  }
  function toFa(s) {
    return s.replace(/[0-9]/g, function (d) { return FA[+d]; });
  }
  document.querySelectorAll('[data-price-fmt]').forEach(function (inp) {
    inp.addEventListener('input', function () {
      var en = normalize(inp.value);
      if (inp.getAttribute('data-price-fmt') === 'toman') {
        en = en.replace(/^0+(?=\d)/, '').replace(/\B(?=(\d{3})+(?!\d))/g, '٬');
      }
      inp.value = toFa(en);
    });
  });
});
</script>
<?php admin_page_end('pricing');
