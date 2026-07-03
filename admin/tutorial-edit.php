<?php
/**
 * Tutorial STRUCTURED builder — no freeform body editor, per the plan.
 * Meta + video (Aparat only, sanitized) + intro + prerequisites +
 * steps repeater (add/remove/move; title, text, image via media
 * endpoint, annotation, tip, warning) + troubleshooting repeater.
 * Persists to content_json / troubleshooting_json. «پیش‌نمایش» posts
 * the current form to tutorial-preview.php (server-rendered fragment
 * with the real central site CSS).
 */
require_once __DIR__ . '/../includes/admin_layout.php';
require_once __DIR__ . '/../includes/cache.php';
require_once __DIR__ . '/../includes/aparat.php';

require_admin();

$id = (int) ($_GET['id'] ?? $_POST['id'] ?? 0);
$error = '';

$tut = [
    'id' => 0, 'title' => '', 'slug' => '', 'category_id' => null,
    'duration_minutes' => 3, 'status' => 'draft', 'sort_order' => 0,
    'video_type' => 'none', 'video_embed' => '', 'intro_text' => '',
    'prerequisites_html' => '', 'content_json' => '[]', 'troubleshooting_json' => '[]',
    'seo_title' => null, 'seo_description' => null,
];

if ($id > 0) {
    $stmt = db()->prepare('SELECT * FROM tutorials WHERE id = ?');
    $stmt->execute([$id]);
    $found = $stmt->fetch();
    if (!$found) {
        header('Location: /admin/tutorials.php?toast=' . rawurlencode('آموزش پیدا نشد') . '&toast_kind=err');
        exit;
    }
    $tut = $found;
}

require_once __DIR__ . '/../includes/tutorial_form.php';

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    if (!csrf_verify($_POST['csrf_token'] ?? null)) {
        http_response_code(403);
        $error = 'درخواست نامعتبر (CSRF). صفحه را از نو باز کن.';
    } else {
        $tut = tutorial_collect_post($_POST, $tut);

        if ($tut['title'] === '') {
            $error = 'عنوان آموزش را بنویس.';
        } elseif ($tut['slug'] === '') {
            $error = 'نامک لاتین را وارد کن.';
        } else {
            $stmt = db()->prepare('SELECT id FROM tutorials WHERE slug = ? AND id != ?');
            $stmt->execute([$tut['slug'], $id]);
            if ($stmt->fetch()) {
                $error = 'این نامک قبلاً استفاده شده است.';
            }
        }

        // Aparat sanitizer: with video enabled and embed pasted, only an
        // aparat.com iframe is accepted (stored sanitized).
        if ($error === '' && $tut['video_type'] === 'aparat' && $tut['video_embed'] !== '') {
            $clean = aparat_sanitize($tut['video_embed']);
            if ($clean === null) {
                $error = 'فقط کد iframe از aparat.com مجاز است.';
            } else {
                $tut['video_embed'] = $clean;
            }
        }
        if ($tut['video_type'] === 'none') {
            $tut['video_embed'] = '';
        }

        if ($error === '') {
            if ($id > 0) {
                db()->prepare(
                    'UPDATE tutorials SET title=?, slug=?, category_id=?, duration_minutes=?, status=?, sort_order=?,
                     video_type=?, video_embed=?, intro_text=?, prerequisites_html=?, content_json=?,
                     troubleshooting_json=?, seo_title=?, seo_description=? WHERE id=?'
                )->execute([
                    $tut['title'], $tut['slug'], $tut['category_id'], $tut['duration_minutes'], $tut['status'],
                    $tut['sort_order'], $tut['video_type'], $tut['video_embed'], $tut['intro_text'],
                    $tut['prerequisites_html'], $tut['content_json'], $tut['troubleshooting_json'],
                    $tut['seo_title'], $tut['seo_description'], $id,
                ]);
            } else {
                db()->prepare(
                    'INSERT INTO tutorials (title, slug, category_id, duration_minutes, status, sort_order,
                     video_type, video_embed, intro_text, prerequisites_html, content_json, troubleshooting_json,
                     seo_title, seo_description) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)'
                )->execute([
                    $tut['title'], $tut['slug'], $tut['category_id'], $tut['duration_minutes'], $tut['status'],
                    $tut['sort_order'], $tut['video_type'], $tut['video_embed'], $tut['intro_text'],
                    $tut['prerequisites_html'], $tut['content_json'], $tut['troubleshooting_json'],
                    $tut['seo_title'], $tut['seo_description'],
                ]);
                $id = (int) db()->lastInsertId();
            }
            cache_flush();
            header('Location: /admin/tutorial-edit.php?id=' . $id . '&toast=' . rawurlencode('ذخیره شد'));
            exit;
        }
    }
}

