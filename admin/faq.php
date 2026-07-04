<?php
/**
 * FAQ admin — two server-side tabs (home / pricing), inline sort-order
 * saves, and modal CRUD per the approved admin design. Answers are
 * stored as plain text (public pages escape them with e()).
 */
require_once __DIR__ . '/../includes/admin_layout.php';
require_once __DIR__ . '/../includes/cache.php';

require_admin();

/** Whitelist the tab/page value. */
function faq_page_value(?string $v): string
{
    return $v === 'pricing' ? 'pricing' : 'home';
}

$page = faq_page_value($_GET['page'] ?? null);
$error = '';

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    $page = faq_page_value($_POST['page'] ?? null);
    if (!csrf_verify($_POST['csrf_token'] ?? null)) {
        http_response_code(403);
        $error = 'درخواست نامعتبر (CSRF). صفحه را از نو باز کن.';
    } else {
        $action = $_POST['action'] ?? '';
        if ($action === 'save') {
            $id = (int) ($_POST['id'] ?? 0);
            $question = trim((string) ($_POST['question'] ?? ''));
            $answer = trim((string) ($_POST['answer_html'] ?? ''));
            $order = (int) en_digits((string) ($_POST['sort_order'] ?? '0'));
            if ($question === '') {
                $error = 'سؤال را بنویس.';
            } else {
                if ($id > 0) {
                    db()->prepare('UPDATE faqs SET page = ?, question = ?, answer_html = ?, sort_order = ? WHERE id = ?')
                        ->execute([$page, $question, $answer, $order, $id]);
                } else {
                    db()->prepare('INSERT INTO faqs (page, question, answer_html, sort_order) VALUES (?, ?, ?, ?)')
                        ->execute([$page, $question, $answer, $order]);
                }
                cache_flush();
                header('Location: /admin/faq.php?page=' . $page . '&toast=' . rawurlencode('ذخیره شد'));
                exit;
            }
        } elseif ($action === 'order') {
            db()->prepare('UPDATE faqs SET sort_order = ? WHERE id = ?')
                ->execute([(int) en_digits((string) ($_POST['sort_order'] ?? '0')), (int) ($_POST['id'] ?? 0)]);
            cache_flush();
            header('Location: /admin/faq.php?page=' . $page . '&toast=' . rawurlencode('ترتیب ذخیره شد'));
            exit;
        } elseif ($action === 'delete') {
            db()->prepare('DELETE FROM faqs WHERE id = ?')->execute([(int) ($_POST['id'] ?? 0)]);
            cache_flush();
            header('Location: /admin/faq.php?page=' . $page . '&toast=' . rawurlencode('حذف شد'));
            exit;
        }
    }
}

$stmt = db()->prepare('SELECT * FROM faqs WHERE page = ? ORDER BY sort_order, id');
$stmt->execute([$page]);
$faqs = $stmt->fetchAll();

admin_page_start('سؤالات متداول', 'faq', $error !== '' ? ['toast' => $error, 'toast_kind' => 'err'] : []);
?>
<div>
  <!-- Tabs (server-side) + new-question button -->
  <div style="display:flex;flex-wrap:wrap;align-items:center;gap:10px;margin-bottom:14px;">
    <a href="/admin/faq.php?page=home" style="display:inline-flex;align-items:center;justify-content:center;min-height:42px;padding:9px 18px;border-radius:10px;font-weight:700;font-size:13.5px;text-decoration:none;<?= $page === 'home' ? 'background:#E76F51;color:#fff;' : 'background:#fff;color:#3D4A6B;' ?>">صفحه اصلی</a>
    <a href="/admin/faq.php?page=pricing" style="display:inline-flex;align-items:center;justify-content:center;min-height:42px;padding:9px 18px;border-radius:10px;font-weight:700;font-size:13.5px;text-decoration:none;<?= $page === 'pricing' ? 'background:#E76F51;color:#fff;' : 'background:#fff;color:#3D4A6B;' ?>">صفحه تعرفه‌ها</a>
    <button type="button" class="a-btn a-btn-primary" data-faq-new style="margin-inline-start:auto;">+ سؤال جدید</button>
  </div>

  <?php if (!$faqs): ?>
  <div class="a-empty">
    <svg width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="#c9c3b6" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22a10 10 0 1 0 0-20 10 10 0 0 0 0 20z"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/><path d="M12 17h.01"/></svg>
    <div class="a-empty-title">هنوز سؤالی برای این صفحه ثبت نشده است.</div>
    <button type="button" class="a-btn a-btn-primary" data-faq-new>+ سؤال جدید</button>
  </div>
  <?php else: ?>
  <div class="a-card" style="overflow:hidden;">
    <?php foreach ($faqs as $f): ?>
    <div class="a-row" style="display:flex;align-items:center;gap:12px;padding:12px 18px;border-bottom:1px solid #F6F4EF;font-size:13px;transition:background .15s ease;">
      <form method="post" action="/admin/faq.php" data-order-form style="margin:0;flex:none;">
        <?= csrf_field() ?>
        <input type="hidden" name="action" value="order">
        <input type="hidden" name="id" value="<?= (int) $f['id'] ?>">
        <input type="hidden" name="page" value="<?= e($page) ?>">
        <input class="a-input" type="text" name="sort_order" value="<?= fa_digits($f['sort_order']) ?>" aria-label="ترتیب" style="width:48px;text-align:center;">
      </form>
      <b style="flex:1;color:#1F2A44;"><?= e($f['question']) ?></b>
      <span style="display:flex;justify-content:flex-end;gap:4px;flex:none;">
        <button type="button" class="a-iconbtn edit" aria-label="ویرایش" data-faq-edit data-faq='<?= e(json_encode(['id' => (int) $f['id'], 'question' => $f['question'], 'answer' => $f['answer_html'], 'sort_order' => (int) $f['sort_order']], JSON_UNESCAPED_UNICODE)) ?>'>
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/></svg>
        </button>
        <form method="post" action="/admin/faq.php" data-confirm="<?= e($f['question']) ?>" style="margin:0;display:inline-flex;">
          <?= csrf_field() ?>
          <input type="hidden" name="action" value="delete">
          <input type="hidden" name="id" value="<?= (int) $f['id'] ?>">
          <input type="hidden" name="page" value="<?= e($page) ?>">
          <button type="submit" class="a-iconbtn del" aria-label="حذف">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6"/><path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
          </button>
        </form>
      </span>
    </div>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>
