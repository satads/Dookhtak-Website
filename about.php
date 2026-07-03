<?php
/**
 * About page ("درباره ما"). Fully static content converted 1:1 from the
 * approved design; texts marked as placeholders in the design stay
 * placeholders until the team provides final copy.
 */
require_once __DIR__ . '/includes/layout.php';

render_head([
    'title' => 'درباره دوختک | دوختک',
    'description' => 'قصه دوختک و تیمی که آن را برای خیاط‌ها و مزون‌دارهای ایران می‌سازد.',
    'css' => '/assets/css/page-about.css',
    'js' => '/assets/js/page-about.js',
]);
?>
<div dir="rtl" lang="fa" style="position:relative;min-height:100vh;background-color:#FAF8F4;background-image:linear-gradient(#EDEAE3 1px,transparent 1px),linear-gradient(90deg,#EDEAE3 1px,transparent 1px);background-size:46px 46px;overflow-x:hidden;line-height:1.6;">

  <?php include __DIR__ . '/includes/site_header.php'; ?>

  <!-- COMPACT HERO -->
  <header data-screen-label="هیرو درباره ما">
    <div id="heroWrap" style="max-width:780px;margin-inline:auto;padding:130px 24px 40px;text-align:center;">
      <h1 id="heroTitle" data-hero style="opacity:0;transform:translateY(12px);transition:opacity .5s ease,transform .5s ease;font-size:44px;line-height:1.5;font-weight:700;color:#1F2A44;margin:0;letter-spacing:-.5px;">
        <span style="position:relative;white-space:nowrap;">اتیکت<svg width="100%" height="14" viewBox="0 0 140 14" preserveAspectRatio="none" style="position:absolute;inset-inline-start:0;bottom:-6px;overflow:visible;"><path d="M3 8 Q40 2 72 7 T137 6" stroke="#E76F51" stroke-width="6" fill="none" stroke-linecap="round" opacity=".85"/></svg></span>
        پشت یقه دوختک
      </h1>
      <p data-hero style="opacity:0;transform:translateY(12px);transition:opacity .5s ease,transform .5s ease;font-size:17px;line-height:2;color:#3D4A6B;margin:28px auto 0;max-width:560px;">هر لباس خوب، پشت یقه‌اش نوشته از چه جنسی است. این صفحه همان اتیکت ماست: که هستیم، چرا دوختک را ساختیم و به چه چیزهایی پایبندیم.</p>
    </div>
  </header>

  <!-- STORY -->
  <section data-screen-label="قصه دوختک">
    <div style="max-width:1080px;margin-inline:auto;padding:20px 24px 30px;display:flex;flex-wrap:wrap;gap:48px;align-items:center;justify-content:center;">
      <!-- ⚠ متن روایت placeholder واقع‌گرایانه — متن نهایی جایگزین شود -->
      <div data-rv style="opacity:0;transform:translateY(16px);transition:opacity .5s ease,transform .5s ease;flex:1 1 420px;max-width:720px;font-size:17.5px;line-height:2;color:#3D4A6B;">
        <h2 data-h2 style="font-size:32px;font-weight:700;color:#1F2A44;margin:0 0 20px;">چرا دوختک را ساختیم</h2>
        <p style="margin:0 0 1.2em;">از نزدیک دیده بودیم: دفترهایی پر از اندازه که هر بار باید ورق بخورد، سررسیدهایی که در شلوغی یادشان می‌رود، عکس نمونه‌کارهایی که لابه‌لای هزار عکس دیگر در گوشی گم می‌شوند و حساب‌هایی که آخر ماه جور درنمی‌آید. کار خیاطی هنر است — اما مدیریتش، فرسایش.</p>
        <p style="margin:0 0 1.2em;">دنبال ابزاری گشتیم که این بار را بردارد؛ چیزی پیدا نشد. نرم‌افزارهای موجود یا قدیمی و دسکتاپی بودند، یا اصلاً برای خیاطی طراحی نشده بودند و زبان این کار را نمی‌فهمیدند. جای ابزاری خالی بود که به زبان خود خیاط حرف بزند.</p>
        <p style="margin:0;">پس دوختک را ساختیم — تا خیاط فقط بدوزد و خلق کند، و بقیه‌اش (سفارش، مشتری، پیامک، حساب) خودش بچرخد.</p>
      </div>
      <!-- ایلوستریشن مینیمال: دفترچه ← کوک ← گوشی -->
      <div data-rv data-rv-delay="80" data-desktop-only="flex" style="opacity:0;transform:translateY(16px);transition:opacity .5s ease,transform .5s ease;flex:0 1 260px;display:none;justify-content:center;">
        <svg width="240" height="300" viewBox="0 0 240 300" fill="none">
          <!-- دفترچه قدیمی -->
          <rect x="18" y="18" width="120" height="86" rx="8" fill="#FFFFFF" stroke="#E8E6E1" stroke-width="1.5" transform="rotate(-3 78 61)"/>
          <line x1="38" y1="42" x2="118" y2="38" stroke="#EDEAE3" stroke-width="2" stroke-linecap="round"/>
          <line x1="39" y1="56" x2="119" y2="52" stroke="#EDEAE3" stroke-width="2" stroke-linecap="round"/>
          <line x1="40" y1="70" x2="106" y2="66" stroke="#EDEAE3" stroke-width="2" stroke-linecap="round"/>
          <line x1="41" y1="84" x2="112" y2="80" stroke="#EDEAE3" stroke-width="2" stroke-linecap="round"/>
          <path d="M30 24 30 96" stroke="#E76F51" stroke-width="1.6" stroke-dasharray="3 4" opacity=".5" transform="rotate(-3 78 61)"/>
          <!-- خط کوک اتصال -->
          <path d="M112 112 Q150 150 128 196" stroke="#E76F51" stroke-width="2" stroke-dasharray="9 7" stroke-linecap="round"/>
          <path d="M128 196 121 186 M128 196 136 188" stroke="#E76F51" stroke-width="2" stroke-linecap="round"/>
          <!-- گوشی -->
          <rect x="86" y="200" width="86" height="88" rx="14" fill="#1F2A44"/>
          <rect x="92" y="212" width="74" height="70" rx="9" fill="#FFFFFF"/>
          <rect x="99" y="220" width="28" height="24" rx="4" fill="#FCEFEA"/>
          <rect x="131" y="220" width="28" height="24" rx="4" fill="#EDEAE3"/>
          <rect x="99" y="250" width="60" height="6" rx="3" fill="#EDEAE3"/>
          <rect x="99" y="262" width="44" height="6" rx="3" fill="#E76F51" opacity=".8"/>
          <rect x="116" y="204" width="26" height="4" rx="2" fill="#3D4A6B"/>
        </svg>
      </div>
    </div>
  </section>

  <!-- CARE LABEL CARD -->
  <section data-screen-label="شناسنامه دوختک">
    <div style="max-width:560px;margin-inline:auto;padding:26px 24px 40px;">
      <div data-rv style="opacity:0;transform:translateY(18px);transition:opacity .5s ease,transform .5s ease;position:relative;padding-top:34px;">
        <!-- بند نخی -->
        <div id="tagWrap" style="transform-origin:50% 0;">
          <svg width="90" height="44" viewBox="0 0 90 44" style="display:block;margin:0 auto -8px;position:relative;z-index:2;">
            <path d="M12 2 Q45 40 78 2" stroke="#c9c3b6" stroke-width="1.8" fill="none"/>
          </svg>
          <div style="background:#fff;border:1px solid var(--color-border);border-radius:12px;box-shadow:0 3px 8px rgba(31,42,68,.06),0 24px 48px -26px rgba(31,42,68,.35);overflow:hidden;">
            <div style="display:flex;flex-direction:column;align-items:center;padding:26px 26px 8px;">
              <span style="width:16px;height:16px;border-radius:50%;background:#FAF8F4;border:2px solid #d9d3c6;margin-bottom:14px;"></span>
              <div style="font-size:12px;font-weight:700;color:#9a9587;letter-spacing:3px;">شناسنامه</div>
              <div style="display:flex;align-items:center;gap:9px;margin-top:6px;">
                <img src="/assets/images/Logo01-NEW.png" alt="" style="height:30px;">
                <b style="font-size:21px;color:#1F2A44;">دوختک</b>
              </div>
            </div>
            <div style="padding:18px 10px 22px;">
              <div class="hv220710" style="display:flex;align-items:flex-start;gap:13px;padding:13px 18px;border-bottom:2px dashed #F0EBE1;transition:background .2s ease;border-radius:8px;">
                <span style="flex:none;display:inline-flex;width:36px;height:36px;align-items:center;justify-content:center;background:#FCEFEA;border-radius:50%;color:#E76F51;"><svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17.5 19H9a7 7 0 1 1 6.71-9h1.79a4.5 4.5 0 1 1 0 9Z"/></svg></span>
                <span style="font-size:14px;line-height:1.9;color:#3D4A6B;"><b style="color:#1F2A44;">جنس:</b> ۱۰۰٪ ابری — اطلاعاتت روی سرور امن، نه روی یک کامپیوتر خاموش‌شدنی</span>
              </div>
              <div class="hv220710" style="display:flex;align-items:flex-start;gap:13px;padding:13px 18px;border-bottom:2px dashed #F0EBE1;transition:background .2s ease;border-radius:8px;">
                <span style="flex:none;display:inline-flex;width:36px;height:36px;align-items:center;justify-content:center;background:#FCEFEA;border-radius:50%;color:#E76F51;"><svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21.3 15.3a2.4 2.4 0 0 1 0 3.4l-2.6 2.6a2.4 2.4 0 0 1-3.4 0L2.7 8.7a2.41 2.41 0 0 1 0-3.4l2.6-2.6a2.41 2.41 0 0 1 3.4 0Z"/><path d="m14.5 12.5 2-2"/><path d="m11.5 9.5 2-2"/><path d="m8.5 6.5 2-2"/><path d="m17.5 15.5 2-2"/></svg></span>
                <span style="font-size:14px;line-height:1.9;color:#3D4A6B;"><b style="color:#1F2A44;">دوخت:</b> طراحی و ساخت: ایران</span>
              </div>
              <div class="hv220710" style="display:flex;align-items:flex-start;gap:13px;padding:13px 18px;border-bottom:2px dashed #F0EBE1;transition:background .2s ease;border-radius:8px;">
                <span style="flex:none;display:inline-flex;width:36px;height:36px;align-items:center;justify-content:center;background:#FCEFEA;border-radius:50%;color:#E76F51;"><svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><path d="M16 6 12 2 8 6"/><path d="M12 2v13"/></svg></span>
                <span style="font-size:14px;line-height:1.9;color:#3D4A6B;"><b style="color:#1F2A44;">اندازه:</b> از خیاطی تک‌نفره تا مزون چندخیاطه — قابل تنظیم</span>
              </div>
              <div class="hv220710" style="display:flex;align-items:flex-start;gap:13px;padding:13px 18px;border-bottom:2px dashed #F0EBE1;transition:background .2s ease;border-radius:8px;">
                <span style="flex:none;display:inline-flex;width:36px;height:36px;align-items:center;justify-content:center;background:#FCEFEA;border-radius:50%;color:#E76F51;"><svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12a9 9 0 1 1-9-9c2.52 0 4.93 1 6.74 2.74L21 8"/><path d="M21 3v5h-5"/></svg></span>
                <span style="font-size:14px;line-height:1.9;color:#3D4A6B;"><b style="color:#1F2A44;">دستور نگهداری:</b> نیازی نیست؛ بکاپ خودکار، آپدیت خودکار، پشتیبانی فارسی</span>
              </div>
              <!-- ⚠ placeholder: تاریخ و سازنده -->
              <div class="hv220710" style="display:flex;align-items:flex-start;gap:13px;padding:13px 18px;border-bottom:2px dashed #F0EBE1;transition:background .2s ease;border-radius:8px;">
                <span style="flex:none;display:inline-flex;width:36px;height:36px;align-items:center;justify-content:center;background:#FCEFEA;border-radius:50%;color:#E76F51;"><svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M8 2v4"/><path d="M16 2v4"/><rect width="18" height="18" x="3" y="4" rx="2"/><path d="M3 10h18"/></svg></span>
                <span style="font-size:14px;line-height:1.9;color:#3D4A6B;"><b style="color:#1F2A44;">تاریخ تولید:</b> [سال شروع — شمسی]</span>
              </div>
              <div class="hv220710" style="display:flex;align-items:flex-start;gap:13px;padding:13px 18px;transition:background .2s ease;border-radius:8px;">
                <span style="flex:none;display:inline-flex;width:36px;height:36px;align-items:center;justify-content:center;background:#FCEFEA;border-radius:50%;color:#E76F51;"><svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg></span>
                <span style="font-size:14px;line-height:1.9;color:#3D4A6B;"><b style="color:#1F2A44;">سازنده:</b> آتین نگار مانا</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- VALUES -->
  <section data-screen-label="اصول دوخت ما">
    <div style="max-width:1080px;margin-inline:auto;padding:26px 24px 40px;">
      <div data-rv style="opacity:0;transform:translateY(16px);transition:opacity .5s ease,transform .5s ease;text-align:center;margin-bottom:34px;">
        <h2 data-h2 style="font-size:34px;font-weight:700;color:#1F2A44;margin:0;">
          <span style="position:relative;white-space:nowrap;">اصول دوخت<svg width="100%" height="14" viewBox="0 0 140 14" preserveAspectRatio="none" style="position:absolute;inset-inline-start:0;bottom:-6px;overflow:visible;"><path d="M3 8 Q40 2 72 7 T137 6" stroke="#E76F51" stroke-width="6" fill="none" stroke-linecap="round" opacity=".85"/></svg></span>
          ما
        </h2>
      </div>
      <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(230px,1fr));gap:18px;">
        <div data-rv data-rv-delay="0" style="opacity:0;transform:translateY(16px);transition:opacity .5s ease,transform .5s ease;background:#fff;border:1px solid var(--color-border);border-radius:16px;padding:24px;box-shadow:var(--shadow-card);">
          <span style="display:inline-flex;width:44px;height:44px;align-items:center;justify-content:center;background:#FCEFEA;border-radius:50%;color:#E76F51;"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg></span>
          <h3 style="font-size:17px;font-weight:700;color:#1F2A44;margin:14px 0 7px;">سادگی، نه امکانات نمایشی</h3>
          <p style="font-size:14px;line-height:1.9;color:#3D4A6B;margin:0;">هر قابلیت باید گرهی واقعی از کار خیاط باز کند، وگرنه اضافه نمی‌شود.</p>
        </div>
        <div data-rv data-rv-delay="60" style="opacity:0;transform:translateY(16px);transition:opacity .5s ease,transform .5s ease;background:#fff;border:1px solid var(--color-border);border-radius:16px;padding:24px;box-shadow:var(--shadow-card);">
          <span style="display:inline-flex;width:44px;height:44px;align-items:center;justify-content:center;background:#FCEFEA;border-radius:50%;color:#E76F51;"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10Z"/></svg></span>
          <h3 style="font-size:17px;font-weight:700;color:#1F2A44;margin:14px 0 7px;">اطلاعات تو، مال توست</h3>
          <p style="font-size:14px;line-height:1.9;color:#3D4A6B;margin:0;">داده‌هایت امن نگه داشته می‌شود و بکاپ خودکار دارد.</p>
        </div>
        <div data-rv data-rv-delay="120" style="opacity:0;transform:translateY(16px);transition:opacity .5s ease,transform .5s ease;background:#fff;border:1px solid var(--color-border);border-radius:16px;padding:24px;box-shadow:var(--shadow-card);">
          <span style="display:inline-flex;width:44px;height:44px;align-items:center;justify-content:center;background:#FCEFEA;border-radius:50%;color:#E76F51;"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M7.9 20A9 9 0 1 0 4 16.1L2 22Z"/></svg></span>
          <h3 style="font-size:17px;font-weight:700;color:#1F2A44;margin:14px 0 7px;">پشتیبانی واقعی</h3>
          <p style="font-size:14px;line-height:1.9;color:#3D4A6B;margin:0;">آدم واقعی، به فارسی، جواب می‌دهد.</p>
        </div>
        <div data-rv data-rv-delay="180" style="opacity:0;transform:translateY(16px);transition:opacity .5s ease,transform .5s ease;background:#fff;border:1px solid var(--color-border);border-radius:16px;padding:24px;box-shadow:var(--shadow-card);">
          <span style="display:inline-flex;width:44px;height:44px;align-items:center;justify-content:center;background:#FCEFEA;border-radius:50%;color:#E76F51;"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12a9 9 0 1 1-9-9c2.52 0 4.93 1 6.74 2.74L21 8"/><path d="M21 3v5h-5"/></svg></span>
          <h3 style="font-size:17px;font-weight:700;color:#1F2A44;margin:14px 0 7px;">همیشه در حال بهترشدن</h3>
          <p style="font-size:14px;line-height:1.9;color:#3D4A6B;margin:0;">دوختک هر ماه کامل‌تر می‌شود، بی‌آنکه تو کاری کنی.</p>
        </div>
      </div>
    </div>
  </section>

  <!-- TIMELINE -->
  <section data-screen-label="مسیر دوختک">
    <div style="max-width:640px;margin-inline:auto;padding:26px 24px 40px;">
      <div data-rv style="opacity:0;transform:translateY(16px);transition:opacity .5s ease,transform .5s ease;text-align:center;margin-bottom:32px;">
        <h2 data-h2 style="font-size:34px;font-weight:700;color:#1F2A44;margin:0;">مسیر دوختک</h2>
      </div>
      <!-- ⚠ همه milestoneها placeholder — تاریخ‌ها و رویدادهای واقعی جایگزین شود -->
      <div style="position:relative;padding-inline-start:34px;">
        <div style="position:absolute;top:6px;bottom:6px;inset-inline-start:9px;width:2px;background-image:repeating-linear-gradient(180deg,#E76F51 0 9px,transparent 9px 16px);opacity:.55;"></div>
        <div data-rv style="opacity:0;transform:translateY(14px);transition:opacity .5s ease,transform .5s ease;position:relative;padding-bottom:26px;">
          <span data-dot style="position:absolute;inset-inline-start:-32px;top:4px;width:16px;height:16px;border-radius:50%;background:#fff;border:2px solid #E76F51;transition:background .4s ease;"></span>
          <div style="font-size:12.5px;font-weight:700;color:#D45A3D;margin-bottom:3px;">[تاریخ — شمسی]</div>
          <div style="font-size:15.5px;color:#3D4A6B;">جرقه اولیه و شروع ساخت دوختک</div>
        </div>
        <div data-rv data-rv-delay="60" style="opacity:0;transform:translateY(14px);transition:opacity .5s ease,transform .5s ease;position:relative;padding-bottom:26px;">
          <span data-dot style="position:absolute;inset-inline-start:-32px;top:4px;width:16px;height:16px;border-radius:50%;background:#fff;border:2px solid #E76F51;transition:background .4s ease;"></span>
          <div style="font-size:12.5px;font-weight:700;color:#D45A3D;margin-bottom:3px;">[تاریخ — شمسی]</div>
          <div style="font-size:15.5px;color:#3D4A6B;">اولین نسخه آزمایشی در دست چند خیاط</div>
        </div>
        <div data-rv data-rv-delay="120" style="opacity:0;transform:translateY(14px);transition:opacity .5s ease,transform .5s ease;position:relative;padding-bottom:26px;">
          <span data-dot style="position:absolute;inset-inline-start:-32px;top:4px;width:16px;height:16px;border-radius:50%;background:#fff;border:2px solid #E76F51;transition:background .4s ease;"></span>
          <div style="font-size:12.5px;font-weight:700;color:#D45A3D;margin-bottom:3px;">[تاریخ — شمسی]</div>
          <div style="font-size:15.5px;color:#3D4A6B;">[رویداد — placeholder]</div>
        </div>
        <div data-rv data-rv-delay="180" style="opacity:0;transform:translateY(14px);transition:opacity .5s ease,transform .5s ease;position:relative;padding-bottom:26px;">
          <span data-dot style="position:absolute;inset-inline-start:-32px;top:4px;width:16px;height:16px;border-radius:50%;background:#fff;border:2px solid #E76F51;transition:background .4s ease;"></span>
          <div style="font-size:12.5px;font-weight:700;color:#D45A3D;margin-bottom:3px;">امروز</div>
          <div style="font-size:15.5px;color:#3D4A6B;">دوختک آماده همراهی مزون توست</div>
        </div>
        <div data-rv data-rv-delay="240" style="opacity:0;transform:translateY(14px);transition:opacity .5s ease,transform .5s ease;position:relative;">
          <span data-dot style="position:absolute;inset-inline-start:-32px;top:4px;width:16px;height:16px;border-radius:50%;background:#fff;border:2px solid #E76F51;transition:background .4s ease;"></span>
          <div style="display:flex;align-items:center;gap:8px;font-size:12.5px;font-weight:700;color:#D45A3D;margin-bottom:3px;">
            فردا
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#E76F51" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="6" cy="6" r="3"/><path d="M8.12 8.12 12 12"/><path d="M20 4 8.12 15.88"/><circle cx="6" cy="18" r="3"/><path d="M14.8 14.8 20 20"/></svg>
          </div>
          <div style="font-size:15.5px;color:#3D4A6B;">قابلیت‌های بعدی در راه‌اند — هنوز در حال دوختیم</div>
        </div>
      </div>
    </div>
  </section>

  <!-- NAME STORY -->
  <section data-screen-label="اسم دوختک">
    <div data-rv style="opacity:0;transform:translateY(16px);transition:opacity .5s ease,transform .5s ease;max-width:860px;margin-inline:auto;padding:20px 24px 36px;">
      <div style="background:#fff;border:1px solid var(--color-border);border-radius:18px;padding:32px 30px;box-shadow:var(--shadow-card);display:flex;flex-wrap:wrap;align-items:center;gap:30px;">
        <div style="flex:0 0 auto;text-align:center;margin-inline:auto;">
          <div style="display:flex;align-items:center;gap:10px;justify-content:center;">
            <img src="/assets/images/Logo01-NEW.png" alt="" style="height:44px;">
            <b style="font-size:32px;color:#1F2A44;">دوختک</b>
          </div>
          <div style="height:2px;width:140px;margin:12px auto 0;background-image:repeating-linear-gradient(90deg,#E76F51 0 10px,transparent 10px 17px);"></div>
        </div>
        <div style="flex:1 1 320px;">
          <h2 style="font-size:21px;font-weight:700;color:#1F2A44;margin:0 0 10px;">اسم دوختک از کجا آمد؟</h2>
          <!-- ⚠ placeholder: قصه انتخاب اسم — متن نهایی از تیم دوختک. متن جعلی ننوشته‌ایم. -->
          <p style="font-size:15.5px;line-height:2;color:#6B7280;margin:0;font-style:normal;">[قصه انتخاب اسم دوختک — دو سه جمله، از زبان سازنده]</p>
        </div>
      </div>
    </div>
  </section>

  <!-- MAKER -->
  <section data-screen-label="پشت دوختک">
    <div data-rv style="opacity:0;transform:translateY(16px);transition:opacity .5s ease,transform .5s ease;max-width:860px;margin-inline:auto;padding:0 24px 40px;">
      <div style="display:flex;flex-wrap:wrap;gap:28px;align-items:center;">
        <!-- placeholder: تصویر سازنده / محیط کار (اختیاری) -->
        <div style="flex:0 1 240px;min-width:200px;aspect-ratio:4/5;border-radius:16px;background:linear-gradient(150deg,#EFEBE2,#E0D8C6);display:flex;align-items:center;justify-content:center;margin-inline:auto;">
          <span style="font-size:11.5px;color:#7d6b4f;background:rgba(255,255,255,.72);border-radius:999px;padding:4px 13px;text-align:center;">تصویر سازنده / محیط کار<br>(اختیاری)</span>
        </div>
        <div style="flex:1 1 380px;">
          <h2 data-h2 style="font-size:30px;font-weight:700;color:#1F2A44;margin:0 0 14px;">پشت دوختک کیست؟</h2>
          <!-- ⚠ placeholder: متن معرفی سازنده -->
          <p style="font-size:16.5px;line-height:2;color:#3D4A6B;margin:0 0 18px;">دوختک در [شهر] و با وسواس یک خیاط ساخته می‌شود. پشت آن آتین نگار مانا است — [یک جمله سابقه و انگیزه سازنده].</p>
          <a class="hv59f862" href="/contact" style="display:inline-flex;align-items:center;gap:8px;min-height:44px;color:#E76F51;font-weight:700;font-size:15.5px;">
            حرفی، پیشنهادی، سؤالی؟ مستقیم با خودمان در میان بگذار
            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="transform:scaleX(-1);"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
          </a>
        </div>
      </div>
    </div>
  </section>

  <!-- FINAL CTA -->
  <section id="cta" data-screen-label="سی‌تی‌ای نهایی">
    <div data-rv style="opacity:0;transform:translateY(18px);transition:opacity .5s ease,transform .5s ease;max-width:1000px;margin-inline:auto;padding:6px 24px 74px;">
      <div style="position:relative;background:#FCEFEA;border-radius:24px;padding:52px 28px;text-align:center;overflow:hidden;">
        <div style="position:absolute;inset:14px;border:2px dashed #E76F51;border-radius:16px;opacity:.55;pointer-events:none;"></div>
        <h2 data-h2 style="position:relative;font-size:32px;font-weight:700;color:#1F2A44;margin:0 0 12px;">حالا که ما را شناختی، بگذار دوختک هم مزون تو را بشناسد</h2>
        <div style="position:relative;display:flex;flex-wrap:wrap;justify-content:center;align-items:center;gap:18px;margin-top:22px;">
          <a class="hv791c00" href="/pricing" style="display:inline-flex;align-items:center;justify-content:center;min-height:52px;background:var(--color-primary);color:#fff;font-weight:700;font-size:17px;padding:14px 32px;border-radius:14px;box-shadow:0 10px 26px rgba(231,111,81,.34);transition:background .2s ease,transform .2s ease;">۱۰ روز رایگان شروع کن</a>
          <a class="hvd6c76f" href="/contact" style="display:inline-flex;align-items:center;min-height:44px;color:#1F2A44;font-weight:700;font-size:16px;">تماس با ما</a>
        </div>
      </div>
    </div>
  </section>

  <div id="footer" style="scroll-margin-top:96px;"><?php include __DIR__ . '/includes/site_footer.php'; ?></div>

</div>
<?php render_foot();
