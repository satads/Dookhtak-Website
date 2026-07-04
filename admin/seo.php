<?php
/**
 * SEO admin — four server-side tabs:
 *   pages     — per-page meta over seo_pages (title / description / og image)
 *   general   — site_title_suffix, default_meta_description, default_og_image, robots.txt
 *   scripts   — head_scripts / body_scripts injected on every public page
 *   redirects — 301/302 redirect CRUD over the redirects table
 * CSRF on every POST; cache_flush() after each save (SEO touches all pages).
 * head_scripts / body_scripts are trusted admin HTML (analytics, verification
 * tags) and are stored/output verbatim.
 */
require_once __DIR__ . '/../includes/admin_layout.php';
require_once __DIR__ . '/../includes/cache.php';

require_admin();

/** Whitelist the active tab. */
function seo_tab(?string $v): string
{
    return in_array($v, ['pages', 'general', 'scripts', 'redirects'], true) ? $v : 'pages';
}

$PAGE_KEYS = ['home', 'features', 'pricing', 'tutorials', 'blog', 'about', 'contact'];
$PAGE_LABELS = [
    'home' => 'صفحه اصلی', 'features' => 'امکانات', 'pricing' => 'تعرفه‌ها',
    'tutorials' => 'آموزش‌ها', 'blog' => 'بلاگ', 'about' => 'درباره ما', 'contact' => 'تماس',
];
$GENERAL_KEYS = ['site_title_suffix', 'default_meta_description', 'default_og_image', 'robots_txt'];

$tab = seo_tab($_GET['tab'] ?? null);
$error = '';

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    $tab = seo_tab($_POST['tab'] ?? null);
    if (!csrf_verify($_POST['csrf_token'] ?? null)) {
        http_response_code(403);
        $error = 'درخواست نامعتبر (CSRF). صفحه را از نو باز کن.';
    } else {
        $action = $_POST['action'] ?? '';

        if ($action === 'save_pages') {
            $stmt = db()->prepare(
                'INSERT INTO seo_pages (page_key, meta_title, meta_description, og_image) VALUES (?,?,?,?)
                 ON DUPLICATE KEY UPDATE meta_title = VALUES(meta_title), meta_description = VALUES(meta_description), og_image = VALUES(og_image)'
            );
            foreach ($PAGE_KEYS as $k) {
                $stmt->execute([
                    $k,
                    mb_substr(trim((string) ($_POST["title_$k"] ?? '')), 0, 220),
                    mb_substr(trim((string) ($_POST["desc_$k"] ?? '')), 0, 320),
                    mb_substr(trim((string) ($_POST["og_$k"] ?? '')), 0, 255),
                ]);
            }
            cache_flush();
            header('Location: /admin/seo.php?tab=pages&toast=' . rawurlencode('متای صفحات ذخیره شد'));
            exit;
        }

        if ($action === 'save_general') {
            $stmt = db()->prepare('UPDATE seo_settings SET `value` = ? WHERE `key` = ?');
            foreach ($GENERAL_KEYS as $k) {
                if (!array_key_exists($k, $_POST)) {
                    continue;
                }
                $stmt->execute([trim((string) $_POST[$k]), $k]);
            }
            cache_flush();
            header('Location: /admin/seo.php?tab=general&toast=' . rawurlencode('تنظیمات عمومی ذخیره شد'));
            exit;
        }

        if ($action === 'save_scripts') {
            $stmt = db()->prepare('UPDATE seo_settings SET `value` = ? WHERE `key` = ?');
            $stmt->execute([(string) ($_POST['head_scripts'] ?? ''), 'head_scripts']);
            $stmt->execute([(string) ($_POST['body_scripts'] ?? ''), 'body_scripts']);
            cache_flush();
            header('Location: /admin/seo.php?tab=scripts&toast=' . rawurlencode('اسکریپت‌ها ذخیره شد'));
            exit;
        }

        if ($action === 'redirect_add') {
            $from = trim((string) ($_POST['from_path'] ?? ''));
            $to = trim((string) ($_POST['to_url'] ?? ''));
            $code = (int) ($_POST['status_code'] ?? 301);
            $code = in_array($code, [301, 302], true) ? $code : 301;
            if ($from === '' || $from[0] !== '/') {
                $error = 'مسیر مبدأ باید با / شروع شود (مثل /old-page).';
            } elseif ($to === '') {
                $error = 'مقصد را وارد کن.';
            } elseif ($from === $to) {
                $error = 'مبدأ و مقصد نمی‌توانند یکی باشند.';
            } else {
                try {
                    db()->prepare('INSERT INTO redirects (from_path, to_url, status_code) VALUES (?,?,?)')
                        ->execute([mb_substr($from, 0, 255), mb_substr($to, 0, 500), $code]);
                    cache_flush();
                    header('Location: /admin/seo.php?tab=redirects&toast=' . rawurlencode('ریدایرکت افزوده شد'));
                    exit;
                } catch (PDOException $e) {
                    $error = 'این مسیر مبدأ قبلاً ثبت شده است.';
                }
            }
        }

        if ($action === 'redirect_delete') {
            db()->prepare('DELETE FROM redirects WHERE id = ?')->execute([(int) ($_POST['id'] ?? 0)]);
            cache_flush();
            header('Location: /admin/seo.php?tab=redirects&toast=' . rawurlencode('حذف شد'));
            exit;
        }
    }
}

