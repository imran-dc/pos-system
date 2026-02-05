<?php include 'includes/header.php'; ?>
<?php require_once __DIR__ . '/config/db.php'; ?>

<h3>Add Expense</h3>

<form method="POST">
    <input type="text" name="title" class="form-control" placeholder="Expense Title" required><br>
    <input type="number" step="0.01" name="amount" class="form-control" placeholder="Amount" required><br>
    <button type="submit" name="save" class="btn btn-danger">Save Expense</button>
</form>

<?php
if(isset($_POST['save'])){
    $title = $_POST['title'];
    $amount = $_POST['amount'];

    $conn->query("INSERT INTO expenses (title, amount) VALUES ('$title','$amount')");
}
?>

<hr>

<h4>All Expenses</h4>

<table class="table table-bordered">
<tr>
    <th>Date</th>
    <th>Title</th>
    <th>Amount</th>
</tr>

<?php
$data = $conn->query("SELECT * FROM expenses ORDER BY id DESC");
while($row = $data->fetch_assoc()){
    echo "<tr>
            <td>{$row['created_at']}</td>
            <td>{$row['title']}</td>
            <td>{$row['amount']}</td>
          </tr>";
}
?>

</table>

<?php include 'includes/footer.php'; ?>
