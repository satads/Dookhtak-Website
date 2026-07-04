<?php
/**
 * Features page — the five-chapter "journey" through everything Dookhtak
 * does, with a stitched seam, station markers and a sticky chapter spy.
 * Static content mirrors the approved design 1:1; DB wiring comes in
 * later phases.
 */
require_once __DIR__ . '/includes/layout.php';
public_boot(); // redirect middleware + page cache

// Checklist — mirrors the design's checklistData (18 items).
$checklist = [
    'پرونده مشتری و اندازه‌های اندام',
    'سوابق سفارش‌های هر مشتری',
    'باشگاه مشتریان و پیامک انبوه',
    'پیامک تبریک تولد خودکار',
    'مدیریت کامل سفارش‌ها',
    'مراحل دوخت و سررسید تحویل',
    'پیامک خودکار وضعیت سفارش',
    'ارجاع سفارش به همکار',
    'گالری نمونه‌کار با عکس و فیلم',
    'لینک اختصاصی گالری برای مشتری',
    'لینک پرداخت آنلاین',
    'حسابداری درآمد و هزینه',
    'یادآوری چک‌های دریافتی و پرداختی',
    'گزارش‌های مالی روشن',
    'دسترسی از موبایل، تبلت و کامپیوتر',
    'دامنه اختصاصی',
    'به‌روزرسانی خودکار',
    'پشتیبانی و آموزش فارسی',
];

$app_url = setting('app_url', 'https://app.dookhtak.ir');

render_head([
    'page_key' => 'features',
    'css' => '/assets/css/page-features.css',
    'js' => '/assets/js/page-features.js',
]);
?>
<div dir="rtl" lang="fa" style="position:relative;min-height:100vh;background-color:#FAF8F4;background-image:linear-gradient(#EDEAE3 1px,transparent 1px),linear-gradient(90deg,#EDEAE3 1px,transparent 1px);background-size:46px 46px;overflow-x:hidden;line-height:1.6;">

