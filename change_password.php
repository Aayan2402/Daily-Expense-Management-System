<?php
require_once __DIR__ . "/config.php";
require_once __DIR__ . "/auth.php";
require_login();
$active="password"; $page_title="Change Password"; $css_file="assets/css/change_password.css";

$err=""; $ok="";
if ($_SERVER['REQUEST_METHOD']==='POST') {
    verify_csrf();
    $current = $_POST['current'] ?? '';
    $new = $_POST['new'] ?? '';
    $confirm = $_POST['confirm'] ?? '';
    if ($new !== $confirm) { $err = "Passwords do not match."; }
    else {
        $stmt = $conn->prepare("SELECT password_hash FROM users WHERE id = ?");
        $stmt->bind_param("i", $_SESSION['user_id']);
        $stmt->execute();
        $hash = $stmt->get_result()->fetch_assoc()['password_hash'] ?? null;
        if (!$hash || !password_verify($current, $hash)) {
            $err = "Current password is incorrect.";
        } else {
            $newhash = password_hash($new, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE users SET password_hash=? WHERE id=?");
            $stmt->bind_param("si", $newhash, $_SESSION['user_id']);
            $stmt->execute();
            $ok = "Password updated.";
        }
    }
}
$token = csrf_token();

include __DIR__ . "/header.php";
?>
<div class="card narrow">
  <?php if($err): ?><div class="alert danger"><?php echo htmlspecialchars($err); ?></div><?php endif; ?>
  <?php if($ok): ?><div class="alert success"><?php echo htmlspecialchars($ok); ?></div><?php endif; ?>
  <form method="post" class="form-vertical">
    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($token); ?>">
    <label>Current Password</label>
    <input type="password" name="current" required>
    <label>New Password</label>
    <input type="password" name="new" required>
    <label>Confirm New Password</label>
    <input type="password" name="confirm" required>
    <div class="actions">
      <button type="submit" class="btn">Change Password</button>
    </div>
  </form>
</div>
<?php include __DIR__ . "/footer.php"; ?>
