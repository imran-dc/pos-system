<?php
include '../includes/header.php';
include '../db.php';

$result = $conn->query("
SELECT 
p.name,
SUM(si.qty) as total_qty
FROM sale_items si
JOIN products p ON si.product_id = p.id
GROUP BY si.product_id
ORDER BY total_qty DESC
LIMIT 10
");
?>

<h4>Top Selling Items</h4>

<table class="table table-bordered">
<tr>
    <th>Product</th>
    <th>Quantity Sold</th>
</tr>

<?php while($row = $result->fetch_assoc()): ?>
<tr>
    <td><?= $row['name'] ?></td>
    <td><?= $row['total_qty'] ?></td>
</tr>
<?php endwhile; ?>
</table>

<?php include '../includes/footer.php'; ?>