<?php include __DIR__ . '/includes/site_header.php'; ?>

  <!-- COMPACT HERO -->
  <section data-screen-label="هیرو امکانات" style="position:relative;">
    <div id="heroWrap" style="max-width:860px;margin-inline:auto;padding:132px 24px 46px;text-align:center;">
      <span data-hero style="opacity:0;transform:translateY(12px);transition:opacity .5s ease,transform .5s ease;display:inline-block;background:#FCEFEA;color:#D45A3D;font-size:13.5px;font-weight:700;padding:7px 15px;border-radius:999px;letter-spacing:.2px;">همه امکانات، یک‌جا</span>
      <h1 id="heroTitle" data-hero style="opacity:0;transform:translateY(12px);transition:opacity .5s ease,transform .5s ease;font-size:46px;line-height:1.45;font-weight:700;color:#1F2A44;margin:20px 0 0;letter-spacing:-.5px;">
        <span style="position:relative;white-space:nowrap;">هر کاری<svg width="100%" height="14" viewBox="0 0 140 14" preserveAspectRatio="none" style="position:absolute;inset-inline-start:0;bottom:-6px;overflow:visible;"><path d="M3 8 Q40 2 72 7 T137 6" stroke="#E76F51" stroke-width="6" fill="none" stroke-linecap="round" opacity=".85"/></svg></span>
        که مزونت لازم دارد
      </h1>
      <p data-hero style="opacity:0;transform:translateY(12px);transition:opacity .5s ease,transform .5s ease;font-size:17.5px;line-height:1.9;color:#3D4A6B;margin:26px auto 0;max-width:520px;">از اولین سلامِ مشتری تا تسویه آخر — دوختک قدم‌به‌قدم کنارت است.</p>
      <div id="heroCtas" data-hero style="opacity:0;transform:translateY(12px);transition:opacity .5s ease,transform .5s ease;display:flex;flex-wrap:wrap;justify-content:center;align-items:center;gap:16px;margin-top:30px;">
        <a class="hv6ef100" href="#cta" style="display:inline-flex;align-items:center;justify-content:center;white-space:nowrap;min-height:52px;background:#E76F51;color:#fff;font-weight:700;font-size:17px;padding:14px 28px;border-radius:13px;box-shadow:0 8px 22px rgba(231,111,81,.3);transition:background .2s ease,transform .2s ease,box-shadow .2s ease;">۱۰ روز رایگان شروع کن</a>
        <a class="hvd6c76f" href="/pricing" style="display:inline-flex;align-items:center;min-height:44px;color:#1F2A44;font-weight:700;font-size:16px;">مشاهده تعرفه‌ها</a>
      </div>
    </div>
  </section>

  <!-- STICKY CHAPTER CHIPS -->
  <div id="chipBar" style="position:sticky;top:78px;z-index:50;padding:8px 14px;">
    <div id="chipRow" style="display:flex;justify-content:center;gap:8px;max-width:720px;margin-inline:auto;background:rgba(255,255,255,.85);backdrop-filter:blur(8px);-webkit-backdrop-filter:blur(8px);border:1px solid #E8E6E1;border-radius:999px;padding:6px;overflow-x:auto;scrollbar-width:none;">
      <a data-chip="ch1" href="#ch1" style="flex:none;display:inline-flex;align-items:center;min-height:38px;padding:7px 16px;border-radius:999px;font-size:13.5px;font-weight:700;color:#3D4A6B;transition:background .25s ease,color .25s ease;scroll-snap-align:center;">مشتری‌ها</a>
      <a data-chip="ch2" href="#ch2" style="flex:none;display:inline-flex;align-items:center;min-height:38px;padding:7px 16px;border-radius:999px;font-size:13.5px;font-weight:700;color:#3D4A6B;transition:background .25s ease,color .25s ease;scroll-snap-align:center;">سفارش‌ها</a>
      <a data-chip="ch3" href="#ch3" style="flex:none;display:inline-flex;align-items:center;min-height:38px;padding:7px 16px;border-radius:999px;font-size:13.5px;font-weight:700;color:#3D4A6B;transition:background .25s ease,color .25s ease;scroll-snap-align:center;">ویترین</a>
      <a data-chip="ch4" href="#ch4" style="flex:none;display:inline-flex;align-items:center;min-height:38px;padding:7px 16px;border-radius:999px;font-size:13.5px;font-weight:700;color:#3D4A6B;transition:background .25s ease,color .25s ease;scroll-snap-align:center;">پول و حساب</a>
      <a data-chip="ch5" href="#ch5" style="flex:none;display:inline-flex;align-items:center;min-height:38px;padding:7px 16px;border-radius:999px;font-size:13.5px;font-weight:700;color:#3D4A6B;transition:background .25s ease,color .25s ease;scroll-snap-align:center;">زیرساخت</a>
    </div>
  </div>

  <!-- JOURNEY -->
  <div id="journey" style="position:relative;max-width:1180px;margin-inline:auto;padding:30px 24px 10px;">
    <div id="seam" style="position:absolute;top:0;bottom:0;inset-inline-start:57%;width:2px;background-image:repeating-linear-gradient(180deg,#E0DACC 0 10px,transparent 10px 18px);">
      <div id="seamFill" style="position:absolute;top:0;inset-inline-start:0;width:2px;height:0%;background-image:repeating-linear-gradient(180deg,#E76F51 0 10px,transparent 10px 18px);"></div>
    </div>
    <div data-station data-for="ch1" style="position:absolute;z-index:5;width:44px;height:44px;border-radius:50%;background:#fff;border:2px solid #E76F51;display:flex;align-items:center;justify-content:center;font-size:18px;font-weight:700;color:#1F2A44;transition:background .4s ease,color .4s ease,transform .4s ease;box-shadow:0 4px 12px rgba(31,42,68,.12);">۱</div>
    <div data-station data-for="ch2" style="position:absolute;z-index:5;width:44px;height:44px;border-radius:50%;background:#fff;border:2px solid #E76F51;display:flex;align-items:center;justify-content:center;font-size:18px;font-weight:700;color:#1F2A44;transition:background .4s ease,color .4s ease,transform .4s ease;box-shadow:0 4px 12px rgba(31,42,68,.12);">۲</div>
    <div data-station data-for="ch3" style="position:absolute;z-index:5;width:44px;height:44px;border-radius:50%;background:#fff;border:2px solid #E76F51;display:flex;align-items:center;justify-content:center;font-size:18px;font-weight:700;color:#1F2A44;transition:background .4s ease,color .4s ease,transform .4s ease;box-shadow:0 4px 12px rgba(31,42,68,.12);">۳</div>
    <div data-station data-for="ch4" style="position:absolute;z-index:5;width:44px;height:44px;border-radius:50%;background:#fff;border:2px solid #E76F51;display:flex;align-items:center;justify-content:center;font-size:18px;font-weight:700;color:#1F2A44;transition:background .4s ease,color .4s ease,transform .4s ease;box-shadow:0 4px 12px rgba(31,42,68,.12);">۴</div>
    <div data-station data-for="ch5" style="position:absolute;z-index:5;width:44px;height:44px;border-radius:50%;background:#fff;border:2px solid #E76F51;display:flex;align-items:center;justify-content:center;font-size:18px;font-weight:700;color:#1F2A44;transition:background .4s ease,color .4s ease,transform .4s ease;box-shadow:0 4px 12px rgba(31,42,68,.12);">۵</div>
    <section id="ch1" data-chsec data-screen-label="فصل ۱ مشتری‌ها" style="position:relative;z-index:1;padding:54px 0 34px;scroll-margin-top:150px;">
      <div data-rv style="opacity:0;transform:translateY(16px);transition:opacity .5s ease,transform .5s ease;margin-bottom:26px;max-width:520px;">
        <span style="font-size:13px;font-weight:700;color:#D45A3D;letter-spacing:1px;">فصل ۱ — مشتری‌ها</span>
        <div style="font-size:21px;font-weight:700;color:#1F2A44;margin-top:8px;">همه‌چیز از یک مشتری شروع می‌شود.</div>
      </div>
      <div data-ch data-side="a" data-rv style="opacity:0;transform:translateY(16px);transition:opacity .5s ease,transform .5s ease;display:flex;gap:5%;align-items:center;justify-content:space-between;">
        <div data-col="text">
          <h2 data-h2 style="font-size:36px;font-weight:700;color:#1F2A44;margin:0 0 16px;line-height:1.5;">هر مشتری، یک پرونده کامل</h2>
          <p style="font-size:16.5px;line-height:1.9;color:#3D4A6B;margin:0 0 20px;">اندازه‌های اندام یک‌بار ثبت می‌شود و برای همیشه در دسترس است؛ سلیقه و سوابق سفارش‌های هر مشتری هم کنارش. دفعه بعد که آمد، همه‌چیز جلوی چشمت است.</p>
          <div style="display:flex;flex-direction:column;gap:11px;font-size:15px;color:#3D4A6B;">
            <span style="display:flex;align-items:center;gap:10px;"><svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="#E76F51" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" style="flex:none;"><path d="M20 6 9 17l-5-5"/></svg>فرم کامل اندازه‌های اندام برای هر مشتری</span>
            <span style="display:flex;align-items:center;gap:10px;"><svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="#E76F51" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" style="flex:none;"><path d="M20 6 9 17l-5-5"/></svg>سوابق همه سفارش‌های قبلی در یک نگاه</span>
            <span style="display:flex;align-items:center;gap:10px;"><svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="#E76F51" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" style="flex:none;"><path d="M20 6 9 17l-5-5"/></svg>جستجوی دو ثانیه‌ای بین همه مشتری‌ها</span>
          </div>
        </div>
        <div data-col="vis">
          <!-- SCREENSHOT-F1: پرونده مشتری با فرم اندازه‌ها -->
          <div style="position:relative;background:#fff;border:1px solid #E8E6E1;border-radius:14px;box-shadow:0 2px 6px rgba(31,42,68,.08),0 26px 52px -22px rgba(31,42,68,.32);transform:rotate(-.75deg);overflow:hidden;">
            <div style="display:flex;align-items:center;gap:6px;padding:9px 12px;border-bottom:1px solid #F0EDE6;background:#FBFAF7;">
              <span style="width:9px;height:9px;border-radius:50%;background:#E6E1D7;"></span><span style="width:9px;height:9px;border-radius:50%;background:#E6E1D7;"></span><span style="width:9px;height:9px;border-radius:50%;background:#E6E1D7;"></span>
              <span style="margin-inline-start:auto;font-size:10px;color:#c9c3b6;">SCREENSHOT-F1</span>
            </div>
            <div style="padding:16px;">
              <div style="display:flex;align-items:center;gap:10px;margin-bottom:12px;">
                <span style="width:38px;height:38px;border-radius:50%;background:#1F2A44;color:#fff;display:flex;align-items:center;justify-content:center;font-size:14px;font-weight:700;">م</span>
                <div><div style="font-size:14px;font-weight:700;color:#1F2A44;">مریم احمدی</div><div style="font-size:11px;color:#6B7280;">مشتری از بهار ۱۴۰۳ · ۸ سفارش</div></div>
              </div>
              <div style="font-size:11.5px;font-weight:700;color:#1F2A44;margin-bottom:7px;">اندازه‌های اندام</div>
              <div style="display:flex;flex-wrap:wrap;gap:6px;font-size:11px;margin-bottom:12px;">
                <span style="background:#FAF8F4;border:1px solid #EDEAE3;border-radius:7px;padding:4px 9px;color:#3D4A6B;">قد ۱۶۸</span>
                <span style="background:#FAF8F4;border:1px solid #EDEAE3;border-radius:7px;padding:4px 9px;color:#3D4A6B;">دور سینه ۹۲</span>
                <span style="background:#FAF8F4;border:1px solid #EDEAE3;border-radius:7px;padding:4px 9px;color:#3D4A6B;">دور کمر ۷۴</span>
                <span style="background:#FAF8F4;border:1px solid #EDEAE3;border-radius:7px;padding:4px 9px;color:#3D4A6B;">دور باسن ۱۰۰</span>
                <span style="background:#FAF8F4;border:1px solid #EDEAE3;border-radius:7px;padding:4px 9px;color:#3D4A6B;">قد آستین ۵۸</span>
                <span style="background:#FAF8F4;border:1px solid #EDEAE3;border-radius:7px;padding:4px 9px;color:#3D4A6B;">سرشانه ۳۹</span>
              </div>
              <div style="font-size:11.5px;font-weight:700;color:#1F2A44;margin-bottom:7px;">سوابق سفارش‌ها</div>
              <div style="display:flex;justify-content:space-between;align-items:center;background:#FAF8F4;border:1px solid #EDEAE3;border-radius:9px;padding:8px 10px;font-size:11px;color:#3D4A6B;margin-bottom:5px;"><span style="font-weight:700;color:#1F2A44;">مانتو مجلسی</span><span>۱۴ تیر ۱۴۰۵</span><span style="color:#E76F51;font-weight:700;">در حال دوخت</span></div>
              <div style="display:flex;justify-content:space-between;align-items:center;background:#FAF8F4;border:1px solid #EDEAE3;border-radius:9px;padding:8px 10px;font-size:11px;color:#3D4A6B;"><span style="font-weight:700;color:#1F2A44;">کت و دامن</span><span>۲۰ اسفند ۱۴۰۴</span><span style="color:#1F8A5B;font-weight:700;">تحویل شد</span></div>
            </div>
          </div>
        </div>
      </div>
      <div data-subrow data-rv data-rv-delay="80" style="opacity:0;transform:translateY(16px);transition:opacity .5s ease,transform .5s ease;display:flex;flex-wrap:wrap;gap:18px;margin-top:28px;position:relative;z-index:1;">
        <div class="hv76221a" style="flex:1 1 320px;background:#fff;border:1px solid #E8E6E1;border-radius:16px;padding:22px;box-shadow:0 10px 30px -24px rgba(31,42,68,.4);transition:transform .25s ease,box-shadow .25s ease;">
          <div style="display:flex;align-items:center;gap:11px;margin-bottom:8px;">
            <span style="flex:none;display:inline-flex;width:42px;height:42px;align-items:center;justify-content:center;background:#FCEFEA;border-radius:50%;color:#E76F51;"><svg width="21" height="21" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m3 11 18-5v12L3 14v-3z"/><path d="M11.6 16.8a3 3 0 1 1-5.8-1.6"/></svg></span>
            <h3 style="font-size:17.5px;font-weight:700;color:#1F2A44;margin:0;">باشگاه مشتریان و پیامک انبوه</h3>
          </div>
          <p style="font-size:14.5px;color:#3D4A6B;margin:0 0 14px;">خبر تخفیف و کلکسیون جدید را با یک کلیک به همه مشتری‌ها برسان.</p>
          <div style="background:#FAF8F4;border:1px solid #EDEAE3;border-radius:12px;padding:11px;display:flex;flex-direction:column;gap:7px;">
            <div style="align-self:flex-start;max-width:90%;background:#fff;border:1px solid #EDEAE3;border-radius:11px 11px 11px 4px;padding:7px 11px;font-size:11px;color:#3D4A6B;">پارچه‌های جدید پاییزی رسید — گالری مزون را ببین.</div>
            <div style="align-self:flex-start;max-width:90%;background:#fff;border:1px solid #EDEAE3;border-radius:11px 11px 11px 4px;padding:7px 11px;font-size:11px;color:#3D4A6B;">این هفته دوخت مانتو ۱۵٪ تخفیف دارد.</div>
            <div style="font-size:10px;color:#9a9587;">ارسال به ۱۲۴ مشتری · همین حالا</div>
          </div>
        </div>
        <div class="hv76221a" style="flex:1 1 320px;background:#fff;border:1px solid #E8E6E1;border-radius:16px;padding:22px;box-shadow:0 10px 30px -24px rgba(31,42,68,.4);transition:transform .25s ease,box-shadow .25s ease;">
          <div style="display:flex;align-items:center;gap:11px;margin-bottom:8px;">
            <span style="flex:none;display:inline-flex;width:42px;height:42px;align-items:center;justify-content:center;background:#FCEFEA;border-radius:50%;color:#E76F51;"><svg width="21" height="21" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-8a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v8"/><path d="M4 16s.5-1 2-1 2.5 2 4 2 2.5-2 4-2 2.5 2 4 2 2-1 2-1"/><path d="M2 21h20"/><path d="M7 8v3"/><path d="M12 8v3"/><path d="M17 8v3"/></svg></span>
            <h3 style="font-size:17.5px;font-weight:700;color:#1F2A44;margin:0;">پیامک تبریک تولد خودکار</h3>
          </div>
          <p style="font-size:14.5px;color:#3D4A6B;margin:0 0 14px;">دوختک تولد هر مشتری را یادش می‌ماند و خودش تبریک می‌فرستد؛ مشتری حس خاص‌بودن می‌کند.</p>
          <div style="background:#FAF8F4;border:1px solid #EDEAE3;border-radius:12px;padding:11px;">
            <div style="align-self:flex-start;background:#fff;border:1px solid #EDEAE3;border-radius:11px 11px 11px 4px;padding:7px 11px;font-size:11px;color:#3D4A6B;">مریم جان، تولدت مبارک! دوخت بعدی مهمان تخفیف مزون ما هستی.</div>
            <div style="font-size:10px;color:#9a9587;margin-top:6px;">ارسال خودکار · ۳ مرداد، روز تولد مریم</div>
          </div>
        </div>
      </div>
    </section>
    <section id="ch2" data-chsec data-screen-label="فصل ۲ سفارش‌ها" style="position:relative;z-index:1;padding:54px 0 34px;scroll-margin-top:150px;">
      <div data-rv style="opacity:0;transform:translateY(16px);transition:opacity .5s ease,transform .5s ease;margin-bottom:26px;max-width:520px;margin-inline-start:auto;text-align:start;">
        <span style="font-size:13px;font-weight:700;color:#D45A3D;letter-spacing:1px;">فصل ۲ — سفارش‌ها</span>
        <div style="font-size:21px;font-weight:700;color:#1F2A44;margin-top:8px;">قول‌وقرارها دیگر فراموش نمی‌شوند.</div>
      </div>
      <div data-ch data-side="b" data-rv style="opacity:0;transform:translateY(16px);transition:opacity .5s ease,transform .5s ease;display:flex;gap:5%;align-items:center;justify-content:space-between;">
        <div data-col="text">
          <h2 data-h2 style="font-size:36px;font-weight:700;color:#1F2A44;margin:0 0 16px;line-height:1.5;">هر سفارش، سرِ وقت آماده</h2>
          <p style="font-size:16.5px;line-height:1.9;color:#3D4A6B;margin:0 0 20px;">سفارش را با جزئیات ثبت کن؛ مراحل دوخت، سررسید تحویل و وضعیت هر کار در یک نگاه جلوی چشمت است — چه یک سفارش داشته باشی، چه چهل تا.</p>
          <div style="display:flex;flex-direction:column;gap:11px;font-size:15px;color:#3D4A6B;">
            <span style="display:flex;align-items:center;gap:10px;"><svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="#E76F51" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" style="flex:none;"><path d="M20 6 9 17l-5-5"/></svg>مراحل کار از برش تا تحویل، مرحله‌به‌مرحله</span>
            <span style="display:flex;align-items:center;gap:10px;"><svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="#E76F51" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" style="flex:none;"><path d="M20 6 9 17l-5-5"/></svg>سررسید تحویل با یادآوری، قبل از دیر شدن</span>
            <span style="display:flex;align-items:center;gap:10px;"><svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="#E76F51" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" style="flex:none;"><path d="M20 6 9 17l-5-5"/></svg>وضعیت همه سفارش‌ها در یک صفحه</span>
          </div>
        </div>
        <div data-col="vis">
          <!-- SCREENSHOT-F2: صفحه مدیریت سفارش‌ها -->
          <div style="position:relative;background:#fff;border:1px solid #E8E6E1;border-radius:14px;box-shadow:0 2px 6px rgba(31,42,68,.08),0 26px 52px -22px rgba(31,42,68,.32);transform:rotate(.75deg);overflow:hidden;">
            <div style="display:flex;align-items:center;gap:6px;padding:9px 12px;border-bottom:1px solid #F0EDE6;background:#FBFAF7;">
              <span style="width:9px;height:9px;border-radius:50%;background:#E6E1D7;"></span><span style="width:9px;height:9px;border-radius:50%;background:#E6E1D7;"></span><span style="width:9px;height:9px;border-radius:50%;background:#E6E1D7;"></span>
              <span style="margin-inline-start:auto;font-size:10px;color:#c9c3b6;">SCREENSHOT-F2</span>
            </div>
            <div style="padding:16px;">
              <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px;">
                <span style="font-size:13.5px;font-weight:700;color:#1F2A44;">سفارش‌های جاری</span>
                <span style="font-size:10.5px;color:#fff;background:#E76F51;padding:4px 11px;border-radius:8px;font-weight:700;">+ سفارش جدید</span>
              </div>
              <div style="display:grid;grid-template-columns:1.2fr .9fr .7fr 1fr;font-size:10px;color:#9a9587;padding:0 4px 7px;border-bottom:1px solid #F0EDE6;"><span>مشتری</span><span>نوع لباس</span><span>تحویل</span><span>وضعیت</span></div>
              <div style="display:grid;grid-template-columns:1.2fr .9fr .7fr 1fr;align-items:center;font-size:11px;color:#3D4A6B;padding:9px 4px;border-bottom:1px solid #F6F4EF;"><span style="font-weight:700;color:#1F2A44;">مریم احمدی</span><span>مانتو مجلسی</span><span>۱۴ تیر</span><span style="justify-self:start;background:#FCEFEA;color:#D45A3D;font-weight:700;font-size:9.5px;padding:2px 8px;border-radius:999px;">در حال دوخت</span></div>
              <div style="display:grid;grid-template-columns:1.2fr .9fr .7fr 1fr;align-items:center;font-size:11px;color:#3D4A6B;padding:9px 4px;border-bottom:1px solid #F6F4EF;"><span style="font-weight:700;color:#1F2A44;">سارا کریمی</span><span>کت و دامن</span><span>۱۸ تیر</span><span style="justify-self:start;background:#E8F5EE;color:#1F8A5B;font-weight:700;font-size:9.5px;padding:2px 8px;border-radius:999px;">آماده تحویل</span></div>
              <div style="display:grid;grid-template-columns:1.2fr .9fr .7fr 1fr;align-items:center;font-size:11px;color:#3D4A6B;padding:9px 4px;border-bottom:1px solid #F6F4EF;"><span style="font-weight:700;color:#1F2A44;">زهرا موسوی</span><span>لباس شب</span><span>۲۲ تیر</span><span style="justify-self:start;background:#EEF1F7;color:#3D4A6B;font-weight:700;font-size:9.5px;padding:2px 8px;border-radius:999px;">برش</span></div>
              <div style="display:grid;grid-template-columns:1.2fr .9fr .7fr 1fr;align-items:center;font-size:11px;color:#3D4A6B;padding:9px 4px;"><span style="font-weight:700;color:#1F2A44;">الهام رضایی</span><span>پیراهن</span><span>۲۵ تیر</span><span style="justify-self:start;background:#EEF1F7;color:#3D4A6B;font-weight:700;font-size:9.5px;padding:2px 8px;border-radius:999px;">سفارش جدید</span></div>
            </div>
          </div>
        </div>
      </div>
      <div data-subrow data-rv data-rv-delay="80" style="opacity:0;transform:translateY(16px);transition:opacity .5s ease,transform .5s ease;display:flex;flex-wrap:wrap;gap:18px;margin-top:28px;position:relative;z-index:1;">
        <div class="hv76221a" style="flex:1 1 320px;background:#fff;border:1px solid #E8E6E1;border-radius:16px;padding:22px;box-shadow:0 10px 30px -24px rgba(31,42,68,.4);transition:transform .25s ease,box-shadow .25s ease;">
          <div style="display:flex;align-items:center;gap:11px;margin-bottom:8px;">
            <span style="flex:none;display:inline-flex;width:42px;height:42px;align-items:center;justify-content:center;background:#FCEFEA;border-radius:50%;color:#E76F51;"><svg width="21" height="21" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M7.9 20A9 9 0 1 0 4 16.1L2 22Z"/></svg></span>
            <h3 style="font-size:17.5px;font-weight:700;color:#1F2A44;margin:0;">پیامک خودکار به مشتری</h3>
          </div>
          <p style="font-size:14.5px;color:#3D4A6B;margin:0 0 14px;">«سفارش شما آماده است» بدون اینکه دست به گوشی ببری؛ مشتری هم بی‌خبر نمی‌ماند.</p>
          <div style="background:#FAF8F4;border:1px solid #EDEAE3;border-radius:12px;padding:11px;">
            <div style="background:#fff;border:1px solid #EDEAE3;border-radius:11px 11px 11px 4px;padding:7px 11px;font-size:11px;color:#3D4A6B;">سارا جان، کت و دامن شما آماده تحویل است. مزون مریم</div>
            <div style="font-size:10px;color:#9a9587;margin-top:6px;">ارسال خودکار هنگام تغییر وضعیت سفارش</div>
          </div>
        </div>
        <div class="hv76221a" style="flex:1 1 320px;background:#fff;border:1px solid #E8E6E1;border-radius:16px;padding:22px;box-shadow:0 10px 30px -24px rgba(31,42,68,.4);transition:transform .25s ease,box-shadow .25s ease;">
          <div style="display:flex;align-items:center;gap:11px;margin-bottom:8px;">
            <span style="flex:none;display:inline-flex;width:42px;height:42px;align-items:center;justify-content:center;background:#FCEFEA;border-radius:50%;color:#E76F51;"><svg width="21" height="21" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg></span>
            <h3 style="font-size:17.5px;font-weight:700;color:#1F2A44;margin:0;">ارجاع سفارش به همکار</h3>
          </div>
          <p style="font-size:14.5px;color:#3D4A6B;margin:0 0 14px;">سفارش را به خیاط‌های زیرمجموعه‌ات بسپار و فقط روند را دنبال کن؛ مخصوص مزون‌هایی که تیم دارند.</p>
          <!-- SCREENSHOT-F3 (اختیاری): صفحه ارجاع سفارش -->
          <div style="background:#FAF8F4;border:1px solid #EDEAE3;border-radius:12px;padding:13px;display:flex;align-items:center;justify-content:center;gap:10px;">
            <span style="display:flex;flex-direction:column;align-items:center;gap:4px;"><i style="width:36px;height:36px;border-radius:50%;background:#1F2A44;color:#fff;display:flex;align-items:center;justify-content:center;font-style:normal;font-size:12px;font-weight:700;">م</i><b style="font-size:10px;color:#6B7280;font-weight:400;">مزون تو</b></span>
            <span style="flex:1;max-width:90px;display:flex;flex-direction:column;align-items:center;gap:3px;"><svg width="100%" height="10" viewBox="0 0 90 10" preserveAspectRatio="none"><path d="M88 5 H10" stroke="#E76F51" stroke-width="2" stroke-dasharray="7 5" stroke-linecap="round"/><path d="M14 1 8 5l6 4" stroke="#E76F51" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"/></svg><b style="font-size:9.5px;color:#D45A3D;background:#FCEFEA;border-radius:999px;padding:2px 8px;">ارجاع شد</b></span>
            <span style="display:flex;flex-direction:column;align-items:center;gap:4px;"><i style="width:36px;height:36px;border-radius:50%;background:#E76F51;color:#fff;display:flex;align-items:center;justify-content:center;font-style:normal;font-size:12px;font-weight:700;">خ</i><b style="font-size:10px;color:#6B7280;font-weight:400;">خیاط همکار</b></span>
          </div>
        </div>
      </div>
    </section>
    <section id="ch3" data-chsec data-screen-label="فصل ۳ ویترین" style="position:relative;z-index:1;padding:54px 0 34px;scroll-margin-top:150px;">
      <div data-rv style="opacity:0;transform:translateY(16px);transition:opacity .5s ease,transform .5s ease;margin-bottom:26px;max-width:560px;">
        <span style="font-size:13px;font-weight:700;color:#D45A3D;letter-spacing:1px;">فصل ۳ — ویترین</span>
        <div style="font-size:21px;font-weight:700;color:#1F2A44;margin-top:8px;">مزونت را همیشه باز نگه دار — حتی نیمه‌شب.</div>
      </div>
      <div data-rv style="opacity:0;transform:translateY(16px);transition:opacity .5s ease,transform .5s ease;position:relative;z-index:1;background:#fff;border:1px solid #E8E6E1;border-radius:22px;padding:34px 28px;box-shadow:0 14px 40px -28px rgba(31,42,68,.4);">
        <div style="display:flex;flex-wrap:wrap;gap:40px;align-items:center;">
          <div style="flex:1 1 340px;min-width:280px;">
            <h2 data-h2 style="font-size:36px;font-weight:700;color:#1F2A44;margin:0 0 16px;line-height:1.5;">گالری نمونه‌کار،
              <span style="position:relative;white-space:nowrap;">ویترین آنلاینت<svg width="100%" height="14" viewBox="0 0 140 14" preserveAspectRatio="none" style="position:absolute;inset-inline-start:0;bottom:-6px;overflow:visible;"><path d="M3 8 Q40 2 72 7 T137 6" stroke="#E76F51" stroke-width="6" fill="none" stroke-linecap="round" opacity=".85"/></svg></span>
            </h2>
            <p style="font-size:16.5px;line-height:1.9;color:#3D4A6B;margin:0 0 22px;">عکس و فیلم نمونه‌کارها و پارچه‌هایت را آپلود کن و لینکش را به مشتری بده؛ ببیند، انتخاب کند و سفارش بدهد — بدون اینکه پایش به مزون برسد.</p>
            <div style="display:flex;flex-direction:column;gap:11px;font-size:15px;color:#3D4A6B;">
              <span style="display:flex;align-items:center;gap:10px;"><svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="#E76F51" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" style="flex:none;"><path d="M20 6 9 17l-5-5"/></svg>عکس و فیلم، هر دو</span>
              <span style="display:flex;align-items:center;gap:10px;"><svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="#E76F51" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" style="flex:none;"><path d="M20 6 9 17l-5-5"/></svg>لینک اختصاصی برای اشتراک با مشتری</span>
              <span style="display:flex;align-items:center;gap:10px;"><svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="#E76F51" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" style="flex:none;"><path d="M20 6 9 17l-5-5"/></svg>دسته‌بندی نمونه‌کار و پارچه</span>
            </div>
          </div>
          <div style="flex:1 1 400px;min-width:280px;position:relative;display:flex;gap:20px;align-items:flex-start;justify-content:center;flex-wrap:wrap;">
            <!-- SCREENSHOT-F4: گالری از دید مدیریت -->
            <div style="flex:1 1 230px;max-width:280px;position:relative;background:#fff;border:1px solid #E8E6E1;border-radius:12px;box-shadow:0 2px 6px rgba(31,42,68,.07),0 20px 40px -22px rgba(31,42,68,.32);transform:rotate(-1deg);overflow:hidden;">
              <div style="display:flex;align-items:center;justify-content:space-between;padding:9px 12px;border-bottom:1px solid #F0EDE6;">
                <span style="font-size:11.5px;font-weight:700;color:#1F2A44;">گالری — مدیریت</span>
                <span style="font-size:9px;color:#c9c3b6;">SCREENSHOT-F4</span>
              </div>
              <div style="display:grid;grid-template-columns:1fr 1fr;gap:6px;padding:10px;">
                <div style="aspect-ratio:3/4;border-radius:7px;background:linear-gradient(150deg,#F6E0D6,#EEC9B9);display:flex;align-items:center;justify-content:center;"><span style="font-size:9.5px;color:#8a5f4f;background:rgba(255,255,255,.72);border-radius:999px;padding:2px 8px;">نمونه‌کار ۱</span></div>
                <div style="aspect-ratio:3/4;border-radius:7px;background:linear-gradient(150deg,#3B4869,#2A3550);display:flex;align-items:center;justify-content:center;"><span style="font-size:9.5px;color:#dfe4f0;background:rgba(31,42,68,.5);border-radius:999px;padding:2px 8px;">فیلم نمونه‌کار</span></div>
                <div style="aspect-ratio:3/4;border-radius:7px;background:linear-gradient(150deg,#E9EDE2,#D5DCC9);display:flex;align-items:center;justify-content:center;"><span style="font-size:9.5px;color:#5d6650;background:rgba(255,255,255,.72);border-radius:999px;padding:2px 8px;">نمونه پارچه</span></div>
                <div style="aspect-ratio:3/4;border-radius:7px;background:linear-gradient(150deg,#F0CDBD,#E5B2A0);display:flex;align-items:center;justify-content:center;"><span style="font-size:9.5px;color:#7d4f3f;background:rgba(255,255,255,.72);border-radius:999px;padding:2px 8px;">نمونه‌کار ۲</span></div>
              </div>
              <div style="display:flex;align-items:center;justify-content:center;gap:6px;margin:0 10px 10px;background:#FAF8F4;border:1px dashed #E0DACC;border-radius:9px;padding:7px;font-size:10.5px;color:#6B7280;">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#E76F51" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg>
                dookhtak.ir/g/mezon-maryam
              </div>
            </div>
            <!-- stitch connector -->
            <div data-desktop-only="block" style="display:none;flex:none;align-self:center;">
              <svg width="56" height="30" viewBox="0 0 56 30" fill="none"><path d="M52 8 Q28 16 10 22" stroke="#E76F51" stroke-width="2" stroke-dasharray="8 6" stroke-linecap="round"/><path d="M10 22 19 17 M10 22 19 25" stroke="#E76F51" stroke-width="2" stroke-linecap="round"/></svg>
            </div>
            <!-- SCREENSHOT-F5: گالری از دید مشتری -->
            <div style="flex:0 0 168px;position:relative;background:#1F2A44;border-radius:24px;padding:8px;border:1px solid #E8E6E1;box-shadow:0 2px 6px rgba(31,42,68,.14),0 22px 44px -16px rgba(31,42,68,.42);transform:rotate(1.5deg);">
              <div style="background:#fff;border-radius:18px;overflow:hidden;">
                <div style="height:22px;background:#1F2A44;display:flex;justify-content:center;align-items:flex-start;"><span style="width:44px;height:5px;background:#3D4A6B;border-radius:0 0 6px 6px;"></span></div>
                <div style="padding:10px;">
                  <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:7px;">
                    <span style="font-size:10px;font-weight:700;color:#1F2A44;">مزون مریم — نمونه‌کارها</span>
                  </div>
                  <div style="display:grid;grid-template-columns:1fr 1fr;gap:4px;">
                    <div style="aspect-ratio:3/4;border-radius:5px;background:linear-gradient(150deg,#F6E0D6,#EEC9B9);"></div>
                    <div style="aspect-ratio:3/4;border-radius:5px;background:linear-gradient(150deg,#3B4869,#2A3550);"></div>
                    <div style="aspect-ratio:3/4;border-radius:5px;background:linear-gradient(150deg,#E9EDE2,#D5DCC9);"></div>
                    <div style="aspect-ratio:3/4;border-radius:5px;background:linear-gradient(150deg,#F0CDBD,#E5B2A0);"></div>
                  </div>
                  <div style="display:flex;align-items:center;justify-content:center;background:#E76F51;color:#fff;border-radius:8px;padding:6px;margin-top:7px;font-size:9.5px;font-weight:700;">انتخاب و ثبت سفارش</div>
                  <div style="font-size:8.5px;color:#9a9587;text-align:center;margin-top:5px;">SCREENSHOT-F5 · دید مشتری</div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
    <section id="ch4" data-chsec data-screen-label="فصل ۴ پول و حساب" style="position:relative;z-index:1;padding:54px 0 34px;scroll-margin-top:150px;">
      <div data-rv style="opacity:0;transform:translateY(16px);transition:opacity .5s ease,transform .5s ease;margin-bottom:26px;max-width:520px;margin-inline-start:auto;text-align:start;">
        <span style="font-size:13px;font-weight:700;color:#D45A3D;letter-spacing:1px;">فصل ۴ — پول و حساب</span>
        <div style="font-size:21px;font-weight:700;color:#1F2A44;margin-top:8px;">پولت را راحت بگیر، حسابت را دقیق نگه دار.</div>
      </div>
      <div data-ch data-side="b" data-rv style="opacity:0;transform:translateY(16px);transition:opacity .5s ease,transform .5s ease;display:flex;gap:5%;align-items:center;justify-content:space-between;">
        <div data-col="text">
          <h2 data-h2 style="font-size:36px;font-weight:700;color:#1F2A44;margin:0 0 16px;line-height:1.5;">تسویه با یک لینک</h2>
          <p style="font-size:16.5px;line-height:1.9;color:#3D4A6B;margin:0 0 20px;">لینک پرداخت را بفرست، مشتری همان لحظه با درگاه امن پرداخت کند؛ بدون کارت‌به‌کارت، بدون پیگیری — و همه پرداخت‌ها خودبه‌خود در حسابت ثبت می‌شوند.</p>
          <div style="display:flex;flex-direction:column;gap:11px;font-size:15px;color:#3D4A6B;">
            <span style="display:flex;align-items:center;gap:10px;"><svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="#E76F51" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" style="flex:none;"><path d="M20 6 9 17l-5-5"/></svg>پرداخت آنلاین با درگاه امن</span>
            <span style="display:flex;align-items:center;gap:10px;"><svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="#E76F51" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" style="flex:none;"><path d="M20 6 9 17l-5-5"/></svg>ثبت خودکار هر پرداخت پای سفارشش</span>
            <span style="display:flex;align-items:center;gap:10px;"><svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="#E76F51" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" style="flex:none;"><path d="M20 6 9 17l-5-5"/></svg>خداحافظی با کارت‌به‌کارت و پیگیری تلفنی</span>
          </div>
        </div>
        <div data-col="vis">
          <!-- SCREENSHOT-F6: صفحه پرداخت مشتری / ساخت لینک پرداخت -->
          <div style="position:relative;background:#fff;border:1px solid #E8E6E1;border-radius:14px;box-shadow:0 2px 6px rgba(31,42,68,.08),0 26px 52px -22px rgba(31,42,68,.32);transform:rotate(-.75deg);overflow:hidden;">
            <div style="display:flex;align-items:center;gap:6px;padding:9px 12px;border-bottom:1px solid #F0EDE6;background:#FBFAF7;">
              <span style="width:9px;height:9px;border-radius:50%;background:#E6E1D7;"></span><span style="width:9px;height:9px;border-radius:50%;background:#E6E1D7;"></span><span style="width:9px;height:9px;border-radius:50%;background:#E6E1D7;"></span>
              <span style="margin-inline-start:auto;font-size:10px;color:#c9c3b6;">SCREENSHOT-F6</span>
            </div>
            <div style="padding:16px;">
              <div style="font-size:13px;font-weight:700;color:#1F2A44;margin-bottom:12px;">لینک پرداخت — مانتو مجلسی، مریم احمدی</div>
              <div style="display:flex;align-items:center;justify-content:space-between;background:#FAF8F4;border:1px solid #EDEAE3;border-radius:10px;padding:10px 12px;font-size:12px;color:#3D4A6B;margin-bottom:9px;">
                <span>مبلغ</span><b style="color:#1F2A44;font-weight:700;">۲٬۴۵۰٬۰۰۰ تومان</b>
              </div>
              <div style="display:flex;align-items:center;gap:7px;background:#FAF8F4;border:1px dashed #E0DACC;border-radius:10px;padding:10px 12px;font-size:11.5px;color:#6B7280;margin-bottom:12px;">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#E76F51" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg>
                pay.dookhtak.ir/m84
                <span style="margin-inline-start:auto;color:#fff;background:#1F2A44;border-radius:7px;padding:3px 10px;font-size:10px;font-weight:700;">کپی و ارسال</span>
              </div>
              <div style="display:flex;align-items:center;gap:8px;background:#E8F5EE;border:1px solid #C4E5D2;border-radius:10px;padding:10px 12px;font-size:12px;color:#1F8A5B;font-weight:700;">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
                پرداخت شد · امروز ۱۴:۲۲
              </div>
            </div>
          </div>
        </div>
      </div>
      <div data-subrow data-rv data-rv-delay="80" style="opacity:0;transform:translateY(16px);transition:opacity .5s ease,transform .5s ease;display:flex;flex-wrap:wrap;gap:18px;margin-top:28px;position:relative;z-index:1;">
        <div class="hv76221a" style="flex:1 1 260px;background:#fff;border:1px solid #E8E6E1;border-radius:16px;padding:22px;box-shadow:0 10px 30px -24px rgba(31,42,68,.4);transition:transform .25s ease,box-shadow .25s ease;">
          <div style="display:flex;align-items:center;gap:11px;margin-bottom:8px;">
            <span style="flex:none;display:inline-flex;width:42px;height:42px;align-items:center;justify-content:center;background:#FCEFEA;border-radius:50%;color:#E76F51;"><svg width="21" height="21" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 2v20l2-1 2 1 2-1 2 1 2-1 2 1 2-1 2 1V2l-2 1-2-1-2 1-2-1-2 1-2-1-2 1Z"/><path d="M16 8h-6a2 2 0 1 0 0 4h4a2 2 0 1 1 0 4H8"/><path d="M12 17.5v-11"/></svg></span>
            <h3 style="font-size:17px;font-weight:700;color:#1F2A44;margin:0;">حسابداری حرفه‌ای</h3>
          </div>
          <p style="font-size:14px;color:#3D4A6B;margin:0 0 14px;">ثبت کامل درآمدها و هزینه‌ها؛ سود واقعی مزونت را لحظه‌ای ببین.</p>
          <!-- SCREENSHOT-F7 (اختیاری): گزارش مالی -->
          <div style="background:#FAF8F4;border:1px solid #EDEAE3;border-radius:12px;padding:12px;">
            <div style="display:flex;align-items:flex-end;justify-content:space-around;gap:8px;height:56px;border-bottom:1px solid #E3DFD5;">
              <div style="display:flex;align-items:flex-end;gap:3px;"><span style="width:10px;height:30px;background:#1F2A44;border-radius:3px 3px 0 0;"></span><span style="width:10px;height:17px;background:#E76F51;border-radius:3px 3px 0 0;opacity:.85;"></span></div>
              <div style="display:flex;align-items:flex-end;gap:3px;"><span style="width:10px;height:40px;background:#1F2A44;border-radius:3px 3px 0 0;"></span><span style="width:10px;height:14px;background:#E76F51;border-radius:3px 3px 0 0;opacity:.85;"></span></div>
              <div style="display:flex;align-items:flex-end;gap:3px;"><span style="width:10px;height:26px;background:#1F2A44;border-radius:3px 3px 0 0;"></span><span style="width:10px;height:20px;background:#E76F51;border-radius:3px 3px 0 0;opacity:.85;"></span></div>
              <div style="display:flex;align-items:flex-end;gap:3px;"><span style="width:10px;height:46px;background:#1F2A44;border-radius:3px 3px 0 0;"></span><span style="width:10px;height:15px;background:#E76F51;border-radius:3px 3px 0 0;opacity:.85;"></span></div>
            </div>
            <div style="display:flex;gap:12px;font-size:9.5px;color:#6B7280;margin-top:6px;">
              <span style="display:flex;align-items:center;gap:4px;"><i style="width:8px;height:8px;background:#1F2A44;border-radius:2px;"></i>درآمد</span>
              <span style="display:flex;align-items:center;gap:4px;"><i style="width:8px;height:8px;background:#E76F51;border-radius:2px;"></i>هزینه</span>
            </div>
          </div>
        </div>
        <div class="hv76221a" style="flex:1 1 260px;background:#fff;border:1px solid #E8E6E1;border-radius:16px;padding:22px;box-shadow:0 10px 30px -24px rgba(31,42,68,.4);transition:transform .25s ease,box-shadow .25s ease;">
          <div style="display:flex;align-items:center;gap:11px;margin-bottom:8px;">
            <span style="flex:none;display:inline-flex;width:42px;height:42px;align-items:center;justify-content:center;background:#FCEFEA;border-radius:50%;color:#E76F51;"><svg width="21" height="21" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"/><path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"/></svg></span>
            <h3 style="font-size:17px;font-weight:700;color:#1F2A44;margin:0;">یادآوری چک‌ها</h3>
          </div>
          <p style="font-size:14px;color:#3D4A6B;margin:0 0 14px;">چک‌های دریافتی و پرداختی را ثبت کن؛ دوختک قبل از سررسید بهت یادآوری می‌کند — استرس چک برگشتی تمام.</p>
          <div style="background:#FAF8F4;border:1px solid #EDEAE3;border-radius:12px;padding:11px;display:flex;flex-direction:column;gap:6px;">
            <div style="display:flex;align-items:center;gap:7px;background:#fff;border:1px solid #EDEAE3;border-radius:9px;padding:7px 10px;font-size:11px;color:#3D4A6B;">
              <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#E76F51" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"/><path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"/></svg>
              چک دریافتی — <b style="color:#1F2A44;">۱۵ تیر</b> · ۳ روز مانده
            </div>
            <div style="display:flex;align-items:center;gap:7px;background:#fff;border:1px solid #EDEAE3;border-radius:9px;padding:7px 10px;font-size:11px;color:#3D4A6B;">
              <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#6B7280" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"/><path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"/></svg>
              چک پرداختی — <b style="color:#1F2A44;">۳۰ تیر</b>
            </div>
          </div>
        </div>
        <div class="hv76221a" style="flex:1 1 260px;background:#fff;border:1px solid #E8E6E1;border-radius:16px;padding:22px;box-shadow:0 10px 30px -24px rgba(31,42,68,.4);transition:transform .25s ease,box-shadow .25s ease;">
          <div style="display:flex;align-items:center;gap:11px;margin-bottom:8px;">
            <span style="flex:none;display:inline-flex;width:42px;height:42px;align-items:center;justify-content:center;background:#FCEFEA;border-radius:50%;color:#E76F51;"><svg width="21" height="21" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 3v16a2 2 0 0 0 2 2h16"/><path d="M18 17V9"/><path d="M13 17V5"/><path d="M8 17v-3"/></svg></span>
            <h3 style="font-size:17px;font-weight:700;color:#1F2A44;margin:0;">گزارش‌های روشن</h3>
          </div>
          <p style="font-size:14px;color:#3D4A6B;margin:0 0 14px;">دخل‌وخرج ماه بدون ماشین‌حساب و ورق‌زدن دفتر.</p>
          <div style="background:#FAF8F4;border:1px solid #EDEAE3;border-radius:12px;padding:12px;display:flex;flex-direction:column;gap:6px;font-size:11.5px;color:#3D4A6B;">
            <div style="display:flex;justify-content:space-between;"><span>درآمد تیر</span><b style="color:#1F2A44;">۱۸٬۴۰۰٬۰۰۰</b></div>
            <div style="display:flex;justify-content:space-between;"><span>هزینه تیر</span><b style="color:#1F2A44;">۶٬۲۰۰٬۰۰۰</b></div>
            <div style="display:flex;justify-content:space-between;border-top:1px dashed #E0DACC;padding-top:6px;"><span style="font-weight:700;color:#1F2A44;">سود</span><b style="color:#E76F51;">۱۲٬۲۰۰٬۰۰۰ تومان</b></div>
          </div>
        </div>
      </div>
    </section>
    <section id="ch5" data-chsec data-screen-label="فصل ۵ زیرساخت" style="position:relative;z-index:1;padding:54px 0 60px;scroll-margin-top:150px;">
      <div data-rv style="opacity:0;transform:translateY(16px);transition:opacity .5s ease,transform .5s ease;margin-bottom:26px;max-width:560px;">
        <span style="font-size:13px;font-weight:700;color:#D45A3D;letter-spacing:1px;">فصل ۵ — زیرساخت</span>
        <div style="font-size:21px;font-weight:700;color:#1F2A44;margin-top:8px;">و همه این‌ها، هرجا که باشی — در مغازه، خانه، مسافرت.</div>
      </div>
      <div data-rv style="opacity:0;transform:translateY(16px);transition:opacity .5s ease,transform .5s ease;position:relative;z-index:1;display:grid;grid-template-columns:repeat(auto-fit,minmax(240px,1fr));gap:18px;">
        <div class="hv76221a" style="background:#fff;border:1px solid #E8E6E1;border-radius:16px;padding:24px;box-shadow:0 10px 30px -24px rgba(31,42,68,.4);transition:transform .25s ease,box-shadow .25s ease;">
          <span style="display:inline-flex;width:44px;height:44px;align-items:center;justify-content:center;background:#FCEFEA;border-radius:50%;color:#E76F51;"><svg width="21" height="21" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17.5 19H9a7 7 0 1 1 6.71-9h1.79a4.5 4.5 0 1 1 0 9Z"/></svg></span>
          <h3 style="font-size:17.5px;font-weight:700;color:#1F2A44;margin:14px 0 7px;">ابری و همه‌جا در دسترس</h3>
          <p style="font-size:14px;color:#3D4A6B;margin:0;">موبایل، تبلت و کامپیوتر؛ اطلاعات همیشه همگام.</p>
        </div>
        <div class="hv76221a" style="background:#fff;border:1px solid #E8E6E1;border-radius:16px;padding:24px;box-shadow:0 10px 30px -24px rgba(31,42,68,.4);transition:transform .25s ease,box-shadow .25s ease;">
          <span style="display:inline-flex;width:44px;height:44px;align-items:center;justify-content:center;background:#FCEFEA;border-radius:50%;color:#E76F51;"><svg width="21" height="21" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 2a14.5 14.5 0 0 0 0 20 14.5 14.5 0 0 0 0-20"/><path d="M2 12h20"/></svg></span>
          <h3 style="font-size:17.5px;font-weight:700;color:#1F2A44;margin:14px 0 7px;">دامنه اختصاصی</h3>
          <p style="font-size:14px;color:#3D4A6B;margin:0;">دوختک روی آدرس اینترنتی برند خودت بالا بیاید؛ مشتری فقط اسم تو را می‌بیند.</p>
        </div>
        <div class="hv76221a" style="background:#fff;border:1px solid #E8E6E1;border-radius:16px;padding:24px;box-shadow:0 10px 30px -24px rgba(31,42,68,.4);transition:transform .25s ease,box-shadow .25s ease;">
          <span style="display:inline-flex;width:44px;height:44px;align-items:center;justify-content:center;background:#FCEFEA;border-radius:50%;color:#E76F51;"><svg width="21" height="21" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12a9 9 0 1 1-9-9c2.52 0 4.93 1 6.74 2.74L21 8"/><path d="M21 3v5h-5"/></svg></span>
          <h3 style="font-size:17.5px;font-weight:700;color:#1F2A44;margin:14px 0 7px;">همیشه به‌روز</h3>
          <p style="font-size:14px;color:#3D4A6B;margin:0;">قابلیت‌های جدید خودکار اضافه می‌شوند؛ بدون نصب، بدون هزینه آپدیت.</p>
        </div>
        <div class="hv76221a" style="background:#fff;border:1px solid #E8E6E1;border-radius:16px;padding:24px;box-shadow:0 10px 30px -24px rgba(31,42,68,.4);transition:transform .25s ease,box-shadow .25s ease;">
          <span style="display:inline-flex;width:44px;height:44px;align-items:center;justify-content:center;background:#FCEFEA;border-radius:50%;color:#E76F51;"><svg width="21" height="21" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="m4.93 4.93 4.24 4.24"/><path d="m14.83 9.17 4.24-4.24"/><path d="m14.83 14.83 4.24 4.24"/><path d="m9.17 14.83-4.24 4.24"/><circle cx="12" cy="12" r="4"/></svg></span>
          <h3 style="font-size:17.5px;font-weight:700;color:#1F2A44;margin:14px 0 7px;">پشتیبانی و آموزش فارسی</h3>
          <p style="font-size:14px;color:#3D4A6B;margin:0;">از راه‌اندازی تا هر سؤالی، کنارت هستیم.</p>
        </div>
      </div>
    </section>
  </div>

  <section data-screen-label="چک‌لیست دوختک">
    <div style="max-width:1000px;margin-inline:auto;padding:30px 24px 70px;">
      <div data-rv style="opacity:0;transform:translateY(16px);transition:opacity .5s ease,transform .5s ease;text-align:center;margin-bottom:36px;">
        <h2 data-h2 style="font-size:36px;font-weight:700;color:#1F2A44;margin:0;">
          <span style="position:relative;white-space:nowrap;">یک‌جا ببین<svg width="100%" height="14" viewBox="0 0 140 14" preserveAspectRatio="none" style="position:absolute;inset-inline-start:0;bottom:-6px;overflow:visible;"><path d="M3 8 Q40 2 72 7 T137 6" stroke="#E76F51" stroke-width="6" fill="none" stroke-linecap="round" opacity=".85"/></svg></span>
          چه چیزهایی داری
        </h2>
        <p style="font-size:16px;color:#6B7280;margin:20px 0 0;">مرور ده‌ثانیه‌ای همه امکانات دوختک.</p>
      </div>
      <div id="checkGrid" data-rv data-rv-delay="60" style="opacity:0;transform:translateY(16px);transition:opacity .5s ease,transform .5s ease;background:#fff;border:1px solid #E8E6E1;border-radius:18px;padding:28px;box-shadow:0 12px 34px -26px rgba(31,42,68,.4);display:grid;grid-template-columns:repeat(3,1fr);gap:14px 26px;">
        <?php foreach ($checklist as $c): ?>
          <span style="display:flex;align-items:center;gap:10px;font-size:14.5px;color:#3D4A6B;min-height:28px;">
            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="#E76F51" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" style="flex:none;"><path d="M20 6 9 17l-5-5"/></svg>
            <?= e($c) ?>
          </span>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <!-- FINAL CTA -->
  <section id="cta" data-screen-label="سی‌تی‌ای نهایی" style="scroll-margin-top:96px;">
    <div data-rv style="opacity:0;transform:translateY(18px);transition:opacity .5s ease,transform .5s ease;max-width:1000px;margin-inline:auto;padding:30px 24px 74px;">
      <div style="position:relative;background:#FCEFEA;border-radius:24px;padding:58px 28px;text-align:center;overflow:hidden;">
        <div style="position:absolute;inset:14px;border:2px dashed #E76F51;border-radius:16px;opacity:.55;pointer-events:none;"></div>
        <h2 data-h2 style="position:relative;font-size:38px;font-weight:700;color:#1F2A44;margin:0 0 14px;">همه این امکانات، ۱۰ روز رایگان</h2>
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