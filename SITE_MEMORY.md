# SITE_MEMORY — Dookhtak Marketing Website

این فایل حافظه اختصاصی «وب‌سایت معرفی دوختک» است. این پروژه کاملاً مستقل است و هیچ ارتباطی با AppDookhtak-Client، PanelDookhtak-Master یا فایل‌های حافظه آن پروژه‌ها ندارد.

---

## GLOBAL RULES (تکرار در همه فازها — غیرقابل مذاکره)

- **استقلال پروژه:** فقط داخل همین پوشه پروژه؛ هیچ فایل بیرونی خوانده یا تغییر داده نمی‌شود.
- **استک:** هاست اشتراکی cPanel ایران — PHP 8.x، MySQL (cPanel)، Apache/.htaccess، بدون Composer و بدون Node روی سرور. PHP خام، PDO MySQL utf8mb4، همه کوئری‌ها prepared statement.
- **اسکیما:** `database/schema.sql` برای ایمپورت دستی در phpMyAdmin — هرگز از کد اجرا نمی‌شود؛ هر تغییر بعدی = بازتولید کل فایل + اعلام صریح.
- **زبان و UI:** فارسی کامل (fa)، RTL، ارقام فارسی، تاریخ جلالی. بدون ایموجی؛ فقط آیکون‌های Lucide. متن UI فارسی، کامنت کد انگلیسی.
- **طراحی:** خروجی دیزاین (ZIP) تأیید نهایی است — pixel-for-pixel حفظ شود؛ فقط بک‌اند اضافه می‌کنیم. توکن‌ها: coral `#E76F51`، coral-hover `#D45A3D`، coral-soft `#FCEFEA`، navy `#1F2A44`، body `#3D4A6B`، bg `#FAF8F4`، border `#E8E6E1`، admin work-area `#F7F6F3`.
- **سیستم دارایی مرکزی:** فونت/توکن/ریست/کامپوننت مشترک فقط در `assets/css/site.css`؛ رفتارهای مشترک فقط در `assets/js/site.js`؛ هدر/فوتر فقط `includes/site_header.php` / `includes/site_footer.php` با یک آرایه منوی واحد (`includes/nav.php`). هر صفحه حداکثر یک CSS و یک JS اختصاصی کوچک.
- **مدیا:** ویدئو همیشه embed آپارات؛ آپلود فقط تصویر (حداکثر ۲MB).
- **انضباط دامنه:** هر فاز فقط محدوده خودش؛ بدون فیچر اضافه (نقش کاربر، کامنت، خبرنامه، آنالیتیکس، دارک‌مود = ممنوع).
- **بدون CDN خارجی (قانون افزوده‌شده هنگام تأیید فاز ۳ — دائمی):** هیچ asset خارجی در هیچ‌جای پروژه (عمومی و ادمین) مجاز نیست — همه‌چیز self-hosted از همین هاست cPanel (سایت در ایران سرو می‌شود). Quill در `assets/vendor/quill/`؛ آیکون‌های lucide همگی SVG inline (هیچ fetch آیکونی وجود ندارد)؛ فونت‌ها محلی. تنها استثنا: iframe ویدئوهای آپارات در محتوای آموزش. لینک‌های ناوبری خروجی (پروفایل سوشال، دکمه‌های اشتراک t.me/wa.me از دیزاین) asset نیستند و مجازند. چک خودکار در suite رگرسیون (qa/audit.sh چک ۱۱) هر مرجع خارجی را FAIL می‌کند.

---

## Phase Tracker

| فاز | وضعیت | تاریخ تکمیل | تگ گیت | خلاصه تست |
|-----|--------|-------------|---------|------------|
| ۱ — Foundation & Static Conversion | COMPLETE | 2026-07-03 | `phase-1-complete` | ممیزی خودکار ۴۰/۴۰ پاس + اسکرین‌شات ۸ صفحه دسکتاپ/موبایل مطابق دیزاین + هارنس SPA آموزش‌ها ۳۷/۳۷ |
| ۲ — Admin Shell, Auth, Settings & Media | COMPLETE | 2026-07-03 | `phase-2-complete` | چک‌لیست پذیرش ۵/۵ با شواهد اجرایی + رگرسیون ۴۰/۴۰ + دیف بصری ۱۶/۱۶ صفر پیکسل |
| ۳ — Blog (Admin + Public SSR) | COMPLETE | 2026-07-03 | `phase-3-complete` | پذیرش ۵/۵ با E2E مرورگری + رگرسیون ۴۱/۴۱ + دیف بصری (۳ baseline عمداً به‌روزرسانی شد) |
| ۴ — Tutorials Structured Builder | PENDING | — | — | — |
| ۵ — Pricing, FAQ, Testimonials, Contact & Mother Adapter | PENDING | — | — | — |
| ۶ — SEO, Performance & Delivery | PENDING | — | — | — |

