<?php
/**
 * Home / landing page. Static placeholder content mirrors the DB seeds;
 * dynamic wiring arrives in later phases (blog Phase 3, FAQ/testimonials
 * Phase 5). Markup is the approved design, converted 1:1.
 */
require_once __DIR__ . '/includes/layout.php';

// FAQ (home) — matches the `faqs` seeds for page='home'.
$faqs = [
    ['q' => 'اطلاعاتم کجا ذخیره می‌شود؟ امن است؟', 'a' => 'همه اطلاعات شما روی سرورهای امن ابری نگهداری و به‌طور خودکار پشتیبان‌گیری می‌شود؛ فقط خودتان به آن دسترسی دارید.'],
    ['q' => 'به اینترنت قوی نیاز دارم؟', 'a' => 'نه. دوختک با اینترنت معمولی موبایل هم به‌خوبی کار می‌کند.'],
    ['q' => 'اگر با نرم‌افزار راحت نباشم چه؟', 'a' => 'آموزش و پشتیبانی کامل فارسی داریم و قدم‌به‌قدم کنارتان هستیم تا راه بیفتید.'],
    ['q' => 'بعد از ۱۰ روز رایگان چه می‌شود؟', 'a' => 'بعد از پایان دوره رایگان می‌توانید یکی از پلن‌ها را انتخاب کنید؛ اطلاعاتتان محفوظ می‌ماند.'],
    ['q' => 'روی گوشی کار می‌کند؟', 'a' => 'بله، روی موبایل، تبلت و کامپیوتر بدون نصب برنامه اجرا می‌شود.'],
    ['q' => 'می‌توانم سایت را روی دامنه خودم داشته باشم؟', 'a' => 'بله، دوختک می‌تواند روی دامنه اختصاصی خودتان بالا بیاید.'],
    ['q' => 'هر وقت بخواهم می‌توانم لغو کنم؟', 'a' => 'بله، هیچ قراردادی نیست و هر زمان می‌توانید اشتراک را لغو کنید.'],
];

// Before/after comparison rows (dark section).
$compare = [
    ['before' => 'برای اندازه‌های مشتری قدیمی، دفتر را ورق می‌زنی', 'after' => 'اندازه‌ها با دو ثانیه جستجو جلوی چشمت است'],
    ['before' => 'برای طلبت زنگ می‌زنی و منتظر کارت‌به‌کارت می‌مانی', 'after' => 'لینک پرداخت می‌فرستی، همان لحظه آنلاین تسویه می‌شود'],
    ['before' => 'نمونه‌کارها پراکنده در گوشی گم می‌شوند', 'after' => 'گالری مرتب را با یک لینک به مشتری نشان می‌دهی'],
    ['before' => 'سررسید چک‌ها را باید حفظ کنی و استرس داری', 'after' => 'دوختک چک‌ها را خودش به‌موقع یادآوری می‌کند'],
    ['before' => 'دخل‌وخرج را آخر ماه با ماشین‌حساب درمی‌آوری', 'after' => 'گزارش درآمد و هزینه لحظه‌ای آماده است'],
];

// Testimonials — matches the `testimonials` seeds.
$testimonials = [
    ['quote' => 'دفترهای سفارشم را کنار گذاشتم. حالا هر سفارش با سررسیدش جلوی چشمم است و دیگر چیزی از قلم نمی‌افتد.', 'name' => 'نرگس ح.', 'meta' => 'تهران — مزون مانتو', 'rot' => '-1.5deg'],
    ['quote' => 'مشتری‌ها گالری کارهایم را در گوشی می‌بینند و راحت انتخاب می‌کنند. سفارش‌هایم بیشتر شده.', 'name' => 'فاطمه ر.', 'meta' => 'اصفهان — خیاطی زنانه', 'rot' => '1deg'],
    ['quote' => 'لینک پرداخت را که فرستادم، دیگر دنبال کارت‌به‌کارت و پیگیری نیستم.', 'name' => 'سمیرا ک.', 'meta' => 'شیراز — تعمیرات پوشاک', 'rot' => '-.75deg'],
];

$app_url = setting('app_url', 'https://app.dookhtak.ir');

render_head([
    'title' => 'دوختک — سامانه ابری مدیریت خیاطی و مزون',
    'description' => 'دوختک همه کارهای مدیریتی خیاطی را ساده می‌کند: سفارش، مشتری، گالری و حسابداری، همه روی یک میز.',
    'css' => '/assets/css/page-home.css',
    'js' => '/assets/js/page-home.js',
]);
?>
<div dir="rtl" lang="fa" style="position:relative;min-height:100vh;background-color:#FAF8F4;background-image:linear-gradient(#EDEAE3 1px,transparent 1px),linear-gradient(90deg,#EDEAE3 1px,transparent 1px);background-size:46px 46px;overflow-x:hidden;line-height:1.6;">

  <!-- tape measure scroll progress (desktop only) -->
  <div data-desktop-only="block" style="display:none;position:fixed;top:0;inset-inline:0;height:6px;z-index:70;background:#EFEBE2;">
    <div id="tapeFill" style="height:100%;width:0%;background:#E76F51;"></div>
    <div style="position:absolute;inset:0;background-image:repeating-linear-gradient(90deg,rgba(31,42,68,.16) 0 1px,transparent 1px 12px);pointer-events:none;"></div>
  </div>

