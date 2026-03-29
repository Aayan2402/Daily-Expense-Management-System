<?php
require_once __DIR__ . "/auth.php";
require_login();
$page_title = $page_title ?? "Daily Expenses Manager";
$css_file = $css_file ?? "assets/css/dashboard.css";
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?php echo htmlspecialchars($page_title); ?></title>
  <link rel="stylesheet" href="assets/css/base.css">
  <link rel="stylesheet" href="<?php echo htmlspecialchars($css_file); ?>">
</head>
<body>
<div class="layout">
  <aside class="sidebar">
    <div class="brand">
      <div class="avatar"><?php echo strtoupper(substr($_SESSION['name'] ?? "U", 0, 1)); ?></div>
      <div class="user-meta">
        <div class="user-name"><?php echo htmlspecialchars($_SESSION['name'] ?? "User"); ?></div>
        <div class="user-email"><?php echo htmlspecialchars($_SESSION['email'] ?? ""); ?></div>
      </div>
    </div>
    <nav class="nav">
      <a class="nav-link<?php if(($active ?? '')==='dashboard') echo ' active'; ?>" href="dashboard.php">🏠 Dashboard</a>
      <a class="nav-link<?php if(($active ?? '')==='add') echo ' active'; ?>" href="add_expense.php">➕ Add Expenses</a>
      <a class="nav-link<?php if(($active ?? '')==='manage') echo ' active'; ?>" href="manage_expenses.php">💰 Manage Expenses</a>
      <div class="nav-section">Settings</div>
      <a class="nav-link<?php if(($active ?? '')==='profile') echo ' active'; ?>" href="profile.php">👤 Profile</a>
      <a class="nav-link<?php if(($active ?? '')==='password') echo ' active'; ?>" href="change_password.php">🔑 Change Password</a>
      <a class="nav-link" href="logout.php">⏻ Logout</a>
    </nav>
    <div class="footer-note">Daily Expenses Manager</div>
  </aside>
  <main class="main">
    <header class="page-header">
      <h1><?php echo htmlspecialchars($page_title); ?></h1>
    </header>
    <section class="content">
