<?php
require_once __DIR__ . '/config/db.php';
include 'includes/header.php';

$sql = "SELECT s.id,
        s.total_amount,
        s.created_at,
        SUM(si.quantity) as total_items
        FROM sales s
        LEFT JOIN sale_items si ON s.id = si.sale_id
        GROUP BY s.id
        ORDER BY s.id DESC";

$res = $conn->query($sql);

if(!$res){
    die("Query Error: ".$conn->error);
}
?>

<h3>Sales Report</h3>

<table class="table table-bordered">
<tr>
<th>Invoice #</th>
<th>Date</th>
<th>Total Items</th>
<th>Total Amount</th>
<th>Action</th>
</tr>

<?php
while($row = $res->fetch_assoc()){
echo "<tr>
<td>#{$row['id']}</td>
<td>{$row['created_at']}</td>
<td>{$row['total_items']}</td>
<td>{$row['total_amount']}</td>
<td><a href='view_invoice.php?id={$row['id']}'>View</a></td>
</tr>";
}
?>

</table>

<?php include 'includes/footer.php'; ?>
