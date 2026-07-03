<?php
/**
 * Blog single-post template.
 *
 * PHASE 1: this is a static TEMPLATE — it always renders the sample post
 * 'pricing-mistakes' regardless of the requested slug. router.php sets
 * $_GET['slug'] for /blog/{slug}, but it is intentionally ignored (and
 * never echoed). Phase 3 replaces this with full SSR from the database.
 */
require_once __DIR__ . '/includes/layout.php';
require_once __DIR__ . '/includes/blog_sample_data.php';

$current_slug = 'pricing-mistakes';
$current_cat = 'قیمت‌گذاری';
$post = null;
foreach (blog_sample_posts() as $p) {
    if ($p['slug'] === $current_slug) {
        $post = $p;
        break;
    }
}

// Related posts (server-side, replacing the design's client-side logic):
// exclude the current post, same-category posts first, then the rest — 3 items.
$others = array_values(array_filter(blog_sample_posts(), fn ($p) => $p['slug'] !== $current_slug));
$same_cat = array_values(array_filter($others, fn ($p) => $p['cat'] === $current_cat));
$rest = array_values(array_filter($others, fn ($p) => $p['cat'] !== $current_cat));
$related = array_slice(array_merge($same_cat, $rest), 0, 3);
foreach ($related as &$rp) {
    $rp['href'] = '/blog/' . $rp['slug'];
}
unset($rp);

