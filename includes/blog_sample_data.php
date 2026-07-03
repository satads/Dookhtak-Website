<?php
/**
 * Phase-1 static blog sample data — mirrors the `blog_posts` /
 * `blog_categories` seeds and the design's placeholder content.
 * Phase 3 replaces every consumer of this file with DB queries and
 * deletes it.
 */

/** Category filter chips, in design order ('همه' first). */
function blog_sample_categories(): array
{
    return ['همه', 'مدیریت مزون', 'جذب مشتری و فروش', 'قیمت‌گذاری', 'ترفندهای دوختک', 'داستان خیاط‌ها'];
}

/**
 * Sample posts. g1/g2 are the design's cover-gradient colors (posts have
 * no cover images yet). date = Jalali display string, datetime = Gregorian.
 */
function blog_sample_posts(): array
{
    return [
        ['slug' => 'pricing-mistakes', 'title' => '۷ اشتباه رایج در قیمت‌گذاری دوخت سفارشی', 'cat' => 'قیمت‌گذاری',
         'excerpt' => 'قیمت پایین همیشه مشتری نمی‌آورد؛ گاهی فقط سود تو را آب می‌کند. این ۷ اشتباه را بشناس و از فردا درست قیمت بده.',
         'date' => '۲ تیر ۱۴۰۵', 'datetime' => '2026-06-23', 'read' => '۵ دقیقه', 'g1' => '#F6E0D6', 'g2' => '#EEC9B9', 'featured' => true],
        ['slug' => 'loyal-customers', 'title' => 'چطور مشتری ثابت بسازیم؟', 'cat' => 'جذب مشتری و فروش',
         'excerpt' => 'مشتری که برمی‌گردد، ارزان‌ترین مشتری توست. چند عادت ساده که مشتری گذری را به مشتری همیشگی تبدیل می‌کند.',
         'date' => '۲۸ خرداد ۱۴۰۵', 'datetime' => '2026-06-18', 'read' => '۴ دقیقه', 'g1' => '#EEF1F7', 'g2' => '#D9DFEC', 'featured' => false],
        ['slug' => 'photography-guide', 'title' => 'راهنمای عکاسی از نمونه‌کار با گوشی', 'cat' => 'ترفندهای دوختک',
         'excerpt' => 'بدون دوربین حرفه‌ای هم می‌شود عکس‌هایی گرفت که مشتری را قانع کند. نور، زاویه و پس‌زمینه — همین سه چیز.',
         'date' => '۱۵ خرداد ۱۴۰۵', 'datetime' => '2026-06-05', 'read' => '۶ دقیقه', 'g1' => '#EFEBE2', 'g2' => '#E0D8C6', 'featured' => false],
        ['slug' => 'eid-deadlines', 'title' => 'مدیریت سررسیدها در شلوغی شب عید', 'cat' => 'مدیریت مزون',
         'excerpt' => 'اسفند که می‌رسد، هر خیاطی می‌داند یعنی چه. برنامه‌ریزی سررسیدها طوری که نه مشتری برنجد، نه خودت بی‌خواب شوی.',
         'date' => '۸ خرداد ۱۴۰۵', 'datetime' => '2026-05-29', 'read' => '۵ دقیقه', 'g1' => '#F0CDBD', 'g2' => '#E5B2A0', 'featured' => false],
        ['slug' => 'referral-customers', 'title' => 'چطور از مشتری راضی، معرف بسازیم؟', 'cat' => 'جذب مشتری و فروش',
         'excerpt' => 'بهترین تبلیغ، تعریفی است که مشتری راضی پیش دوستانش می‌کند. راه‌هایی برای اینکه این تعریف خودبه‌خود اتفاق بیفتد.',
         'date' => '۱ خرداد ۱۴۰۵', 'datetime' => '2026-05-22', 'read' => '۴ دقیقه', 'g1' => '#E9EDE2', 'g2' => '#D5DCC9', 'featured' => false],
        ['slug' => 'measurement-records', 'title' => 'دفتر اندازه‌ها را بازنشسته کن', 'cat' => 'ترفندهای دوختک',
         'excerpt' => 'ثبت دیجیتال اندازه‌های اندام چه فرقی با دفتر کاغذی دارد؟ تجربه خیاط‌هایی که دیگر ورق نمی‌زنند.',
         'date' => '۲۵ اردیبهشت ۱۴۰۵', 'datetime' => '2026-05-15', 'read' => '۳ دقیقه', 'g1' => '#F6E0D6', 'g2' => '#E5B2A0', 'featured' => false],
        ['slug' => 'mezon-bookkeeping', 'title' => 'حساب‌وکتاب مزون بدون سردرد', 'cat' => 'مدیریت مزون',
         'excerpt' => 'دخل‌وخرج را همان روز ثبت کن، نه آخر ماه. یک روش ساده که سود واقعی‌ات را نشانت می‌دهد.',
         'date' => '۱۸ اردیبهشت ۱۴۰۵', 'datetime' => '2026-05-08', 'read' => '۵ دقیقه', 'g1' => '#EEF1F7', 'g2' => '#C9D2E4', 'featured' => false],
        ['slug' => 'instagram-to-order', 'title' => 'از فالوئر اینستاگرام تا سفارش واقعی', 'cat' => 'جذب مشتری و فروش',
         'excerpt' => 'لایک سفارش نمی‌شود، مگر مسیرش را کوتاه کنی. چطور دنبال‌کننده را با یک لینک به مشتری تبدیل کنیم.',
         'date' => '۱۱ اردیبهشت ۱۴۰۵', 'datetime' => '2026-05-01', 'read' => '۶ دقیقه', 'g1' => '#EFEBE2', 'g2' => '#D8CDB4', 'featured' => false],
        ['slug' => 'tailor-story-maryam', 'title' => 'داستان مزون مریم: از اتاق خواب تا ۱۲۰ مشتری ثابت', 'cat' => 'داستان خیاط‌ها',
         'excerpt' => 'مریم سه سال پیش با یک چرخ خیاطی در خانه شروع کرد. روایت رشد یک مزون کوچک، با فراز و نشیب‌های واقعی‌اش.',
         'date' => '۴ اردیبهشت ۱۴۰۵', 'datetime' => '2026-04-24', 'read' => '۷ دقیقه', 'g1' => '#F0CDBD', 'g2' => '#EEC9B9', 'featured' => false],
    ];
}