</div>

<!-- FAQ form modal (per approved design) -->
<div id="faqModal" class="a-modal-overlay" style="display:none;">
  <div role="dialog" aria-modal="true" style="width:100%;max-width:460px;background:#fff;border-radius:14px;padding:24px;box-shadow:0 24px 60px -20px rgba(31,42,68,.5);max-height:86vh;overflow-y:auto;text-align:start;">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;">
      <b id="faqModalTitle" style="font-size:15px;color:#1F2A44;">سؤال جدید</b>
      <button type="button" data-faq-close aria-label="بستن" style="display:inline-flex;align-items:center;justify-content:center;width:38px;height:38px;background:none;border:none;color:#1F2A44;border-radius:8px;cursor:pointer;">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
      </button>
    </div>
    <form method="post" action="/admin/faq.php">
      <?= csrf_field() ?>
      <input type="hidden" name="action" value="save">
      <input type="hidden" name="page" value="<?= e($page) ?>">
      <input type="hidden" name="id" id="faq-id" value="0">
      <div style="margin-bottom:13px;">
        <label class="a-label-strong" for="faq-question">سؤال</label>
        <input class="a-input" type="text" id="faq-question" name="question" style="min-height:42px;font-size:13.5px;">
      </div>
      <div style="margin-bottom:13px;">
        <label class="a-label-strong" for="faq-answer">پاسخ</label>
        <textarea class="a-input" id="faq-answer" name="answer_html" rows="4" style="font-size:13.5px;resize:vertical;"></textarea>
      </div>
      <div style="margin-bottom:13px;">
        <label class="a-label-strong" for="faq-order">ترتیب</label>
        <input class="a-input" type="text" id="faq-order" name="sort_order" style="min-height:42px;font-size:13.5px;">
      </div>
      <div style="display:flex;gap:10px;justify-content:flex-end;margin-top:6px;">
        <button type="button" data-faq-close class="a-btn a-btn-cancel" style="padding:9px 20px;font-size:13px;">انصراف</button>
        <button type="submit" class="a-btn a-btn-primary" style="padding:9px 24px;font-size:13px;">ذخیره</button>
      </div>
    </form>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
  'use strict';
  var modal = document.getElementById('faqModal');
  function open(data) {
    document.getElementById('faqModalTitle').textContent = data ? 'ویرایش سؤال' : 'سؤال جدید';
    document.getElementById('faq-id').value = data ? data.id : 0;
    document.getElementById('faq-question').value = data ? data.question : '';
    document.getElementById('faq-answer').value = data ? data.answer : '';
    document.getElementById('faq-order').value = data ? data.sort_order : '';
    modal.style.display = 'flex';
    document.getElementById('faq-question').focus();
  }
  document.querySelectorAll('[data-faq-new]').forEach(function (b) {
    b.addEventListener('click', function () { open(null); });
  });
  document.querySelectorAll('[data-faq-edit]').forEach(function (b) {
    b.addEventListener('click', function () { open(JSON.parse(b.getAttribute('data-faq'))); });
  });
  document.querySelectorAll('[data-faq-close]').forEach(function (b) {
    b.addEventListener('click', function () { modal.style.display = 'none'; });
  });
  modal.addEventListener('click', function (e) { if (e.target === modal) modal.style.display = 'none'; });

  /* Inline order inputs: auto-submit on change */
  document.querySelectorAll('[data-order-form] input[name="sort_order"]').forEach(function (inp) {
    inp.addEventListener('change', function () { inp.closest('form').submit(); });
  });
});
</script>
<?php admin_page_end('faq');