<?php include __DIR__ . '/includes/site_header.php'; ?>

  <!-- HERO -->
  <section id="top" data-screen-label="هیرو" style="position:relative;">
    <div id="heroWrap" style="max-width:1180px;margin-inline:auto;padding:126px 24px 44px;display:flex;flex-wrap:wrap;align-items:center;gap:56px;">
      <!-- copy -->
      <div style="flex:1 1 400px;min-width:290px;">
        <span data-hero style="opacity:0;transform:translateY(14px);transition:opacity .5s ease,transform .5s ease;display:inline-block;background:#FCEFEA;color:#D45A3D;font-size:13.5px;font-weight:700;padding:7px 15px;border-radius:999px;letter-spacing:.2px;">سامانه ابری مدیریت خیاطی و مزون</span>
        <h1 id="heroTitle" data-hero style="opacity:0;transform:translateY(14px);transition:opacity .5s ease,transform .5s ease;font-size:52px;line-height:1.4;font-weight:700;color:#1F2A44;margin:22px 0 0;letter-spacing:-.5px;">
          سفارش، مشتری و حسابِ خیاطی‌ات،<br>همه روی
          <span style="position:relative;white-space:nowrap;color:#E76F51;">یک میز<span id="heroStitch" style="position:absolute;inset-inline-start:0;bottom:-10px;width:100%;height:12px;clip-path:inset(0 0 0 0);background-image:repeating-linear-gradient(90deg,#E76F51 0 11px,transparent 11px 19px);"></span></span>
        </h1>
        <p data-hero style="opacity:0;transform:translateY(14px);transition:opacity .5s ease,transform .5s ease;font-size:18px;line-height:1.85;color:#3D4A6B;margin:30px 0 0;max-width:520px;">دوختک همه کارهای مدیریتی خیاطی را ساده می‌کند تا تو فقط بدوزی.</p>
        <div id="heroCtas" data-hero style="opacity:0;transform:translateY(14px);transition:opacity .5s ease,transform .5s ease;display:flex;flex-wrap:wrap;gap:12px;margin-top:32px;">
          <a class="hv6ef100" href="#cta" style="display:inline-flex;align-items:center;justify-content:center;gap:8px;white-space:nowrap;min-height:52px;background:#E76F51;color:#fff;font-weight:700;font-size:17px;padding:14px 28px;border-radius:13px;box-shadow:0 8px 22px rgba(231,111,81,.3);transition:background .2s ease,transform .2s ease,box-shadow .2s ease;">۱۰ روز رایگان شروع کن</a>
          <a class="hv363ed5" href="#gallery" style="display:inline-flex;align-items:center;justify-content:center;gap:8px;min-height:52px;background:transparent;color:#1F2A44;font-weight:700;font-size:17px;padding:14px 26px;border-radius:13px;border:1.5px solid #1F2A44;transition:background .2s ease,color .2s ease;">مشاهده دمو</a>
        </div>
        <p data-hero style="opacity:0;transform:translateY(14px);transition:opacity .5s ease,transform .5s ease;display:flex;align-items:center;gap:10px;font-size:14px;color:#6B7280;margin-top:22px;">
          <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="#E76F51" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
          بدون نیاز به کارت بانکی · راه‌اندازی در ۲ دقیقه
        </p>
      </div>

      <!-- art -->
      <div id="heroArt" data-hero style="opacity:0;transform:translateY(14px);transition:opacity .5s ease,transform .5s ease;flex:1 1 440px;min-width:290px;position:relative;padding:14px 8px;">
        <!-- desktop dashboard frame -->
        <div id="heroDesk" style="position:relative;background:#fff;border:1px solid #E8E6E1;border-radius:14px;box-shadow:0 2px 6px rgba(31,42,68,.08),0 28px 56px -22px rgba(31,42,68,.32);transform:rotate(.75deg);overflow:hidden;">
          <div style="display:flex;align-items:center;gap:6px;padding:10px 14px;border-bottom:1px solid #F0EDE6;background:#FBFAF7;">
            <span style="width:10px;height:10px;border-radius:50%;background:#E6E1D7;"></span>
            <span style="width:10px;height:10px;border-radius:50%;background:#E6E1D7;"></span>
            <span style="width:10px;height:10px;border-radius:50%;background:#E6E1D7;"></span>
            <span style="margin-inline-start:auto;font-size:11.5px;color:#9a9587;background:#fff;border:1px solid #EDEAE3;border-radius:7px;padding:3px 12px;">app.dookhtak.ir/dashboard</span>
          </div>
          <div style="display:flex;min-height:290px;">
            <div style="width:120px;flex:none;background:#1F2A44;padding:16px 10px;display:flex;flex-direction:column;gap:5px;">
              <div style="display:flex;align-items:center;gap:7px;color:#fff;font-weight:700;font-size:13px;margin-bottom:10px;padding-inline-start:4px;"><img src="/assets/images/Logo01-NEW.png" style="height:20px;filter:brightness(0) invert(1);opacity:.95;" alt="">دوختک</div>
              <div style="font-size:12px;color:#9aa4bd;padding:7px 10px;border-radius:8px;">داشبورد</div>
              <div style="font-size:12px;color:#fff;background:#E76F51;padding:7px 10px;border-radius:8px;font-weight:700;">سفارش‌ها</div>
              <div style="font-size:12px;color:#9aa4bd;padding:7px 10px;border-radius:8px;">مشتری‌ها</div>
              <div style="font-size:12px;color:#9aa4bd;padding:7px 10px;border-radius:8px;">گالری</div>
              <div style="font-size:12px;color:#9aa4bd;padding:7px 10px;border-radius:8px;">حسابداری</div>
              <div style="font-size:12px;color:#9aa4bd;padding:7px 10px;border-radius:8px;">پیامک</div>
            </div>
            <div style="flex:1;padding:18px 16px;background:#fff;">
              <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:14px;">
                <span style="font-size:15px;font-weight:700;color:#1F2A44;">سفارش‌های جاری</span>
                <span style="font-size:11px;color:#fff;background:#E76F51;padding:4px 12px;border-radius:8px;font-weight:700;">+ سفارش جدید</span>
              </div>
              <div style="display:grid;grid-template-columns:1.4fr 1fr .8fr 1fr;font-size:11px;color:#9a9587;padding:0 4px 8px;border-bottom:1px solid #F0EDE6;">
                <span>مشتری</span><span>نوع لباس</span><span>تحویل</span><span>وضعیت</span>
              </div>
              <div style="display:grid;grid-template-columns:1.4fr 1fr .8fr 1fr;align-items:center;font-size:12px;color:#3D4A6B;padding:11px 4px;border-bottom:1px solid #F6F4EF;"><span style="font-weight:700;color:#1F2A44;">مریم احمدی</span><span>مانتو مجلسی</span><span>۱۴ تیر</span><span><b style="color:#E76F51;font-weight:700;">در حال دوخت</b></span></div>
              <div style="display:grid;grid-template-columns:1.4fr 1fr .8fr 1fr;align-items:center;font-size:12px;color:#3D4A6B;padding:11px 4px;border-bottom:1px solid #F6F4EF;"><span style="font-weight:700;color:#1F2A44;">سارا کریمی</span><span>کت و دامن</span><span>۱۸ تیر</span><span><b style="color:#1F8A5B;font-weight:700;">آماده تحویل</b></span></div>
              <div style="display:grid;grid-template-columns:1.4fr 1fr .8fr 1fr;align-items:center;font-size:12px;color:#3D4A6B;padding:11px 4px;border-bottom:1px solid #F6F4EF;"><span style="font-weight:700;color:#1F2A44;">زهرا موسوی</span><span>لباس شب</span><span>۲۲ تیر</span><span style="color:#6B7280;">برش</span></div>
              <div style="display:grid;grid-template-columns:1.4fr 1fr .8fr 1fr;align-items:center;font-size:12px;color:#3D4A6B;padding:11px 4px;"><span style="font-weight:700;color:#1F2A44;">الهام رضایی</span><span>پیراهن</span><span>۲۵ تیر</span><span style="color:#6B7280;">سفارش جدید</span></div>
            </div>
          </div>
        </div>

        <!-- mobile frame -->
        <div id="heroPhone" style="position:absolute;bottom:-30px;inset-inline-start:-10px;width:154px;background:#1F2A44;border-radius:24px;padding:8px;border:1px solid #E8E6E1;box-shadow:0 2px 6px rgba(31,42,68,.14),0 22px 44px -16px rgba(31,42,68,.42);transform:rotate(-2deg);z-index:4;">
          <div style="background:#fff;border-radius:18px;overflow:hidden;">
            <div style="height:22px;background:#1F2A44;display:flex;justify-content:center;align-items:flex-start;"><span style="width:44px;height:5px;background:#3D4A6B;border-radius:0 0 6px 6px;"></span></div>
            <div style="padding:10px;">
              <div style="font-size:11px;font-weight:700;color:#1F2A44;margin-bottom:8px;">سفارش‌های امروز</div>
              <div style="background:#FAF8F4;border:1px solid #EDEAE3;border-radius:9px;padding:8px;margin-bottom:6px;"><div style="font-size:10.5px;font-weight:700;color:#1F2A44;">مریم احمدی</div><div style="font-size:9px;color:#6B7280;margin-top:2px;">مانتو · ۱۴ تیر</div><div style="font-size:9px;color:#E76F51;font-weight:700;margin-top:3px;">در حال دوخت</div></div>
              <div style="background:#FAF8F4;border:1px solid #EDEAE3;border-radius:9px;padding:8px;"><div style="font-size:10.5px;font-weight:700;color:#1F2A44;">سارا کریمی</div><div style="font-size:9px;color:#6B7280;margin-top:2px;">کت و دامن · ۱۸ تیر</div><div style="font-size:9px;color:#1F8A5B;font-weight:700;margin-top:3px;">آماده تحویل</div></div>
            </div>
          </div>
        </div>

        <!-- annotations (desktop only, subtle) -->
        <div data-desktop-only="block" style="display:none;position:absolute;top:66px;inset-inline-start:16px;font-size:11px;font-weight:700;color:#6B7280;letter-spacing:.3px;transform:rotate(-2.5deg);z-index:6;">
          سررسید تحویل هر سفارش
          <svg width="66" height="26" viewBox="0 0 66 26" fill="none" style="display:block;margin-top:2px;transform:scaleX(-1);"><path d="M62 4 Q30 8 6 22" stroke="#9CA3AF" stroke-width="1.4" stroke-dasharray="4 3" stroke-linecap="round"/><path d="M6 22 13 17 M6 22 14 23" stroke="#9CA3AF" stroke-width="1.4" stroke-linecap="round"/></svg>
        </div>
        <div data-desktop-only="block" style="display:none;position:absolute;bottom:-46px;inset-inline-start:150px;font-size:11px;font-weight:700;color:#6B7280;letter-spacing:.3px;transform:rotate(2deg);z-index:6;">
          <svg width="52" height="22" viewBox="0 0 52 22" fill="none" style="display:block;margin-bottom:2px;"><path d="M4 20 Q22 4 48 6" stroke="#9CA3AF" stroke-width="1.4" stroke-dasharray="4 3" stroke-linecap="round"/><path d="M48 6 41 3 M48 6 42 11" stroke="#9CA3AF" stroke-width="1.4" stroke-linecap="round"/></svg>
          دسترسی از موبایل
        </div>
      </div>
    </div>
  </section>

  <!-- stitch divider -->
  <div style="max-width:1180px;margin:26px auto 0;height:2px;background-image:repeating-linear-gradient(90deg,#E76F51 0 11px,transparent 11px 20px);opacity:.6;"></div>

  <!-- FEATURES -->
  <section id="features" data-screen-label="امکانات" style="scroll-margin-top:96px;">
    <div style="max-width:1180px;margin-inline:auto;padding:66px 24px 20px;">
      <div data-rv="y" style="opacity:0;transform:translateY(18px);transition:opacity .5s ease,transform .5s ease;text-align:center;margin-bottom:44px;">
        <span style="font-size:13px;font-weight:700;color:#D45A3D;letter-spacing:1px;">امکانات دوختک</span>
        <h2 data-h2 style="font-size:38px;font-weight:700;color:#1F2A44;margin:12px 0 0;">هر کاری،
          <span style="position:relative;white-space:nowrap;">یک تکه<svg width="100%" height="14" viewBox="0 0 140 14" preserveAspectRatio="none" style="position:absolute;inset-inline-start:0;bottom:-6px;overflow:visible;"><path d="M3 8 Q40 2 72 7 T137 6" stroke="#E76F51" stroke-width="6" fill="none" stroke-linecap="round" opacity=".85"/></svg></span>
          سرِ جایش</h2>
        <p style="font-size:17px;color:#6B7280;max-width:560px;margin:14px auto 0;">امکانات دوختک مثل تکه‌های یک الگو کنار هم می‌نشینند تا کل کار مزونت را بپوشانند.</p>
      </div>

      <div id="featGrid" style="display:grid;grid-template-columns:repeat(12,1fr);gap:18px;align-items:stretch;">

        <!-- 1 orders -->
        <div data-span="7" data-rv="y" data-rv-delay="0" style="opacity:0;transform:translateY(18px);transition:opacity .5s ease,transform .5s ease;">
          <div class="hv86f556" style="height:100%;background:#fff;border:1px solid #E8E6E1;border-radius:18px 18px 18px 40px;padding:26px;box-shadow:0 10px 30px -22px rgba(31,42,68,.35);transition:outline .18s ease,box-shadow .25s ease,transform .25s ease;">
            <div style="display:flex;flex-wrap:wrap;gap:22px;align-items:center;">
              <div style="flex:1 1 210px;">
                <span style="display:inline-flex;width:48px;height:48px;align-items:center;justify-content:center;background:#FCEFEA;border-radius:50%;color:#E76F51;"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="8" height="4" x="8" y="2" rx="1"/><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/><path d="M12 11h4"/><path d="M12 16h4"/><path d="M8 11h.01"/><path d="M8 16h.01"/></svg></span>
                <h3 style="font-size:21px;font-weight:700;color:#1F2A44;margin:16px 0 8px;">مدیریت سفارش‌ها</h3>
                <p style="font-size:15px;color:#3D4A6B;margin:0;">ثبت سفارش، مراحل کار و سررسید تحویل؛ هیچ سفارشی فراموش نمی‌شود.</p>
              </div>
              <!-- SCREENSHOT-3: صفحه ثبت/مدیریت سفارش -->
              <div style="flex:1 1 210px;background:#FAF8F4;border:1px solid #EDEAE3;border-radius:12px;padding:12px;">
                <div style="display:grid;grid-template-columns:1.2fr .7fr .9fr;font-size:10px;color:#9a9587;padding-bottom:6px;border-bottom:1px solid #EDEAE3;"><span>مشتری</span><span>تحویل</span><span>وضعیت</span></div>
                <div style="display:grid;grid-template-columns:1.2fr .7fr .9fr;align-items:center;font-size:11.5px;padding:8px 0;border-bottom:1px solid #F0EDE6;"><span style="font-weight:700;color:#1F2A44;">مریم احمدی</span><span style="color:#6B7280;">۱۴ تیر</span><span style="justify-self:start;background:#FCEFEA;color:#D45A3D;font-weight:700;font-size:10px;padding:2px 8px;border-radius:999px;">در حال دوخت</span></div>
                <div style="display:grid;grid-template-columns:1.2fr .7fr .9fr;align-items:center;font-size:11.5px;padding:8px 0;border-bottom:1px solid #F0EDE6;"><span style="font-weight:700;color:#1F2A44;">سارا کریمی</span><span style="color:#6B7280;">۱۸ تیر</span><span style="justify-self:start;background:#E8F5EE;color:#1F8A5B;font-weight:700;font-size:10px;padding:2px 8px;border-radius:999px;">آماده تحویل</span></div>
                <div style="display:grid;grid-template-columns:1.2fr .7fr .9fr;align-items:center;font-size:11.5px;padding:8px 0 0;"><span style="font-weight:700;color:#1F2A44;">زهرا موسوی</span><span style="color:#6B7280;">۲۲ تیر</span><span style="justify-self:start;background:#EEF1F7;color:#3D4A6B;font-weight:700;font-size:10px;padding:2px 8px;border-radius:999px;">برش</span></div>
              </div>
            </div>
          </div>
        </div>

        <!-- 2 customer file -->
        <div data-span="5" data-rv="y" data-rv-delay="70" data-offset="12" style="opacity:0;transform:translateY(18px);transition:opacity .5s ease,transform .5s ease;">
          <div class="hv86f556" style="height:100%;background:#fff;border:1px solid #E8E6E1;border-radius:18px 40px 18px 18px;padding:26px;box-shadow:0 10px 30px -22px rgba(31,42,68,.35);transition:outline .18s ease,box-shadow .25s ease,transform .25s ease;">
            <span style="display:inline-flex;width:48px;height:48px;align-items:center;justify-content:center;background:#FCEFEA;border-radius:50%;color:#E76F51;"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21.3 15.3a2.4 2.4 0 0 1 0 3.4l-2.6 2.6a2.4 2.4 0 0 1-3.4 0L2.7 8.7a2.41 2.41 0 0 1 0-3.4l2.6-2.6a2.41 2.41 0 0 1 3.4 0Z"/><path d="m14.5 12.5 2-2"/><path d="m11.5 9.5 2-2"/><path d="m8.5 6.5 2-2"/><path d="m17.5 15.5 2-2"/></svg></span>
            <h3 style="font-size:21px;font-weight:700;color:#1F2A44;margin:16px 0 8px;">پرونده مشتری و اندازه‌ها</h3>
            <p style="font-size:15px;color:#3D4A6B;margin:0 0 14px;">اندازه‌های اندام هر مشتری یک‌بار ثبت می‌شود و همیشه در دسترس است.</p>
            <!-- SCREENSHOT-4: پرونده مشتری با اندازه‌ها -->
            <div style="background:#FAF8F4;border:1px solid #EDEAE3;border-radius:12px;padding:12px 14px;">
              <div style="font-size:12px;font-weight:700;color:#1F2A44;margin-bottom:8px;">مریم احمدی — اندازه‌ها</div>
              <div style="display:flex;flex-wrap:wrap;gap:6px;font-size:11.5px;">
                <span style="background:#fff;border:1px solid #EDEAE3;border-radius:7px;padding:4px 9px;color:#3D4A6B;">قد ۱۶۸</span>
                <span style="background:#fff;border:1px solid #EDEAE3;border-radius:7px;padding:4px 9px;color:#3D4A6B;">دور سینه ۹۲</span>
                <span style="background:#fff;border:1px solid #EDEAE3;border-radius:7px;padding:4px 9px;color:#3D4A6B;">دور کمر ۷۴</span>
                <span style="background:#fff;border:1px solid #EDEAE3;border-radius:7px;padding:4px 9px;color:#3D4A6B;">دور باسن ۱۰۰</span>
                <span style="background:#fff;border:1px solid #EDEAE3;border-radius:7px;padding:4px 9px;color:#3D4A6B;">قد آستین ۵۸</span>
              </div>
            </div>
          </div>
        </div>

        <!-- 3 payment -->
        <div data-span="5" data-rv="y" data-rv-delay="0" style="opacity:0;transform:translateY(18px);transition:opacity .5s ease,transform .5s ease;">
          <div class="hv86f556" style="height:100%;background:#fff;border:1px solid #E8E6E1;border-radius:40px 18px 18px 18px;padding:26px;box-shadow:0 10px 30px -22px rgba(31,42,68,.35);transition:outline .18s ease,box-shadow .25s ease,transform .25s ease;">
            <span style="display:inline-flex;width:48px;height:48px;align-items:center;justify-content:center;background:#FCEFEA;border-radius:50%;color:#E76F51;"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="14" x="2" y="5" rx="2"/><line x1="2" x2="22" y1="10" y2="10"/></svg></span>
            <h3 style="font-size:21px;font-weight:700;color:#1F2A44;margin:16px 0 8px;">لینک پرداخت آنلاین</h3>
            <p style="font-size:15px;color:#3D4A6B;margin:0 0 14px;">لینک پرداخت را بفرست، مشتری همان لحظه آنلاین تسویه کند؛ بدون کارت‌به‌کارت و پیگیری.</p>
            <div style="background:#FAF8F4;border:1px solid #EDEAE3;border-radius:12px;padding:12px;display:flex;flex-direction:column;gap:8px;">
              <div style="align-self:flex-start;max-width:88%;background:#fff;border:1px solid #EDEAE3;border-radius:12px 12px 12px 4px;padding:8px 12px;font-size:11.5px;color:#3D4A6B;">لینک پرداخت سفارش شما: <b style="color:#E76F51;font-weight:700;">pay.dookhtak.ir/m84</b></div>
              <div style="align-self:flex-end;display:flex;align-items:center;gap:6px;background:#E8F5EE;border:1px solid #C4E5D2;border-radius:12px 12px 4px 12px;padding:8px 12px;font-size:11.5px;color:#1F8A5B;font-weight:700;">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
                پرداخت شد · ۲٬۴۵۰٬۰۰۰ تومان
              </div>
            </div>
          </div>
        </div>

        <!-- 4 accounting -->
        <div data-span="7" data-rv="y" data-rv-delay="70" data-offset="12" style="opacity:0;transform:translateY(18px);transition:opacity .5s ease,transform .5s ease;">
          <div class="hv86f556" style="height:100%;background:#fff;border:1px solid #E8E6E1;border-radius:18px 18px 40px 18px;padding:26px;box-shadow:0 10px 30px -22px rgba(31,42,68,.35);transition:outline .18s ease,box-shadow .25s ease,transform .25s ease;">
            <div style="display:flex;flex-wrap:wrap;gap:22px;align-items:center;">
              <div style="flex:1 1 210px;">
                <span style="display:inline-flex;width:48px;height:48px;align-items:center;justify-content:center;background:#FCEFEA;border-radius:50%;color:#E76F51;"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 2v20l2-1 2 1 2-1 2 1 2-1 2 1 2-1 2 1V2l-2 1-2-1-2 1-2-1-2 1-2-1-2 1Z"/><path d="M16 8h-6a2 2 0 1 0 0 4h4a2 2 0 1 1 0 4H8"/><path d="M12 17.5v-11"/></svg></span>
                <h3 style="font-size:21px;font-weight:700;color:#1F2A44;margin:16px 0 8px;">حسابداری حرفه‌ای</h3>
                <p style="font-size:15px;color:#3D4A6B;margin:0;">همه درآمدها و هزینه‌ها ثبت می‌شود و چک‌های دریافتی و پرداختی به‌موقع یادآوری می‌شوند.</p>
              </div>
              <div style="flex:1 1 210px;background:#FAF8F4;border:1px solid #EDEAE3;border-radius:12px;padding:14px;">
                <div style="display:flex;align-items:flex-end;justify-content:space-around;gap:8px;height:74px;border-bottom:1px solid #E3DFD5;padding-bottom:0;">
                  <div style="display:flex;align-items:flex-end;gap:3px;"><span style="width:11px;height:38px;background:#1F2A44;border-radius:3px 3px 0 0;"></span><span style="width:11px;height:22px;background:#E76F51;border-radius:3px 3px 0 0;opacity:.85;"></span></div>
                  <div style="display:flex;align-items:flex-end;gap:3px;"><span style="width:11px;height:50px;background:#1F2A44;border-radius:3px 3px 0 0;"></span><span style="width:11px;height:18px;background:#E76F51;border-radius:3px 3px 0 0;opacity:.85;"></span></div>
                  <div style="display:flex;align-items:flex-end;gap:3px;"><span style="width:11px;height:34px;background:#1F2A44;border-radius:3px 3px 0 0;"></span><span style="width:11px;height:26px;background:#E76F51;border-radius:3px 3px 0 0;opacity:.85;"></span></div>
                  <div style="display:flex;align-items:flex-end;gap:3px;"><span style="width:11px;height:58px;background:#1F2A44;border-radius:3px 3px 0 0;"></span><span style="width:11px;height:20px;background:#E76F51;border-radius:3px 3px 0 0;opacity:.85;"></span></div>
                </div>
                <div style="display:flex;gap:14px;font-size:10px;color:#6B7280;margin-top:7px;">
                  <span style="display:flex;align-items:center;gap:4px;"><i style="width:8px;height:8px;background:#1F2A44;border-radius:2px;"></i>درآمد</span>
                  <span style="display:flex;align-items:center;gap:4px;"><i style="width:8px;height:8px;background:#E76F51;border-radius:2px;"></i>هزینه</span>
                </div>
                <div style="display:flex;align-items:center;gap:7px;background:#fff;border:1px solid #EDEAE3;border-radius:9px;padding:7px 10px;margin-top:10px;font-size:11px;color:#3D4A6B;">
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#E76F51" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"/><path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"/></svg>
                  یادآوری: چک دریافتی — <b style="color:#1F2A44;">۱۵ تیر</b>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- 5 referral -->
        <div data-span="6" data-rv="y" data-rv-delay="0" style="opacity:0;transform:translateY(18px);transition:opacity .5s ease,transform .5s ease;">
          <div class="hv86f556" style="height:100%;background:#fff;border:1px solid #E8E6E1;border-radius:18px 18px 18px 40px;padding:26px;box-shadow:0 10px 30px -22px rgba(31,42,68,.35);transition:outline .18s ease,box-shadow .25s ease,transform .25s ease;">
            <span style="display:inline-flex;width:48px;height:48px;align-items:center;justify-content:center;background:#FCEFEA;border-radius:50%;color:#E76F51;"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg></span>
            <h3 style="font-size:21px;font-weight:700;color:#1F2A44;margin:16px 0 8px;">ارجاع سفارش به همکار</h3>
            <p style="font-size:15px;color:#3D4A6B;margin:0 0 14px;">سفارش را به خیاط‌های زیرمجموعه‌ات بسپار و فقط روند کار را دنبال کن. مناسب مزون‌های بزرگ‌تر.</p>
            <div style="background:#FAF8F4;border:1px solid #EDEAE3;border-radius:12px;padding:14px;display:flex;align-items:center;justify-content:center;gap:10px;">
              <span style="display:flex;flex-direction:column;align-items:center;gap:4px;"><i style="width:38px;height:38px;border-radius:50%;background:#1F2A44;color:#fff;display:flex;align-items:center;justify-content:center;font-style:normal;font-size:13px;font-weight:700;">م</i><b style="font-size:10px;color:#6B7280;font-weight:400;">مزون تو</b></span>
              <span style="flex:1;max-width:90px;display:flex;flex-direction:column;align-items:center;gap:3px;"><svg width="100%" height="10" viewBox="0 0 90 10" preserveAspectRatio="none"><path d="M88 5 H10" stroke="#E76F51" stroke-width="2" stroke-dasharray="7 5" stroke-linecap="round"/><path d="M14 1 8 5l6 4" stroke="#E76F51" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"/></svg><b style="font-size:9.5px;color:#D45A3D;background:#FCEFEA;border-radius:999px;padding:2px 8px;">ارجاع شد</b></span>
              <span style="display:flex;flex-direction:column;align-items:center;gap:4px;"><i style="width:38px;height:38px;border-radius:50%;background:#E76F51;color:#fff;display:flex;align-items:center;justify-content:center;font-style:normal;font-size:13px;font-weight:700;">خ</i><b style="font-size:10px;color:#6B7280;font-weight:400;">خیاط همکار</b></span>
            </div>
          </div>
        </div>

        <!-- 6 club -->
        <div data-span="6" data-rv="y" data-rv-delay="70" data-offset="12" style="opacity:0;transform:translateY(18px);transition:opacity .5s ease,transform .5s ease;">
          <div class="hv86f556" style="height:100%;background:#fff;border:1px solid #E8E6E1;border-radius:18px 18px 40px 18px;padding:26px;box-shadow:0 10px 30px -22px rgba(31,42,68,.35);transition:outline .18s ease,box-shadow .25s ease,transform .25s ease;">
            <span style="display:inline-flex;width:48px;height:48px;align-items:center;justify-content:center;background:#FCEFEA;border-radius:50%;color:#E76F51;"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m3 11 18-5v12L3 14v-3z"/><path d="M11.6 16.8a3 3 0 1 1-5.8-1.6"/></svg></span>
            <h3 style="font-size:21px;font-weight:700;color:#1F2A44;margin:16px 0 8px;">باشگاه مشتریان</h3>
            <p style="font-size:15px;color:#3D4A6B;margin:0 0 14px;">پیامک انبوه به همه مشتری‌ها و پیامک تبریک تولد خودکار؛ مشتری‌ها را کنارت نگه‌دار.</p>
            <div style="background:#FAF8F4;border:1px solid #EDEAE3;border-radius:12px;padding:12px;display:flex;flex-direction:column;gap:8px;">
              <div style="align-self:flex-start;max-width:92%;display:flex;align-items:flex-start;gap:7px;background:#fff;border:1px solid #EDEAE3;border-radius:12px 12px 12px 4px;padding:8px 12px;font-size:11.5px;color:#3D4A6B;">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#E76F51" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex:none;margin-top:2px;"><path d="M20 21v-8a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v8"/><path d="M4 16s.5-1 2-1 2.5 2 4 2 2.5-2 4-2 2.5 2 4 2 2-1 2-1"/><path d="M2 21h20"/><path d="M7 8v3"/><path d="M12 8v3"/><path d="M17 8v3"/></svg>
                مریم جان، تولدت مبارک! دوخت بعدی مهمان تخفیف مزون ما هستی.
              </div>
              <div style="align-self:flex-start;max-width:92%;display:flex;align-items:flex-start;gap:7px;background:#fff;border:1px solid #EDEAE3;border-radius:12px 12px 12px 4px;padding:8px 12px;font-size:11.5px;color:#3D4A6B;">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#E76F51" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex:none;margin-top:2px;"><path d="m3 11 18-5v12L3 14v-3z"/><path d="M11.6 16.8a3 3 0 1 1-5.8-1.6"/></svg>
                پارچه‌های جدید پاییزی رسید — گالری مزون را ببین.
              </div>
            </div>
          </div>
        </div>

      </div>
    </div>
  </section>

  <!-- GALLERY SPOTLIGHT -->
  <section id="gallery" data-screen-label="اسپات‌لایت گالری" style="scroll-margin-top:96px;">
    <div data-rv="y" style="opacity:0;transform:translateY(18px);transition:opacity .5s ease,transform .5s ease;max-width:1180px;margin-inline:auto;padding:76px 24px 70px;display:flex;flex-wrap:wrap;align-items:center;gap:52px;">
      <div style="flex:1 1 360px;min-width:290px;">
        <span style="font-size:13px;font-weight:700;color:#D45A3D;letter-spacing:1px;">ویترین آنلاین مزونت</span>
        <h2 data-h2 style="font-size:38px;font-weight:700;color:#1F2A44;margin:12px 0 0;line-height:1.4;">نمونه‌کارهایت را
          <span style="position:relative;white-space:nowrap;">ویترین<svg width="100%" height="14" viewBox="0 0 140 14" preserveAspectRatio="none" style="position:absolute;inset-inline-start:0;bottom:-8px;overflow:visible;"><path d="M3 8 Q40 2 72 7 T137 6" stroke="#E76F51" stroke-width="6" fill="none" stroke-linecap="round" opacity=".85"/></svg></span>
          کن</h2>
        <p style="font-size:17px;line-height:1.9;color:#3D4A6B;margin:26px 0 0;max-width:480px;">عکس و فیلم نمونه‌کارها و پارچه‌هایت را در گالری دوختک بگذار، لینکش را برای مشتری بفرست تا ببیند، انتخاب کند و سفارش بدهد — مزونت همیشه باز است.</p>
        <a class="hv59f862" href="#cta" style="display:inline-flex;align-items:center;gap:8px;margin-top:26px;min-height:44px;color:#E76F51;font-weight:700;font-size:16px;">
          گالری نمونه را ببین
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="transform:scaleX(-1);"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
        </a>
      </div>

      <!-- board -->
      <div id="galBoard" style="flex:1 1 440px;min-width:290px;position:relative;padding-bottom:30px;">
        <div id="galGrid" style="display:grid;grid-template-columns:1.35fr 1fr;gap:16px;align-items:start;">
          <!-- SCREENSHOT-5: نمای گالری در اپ -->
          <div class="hvcb9227" style="background:#fff;border:1px solid #E8E6E1;border-radius:12px;box-shadow:0 2px 6px rgba(31,42,68,.07),0 20px 40px -22px rgba(31,42,68,.32);transform:rotate(-1deg);overflow:hidden;transition:transform .25s ease,box-shadow .25s ease;">
            <div style="display:flex;align-items:center;justify-content:space-between;padding:10px 12px;border-bottom:1px solid #F0EDE6;">
              <span style="font-size:12px;font-weight:700;color:#1F2A44;">گالری نمونه‌کار</span>
              <span style="font-size:10px;color:#D45A3D;background:#FCEFEA;border-radius:999px;padding:2px 9px;font-weight:700;">لینک مشتری</span>
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:6px;padding:10px;">
              <div style="aspect-ratio:3/4;border-radius:7px;background:linear-gradient(150deg,#F6E0D6,#EEC9B9);display:flex;align-items:center;justify-content:center;"><span style="font-size:10px;color:#8a5f4f;background:rgba(255,255,255,.72);border-radius:999px;padding:3px 9px;">عکس نمونه‌کار ۱</span></div>
              <div style="aspect-ratio:3/4;border-radius:7px;background:linear-gradient(150deg,#3B4869,#2A3550);display:flex;align-items:center;justify-content:center;"><span style="font-size:10px;color:#dfe4f0;background:rgba(31,42,68,.5);border-radius:999px;padding:3px 9px;">عکس نمونه‌کار ۲</span></div>
              <div style="aspect-ratio:3/4;border-radius:7px;background:linear-gradient(150deg,#E9EDE2,#D5DCC9);display:flex;align-items:center;justify-content:center;"><span style="font-size:10px;color:#5d6650;background:rgba(255,255,255,.72);border-radius:999px;padding:3px 9px;">عکس نمونه‌کار ۳</span></div>
              <div style="aspect-ratio:3/4;border-radius:7px;background:linear-gradient(150deg,#F0CDBD,#E5B2A0);display:flex;align-items:center;justify-content:center;"><span style="font-size:10px;color:#7d4f3f;background:rgba(255,255,255,.72);border-radius:999px;padding:3px 9px;">عکس نمونه‌کار ۴</span></div>
            </div>
          </div>
          <div style="display:flex;flex-direction:column;gap:16px;">
            <div class="hv43a5ee" style="background:#fff;border:1px solid #E8E6E1;border-radius:12px;padding:8px;box-shadow:0 2px 5px rgba(31,42,68,.06),0 16px 30px -20px rgba(31,42,68,.3);transform:rotate(1.5deg);transition:transform .25s ease,box-shadow .25s ease;">
              <div style="height:86px;border-radius:8px;background:linear-gradient(140deg,#E88B70,#D9603F);display:flex;align-items:center;justify-content:center;"><span style="font-size:10px;color:#fff;background:rgba(31,42,68,.28);border-radius:999px;padding:3px 9px;">نمونه پارچه مخمل</span></div>
            </div>
            <div class="hv62c49e" style="background:#fff;border:1px solid #E8E6E1;border-radius:12px;padding:8px;box-shadow:0 2px 5px rgba(31,42,68,.06),0 16px 30px -20px rgba(31,42,68,.3);transform:rotate(-1.5deg);transition:transform .25s ease,box-shadow .25s ease;">
              <div style="height:86px;border-radius:8px;background:linear-gradient(140deg,#8C9BC0,#5E6E96);display:flex;align-items:center;justify-content:center;"><span style="font-size:10px;color:#fff;background:rgba(31,42,68,.32);border-radius:999px;padding:3px 9px;">فیلم نمونه‌کار</span></div>
            </div>
            <div class="hveb1e69" style="background:#fff;border:1px solid #E8E6E1;border-radius:12px;padding:8px;box-shadow:0 2px 5px rgba(31,42,68,.06),0 16px 30px -20px rgba(31,42,68,.3);transform:rotate(1deg);transition:transform .25s ease,box-shadow .25s ease;">
              <div style="height:60px;border-radius:8px;background:linear-gradient(140deg,#EFE6D8,#E2D4BE);display:flex;align-items:center;justify-content:center;"><span style="font-size:10px;color:#7d6b4f;background:rgba(255,255,255,.72);border-radius:999px;padding:3px 9px;">نمونه پارچه کتان</span></div>
            </div>
          </div>
        </div>

        <!-- stitch connector board → phone (desktop) -->
        <div data-desktop-only="block" style="display:none;position:absolute;bottom:64px;inset-inline-start:168px;z-index:6;">
          <svg width="110" height="64" viewBox="0 0 110 64" fill="none"><path d="M104 6 Q56 18 16 52" stroke="#E76F51" stroke-width="2" stroke-dasharray="8 6" stroke-linecap="round"/><path d="M16 52 26 46 M16 52 25 56" stroke="#E76F51" stroke-width="2" stroke-linecap="round"/></svg>
          <div style="font-size:11.5px;font-weight:700;color:#D45A3D;margin-top:2px;text-align:center;">مشتری در گوشی‌اش می‌بیند</div>
        </div>

        <!-- phone: customer view -->
        <div id="galPhone" style="position:absolute;bottom:-36px;inset-inline-start:-8px;width:178px;background:#1F2A44;border-radius:26px;padding:8px;border:1px solid #E8E6E1;box-shadow:0 2px 6px rgba(31,42,68,.14),0 22px 46px -16px rgba(31,42,68,.45);transform:rotate(2deg);z-index:5;">
          <div style="background:#fff;border-radius:20px;overflow:hidden;">
            <div style="height:24px;background:#1F2A44;display:flex;justify-content:center;align-items:flex-start;"><span style="width:48px;height:5px;background:#3D4A6B;border-radius:0 0 6px 6px;"></span></div>
            <div style="padding:11px;">
              <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:8px;">
                <span style="font-size:11px;font-weight:700;color:#1F2A44;">مزون مریم — نمونه‌کارها</span>
              </div>
              <div style="display:grid;grid-template-columns:1fr 1fr;gap:5px;">
                <div style="aspect-ratio:3/4;border-radius:6px;background:linear-gradient(150deg,#F6E0D6,#EEC9B9);"></div>
                <div style="aspect-ratio:3/4;border-radius:6px;background:linear-gradient(150deg,#3B4869,#2A3550);"></div>
                <div style="aspect-ratio:3/4;border-radius:6px;background:linear-gradient(150deg,#E9EDE2,#D5DCC9);"></div>
                <div style="aspect-ratio:3/4;border-radius:6px;background:linear-gradient(150deg,#F0CDBD,#E5B2A0);"></div>
              </div>
              <div style="display:flex;align-items:center;justify-content:center;gap:6px;background:#E76F51;color:#fff;border-radius:9px;padding:7px;margin-top:8px;font-size:10.5px;font-weight:700;">انتخاب و ثبت سفارش</div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- TOOLBOX -->
  <section data-screen-label="جعبه خرج‌کار">
    <div data-rv="y" style="opacity:0;transform:translateY(18px);transition:opacity .5s ease,transform .5s ease;max-width:1180px;margin-inline:auto;padding:16px 24px 60px;">
      <div style="background:#fff;border:1px solid #E8E6E1;border-radius:20px;padding:10px;box-shadow:0 2px 6px rgba(31,42,68,.05),0 18px 44px -28px rgba(31,42,68,.4);">
        <div id="toolboxGrid" style="display:grid;grid-template-columns:repeat(4,1fr);">
          <div class="hv03017a" data-toolbox-item style="position:relative;display:flex;align-items:center;gap:14px;padding:22px 20px;border-radius:14px;transition:background .25s ease;">
            <span style="position:absolute;top:18%;bottom:18%;inset-inline-end:0;width:2px;background-image:repeating-linear-gradient(180deg,#E7DFD3 0 7px,transparent 7px 13px);"></span>
            <span style="flex:none;display:inline-flex;width:46px;height:46px;align-items:center;justify-content:center;background:#FCEFEA;border-radius:14px;color:#E76F51;"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 2a14.5 14.5 0 0 0 0 20 14.5 14.5 0 0 0 0-20"/><path d="M2 12h20"/></svg></span>
            <div>
              <div style="font-size:15.5px;font-weight:700;color:#1F2A44;">دامنه اختصاصی</div>
              <div style="font-size:13.5px;line-height:1.8;color:#6B7280;margin-top:5px;">دوختک روی آدرس اینترنتی خودت</div>
            </div>
          </div>
          <div class="hv03017a" data-toolbox-item style="position:relative;display:flex;align-items:center;gap:14px;padding:22px 20px;border-radius:14px;transition:background .25s ease;">
            <span style="position:absolute;top:18%;bottom:18%;inset-inline-end:0;width:2px;background-image:repeating-linear-gradient(180deg,#E7DFD3 0 7px,transparent 7px 13px);"></span>
            <span style="flex:none;display:inline-flex;width:46px;height:46px;align-items:center;justify-content:center;background:#FCEFEA;border-radius:14px;color:#E76F51;"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8V6a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2v7a2 2 0 0 0 2 2h8"/><path d="M7 19h5"/><rect width="6" height="10" x="16" y="12" rx="2"/></svg></span>
            <div>
              <div style="font-size:15.5px;font-weight:700;color:#1F2A44;">همه‌جا در دسترس</div>
              <div style="font-size:13.5px;line-height:1.8;color:#6B7280;margin-top:5px;">موبایل، تبلت و کامپیوتر</div>
            </div>
          </div>
          <div class="hv03017a" data-toolbox-item style="position:relative;display:flex;align-items:center;gap:14px;padding:22px 20px;border-radius:14px;transition:background .25s ease;">
            <span style="position:absolute;top:18%;bottom:18%;inset-inline-end:0;width:2px;background-image:repeating-linear-gradient(180deg,#E7DFD3 0 7px,transparent 7px 13px);"></span>
            <span style="flex:none;display:inline-flex;width:46px;height:46px;align-items:center;justify-content:center;background:#FCEFEA;border-radius:14px;color:#E76F51;"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17.5 19H9a7 7 0 1 1 6.71-9h1.79a4.5 4.5 0 1 1 0 9Z"/></svg></span>
            <div>
              <div style="font-size:15.5px;font-weight:700;color:#1F2A44;">همیشه به‌روز</div>
              <div style="font-size:13.5px;line-height:1.8;color:#6B7280;margin-top:5px;">قابلیت‌های جدید خودکار، بدون نصب</div>
            </div>
          </div>
          <div class="hv03017a" data-toolbox-item style="position:relative;display:flex;align-items:center;gap:14px;padding:22px 20px;border-radius:14px;transition:background .25s ease;">
            <span style="flex:none;display:inline-flex;width:46px;height:46px;align-items:center;justify-content:center;background:#FCEFEA;border-radius:14px;color:#E76F51;"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="m4.93 4.93 4.24 4.24"/><path d="m14.83 9.17 4.24-4.24"/><path d="m14.83 14.83 4.24 4.24"/><path d="m9.17 14.83-4.24 4.24"/><circle cx="12" cy="12" r="4"/></svg></span>
            <div>
              <div style="font-size:15.5px;font-weight:700;color:#1F2A44;">پشتیبانی و آموزش فارسی</div>
              <div style="font-size:13.5px;line-height:1.8;color:#6B7280;margin-top:5px;">کنارت هستیم تا راه بیفتی</div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- RECIPE STEPS -->
  <section data-screen-label="دستور دوخت">
    <div data-rv="y" style="opacity:0;transform:translateY(18px);transition:opacity .5s ease,transform .5s ease;max-width:1000px;margin-inline:auto;padding:20px 24px 66px;text-align:center;">
      <span style="font-size:13px;font-weight:700;color:#D45A3D;letter-spacing:1px;">دستور دوخت</span>
      <h2 data-h2 style="font-size:38px;font-weight:700;color:#1F2A44;margin:12px 0 40px;">در سه قدم شروع کن</h2>
      <div style="display:flex;flex-wrap:wrap;justify-content:center;align-items:flex-start;gap:12px;">
        <div style="flex:1 1 240px;max-width:280px;">
          <div style="width:60px;height:60px;margin:0 auto 18px;border-radius:50%;background:#fff;border:2px solid #E76F51;display:flex;align-items:center;justify-content:center;font-size:23px;font-weight:700;color:#1F2A44;">۱</div>
          <div style="font-size:18px;font-weight:700;color:#1F2A44;margin-bottom:6px;">ثبت‌نام کن</div>
          <div style="font-size:14.5px;color:#6B7280;">کمتر از ۲ دقیقه و بدون کارت بانکی.</div>
        </div>
        <div data-desktop-only="block" style="display:none;flex:none;width:70px;margin-top:29px;height:2px;background-image:repeating-linear-gradient(90deg,#E76F51 0 8px,transparent 8px 14px);"></div>
        <div style="flex:1 1 240px;max-width:280px;">
          <div style="width:60px;height:60px;margin:0 auto 18px;border-radius:50%;background:#fff;border:2px solid #E76F51;display:flex;align-items:center;justify-content:center;font-size:23px;font-weight:700;color:#1F2A44;">۲</div>
          <div style="font-size:18px;font-weight:700;color:#1F2A44;margin-bottom:6px;">مشتری‌ها و سفارش‌ها را ثبت کن</div>
          <div style="font-size:14.5px;color:#6B7280;">اطلاعات مزونت را وارد کن.</div>
        </div>
        <div data-desktop-only="block" style="display:none;flex:none;width:70px;margin-top:29px;height:2px;background-image:repeating-linear-gradient(90deg,#E76F51 0 8px,transparent 8px 14px);"></div>
        <div style="flex:1 1 240px;max-width:280px;">
          <div style="width:60px;height:60px;margin:0 auto 18px;border-radius:50%;background:#E76F51;border:2px solid #E76F51;display:flex;align-items:center;justify-content:center;font-size:23px;font-weight:700;color:#fff;">۳</div>
          <div style="font-size:18px;font-weight:700;color:#1F2A44;margin-bottom:6px;">بقیه‌اش با دوختک</div>
          <div style="font-size:14.5px;color:#6B7280;">یادآوری، پیامک، پرداخت و حساب خودکار مدیریت می‌شود.</div>
        </div>
      </div>
    </div>
  </section>

  <!-- DARK: before/after -->
  <section data-screen-label="اتاق پرو" style="background-color:#1F2A44;background-image:linear-gradient(rgba(255,255,255,.03) 1px,transparent 1px),linear-gradient(90deg,rgba(255,255,255,.03) 1px,transparent 1px);background-size:46px 46px;">
    <div style="max-width:1000px;margin-inline:auto;padding:70px 24px;">
      <div data-rv="y" style="opacity:0;transform:translateY(18px);transition:opacity .5s ease,transform .5s ease;text-align:center;margin-bottom:40px;">
        <span style="font-size:13px;font-weight:700;color:#E76F51;letter-spacing:1px;">اتاق پرو: قبل و بعد</span>
        <h2 data-h2 style="font-size:38px;font-weight:700;color:#fff;margin:12px 0 0;">از دفتر و دفترچه، به دوختک</h2>
      </div>
      <div style="display:flex;flex-direction:column;gap:14px;">
        <div data-desktop-only="flex" style="display:none;gap:12px;padding-inline:6px;">
          <div style="flex:1;font-size:13px;font-weight:700;color:#8b95af;letter-spacing:.5px;">بدون دوختک</div>
          <div style="flex:1;font-size:13px;font-weight:700;color:#E76F51;letter-spacing:.5px;">با دوختک</div>
        </div>
        <?php foreach ($compare as $i => $row): ?>
          <div style="display:flex;flex-wrap:wrap;gap:12px;">
            <div data-rv="x1" data-rv-delay="<?= $i * 60 ?>" style="opacity:0;transform:translateX(-16px);transition:opacity .55s ease,transform .55s ease;flex:1 1 260px;display:flex;gap:11px;align-items:flex-start;background:rgba(255,255,255,.03);border:1px solid rgba(255,255,255,.08);border-radius:12px;padding:16px 18px;">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#6B7280" stroke-width="2.2" stroke-linecap="round" style="flex:none;margin-top:2px;"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
              <span style="font-size:15px;color:#b7bed0;"><?= e($row['before']) ?></span>
            </div>
            <div data-rv="x2" data-rv-delay="<?= $i * 60 + 90 ?>" style="opacity:0;transform:translateX(16px);transition:opacity .55s ease,transform .55s ease;flex:1 1 260px;display:flex;gap:11px;align-items:flex-start;background:rgba(231,111,81,.1);border:1px solid rgba(231,111,81,.28);border-radius:12px;padding:16px 18px;">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#E76F51" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" style="flex:none;margin-top:2px;"><path d="M20 6 9 17l-5-5"/></svg>
              <span style="font-size:15px;color:#fff;"><?= e($row['after']) ?></span>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <!-- TESTIMONIALS -->
  <section data-screen-label="نظر مشتریان">
    <div data-rv="y" style="opacity:0;transform:translateY(18px);transition:opacity .5s ease,transform .5s ease;max-width:1180px;margin-inline:auto;padding:74px 24px 60px;text-align:center;">
      <span style="font-size:13px;font-weight:700;color:#D45A3D;letter-spacing:1px;">اتیکت‌های دوختک</span>
      <h2 data-h2 style="font-size:38px;font-weight:700;color:#1F2A44;margin:12px 0 42px;">خیاط‌ها چه می‌گویند</h2>
      <div id="testiRow" style="display:flex;flex-wrap:wrap;justify-content:center;gap:26px;">
        <?php foreach ($testimonials as $t): ?>
          <div data-testi style="flex:0 1 310px;background:#fff;border:1px solid #E8E6E1;border-radius:14px;padding:26px 24px;box-shadow:0 2px 6px rgba(31,42,68,.05),0 18px 38px -26px rgba(31,42,68,.4);text-align:start;transform:rotate(<?= e($t['rot']) ?>);">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="#FCEFEA" style="margin-bottom:6px;"><path d="M10 7 7 12v5h5v-5H9l1.5-3zM19 7l-3 5v5h5v-5h-3l1.5-3z"/></svg>
            <p style="font-size:15.5px;line-height:1.9;color:#3D4A6B;margin:0 0 18px;"><?= e($t['quote']) ?></p>
            <div style="font-size:14.5px;font-weight:700;color:#1F2A44;"><?= e($t['name']) ?></div>
            <div style="font-size:12.5px;color:#6B7280;margin-top:2px;"><?= e($t['meta']) ?></div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <!-- BLOG -->
  <section id="blog" data-screen-label="مجله دوختک" style="scroll-margin-top:96px;">
    <div style="max-width:1180px;margin-inline:auto;padding:20px 24px 70px;">
      <div data-rv="y" style="opacity:0;transform:translateY(18px);transition:opacity .5s ease,transform .5s ease;text-align:center;margin-bottom:40px;">
        <h2 data-h2 style="font-size:38px;font-weight:700;color:#1F2A44;margin:0;">
          <span style="position:relative;white-space:nowrap;">مجله دوختک<svg width="100%" height="14" viewBox="0 0 140 14" preserveAspectRatio="none" style="position:absolute;inset-inline-start:0;bottom:-6px;overflow:visible;"><path d="M3 8 Q40 2 72 7 T137 6" stroke="#E76F51" stroke-width="6" fill="none" stroke-linecap="round" opacity=".85"/></svg></span>
        </h2>
        <p style="font-size:16.5px;color:#6B7280;margin:20px 0 0;">نکته‌ها و ترفندهای مدیریت خیاطی و مزون‌داری</p>
      </div>
      <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(270px,1fr));gap:22px;">
        <div data-rv="y" data-rv-delay="0" style="opacity:0;transform:translateY(18px);transition:opacity .5s ease,transform .5s ease;">
          <a class="hv04e05c" href="/blog" style="position:relative;display:flex;flex-direction:column;height:100%;background:#fff;border:1px solid #E8E6E1;border-radius:20px;overflow:hidden;box-shadow:0 2px 5px rgba(31,42,68,.05),0 16px 40px -28px rgba(31,42,68,.45);transition:transform .3s cubic-bezier(.2,.7,.3,1),box-shadow .3s ease,border-color .3s ease;">
            <div style="position:relative;height:168px;background:linear-gradient(150deg,#FCEFEA,#F0C9B8);overflow:hidden;">
              <span style="position:absolute;inset:0;background-image:repeating-linear-gradient(-45deg,rgba(255,255,255,.14) 0 2px,transparent 2px 14px);"></span>
              <span style="position:absolute;bottom:-26px;inset-inline-start:-26px;width:120px;height:120px;border-radius:50%;background:rgba(255,255,255,.22);"></span>
              <span style="position:absolute;bottom:14px;inset-inline-end:14px;background:rgba(255,255,255,.94);color:#D45A3D;font-size:11.5px;font-weight:700;padding:5px 13px;border-radius:999px;box-shadow:0 4px 12px rgba(31,42,68,.14);">قیمت‌گذاری</span>
            </div>
            <div style="display:flex;flex-direction:column;flex:1;padding:22px;text-align:start;">
              <h3 style="font-size:17.5px;font-weight:700;color:#1F2A44;margin:0 0 12px;line-height:1.75;">۷ اشتباه رایج در قیمت‌گذاری دوخت سفارشی</h3>
              <div style="display:flex;align-items:center;gap:8px;font-size:12.5px;color:#6B7280;">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="4" rx="2"/><path d="M16 2v4"/><path d="M8 2v4"/><path d="M3 10h18"/></svg>
                ۲ تیر ۱۴۰۵
                <span style="width:3px;height:3px;border-radius:50%;background:#c9c3b6;"></span>
                ۵ دقیقه مطالعه
              </div>
              <div style="display:flex;align-items:center;justify-content:space-between;margin-top:auto;padding-top:16px;border-top:2px dashed #F0EBE1;margin-top:16px;">
                <span style="color:#E76F51;font-weight:700;font-size:14px;">ادامه مطلب</span>
                <span style="display:inline-flex;width:32px;height:32px;align-items:center;justify-content:center;background:#FCEFEA;border-radius:50%;color:#E76F51;"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="transform:scaleX(-1);"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg></span>
              </div>
            </div>
          </a>
        </div>
        <div data-rv="y" data-rv-delay="70" style="opacity:0;transform:translateY(18px);transition:opacity .5s ease,transform .5s ease;">
          <a class="hv04e05c" href="/blog" style="position:relative;display:flex;flex-direction:column;height:100%;background:#fff;border:1px solid #E8E6E1;border-radius:20px;overflow:hidden;box-shadow:0 2px 5px rgba(31,42,68,.05),0 16px 40px -28px rgba(31,42,68,.45);transition:transform .3s cubic-bezier(.2,.7,.3,1),box-shadow .3s ease,border-color .3s ease;">
            <div style="position:relative;height:168px;background:linear-gradient(150deg,#EEF1F7,#CBD4E6);overflow:hidden;">
              <span style="position:absolute;inset:0;background-image:repeating-linear-gradient(-45deg,rgba(255,255,255,.14) 0 2px,transparent 2px 14px);"></span>
              <span style="position:absolute;bottom:-26px;inset-inline-start:-26px;width:120px;height:120px;border-radius:50%;background:rgba(255,255,255,.22);"></span>
              <span style="position:absolute;bottom:14px;inset-inline-end:14px;background:rgba(255,255,255,.94);color:#D45A3D;font-size:11.5px;font-weight:700;padding:5px 13px;border-radius:999px;box-shadow:0 4px 12px rgba(31,42,68,.14);">مشتری‌مداری</span>
            </div>
            <div style="display:flex;flex-direction:column;flex:1;padding:22px;text-align:start;">
              <h3 style="font-size:17.5px;font-weight:700;color:#1F2A44;margin:0 0 12px;line-height:1.75;">چطور مشتری ثابت بسازیم؟</h3>
              <div style="display:flex;align-items:center;gap:8px;font-size:12.5px;color:#6B7280;">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="4" rx="2"/><path d="M16 2v4"/><path d="M8 2v4"/><path d="M3 10h18"/></svg>
                ۲۸ خرداد ۱۴۰۵
                <span style="width:3px;height:3px;border-radius:50%;background:#c9c3b6;"></span>
                ۴ دقیقه مطالعه
              </div>
              <div style="display:flex;align-items:center;justify-content:space-between;margin-top:auto;padding-top:16px;border-top:2px dashed #F0EBE1;margin-top:16px;">
                <span style="color:#E76F51;font-weight:700;font-size:14px;">ادامه مطلب</span>
                <span style="display:inline-flex;width:32px;height:32px;align-items:center;justify-content:center;background:#FCEFEA;border-radius:50%;color:#E76F51;"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="transform:scaleX(-1);"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg></span>
              </div>
            </div>
          </a>
        </div>
        <div data-rv="y" data-rv-delay="140" style="opacity:0;transform:translateY(18px);transition:opacity .5s ease,transform .5s ease;">
          <a class="hv04e05c" href="/blog" style="position:relative;display:flex;flex-direction:column;height:100%;background:#fff;border:1px solid #E8E6E1;border-radius:20px;overflow:hidden;box-shadow:0 2px 5px rgba(31,42,68,.05),0 16px 40px -28px rgba(31,42,68,.45);transition:transform .3s cubic-bezier(.2,.7,.3,1),box-shadow .3s ease,border-color .3s ease;">
            <div style="position:relative;height:168px;background:linear-gradient(150deg,#EFEBE2,#DCD2BC);overflow:hidden;">
              <span style="position:absolute;inset:0;background-image:repeating-linear-gradient(-45deg,rgba(255,255,255,.14) 0 2px,transparent 2px 14px);"></span>
              <span style="position:absolute;bottom:-26px;inset-inline-start:-26px;width:120px;height:120px;border-radius:50%;background:rgba(255,255,255,.22);"></span>
              <span style="position:absolute;bottom:14px;inset-inline-end:14px;background:rgba(255,255,255,.94);color:#D45A3D;font-size:11.5px;font-weight:700;padding:5px 13px;border-radius:999px;box-shadow:0 4px 12px rgba(31,42,68,.14);">عکاسی</span>
            </div>
            <div style="display:flex;flex-direction:column;flex:1;padding:22px;text-align:start;">
              <h3 style="font-size:17.5px;font-weight:700;color:#1F2A44;margin:0 0 12px;line-height:1.75;">راهنمای عکاسی از نمونه‌کار با گوشی</h3>
              <div style="display:flex;align-items:center;gap:8px;font-size:12.5px;color:#6B7280;">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="4" rx="2"/><path d="M16 2v4"/><path d="M8 2v4"/><path d="M3 10h18"/></svg>
                ۱۵ خرداد ۱۴۰۵
                <span style="width:3px;height:3px;border-radius:50%;background:#c9c3b6;"></span>
                ۶ دقیقه مطالعه
              </div>
              <div style="display:flex;align-items:center;justify-content:space-between;margin-top:auto;padding-top:16px;border-top:2px dashed #F0EBE1;margin-top:16px;">
                <span style="color:#E76F51;font-weight:700;font-size:14px;">ادامه مطلب</span>
                <span style="display:inline-flex;width:32px;height:32px;align-items:center;justify-content:center;background:#FCEFEA;border-radius:50%;color:#E76F51;"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="transform:scaleX(-1);"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg></span>
              </div>
            </div>
          </a>
        </div>
      </div>
      <div data-rv="y" style="opacity:0;transform:translateY(18px);transition:opacity .5s ease,transform .5s ease;text-align:center;margin-top:32px;">
        <a class="hvfa4bd4" href="/blog" style="display:inline-flex;align-items:center;justify-content:center;min-height:48px;color:#1F2A44;font-weight:700;font-size:15.5px;padding:12px 30px;border-radius:12px;border:1.5px solid #1F2A44;transition:background .2s ease,color .2s ease;">همه مقاله‌ها</a>
      </div>
    </div>
  </section>

  <!-- FAQ -->
  <section id="faq" data-screen-label="سؤالات متداول" style="scroll-margin-top:96px;">
    <div data-rv="y" style="opacity:0;transform:translateY(18px);transition:opacity .5s ease,transform .5s ease;max-width:760px;margin-inline:auto;padding:10px 24px 70px;">
      <h2 data-h2 style="font-size:38px;font-weight:700;color:#1F2A44;text-align:center;margin:0 0 36px;">سؤالات متداول</h2>
      <div id="homeFaq">
      <?php foreach ($faqs as $i => $f): $open = $i === 0; // first item open, like the design ?>
        <div data-faq-item style="border-bottom:2px dashed #E7DFD3;">
          <button data-faq-btn style="width:100%;min-height:52px;background:none;border:none;display:flex;align-items:center;justify-content:space-between;gap:16px;padding:19px 4px;text-align:start;font-size:16.5px;font-weight:700;color:#1F2A44;">
            <span><?= e($f['q']) ?></span>
            <span data-faq-icon style="flex:none;color:#E76F51;font-size:24px;line-height:1;display:inline-block;transform:rotate(<?= $open ? '45deg' : '0deg' ?>);transition:transform .3s ease;">+</span>
          </button>
          <div data-faq-panel style="display:grid;grid-template-rows:<?= $open ? '1fr' : '0fr' ?>;transition:grid-template-rows .35s ease;">
            <div style="overflow:hidden;">
              <div style="padding:0 4px 20px;font-size:15px;line-height:1.9;color:#3D4A6B;"><?= e($f['a']) ?></div>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
      </div>
    </div>
  </section>

  <!-- FINAL CTA -->
  <section id="cta" data-screen-label="سی‌تی‌ای نهایی" style="scroll-margin-top:96px;">
    <div data-rv="y" style="opacity:0;transform:translateY(18px);transition:opacity .5s ease,transform .5s ease;max-width:1000px;margin-inline:auto;padding:0 24px 74px;">
      <div style="position:relative;background:#FCEFEA;border-radius:24px;padding:58px 28px;text-align:center;overflow:hidden;">
        <div style="position:absolute;inset:14px;border:2px dashed #E76F51;border-radius:16px;opacity:.55;pointer-events:none;"></div>
        <h2 data-h2 style="position:relative;font-size:38px;font-weight:700;color:#1F2A44;margin:0 0 14px;">۱۰ روز رایگان امتحان کن</h2>
        <p style="position:relative;font-size:17px;color:#3D4A6B;margin:0 0 28px;">بدون نیاز به کارت بانکی؛ همین امروز مزونت را روی دوختک بیاور.</p>
        <div style="position:relative;display:flex;flex-wrap:wrap;justify-content:center;align-items:center;gap:18px;">
          <a class="hv009fd9" href="<?= e($app_url) ?>" style="display:inline-flex;align-items:center;justify-content:center;min-height:54px;background:#E76F51;color:#fff;font-weight:700;font-size:18px;padding:15px 36px;border-radius:14px;box-shadow:0 10px 26px rgba(231,111,81,.34);transition:background .2s ease,transform .2s ease,box-shadow .2s ease;">شروع رایگان</a>
          <a class="hvd6c76f" href="/pricing" style="display:inline-flex;align-items:center;min-height:44px;color:#1F2A44;font-weight:700;font-size:16px;">مشاهده تعرفه‌ها</a>
        </div>
      </div>
    </div>
  </section>

<div id="footer" style="scroll-margin-top:96px;"><?php include __DIR__ . '/includes/site_footer.php'; ?></div>

</div>
<?php render_foot();
