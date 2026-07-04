<?php
/**
 * Pricing page. Prices load from the pricing_values table (edited in
 * admin » تعرفه‌ها); the seed values below are only a fallback if the
 * DB read fails. Markup is the approved design, converted 1:1.
 */
require_once __DIR__ . '/includes/layout.php';
public_boot(); // redirect middleware + page cache

$app_url = setting('app_url', 'https://app.dookhtak.ir');

// Prices in toman — loaded from pricing_values (seed values as fallback).
$pv = [
    'setup_fee' => 490000, 'vip_price' => 9900000,
    'sub_1m' => 290000, 'sub_3m' => 790000, 'sub_6m' => 1490000, 'sub_12m' => 2690000,
    'discount_3m_percent' => 9, 'discount_6m_percent' => 14, 'discount_12m_percent' => 23,
    'module_gallery' => 990000, 'module_sms' => 790000, 'module_gateway' => 690000,
    'module_domain' => 590000, 'module_dedicated_line' => 890000,
];
try {
    foreach (db()->query('SELECT `key`, `value` FROM pricing_values') as $row) {
        if (array_key_exists($row['key'], $pv)) {
            $pv[$row['key']] = (int) $row['value'];
        }
    }
} catch (Throwable $e) {
    error_log('pricing_values read failed: ' . $e->getMessage());
}
$prices = [
    'setup' => $pv['setup_fee'],
    'vip'   => $pv['vip_price'],
    'sub'   => [
        '1'  => ['months' => 1,  'total' => $pv['sub_1m'],  'discount' => 0],
        '3'  => ['months' => 3,  'total' => $pv['sub_3m'],  'discount' => $pv['discount_3m_percent']],
        '6'  => ['months' => 6,  'total' => $pv['sub_6m'],  'discount' => $pv['discount_6m_percent']],
        '12' => ['months' => 12, 'total' => $pv['sub_12m'], 'discount' => $pv['discount_12m_percent']],
    ],
];
$period_order = ['1', '3', '6', '12'];
$default_period = '12';
$units = ['1' => 'یک‌ماهه', '3' => 'سه‌ماهه', '6' => 'شش‌ماهه', '12' => 'یک‌ساله'];

// Pre-formatted per-period display strings; the same data feeds page JS.
$periods = [];
foreach ($period_order as $p) {
    $sub = $prices['sub'][$p];
    $periods[$p] = [
        'label'   => fa_digits($p) . ' ماهه',
        'total'   => fa_number_format($sub['total']),
        'unit'    => $units[$p],
        'monthly' => $sub['months'] === 1
            ? 'پرداخت ماه‌به‌ماه'
            : 'ماهی ' . fa_number_format(intdiv($sub['total'], $sub['months'] * 1000) * 1000) . ' تومان',
        'badge'   => $sub['discount'] > 0 ? fa_digits($sub['discount']) . '٪ صرفه‌جویی' : ' ',
    ];
}
$cur = $periods[$default_period];
$needle_start = (array_search($default_period, $period_order, true) * 25) . '%';

