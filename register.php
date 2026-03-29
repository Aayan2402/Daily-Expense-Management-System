<?php
require_once __DIR__ . "/config.php";
require_once __DIR__ . "/auth.php";
if (is_logged_in()) { header("Location: dashboard.php"); exit(); }

$err = ""; $ok = "";
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    verify_csrf();
    $name = trim($_POST['name'] ?? "");
    $email = trim($_POST['email'] ?? "");
    $password = $_POST['password'] ?? "";
    $confirm = $_POST['confirm'] ?? "";

    if ($password !== $confirm) {
        $err = "Passwords do not match.";
    } else {
        // Check if email exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        if ($stmt->get_result()->fetch_assoc()) {
            $err = "Email already registered.";
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (name, email, password_hash) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $name, $email, $hash);
            $stmt->execute();
            $ok = "Account created. You can now log in.";
        }
    }
}
$token = csrf_token();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Register • Daily Expenses</title>
<link rel="stylesheet" href="assets/css/base.css">
<link rel="stylesheet" href="assets/css/register.css">
</head>
<body class="auth-body">
  <div class="auth-card">
    <h1>Create account</h1>
    <p class="muted">Sign up to start tracking</p>
    <?php if (!empty($err)): ?>
      <div class="alert danger"><?php echo htmlspecialchars($err); ?></div>
    <?php endif; ?>
    <?php if (!empty($ok)): ?>
      <div class="alert success"><?php echo htmlspecialchars($ok); ?></div>
    <?php endif; ?>
    <form method="post">
      <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($token); ?>">
      <label>Name</label>
      <input type="text" name="name" required placeholder="Your name">
      <label>Email</label>
      <input type="email" name="email" required placeholder="you@example.com">
      <label>Password</label>
      <input type="password" name="password" required>
      <label>Confirm Password</label>
      <input type="password" name="confirm" required>
      <button type="submit" class="btn">Create account</button>
    </form>
    <p class="muted small">Already have an account? <a href="login.php">Login</a></p>
  </div>
</body>
</html>
