<?php
require_once __DIR__ . "/config.php";
require_once __DIR__ . "/auth.php";
require_login();
$active="manage"; $page_title="Manage Expenses"; $css_file="assets/css/manage_expenses.css";

$q = trim($_GET['q'] ?? '');
$cat = trim($_GET['category'] ?? '');
$categories = ['','Medicine','Food','Bills and Recharges','Entertainment','Clothings','Household Items','Rent','Others'];

$token = csrf_token();

// Build query safely
$sql = "SELECT id, spent_on, category, notes, amount FROM expenses WHERE user_id = ?";
$params = [$_SESSION['user_id']]; $types = "i";
if ($q !== '') { $sql .= " AND notes LIKE ?"; $params[] = "%".$q."%"; $types .= "s"; }
if ($cat !== '' && in_array($cat, $categories, true)) { $sql .= " AND category = ?"; $params[] = $cat; $types .= "s"; }
$sql .= " ORDER BY spent_on DESC, id DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$res = $stmt->get_result();
$rows = $res->fetch_all(MYSQLI_ASSOC);

// total
$stmt2 = $conn->prepare("SELECT COALESCE(SUM(amount),0) AS total FROM expenses WHERE user_id = ?");
$stmt2->bind_param("i", $_SESSION['user_id']);
$stmt2->execute();
$total_all = $stmt2->get_result()->fetch_assoc()['total'] ?? 0;

include __DIR__ . "/header.php";
?>
<div class="card">
  <form method="get" class="filter-row">
    <input type="text" name="q" placeholder="Search notes..." value="<?php echo htmlspecialchars($q); ?>">
    <select name="category">
      <?php foreach($categories as $c): ?>
        <option value="<?php echo htmlspecialchars($c); ?>" <?php if($c===$cat) echo "selected"; ?>>
          <?php echo $c === '' ? 'All Categories' : htmlspecialchars($c); ?>
        </option>
      <?php endforeach; ?>
    </select>
    <button type="submit" class="btn">Filter</button>
    <a href="manage_expenses.php" class="btn ghost">Reset</a>
  </form>

  <table class="table">
    <thead>
      <tr>
        <th>Date</th>
        <th>Category</th>
        <th>Notes</th>
        <th class="right">Amount</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
      <?php if (!$rows): ?>
        <tr><td colspan="5" class="muted center">No records</td></tr>
      <?php else: ?>
        <?php foreach($rows as $r): ?>
          <tr>
            <td><?php echo htmlspecialchars($r['spent_on']); ?></td>
            <td><?php echo htmlspecialchars($r['category']); ?></td>
            <td><?php echo htmlspecialchars($r['notes']); ?></td>
            <td class="right">₹ <?php echo number_format($r['amount'], 2); ?></td>
            <td>
              <a class="link" href="edit_expense.php?id=<?php echo (int)$r['id']; ?>">Edit</a>
              <form method="post" action="delete_expense.php" class="inline-form">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($token); ?>">
                <input type="hidden" name="id" value="<?php echo (int)$r['id']; ?>">
                <button type="submit" class="link danger">Delete</button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
      <?php endif; ?>
    </tbody>
    <tfoot>
      <tr>
        <th colspan="3" class="right">Total</th>
        <th class="right">₹ <?php echo number_format($total_all, 2); ?></th>
        <th></th>
      </tr>
    </tfoot>
  </table>
</div>
<?php include __DIR__ . "/footer.php"; ?>
