<?php
require_once __DIR__ . "/config.php";
require_once __DIR__ . "/auth.php";
if (is_logged_in()) { header("Location: dashboard.php"); exit(); }

$err = "";
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    verify_csrf();
    $email = trim($_POST['email'] ?? "");
    $password = $_POST['password'] ?? "";
    $stmt = $conn->prepare("SELECT id, name, email, password_hash FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($row = $res->fetch_assoc()) {
        if (password_verify($password, $row['password_hash'])) {
            $_SESSION['user_id'] = (int)$row['id'];
            $_SESSION['name'] = $row['name'];
            $_SESSION['email'] = $row['email'];
            header("Location: dashboard.php");
            exit();
        } else { $err = "Invalid email or password."; }
    } else { $err = "Invalid email or password."; }
}
$token = csrf_token();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Login • Daily Expenses</title>
<link rel="stylesheet" href="assets/css/base.css">
<link rel="stylesheet" href="assets/css/login.css">
<style>
  .auth-body {
    /* Background image */
    background: url("backgroundlog.jpg") no-repeat center center fixed;
    background-size: cover;
    min-height: 100vh;

    /* Optional: center card */
    display: flex;
    justify-content: center;
    align-items: center;
  }
</style>
</head>
<body class="auth-body">
  <div class="auth-card">
    <h1>Welcome back</h1>
    <p class="muted">Login to continue</p>
    <?php if (!empty($err)): ?>
      <div class="alert danger"><?php echo htmlspecialchars($err); ?></div>
    <?php endif; ?>
    <form method="post">
      <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($token); ?>">
      <label>Email</label>
      <input type="email" name="email" required placeholder="you@example.com">
      <label>Password</label>
      <input type="password" name="password" required placeholder="••••••••">
      <button type="submit" class="btn">Login</button>
    </form>
    <p class="muted small">No account? <a href="register.php">Create one</a></p>
  </div>
</body>
</html>
