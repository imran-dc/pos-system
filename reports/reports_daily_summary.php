<?php
include '../includes/header.php';
include '../db.php';

$result = $conn->query("
SELECT 
sale_date,
SUM(grand_total) as total_sales
FROM sales
GROUP BY sale_date
ORDER BY sale_date DESC
");
?>

<h4>Daily Sales Summary</h4>

<table class="table table-bordered">
<tr>
    <th>Date</th>
    <th>Total Sales</th>
</tr>

<?php while($row = $result->fetch_assoc()): ?>
<tr>
    <td><?= $row['sale_date'] ?></td>
    <td><?= $row['total_sales'] ?></td>
</tr>
<?php endwhile; ?>
</table>

<?php include '../includes/footer.php'; ?>