$cats = db()->query('SELECT id, title FROM tutorial_categories ORDER BY sort_order, id')->fetchAll();
$steps_data = json_decode($tut['content_json'] ?? '[]', true) ?: [];
$trouble_data = json_decode($tut['troubleshooting_json'] ?? '[]', true) ?: [];

admin_page_start($id > 0 ? 'ویرایش آموزش' : 'آموزش جدید', 'tuts', $error !== '' ? ['toast' => $error, 'toast_kind' => 'err'] : []);
?>
<div>
  <div id="dirtyBar" style="display:none;align-items:center;gap:9px;background:#FDF6E7;border:1px solid #F3E2B8;border-radius:10px;padding:10px 14px;font-size:12.5px;color:#8a6d1f;margin-bottom:14px;">
    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#EEA62B" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/><path d="M12 9v4"/><path d="M12 17h.01"/></svg>
    تغییرات ذخیره‌نشده داری — قبل از خروج ذخیره کن.
  </div>

  <form id="tutForm" method="post" action="/admin/tutorial-edit.php<?= $id > 0 ? '?id=' . $id : '' ?>">
    <?= csrf_field() ?>
    <input type="hidden" name="id" value="<?= $id ?>">
    <input type="hidden" name="content_json" id="contentJson">
    <input type="hidden" name="troubleshooting_json" id="troubleJson">

    <div id="editorRow" style="display:flex;gap:16px;align-items:flex-start;">
      <!-- Main column -->
      <div style="flex:1 1 0;min-width:0;">
        <div class="a-card" style="padding:18px;">
          <input type="text" name="title" id="edTitle" placeholder="عنوان آموزش…" value="<?= e($tut['title']) ?>" style="width:100%;min-height:48px;padding:10px 2px;font-size:20px;font-weight:700;color:#1F2A44;background:none;border:none;border-bottom:2px dashed #F0EBE1;outline:none;margin-bottom:12px;">
          <div style="display:flex;align-items:center;gap:8px;font-size:12.5px;color:#6B7280;">
            <span>نامک:</span>
            <input class="a-input" type="text" dir="ltr" name="slug" id="edSlug" value="<?= e($tut['slug']) ?>" style="flex:1;max-width:280px;min-height:36px;padding:6px 10px;font-size:12.5px;border-radius:8px;">
          </div>
        </div>

        <div class="a-card a-card-pad" style="margin-top:12px;">
          <b class="a-card-title">مقدمه</b>
          <textarea class="a-input" name="intro_text" rows="2" placeholder="مقدمه کوتاه (اختیاری)…" style="min-height:0;resize:vertical;font-size:13.5px;line-height:1.9;"><?= e($tut['intro_text'] ?? '') ?></textarea>
        </div>

        <div class="a-card a-card-pad" style="margin-top:12px;">
          <b class="a-card-title">قبل از شروع (پیش‌نیازها — اختیاری)</b>
          <textarea class="a-input" name="prerequisites_html" rows="2" placeholder="متن پیش‌نیاز…" style="min-height:0;resize:vertical;font-size:13.5px;line-height:1.9;"><?= e($tut['prerequisites_html'] ?? '') ?></textarea>
          <div class="a-hint">می‌توانی لینک بگذاری؛ مثال: <code dir="ltr" style="font-size:11px;">&lt;a href="#add-customer"&gt;آموزش ثبت مشتری&lt;/a&gt;</code> — فقط تگ لینک مجاز است.</div>
        </div>

        <!-- Steps repeater -->
        <div class="a-card a-card-pad" style="margin-top:12px;">
          <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px;">
            <b style="font-size:13.5px;color:#1F2A44;font-weight:700;">قدم‌های آموزش</b>
            <button type="button" class="a-btn a-btn-primary" data-step-add style="min-height:38px;padding:7px 14px;font-size:12.5px;">+ قدم جدید</button>
          </div>
          <div id="stepList"></div>
          <div id="stepEmpty" class="a-hint" style="display:none;margin:0;">هنوز قدمی اضافه نشده — با «+ قدم جدید» شروع کن.</div>
        </div>

        <!-- Troubleshooting repeater -->
        <div class="a-card a-card-pad" style="margin-top:12px;">
          <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px;">
            <b style="font-size:13.5px;color:#1F2A44;font-weight:700;">اگر مشکلی پیش آمد (اختیاری)</b>
            <button type="button" class="a-btn a-btn-outline" data-tr-add style="min-height:38px;padding:7px 14px;font-size:12.5px;">+ پرسش جدید</button>
          </div>
          <div id="trList"></div>
        </div>
      </div>

      <!-- Side column -->
      <div id="editorSide" style="flex:0 0 280px;display:flex;flex-direction:column;gap:12px;">
        <div class="a-card" style="padding:15px;">
          <b style="display:block;font-size:13px;color:#1F2A44;margin-bottom:10px;">انتشار</b>
          <select class="a-input" name="status" style="margin-bottom:10px;">
            <option value="draft" <?= $tut['status'] === 'draft' ? 'selected' : '' ?>>پیش‌نویس</option>
            <option value="published" <?= $tut['status'] === 'published' ? 'selected' : '' ?>>انتشار</option>
          </select>
          <button type="submit" class="a-btn a-btn-primary" style="width:100%;min-height:44px;font-size:14px;margin-bottom:8px;">ذخیره</button>
          <button type="submit" class="a-btn a-btn-outline" formaction="/admin/tutorial-preview.php" formtarget="_blank" style="width:100%;min-height:42px;font-size:13.5px;">پیش‌نمایش</button>
        </div>

        <div class="a-card" style="padding:15px;">
          <b style="display:block;font-size:13px;color:#1F2A44;margin-bottom:10px;">دسته‌بندی و ترتیب</b>
          <select class="a-input" name="category_id" style="margin-bottom:10px;">
            <option value="">بدون دسته</option>
            <?php foreach ($cats as $c): ?>
            <option value="<?= (int) $c['id'] ?>" <?= (int) ($tut['category_id'] ?? 0) === (int) $c['id'] ? 'selected' : '' ?>><?= e($c['title']) ?></option>
            <?php endforeach; ?>
          </select>
          <div style="display:flex;align-items:center;gap:8px;font-size:12.5px;color:#6B7280;">
            ترتیب:
            <input class="a-input" type="text" name="sort_order" value="<?= e(fa_digits($tut['sort_order'])) ?>" style="width:60px;min-height:36px;padding:6px 9px;font-size:13px;border-radius:8px;text-align:center;">
          </div>
        </div>

        <div class="a-card" style="padding:15px;">
          <label style="display:flex;align-items:center;justify-content:space-between;gap:10px;font-size:13px;font-weight:700;color:#1F2A44;cursor:pointer;min-height:40px;">
            ویدئو دارد (آپارات)
            <input type="hidden" name="video_type" id="videoType" value="<?= e($tut['video_type']) ?>">
            <button type="button" class="a-switch <?= $tut['video_type'] === 'aparat' ? 'on' : '' ?>" role="switch" aria-checked="<?= $tut['video_type'] === 'aparat' ? 'true' : 'false' ?>" id="videoSwitch">
              <span class="knob"></span>
            </button>
          </label>
          <textarea class="a-input" name="video_embed" id="videoEmbed" rows="3" placeholder="کد embed آپارات…" dir="ltr" style="<?= $tut['video_type'] === 'aparat' ? '' : 'display:none;' ?>min-height:0;margin-top:10px;resize:vertical;font-size:12px;"><?= e($tut['video_embed'] ?? '') ?></textarea>
          <div style="display:flex;align-items:center;gap:8px;font-size:12.5px;color:#6B7280;margin-top:12px;">
            مدت‌زمان:
            <input class="a-input" type="text" name="duration_minutes" value="<?= e(fa_digits($tut['duration_minutes'])) ?>" style="width:60px;min-height:36px;padding:6px 9px;font-size:13px;border-radius:8px;text-align:center;">
            دقیقه
          </div>
        </div>

        <div class="a-card" style="padding:15px;">
          <b style="display:block;font-size:13px;color:#1F2A44;margin-bottom:10px;">سئو</b>
          <input class="a-input" type="text" name="seo_title" id="seoTitle" placeholder="عنوان سئو" value="<?= e($tut['seo_title'] ?? '') ?>" style="min-height:38px;font-size:12.5px;border-radius:8px;margin-bottom:2px;">
          <div style="font-size:11px;color:#9a9587;text-align:end;margin-bottom:8px;"><span id="seoTitleCount">۰</span> / ۶۰</div>
          <textarea class="a-input" name="seo_description" id="seoDesc" rows="3" placeholder="توضیحات متا…" style="min-height:0;resize:vertical;font-size:12.5px;border-radius:8px;"><?= e($tut['seo_description'] ?? '') ?></textarea>
          <div style="font-size:11px;text-align:end;margin-top:4px;color:#9a9587;"><span id="seoDescCount">۰</span> / ۱۶۰</div>
        </div>
      </div>
    </div>
  </form>
</div>

<script>
window.TUT_STEPS = <?= json_encode($steps_data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG) ?>;
window.TUT_TROUBLE = <?= json_encode($trouble_data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG) ?>;
</script>
<script src="/assets/js/admin-tutorial-builder.js"></script>
<?php admin_page_end('tuts');