قانون: هر فاز فقط با پیام صریح «APPROVED — proceed to phase N+1» باز می‌شود. مرجع محدوده هر فاز: `BUILD_PLAN.md` (منبع واحد حقیقت).

نقطه‌های بازگشت (تگ‌ها local هستند — پروکسی گیت این محیط push تگ را نمی‌پذیرد؛ هش‌ها مرجع‌اند): فاز ۱ = `4a27345`، فاز ۲ = `2c59af1`، فاز ۳ = `3a58faf`.

**زیرساخت QA بصری:** `qa/screenshot.js` (Playwright + Chromium محلی) از ۸ صفحه عمومی اسکرین‌شات دسکتاپ (۱۴۴۰) و موبایل (۳۹۰) می‌گیرد؛ baseline فاز ۱ در `qa/baselines/` (git-ignored). بعد از هر فاز: `node qa/screenshot.js current` سپس `node qa/compare.js` — هر تفاوت خارج از محدوده فاز = شکست رگرسیون.

---

## ساختار پروژه

```
/            صفحات عمومی PHP (index, features, pricing, tutorials, blog, blog-post, about, contact, 404) + router.php (فقط dev)
/assets      css/site.css + css/page-*.css ، js/site.js + js/page-*.js ، fonts (IRANSans woff2) ، images
/includes    config(.example), db, helpers, auth, cache, layout, nav, site_header, site_footer, blog_sample_data (موقت فاز ۱)
/admin       placeholder (فاز ۲)
/api         (فازهای بعد)
/uploads     فقط تصویر: /uploads/images/YYYY/MM
/database    schema.sql + create_admin.php (فقط CLI — بعد از استقرار حذف شود)
/cache       کش فایل (deny از وب)
/content     فرگمنت‌های HTML آموزش‌ها (استاتیک فاز ۱ — فاز ۴ با API جایگزین می‌شود)
```

---

## فاز ۱ — انجام شد (تاریخ: ۲۰۲۶-۰۷-۰۳)

### موجودی خروجی دیزاین
- ZIP شامل: ۸ صفحه عمومی + ۱ طرح ادمین (`Dookhtak Admin.dc.html` — مرجع فاز ۲)، `SiteHeader/SiteFooter` (کامپوننت مرکزی)، `site.css` (توکن‌ها)، `site-config.js` (منو)، `blog-posts.js` (۹ مقاله نمونه)، `tutorials-data.js` (۸ دسته + ۲۶ آموزش + مسیر شروع سریع)، ۲۷ فرگمنت `content/*.html`، فونت IRANSans (woff) و لوگو.
- فرمت خروجی: قالب کامپوننتی «DCLogic» با `{{ }}`، `sc-for`، `sc-if`، `style-hover/style-active` و اسکریپت منطق در انتهای هر فایل.