// Add-on modules — one-time purchases (icon paths from lucide).
$modules = [
    ['title' => 'گالری نمونه‌کار', 'desc' => 'ویترین آنلاین عکس و فیلم کارهایت.', 'price' => $pv['module_gallery'], 'note' => '',
     'iconPath' => 'M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z M12 17a4 4 0 1 0 0-8 4 4 0 0 0 0 8z'],
    ['title' => 'سامانه پیامکی', 'desc' => 'پیامک خودکار، انبوه و تبریک تولد.', 'price' => $pv['module_sms'], 'note' => 'هزینه شارژ پیامک جداگانه محاسبه می‌شود',
     'iconPath' => 'M7.9 20A9 9 0 1 0 4 16.1L2 22Z'],
    ['title' => 'درگاه پرداخت', 'desc' => 'لینک پرداخت آنلاین برای مشتری‌هایت.', 'price' => $pv['module_gateway'], 'note' => '',
     'iconPath' => 'M22 7H2v10a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2Z M2 7V7a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2 M2 10h20'],
    ['title' => 'دامنه اختصاصی', 'desc' => 'دوختک روی آدرس اینترنتی خودت.', 'price' => $pv['module_domain'], 'note' => '',
     'iconPath' => 'M12 22a10 10 0 1 0 0-20 10 10 0 0 0 0 20z M12 2a14.5 14.5 0 0 0 0 20 14.5 14.5 0 0 0 0-20 M2 12h20'],
    ['title' => 'خط اختصاصی پیامکی', 'desc' => 'پیامک‌ها با شماره اختصاصی خودت ارسال شود.', 'price' => $pv['module_dedicated_line'], 'note' => 'نیازمند ماژول سامانه پیامکی',
     'iconPath' => 'M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.13.96.36 1.9.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.91.34 1.85.57 2.81.7A2 2 0 0 1 22 16.92z'],
];

// Comparison table. Cell spec: 'check' → coral check icon,
// 'buy' → 'خرید جداگانه', 'text:X' → X.
$compare_rows = [
    ['label' => 'هزینه راه‌اندازی', 'base' => 'text:' . fa_number_format($prices['setup']) . ' تومان (یک‌بار)', 'vip' => 'text:در قیمت پلن ویژه لحاظ شده'],
    ['label' => 'اشتراک دوره‌ای', 'base' => 'text:دارد', 'vip' => 'text:ندارد — یک‌بار پرداخت'],
    ['label' => 'امکانات پایه (سفارش، مشتری، حسابداری)', 'base' => 'check', 'vip' => 'check'],
    ['label' => 'ماژول گالری نمونه‌کار', 'base' => 'buy', 'vip' => 'check'],
    ['label' => 'ماژول سامانه پیامکی', 'base' => 'buy', 'vip' => 'check'],
    ['label' => 'ماژول درگاه پرداخت', 'base' => 'buy', 'vip' => 'check'],
    ['label' => 'ماژول دامنه اختصاصی', 'base' => 'buy', 'vip' => 'check'],
    ['label' => 'ماژول خط اختصاصی پیامکی', 'base' => 'buy', 'vip' => 'check'],
    ['label' => 'قابلیت‌های آینده', 'base' => 'text:طبق پلن و ماژول‌ها', 'vip' => 'check'],
];
$cell_spec = function (string $spec): array {
    if ($spec === 'check') {
        return ['check' => true, 'text' => ''];
    }
    if ($spec === 'buy') {
        return ['check' => false, 'text' => 'خرید جداگانه'];
    }
    return ['check' => false, 'text' => substr($spec, 5)];
};

// FAQ (pricing) — all items closed initially.
$faqs = [];
foreach (db()->query("SELECT question, answer_html FROM faqs WHERE page = 'pricing' ORDER BY sort_order, id") as $faq_row) {
    $faqs[] = ['q' => $faq_row['question'], 'a' => $faq_row['answer_html']];
}

render_head([
    'page_key' => 'pricing',
    'css' => '/assets/css/page-pricing.css',
    'js' => '/assets/js/page-pricing.js',
]);
?>
<div dir="rtl" lang="fa" style="position:relative;min-height:100vh;background-color:#FAF8F4;background-image:linear-gradient(#EDEAE3 1px,transparent 1px),linear-gradient(90deg,#EDEAE3 1px,transparent 1px);background-size:46px 46px;overflow-x:hidden;line-height:1.6;">

