<?php
require_once __DIR__ . "/config.php";
require_once __DIR__ . "/auth.php";
require_login();
$active="add"; $page_title="Add Your Daily Expenses"; $css_file="assets/css/add_expense.css";

$info = ""; $err="";
$categories = ['Medicine','Food','Bills and Recharges','Entertainment','Clothings','Household Items','Rent','Others'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $amount = (float)($_POST['amount'] ?? 0);
    $date = $_POST['date'] ?? date('Y-m-d');
    $category = $_POST['category'] ?? '';
    $notes = trim($_POST['notes'] ?? '');

    if ($amount <= 0) { $err = "Amount must be greater than 0."; }
    elseif (!in_array($category, $categories, true)) { $err = "Invalid category."; }
    else {
        $stmt = $conn->prepare("INSERT INTO expenses (user_id, amount, category, notes, spent_on) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("idsss", $_SESSION['user_id'], $amount, $category, $notes, $date);
        $stmt->execute();
        $info = "Expense added successfully.";
    }
}
$token = csrf_token();

include __DIR__ . "/header.php";
?>
<div class="card">
  <?php if (!empty($info)): ?><div class="alert success"><?php echo htmlspecialchars($info); ?></div><?php endif; ?>
  <?php if (!empty($err)): ?><div class="alert danger"><?php echo htmlspecialchars($err); ?></div><?php endif; ?>
  <form method="post" class="form-grid">
    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($token); ?>">
    <div class="grid">
      <div>
        <label>Enter Amount (₹)</label>
        <input type="number" step="0.01" min="0.01" name="amount" placeholder="e.g. 120.50" required>
      </div>
      <div>
        <label>Date</label>
        <input type="date" name="date" value="<?php echo date('Y-m-d'); ?>" required>
      </div>
    </div>

    <div class="grid two">
      <div>
        <label>Category</label>
        <div class="radio-grid">
          <?php foreach($categories as $i=>$c): ?>
            <label class="radio">
              <input type="radio" name="category" value="<?php echo htmlspecialchars($c); ?>" <?php echo $i===0 ? 'checked' : ''; ?>>
              <span><?php echo htmlspecialchars($c); ?></span>
            </label>
          <?php endforeach; ?>
        </div>
      </div>
      <div>
        <label>Notes (optional)</label>
        <textarea name="notes" placeholder="Short description" rows="6"></textarea>
      </div>
    </div>

    <div class="actions">
      <button type="submit" class="btn">Add Expense</button>
      <a class="btn ghost" href="manage_expenses.php">View All</a>
    </div>
  </form>
</div>
<?php include __DIR__ . "/footer.php"; ?>
