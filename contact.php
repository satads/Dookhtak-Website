<?php
/**
 * Contact page — compact hero, routing cards, consultation-request form
 * and contact channels. Markup is the approved design, converted 1:1.
 * Contact info (phone, email, working hours) is DB-driven via the
 * settings table, like the footer. The form itself is handled in
 * /assets/js/page-contact.js (demo mode until Phase 5 wires the API).
 */
require_once __DIR__ . '/includes/layout.php';

$app_url = setting('app_url', 'https://app.dookhtak.ir');
$support_phone = setting('support_phone', '۰۲۱-۱۲۳۴۵۶۷۸');
$support_email = setting('support_email', 'hello@dookhtak.ir');
$working_hours = setting('working_hours', 'شنبه تا پنجشنبه، ۹ تا ۱۸');

// tel: href derived from the display number (Latin digits, leading 0 → +98
// so the default reproduces the design's tel:+982112345678 exactly).
$tel_digits = preg_replace('/[^0-9+]/', '', en_digits($support_phone));
if (strpos($tel_digits, '0') === 0) {
    $tel_digits = '+98' . substr($tel_digits, 1);
}
$support_phone_tel = 'tel:' . $tel_digits;

render_head([
    'title' => 'تماس با دوختک | دوختک',
    'description' => 'برای مشاوره رایگان و شروع کار با دوختک با ما در تماس باشید.',
    'css' => '/assets/css/page-contact.css',
    'js' => '/assets/js/page-contact.js',
]);
?>
<div dir="rtl" lang="fa" style="position:relative;min-height:100vh;background-color:#FAF8F4;background-image:linear-gradient(#EDEAE3 1px,transparent 1px),linear-gradient(90deg,#EDEAE3 1px,transparent 1px);background-size:46px 46px;overflow-x:hidden;line-height:1.6;">

  <?php include __DIR__ . '/includes/site_header.php'; ?>

  <!-- COMPACT HERO -->
  <header data-screen-label="هیرو تماس">
    <div id="heroWrap" style="max-width:760px;margin-inline:auto;padding:128px 24px 34px;text-align:center;">
      <h1 id="heroTitle" data-hero style="opacity:0;transform:translateY(12px);transition:opacity .5s ease,transform .5s ease;font-size:44px;line-height:1.5;font-weight:700;color:#1F2A44;margin:0;letter-spacing:-.5px;">
        حرفت را
        <span style="position:relative;white-space:nowrap;">می‌شنویم<svg width="100%" height="14" viewBox="0 0 140 14" preserveAspectRatio="none" style="position:absolute;inset-inline-start:0;bottom:-6px;overflow:visible;"><path d="M3 8 Q40 2 72 7 T137 6" stroke="#E76F51" stroke-width="6" fill="none" stroke-linecap="round" opacity=".85"/></svg></span>
      </h1>
      <p data-hero style="opacity:0;transform:translateY(12px);transition:opacity .5s ease,transform .5s ease;font-size:17px;line-height:2;color:#3D4A6B;margin:26px auto 0;max-width:500px;">سؤال، مشاوره، پیشنهاد یا هر حرف دیگری — مستقیم با خودمان در میان بگذار.</p>
    </div>
  </header>

  <!-- ROUTING CARDS -->
  <section data-screen-label="مسیربندی">
    <div style="max-width:1000px;margin-inline:auto;padding:8px 24px 14px;display:flex;flex-wrap:wrap;gap:20px;">
      <div data-rv style="opacity:0;transform:translateY(16px);transition:opacity .5s ease,transform .5s ease;flex:1 1 320px;display:flex;">
        <div style="flex:1;display:flex;flex-direction:column;background:#fff;border:1px solid var(--color-border);border-radius:18px;padding:26px;box-shadow:var(--shadow-card);">
          <span style="display:inline-flex;width:46px;height:46px;align-items:center;justify-content:center;background:#FCEFEA;border-radius:50%;color:#E76F51;"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.13.96.36 1.9.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.91.34 1.85.57 2.81.7A2 2 0 0 1 22 16.92z"/></svg></span>
          <h2 style="font-size:20px;font-weight:700;color:#1F2A44;margin:16px 0 8px;">می‌خواهی دوختک را بشناسی؟</h2>
          <p style="font-size:15px;line-height:1.9;color:#3D4A6B;margin:0 0 20px;flex:1;">شماره‌ات را بگذار، خودمان تماس می‌گیریم و رایگان راهنمایی‌ات می‌کنیم.</p>
          <button class="hv791c00" type="button" data-go-form style="display:flex;align-items:center;justify-content:center;min-height:50px;background:var(--color-primary);color:#fff;border:none;font-weight:700;font-size:15.5px;padding:13px 24px;border-radius:13px;box-shadow:0 8px 22px rgba(231,111,81,.3);transition:background .2s ease,transform .2s ease;">درخواست مشاوره رایگان</button>
        </div>
      </div>
      <div data-rv data-rv-delay="70" style="opacity:0;transform:translateY(16px);transition:opacity .5s ease,transform .5s ease;flex:1 1 320px;display:flex;">
        <div style="flex:1;display:flex;flex-direction:column;background:#fff;border:1px solid var(--color-border);border-radius:18px;padding:26px;box-shadow:var(--shadow-card);">
          <span style="display:inline-flex;width:46px;height:46px;align-items:center;justify-content:center;background:#EEF1F7;border-radius:50%;color:#1F2A44;"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 9a2 2 0 0 1-2 2H6l-4 4V4a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2z"/><path d="M18 9h2a2 2 0 0 1 2 2v11l-4-4h-6a2 2 0 0 1-2-2v-1"/></svg></span>
          <h2 style="font-size:20px;font-weight:700;color:#1F2A44;margin:16px 0 8px;">مشتری دوختک هستی؟</h2>
          <p style="font-size:15px;line-height:1.9;color:#3D4A6B;margin:0 0 20px;flex:1;">سریع‌ترین راه پشتیبانی، سیستم تیکت داخل خود اپ است — از منوی پشتیبانی اپ، تیکت بفرست تا در اولین فرصت پاسخ بگیری.</p>
          <a class="hv363ed5" href="<?= e($app_url) ?>" target="_blank" rel="noopener" style="display:flex;align-items:center;justify-content:center;min-height:50px;background:transparent;color:#1F2A44;font-weight:700;font-size:15.5px;padding:13px 24px;border-radius:13px;border:1.5px solid #1F2A44;transition:background .2s ease,color .2s ease;">ورود به اپ دوختک</a>
        </div>
      </div>
    </div>
  </section>

  <!-- MAIN: FORM + CONTACT WAYS -->
  <section data-screen-label="فرم مشاوره" id="consult" style="scroll-margin-top:110px;">
    <div id="mainRow" style="max-width:1000px;margin-inline:auto;padding:26px 24px 20px;display:flex;flex-wrap:wrap;gap:24px;align-items:flex-start;">

      <!-- برگه درخواست مشاوره -->
      <div data-rv style="opacity:0;transform:translateY(16px);transition:opacity .5s ease,transform .5s ease;flex:1.4 1 380px;">
        <div style="position:relative;background:#fff;border:1px solid var(--color-border);border-radius:18px;box-shadow:var(--shadow-card);overflow:hidden;">
          <!-- سربرگ برگه -->
          <div style="display:flex;align-items:center;justify-content:space-between;gap:10px;padding:16px 24px;border-bottom:2px dashed #F0EBE1;background:#FBFAF7;">
            <div style="display:flex;align-items:center;gap:9px;">
              <img src="/assets/images/Logo01-NEW.png" alt="" style="height:24px;">
              <b style="font-size:14.5px;color:#1F2A44;">برگه درخواست مشاوره — دوختک</b>
            </div>
            <span style="font-size:11px;color:#9a9587;">رایگان</span>
          </div>

          <!-- Form (initial state: visible; swapped for the success panel by page JS) -->
          <form id="consultForm" novalidate="novalidate" style="padding:22px 24px 26px;display:flex;flex-direction:column;gap:20px;">
            <!-- honeypot -->
            <input type="text" name="company" tabindex="-1" autocomplete="off" aria-hidden="true" style="position:absolute;inset-inline-start:-9999px;width:1px;height:1px;opacity:0;pointer-events:none;">

            <div>
              <label for="f-name" style="display:flex;align-items:center;gap:9px;font-size:14px;font-weight:700;color:#1F2A44;margin-bottom:8px;">
                <span style="flex:none;display:inline-flex;width:24px;height:24px;align-items:center;justify-content:center;background:#FCEFEA;color:#D45A3D;border-radius:50%;font-size:12px;">۱</span>
                نام و نام خانوادگی <b style="color:#E76F51;">*</b>
              </label>
              <input id="f-name" type="text" aria-describedby="e-name" style="width:100%;min-height:48px;padding:11px 14px;font-family:inherit;font-size:16px;color:#1F2A44;background:#FAF8F4;border:1px solid var(--color-border);border-radius:11px;outline:none;transition:border-color .2s ease,box-shadow .2s ease;">
              <div id="e-name" style="display:none;font-size:12.5px;color:#BB2D3B;margin-top:6px;"></div>
            </div>

            <div>
              <label for="f-phone" style="display:flex;align-items:center;gap:9px;font-size:14px;font-weight:700;color:#1F2A44;margin-bottom:8px;">
                <span style="flex:none;display:inline-flex;width:24px;height:24px;align-items:center;justify-content:center;background:#FCEFEA;color:#D45A3D;border-radius:50%;font-size:12px;">۲</span>
                شماره موبایل <b style="color:#E76F51;">*</b>
              </label>
              <input id="f-phone" type="tel" inputmode="tel" dir="ltr" placeholder="۰۹۱۲۳۴۵۶۷۸۹" aria-describedby="e-phone" style="width:100%;min-height:48px;padding:11px 14px;font-family:inherit;font-size:16px;color:#1F2A44;background:#FAF8F4;border:1px solid var(--color-border);border-radius:11px;outline:none;text-align:end;transition:border-color .2s ease,box-shadow .2s ease;">
              <div id="e-phone" style="display:none;font-size:12.5px;color:#BB2D3B;margin-top:6px;"></div>
            </div>

            <div>
              <label for="f-biz" style="display:flex;align-items:center;gap:9px;font-size:14px;font-weight:700;color:#1F2A44;margin-bottom:8px;">
                <span style="flex:none;display:inline-flex;width:24px;height:24px;align-items:center;justify-content:center;background:#FCEFEA;color:#D45A3D;border-radius:50%;font-size:12px;">۳</span>
                نوع فعالیت
              </label>
              <select id="f-biz" style="width:100%;min-height:48px;padding:11px 14px;font-family:inherit;font-size:15.5px;color:#1F2A44;background:#FAF8F4;border:1px solid var(--color-border);border-radius:11px;outline:none;appearance:auto;">
                <option value="">انتخاب کن…</option>
                <option value="خیاطی زنانه">خیاطی زنانه</option>
                <option value="خیاطی مردانه">خیاطی مردانه</option>
                <option value="مزون">مزون</option>
                <option value="تعمیرات پوشاک">تعمیرات پوشاک</option>
                <option value="سایر">سایر</option>
              </select>
            </div>

            <div>
              <label for="f-subject" style="display:flex;align-items:center;gap:9px;font-size:14px;font-weight:700;color:#1F2A44;margin-bottom:8px;">
                <span style="flex:none;display:inline-flex;width:24px;height:24px;align-items:center;justify-content:center;background:#FCEFEA;color:#D45A3D;border-radius:50%;font-size:12px;">۴</span>
                موضوع
              </label>
              <select id="f-subject" style="width:100%;min-height:48px;padding:11px 14px;font-family:inherit;font-size:15.5px;color:#1F2A44;background:#FAF8F4;border:1px solid var(--color-border);border-radius:11px;outline:none;appearance:auto;">
                <option value="">انتخاب کن…</option>
                <option value="مشاوره خرید">مشاوره خرید</option>
                <option value="سؤال درباره امکانات">سؤال درباره امکانات</option>
                <option value="پیشنهاد">پیشنهاد</option>
                <option value="همکاری">همکاری</option>
                <option value="سایر">سایر</option>
              </select>
            </div>

            <div>
              <label for="f-msg" style="display:flex;align-items:center;gap:9px;font-size:14px;font-weight:700;color:#1F2A44;margin-bottom:8px;">
                <span style="flex:none;display:inline-flex;width:24px;height:24px;align-items:center;justify-content:center;background:#FCEFEA;color:#D45A3D;border-radius:50%;font-size:12px;">۵</span>
                پیام
              </label>
              <textarea id="f-msg" rows="4" placeholder="اگر توضیحی داری بنویس (اختیاری)" style="width:100%;min-height:110px;padding:12px 14px;font-family:inherit;font-size:15.5px;line-height:1.9;color:#1F2A44;background:#FAF8F4;border:1px solid var(--color-border);border-radius:11px;outline:none;resize:vertical;"></textarea>
            </div>

            <div id="sendErr" role="alert" style="display:none;font-size:13.5px;color:#BB2D3B;background:#FDF0F1;border:1px solid #F3CDD1;border-radius:11px;padding:11px 14px;">ارسال ناموفق بود — دوباره تلاش کن یا مستقیم تماس بگیر: <a href="<?= e($support_phone_tel) ?>" dir="ltr" style="font-weight:700;color:#BB2D3B;text-decoration:underline;"><?= e($support_phone) ?></a></div>

            <div>
              <button class="hvffc1fa" id="consultSubmit" type="submit" style="width:100%;display:flex;align-items:center;justify-content:center;gap:10px;min-height:54px;background:var(--color-primary);color:#fff;border:none;font-weight:700;font-size:16.5px;padding:14px;border-radius:13px;box-shadow:0 10px 26px rgba(231,111,81,.3);opacity:1;cursor:pointer;transition:background .2s ease,transform .2s ease;">
                <span id="btnSpinner" style="display:none;width:18px;height:18px;border:2.5px solid rgba(255,255,255,.4);border-top-color:#fff;border-radius:50%;animation:spin .7s linear infinite;"></span>
                <span id="btnLabel">ثبت درخواست مشاوره</span>
              </button>
              <div style="text-align:center;font-size:13px;color:#6B7280;margin-top:12px;">در ساعات کاری، معمولاً همان روز تماس می‌گیریم.</div>
            </div>
          </form>

          <!-- حالت موفق (initial state: hidden; shown by page JS after a successful send) -->
          <div id="consultSuccess" style="display:none;position:relative;padding:52px 28px;text-align:center;opacity:0;transform:scale(.96);transition:opacity .45s ease,transform .45s ease;">
            <div style="position:absolute;inset:12px;border:2px dashed #E76F51;border-radius:12px;opacity:.5;pointer-events:none;"></div>
            <span style="position:relative;display:inline-flex;width:64px;height:64px;align-items:center;justify-content:center;background:#FCEFEA;border-radius:50%;color:#E76F51;margin-bottom:18px;">
              <svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
            </span>
            <h2 style="position:relative;font-size:23px;font-weight:700;color:#1F2A44;margin:0 0 8px;">درخواستت ثبت شد.</h2>
            <p style="position:relative;font-size:15.5px;color:#3D4A6B;margin:0;">به‌زودی تماس می‌گیریم.</p>
          </div>
        </div>
      </div>

      <!-- راه‌های تماس -->
      <div data-rv data-rv-delay="80" style="opacity:0;transform:translateY(16px);transition:opacity .5s ease,transform .5s ease;flex:1 1 280px;">
        <div style="background:#fff;border:1px solid var(--color-border);border-radius:18px;padding:24px;box-shadow:var(--shadow-card);">
          <h2 style="font-size:18px;font-weight:700;color:#1F2A44;margin:0 0 18px;">راه‌های تماس</h2>
          <div style="display:flex;flex-direction:column;gap:16px;">
            <!-- Contact info comes from the settings table (support_phone, working_hours, support_email). -->
            <a class="hvd6c76f" href="<?= e($support_phone_tel) ?>" style="display:flex;gap:12px;align-items:flex-start;">
              <span style="flex:none;display:inline-flex;width:40px;height:40px;align-items:center;justify-content:center;background:#FCEFEA;border-radius:50%;color:#E76F51;"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.13.96.36 1.9.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.91.34 1.85.57 2.81.7A2 2 0 0 1 22 16.92z"/></svg></span>
              <span>
                <span style="display:block;font-size:14.5px;font-weight:700;color:#1F2A44;" dir="ltr"><?= e($support_phone) ?></span>
                <span style="display:block;font-size:12.5px;color:#6B7280;margin-top:2px;"><?= e($working_hours) ?></span>
              </span>
            </a>
            <a class="hvd6c76f" href="mailto:<?= e($support_email) ?>" style="display:flex;gap:12px;align-items:flex-start;">
              <span style="flex:none;display:inline-flex;width:40px;height:40px;align-items:center;justify-content:center;background:#FCEFEA;border-radius:50%;color:#E76F51;"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg></span>
              <span>
                <span style="display:block;font-size:14.5px;font-weight:700;color:#1F2A44;" dir="ltr"><?= e($support_email) ?></span>
                <span style="display:block;font-size:12.5px;color:#6B7280;margin-top:2px;">برای مکاتبات رسمی و همکاری</span>
              </span>
            </a>
            <!-- شبکه‌های اجتماعی: کانال‌ها هنوز ایجاد نشده‌اند — فعال‌سازی بعد از ساخت کانال‌ها
            <div style="display:flex;gap:10px;">
              <a href="[INSTAGRAM-URL]" aria-label="اینستاگرام">…</a>
              <a href="[TELEGRAM-URL]" aria-label="تلگرام">…</a>
              <a href="[BALE-URL]" aria-label="بله">…</a>
            </div>
            -->
          </div>
        </div>
        <div style="display:flex;gap:9px;align-items:flex-start;font-size:12.5px;color:#6B7280;background:#FCEFEA;border-radius:12px;padding:12px 14px;margin-top:14px;">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#D45A3D" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex:none;margin-top:3px;"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4"/><path d="M12 8h.01"/></svg>
          مشتری دوختک هستی؟ تیکت داخل اپ سریع‌ترین راه است.
        </div>
      </div>
    </div>
  </section>

  <!-- QUICK ANSWERS -->
  <section data-screen-label="شاید جوابت همین‌جا باشد">
    <div style="max-width:1000px;margin-inline:auto;padding:30px 24px 70px;">
      <h2 data-rv style="opacity:0;transform:translateY(14px);transition:opacity .5s ease,transform .5s ease;font-size:20px;font-weight:700;color:#1F2A44;margin:0 0 18px;">شاید جوابت همین‌جا باشد</h2>
      <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(240px,1fr));gap:16px;">
        <a class="hvd7433b" href="/#faq" data-rv style="opacity:0;transform:translateY(14px);transition:opacity .5s ease,transform .5s ease,border-color .2s ease;display:flex;align-items:center;gap:13px;background:#fff;border:1px solid var(--color-border);border-radius:14px;padding:18px 20px;min-height:64px;">
          <span style="flex:none;display:inline-flex;width:40px;height:40px;align-items:center;justify-content:center;background:#FCEFEA;border-radius:50%;color:#E76F51;"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/><path d="M12 17h.01"/></svg></span>
          <b style="flex:1;font-size:15px;color:#1F2A44;">سؤالات متداول</b>
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#E76F51" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="flex:none;"><path d="m15 18-6-6 6-6"/></svg>
        </a>
        <a class="hvd7433b" href="/pricing" data-rv data-rv-delay="60" style="opacity:0;transform:translateY(14px);transition:opacity .5s ease,transform .5s ease,border-color .2s ease;display:flex;align-items:center;gap:13px;background:#fff;border:1px solid var(--color-border);border-radius:14px;padding:18px 20px;min-height:64px;">
          <span style="flex:none;display:inline-flex;width:40px;height:40px;align-items:center;justify-content:center;background:#FCEFEA;border-radius:50%;color:#E76F51;"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v20"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg></span>
          <b style="flex:1;font-size:15px;color:#1F2A44;">تعرفه‌ها</b>
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#E76F51" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="flex:none;"><path d="m15 18-6-6 6-6"/></svg>
        </a>
        <a class="hvd7433b" href="/tutorials" data-rv data-rv-delay="120" style="opacity:0;transform:translateY(14px);transition:opacity .5s ease,transform .5s ease,border-color .2s ease;display:flex;align-items:center;gap:13px;background:#fff;border:1px solid var(--color-border);border-radius:14px;padding:18px 20px;min-height:64px;">
          <span style="flex:none;display:inline-flex;width:40px;height:40px;align-items:center;justify-content:center;background:#FCEFEA;border-radius:50%;color:#E76F51;"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg></span>
          <b style="flex:1;font-size:15px;color:#1F2A44;">آموزش کار با دوختک</b>
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#E76F51" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="flex:none;"><path d="m15 18-6-6 6-6"/></svg>
        </a>
      </div>
    </div>
  </section>

  <div id="footer" style="scroll-margin-top:96px;"><?php include __DIR__ . '/includes/site_footer.php'; ?></div>

</div>
<?php render_foot();
