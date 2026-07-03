<?php
/**
 * Blog post editor — two-column per the approved admin design.
 * Main: title, editable latin auto-slug, self-hosted Quill 2 (RTL)
 * with H2/H3/bold/list/link/blockquote and image insert through the
 * Phase-2 media endpoint. Side: publish card, category, cover image
 * (upload + alt + preview + remove), SEO card with char counters,
 * reading minutes. Unsaved-changes warning; slug uniqueness enforced.
 */
require_once __DIR__ . '/../includes/admin_layout.php';
require_once __DIR__ . '/../includes/cache.php';

require_admin();

$id = (int) ($_GET['id'] ?? $_POST['id'] ?? 0);
$error = '';

// Defaults for a new post.
$post = [
    'id' => 0, 'title' => '', 'slug' => '', 'excerpt' => '', 'body_html' => '',
    'cover_image' => null, 'cover_alt' => null, 'category_id' => null,
    'status' => 'draft', 'published_at' => null,
    'seo_title' => null, 'seo_description' => null, 'reading_minutes' => 5,
];

if ($id > 0) {
    $stmt = db()->prepare('SELECT * FROM blog_posts WHERE id = ?');
    $stmt->execute([$id]);
    $found = $stmt->fetch();
    if (!$found) {
        header('Location: /admin/blog.php?toast=' . rawurlencode('مقاله پیدا نشد') . '&toast_kind=err');
        exit;
    }
    $post = $found;
}

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    if (!csrf_verify($_POST['csrf_token'] ?? null)) {
        http_response_code(403);
        $error = 'درخواست نامعتبر (CSRF). صفحه را از نو باز کن.';
    } else {
        // Collect submitted values over the loaded row.
        $post['title'] = trim((string) ($_POST['title'] ?? ''));
        $post['slug'] = slugify((string) ($_POST['slug'] ?? ''));
        $post['excerpt'] = trim((string) ($_POST['excerpt'] ?? ''));
        // Trusted admin-authored HTML. Quill's getSemanticHTML() serializes
        // ordinary spaces as &nbsp;, which breaks Persian line wrapping —
        // normalize them back to plain spaces.
        $post['body_html'] = str_replace(["\u{00A0}", '&nbsp;'], ' ', (string) ($_POST['body_html'] ?? ''));
        $post['category_id'] = (int) ($_POST['category_id'] ?? 0) ?: null;
        $post['status'] = ($_POST['status'] ?? 'draft') === 'published' ? 'published' : 'draft';
        $post['cover_image'] = trim((string) ($_POST['cover_image'] ?? '')) ?: null;
        $post['cover_alt'] = trim((string) ($_POST['cover_alt'] ?? '')) ?: null;
        $post['seo_title'] = trim((string) ($_POST['seo_title'] ?? '')) ?: null;
        $post['seo_description'] = trim((string) ($_POST['seo_description'] ?? '')) ?: null;
        $post['reading_minutes'] = max(1, (int) en_digits((string) ($_POST['reading_minutes'] ?? '5')));

        if ($post['title'] === '') {
            $error = 'عنوان مقاله را بنویس.';
        } elseif ($post['slug'] === '') {
            $error = 'نامک لاتین را وارد کن.';
        } else {
            $stmt = db()->prepare('SELECT id FROM blog_posts WHERE slug = ? AND id != ?');
            $stmt->execute([$post['slug'], $id]);
            if ($stmt->fetch()) {
                $error = 'این نامک قبلاً استفاده شده است.';
            }
        }

        if ($error === '') {
            // First publish stamps published_at; it never moves afterwards.
            if ($post['status'] === 'published' && empty($post['published_at'])) {
                $post['published_at'] = date('Y-m-d H:i:s');
            }
            if ($id > 0) {
                db()->prepare(
                    'UPDATE blog_posts SET title=?, slug=?, excerpt=?, body_html=?, cover_image=?, cover_alt=?,
                     category_id=?, status=?, published_at=?, seo_title=?, seo_description=?, reading_minutes=?
                     WHERE id=?'
                )->execute([
                    $post['title'], $post['slug'], $post['excerpt'], $post['body_html'],
                    $post['cover_image'], $post['cover_alt'], $post['category_id'], $post['status'],
                    $post['published_at'], $post['seo_title'], $post['seo_description'], $post['reading_minutes'], $id,
                ]);
            } else {
                db()->prepare(
                    'INSERT INTO blog_posts (title, slug, excerpt, body_html, cover_image, cover_alt,
                     category_id, status, published_at, seo_title, seo_description, reading_minutes)
                     VALUES (?,?,?,?,?,?,?,?,?,?,?,?)'
                )->execute([
                    $post['title'], $post['slug'], $post['excerpt'], $post['body_html'],
                    $post['cover_image'], $post['cover_alt'], $post['category_id'], $post['status'],
                    $post['published_at'], $post['seo_title'], $post['seo_description'], $post['reading_minutes'],
                ]);
                $id = (int) db()->lastInsertId();
            }
            cache_flush();
            header('Location: /admin/blog-edit.php?id=' . $id . '&toast=' . rawurlencode('ذخیره شد'));
            exit;
        }
    }
}

