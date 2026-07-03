<?php
/**
 * Account — change password. Current password required; new password
 * min 8 chars (design hint) with confirmation. Persian errors per the
 * approved design.
 */
require_once __DIR__ . '/../includes/admin_layout.php';

require_admin();

$error = '';

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    if (!csrf_verify($_POST['csrf_token'] ?? null)) {
        http_response_code(403);
        $error = 'درخواست نامعتبر (CSRF). صفحه را از نو باز کن.';
    } else {
        $cur = (string) ($_POST['current_password'] ?? '');
        $new = (string) ($_POST['new_password'] ?? '');
        $rep = (string) ($_POST['repeat_password'] ?? '');

        $stmt = db()->prepare('SELECT password_hash FROM admin_users WHERE id = ?');
        $stmt->execute([$_SESSION['admin_id']]);
        $row = $stmt->fetch();

        if ($cur === '') {
            $error = 'رمز فعلی را وارد کن.';
        } elseif (!$row || !password_verify($cur, $row['password_hash'])) {
            $error = 'رمز فعلی درست نیست.';
        } elseif (mb_strlen($new) < 8) {
            $error = 'رمز جدید باید حداقل ۸ کاراکتر باشد.';
        } elseif ($new !== $rep) {
            $error = 'تکرار رمز با رمز جدید یکسان نیست.';
        } else {
            db()->prepare('UPDATE admin_users SET password_hash = ? WHERE id = ?')
                ->execute([password_hash($new, PASSWORD_DEFAULT), $_SESSION['admin_id']]);
            header('Location: /admin/account.php?toast=' . rawurlencode('رمز عبور تغییر کرد'));
            exit;
        }
    }
}

$new_bad = $error !== '' && mb_strlen((string) ($_POST['new_password'] ?? '')) < 8;
$rep_bad = $error !== '' && (($_POST['new_password'] ?? '') !== ($_POST['repeat_password'] ?? ''));

admin_page_start('حساب کاربری', 'account');
?>
<div style="max-width:420px;">
  <div class="a-card" style="padding:20px;">
    <b style="display:block;font-size:14px;color:#1F2A44;margin-bottom:16px;">تغییر رمز عبور</b>
    <form method="post" action="/admin/account.php">
      <?= csrf_field() ?>
      <label class="a-label-strong" for="pw-cur">رمز فعلی</label>
      <input id="pw-cur" class="a-input" type="password" dir="ltr" name="current_password" autocomplete="current-password" style="min-height:42px;font-size:14px;margin-bottom:14px;">
      <label class="a-label-strong" for="pw-new">رمز جدید</label>
      <input id="pw-new" class="a-input <?= $new_bad ? 'a-field-error' : '' ?>" type="password" dir="ltr" name="new_password" autocomplete="new-password" style="min-height:42px;font-size:14px;margin-bottom:4px;">
      <div class="a-hint" style="margin-bottom:14px;">حداقل ۸ کاراکتر</div>
      <label class="a-label-strong" for="pw-rep">تکرار رمز جدید</label>
      <input id="pw-rep" class="a-input <?= $rep_bad ? 'a-field-error' : '' ?>" type="password" dir="ltr" name="repeat_password" autocomplete="new-password" style="min-height:42px;font-size:14px;">
      <?php if ($error !== ''): ?>
      <div class="a-error-text"><?= e($error) ?></div>
      <?php endif; ?>
      <button type="submit" class="a-btn a-btn-primary" style="width:100%;margin-top:18px;min-height:44px;font-size:14px;">تغییر رمز</button>
    </form>
  </div>
</div>
<?php admin_page_end('account');
