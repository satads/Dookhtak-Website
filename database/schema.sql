-- ============================================================
-- Dookhtak marketing website — FULL database schema (all phases).
-- Import ONCE, manually, via phpMyAdmin into an empty database.
-- Charset: utf8mb4. Never auto-run from code.
-- ============================================================

SET NAMES utf8mb4;
SET time_zone = '+03:30';

-- ---------- Admin ----------

CREATE TABLE admin_users (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  username VARCHAR(64) NOT NULL,
  password_hash VARCHAR(255) NOT NULL,
  last_login_at DATETIME NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_admin_username (username)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE login_attempts (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  ip_address VARCHAR(45) NOT NULL,
  attempted_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_attempts_ip_time (ip_address, attempted_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------- Site settings ----------

CREATE TABLE settings (
  `key` VARCHAR(64) NOT NULL,
  `value` TEXT NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO settings (`key`, `value`) VALUES
('support_phone', '۰۲۱-۱۲۳۴۵۶۷۸'),
('support_email', 'hello@dookhtak.ir'),
('working_hours', 'شنبه تا پنجشنبه، ۹ تا ۱۸'),
('instagram_url', 'https://instagram.com/dookhtak'),
('instagram_enabled', '1'),
('telegram_url', ''),
('telegram_enabled', '0'),
('bale_url', ''),
('bale_enabled', '0'),
('enamad_code', ''),
('app_url', 'https://app.dookhtak.ir'),
('trial_days', '10'),
('tutorials_quick_start', '["install-android","add-customer","first-order","sms-activate","gallery-upload"]');

-- ---------- Blog ----------

CREATE TABLE blog_categories (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  slug VARCHAR(120) NOT NULL,
  title VARCHAR(160) NOT NULL,
  sort_order INT NOT NULL DEFAULT 0,
  PRIMARY KEY (id),
  UNIQUE KEY uq_blog_cat_slug (slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO blog_categories (id, slug, title, sort_order) VALUES
(1, 'maison-management', 'مدیریت مزون', 1),
(2, 'sales-customers', 'جذب مشتری و فروش', 2),
(3, 'pricing', 'قیمت‌گذاری', 3),
(4, 'dookhtak-tips', 'ترفندهای دوختک', 4),
(5, 'tailor-stories', 'داستان خیاط‌ها', 5);

CREATE TABLE blog_posts (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  slug VARCHAR(160) NOT NULL,
  title VARCHAR(220) NOT NULL,
  excerpt TEXT NULL,
  body_html MEDIUMTEXT NULL,
  cover_image VARCHAR(255) NULL,
  cover_alt VARCHAR(220) NULL,
  category_id INT UNSIGNED NULL,
  status ENUM('draft','published') NOT NULL DEFAULT 'draft',
  published_at DATETIME NULL,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  seo_title VARCHAR(220) NULL,
  seo_description VARCHAR(320) NULL,
  reading_minutes TINYINT UNSIGNED NOT NULL DEFAULT 5,
  PRIMARY KEY (id),
  UNIQUE KEY uq_post_slug (slug),
  KEY idx_post_status_date (status, published_at),
  CONSTRAINT fk_post_category FOREIGN KEY (category_id) REFERENCES blog_categories (id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Sample posts mirroring the approved design's placeholder content.
INSERT INTO blog_posts (slug, title, excerpt, body_html, category_id, status, published_at, reading_minutes) VALUES
('pricing-mistakes', '۷ اشتباه رایج در قیمت‌گذاری دوخت سفارشی', 'قیمت پایین همیشه مشتری نمی‌آورد؛ گاهی فقط سود تو را آب می‌کند. این ۷ اشتباه را بشناس و از فردا درست قیمت بده.', '<p>بیشتر خیاط‌ها قیمت را با «حسِ لحظه» تعیین می‌کنند: مشتری که اخم می‌کند، قیمت پایین می‌آید؛ ماهی که خرج زیاد دارد، قیمت بالا می‌رود. نتیجه‌اش این است که آخر ماه نمی‌دانی سود کرده‌ای یا فقط خسته شده‌ای. در این مقاله هفت اشتباه رایج قیمت‌گذاری را مرور می‌کنیم — و راه ساده اصلاح هرکدام را.</p><h2>۱. حساب‌نکردن وقتِ خودت</h2><p>پارچه و خرج‌کار را همه حساب می‌کنند؛ اما ساعت‌هایی که پای چرخ و میز برش می‌گذری، ارزشمندترین چیزی است که می‌فروشی. اگر دوخت یک مانتو ده ساعت وقت می‌برد، آن ده ساعت باید توی قیمت باشد — با نرخی که برای مهارتت منصفانه است، نه نرخ کارگر ساده.</p><h2>۲. رقابت فقط بر سر ارزانی</h2><p>مشتری‌ای که فقط به‌خاطر ارزانی آمده، با اولین قیمت ارزان‌تر می‌رود. اما مشتری‌ای که به‌خاطر دوخت تمیز و تحویل سرِ وقت آمده، می‌ماند و معرف می‌آورد. قیمتِ کمی بالاتر با کیفیت ثابت، از قیمتِ پایین با کیفیت متغیر بسیار سودآورتر است.</p><blockquote>«قیمت پایین، بی‌ارزش‌بودن کار را جار می‌زند. مشتری خوب دنبال خیاط خوب است، نه خیاط ارزان.» — یادداشت یک مزون‌دار باتجربه</blockquote><h2>۳. قیمت‌های سرگردان برای کارهای مشابه</h2><p>اگر برای دو مانتوی مشابه، دو قیمت متفاوت گفته باشی و مشتری‌ها باخبر شوند، اعتماد از بین می‌رود. یک نرخ‌نامه ساده برای خودت بنویس: پایه هر نوع لباس + اضافات (آستر، یقه خاص، سنگ‌دوزی). بعد به آن وفادار بمان.</p><h2>۴. جانگرفتن هزینه‌های پنهان</h2><p>اجاره، برق، استهلاک چرخ، نخ و سوزن، رفت‌وآمد — این‌ها «خرج مغازه» است و باید سهمی از هر سفارش را بگیرد. یک روش ساده:</p><ul><li>جمع هزینه‌های ثابت ماه را بنویس.</li><li>بر تعداد متوسط سفارش‌های ماه تقسیم کن.</li><li>عدد به‌دست‌آمده را به قیمت پایه هر سفارش اضافه کن.</li></ul><h2>۵. تخفیف‌دادن بدون حساب</h2><p>تخفیف ابزار است، نه عادت. اگر همیشه تخفیف بدهی، قیمت واقعی‌ات همان قیمتِ بعد از تخفیف است — فقط خودت خبر نداری. تخفیف را برای مناسبت مشخص (تولد مشتری، معرفی دوست) نگه دار تا هم اثر داشته باشد، هم حسابش دستت باشد.</p><h2>۶. نگفتن قیمت پیش از شروع کار</h2><p>«حالا بعداً حساب می‌کنیم» شروعِ بیشتر دلخوری‌هاست. قیمت و شرایط پرداخت را همان اول، شفاف و حتی‌الامکان مکتوب بگو. مشتری که از اول عدد را می‌داند، موقع تحویل چانه نمی‌زند.</p><h2>۷. بازنکردن حساب سود در پایان ماه</h2><p>اگر ندانی ماه گذشته واقعاً چقدر سود کردی، نمی‌فهمی کدام کارها می‌ارزند و کدام فقط وقتت را می‌گیرند. ماهی یک‌بار درآمد و هزینه را کنار هم بگذار؛ خیلی زود الگوها را می‌بینی — مثلاً اینکه تعمیرات کوچک شاید شلوغت کند اما سودش با دوخت سفارشی قابل مقایسه نیست.</p><h3>جمع‌بندی</h3><p>قیمت‌گذاری درست یعنی احترام به کار خودت: وقتت را حساب کن، نرخ‌نامه داشته باش، هزینه‌های پنهان را جا نگذار و آخر هر ماه حساب سود را باز کن. بقیه‌اش تمرین است.</p>', 3, 'published', '2026-06-23 10:00:00', 5),
('loyal-customers', 'چطور مشتری ثابت بسازیم؟', 'مشتری که برمی‌گردد، ارزان‌ترین مشتری توست. چند عادت ساده که مشتری گذری را به مشتری همیشگی تبدیل می‌کند.', '<p>مشتری که برمی‌گردد، ارزان‌ترین مشتری توست. چند عادت ساده که مشتری گذری را به مشتری همیشگی تبدیل می‌کند.</p><p>[محتوای کامل مقاله — placeholder]</p>', 2, 'published', '2026-06-18 10:00:00', 4),
('photography-guide', 'راهنمای عکاسی از نمونه‌کار با گوشی', 'بدون دوربین حرفه‌ای هم می‌شود عکس‌هایی گرفت که مشتری را قانع کند. نور، زاویه و پس‌زمینه — همین سه چیز.', '<p>بدون دوربین حرفه‌ای هم می‌شود عکس‌هایی گرفت که مشتری را قانع کند. نور، زاویه و پس‌زمینه — همین سه چیز.</p><p>[محتوای کامل مقاله — placeholder]</p>', 4, 'published', '2026-06-05 10:00:00', 6),
('eid-deadlines', 'مدیریت سررسیدها در شلوغی شب عید', 'اسفند که می‌رسد، هر خیاطی می‌داند یعنی چه. برنامه‌ریزی سررسیدها طوری که نه مشتری برنجد، نه خودت بی‌خواب شوی.', '<p>اسفند که می‌رسد، هر خیاطی می‌داند یعنی چه. برنامه‌ریزی سررسیدها طوری که نه مشتری برنجد، نه خودت بی‌خواب شوی.</p><p>[محتوای کامل مقاله — placeholder]</p>', 1, 'published', '2026-05-29 10:00:00', 5),
('referral-customers', 'چطور از مشتری راضی، معرف بسازیم؟', 'بهترین تبلیغ، تعریفی است که مشتری راضی پیش دوستانش می‌کند. راه‌هایی برای اینکه این تعریف خودبه‌خود اتفاق بیفتد.', '<p>بهترین تبلیغ، تعریفی است که مشتری راضی پیش دوستانش می‌کند. راه‌هایی برای اینکه این تعریف خودبه‌خود اتفاق بیفتد.</p><p>[محتوای کامل مقاله — placeholder]</p>', 2, 'published', '2026-05-22 10:00:00', 4),
('measurement-records', 'دفتر اندازه‌ها را بازنشسته کن', 'ثبت دیجیتال اندازه‌های اندام چه فرقی با دفتر کاغذی دارد؟ تجربه خیاط‌هایی که دیگر ورق نمی‌زنند.', '<p>ثبت دیجیتال اندازه‌های اندام چه فرقی با دفتر کاغذی دارد؟ تجربه خیاط‌هایی که دیگر ورق نمی‌زنند.</p><p>[محتوای کامل مقاله — placeholder]</p>', 4, 'published', '2026-05-15 10:00:00', 3),
('mezon-bookkeeping', 'حساب‌وکتاب مزون بدون سردرد', 'دخل‌وخرج را همان روز ثبت کن، نه آخر ماه. یک روش ساده که سود واقعی‌ات را نشانت می‌دهد.', '<p>دخل‌وخرج را همان روز ثبت کن، نه آخر ماه. یک روش ساده که سود واقعی‌ات را نشانت می‌دهد.</p><p>[محتوای کامل مقاله — placeholder]</p>', 1, 'published', '2026-05-08 10:00:00', 5),
('instagram-to-order', 'از فالوئر اینستاگرام تا سفارش واقعی', 'لایک سفارش نمی‌شود، مگر مسیرش را کوتاه کنی. چطور دنبال‌کننده را با یک لینک به مشتری تبدیل کنیم.', '<p>لایک سفارش نمی‌شود، مگر مسیرش را کوتاه کنی. چطور دنبال‌کننده را با یک لینک به مشتری تبدیل کنیم.</p><p>[محتوای کامل مقاله — placeholder]</p>', 2, 'published', '2026-05-01 10:00:00', 6),
('tailor-story-maryam', 'داستان مزون مریم: از اتاق خواب تا ۱۲۰ مشتری ثابت', 'مریم سه سال پیش با یک چرخ خیاطی در خانه شروع کرد. روایت رشد یک مزون کوچک، با فراز و نشیب‌های واقعی‌اش.', '<p>مریم سه سال پیش با یک چرخ خیاطی در خانه شروع کرد. روایت رشد یک مزون کوچک، با فراز و نشیب‌های واقعی‌اش.</p><p>[محتوای کامل مقاله — placeholder]</p>', 5, 'published', '2026-04-24 10:00:00', 7);

-- ---------- Tutorials ----------

CREATE TABLE tutorial_categories (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  slug VARCHAR(120) NOT NULL,
  title VARCHAR(160) NOT NULL,
  lucide_icon VARCHAR(64) NOT NULL DEFAULT 'book-open',
  sort_order INT NOT NULL DEFAULT 0,
  PRIMARY KEY (id),
  UNIQUE KEY uq_tut_cat_slug (slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO tutorial_categories (id, slug, title, lucide_icon, sort_order) VALUES
(1, 'start', 'شروع کار', 'rocket', 1),
(2, 'customers', 'مشتری‌ها', 'user', 2),
(3, 'orders', 'سفارش‌ها', 'clipboard-list', 3),
(4, 'gallery', 'گالری نمونه‌کار', 'image', 4),
(5, 'sms', 'پیامک و باشگاه مشتریان', 'message-circle', 5),
(6, 'payment', 'پرداخت و درگاه', 'credit-card', 6),
(7, 'account', 'حسابداری', 'receipt', 7),
(8, 'settings', 'تنظیمات و امکانات بیشتر', 'settings', 8);

CREATE TABLE tutorials (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  slug VARCHAR(160) NOT NULL,
  title VARCHAR(220) NOT NULL,
  category_id INT UNSIGNED NULL,
  duration_minutes TINYINT UNSIGNED NOT NULL DEFAULT 3,
  status ENUM('draft','published') NOT NULL DEFAULT 'draft',
  sort_order INT NOT NULL DEFAULT 0,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  video_type ENUM('none','aparat') NOT NULL DEFAULT 'none',
  video_embed TEXT NULL,
  intro_text TEXT NULL,
  prerequisites_html TEXT NULL,
  content_json MEDIUMTEXT NULL,
  troubleshooting_json TEXT NULL,
  seo_title VARCHAR(220) NULL,
  seo_description VARCHAR(320) NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_tutorial_slug (slug),
  KEY idx_tutorial_status (status, sort_order),
  CONSTRAINT fk_tutorial_category FOREIGN KEY (category_id) REFERENCES tutorial_categories (id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Seed rows mirror the export's static tutorial list. content_json /
-- troubleshooting_json / prerequisites_html hold the migrated structured
-- JSON content of the Phase 1 sample fragments (Phase 4 migration).
-- video_type='aparat' marks tutorials whose design shows a video block
-- (embed code itself is a Phase 4 placeholder).
INSERT INTO tutorials (slug, title, category_id, duration_minutes, status, sort_order, video_type, prerequisites_html, content_json, troubleshooting_json) VALUES
('install-android', 'چطور دوختک را روی گوشی اندروید نصب کنم؟', 1, 3, 'published', 1, 'aparat', NULL, '[{"title":"با کروم وارد آدرس دوختک شو","text":"در مرورگر کروم گوشی، آدرس اپ مزونت را باز کن. [آدرس ورود — placeholder]","tip":null,"warning":null,"image":null,"image_alt":null,"annotation":"مرورگر کروم با آدرس اپ"},{"title":"منوی مرورگر را باز کن","text":"روی سه‌نقطه بالای کروم بزن.","tip":null,"warning":null,"image":null,"image_alt":null,"annotation":"منوی سه‌نقطه کروم"},{"title":"«افزودن به صفحه اصلی» را بزن","text":"از منو، گزینه «Add to Home screen» یا «افزودن به صفحه اصلی» را انتخاب کن.","tip":null,"warning":null,"image":null,"image_alt":null,"annotation":"گزینه افزودن به صفحه اصلی"},{"title":"تأیید کن — آیکون دوختک روی گوشی می‌نشیند","text":"اسم را همان «دوختک» بگذار و «افزودن» را بزن. از این به بعد مثل هر اپ دیگری از صفحه اصلی گوشی بازش می‌کنی.","tip":"دوختک ابری است — نیازی به آپدیت دستی نیست و همیشه آخرین نسخه را داری.","warning":null,"image":null,"image_alt":null,"annotation":null}]', '[{"q":"گزینه «افزودن به صفحه اصلی» را نمی‌بینم","a":"مطمئن شو با مرورگر کروم (نه مرورگر داخلی پیام‌رسان‌ها) صفحه را باز کرده‌ای. [تطبیق با اپ واقعی]"},{"q":"آیکون اضافه شد اما صفحه باز نمی‌شود","a":"یک‌بار اینترنت گوشی را چک کن و دوباره بزن؛ اگر حل نشد تیکت بفرست."}]'),
('install-iphone', 'چطور دوختک را روی آیفون نصب کنم؟', 1, 3, 'published', 2, 'aparat', NULL, '[{"title":"[تیتر قدم — placeholder]","text":"[متن کوتاه قدم — این آموزش هنوز کامل نشده و ساختارش آماده جایگذاری محتواست.]","tip":null,"warning":null,"image":null,"image_alt":null,"annotation":"[توضیح تصویر لازم]"},{"title":"[تیتر قدم — placeholder]","text":"[متن کوتاه قدم]","tip":null,"warning":null,"image":null,"image_alt":null,"annotation":null}]', '[]'),
('signup-login', 'چطور ثبت‌نام و وارد شوم؟', 1, 4, 'published', 3, 'aparat', NULL, '[{"title":"[تیتر قدم — placeholder]","text":"[متن کوتاه قدم — این آموزش هنوز کامل نشده و ساختارش آماده جایگذاری محتواست.]","tip":null,"warning":null,"image":null,"image_alt":null,"annotation":"[توضیح تصویر لازم]"},{"title":"[تیتر قدم — placeholder]","text":"[متن کوتاه قدم]","tip":null,"warning":null,"image":null,"image_alt":null,"annotation":null}]', '[]'),
('app-tour', 'آشنایی با صفحه اصلی اپ', 1, 5, 'published', 4, 'none', NULL, '[{"title":"[تیتر قدم — placeholder]","text":"[متن کوتاه قدم — این آموزش هنوز کامل نشده و ساختارش آماده جایگذاری محتواست.]","tip":null,"warning":null,"image":null,"image_alt":null,"annotation":"[توضیح تصویر لازم]"},{"title":"[تیتر قدم — placeholder]","text":"[متن کوتاه قدم]","tip":null,"warning":null,"image":null,"image_alt":null,"annotation":null}]', '[]'),
('add-customer', 'چطور مشتری جدید ثبت کنم؟', 2, 3, 'published', 5, 'aparat', NULL, '[{"title":"[تیتر قدم — placeholder]","text":"[متن کوتاه قدم — این آموزش هنوز کامل نشده و ساختارش آماده جایگذاری محتواست.]","tip":null,"warning":null,"image":null,"image_alt":null,"annotation":"[توضیح تصویر لازم]"},{"title":"[تیتر قدم — placeholder]","text":"[متن کوتاه قدم]","tip":null,"warning":null,"image":null,"image_alt":null,"annotation":null}]', '[]'),
('add-measurements', 'چطور اندازه‌های اندام مشتری را ثبت کنم؟', 2, 4, 'published', 6, 'aparat', NULL, '[{"title":"[تیتر قدم — placeholder]","text":"[متن کوتاه قدم — این آموزش هنوز کامل نشده و ساختارش آماده جایگذاری محتواست.]","tip":null,"warning":null,"image":null,"image_alt":null,"annotation":"[توضیح تصویر لازم]"},{"title":"[تیتر قدم — placeholder]","text":"[متن کوتاه قدم]","tip":null,"warning":null,"image":null,"image_alt":null,"annotation":null}]', '[]'),
('find-edit-customer', 'چطور مشتری را پیدا و ویرایش کنم؟', 2, 2, 'published', 7, 'none', NULL, '[{"title":"[تیتر قدم — placeholder]","text":"[متن کوتاه قدم — این آموزش هنوز کامل نشده و ساختارش آماده جایگذاری محتواست.]","tip":null,"warning":null,"image":null,"image_alt":null,"annotation":"[توضیح تصویر لازم]"},{"title":"[تیتر قدم — placeholder]","text":"[متن کوتاه قدم]","tip":null,"warning":null,"image":null,"image_alt":null,"annotation":null}]', '[]'),
('first-order', 'چطور اولین سفارشم را ثبت کنم؟', 3, 4, 'published', 8, 'aparat', 'برای این آموزش لازم است حداقل یک مشتری ثبت کرده باشی. اگر هنوز مشتری نداری، اول <a href="#add-customer" style="color:#E76F51;font-weight:700;text-decoration:underline;text-underline-offset:3px;">آموزش ثبت مشتری</a> را ببین.', '[{"title":"وارد بخش «سفارش‌ها» شو","text":"از منوی اپ، روی «سفارش‌ها» بزن.","tip":null,"warning":null,"image":null,"image_alt":null,"annotation":"صفحه اصلی اپ با منوی «سفارش‌ها»"},{"title":"روی دکمه «+ سفارش جدید» بزن","text":"دکمه مرجانی بالای صفحه سفارش‌هاست.","tip":null,"warning":null,"image":null,"image_alt":null,"annotation":"لیست سفارش‌ها با دکمه «+ سفارش جدید»"},{"title":"مشتری را انتخاب کن","text":"اسم مشتری را بنویس تا از لیست پیدا شود، بعد رویش بزن.","tip":"اگر مشتری جدید است، همین‌جا می‌توانی با دکمه «مشتری جدید» بدون خروج از فرم ثبتش کنی.","warning":null,"image":null,"image_alt":null,"annotation":"جستجو و انتخاب مشتری در فرم سفارش"},{"title":"جزئیات سفارش را وارد کن","text":"نوع لباس، توضیحات دوخت و مبلغ را بنویس.","tip":null,"warning":null,"image":null,"image_alt":null,"annotation":"فرم جزئیات سفارش — نوع لباس، توضیح، مبلغ"},{"title":"تاریخ تحویل را مشخص کن","text":"از تقویم شمسی، روز تحویل را انتخاب کن — دوختک قبل از موعد یادت می‌اندازد.","tip":null,"warning":"تاریخ تحویل را کمی زودتر از قولی که به مشتری داده‌ای بگذار تا برای پرو و اصلاح وقت داشته باشی.","image":null,"image_alt":null,"annotation":"تقویم شمسی — انتخاب سررسید تحویل"},{"title":"ذخیره کن — تمام!","text":"روی «ذخیره سفارش» بزن. سفارش با وضعیت «سفارش جدید» در لیستت می‌نشیند و از همین‌جا می‌توانی مرحله‌اش را جلو ببری.","tip":null,"warning":null,"image":null,"image_alt":null,"annotation":null}]', '[{"q":"اسم مشتری در جستجو پیدا نمی‌شود","a":"احتمالاً هنوز ثبتش نکرده‌ای — از دکمه «مشتری جدید» داخل همان فرم ثبتش کن. [تطبیق با اپ واقعی]"},{"q":"دکمه «سفارش جدید» را نمی‌بینم","a":"یک‌بار صفحه را پایین بکش تا تازه شود؛ اگر باز هم نبود، با حساب مدیر وارد شده باش. [تطبیق با اپ واقعی]"},{"q":"تاریخ تحویل اشتباه ثبت شد","a":"وارد همان سفارش شو و روی تاریخ بزن تا از تقویم اصلاحش کنی. [تطبیق با اپ واقعی]"}]'),
('order-status', 'مراحل و وضعیت سفارش چطور کار می‌کند؟', 3, 3, 'published', 9, 'none', NULL, '[{"title":"[تیتر قدم — placeholder]","text":"[متن کوتاه قدم — این آموزش هنوز کامل نشده و ساختارش آماده جایگذاری محتواست.]","tip":null,"warning":null,"image":null,"image_alt":null,"annotation":"[توضیح تصویر لازم]"},{"title":"[تیتر قدم — placeholder]","text":"[متن کوتاه قدم]","tip":null,"warning":null,"image":null,"image_alt":null,"annotation":null}]', '[]'),
('order-deadline', 'چطور سررسید تحویل تنظیم کنم؟', 3, 2, 'published', 10, 'none', NULL, '[{"title":"[تیتر قدم — placeholder]","text":"[متن کوتاه قدم — این آموزش هنوز کامل نشده و ساختارش آماده جایگذاری محتواست.]","tip":null,"warning":null,"image":null,"image_alt":null,"annotation":"[توضیح تصویر لازم]"},{"title":"[تیتر قدم — placeholder]","text":"[متن کوتاه قدم]","tip":null,"warning":null,"image":null,"image_alt":null,"annotation":null}]', '[]'),
('refer-order', 'چطور سفارش را به همکار ارجاع بدهم؟', 3, 4, 'published', 11, 'aparat', NULL, '[{"title":"[تیتر قدم — placeholder]","text":"[متن کوتاه قدم — این آموزش هنوز کامل نشده و ساختارش آماده جایگذاری محتواست.]","tip":null,"warning":null,"image":null,"image_alt":null,"annotation":"[توضیح تصویر لازم]"},{"title":"[تیتر قدم — placeholder]","text":"[متن کوتاه قدم]","tip":null,"warning":null,"image":null,"image_alt":null,"annotation":null}]', '[]'),
('gallery-upload', 'چطور عکس و فیلم نمونه‌کار آپلود کنم؟', 4, 4, 'published', 12, 'aparat', NULL, '[{"title":"[تیتر قدم — placeholder]","text":"[متن کوتاه قدم — این آموزش هنوز کامل نشده و ساختارش آماده جایگذاری محتواست.]","tip":null,"warning":null,"image":null,"image_alt":null,"annotation":"[توضیح تصویر لازم]"},{"title":"[تیتر قدم — placeholder]","text":"[متن کوتاه قدم]","tip":null,"warning":null,"image":null,"image_alt":null,"annotation":null}]', '[]'),
('gallery-categories', 'چطور گالری را دسته‌بندی کنم؟', 4, 2, 'published', 13, 'none', NULL, '[{"title":"[تیتر قدم — placeholder]","text":"[متن کوتاه قدم — این آموزش هنوز کامل نشده و ساختارش آماده جایگذاری محتواست.]","tip":null,"warning":null,"image":null,"image_alt":null,"annotation":"[توضیح تصویر لازم]"},{"title":"[تیتر قدم — placeholder]","text":"[متن کوتاه قدم]","tip":null,"warning":null,"image":null,"image_alt":null,"annotation":null}]', '[]'),
('gallery-share', 'چطور لینک گالری را برای مشتری بفرستم؟', 4, 3, 'published', 14, 'aparat', NULL, '[{"title":"[تیتر قدم — placeholder]","text":"[متن کوتاه قدم — این آموزش هنوز کامل نشده و ساختارش آماده جایگذاری محتواست.]","tip":null,"warning":null,"image":null,"image_alt":null,"annotation":"[توضیح تصویر لازم]"},{"title":"[تیتر قدم — placeholder]","text":"[متن کوتاه قدم]","tip":null,"warning":null,"image":null,"image_alt":null,"annotation":null}]', '[]'),
('sms-activate', 'چطور سامانه پیامکی را فعال کنم؟', 5, 4, 'published', 15, 'aparat', NULL, '[{"title":"[تیتر قدم — placeholder]","text":"[متن کوتاه قدم — این آموزش هنوز کامل نشده و ساختارش آماده جایگذاری محتواست.]","tip":null,"warning":null,"image":null,"image_alt":null,"annotation":"[توضیح تصویر لازم]"},{"title":"[تیتر قدم — placeholder]","text":"[متن کوتاه قدم]","tip":null,"warning":null,"image":null,"image_alt":null,"annotation":null}]', '[]'),
('sms-bulk', 'چطور پیامک انبوه بفرستم؟', 5, 3, 'published', 16, 'none', NULL, '[{"title":"[تیتر قدم — placeholder]","text":"[متن کوتاه قدم — این آموزش هنوز کامل نشده و ساختارش آماده جایگذاری محتواست.]","tip":null,"warning":null,"image":null,"image_alt":null,"annotation":"[توضیح تصویر لازم]"},{"title":"[تیتر قدم — placeholder]","text":"[متن کوتاه قدم]","tip":null,"warning":null,"image":null,"image_alt":null,"annotation":null}]', '[]'),
('sms-birthday', 'چطور تبریک تولد خودکار را روشن کنم؟', 5, 2, 'published', 17, 'none', NULL, '[{"title":"[تیتر قدم — placeholder]","text":"[متن کوتاه قدم — این آموزش هنوز کامل نشده و ساختارش آماده جایگذاری محتواست.]","tip":null,"warning":null,"image":null,"image_alt":null,"annotation":"[توضیح تصویر لازم]"},{"title":"[تیتر قدم — placeholder]","text":"[متن کوتاه قدم]","tip":null,"warning":null,"image":null,"image_alt":null,"annotation":null}]', '[]'),
('sms-dedicated-line', 'چطور خط اختصاصی بگیرم؟', 5, 3, 'published', 18, 'none', NULL, '[{"title":"[تیتر قدم — placeholder]","text":"[متن کوتاه قدم — این آموزش هنوز کامل نشده و ساختارش آماده جایگذاری محتواست.]","tip":null,"warning":null,"image":null,"image_alt":null,"annotation":"[توضیح تصویر لازم]"},{"title":"[تیتر قدم — placeholder]","text":"[متن کوتاه قدم]","tip":null,"warning":null,"image":null,"image_alt":null,"annotation":null}]', '[]'),
('payment-activate', 'چطور درگاه پرداخت را فعال کنم؟', 6, 5, 'published', 19, 'aparat', NULL, '[{"title":"[تیتر قدم — placeholder]","text":"[متن کوتاه قدم — این آموزش هنوز کامل نشده و ساختارش آماده جایگذاری محتواست.]","tip":null,"warning":null,"image":null,"image_alt":null,"annotation":"[توضیح تصویر لازم]"},{"title":"[تیتر قدم — placeholder]","text":"[متن کوتاه قدم]","tip":null,"warning":null,"image":null,"image_alt":null,"annotation":null}]', '[]'),
('payment-link', 'چطور لینک پرداخت بسازم و بفرستم؟', 6, 3, 'published', 20, 'aparat', NULL, '[{"title":"[تیتر قدم — placeholder]","text":"[متن کوتاه قدم — این آموزش هنوز کامل نشده و ساختارش آماده جایگذاری محتواست.]","tip":null,"warning":null,"image":null,"image_alt":null,"annotation":"[توضیح تصویر لازم]"},{"title":"[تیتر قدم — placeholder]","text":"[متن کوتاه قدم]","tip":null,"warning":null,"image":null,"image_alt":null,"annotation":null}]', '[]'),
('payment-track', 'چطور پرداخت‌ها را پیگیری کنم؟', 6, 2, 'published', 21, 'none', NULL, '[{"title":"[تیتر قدم — placeholder]","text":"[متن کوتاه قدم — این آموزش هنوز کامل نشده و ساختارش آماده جایگذاری محتواست.]","tip":null,"warning":null,"image":null,"image_alt":null,"annotation":"[توضیح تصویر لازم]"},{"title":"[تیتر قدم — placeholder]","text":"[متن کوتاه قدم]","tip":null,"warning":null,"image":null,"image_alt":null,"annotation":null}]', '[]'),
('account-income-expense', 'چطور درآمد و هزینه ثبت کنم؟', 7, 4, 'published', 22, 'aparat', NULL, '[{"title":"[تیتر قدم — placeholder]","text":"[متن کوتاه قدم — این آموزش هنوز کامل نشده و ساختارش آماده جایگذاری محتواست.]","tip":null,"warning":null,"image":null,"image_alt":null,"annotation":"[توضیح تصویر لازم]"},{"title":"[تیتر قدم — placeholder]","text":"[متن کوتاه قدم]","tip":null,"warning":null,"image":null,"image_alt":null,"annotation":null}]', '[]'),
('account-cheques', 'چطور چک ثبت کنم و یادآوری بگیرم؟', 7, 3, 'published', 23, 'none', NULL, '[{"title":"[تیتر قدم — placeholder]","text":"[متن کوتاه قدم — این آموزش هنوز کامل نشده و ساختارش آماده جایگذاری محتواست.]","tip":null,"warning":null,"image":null,"image_alt":null,"annotation":"[توضیح تصویر لازم]"},{"title":"[تیتر قدم — placeholder]","text":"[متن کوتاه قدم]","tip":null,"warning":null,"image":null,"image_alt":null,"annotation":null}]', '[]'),
('account-reports', 'گزارش‌های مالی را کجا ببینم؟', 7, 3, 'published', 24, 'none', NULL, '[{"title":"[تیتر قدم — placeholder]","text":"[متن کوتاه قدم — این آموزش هنوز کامل نشده و ساختارش آماده جایگذاری محتواست.]","tip":null,"warning":null,"image":null,"image_alt":null,"annotation":"[توضیح تصویر لازم]"},{"title":"[تیتر قدم — placeholder]","text":"[متن کوتاه قدم]","tip":null,"warning":null,"image":null,"image_alt":null,"annotation":null}]', '[]'),
('custom-domain', 'چطور دامنه اختصاصی وصل کنم؟', 8, 6, 'published', 25, 'aparat', NULL, '[{"title":"[تیتر قدم — placeholder]","text":"[متن کوتاه قدم — این آموزش هنوز کامل نشده و ساختارش آماده جایگذاری محتواست.]","tip":null,"warning":null,"image":null,"image_alt":null,"annotation":"[توضیح تصویر لازم]"},{"title":"[تیتر قدم — placeholder]","text":"[متن کوتاه قدم]","tip":null,"warning":null,"image":null,"image_alt":null,"annotation":null}]', '[]'),
('staff-access', 'چطور پرسنل و دسترسی‌ها را مدیریت کنم؟', 8, 4, 'published', 26, 'none', NULL, '[{"title":"[تیتر قدم — placeholder]","text":"[متن کوتاه قدم — این آموزش هنوز کامل نشده و ساختارش آماده جایگذاری محتواست.]","tip":null,"warning":null,"image":null,"image_alt":null,"annotation":"[توضیح تصویر لازم]"},{"title":"[تیتر قدم — placeholder]","text":"[متن کوتاه قدم]","tip":null,"warning":null,"image":null,"image_alt":null,"annotation":null}]', '[]'),
('support-ticket', 'چطور تیکت پشتیبانی بفرستم؟', 8, 2, 'published', 27, 'none', NULL, '[{"title":"[تیتر قدم — placeholder]","text":"[متن کوتاه قدم — این آموزش هنوز کامل نشده و ساختارش آماده جایگذاری محتواست.]","tip":null,"warning":null,"image":null,"image_alt":null,"annotation":"[توضیح تصویر لازم]"},{"title":"[تیتر قدم — placeholder]","text":"[متن کوتاه قدم]","tip":null,"warning":null,"image":null,"image_alt":null,"annotation":null}]', '[]');

-- ---------- FAQ ----------

CREATE TABLE faqs (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  page ENUM('home','pricing') NOT NULL,
  question VARCHAR(300) NOT NULL,
  answer_html TEXT NOT NULL,
  sort_order INT NOT NULL DEFAULT 0,
  PRIMARY KEY (id),
  KEY idx_faq_page (page, sort_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO faqs (page, question, answer_html, sort_order) VALUES
('home', 'اطلاعاتم کجا ذخیره می‌شود؟ امن است؟', 'همه اطلاعات شما روی سرورهای امن ابری نگهداری و به‌طور خودکار پشتیبان‌گیری می‌شود؛ فقط خودتان به آن دسترسی دارید.', 1),
('home', 'به اینترنت قوی نیاز دارم؟', 'نه. دوختک با اینترنت معمولی موبایل هم به‌خوبی کار می‌کند.', 2),
('home', 'اگر با نرم‌افزار راحت نباشم چه؟', 'آموزش و پشتیبانی کامل فارسی داریم و قدم‌به‌قدم کنارتان هستیم تا راه بیفتید.', 3),
('home', 'بعد از ۱۰ روز رایگان چه می‌شود؟', 'بعد از پایان دوره رایگان می‌توانید یکی از پلن‌ها را انتخاب کنید؛ اطلاعاتتان محفوظ می‌ماند.', 4),
('home', 'روی گوشی کار می‌کند؟', 'بله، روی موبایل، تبلت و کامپیوتر بدون نصب برنامه اجرا می‌شود.', 5),
('home', 'می‌توانم سایت را روی دامنه خودم داشته باشم؟', 'بله، دوختک می‌تواند روی دامنه اختصاصی خودتان بالا بیاید.', 6),
('home', 'هر وقت بخواهم می‌توانم لغو کنم؟', 'بله، هیچ قراردادی نیست و هر زمان می‌توانید اشتراک را لغو کنید.', 7),
('pricing', 'هزینه راه‌اندازی اولیه شامل چه چیزهایی است؟', 'ساخت حساب مزون شما، آماده‌سازی اولیه سامانه و آموزش شروع کار. این هزینه فقط یک‌بار هنگام ثبت‌نام پلن پایه پرداخت می‌شود. [جزئیات دقیق را تیم دوختک تکمیل کند]', 1),
('pricing', 'فرق پلن پایه و پلن ویژه دقیقاً چیست؟', 'پلن پایه با اشتراک دوره‌ای کار می‌کند و ماژول‌های اضافه را هر وقت لازم داشتی جداگانه می‌خری. پلن ویژه یک‌بار پرداخت است: بدون اشتراک ماهانه، همه ماژول‌ها از روز اول فعال و همه قابلیت‌های آینده رایگان.', 2),
('pricing', 'ماژول‌ها اشتراکی‌اند یا یک‌بار خرید؟', 'یک‌بار برای همیشه. هر ماژول را یک‌بار می‌خری و تا وقتی اشتراک پلن پایه‌ات فعال است، بدون هزینه دوباره استفاده می‌کنی.', 3),
('pricing', '۱۰ روز تست رایگان شامل چه امکاناتی است؟', 'در دوره تست به امکانات سامانه دسترسی داری تا با خیال راحت آن را بسنجی؛ بدون نیاز به کارت بانکی. [محدوده دقیق امکانات دوره تست را تیم دوختک تکمیل کند]', 4),
('pricing', 'اگر اشتراک پلن پایه را تمدید نکنم چه می‌شود؟', 'اطلاعات شما محفوظ می‌ماند و با تمدید دوباره، همه‌چیز همان‌جا که بود در دسترس است. [مدت نگهداری اطلاعات را تیم دوختک تکمیل کند]', 5),
('pricing', 'هزینه شارژ پیامک چطور حساب می‌شود؟', 'شارژ پیامک جدا از قیمت ماژول است و بر اساس تعداد پیامک ارسالی از داخل اپ خریداری می‌شود. [تعرفه هر پیامک را تیم دوختک تکمیل کند]', 6),
('pricing', 'می‌توانم بعداً از پایه به پلن ویژه ارتقا بدهم؟', 'بله. [سازوکار محاسبه مابه‌التفاوت را تیم دوختک تکمیل کند]', 7);

-- ---------- Testimonials ----------

CREATE TABLE testimonials (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  quote TEXT NOT NULL,
  person_name VARCHAR(120) NOT NULL,
  city VARCHAR(80) NULL,
  business_type VARCHAR(120) NULL,
  sort_order INT NOT NULL DEFAULT 0,
  status ENUM('draft','published') NOT NULL DEFAULT 'published',
  PRIMARY KEY (id),
  KEY idx_testimonial_status (status, sort_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO testimonials (quote, person_name, city, business_type, sort_order, status) VALUES
('دفترهای سفارشم را کنار گذاشتم. حالا هر سفارش با سررسیدش جلوی چشمم است و دیگر چیزی از قلم نمی‌افتد.', 'نرگس ح.', 'تهران', 'مزون مانتو', 1, 'published'),
('مشتری‌ها گالری کارهایم را در گوشی می‌بینند و راحت انتخاب می‌کنند. سفارش‌هایم بیشتر شده.', 'فاطمه ر.', 'اصفهان', 'خیاطی زنانه', 2, 'published'),
('لینک پرداخت را که فرستادم، دیگر دنبال کارت‌به‌کارت و پیگیری نیستم.', 'سمیرا ک.', 'شیراز', 'تعمیرات پوشاک', 3, 'published');

-- ---------- Pricing ----------

CREATE TABLE pricing_values (
  `key` VARCHAR(64) NOT NULL,
  `value` VARCHAR(64) NOT NULL,
  label VARCHAR(160) NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Values are stored as plain Latin-digit integers (Toman); the site
-- formats them with Persian digits and separators at render time.
INSERT INTO pricing_values (`key`, `value`, label) VALUES
('setup_fee', '490000', 'هزینه راه‌اندازی اولیه'),
('sub_1m', '290000', 'اشتراک یک‌ماهه'),
('sub_3m', '790000', 'اشتراک سه‌ماهه'),
('sub_6m', '1490000', 'اشتراک شش‌ماهه'),
('sub_12m', '2690000', 'اشتراک یک‌ساله'),
('vip_price', '9900000', 'پلن ویژه (یک‌بار پرداخت)'),
('module_gallery', '990000', 'ماژول گالری نمونه‌کار'),
('module_sms', '790000', 'ماژول سامانه پیامکی'),
('module_gateway', '690000', 'ماژول درگاه پرداخت'),
('module_domain', '590000', 'ماژول دامنه اختصاصی'),
('module_dedicated_line', '890000', 'ماژول خط اختصاصی پیامکی'),
('discount_3m_percent', '9', 'درصد صرفه‌جویی سه‌ماهه'),
('discount_6m_percent', '14', 'درصد صرفه‌جویی شش‌ماهه'),
('discount_12m_percent', '23', 'درصد صرفه‌جویی یک‌ساله');

-- ---------- Contact form submissions ----------

CREATE TABLE submissions (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  name VARCHAR(160) NOT NULL,
  phone VARCHAR(20) NOT NULL,
  business_type VARCHAR(120) NULL,
  subject VARCHAR(160) NULL,
  message TEXT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  is_read TINYINT(1) NOT NULL DEFAULT 0,
  ip_address VARCHAR(45) NULL,
  forward_status ENUM('pending','sent','failed') NOT NULL DEFAULT 'pending',
  forward_attempts TINYINT UNSIGNED NOT NULL DEFAULT 0,
  last_forward_error VARCHAR(500) NULL,
  PRIMARY KEY (id),
  KEY idx_submission_read (is_read, created_at),
  KEY idx_submission_ip_time (ip_address, created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------- SEO ----------

CREATE TABLE seo_pages (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  page_key VARCHAR(32) NOT NULL,
  meta_title VARCHAR(220) NULL,
  meta_description VARCHAR(320) NULL,
  og_image VARCHAR(255) NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_seo_page (page_key)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- meta_title holds the full <title> (admin-editable, WYSIWYG). The home
-- page is brand-first (no suffix); other pages carry the ' | دوختک' suffix.
INSERT INTO seo_pages (page_key, meta_title, meta_description) VALUES
('home', 'دوختک — سامانه ابری مدیریت خیاطی و مزون', 'دوختک همه کارهای مدیریتی خیاطی را ساده می‌کند: سفارش، مشتری، گالری و حسابداری، همه روی یک میز.'),
('features', 'امکانات دوختک | دوختک', 'مدیریت سفارش، پرونده مشتری و اندازه‌ها، گالری نمونه‌کار، لینک پرداخت، حسابداری و باشگاه مشتریان — امکانات کامل دوختک.'),
('pricing', 'تعرفه‌های دوختک | دوختک', 'تعرفه پلن پایه و پلن ویژه دوختک به‌همراه ماژول‌های تکمیلی؛ ۱۰ روز تست رایگان بدون کارت بانکی.'),
('tutorials', 'مرکز آموزش دوختک | دوختک', 'آموزش قدم‌به‌قدم کار با دوختک: از نصب و ثبت‌نام تا سفارش، گالری، پیامک، پرداخت و حسابداری.'),
('blog', 'مجله دوختک | دوختک', 'نکته‌ها و ترفندهای مدیریت خیاطی و مزون‌داری در مجله دوختک.'),
('about', 'درباره دوختک | دوختک', 'قصه دوختک و تیمی که آن را برای خیاط‌ها و مزون‌دارهای ایران می‌سازد.'),
('contact', 'تماس با دوختک | دوختک', 'برای مشاوره رایگان و شروع کار با دوختک با ما در تماس باشید.');

CREATE TABLE seo_settings (
  `key` VARCHAR(64) NOT NULL,
  `value` TEXT NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO seo_settings (`key`, `value`) VALUES
('site_title_suffix', ' | دوختک'),
('default_meta_description', 'دوختک همه کارهای مدیریتی خیاطی را ساده می‌کند: سفارش، مشتری، گالری و حسابداری، همه روی یک میز.'),
('default_og_image', ''),
('head_scripts', ''),
('body_scripts', ''),
('robots_txt', 'User-agent: *\nDisallow: /admin\nDisallow: /includes\nDisallow: /api\nDisallow: /cache\n\nSitemap: https://dookhtak.ir/sitemap.xml');

CREATE TABLE redirects (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  from_path VARCHAR(255) NOT NULL,
  to_url VARCHAR(500) NOT NULL,
  status_code SMALLINT UNSIGNED NOT NULL DEFAULT 301,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_redirect_from (from_path)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------- Media library ----------

CREATE TABLE media (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  file_path VARCHAR(255) NOT NULL,
  type ENUM('image') NOT NULL DEFAULT 'image',
  alt_text VARCHAR(255) NULL,
  width SMALLINT UNSIGNED NULL,
  height SMALLINT UNSIGNED NULL,
  size_bytes INT UNSIGNED NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
