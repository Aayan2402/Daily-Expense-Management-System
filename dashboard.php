<?php
require_once __DIR__ . "/config.php";
require_once __DIR__ . "/auth.php";
require_login();
$active = "dashboard"; $page_title="Dashboard"; $css_file="assets/css/dashboard.css";

$user_id = (int)$_SESSION['user_id'];

// totals
$stmt = $conn->prepare("SELECT COALESCE(SUM(amount),0) AS total FROM expenses WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$total_all = $stmt->get_result()->fetch_assoc()['total'] ?? 0;

$stmt = $conn->prepare("SELECT COALESCE(SUM(amount),0) AS total FROM expenses WHERE user_id = ? AND YEAR(spent_on)=YEAR(CURDATE()) AND MONTH(spent_on)=MONTH(CURDATE())");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$total_month = $stmt->get_result()->fetch_assoc()['total'] ?? 0;

$stmt = $conn->prepare("SELECT COUNT(*) AS cnt FROM expenses WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$entries = $stmt->get_result()->fetch_assoc()['cnt'] ?? 0;

// category totals
$stmt = $conn->prepare("SELECT category, COALESCE(SUM(amount),0) AS total FROM expenses WHERE user_id=? GROUP BY category ORDER BY total DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$cats = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

include __DIR__ . "/header.php";
?>
<div class="stats">
  <div class="card stat">
    <div class="stat-label">Total Spent</div>
    <div class="stat-value">₹ <?php echo number_format($total_all, 2); ?></div>
  </div>
  <div class="card stat">
    <div class="stat-label">This Month</div>
    <div class="stat-value">₹ <?php echo number_format($total_month, 2); ?></div>
  </div>
  <div class="card stat">
    <div class="stat-label">Entries</div>
    <div class="stat-value"><?php echo (int)$entries; ?></div>
  </div>
</div>

<div class="card">
  <h2>Top Categories</h2>
  <div class="bars">
    <?php
      $max = 0;
      foreach($cats as $c){ if ($c['total'] > $max) $max = (float)$c['total']; }
      if ($max == 0) {
        echo '<p class="muted">No data yet. Add some expenses.</p>';
      } else {
        foreach ($cats as $c) {
          $width = $max > 0 ? round(($c['total']/$max)*100) : 0;
          $label = htmlspecialchars($c['category']);
          $amount = number_format($c['total'],2);
          echo '<div class="bar-row"><div class="bar-label">'.$label.'</div><div class="bar"><div class="bar-fill" style="width:'.$width.'%"></div></div><div class="bar-amount">₹ '.$amount.'</div></div>';
        }
      }
    ?>
  </div>
</div>

<?php include __DIR__ . "/footer.php"; ?>