<?php include __DIR__ . '/includes/site_header.php'; ?>

  <!-- COMPACT HERO -->
  <section data-screen-label="هیرو تعرفه" style="position:relative;">
    <div id="heroWrap" style="max-width:820px;margin-inline:auto;padding:130px 24px 34px;text-align:center;">
      <span data-hero style="opacity:0;transform:translateY(12px);transition:opacity .5s ease,transform .5s ease;display:inline-flex;align-items:center;gap:8px;background:#FCEFEA;color:#D45A3D;font-size:13.5px;font-weight:700;padding:7px 15px;border-radius:999px;">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
        ۱۰ روز تست رایگان — بدون نیاز به کارت بانکی
      </span>
      <h1 id="heroTitle" data-hero style="opacity:0;transform:translateY(12px);transition:opacity .5s ease,transform .5s ease;font-size:46px;line-height:1.45;font-weight:700;color:#1F2A44;margin:20px 0 0;letter-spacing:-.5px;">
        قیمت
        <span style="position:relative;white-space:nowrap;">روشن<svg width="100%" height="14" viewBox="0 0 140 14" preserveAspectRatio="none" style="position:absolute;inset-inline-start:0;bottom:-6px;overflow:visible;"><path d="M3 8 Q40 2 72 7 T137 6" stroke="#E76F51" stroke-width="6" fill="none" stroke-linecap="round" opacity=".85"/></svg></span>،
        بدون سورپرایز
      </h1>
      <p data-hero style="opacity:0;transform:translateY(12px);transition:opacity .5s ease,transform .5s ease;font-size:17.5px;line-height:1.9;color:#3D4A6B;margin:26px auto 0;max-width:540px;">دو راه ساده برای شروع: پلن پایه با اشتراک دوره‌ای، یا پلن ویژه با یک‌بار پرداخت برای همیشه.</p>
    </div>
  </section>

  <!-- TAPE MEASURE PERIOD SELECTOR -->
  <section data-screen-label="انتخابگر دوره" style="position:relative;z-index:2;">
    <div style="max-width:620px;margin-inline:auto;padding:0 24px 8px;">
      <div data-hero style="opacity:0;transform:translateY(12px);transition:opacity .5s ease,transform .5s ease;">
        <div style="text-align:center;font-size:13.5px;font-weight:700;color:#6B7280;margin-bottom:12px;">دوره اشتراک پلن پایه را انتخاب کن</div>
        <div style="position:relative;">
          <!-- needle indicator -->
          <div id="periodNeedle" style="position:absolute;top:-9px;inset-inline-start:<?= e($needle_start) ?>;width:25%;display:flex;justify-content:center;transition:inset-inline-start .35s cubic-bezier(.4,0,.2,1);pointer-events:none;z-index:3;">
            <svg width="16" height="12" viewBox="0 0 16 12"><path d="M8 12 0 0h16Z" fill="#E76F51"/></svg>
          </div>
          <!-- tape -->
          <div style="position:relative;display:flex;background:#fff;border:1px solid var(--color-border);border-radius:12px;overflow:hidden;box-shadow:0 8px 24px -18px rgba(31,42,68,.35);background-image:repeating-linear-gradient(90deg,rgba(31,42,68,.09) 0 1px,transparent 1px 12px);background-position:0 0;background-size:100% 8px;background-repeat:no-repeat;">
            <!-- sliding fill -->
            <div id="periodFill" style="position:absolute;top:0;bottom:0;inset-inline-start:<?= e($needle_start) ?>;width:25%;background:var(--color-primary);transition:inset-inline-start .35s cubic-bezier(.4,0,.2,1);"></div>
            <?php foreach ($period_order as $p): $on = $p === $default_period; ?>
              <button data-period="<?= e($p) ?>" style="position:relative;flex:1;min-height:56px;background:none;border:none;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:2px;padding:8px 4px;">
                <span data-period-label style="font-size:14.5px;font-weight:700;color:<?= $on ? '#fff' : '#3D4A6B' ?>;transition:color .3s ease;white-space:nowrap;"><?= e($periods[$p]['label']) ?></span>
                <span data-period-badge style="font-size:10.5px;font-weight:700;color:<?= $on ? 'rgba(255,255,255,.85)' : '#D45A3D' ?>;transition:color .3s ease;white-space:nowrap;"><?= e($periods[$p]['badge']) ?></span>
              </button>
            <?php endforeach; ?>
          </div>
          <!-- ruler ticks under tape -->
          <div style="height:7px;margin-top:3px;background-image:repeating-linear-gradient(90deg,#D8D2C4 0 1px,transparent 1px 11px);opacity:.8;"></div>
        </div>
      </div>
    </div>
  </section>

  <!-- PLAN CARDS -->
  <section data-screen-label="کارت‌های پلن">
    <div style="max-width:1020px;margin-inline:auto;padding:30px 24px 20px;">
      <div id="planRow" style="display:flex;gap:24px;align-items:stretch;flex-wrap:wrap;">

        <!-- BASE PLAN -->
        <div data-plan="base" data-rv style="opacity:0;transform:translateY(18px);transition:opacity .5s ease,transform .5s ease;flex:1 1 340px;display:flex;">
          <div style="flex:1;display:flex;flex-direction:column;background:#fff;border:1px solid var(--color-border);border-radius:20px;padding:30px 28px;box-shadow:var(--shadow-card);">
            <h2 style="font-size:24px;font-weight:700;color:#1F2A44;margin:0 0 4px;">پلن پایه</h2>
            <div style="font-size:14px;color:#6B7280;margin-bottom:22px;">شروع سبک، رشد قدم‌به‌قدم</div>
            <div id="basePriceWrap" style="opacity:1;transition:opacity .18s ease;">
              <div style="display:flex;align-items:baseline;gap:8px;">
                <!-- placeholder: قیمت اشتراک دوره انتخابی -->
                <span id="basePrice" style="font-size:40px;font-weight:700;color:#1F2A44;letter-spacing:-.5px;"><?= e($cur['total']) ?></span>
                <span id="baseUnit" style="font-size:15px;color:#6B7280;">تومان / <?= e($cur['unit']) ?></span>
              </div>
              <div id="baseMonthly" style="font-size:13px;color:#6B7280;margin-top:4px;"><?= e($cur['monthly']) ?></div>
            </div>
            <div style="height:2px;background-image:repeating-linear-gradient(90deg,var(--color-primary) 0 9px,transparent 9px 16px);opacity:.5;margin:18px 0 14px;"></div>
            <!-- placeholder: قیمت راه‌اندازی -->
            <div style="display:flex;align-items:flex-start;gap:9px;background:#FAF8F4;border:1px solid #EDEAE3;border-radius:11px;padding:11px 13px;font-size:13.5px;color:#3D4A6B;margin-bottom:20px;">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#E76F51" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex:none;margin-top:3px;"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4"/><path d="M12 8h.01"/></svg>
              <span>+ هزینه راه‌اندازی اولیه: <b style="color:#1F2A44;"><?= e(fa_number_format($prices['setup'])) ?> تومان</b> (فقط یک‌بار)</span>
            </div>
            <div style="display:flex;flex-direction:column;gap:10px;font-size:14.5px;color:#3D4A6B;">
              <span style="display:flex;align-items:center;gap:10px;"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#E76F51" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" style="flex:none;"><path d="M20 6 9 17l-5-5"/></svg>مدیریت سفارش‌ها و سررسید تحویل</span>
              <span style="display:flex;align-items:center;gap:10px;"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#E76F51" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" style="flex:none;"><path d="M20 6 9 17l-5-5"/></svg>پرونده مشتری و اندازه‌های اندام</span>
              <span style="display:flex;align-items:center;gap:10px;"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#E76F51" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" style="flex:none;"><path d="M20 6 9 17l-5-5"/></svg>حسابداری و یادآوری چک‌ها</span>
              <span style="display:flex;align-items:center;gap:10px;"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#E76F51" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" style="flex:none;"><path d="M20 6 9 17l-5-5"/></svg>ارجاع سفارش به همکار</span>
              <span style="display:flex;align-items:center;gap:10px;"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#E76F51" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" style="flex:none;"><path d="M20 6 9 17l-5-5"/></svg>دسترسی از موبایل، تبلت و کامپیوتر</span>
              <span style="display:flex;align-items:center;gap:10px;"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#E76F51" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" style="flex:none;"><path d="M20 6 9 17l-5-5"/></svg>پشتیبانی و آموزش فارسی</span>
            </div>
            <a class="hv59f862" href="#modules" style="display:inline-flex;align-items:center;gap:7px;margin-top:16px;font-size:13.5px;font-weight:700;color:#E76F51;">
              ماژول‌های اضافه را هر وقت لازم داشتی، جداگانه بخر
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14"/><path d="m19 12-7 7-7-7"/></svg>
            </a>
            <a class="hv363ed5" href="<?= e($app_url) ?>" style="margin-top:22px;display:flex;align-items:center;justify-content:center;min-height:52px;background:transparent;color:#1F2A44;font-weight:700;font-size:16px;padding:13px 24px;border-radius:13px;border:1.5px solid #1F2A44;transition:background .2s ease,color .2s ease;">شروع با پلن پایه</a>
          </div>
        </div>

        <!-- VIP PLAN -->
        <div data-plan="vip" data-rv data-rv-delay="80" style="opacity:0;transform:translateY(18px);transition:opacity .5s ease,transform .5s ease;flex:1 1 340px;display:flex;">
          <div style="position:relative;flex:1;display:flex;flex-direction:column;background-color:#1F2A44;background-image:linear-gradient(rgba(255,255,255,.03) 1px,transparent 1px),linear-gradient(90deg,rgba(255,255,255,.03) 1px,transparent 1px);background-size:46px 46px;border-radius:20px;padding:30px 28px;box-shadow:0 22px 48px -24px rgba(31,42,68,.55);overflow:hidden;">
            <!-- stitched frame (animated on reveal) -->
            <svg id="vipFrame" style="position:absolute;inset:0;width:100%;height:100%;pointer-events:none;" preserveAspectRatio="none">
              <rect x="10" y="10" rx="13" fill="none" stroke="#E76F51" stroke-width="2" stroke-dasharray="9 7" style="width:calc(100% - 20px);height:calc(100% - 20px);opacity:.75;"/>
            </svg>
            <span style="position:absolute;top:0;inset-inline-start:26px;background:var(--color-primary);color:#fff;font-size:12px;font-weight:700;padding:6px 14px;border-radius:0 0 10px 10px;">یک‌بار بخر، برای همیشه</span>
            <h2 style="font-size:24px;font-weight:700;color:#fff;margin:18px 0 4px;">پلن ویژه</h2>
            <div style="font-size:14px;color:#9aa4bd;margin-bottom:22px;">همه‌چیز فعال، بدون دغدغه تمدید</div>
            <div style="display:flex;align-items:baseline;gap:8px;">
              <!-- placeholder: قیمت VIP -->
              <span style="font-size:40px;font-weight:700;color:#fff;letter-spacing:-.5px;"><?= e(fa_number_format($prices['vip'])) ?></span>
              <span style="font-size:15px;color:#9aa4bd;">تومان</span>
            </div>
            <div style="display:inline-flex;align-items:center;gap:7px;font-size:13.5px;font-weight:700;color:#E76F51;margin-top:6px;">
              <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
              بدون اشتراک ماهانه — برای همیشه
            </div>
            <div style="height:2px;background-image:repeating-linear-gradient(90deg,var(--color-primary) 0 9px,transparent 9px 16px);opacity:.5;margin:18px 0 16px;"></div>
            <div style="display:flex;flex-direction:column;gap:10px;font-size:14.5px;color:#dfe4f0;">
              <span style="display:flex;align-items:center;gap:10px;font-weight:700;color:#fff;"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#E76F51" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" style="flex:none;"><path d="M20 6 9 17l-5-5"/></svg>همه امکانات پلن پایه</span>
              <span style="display:flex;align-items:center;gap:10px;"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#E76F51" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" style="flex:none;"><path d="M20 6 9 17l-5-5"/></svg>ماژول گالری نمونه‌کار</span>
              <span style="display:flex;align-items:center;gap:10px;"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#E76F51" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" style="flex:none;"><path d="M20 6 9 17l-5-5"/></svg>ماژول سامانه پیامکی</span>
              <span style="display:flex;align-items:center;gap:10px;"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#E76F51" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" style="flex:none;"><path d="M20 6 9 17l-5-5"/></svg>ماژول درگاه پرداخت</span>
              <span style="display:flex;align-items:center;gap:10px;"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#E76F51" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" style="flex:none;"><path d="M20 6 9 17l-5-5"/></svg>ماژول دامنه اختصاصی</span>
              <span style="display:flex;align-items:center;gap:10px;"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#E76F51" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" style="flex:none;"><path d="M20 6 9 17l-5-5"/></svg>ماژول خط اختصاصی پیامکی</span>
              <span style="display:flex;align-items:center;gap:10px;"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#E76F51" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" style="flex:none;"><path d="M20 6 9 17l-5-5"/></svg>همه قابلیت‌های آینده</span>
            </div>
            <a class="hv791c00" href="<?= e($app_url) ?>" style="margin-top:auto;display:flex;align-items:center;justify-content:center;min-height:52px;background:var(--color-primary);color:#fff;font-weight:700;font-size:16px;padding:13px 24px;border-radius:13px;margin-top:24px;box-shadow:0 10px 26px rgba(231,111,81,.35);transition:background .2s ease,transform .2s ease;">شروع با پلن ویژه</a>
          </div>
        </div>
      </div>
      <div data-rv data-rv-delay="120" style="opacity:0;transform:translateY(14px);transition:opacity .5s ease,transform .5s ease;text-align:center;font-size:14px;color:#6B7280;margin-top:20px;display:flex;align-items:center;justify-content:center;gap:9px;">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#E76F51" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
        هر دو پلن با ۱۰ روز تست رایگان شروع می‌شوند.
      </div>
    </div>
  </section>

  <!-- MODULES -->
  <section id="modules" data-screen-label="ماژول‌ها" style="scroll-margin-top:110px;">
    <div style="max-width:1180px;margin-inline:auto;padding:56px 24px 30px;">
      <div data-rv style="opacity:0;transform:translateY(16px);transition:opacity .5s ease,transform .5s ease;text-align:center;margin-bottom:38px;">
        <h2 data-h2 style="font-size:36px;font-weight:700;color:#1F2A44;margin:0;line-height:1.6;">
          ماژول‌های اضافه —
          <span style="position:relative;white-space:nowrap;">یک‌بار بخر<svg width="100%" height="14" viewBox="0 0 140 14" preserveAspectRatio="none" style="position:absolute;inset-inline-start:0;bottom:-6px;overflow:visible;"><path d="M3 8 Q40 2 72 7 T137 6" stroke="#E76F51" stroke-width="6" fill="none" stroke-linecap="round" opacity=".85"/></svg></span>،
          برای همیشه
        </h2>
        <p style="font-size:16px;color:#6B7280;margin:20px 0 0;">مخصوص پلن پایه؛ در پلن ویژه همه از اول فعال‌اند.</p>
      </div>
      <div id="modGrid" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(250px,1fr));gap:18px;">
        <?php foreach ($modules as $i => $m): ?>
          <div data-rv data-rv-delay="<?= ($i % 3) * 60 ?>" style="opacity:0;transform:translateY(16px);transition:opacity .5s ease,transform .5s ease;">
            <div class="hv467fc8" style="height:100%;display:flex;flex-direction:column;background:#fff;border:1px solid var(--color-border);border-radius:16px;padding:22px;box-shadow:var(--shadow-card);transition:outline .18s ease,transform .25s ease,box-shadow .25s ease;">
              <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px;">
                <span style="display:inline-flex;width:44px;height:44px;align-items:center;justify-content:center;background:#FCEFEA;border-radius:50%;color:#E76F51;">
                  <svg width="21" height="21" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="<?= e($m['iconPath']) ?>"/></svg>
                </span>
                <span style="font-size:10.5px;font-weight:700;color:#D45A3D;background:#FCEFEA;border-radius:999px;padding:3px 10px;">پرداخت یک‌بار</span>
              </div>
              <h3 style="font-size:17px;font-weight:700;color:#1F2A44;margin:0 0 6px;"><?= e($m['title']) ?></h3>
              <p style="font-size:13.5px;color:#3D4A6B;margin:0 0 12px;flex:1;"><?= e($m['desc']) ?></p>
              <div style="display:flex;align-items:baseline;gap:6px;border-top:2px dashed #F0EBE1;padding-top:12px;">
                <!-- placeholder: قیمت ماژول -->
                <b style="font-size:20px;color:#1F2A44;"><?= e(fa_number_format($m['price'])) ?></b>
                <span style="font-size:12.5px;color:#6B7280;">تومان</span>
              </div>
              <?php if ($m['note'] !== ''): ?>
                <div style="font-size:11px;color:#9a9587;margin-top:6px;"><?= e($m['note']) ?></div>
              <?php endif; ?>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <!-- COMPARISON TABLE -->
  <section data-screen-label="جدول مقایسه">
    <div style="max-width:860px;margin-inline:auto;padding:40px 24px 30px;">
      <div data-rv style="opacity:0;transform:translateY(16px);transition:opacity .5s ease,transform .5s ease;text-align:center;margin-bottom:30px;">
        <h2 data-h2 style="font-size:36px;font-weight:700;color:#1F2A44;margin:0;">پایه یا ویژه؟</h2>
      </div>
      <div data-rv data-rv-delay="60" style="opacity:0;transform:translateY(16px);transition:opacity .5s ease,transform .5s ease;background:#fff;border:1px solid var(--color-border);border-radius:18px;box-shadow:var(--shadow-card);overflow-x:auto;">
        <div style="min-width:520px;">
          <div style="position:sticky;top:0;display:grid;grid-template-columns:1.6fr 1fr 1fr;background:#FBFAF7;border-bottom:1px solid #F0EDE6;z-index:2;">
            <div style="padding:15px 20px;font-size:13.5px;font-weight:700;color:#6B7280;">امکانات</div>
            <div style="padding:15px 12px;font-size:14.5px;font-weight:700;color:#1F2A44;text-align:center;">پلن پایه</div>
            <div style="padding:15px 12px;font-size:14.5px;font-weight:700;color:#E76F51;text-align:center;">پلن ویژه</div>
          </div>
          <?php foreach ($compare_rows as $r): $b = $cell_spec($r['base']); $v = $cell_spec($r['vip']); ?>
            <div style="display:grid;grid-template-columns:1.6fr 1fr 1fr;align-items:center;border-bottom:1px solid #F6F4EF;">
              <div style="padding:13px 20px;font-size:14px;color:#3D4A6B;"><?= e($r['label']) ?></div>
              <div style="padding:13px 12px;text-align:center;font-size:13px;color:#6B7280;">
                <?php if ($b['check']): ?><svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="#E76F51" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:middle;"><path d="M20 6 9 17l-5-5"/></svg><?php endif; ?>
                <?php if ($b['text'] !== ''): ?><span><?= e($b['text']) ?></span><?php endif; ?>
              </div>
              <div style="padding:13px 12px;text-align:center;font-size:13px;color:#6B7280;">
                <?php if ($v['check']): ?><svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="#E76F51" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:middle;"><path d="M20 6 9 17l-5-5"/></svg><?php endif; ?>
                <?php if ($v['text'] !== ''): ?><span><?= e($v['text']) ?></span><?php endif; ?>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  </section>

  <!-- PRICING FAQ -->
  <section id="faq" data-screen-label="سؤالات قیمت" style="scroll-margin-top:110px;">
    <div id="pricingFaq" data-rv style="opacity:0;transform:translateY(16px);transition:opacity .5s ease,transform .5s ease;max-width:760px;margin-inline:auto;padding:30px 24px 60px;">
      <h2 data-h2 style="font-size:36px;font-weight:700;color:#1F2A44;text-align:center;margin:0 0 34px;">سؤالات متداول قیمت</h2>
      <?php foreach ($faqs as $f): // all items closed initially, like the design ?>
        <div data-faq-item style="border-bottom:2px dashed #E7DFD3;">
          <button data-faq-btn style="width:100%;min-height:52px;background:none;border:none;display:flex;align-items:center;justify-content:space-between;gap:16px;padding:19px 4px;text-align:start;font-size:16.5px;font-weight:700;color:#1F2A44;">
            <span><?= e($f['q']) ?></span>
            <span data-faq-icon style="flex:none;color:#E76F51;font-size:24px;line-height:1;display:inline-block;transform:rotate(0deg);transition:transform .3s ease;">+</span>
          </button>
          <div data-faq-panel style="display:grid;grid-template-rows:0fr;transition:grid-template-rows .35s ease;">
            <div style="overflow:hidden;">
              <div style="padding:0 4px 20px;font-size:15px;line-height:1.9;color:#3D4A6B;"><?= e($f['a']) ?></div>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </section>

  <!-- FINAL CTA -->
  <section id="cta" data-screen-label="سی‌تی‌ای نهایی" style="scroll-margin-top:96px;">
    <div data-rv style="opacity:0;transform:translateY(18px);transition:opacity .5s ease,transform .5s ease;max-width:1000px;margin-inline:auto;padding:10px 24px 74px;">
      <div style="position:relative;background:#FCEFEA;border-radius:24px;padding:58px 28px;text-align:center;overflow:hidden;">
        <div style="position:absolute;inset:14px;border:2px dashed #E76F51;border-radius:16px;opacity:.55;pointer-events:none;"></div>
        <h2 data-h2 style="position:relative;font-size:38px;font-weight:700;color:#1F2A44;margin:0 0 14px;">هنوز مطمئن نیستی؟ ۱۰ روز رایگان امتحان کن</h2>
        <p style="position:relative;font-size:17px;color:#3D4A6B;margin:0 0 28px;">بدون نیاز به کارت بانکی؛ اگر دوستش نداشتی، هیچ هزینه‌ای نکرده‌ای.</p>
        <div style="position:relative;display:flex;flex-wrap:wrap;justify-content:center;align-items:center;gap:18px;">
          <a class="hv5ebc2e" href="<?= e($app_url) ?>" style="display:inline-flex;align-items:center;justify-content:center;min-height:54px;background:var(--color-primary);color:#fff;font-weight:700;font-size:18px;padding:15px 36px;border-radius:14px;box-shadow:0 10px 26px rgba(231,111,81,.34);transition:background .2s ease,transform .2s ease,box-shadow .2s ease;">شروع رایگان</a>
          <a class="hvd6c76f" href="/contact" style="display:inline-flex;align-items:center;min-height:44px;color:#1F2A44;font-weight:700;font-size:16px;">سؤالی داری؟ تماس با ما</a>
        </div>
      </div>
    </div>
  </section>

  <div id="footer" style="scroll-margin-top:96px;"><?php include __DIR__ . '/includes/site_footer.php'; ?></div>

</div>
<script>
// Pre-formatted period data for the tape-measure selector (page-pricing.js).
window.DK_PRICING = <?= json_encode([
    'order'   => $period_order,
    'default' => $default_period,
    'periods' => array_map(static fn ($p) => [
        'total'   => $p['total'],
        'unit'    => $p['unit'],
        'monthly' => $p['monthly'],
        'badge'   => $p['badge'],
    ], $periods),
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
</script>
<?php render_foot();
