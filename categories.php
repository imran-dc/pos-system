<?php
require_once __DIR__ . '/config/db.php';
include 'includes/header.php';

if(isset($_POST['add'])){
    $name = $_POST['name'];
    $conn->query("INSERT INTO categories (name) VALUES ('$name')");
}
?>

<h3>Add Category</h3>

<form method="POST">
    <input name="name" placeholder="Category Name" required>
    <button name="add">Add</button>
</form>

<hr>

<table border="1">
<tr>
    <th>ID</th>
    <th>Name</th>
</tr>

<?php
$result = $conn->query("SELECT * FROM categories");
while($row = $result->fetch_assoc()){
?>
<tr>
    <td><?= $row['id'] ?></td>
    <td><?= $row['name'] ?></td>
</tr>
<?php } ?>
</table>
<?php include 'includes/footer.php'; ?>
