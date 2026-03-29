<?php
require_once __DIR__ . "/config.php";
require_once __DIR__ . "/auth.php";
require_login();
$active="manage"; $page_title="Edit Expense"; $css_file="assets/css/add_expense.css";

$categories = ['Medicine','Food','Bills and Recharges','Entertainment','Clothings','Household Items','Rent','Others'];
$err=""; $info="";

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$stmt = $conn->prepare("SELECT id, amount, category, notes, spent_on FROM expenses WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $id, $_SESSION['user_id']);
$stmt->execute();
$exp = $stmt->get_result()->fetch_assoc();

if (!$exp) {
    header("Location: manage_expenses.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD']==='POST') {
    verify_csrf();
    $amount = (float)($_POST['amount'] ?? 0);
    $date = $_POST['date'] ?? $exp['spent_on'];
    $category = $_POST['category'] ?? $exp['category'];
    $notes = trim($_POST['notes'] ?? '');
    if ($amount <= 0) { $err="Amount must be greater than 0."; }
    elseif (!in_array($category, $categories, true)) { $err="Invalid category."; }
    else {
        $stmt = $conn->prepare("UPDATE expenses SET amount=?, category=?, notes=?, spent_on=? WHERE id=? AND user_id=?");
        $stmt->bind_param("dsssii", $amount, $category, $notes, $date, $id, $_SESSION['user_id']);
        $stmt->execute();
        $info="Updated successfully.";
        // refresh current data
        $stmt = $conn->prepare("SELECT id, amount, category, notes, spent_on FROM expenses WHERE id = ? AND user_id = ?");
        $stmt->bind_param("ii", $id, $_SESSION['user_id']);
        $stmt->execute();
        $exp = $stmt->get_result()->fetch_assoc();
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
        <input type="number" step="0.01" min="0.01" name="amount" value="<?php echo htmlspecialchars($exp['amount']); ?>" required>
      </div>
      <div>
        <label>Date</label>
        <input type="date" name="date" value="<?php echo htmlspecialchars($exp['spent_on']); ?>" required>
      </div>
    </div>
    <div class="grid two">
      <div>
        <label>Category</label>
        <div class="radio-grid">
          <?php foreach($categories as $c): ?>
            <label class="radio">
              <input type="radio" name="category" value="<?php echo htmlspecialchars($c); ?>" <?php echo $exp['category']===$c?'checked':''; ?>>
              <span><?php echo htmlspecialchars($c); ?></span>
            </label>
          <?php endforeach; ?>
        </div>
      </div>
      <div>
        <label>Notes</label>
        <textarea name="notes" rows="6"><?php echo htmlspecialchars($exp['notes']); ?></textarea>
      </div>
    </div>
    <div class="actions">
      <button type="submit" class="btn">Save Changes</button>
      <a class="btn ghost" href="manage_expenses.php">Back</a>
    </div>
  </form>
</div>
<?php include __DIR__ . "/footer.php"; ?>