### تصمیم‌های کلیدی تبدیل
1. **hover/active → کلاس CSS:** مبدل مکانیکی (Node، فقط ابزار build محلی) هر ترکیب یکتا را به کلاس `hvXXXXXX` (هش md5) تبدیل کرد؛ قوانین با `!important` تا استایل inline را شکست دهند. ترکیب‌های چندصفحه‌ای → site.css؛ تک‌صفحه‌ای → page-*.css.
2. **applyResponsive → media query:** منطق ریسپانسیو JS خروجی (پهنای ۹۰۰/۱۱۰۰/۱۰۲۴) به media query در CSS صفحه منتقل شد (مقادیر دسکتاپ inline هستند؛ موبایل با `!important` override می‌شود). موارد نیازمند اندازه‌گیری (ایستگاه‌های صفحه امکانات) در JS ماند. `[data-desktop-only]`/`[data-mobile-only]` و جمع‌شدن نوبار ≥1100px مرکزی در site.css.
3. **رفتارهای مشترک در site.js (`window.DK`):** drawer موبایل + سایه اسکرول هدر، موتور reveal (`data-rv` با threshold/tick قابل تنظیم per-page)، استگر hero (`data-hero`)، آکاردئون FAQ (قرارداد `data-faq-item/btn/icon/panel`، تک‌باز)، toast، `prefers-reduced-motion` مرکزی.
4. **حلقه‌های sc-for →** foreach سرورساید PHP (داده استاتیک فاز ۱ آینه seedهای دیتابیس). استثنا: SPA آموزش‌ها کلاینت‌ساید ماند (وفادار به خروجی؛ فاز ۴ به API وصل می‌شود) با `assets/js/tutorials-data.js` (ماژول ES).
5. **هدر/فوتر PHP:** active-state منو سرورساید (تشخیص از URI). فوتر: تلفن/ایمیل/سوشال‌ها (با تاگل enabled و استخراج هندل از URL) و enamad_code از جدول `settings`؛ سال کپی‌رایت جلالی داینامیک (دیزاین ۱۴۰۴ هاردکد داشت — تصمیم: داینامیک).
6. **فونت:** woff خروجی به woff2 تبدیل شد (fontTools)؛ `@font-face` فقط در site.css، `font-display: swap`.
7. **CTAهای `href="#"`** (شروع رایگان/خرید) → `setting('app_url')`. انکرهای `#cta`/`#faq` دست‌نخورده.
8. **URLهای تمیز:** .htaccess برای Apache؛ `router.php` فقط برای `php -S` محلی (وب‌سرور داخلی PHP .htaccess را نمی‌خواند). مسیرها: `/features` … `/blog/{slug}` → `blog-post.php?slug=`.
9. **بلاگ فاز ۱:** آرشیو SSR از `includes/blog_sample_data.php` (فیلتر چیپ کلاینت‌ساید نمایشی)؛ صفحه مقاله = قالب استاتیک نمونه (`pricing-mistakes`) برای هر slug — فاز ۳ SSR کامل + ۴۰۴ برای slug ناموجود.
10. **کانونیکال:** non-www (`dookhtak.ir`) — در .htaccess اعمال شده؛ ریدایرکت HTTPS حاضر ولی کامنت (تاگل مستند).
11. **صفحه ۴۰۴ فارسی** (`404.php`) ساخته شد (router و فاز ۳ استفاده می‌کنند).
12. **seedهای آموزش:** ردیف‌های ۲۷ آموزش با متادیتا seed شدند؛ `content_json='[]'` — فاز ۴ فرگمنت‌های نمونه را به JSON ساختاریافته مهاجرت می‌دهد. `video_type='aparat'` برای آیتم‌های دارای ویدئو (embed خالی تا فاز ۴). آیکون‌های دسته به نام Lucide نگاشت شدند (rocket, user, clipboard-list, image, message-circle, credit-card, receipt, settings).
13. **فرم تماس فاز ۱:** رفتار دمو (DEMO_MODE=true در page-contact.js با قرارداد `submitContactForm`) — فاز ۵ به `api/contact.php` وصل می‌کند. ساعت پاسخ‌گویی/تلفن/ایمیل صفحه تماس از settings.
14. **کش صفحات:** زیرساخت در `includes/cache.php` (serve/start/flush) — `CACHE_ENABLED=false` تا فاز ۶.
15. **style-focus:** خروجی دیزاین علاوه بر hover/active، در فرم‌ها `style-focus` داشت (تماس ×۵، جستجوی آموزش ×۲، ادمین ×۲۳ برای فاز ۲). به قواعد `:focus` در CSS صفحه تبدیل شد (page-contact.css، page-tutorials.css).
16. **امنیت پایه:** سشن سخت‌شده (httponly, SameSite=Lax, secure-on-HTTPS, regenerate)، rate-limit ورود (۵ خطا/IP/۱۵ دقیقه در `login_attempts`)، CSRF helpers، deny وب برای includes/database/cache، غیرفعال‌سازی PHP در uploads، هدرهای امنیتی.