// Load current data for the active tab.
$pages = [];
foreach (db()->query('SELECT page_key, meta_title, meta_description, og_image FROM seo_pages') as $r) {
    $pages[$r['page_key']] = $r;
}
$settings = [];
foreach (db()->query('SELECT `key`, `value` FROM seo_settings') as $r) {
    $settings[$r['key']] = $r['value'];
}
$redirects = db()->query('SELECT * FROM redirects ORDER BY id DESC')->fetchAll();

$tabs = [
    'pages'     => 'متای صفحات',
    'general'   => 'عمومی',
    'scripts'   => 'اسکریپت‌ها',
    'redirects' => 'ریدایرکت‌ها',
];

admin_page_start('سئو', 'seo', $error !== '' ? ['toast' => $error, 'toast_kind' => 'err'] : []);
?>
<div style="max-width:760px;">
  <!-- Tabs (server-side) -->
  <div style="display:flex;flex-wrap:wrap;gap:8px;margin-bottom:16px;">
    <?php foreach ($tabs as $key => $label): ?>
    <a href="/admin/seo.php?tab=<?= e($key) ?>" style="display:inline-flex;align-items:center;justify-content:center;min-height:42px;padding:9px 18px;border-radius:10px;font-weight:700;font-size:13.5px;text-decoration:none;<?= $tab === $key ? 'background:#E76F51;color:#fff;' : 'background:#fff;color:#3D4A6B;' ?>"><?= e($label) ?></a>
    <?php endforeach; ?>
  </div>

  <?php if ($tab === 'pages'): ?>
  <form method="post" action="/admin/seo.php">
    <?= csrf_field() ?>
    <input type="hidden" name="tab" value="pages">
    <input type="hidden" name="action" value="save_pages">
    <?php foreach ($PAGE_KEYS as $k): $row = $pages[$k] ?? ['meta_title' => '', 'meta_description' => '', 'og_image' => '']; ?>
    <div class="a-card a-card-pad" style="margin-bottom:12px;">
      <b class="a-card-title"><?= e($PAGE_LABELS[$k]) ?></b>
      <div style="margin-bottom:10px;">
        <label class="a-label" for="t-<?= e($k) ?>">عنوان صفحه (title)</label>
        <input id="t-<?= e($k) ?>" class="a-input" type="text" name="title_<?= e($k) ?>" value="<?= e($row['meta_title'] ?? '') ?>" maxlength="220">
      </div>
      <div style="margin-bottom:10px;">
        <label class="a-label" for="d-<?= e($k) ?>">توضیح متا (description)</label>
        <textarea id="d-<?= e($k) ?>" class="a-input" name="desc_<?= e($k) ?>" rows="2" maxlength="320" style="resize:vertical;"><?= e($row['meta_description'] ?? '') ?></textarea>
      </div>
      <div>
        <label class="a-label" for="o-<?= e($k) ?>">تصویر اشتراک‌گذاری (og:image) — اختیاری</label>
        <input id="o-<?= e($k) ?>" class="a-input" type="text" dir="ltr" name="og_<?= e($k) ?>" value="<?= e($row['og_image'] ?? '') ?>" placeholder="/uploads/og-home.jpg" maxlength="255">
      </div>
    </div>
    <?php endforeach; ?>
    <div style="position:sticky;bottom:0;background:rgba(247,246,243,.94);backdrop-filter:blur(6px);padding:12px 0;">
      <button type="submit" class="a-btn a-btn-primary" style="min-height:46px;padding:11px 30px;">ذخیره متای صفحات</button>
    </div>
  </form>

  <?php elseif ($tab === 'general'): ?>
  <form method="post" action="/admin/seo.php">
    <?= csrf_field() ?>
    <input type="hidden" name="tab" value="general">
    <input type="hidden" name="action" value="save_general">
    <div class="a-card a-card-pad" style="margin-bottom:12px;">
      <b class="a-card-title">پیش‌فرض‌ها</b>
      <div style="margin-bottom:10px;">
        <label class="a-label" for="g-suffix">پسوند عنوان صفحه‌ها</label>
        <input id="g-suffix" class="a-input" type="text" name="site_title_suffix" value="<?= e($settings['site_title_suffix'] ?? '') ?>" placeholder=" | دوختک">
      </div>
      <div>
        <label class="a-label" for="g-desc">توضیح متای پیش‌فرض</label>
        <textarea id="g-desc" class="a-input" name="default_meta_description" rows="2" style="resize:vertical;"><?= e($settings['default_meta_description'] ?? '') ?></textarea>
      </div>
    </div>
    <div class="a-card a-card-pad" style="margin-bottom:12px;">
      <b class="a-card-title">تصویر اشتراک‌گذاری پیش‌فرض</b>
      <label class="a-label" for="g-og">og:image پیش‌فرض (وقتی صفحه‌ای تصویر اختصاصی ندارد)</label>
      <input id="g-og" class="a-input" type="text" dir="ltr" name="default_og_image" value="<?= e($settings['default_og_image'] ?? '') ?>" placeholder="/uploads/og-default.jpg">
    </div>
    <div class="a-card a-card-pad" style="margin-bottom:12px;">
      <b class="a-card-title">robots.txt</b>
      <textarea class="a-input" name="robots_txt" rows="7" dir="ltr" style="resize:vertical;font-family:monospace;font-size:12.5px;"><?= e($settings['robots_txt'] ?? '') ?></textarea>
      <div style="font-size:11.5px;color:#9a9587;margin-top:6px;">در آدرس <span dir="ltr">/robots.txt</span> نمایش داده می‌شود.</div>
    </div>
    <div style="position:sticky;bottom:0;background:rgba(247,246,243,.94);backdrop-filter:blur(6px);padding:12px 0;">
      <button type="submit" class="a-btn a-btn-primary" style="min-height:46px;padding:11px 30px;">ذخیره</button>
    </div>
  </form>

  <?php elseif ($tab === 'scripts'): ?>
  <form method="post" action="/admin/seo.php">
    <?= csrf_field() ?>
    <input type="hidden" name="tab" value="scripts">
    <input type="hidden" name="action" value="save_scripts">
    <div style="display:flex;align-items:center;gap:9px;background:#FDF6E7;border:1px solid #F3E2B8;border-radius:10px;padding:11px 14px;font-size:12.5px;color:#8a6d1f;margin-bottom:14px;">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#EEA62B" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex:none;"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3"/><path d="M12 9v4"/><path d="M12 17h.01"/></svg>
      این کدها بدون تغییر روی همه صفحات عمومی قرار می‌گیرند؛ فقط کد معتبر و مطمئن (مثل آنالیتیکس یا تگ تأیید) وارد کن.
    </div>
    <div class="a-card a-card-pad" style="margin-bottom:12px;">
      <b class="a-card-title">کد داخل &lt;head&gt;</b>
      <textarea class="a-input" name="head_scripts" rows="6" dir="ltr" style="resize:vertical;font-family:monospace;font-size:12.5px;"><?= e($settings['head_scripts'] ?? '') ?></textarea>
    </div>
    <div class="a-card a-card-pad" style="margin-bottom:12px;">
      <b class="a-card-title">کد پیش از &lt;/body&gt;</b>
      <textarea class="a-input" name="body_scripts" rows="6" dir="ltr" style="resize:vertical;font-family:monospace;font-size:12.5px;"><?= e($settings['body_scripts'] ?? '') ?></textarea>
    </div>
    <div style="position:sticky;bottom:0;background:rgba(247,246,243,.94);backdrop-filter:blur(6px);padding:12px 0;">
      <button type="submit" class="a-btn a-btn-primary" style="min-height:46px;padding:11px 30px;">ذخیره اسکریپت‌ها</button>
    </div>
  </form>

  <?php else: /* redirects */ ?>
  <div class="a-card a-card-pad" style="margin-bottom:16px;">
    <b class="a-card-title">ریدایرکت جدید</b>
    <form method="post" action="/admin/seo.php" style="display:flex;flex-wrap:wrap;gap:10px;align-items:flex-end;">
      <?= csrf_field() ?>
      <input type="hidden" name="tab" value="redirects">
      <input type="hidden" name="action" value="redirect_add">
      <div style="flex:1;min-width:180px;">
        <label class="a-label" for="r-from">از مسیر</label>
        <input id="r-from" class="a-input" type="text" dir="ltr" name="from_path" placeholder="/old-page" required>
      </div>
      <div style="flex:1;min-width:180px;">
        <label class="a-label" for="r-to">به مقصد</label>
        <input id="r-to" class="a-input" type="text" dir="ltr" name="to_url" placeholder="/new-page یا https://…" required>
      </div>
      <div style="flex:0 0 96px;">
        <label class="a-label" for="r-code">نوع</label>
        <select id="r-code" class="a-input" name="status_code">
          <option value="301">۳۰۱ دائمی</option>
          <option value="302">۳۰۲ موقت</option>
        </select>
      </div>
      <button type="submit" class="a-btn a-btn-primary" style="min-height:42px;">افزودن</button>
    </form>
  </div>

  <?php if (!$redirects): ?>
  <div class="a-empty">
    <svg width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="#c9c3b6" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M9 17H7A5 5 0 0 1 7 7h2"/><path d="M15 7h2a5 5 0 0 1 0 10h-2"/><path d="M8 12h8"/></svg>
    <div class="a-empty-title">هنوز ریدایرکتی ثبت نشده است.</div>
  </div>
  <?php else: ?>
  <div class="a-card" style="overflow:hidden;">
    <?php foreach ($redirects as $r): ?>
    <div class="a-row" style="display:flex;align-items:center;gap:12px;padding:12px 18px;border-bottom:1px solid #F6F4EF;font-size:13px;">
      <span dir="ltr" style="flex:1;color:#1F2A44;font-weight:700;"><?= e($r['from_path']) ?></span>
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#9a9587" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex:none;transform:scaleX(-1);"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
      <span dir="ltr" style="flex:1;color:#6B7280;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;"><?= e($r['to_url']) ?></span>
      <span class="a-badge" style="flex:none;background:#EEF1F7;color:#3D4A6B;"><?= fa_digits((int) $r['status_code']) ?></span>
      <form method="post" action="/admin/seo.php" data-confirm="<?= e($r['from_path']) ?>" style="margin:0;flex:none;display:inline-flex;">
        <?= csrf_field() ?>
        <input type="hidden" name="tab" value="redirects">
        <input type="hidden" name="action" value="redirect_delete">
        <input type="hidden" name="id" value="<?= (int) $r['id'] ?>">
        <button type="submit" class="a-iconbtn del" aria-label="حذف">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6"/><path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
        </button>
      </form>
    </div>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>
  <?php endif; ?>
</div>
<?php admin_page_end('seo');
