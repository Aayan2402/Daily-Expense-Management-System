<?php
require_once __DIR__ . "/config.php";
require_once __DIR__ . "/auth.php";
require_login();
$active="profile"; $page_title="Profile"; $css_file="assets/css/profile.css";

$err=""; $ok="";
$user_id = (int)$_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD']==='POST') {
    verify_csrf();
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');

    // Check if new email already exists for other user
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? AND id <> ?");
    $stmt->bind_param("si", $email, $user_id);
    $stmt->execute();
    if ($stmt->get_result()->fetch_assoc()) {
        $err = "Email already in use.";
    } else {
        $stmt = $conn->prepare("UPDATE users SET name=?, email=? WHERE id=?");
        $stmt->bind_param("ssi", $name, $email, $user_id);
        $stmt->execute();
        $_SESSION['name'] = $name;
        $_SESSION['email'] = $email;
        $ok = "Profile updated.";
    }
}

$stmt = $conn->prepare("SELECT name, email, created_at FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

$token = csrf_token();

include __DIR__ . "/header.php";
?>
<div class="card narrow">
  <?php if($err): ?><div class="alert danger"><?php echo htmlspecialchars($err); ?></div><?php endif; ?>
  <?php if($ok): ?><div class="alert success"><?php echo htmlspecialchars($ok); ?></div><?php endif; ?>
  <form method="post" class="form-vertical">
    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($token); ?>">
    <label>Name</label>
    <input type="text" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>
    <label>Email</label>
    <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
    <div class="muted small">Member since: <?php echo htmlspecialchars($user['created_at']); ?></div>
    <div class="actions">
      <button type="submit" class="btn">Save</button>
    </div>
  </form>
</div>
<?php include __DIR__ . "/footer.php"; ?>
