<?php
/**
 * Blog archive — full SSR from the database (published posts only):
 * featured latest, server-side category chips (?cat=slug), grid with
 * pagination. Markup is the approved design, unchanged.
 */
require_once __DIR__ . '/includes/layout.php';
public_boot(); // redirect middleware + page cache
require_once __DIR__ . '/includes/blog_lib.php';

$cats = blog_categories();
$cat_slug = trim((string) ($_GET['cat'] ?? ''));
$active_cat = $cat_slug !== '' ? blog_category_by_slug($cat_slug) : null;

$page = max(1, (int) ($_GET['page'] ?? 1));
$per_page = 9;
[$posts, $total] = blog_published($active_cat['id'] ?? null, $page, $per_page);
$pages = max(1, (int) ceil($total / $per_page));

$featured = blog_latest(1)[0] ?? null;

/** Archive URL keeping the category filter. */
function blog_url(?string $cat, int $page = 1): string
{
    $args = array_filter(['cat' => $cat, 'page' => $page > 1 ? $page : null]);
    return '/blog' . ($args ? '?' . http_build_query($args) : '');
}

render_head([
    'page_key' => 'blog',
    'css' => '/assets/css/page-blog.css',
    'js' => '/assets/js/page-blog.js',
]);
?>
<div dir="rtl" lang="fa" style="position:relative;min-height:100vh;background-color:#FAF8F4;background-image:linear-gradient(#EDEAE3 1px,transparent 1px),linear-gradient(90deg,#EDEAE3 1px,transparent 1px);background-size:46px 46px;overflow-x:hidden;line-height:1.6;">

  <?php include __DIR__ . '/includes/site_header.php'; ?>

  <!-- COMPACT HERO -->
  <header data-screen-label="هیرو مجله" style="position:relative;">
    <div id="heroWrap" style="max-width:820px;margin-inline:auto;padding:128px 24px 30px;text-align:center;">
      <h1 id="heroTitle" data-hero style="opacity:0;transform:translateY(12px);transition:opacity .5s ease,transform .5s ease;font-size:44px;line-height:1.45;font-weight:700;color:#1F2A44;margin:0;letter-spacing:-.5px;">
        <span style="position:relative;white-space:nowrap;">مجله<svg width="100%" height="14" viewBox="0 0 140 14" preserveAspectRatio="none" style="position:absolute;inset-inline-start:0;bottom:-6px;overflow:visible;"><path d="M3 8 Q40 2 72 7 T137 6" stroke="#E76F51" stroke-width="6" fill="none" stroke-linecap="round" opacity=".85"/></svg></span>
        دوختک
      </h1>
      <p data-hero style="opacity:0;transform:translateY(12px);transition:opacity .5s ease,transform .5s ease;font-size:17px;line-height:1.9;color:#3D4A6B;margin:24px auto 0;max-width:520px;">نکته‌ها و ترفندهای مدیریت مزون، جذب مشتری و رشد کسب‌وکار خیاطی</p>
    </div>
  </header>

  <?php if ($featured): [$fg1, $fg2] = blog_gradient($featured); ?>
  <!-- FEATURED POST -->
  <section data-screen-label="مقاله ویژه">
    <div style="max-width:1080px;margin-inline:auto;padding:10px 24px 8px;">
      <a class="hv2f5ab8" href="/blog/<?= e($featured['slug']) ?>" data-rv style="opacity:0;transform:translateY(16px);transition:opacity .5s ease,transform .5s ease,outline .18s ease,box-shadow .25s ease;display:block;background:#fff;border:1px solid var(--color-border);border-radius:20px;overflow:hidden;box-shadow:var(--shadow-card);">
        <div id="featRow" style="display:flex;flex-wrap:wrap;align-items:stretch;">
          <?php if (!empty($featured['cover_image'])): ?>
          <div style="flex:1 1 380px;min-height:240px;overflow:hidden;">
            <img src="/<?= e($featured['cover_image']) ?>" alt="<?= e($featured['cover_alt'] ?? $featured['title']) ?>" style="width:100%;height:100%;min-height:240px;object-fit:cover;display:block;">
          </div>
          <?php else: ?>
          <div style="flex:1 1 380px;min-height:240px;background:linear-gradient(140deg,<?= e($fg1) ?>,<?= e($fg2) ?>);display:flex;align-items:center;justify-content:center;">
            <span style="font-size:12px;color:#8a5f4f;background:rgba(255,255,255,.72);border-radius:999px;padding:4px 13px;">تصویر مقاله — ۱۶:۹</span>
          </div>
          <?php endif; ?>
          <div style="flex:1 1 380px;padding:32px 30px;display:flex;flex-direction:column;justify-content:center;">
            <div style="display:flex;align-items:center;flex-wrap:wrap;gap:10px;font-size:12.5px;color:#6B7280;">
              <span style="background:#FCEFEA;color:#D45A3D;font-weight:700;padding:4px 12px;border-radius:999px;"><?= e($featured['cat_title'] ?? 'بدون دسته') ?></span>
              <time datetime="<?= e(date('Y-m-d', strtotime($featured['published_at']))) ?>"><?= e(jalali_date($featured['published_at'])) ?></time>
              <span>·</span>
              <span><?= e(blog_read_label($featured)) ?></span>
            </div>
            <h2 style="font-size:27px;font-weight:700;color:#1F2A44;line-height:1.6;margin:14px 0 10px;"><?= e($featured['title']) ?></h2>
            <p style="font-size:15.5px;line-height:1.9;color:#3D4A6B;margin:0 0 18px;"><?= e($featured['excerpt'] ?? '') ?></p>
            <span style="display:inline-flex;align-items:center;gap:7px;color:#E76F51;font-weight:700;font-size:15px;">
              ادامه مطلب
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="transform:scaleX(-1);"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
            </span>
          </div>
        </div>
      </a>
    </div>
  </section>
  <?php endif; ?>

  <!-- CATEGORY CHIPS (server-side filter) -->
  <nav data-screen-label="فیلتر دسته‌ها" aria-label="دسته‌بندی مقالات">
    <div id="chipRow" data-rv style="opacity:0;transform:translateY(14px);transition:opacity .5s ease,transform .5s ease;display:flex;justify-content:center;gap:8px;max-width:1080px;margin-inline:auto;padding:26px 24px 6px;flex-wrap:wrap;overflow-x:auto;scrollbar-width:none;">
      <?php
        $chips = array_merge([['slug' => '', 'title' => 'همه']], $cats);
        foreach ($chips as $c):
          $on = ($c['slug'] === '' && !$active_cat) || ($active_cat && $active_cat['slug'] === $c['slug']);
      ?>
        <a href="<?= e(blog_url($c['slug'] ?: null)) ?>" <?= $on ? 'aria-current="true"' : '' ?> style="flex:none;display:inline-flex;align-items:center;min-height:42px;padding:8px 18px;border-radius:999px;font-size:14px;font-weight:700;border:1px solid <?= $on ? 'var(--color-primary)' : 'var(--color-border)' ?>;background:<?= $on ? 'var(--color-primary)' : '#fff' ?>;color:<?= $on ? '#fff' : '#3D4A6B' ?>;transition:background .25s ease,color .25s ease,border-color .25s ease;scroll-snap-align:center;"><?= e($c['title']) ?></a>
      <?php endforeach; ?>
    </div>
  </nav>

  <!-- POSTS GRID -->
  <section data-screen-label="گرید مقالات">
    <div style="max-width:1080px;margin-inline:auto;padding:22px 24px 10px;">
      <?php if (!$posts): ?>
      <div data-rv style="opacity:0;transform:translateY(16px);transition:opacity .5s ease,transform .5s ease;background:#fff;border:1px solid var(--color-border);border-radius:16px;padding:56px 20px;text-align:center;">
        <div style="font-size:16px;font-weight:700;color:#1F2A44;margin-bottom:8px;">در این دسته هنوز مقاله‌ای منتشر نشده است.</div>
        <a class="hvd6c76f" href="/blog" style="color:#E76F51;font-weight:700;font-size:14.5px;">همه مقاله‌ها</a>
      </div>
      <?php else: ?>
      <div id="postGrid" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:20px;opacity:1;transition:opacity .2s ease;">
        <?php foreach ($posts as $p): [$g1, $g2] = blog_gradient($p); ?>
          <!-- الگوی واحد کارت مقاله -->
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
            <div style="display:flex;flex-direction:column;flex:1;padding:18px 20px 16px;">
              <span style="align-self:flex-start;background:#FCEFEA;color:#D45A3D;font-size:11.5px;font-weight:700;padding:3px 11px;border-radius:999px;"><?= e($p['cat_title'] ?? 'بدون دسته') ?></span>
              <h3 style="font-size:17px;font-weight:700;color:#1F2A44;line-height:1.7;margin:11px 0 7px;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;background-image:repeating-linear-gradient(90deg,#E76F51 0 7px,transparent 7px 12px);background-repeat:no-repeat;background-size:0% 2px;background-position:100% 100%;transition:background-size .3s ease;"><?= e($p['title']) ?></h3>
              <p style="font-size:13.5px;line-height:1.85;color:#3D4A6B;margin:0 0 14px;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;flex:1;"><?= e($p['excerpt'] ?? '') ?></p>
              <div style="display:flex;align-items:center;gap:8px;font-size:12px;color:#6B7280;border-top:2px dashed #F0EBE1;padding-top:11px;">
                <time datetime="<?= e(date('Y-m-d', strtotime($p['published_at']))) ?>"><?= e(jalali_date($p['published_at'])) ?></time>
                <span>·</span>
                <span><?= e(blog_read_label($p)) ?></span>
              </div>
            </div>
          </a>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>

      <?php if ($pages > 1): ?>
      <!-- PAGINATION -->
      <nav aria-label="صفحه‌بندی" data-rv style="opacity:0;transform:translateY(14px);transition:opacity .5s ease,transform .5s ease;display:flex;justify-content:center;align-items:center;gap:8px;padding:34px 0 8px;">
        <?php if ($page > 1): ?>
        <a class="hv2fb6f5" href="<?= e(blog_url($active_cat['slug'] ?? null, $page - 1)) ?>" style="display:inline-flex;align-items:center;justify-content:center;gap:6px;min-height:44px;padding:0 16px;border-radius:12px;background:#fff;border:1px solid var(--color-border);color:#1F2A44;font-weight:700;font-size:14px;transition:border-color .2s ease,color .2s ease;">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
          قبلی
        </a>
        <?php endif; ?>
        <?php for ($p = 1; $p <= $pages; $p++): ?>
          <?php if ($p === $page): ?>
          <a href="<?= e(blog_url($active_cat['slug'] ?? null, $p)) ?>" aria-current="page" style="display:inline-flex;align-items:center;justify-content:center;min-width:44px;min-height:44px;border-radius:12px;background:var(--color-primary);color:#fff;font-weight:700;font-size:15px;"><?= fa_digits($p) ?></a>
          <?php else: ?>
          <a class="hv2fb6f5" href="<?= e(blog_url($active_cat['slug'] ?? null, $p)) ?>" style="display:inline-flex;align-items:center;justify-content:center;min-width:44px;min-height:44px;border-radius:12px;background:#fff;border:1px solid var(--color-border);color:#3D4A6B;font-weight:700;font-size:15px;transition:border-color .2s ease,color .2s ease;"><?= fa_digits($p) ?></a>
          <?php endif; ?>
        <?php endfor; ?>
        <?php if ($page < $pages): ?>
        <a class="hv2fb6f5" href="<?= e(blog_url($active_cat['slug'] ?? null, $page + 1)) ?>" style="display:inline-flex;align-items:center;justify-content:center;gap:6px;min-height:44px;padding:0 16px;border-radius:12px;background:#fff;border:1px solid var(--color-border);color:#1F2A44;font-weight:700;font-size:14px;transition:border-color .2s ease,color .2s ease;">
          بعدی
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="transform:scaleX(-1);"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
        </a>
        <?php endif; ?>
      </nav>
      <?php endif; ?>
    </div>
  </section>

  <!-- MID CTA -->
  <section data-screen-label="سی‌تی‌ای مجله">
    <div data-rv style="opacity:0;transform:translateY(16px);transition:opacity .5s ease,transform .5s ease;max-width:1000px;margin-inline:auto;padding:26px 24px 66px;">
      <div style="position:relative;background:#FCEFEA;border-radius:20px;padding:32px 28px;overflow:hidden;display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;gap:18px;">
        <div style="position:absolute;inset:11px;border:2px dashed #E76F51;border-radius:13px;opacity:.55;pointer-events:none;"></div>
        <div style="position:relative;font-size:19px;font-weight:700;color:#1F2A44;line-height:1.7;">به‌جای خواندن درباره مدیریت مزون، از امروز انجامش بده</div>
        <a class="hv791c00" href="/pricing" style="position:relative;display:inline-flex;align-items:center;justify-content:center;min-height:50px;background:var(--color-primary);color:#fff;font-weight:700;font-size:16px;padding:13px 28px;border-radius:13px;box-shadow:0 8px 22px rgba(231,111,81,.3);transition:background .2s ease,transform .2s ease;">۱۰ روز رایگان دوختک</a>
      </div>
    </div>
  </section>

  <div id="footer" style="scroll-margin-top:96px;"><?php include __DIR__ . '/includes/site_footer.php'; ?></div>

</div>
<?php render_foot();