### دیتابیس
- `database/schema.sql` — کل اسکیما برای همه فازها + seed همه محتوای placeholder (۹ پست، ۲۶ آموزش، ۱۴ FAQ، ۳ نظر مشتری، ۱۴ مقدار تعرفه، تنظیمات، seo_pages/seo_settings). تست ایمپورت روی MariaDB سالم بود.
- قیمت‌ها در `pricing_values` به‌صورت عدد لاتین (تومان) ذخیره می‌شوند؛ فرمت فارسی هنگام رندر (`fa_number_format`، جداکننده `٬`). «ماهی X تومان» = `intdiv(total, months*1000)*1000`.
- `create_admin.php` فقط CLI (روی HTTP با 403 می‌میرد) — بعد از ساخت ادمین production حذف شود.

### قرارداد API مرکزی JS (window.DK)
`DK.reduce` ، `DK.faNum(n)` ، `DK.hero(startDelay, step)` ، `DK.reveal({threshold,tick,initialDelay,onScroll,onReveal})` ، `DK.showAll()` ، `DK.faq(rootSelector)` ، `DK.toast(msg)` ، `DK.onResize(fn)`. هدر خودکار init می‌شود (`data-drawer-open/close`، `#navPill`، `#dkDrawer`، `#dkDrawerOverlay`).

### راه‌اندازی محلی (dev)
1. `cp includes/config.example.php includes/config.php` و مقادیر DB.
2. ایمپورت `database/schema.sql` در دیتابیس خالی.
3. `php -S localhost:8080 router.php` (router فقط برای dev؛ روی Apache نیازی نیست).
4. محدودیت‌های `php -S`: .htaccess اجرا نمی‌شود → هدرهای امنیتی، deny پوشه‌ها و rewrite ها را باید روی Apache واقعی جدا تست کرد (router.php رفتار rewrite و deny را شبیه‌سازی می‌کند).

### نتیجه پذیرش فاز ۱
- اسکریپت ممیزی (۴۰ چک): همه پاس — lint همه PHPها، رندر ۲۰۰ همه مسیرها، ۴۰۴ مسیر ناشناخته، صفر باقی‌مانده سینتکس قالب، ممیزی دارایی مرکزی (font-face فقط site.css، توکن‌ها فقط site.css، هر صفحه ≤۲ CSS و ≤۲ JS)، فوتر زنده از DB (تغییر settings → بلافاصله در خروجی)، ایمپورت schema در DB تازه (۱۵ جدول)، create_admin روی HTTP=403، مسیرهای حساس=403، active-state منو (exact و prefix برای /blog/{slug})، فرگمنت‌ها و ماژول SPA.
- تست بصری هدلس (Chromium): اسکرین‌شات دسکتاپ هر ۸ صفحه + موبایل ۳۹۰px + درایور باز — مطابق دیزاین؛ بدون overflow افقی (scrollWidth=clientWidth=390).
- SPA آموزش‌ها: هارنس jsdom با ۳۷/۳۷ چک پاس (deep-link، Back/Forward، جستجو، درایور، ویدئو، retry).

---

## فاز ۲ — انجام شد (تاریخ: ۲۰۲۶-۰۷-۰۳)

### ساختار ادمین
- `/admin/index.php` ورود (کارت وسط صفحه طبق دیزاین، خطای عمومی، پیام قفل ۱۵ دقیقه‌ای)؛ `logout.php` فقط POST+CSRF.
- `includes/admin_layout.php` — پوسته واحد: `admin_page_start()/admin_page_end()`؛ سایدبار سرمه‌ای راست (استیچ کورال روی آیتم فعال، بج خوانده‌نشده روی «پیام‌های دریافتی» با کوئری زنده)، نوار بالا (عنوان، «مشاهده سایت»، منوی کاربر + خروج)، درایور موبایل <1024، جمع‌شدن سایدبار دسکتاپ (localStorage).
- `assets/css/admin.css` + `assets/js/admin.js` — تنها محل استایل/رفتار ادمین (`DKA`: toast سبز/قرمز طبق دیزاین، مودال تأیید حذف، سوییچ‌ها با dim، کپی، POST با CSRF). فونت/توکن از site.css می‌آید (admin.css بعد از آن لود می‌شود).
- صفحات: `dashboard.php` (۴ کارت آمار با کوئری واقعی + ۵ پیام آخر با empty state + اکشن‌های سریع غیرفعال)، `settings.php` (تماس/سوشال با تاگل/اینماد/عمومی + پاک‌کردن کش)، `account.php` (تغییر رمز: رمز فعلی + حداقل ۸ کاراکتر + تکرار)، `media.php` + `media_upload.php` + `media_action.php` (JSON).
- `includes/media_lib.php` — پایپ‌لاین تصویر (فاز ۳ هم استفاده می‌کند): `getimagesize` + whitelist jpg/png/webp، ≤۲MB، نام تصادفی در `uploads/images/YYYY/MM`، WebP کامل + واریانت‌های 1600/800/400 (بدون بزرگ‌نمایی)، ثبت در `media`.