$cats = db()->query('SELECT id, title FROM blog_categories ORDER BY sort_order, id')->fetchAll();

admin_page_start($id > 0 ? 'ویرایش مقاله' : 'مقاله جدید', 'blog', $error !== '' ? ['toast' => $error, 'toast_kind' => 'err'] : []);
?>
<link rel="stylesheet" href="/assets/vendor/quill/quill.snow.css">
<div>
  <div id="dirtyBar" style="display:none;align-items:center;gap:9px;background:#FDF6E7;border:1px solid #F3E2B8;border-radius:10px;padding:10px 14px;font-size:12.5px;color:#8a6d1f;margin-bottom:14px;">
    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#EEA62B" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/><path d="M12 9v4"/><path d="M12 17h.01"/></svg>
    تغییرات ذخیره‌نشده داری — قبل از خروج ذخیره کن.
  </div>

  <form id="postForm" method="post" action="/admin/blog-edit.php<?= $id > 0 ? '?id=' . $id : '' ?>">
    <?= csrf_field() ?>
    <input type="hidden" name="id" value="<?= $id ?>">
    <input type="hidden" name="body_html" id="bodyHtml">
    <input type="hidden" name="cover_image" id="coverPath" value="<?= e($post['cover_image'] ?? '') ?>">

    <div id="editorRow" style="display:flex;gap:16px;align-items:flex-start;">
      <!-- Main column -->
      <div style="flex:1 1 0;min-width:0;">
        <div class="a-card" style="padding:18px;">
          <input type="text" name="title" id="edTitle" placeholder="عنوان…" value="<?= e($post['title']) ?>" style="width:100%;min-height:48px;padding:10px 2px;font-size:20px;font-weight:700;color:#1F2A44;background:none;border:none;border-bottom:2px dashed #F0EBE1;outline:none;margin-bottom:12px;">
          <div style="display:flex;align-items:center;gap:8px;font-size:12.5px;color:#6B7280;margin-bottom:14px;">
            <span>نامک:</span>
            <input class="a-input" type="text" dir="ltr" name="slug" id="edSlug" value="<?= e($post['slug']) ?>" style="flex:1;max-width:280px;min-height:36px;padding:6px 10px;font-size:12.5px;border-radius:8px;">
          </div>
          <!-- Editor toolbar (design buttons driving Quill formats) -->
          <div id="edToolbar" style="display:flex;flex-wrap:wrap;gap:3px;border:1px solid #E8E6E1;border-bottom:none;border-radius:9px 9px 0 0;padding:6px;background:#FBFAF7;">
            <button type="button" class="ql-header a-ql" value="2" title="تیتر H2">H2</button>
            <button type="button" class="ql-header a-ql" value="3" title="تیتر H3">H3</button>
            <button type="button" class="ql-bold a-ql" title="بولد">B</button>
            <button type="button" class="ql-list a-ql" value="bullet" title="لیست">
              <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="8" x2="21" y1="6" y2="6"/><line x1="8" x2="21" y1="12" y2="12"/><line x1="8" x2="21" y1="18" y2="18"/><line x1="3" x2="3.01" y1="6" y2="6"/><line x1="3" x2="3.01" y1="12" y2="12"/><line x1="3" x2="3.01" y1="18" y2="18"/></svg>
            </button>
            <button type="button" class="ql-link a-ql" title="لینک">
              <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg>
            </button>
            <button type="button" class="ql-image a-ql" title="تصویر">
              <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="3" rx="2"/><circle cx="9" cy="9" r="2"/><path d="m21 15-3.09-3.09a2 2 0 0 0-2.82 0L6 21"/></svg>
            </button>
            <button type="button" class="ql-blockquote a-ql" title="نقل‌قول">
              <svg width="15" height="15" viewBox="0 0 24 24" fill="#c9c3b6"><path d="M10 7 7 12v5h5v-5H9l1.5-3zM19 7l-3 5v5h5v-5h-3l1.5-3z"/></svg>
            </button>
          </div>
          <div id="edBody" dir="rtl"><?= $post['body_html'] /* trusted admin HTML */ ?></div>
        </div>
        <div class="a-card a-card-pad" style="margin-top:12px;">
          <label class="a-label-strong" for="edExcerpt">خلاصه (برای کارت‌های آرشیو و متا)</label>
          <textarea class="a-input" id="edExcerpt" name="excerpt" rows="3" style="min-height:0;resize:vertical;font-size:13.5px;line-height:1.9;"><?= e($post['excerpt'] ?? '') ?></textarea>
        </div>
      </div>

      <!-- Side column -->
      <div id="editorSide" style="flex:0 0 280px;display:flex;flex-direction:column;gap:12px;">
        <div class="a-card" style="padding:15px;">
          <b style="display:block;font-size:13px;color:#1F2A44;margin-bottom:10px;">انتشار</b>
          <select class="a-input" name="status" style="margin-bottom:10px;">
            <option value="draft" <?= $post['status'] === 'draft' ? 'selected' : '' ?>>پیش‌نویس</option>
            <option value="published" <?= $post['status'] === 'published' ? 'selected' : '' ?>>انتشار</option>
          </select>
          <div style="font-size:12px;color:#9a9587;margin-bottom:12px;">تاریخ انتشار: <?= $post['published_at'] ? e(jalali_date($post['published_at'])) : '—' ?></div>
          <button type="submit" class="a-btn a-btn-primary" style="width:100%;min-height:44px;font-size:14px;">ذخیره</button>
        </div>

        <div class="a-card" style="padding:15px;">
          <b style="display:block;font-size:13px;color:#1F2A44;margin-bottom:10px;">دسته‌بندی</b>
          <select class="a-input" name="category_id">
            <option value="">بدون دسته</option>
            <?php foreach ($cats as $c): ?>
            <option value="<?= (int) $c['id'] ?>" <?= (int) ($post['category_id'] ?? 0) === (int) $c['id'] ? 'selected' : '' ?>><?= e($c['title']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="a-card" style="padding:15px;">
          <b style="display:block;font-size:13px;color:#1F2A44;margin-bottom:10px;">تصویر شاخص</b>
          <div id="coverPreviewWrap" style="<?= $post['cover_image'] ? '' : 'display:none;' ?>position:relative;border-radius:9px;overflow:hidden;margin-bottom:8px;">
            <img id="coverPreview" src="<?= $post['cover_image'] ? '/' . e($post['cover_image']) : '' ?>" alt="" style="display:block;width:100%;aspect-ratio:16/9;object-fit:cover;">
            <button type="button" id="coverRemove" aria-label="حذف تصویر" style="position:absolute;top:6px;inset-inline-start:6px;display:inline-flex;align-items:center;justify-content:center;width:28px;height:28px;background:rgba(31,42,68,.75);border:none;border-radius:7px;color:#fff;cursor:pointer;">
              <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
            </button>
          </div>
          <button type="button" id="coverUpload" style="<?= $post['cover_image'] ? 'display:none;' : '' ?>width:100%;min-height:74px;background:#F7F6F3;border:1px dashed #d9d3c6;border-radius:9px;font-size:12.5px;color:#6B7280;cursor:pointer;" class="a-cover-btn">+ آپلود تصویر</button>
          <input type="file" id="coverFile" accept="image/jpeg,image/png,image/webp" style="display:none;">
          <input class="a-input" type="text" name="cover_alt" placeholder="متن جایگزین تصویر…" value="<?= e($post['cover_alt'] ?? '') ?>" style="min-height:36px;font-size:12.5px;margin-top:8px;">
        </div>

        <div class="a-card" style="padding:15px;">
          <b style="display:block;font-size:13px;color:#1F2A44;margin-bottom:10px;">سئو</b>
          <input class="a-input" type="text" name="seo_title" id="seoTitle" placeholder="عنوان سئو" value="<?= e($post['seo_title'] ?? '') ?>" style="min-height:38px;font-size:12.5px;border-radius:8px;margin-bottom:2px;">
          <div style="font-size:11px;color:#9a9587;text-align:end;margin-bottom:8px;"><span id="seoTitleCount">۰</span> / ۶۰</div>
          <textarea class="a-input" name="seo_description" id="seoDesc" rows="3" placeholder="توضیحات متا…" style="min-height:0;resize:vertical;font-size:12.5px;border-radius:8px;"><?= e($post['seo_description'] ?? '') ?></textarea>
          <div style="font-size:11px;text-align:end;margin-top:4px;color:#9a9587;"><span id="seoDescCount">۰</span> / ۱۶۰</div>
          <div style="display:flex;align-items:center;gap:8px;font-size:12.5px;color:#6B7280;margin-top:8px;">
            زمان مطالعه:
            <input class="a-input" type="text" name="reading_minutes" value="<?= e(fa_digits($post['reading_minutes'])) ?>" style="width:52px;min-height:36px;padding:6px 9px;font-size:13px;border-radius:8px;text-align:center;">
            دقیقه
          </div>
        </div>
      </div>
    </div>
  </form>
</div>

<script src="/assets/vendor/quill/quill.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
  'use strict';
  var dirty = false;
  var dirtyBar = document.getElementById('dirtyBar');
  function markDirty() { if (!dirty) { dirty = true; dirtyBar.style.display = 'flex'; } }

  /* Quill 2 — RTL, custom design toolbar, image via media endpoint */
  var quill = new Quill('#edBody', {
    theme: 'snow',
    placeholder: 'متن مقاله…',
    modules: {
      toolbar: {
        container: '#edToolbar',
        handlers: {
          image: function () {
            var input = document.createElement('input');
            input.type = 'file';
            input.accept = 'image/jpeg,image/png,image/webp';
            input.onchange = function () {
              if (!input.files.length) return;
              var fd = new FormData();
              fd.append('image', input.files[0]);
              fd.append('csrf_token', document.querySelector('meta[name="csrf-token"]').content);
              fetch('/admin/media_upload.php', { method: 'POST', body: fd })
                .then(function (r) { return r.json(); })
                .then(function (res) {
                  if (!res.ok) { DKA.toast(res.error || 'آپلود ناموفق بود.', 'err'); return; }
                  var range = quill.getSelection(true);
                  quill.insertEmbed(range.index, 'image', '/' + res.media.file_path, 'user');
                  quill.setSelection(range.index + 1);
                })
                .catch(function () { DKA.toast('خطا در ارتباط با سرور.', 'err'); });
            };
            input.click();
          }
        }
      }
    }
  });
  quill.root.setAttribute('dir', 'rtl');
  quill.on('text-change', function (d, o, source) { if (source === 'user') markDirty(); });

  /* Auto-slug (latin) from title while creating a new post */
  var title = document.getElementById('edTitle');
  var slug = document.getElementById('edSlug');
  var slugTouched = slug.value !== '';
  slug.addEventListener('input', function () { slugTouched = true; markDirty(); });
  title.addEventListener('input', function () {
    markDirty();
    if (slugTouched) return;
    slug.value = title.value.trim().toLowerCase()
      .replace(/[^a-z0-9\s-]/g, '').replace(/\s+/g, '-').replace(/-+/g, '-').replace(/^-|-$/g, '');
  });

  /* dirty tracking on the rest of the form */
  document.querySelectorAll('#postForm input, #postForm select, #postForm textarea').forEach(function (el) {
    el.addEventListener('input', markDirty);
    el.addEventListener('change', markDirty);
  });

  /* Cover image upload / remove */
  var coverFile = document.getElementById('coverFile');
  document.getElementById('coverUpload').addEventListener('click', function () { coverFile.click(); });
  coverFile.addEventListener('change', function () {
    if (!coverFile.files.length) return;
    var fd = new FormData();
    fd.append('image', coverFile.files[0]);
    fd.append('csrf_token', document.querySelector('meta[name="csrf-token"]').content);
    fetch('/admin/media_upload.php', { method: 'POST', body: fd })
      .then(function (r) { return r.json(); })
      .then(function (res) {
        if (!res.ok) { DKA.toast(res.error || 'آپلود ناموفق بود.', 'err'); return; }
        document.getElementById('coverPath').value = res.media.file_path;
        document.getElementById('coverPreview').src = '/' + res.media.file_path;
        document.getElementById('coverPreviewWrap').style.display = '';
        document.getElementById('coverUpload').style.display = 'none';
        markDirty();
      })
      .catch(function () { DKA.toast('خطا در ارتباط با سرور.', 'err'); })
      .finally(function () { coverFile.value = ''; });
  });
  document.getElementById('coverRemove').addEventListener('click', function () {
    document.getElementById('coverPath').value = '';
    document.getElementById('coverPreviewWrap').style.display = 'none';
    document.getElementById('coverUpload').style.display = '';
    markDirty();
  });

  /* SEO char counters */
  function faNum(n) { return String(n).replace(/\d/g, function (d) { return '۰۱۲۳۴۵۶۷۸۹'[d]; }); }
  function counter(inputId, countId, limit) {
    var el = document.getElementById(inputId), out = document.getElementById(countId);
    function update() {
      var n = el.value.length;
      out.textContent = faNum(n);
      out.parentElement.style.color = n > limit ? '#BB2D3B' : '#9a9587';
    }
    el.addEventListener('input', update);
    update();
  }
  counter('seoTitle', 'seoTitleCount', 60);
  counter('seoDesc', 'seoDescCount', 160);

  /* Submit: serialize Quill body, clear dirty guard */
  document.getElementById('postForm').addEventListener('submit', function () {
    document.getElementById('bodyHtml').value = quill.getSemanticHTML();
    dirty = false;
  });
  window.addEventListener('beforeunload', function (e) {
    if (dirty) { e.preventDefault(); e.returnValue = ''; }
  });
});
</script>
<?php admin_page_end('blog');
