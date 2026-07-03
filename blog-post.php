<?php
/**
 * Blog single post — full SSR from the database (published only).
 * Clean URL /blog/{slug} rewrites here (.htaccess / router.php).
 * Unknown slug -> styled Persian 404. SEO fallback chain:
 * seo_title -> title, seo_description -> excerpt.
 */
require_once __DIR__ . '/includes/layout.php';
require_once __DIR__ . '/includes/blog_lib.php';

$slug = (string) ($_GET['slug'] ?? '');
$post = $slug !== '' ? blog_post_by_slug($slug) : null;

if (!$post) {
    require __DIR__ . '/404.php';
    exit;
}

$related = blog_related($post, 3);
[$prev, $next] = blog_prev_next($post);
$post_url = rtrim(BASE_URL, '/') . '/blog/' . $post['slug'];
$share_tg = 'https://t.me/share/url?url=' . rawurlencode($post_url);
$share_wa = 'https://wa.me/?text=' . rawurlencode($post_url);
$pub_date = date('Y-m-d', strtotime($post['published_at']));

render_head([
    'title' => ($post['seo_title'] ?: $post['title']) . ' | دوختک',
    'description' => $post['seo_description'] ?: ($post['excerpt'] ?? ''),
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
    <a class="hv36d784" href="<?= e($share_tg) ?>" target="_blank" rel="noopener" aria-label="اشتراک در تلگرام" title="تلگرام" style="display:inline-flex;align-items:center;justify-content:center;width:44px;height:44px;background:#fff;border:1px solid var(--color-border);border-radius:50%;color:#1F2A44;box-shadow:0 6px 16px -8px rgba(31,42,68,.2);transition:color .2s ease,border-color .2s ease;">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m22 2-7 20-4-9-9-4Z"/><path d="M22 2 11 13"/></svg>
    </a>
    <a class="hv36d784" href="<?= e($share_wa) ?>" target="_blank" rel="noopener" aria-label="اشتراک در واتس‌اپ" title="واتس‌اپ" style="display:inline-flex;align-items:center;justify-content:center;width:44px;height:44px;background:#fff;border:1px solid var(--color-border);border-radius:50%;color:#1F2A44;box-shadow:0 6px 16px -8px rgba(31,42,68,.2);transition:color .2s ease,border-color .2s ease;">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M7.9 20A9 9 0 1 0 4 16.1L2 22Z"/><path d="M9 10a.5.5 0 0 0 1 0V9a.5.5 0 0 0-1 0Z"/><path d="M14 10a.5.5 0 0 0 1 0V9a.5.5 0 0 0-1 0Z"/><path d="M9.5 13.5c.5 1 1.5 1.5 2.5 1.5s2-.5 2.5-1.5"/></svg>
    </a>
  </div>

  <!-- ================= ARTICLE ================= -->
  <article data-screen-label="مقاله" style="position:relative;">
    <header style="max-width:760px;margin-inline:auto;padding:122px 24px 0;">
      <!-- breadcrumb -->
      <nav aria-label="مسیر" data-hero style="opacity:0;transform:translateY(12px);transition:opacity .5s ease,transform .5s ease;display:flex;align-items:center;flex-wrap:wrap;gap:7px;font-size:13px;color:#6B7280;">
        <a class="hvd6c76f" href="/blog" style="color:#6B7280;">مجله دوختک</a>
        <?php if (!empty($post['cat_title'])): ?>
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="transform:scaleX(-1);opacity:.6;"><path d="m9 18 6-6-6-6"/></svg>
        <a class="hvd6c76f" href="/blog?cat=<?= e($post['cat_slug']) ?>" style="color:#6B7280;"><?= e($post['cat_title']) ?></a>
        <?php endif; ?>
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="transform:scaleX(-1);opacity:.6;"><path d="m9 18 6-6-6-6"/></svg>
        <span style="color:#3D4A6B;"><?= e($post['title']) ?></span>
      </nav>
      <div data-hero style="opacity:0;transform:translateY(12px);transition:opacity .5s ease,transform .5s ease;display:flex;align-items:center;flex-wrap:wrap;gap:10px;font-size:13px;color:#6B7280;margin-top:20px;">
        <?php if (!empty($post['cat_title'])): ?>
        <span style="background:#FCEFEA;color:#D45A3D;font-weight:700;padding:4px 12px;border-radius:999px;"><?= e($post['cat_title']) ?></span>
        <?php endif; ?>
        <time datetime="<?= e($pub_date) ?>"><?= e(jalali_date($post['published_at'])) ?></time>
        <span>·</span>
        <span><?= e(blog_read_label($post)) ?></span>
        <span>·</span>
        <span>[نام نویسنده]</span>
      </div>
      <h1 id="postTitle" data-hero style="opacity:0;transform:translateY(12px);transition:opacity .5s ease,transform .5s ease;font-size:38px;line-height:1.5;font-weight:700;color:#1F2A44;margin:16px 0 0;letter-spacing:-.3px;"><?= e($post['title']) ?></h1>
      <?php if (!empty($post['cover_image'])): ?>
      <div data-hero style="opacity:0;transform:translateY(12px);transition:opacity .5s ease,transform .5s ease;aspect-ratio:16/9;border-radius:18px;margin-top:26px;overflow:hidden;box-shadow:0 20px 44px -26px rgba(31,42,68,.35);">
        <img src="/<?= e($post['cover_image']) ?>" alt="<?= e($post['cover_alt'] ?? $post['title']) ?>" style="width:100%;height:100%;object-fit:cover;display:block;">
      </div>
      <?php else: [$g1, $g2] = blog_gradient($post); ?>
      <div data-hero style="opacity:0;transform:translateY(12px);transition:opacity .5s ease,transform .5s ease;aspect-ratio:16/9;border-radius:18px;margin-top:26px;background:linear-gradient(140deg,<?= e($g1) ?>,<?= e($g2) ?>);display:flex;align-items:center;justify-content:center;box-shadow:0 20px 44px -26px rgba(31,42,68,.35);">
        <span style="font-size:12.5px;color:#8a5f4f;background:rgba(255,255,255,.72);border-radius:999px;padding:4px 14px;">تصویر شاخص مقاله — ۱۶:۹</span>
      </div>
      <?php endif; ?>
    </header>

    <!-- بدنه مقاله (SSR از دیتابیس؛ تایپوگرافی در page-blog-post.css) -->
    <div id="postBody" class="post-body" data-rv style="opacity:0;transform:translateY(14px);transition:opacity .5s ease,transform .5s ease;max-width:720px;margin-inline:auto;padding:34px 24px 10px;font-size:17.5px;line-height:2;color:#3D4A6B;">
<?= $post['body_html'] /* trusted admin-authored HTML from the editor */ ?>
    </div>

    <!-- SHARE ROW (انتهای مقاله) -->
    <div data-rv style="opacity:0;transform:translateY(14px);transition:opacity .5s ease,transform .5s ease;max-width:720px;margin-inline:auto;padding:8px 24px 6px;display:flex;align-items:center;gap:10px;border-top:none;">
      <span style="font-size:13.5px;font-weight:700;color:#6B7280;">اشتراک‌گذاری:</span>
      <button class="hv36d784" data-copy-link aria-label="کپی لینک" style="display:inline-flex;align-items:center;gap:7px;min-height:44px;padding:8px 16px;background:#fff;border:1px solid var(--color-border);border-radius:999px;color:#1F2A44;font-size:13.5px;font-weight:700;transition:color .2s ease,border-color .2s ease;">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg>
        <span data-copy-label>کپی لینک</span>
      </button>
      <a class="hv36d784" href="<?= e($share_tg) ?>" target="_blank" rel="noopener" aria-label="تلگرام" style="display:inline-flex;align-items:center;justify-content:center;width:44px;height:44px;background:#fff;border:1px solid var(--color-border);border-radius:50%;color:#1F2A44;transition:color .2s ease,border-color .2s ease;">
        <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m22 2-7 20-4-9-9-4Z"/><path d="M22 2 11 13"/></svg>
      </a>
      <a class="hv36d784" href="<?= e($share_wa) ?>" target="_blank" rel="noopener" aria-label="واتس‌اپ" style="display:inline-flex;align-items:center;justify-content:center;width:44px;height:44px;background:#fff;border:1px solid var(--color-border);border-radius:50%;color:#1F2A44;transition:color .2s ease,border-color .2s ease;">
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

  <?php if ($related): ?>
  <!-- RELATED POSTS -->
  <section data-screen-label="مقالات مرتبط">
    <div style="max-width:1080px;margin-inline:auto;padding:44px 24px 8px;">
      <h2 data-rv style="opacity:0;transform:translateY(14px);transition:opacity .5s ease,transform .5s ease;font-size:26px;font-weight:700;color:#1F2A44;margin:0 0 22px;">بیشتر بخوان</h2>
      <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(260px,1fr));gap:20px;">
        <?php foreach ($related as $p): [$g1, $g2] = blog_gradient($p); ?>
          <a class="hvf83133" href="/blog/<?= e($p['slug']) ?>" data-rv style="opacity:0;transform:translateY(16px);transition:opacity .5s ease,transform .5s ease,box-shadow .25s ease;display:flex;flex-direction:column;background:#fff;border:1px solid var(--color-border);border-radius:16px;overflow:hidden;box-shadow:var(--shadow-card);">
            <?php if (!empty($p['cover_image'])): ?>
            <div style="aspect-ratio:16/9;overflow:hidden;">
              <img src="/<?= e($p['cover_image']) ?>" alt="<?= e($p['cover_alt'] ?? $p['title']) ?>" loading="lazy" style="width:100%;height:100%;object-fit:cover;display:block;">
            </div>
            <?php else: ?>
            <div style="aspect-ratio:16/9;background:linear-gradient(140deg,<?= e($g1) ?>,<?= e($g2) ?>);display:flex;align-items:center;justify-content:center;">
              <span style="font-size:11px;color:#1F2A44;background:rgba(255,255,255,.72);border-radius:999px;padding:3px 11px;opacity:.85;">تصویر مقاله</span>
            </div>
            <?php endif; ?>
            <div style="display:flex;flex-direction:column;flex:1;padding:16px 18px 14px;">
              <span style="align-self:flex-start;background:#FCEFEA;color:#D45A3D;font-size:11px;font-weight:700;padding:3px 10px;border-radius:999px;"><?= e($p['cat_title'] ?? 'بدون دسته') ?></span>
              <h3 style="font-size:15.5px;font-weight:700;color:#1F2A44;line-height:1.7;margin:10px 0 8px;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;"><?= e($p['title']) ?></h3>
              <div style="display:flex;align-items:center;gap:8px;font-size:11.5px;color:#6B7280;border-top:2px dashed #F0EBE1;padding-top:10px;margin-top:auto;">
                <time datetime="<?= e(date('Y-m-d', strtotime($p['published_at']))) ?>"><?= e(jalali_date($p['published_at'])) ?></time>
                <span>·</span>
                <span><?= e(blog_read_label($p)) ?></span>
              </div>
            </div>
          </a>
        <?php endforeach; ?>
      </div>
    </div>
  </section>
  <?php endif; ?>

  <?php if ($prev || $next): ?>
  <!-- PREV / NEXT -->
  <nav aria-label="مقاله قبلی و بعدی" data-screen-label="ناوبری مقاله">
    <div data-rv style="opacity:0;transform:translateY(14px);transition:opacity .5s ease,transform .5s ease;max-width:1080px;margin-inline:auto;padding:26px 24px 66px;display:flex;flex-wrap:wrap;gap:14px;justify-content:space-between;">
      <?php if ($prev): ?>
      <a class="hvd7433b" href="/blog/<?= e($prev['slug']) ?>" style="flex:1 1 260px;display:flex;align-items:center;gap:12px;background:#fff;border:1px solid var(--color-border);border-radius:14px;padding:16px 18px;transition:border-color .2s ease;">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#E76F51" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="flex:none;"><path d="m9 18 6-6-6-6"/></svg>
        <span>
          <span style="display:block;font-size:11.5px;color:#6B7280;margin-bottom:3px;">مقاله قبلی</span>
          <span style="display:block;font-size:14.5px;font-weight:700;color:#1F2A44;"><?= e($prev['title']) ?></span>
        </span>
      </a>
      <?php endif; ?>
      <?php if ($next): ?>
      <a class="hvd7433b" href="/blog/<?= e($next['slug']) ?>" style="flex:1 1 260px;display:flex;align-items:center;justify-content:flex-end;gap:12px;background:#fff;border:1px solid var(--color-border);border-radius:14px;padding:16px 18px;text-align:end;transition:border-color .2s ease;">
        <span>
          <span style="display:block;font-size:11.5px;color:#6B7280;margin-bottom:3px;">مقاله بعدی</span>
          <span style="display:block;font-size:14.5px;font-weight:700;color:#1F2A44;"><?= e($next['title']) ?></span>
        </span>
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#E76F51" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="flex:none;"><path d="m15 18-6-6 6-6"/></svg>
      </a>
      <?php endif; ?>
    </div>
  </nav>
  <?php endif; ?>

  <div id="footer" style="scroll-margin-top:96px;"><?php include __DIR__ . '/includes/site_footer.php'; ?></div>

</div>
<?php render_foot();