### تصمیم‌های فاز ۲
1. **آیتم‌های سایدبار طبق BUILD_PLAN (۱۳ آیتم)** — دیزاین ادمین ۱۱ آیتم داشت (بدون رسانه‌ها/سئو)؛ طبق پلن اضافه شدند (آیکون‌ها: image و search از lucide). آیتم‌های فازهای بعد: کم‌رنگ + tooltip «به‌زودی».
2. **صفحه رسانه‌ها در دیزاین وجود نداشت** — با همان سیستم طراحی ادمین (کارت/اینپوت/دکمه/مودال/توست) ساخته شد.
3. ورود/خروج/ذخیره‌ها همه POST+CSRF؛ endpointهای JSON در نبود سشن 401 و بدون CSRF یا 403 می‌دهند.
4. پیام خطای حجم برای `UPLOAD_ERR_INI_SIZE` هم «حجم تصویر بیشتر از ۲ مگابایت است.» نمایش داده می‌شود (upload_max_filesize پیش‌فرض PHP خودش 2M است).
5. دکمه «پاک‌کردن کش» در کارت جدا در تنظیمات؛ ذخیره تنظیمات هم `cache_flush()` صدا می‌زند (آماده فاز ۶).
6. زمان نسبی فارسی (`fa_time_ago`) به helpers اضافه شد (برای داشبورد/اینباکس طبق دیزاین «۲ ساعت پیش»).
7. حذف رسانه: هشدار «اگر جایی استفاده شده باشد خراب نمایش داده می‌شود» (تشخیص خودکار استفاده، فاز ۳ به بعد).
8. کاربر تست محلی: `testadmin` (فقط DB محلی dev — روی production با create_admin.php ساخته و فایل حذف شود).

### شواهد پذیرش فاز ۲ (اجرا شده)
1. ۵ ورود غلط از یک IP → قفل («تلاش بیش از حد») + ورودِ درست هم تا انقضا قفل؛ بعد از شبیه‌سازی گذشت ۱۶ دقیقه (UPDATE روی login_attempts) → ورود موفق 302 به داشبورد.
2. POST بدون CSRF: ورود → رد؛ تنظیمات → 403؛ آپلود → 403؛ بدون سشن → 401.
3. `evil.jpg` (PHP با پسوند jpg) → «فقط تصویر JPG، PNG یا WebP مجاز است.»؛ JPG واقعی ۱۸۰۰×۱۲۰۰ → اصل + `.webp` + `-1600/-800/-400.webp` روی دیسک + ردیف media؛ فایل >2MB → پیام حجم.
4. تغییر تلفن در تنظیمات → بلافاصله در فوتر عمومی و صفحه تماس.
5. ادمین در ۳۶۰px: کارت‌ها استک، همبرگر → درایور راست، جدول‌ها→کارت (اسکرین‌شات‌ها در scratchpad QA).

---

## فاز ۳ — انجام شد (تاریخ: ۲۰۲۶-۰۷-۰۳)