render_head([
    'title' => '۷ اشتباه رایج در قیمت‌گذاری دوخت سفارشی | دوختک',
    'description' => $post['excerpt'] ?? '',
    'css' => '/assets/css/page-blog-post.css',
    'js' => '/assets/js/page-blog-post.js',
]);
?>
<div dir="rtl" lang="fa" style="position:relative;min-height:100vh;background-color:#FAF8F4;background-image:linear-gradient(#EDEAE3 1px,transparent 1px),linear-gradient(90deg,#EDEAE3 1px,transparent 1px);background-size:46px 46px;overflow-x:hidden;line-height:1.6;">

  <?php include __DIR__ . '/includes/site_header.php'; ?>

  <!-- READING PROGRESS TAPE (فقط دسکتاپ) -->
  <div id="readTape" style="display:none;position:fixed;top:0;inset-inline:0;height:6px;z-index:70;background:#EFEBE2;">
    <div id="readFill" style="height:100%;width:0%;background:var(--color-primary);"></div>
    <div style="position:absolute;inset:0;background-image:repeating-linear-gradient(90deg,rgba(31,42,68,.16) 0 1px,transparent 1px 12px);pointer-events:none;"></div>
  </div>

  <!-- FLOATING SHARE (فقط دسکتاپ) -->
  <div id="shareCol" style="display:none;position:fixed;top:50%;inset-inline-end:26px;transform:translateY(-50%);z-index:40;flex-direction:column;gap:10px;">
    <button class="hv36d784" data-copy-link aria-label="کپی لینک" title="کپی لینک" style="display:inline-flex;align-items:center;justify-content:center;width:44px;height:44px;background:#fff;border:1px solid var(--color-border);border-radius:50%;color:#1F2A44;box-shadow:0 6px 16px -8px rgba(31,42,68,.2);transition:color .2s ease,border-color .2s ease;">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg>
    </button>
    <a class="hv36d784" href="https://t.me/share/url?url=[POST-URL]" target="_blank" rel="noopener" aria-label="اشتراک در تلگرام" title="تلگرام" style="display:inline-flex;align-items:center;justify-content:center;width:44px;height:44px;background:#fff;border:1px solid var(--color-border);border-radius:50%;color:#1F2A44;box-shadow:0 6px 16px -8px rgba(31,42,68,.2);transition:color .2s ease,border-color .2s ease;">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m22 2-7 20-4-9-9-4Z"/><path d="M22 2 11 13"/></svg>
    </a>
    <a class="hv36d784" href="https://wa.me/?text=[POST-URL]" target="_blank" rel="noopener" aria-label="اشتراک در واتس‌اپ" title="واتس‌اپ" style="display:inline-flex;align-items:center;justify-content:center;width:44px;height:44px;background:#fff;border:1px solid var(--color-border);border-radius:50%;color:#1F2A44;box-shadow:0 6px 16px -8px rgba(31,42,68,.2);transition:color .2s ease,border-color .2s ease;">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M7.9 20A9 9 0 1 0 4 16.1L2 22Z"/><path d="M9 10a.5.5 0 0 0 1 0V9a.5.5 0 0 0-1 0Z"/><path d="M14 10a.5.5 0 0 0 1 0V9a.5.5 0 0 0-1 0Z"/><path d="M9.5 13.5c.5 1 1.5 1.5 2.5 1.5s2-.5 2.5-1.5"/></svg>
    </a>
  </div>

  <!-- ================= ARTICLE (ناحیه محتوا — برای مقاله جدید فقط این بخش عوض شود) ================= -->
  <article data-screen-label="مقاله" style="position:relative;">
    <header style="max-width:760px;margin-inline:auto;padding:122px 24px 0;">
      <!-- breadcrumb -->
      <nav aria-label="مسیر" data-hero style="opacity:0;transform:translateY(12px);transition:opacity .5s ease,transform .5s ease;display:flex;align-items:center;flex-wrap:wrap;gap:7px;font-size:13px;color:#6B7280;">
        <a class="hvd6c76f" href="/blog" style="color:#6B7280;">مجله دوختک</a>
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="transform:scaleX(-1);opacity:.6;"><path d="m9 18 6-6-6-6"/></svg>
        <a class="hvd6c76f" href="/blog" style="color:#6B7280;">قیمت‌گذاری</a>
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="transform:scaleX(-1);opacity:.6;"><path d="m9 18 6-6-6-6"/></svg>
        <span style="color:#3D4A6B;">۷ اشتباه رایج در قیمت‌گذاری</span>
      </nav>
      <div data-hero style="opacity:0;transform:translateY(12px);transition:opacity .5s ease,transform .5s ease;display:flex;align-items:center;flex-wrap:wrap;gap:10px;font-size:13px;color:#6B7280;margin-top:20px;">
        <span style="background:#FCEFEA;color:#D45A3D;font-weight:700;padding:4px 12px;border-radius:999px;">قیمت‌گذاری</span>
        <time datetime="2026-06-23">۲ تیر ۱۴۰۵</time>
        <span>·</span>
        <span>۵ دقیقه مطالعه</span>
        <span>·</span>
        <span>[نام نویسنده]</span>
      </div>
      <h1 id="postTitle" data-hero style="opacity:0;transform:translateY(12px);transition:opacity .5s ease,transform .5s ease;font-size:38px;line-height:1.5;font-weight:700;color:#1F2A44;margin:16px 0 0;letter-spacing:-.3px;">۷ اشتباه رایج در قیمت‌گذاری دوخت سفارشی</h1>
      <!-- placeholder: تصویر شاخص ۱۶:۹ -->
      <div data-hero style="opacity:0;transform:translateY(12px);transition:opacity .5s ease,transform .5s ease;aspect-ratio:16/9;border-radius:18px;margin-top:26px;background:linear-gradient(140deg,#F6E0D6,#EEC9B9);display:flex;align-items:center;justify-content:center;box-shadow:0 20px 44px -26px rgba(31,42,68,.35);">
        <span style="font-size:12.5px;color:#8a5f4f;background:rgba(255,255,255,.72);border-radius:999px;padding:4px 14px;">تصویر شاخص مقاله — ۱۶:۹</span>
      </div>
    </header>

    <!-- بدنه مقاله — نمونه کامل برای ارزیابی تایپوگرافی -->
    <div id="postBody" data-rv style="opacity:0;transform:translateY(14px);transition:opacity .5s ease,transform .5s ease;max-width:720px;margin-inline:auto;padding:34px 24px 10px;font-size:17.5px;line-height:2;color:#3D4A6B;">

      <p style="margin:0 0 1.2em;">بیشتر خیاط‌ها قیمت را با «حسِ لحظه» تعیین می‌کنند: مشتری که اخم می‌کند، قیمت پایین می‌آید؛ ماهی که خرج زیاد دارد، قیمت بالا می‌رود. نتیجه‌اش این است که آخر ماه نمی‌دانی سود کرده‌ای یا فقط خسته شده‌ای. در این مقاله هفت اشتباه رایج قیمت‌گذاری را مرور می‌کنیم — و راه ساده اصلاح هرکدام را.</p>

      <h2 style="font-size:24px;font-weight:700;color:#1F2A44;line-height:1.6;margin:1.8em 0 .6em;">۱. حساب‌نکردن وقتِ خودت</h2>
      <div style="width:64px;height:2px;background-image:repeating-linear-gradient(90deg,#E76F51 0 8px,transparent 8px 14px);margin:0 0 1em;"></div>
      <p style="margin:0 0 1.2em;">پارچه و خرج‌کار را همه حساب می‌کنند؛ اما ساعت‌هایی که پای چرخ و میز برش می‌گذری، ارزشمندترین چیزی است که می‌فروشی. اگر دوخت یک مانتو ده ساعت وقت می‌برد، آن ده ساعت باید توی قیمت باشد — با نرخی که برای مهارتت منصفانه است، نه نرخ کارگر ساده.</p>

      <h2 style="font-size:24px;font-weight:700;color:#1F2A44;line-height:1.6;margin:1.8em 0 .6em;">۲. رقابت فقط بر سر ارزانی</h2>
      <div style="width:64px;height:2px;background-image:repeating-linear-gradient(90deg,#E76F51 0 8px,transparent 8px 14px);margin:0 0 1em;"></div>
      <p style="margin:0 0 1.2em;">مشتری‌ای که فقط به‌خاطر ارزانی آمده، با اولین قیمت ارزان‌تر می‌رود. اما مشتری‌ای که به‌خاطر دوخت تمیز و تحویل سرِ وقت آمده، می‌ماند و معرف می‌آورد. قیمتِ کمی بالاتر با کیفیت ثابت، از قیمتِ پایین با کیفیت متغیر بسیار سودآورتر است.</p>

      <blockquote style="margin:1.6em 0;padding:20px 22px;background:#FCEFEA;border-radius:14px;border-inline-start:none;position:relative;">
        <div style="position:absolute;top:14px;bottom:14px;inset-inline-start:0;width:3px;background-image:repeating-linear-gradient(180deg,#E76F51 0 8px,transparent 8px 14px);"></div>
        <p style="margin:0;font-size:18.5px;line-height:2;color:#1F2A44;font-weight:400;">«قیمت پایین، بی‌ارزش‌بودن کار را جار می‌زند. مشتری خوب دنبال خیاط خوب است، نه خیاط ارزان.»</p>
        <footer style="font-size:13px;color:#6B7280;margin-top:10px;">— یادداشت یک مزون‌دار باتجربه</footer>
      </blockquote>

      <h2 style="font-size:24px;font-weight:700;color:#1F2A44;line-height:1.6;margin:1.8em 0 .6em;">۳. قیمت‌های سرگردان برای کارهای مشابه</h2>
      <div style="width:64px;height:2px;background-image:repeating-linear-gradient(90deg,#E76F51 0 8px,transparent 8px 14px);margin:0 0 1em;"></div>
      <p style="margin:0 0 1.2em;">اگر برای دو مانتوی مشابه، دو قیمت متفاوت گفته باشی و مشتری‌ها باخبر شوند، اعتماد از بین می‌رود. یک نرخ‌نامه ساده برای خودت بنویس: پایه هر نوع لباس + اضافات (آستر، یقه خاص، سنگ‌دوزی). بعد به آن وفادار بمان.</p>

      <!-- CTA درون‌متنی (حدود یک‌سوم مقاله) -->
      <aside style="position:relative;margin:2em 0;background:#fff;border-radius:16px;padding:22px 24px;overflow:hidden;">
        <div style="position:absolute;inset:8px;border:2px dashed #E76F51;border-radius:11px;opacity:.5;pointer-events:none;"></div>
        <p style="position:relative;margin:0;font-size:15.5px;line-height:1.9;color:#3D4A6B;">دوختک نرخ‌نامه و حساب هر سفارش را برایت نگه می‌دارد تا قیمت‌هایت همیشه یکدست باشند. <a href="/pricing" style="color:#E76F51;font-weight:700;text-decoration:underline;text-decoration-thickness:1px;text-underline-offset:4px;">دوختک را رایگان امتحان کن</a></p>
      </aside>

      <h2 style="font-size:24px;font-weight:700;color:#1F2A44;line-height:1.6;margin:1.8em 0 .6em;">۴. جانگرفتن هزینه‌های پنهان</h2>
      <div style="width:64px;height:2px;background-image:repeating-linear-gradient(90deg,#E76F51 0 8px,transparent 8px 14px);margin:0 0 1em;"></div>
      <p style="margin:0 0 1em;">اجاره، برق، استهلاک چرخ، نخ و سوزن، رفت‌وآمد — این‌ها «خرج مغازه» است و باید سهمی از هر سفارش را بگیرد. یک روش ساده:</p>
      <ul style="margin:0 0 1.2em;padding:0;padding-inline-start:22px;list-style:none;">
        <li style="position:relative;margin-bottom:.7em;"><span style="position:absolute;inset-inline-start:-22px;top:14px;width:8px;height:8px;border-radius:50%;background:#E76F51;"></span>جمع هزینه‌های ثابت ماه را بنویس.</li>
        <li style="position:relative;margin-bottom:.7em;"><span style="position:absolute;inset-inline-start:-22px;top:14px;width:8px;height:8px;border-radius:50%;background:#E76F51;"></span>بر تعداد متوسط سفارش‌های ماه تقسیم کن.</li>
        <li style="position:relative;"><span style="position:absolute;inset-inline-start:-22px;top:14px;width:8px;height:8px;border-radius:50%;background:#E76F51;"></span>عدد به‌دست‌آمده را به قیمت پایه هر سفارش اضافه کن.</li>
      </ul>

      <h2 style="font-size:24px;font-weight:700;color:#1F2A44;line-height:1.6;margin:1.8em 0 .6em;">۵. تخفیف‌دادن بدون حساب</h2>
      <div style="width:64px;height:2px;background-image:repeating-linear-gradient(90deg,#E76F51 0 8px,transparent 8px 14px);margin:0 0 1em;"></div>
      <p style="margin:0 0 1.2em;">تخفیف ابزار است، نه عادت. اگر همیشه تخفیف بدهی، قیمت واقعی‌ات همان قیمتِ بعد از تخفیف است — فقط خودت خبر نداری. تخفیف را برای مناسبت مشخص (تولد مشتری، معرفی دوست) نگه دار تا هم اثر داشته باشد، هم حسابش دستت باشد.</p>

      <div style="margin:1.6em 0;background:#fff;border:1px solid var(--color-border);border-radius:14px;padding:18px 20px;display:flex;gap:13px;align-items:flex-start;">
        <span style="flex:none;display:inline-flex;width:40px;height:40px;align-items:center;justify-content:center;background:#FCEFEA;border-radius:50%;color:#E76F51;">
          <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 14c.2-1 .7-1.7 1.5-2.5 1-.9 1.5-2.2 1.5-3.5A6 6 0 0 0 6 8c0 1 .2 2.2 1.5 3.5.7.7 1.3 1.5 1.5 2.5"/><path d="M9 18h6"/><path d="M10 22h4"/></svg>
        </span>
        <div>
          <div style="font-size:13.5px;font-weight:700;color:#D45A3D;margin-bottom:4px;">نکته دوختک</div>
          <p style="margin:0;font-size:15px;line-height:1.9;color:#3D4A6B;">پیامک تبریک تولد دوختک می‌تواند همراه کد تخفیف برود — تخفیفِ حساب‌شده، سرِ مناسبت درست.</p>
        </div>
      </div>

      <h2 style="font-size:24px;font-weight:700;color:#1F2A44;line-height:1.6;margin:1.8em 0 .6em;">۶. نگفتن قیمت پیش از شروع کار</h2>
      <div style="width:64px;height:2px;background-image:repeating-linear-gradient(90deg,#E76F51 0 8px,transparent 8px 14px);margin:0 0 1em;"></div>
      <p style="margin:0 0 1.2em;">«حالا بعداً حساب می‌کنیم» شروعِ بیشتر دلخوری‌هاست. قیمت و شرایط پرداخت را همان اول، شفاف و حتی‌الامکان مکتوب بگو. مشتری که از اول عدد را می‌داند، موقع تحویل چانه نمی‌زند.</p>

      <!-- placeholder: تصویر داخل متن -->
      <figure style="margin:1.8em 0;">
        <div style="aspect-ratio:16/9;border-radius:14px;background:linear-gradient(140deg,#EEF1F7,#D9DFEC);display:flex;align-items:center;justify-content:center;">
          <span style="font-size:12px;color:#3D4A6B;background:rgba(255,255,255,.72);border-radius:999px;padding:4px 13px;">تصویر داخل متن — نمونه فاکتور شفاف</span>
        </div>
        <figcaption style="text-align:center;font-size:12.5px;color:#6B7280;margin-top:10px;">فاکتور شفاف، پایان چانه‌زنی موقع تحویل</figcaption>
      </figure>

      <h2 style="font-size:24px;font-weight:700;color:#1F2A44;line-height:1.6;margin:1.8em 0 .6em;">۷. بازنکردن حساب سود در پایان ماه</h2>
      <div style="width:64px;height:2px;background-image:repeating-linear-gradient(90deg,#E76F51 0 8px,transparent 8px 14px);margin:0 0 1em;"></div>
      <p style="margin:0 0 1.2em;">اگر ندانی ماه گذشته واقعاً چقدر سود کردی، نمی‌فهمی کدام کارها می‌ارزند و کدام فقط وقتت را می‌گیرند. ماهی یک‌بار درآمد و هزینه را کنار هم بگذار؛ خیلی زود الگوها را می‌بینی — مثلاً اینکه تعمیرات کوچک شاید شلوغت کند اما سودش با دوخت سفارشی قابل مقایسه نیست.</p>

      <h3 style="font-size:19px;font-weight:700;color:#1F2A44;margin:1.6em 0 .5em;">جمع‌بندی</h3>
      <p style="margin:0 0 1.2em;">قیمت‌گذاری درست یعنی احترام به کار خودت: وقتت را حساب کن، نرخ‌نامه داشته باش، هزینه‌های پنهان را جا نگذار و آخر هر ماه حساب سود را باز کن. بقیه‌اش تمرین است.</p>
    </div>

    <!-- SHARE ROW (انتهای مقاله) -->
    <div data-rv style="opacity:0;transform:translateY(14px);transition:opacity .5s ease,transform .5s ease;max-width:720px;margin-inline:auto;padding:8px 24px 6px;display:flex;align-items:center;gap:10px;border-top:none;">
      <span style="font-size:13.5px;font-weight:700;color:#6B7280;">اشتراک‌گذاری:</span>
      <button class="hv36d784" data-copy-link aria-label="کپی لینک" style="display:inline-flex;align-items:center;gap:7px;min-height:44px;padding:8px 16px;background:#fff;border:1px solid var(--color-border);border-radius:999px;color:#1F2A44;font-size:13.5px;font-weight:700;transition:color .2s ease,border-color .2s ease;">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg>
        <span data-copy-label>کپی لینک</span>
      </button>
      <a class="hv36d784" href="https://t.me/share/url?url=[POST-URL]" target="_blank" rel="noopener" aria-label="تلگرام" style="display:inline-flex;align-items:center;justify-content:center;width:44px;height:44px;background:#fff;border:1px solid var(--color-border);border-radius:50%;color:#1F2A44;transition:color .2s ease,border-color .2s ease;">
        <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m22 2-7 20-4-9-9-4Z"/><path d="M22 2 11 13"/></svg>
      </a>
      <a class="hv36d784" href="https://wa.me/?text=[POST-URL]" target="_blank" rel="noopener" aria-label="واتس‌اپ" style="display:inline-flex;align-items:center;justify-content:center;width:44px;height:44px;background:#fff;border:1px solid var(--color-border);border-radius:50%;color:#1F2A44;transition:color .2s ease,border-color .2s ease;">
        <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M7.9 20A9 9 0 1 0 4 16.1L2 22Z"/><path d="M9.5 13.5c.5 1 1.5 1.5 2.5 1.5s2-.5 2.5-1.5"/></svg>
      </a>
    </div>
  </article>
  <!-- ================= پایان ناحیه محتوای مقاله ================= -->

  <!-- END CTA -->
  <section data-screen-label="سی‌تی‌ای مقاله">
    <div data-rv style="opacity:0;transform:translateY(16px);transition:opacity .5s ease,transform .5s ease;max-width:720px;margin-inline:auto;padding:28px 24px 10px;">
      <div style="position:relative;background:#FCEFEA;border-radius:20px;padding:38px 28px;text-align:center;overflow:hidden;">
        <div style="position:absolute;inset:11px;border:2px dashed #E76F51;border-radius:13px;opacity:.55;pointer-events:none;"></div>
        <h2 style="position:relative;font-size:26px;font-weight:700;color:#1F2A44;margin:0 0 10px;">حساب مزونت را دوختک نگه دارد</h2>
        <p style="position:relative;font-size:15.5px;color:#3D4A6B;margin:0 0 22px;">قیمت‌ها، سفارش‌ها و سود هر ماه، مرتب و جلوی چشمت.</p>
        <a class="hv791c00" href="/pricing" style="position:relative;display:inline-flex;align-items:center;justify-content:center;min-height:52px;background:var(--color-primary);color:#fff;font-weight:700;font-size:16.5px;padding:14px 32px;border-radius:13px;box-shadow:0 10px 26px rgba(231,111,81,.34);transition:background .2s ease,transform .2s ease;">۱۰ روز رایگان شروع کن</a>
      </div>
    </div>
  </section>

  <!-- RELATED POSTS -->
  <section data-screen-label="مقالات مرتبط">
    <div style="max-width:1080px;margin-inline:auto;padding:44px 24px 8px;">
      <h2 data-rv style="opacity:0;transform:translateY(14px);transition:opacity .5s ease,transform .5s ease;font-size:26px;font-weight:700;color:#1F2A44;margin:0 0 22px;">بیشتر بخوان</h2>
      <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(260px,1fr));gap:20px;">
        <?php foreach ($related as $p): ?>
          <a class="hvf83133" href="<?= e($p['href']) ?>" data-rv style="opacity:0;transform:translateY(16px);transition:opacity .5s ease,transform .5s ease,box-shadow .25s ease;display:flex;flex-direction:column;background:#fff;border:1px solid var(--color-border);border-radius:16px;overflow:hidden;box-shadow:var(--shadow-card);">
            <div style="aspect-ratio:16/9;background:linear-gradient(140deg,<?= e($p['g1']) ?>,<?= e($p['g2']) ?>);display:flex;align-items:center;justify-content:center;">
              <span style="font-size:11px;color:#1F2A44;background:rgba(255,255,255,.72);border-radius:999px;padding:3px 11px;opacity:.85;">تصویر مقاله</span>
            </div>
            <div style="display:flex;flex-direction:column;flex:1;padding:16px 18px 14px;">
              <span style="align-self:flex-start;background:#FCEFEA;color:#D45A3D;font-size:11px;font-weight:700;padding:3px 10px;border-radius:999px;"><?= e($p['cat']) ?></span>
              <h3 style="font-size:15.5px;font-weight:700;color:#1F2A44;line-height:1.7;margin:10px 0 8px;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;"><?= e($p['title']) ?></h3>
              <div style="display:flex;align-items:center;gap:8px;font-size:11.5px;color:#6B7280;border-top:2px dashed #F0EBE1;padding-top:10px;margin-top:auto;">
                <time datetime="<?= e($p['datetime']) ?>"><?= e($p['date']) ?></time>
                <span>·</span>
                <span><?= e($p['read']) ?> مطالعه</span>
              </div>
            </div>
          </a>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <!-- PREV / NEXT -->
  <nav aria-label="مقاله قبلی و بعدی" data-screen-label="ناوبری مقاله">
    <div data-rv style="opacity:0;transform:translateY(14px);transition:opacity .5s ease,transform .5s ease;max-width:1080px;margin-inline:auto;padding:26px 24px 66px;display:flex;flex-wrap:wrap;gap:14px;justify-content:space-between;">
      <a class="hvd7433b" href="/blog/loyal-customers" style="flex:1 1 260px;display:flex;align-items:center;gap:12px;background:#fff;border:1px solid var(--color-border);border-radius:14px;padding:16px 18px;transition:border-color .2s ease;">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#E76F51" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="flex:none;"><path d="m9 18 6-6-6-6"/></svg>
        <span>
          <span style="display:block;font-size:11.5px;color:#6B7280;margin-bottom:3px;">مقاله قبلی</span>
          <span style="display:block;font-size:14.5px;font-weight:700;color:#1F2A44;">چطور مشتری ثابت بسازیم؟</span>
        </span>
      </a>
      <a class="hvd7433b" href="/blog/photography-guide" style="flex:1 1 260px;display:flex;align-items:center;justify-content:flex-end;gap:12px;background:#fff;border:1px solid var(--color-border);border-radius:14px;padding:16px 18px;text-align:end;transition:border-color .2s ease;">
        <span>
          <span style="display:block;font-size:11.5px;color:#6B7280;margin-bottom:3px;">مقاله بعدی</span>
          <span style="display:block;font-size:14.5px;font-weight:700;color:#1F2A44;">راهنمای عکاسی از نمونه‌کار با گوشی</span>
        </span>
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#E76F51" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="flex:none;"><path d="m15 18-6-6 6-6"/></svg>
      </a>
    </div>
  </nav>

  <div id="footer" style="scroll-margin-top:96px;"><?php include __DIR__ . '/includes/site_footer.php'; ?></div>

</div>
<?php render_foot();