### معماری ویرایشگر (یادداشت الزامی پلن)
- **Quill 2.0.3 کاملاً self-hosted** در `assets/vendor/quill/` (quill.js + quill.snow.css از پکیج رسمی npm — بدون CDN). تولبار سفارشی دیزاین (`#edToolbar` با دکمه‌های H2/H3/B/لیست/لینک/تصویر/نقل‌قول با کلاس‌های ql-*) به Quill وصل است؛ حالت active دکمه‌ها همان hover دیزاین.
- RTL: `dir="rtl"` روی ریشه ادیتور + استایل‌های `#edBody .ql-editor` در admin.css (تنها محل استایل ادمین).
- **درج تصویر** = handler سفارشی → `admin/media_upload.php` (پایپ‌لاین فاز ۲: اعتبارسنجی، WebP، واریانت‌ها) → `insertEmbed`.
- ذخیره با `quill.getSemanticHTML()` در hidden input و POST معمولی + CSRF. **نکته مهم:** getSemanticHTML فاصله‌های عادی را `&nbsp;` می‌کند که شکستن خط فارسی را خراب می‌کرد — هنگام ذخیره به فاصله عادی نرمالایز می‌شود (blog-edit.php).
- body_html = HTML معتمد ادمین (تک‌نقشه ادمین)؛ بدون sanitize در نمایش — تصمیم ثبت‌شده.
- تایپوگرافی بدنه مقاله عمومی: کلاس `.post-body` در page-blog-post.css — تگ‌های سمانتیک Quill (p/h2/h3/ul/ol/blockquote/a/img) دقیقاً با ظاهر نمونه دیزاین (استیچ زیر h2 با ::after، بلاک‌کوت coral-soft با نوار دوخت، بولت‌های نقطه کورال؛ پشتیبانی `li[data-list]` خروجی Quill).
- slug: خودکار از حروف لاتین عنوان (قابل ویرایش)؛ عنوان فارسی → slug دستی الزامی؛ یکتایی با خطای فارسی «این نامک قبلاً استفاده شده است.»
- published_at با اولین انتشار ست می‌شود و دیگر تغییر نمی‌کند (نمایش جلالی در کارت انتشار).
- هشدار تغییرات ذخیره‌نشده: نوار زرد دیزاین + beforeunload.

### بقیه فاز ۳
- ادمین: `blog-categories.php` (CRUD مودالی طبق دیزاین + شمارنده محتوا + حذف با مودال تأیید)، `blog.php` (جستجو/فیلتر دسته و وضعیت/صفحه‌بندی/empty state/بج وضعیت با رنگ‌های دیزاین)، `blog-edit.php`. آیتم‌های سایدبار بلاگ و دسته‌های بلاگ فعال شدند؛ اکشن سریع «+ مقاله جدید» داشبورد فعال شد.
- عمومی: `/blog` کاملاً SSR (ویژه = آخرین منتشرشده، چیپ‌های سرورساید `?cat=slug`، گرید ۹تایی با صفحه‌بندی واقعی — صفحه‌بندی placeholder دیزاین حذف و فقط وقتی >۱ صفحه است رندر می‌شود)، `/blog/{slug}` SSR کامل (زنجیره fallback سئو: seo_title→title، seo_description→excerpt؛ breadcrumb با لینک دسته؛ مرتبط‌ها هم‌دسته-اول؛ قبلی/بعدی داده‌محور؛ slug ناشناس → 404 فارسی)، سکشن بلاگ صفحه اصلی = ۳ مقاله آخر (blog_lib::blog_card — منبع واحد کارت).
- `includes/blog_lib.php`: کوئری‌ها + پالت ۹تایی گرادیان دیزاین به ترتیب seed (پست‌های بدون کاور دقیقاً ظاهر دیزاین را دارند؛ cover_image ست شود → img).
- `includes/blog_sample_data.php` حذف شد (قانون فاز ۳).
- **schema.sql بازتولید شد (اعلام بلند):** بدنه seed مقاله pricing-mistakes با نسخه سمانتیک متن کامل نمونه دیزاین جایگزین شد (فقط تگ‌های قابل‌تولید با Quill؛ باکس‌های aside/نکته/figure نمونه چون با ادیتور قابل ساخت نیستند حذف شدند). ایمپورت تازه تست شد.
- Baseline های بصری به‌روزرسانی‌شده (عمداً): blog-desktop (حذف صفحه‌بندی placeholder → شیفت محتوا)، blog-post-desktop/mobile (بدنه = محتوای واقعی DB + ناوبری قبلی/بعدی داده‌محور). سکشن بلاگ خانه پایین‌تر از محدوده اسکرین‌شات است (تغییر متنی: عنوان دسته‌های واقعی به‌جای برچسب‌های استاتیک).

### نکات باز / تأیید لازم از علی
- دامنه کانونیکال non-www فرض شد (`dookhtak.ir`).
- سال فوتر داینامیک جلالی شد (دیزاین «۱۴۰۴» ثابت داشت).
- متن ساعت پاسخ‌گویی صفحه تماس از settings می‌آید (براکت placeholder دیزاین حذف شد).
- عنوان/توضیح متای فاز ۱ موقت است؛ فاز ۶ لایه SEO کامل را می‌آورد.
