<?php
include '../includes/header.php';
include '../db.php';

$from = $_GET['from'] ?? '';
$to   = $_GET['to'] ?? '';

$where = "";
if($from && $to){
    $where = "WHERE sale_date BETWEEN '$from' AND '$to'";
}

$query = "
SELECT 
    s.invoice_no,
    s.sale_date,
    s.grand_total,
    SUM((si.price - p.purchase_price - si.discount) * si.qty) AS profit
FROM sales s
JOIN sale_items si ON s.id = si.sale_id
JOIN products p ON si.product_id = p.id
$where
GROUP BY s.id
ORDER BY s.id DESC
";

$result = $conn->query($query);
?>

<h4>Sales Summary Report</h4>

<form method="GET">
    From: <input type="date" name="from">
    To: <input type="date" name="to">
    <button class="btn btn-primary">Filter</button>
    <a href="sales_summary.php?export=excel&from=<?=$from?>&to=<?=$to?>" class="btn btn-success">Export Excel</a>
</form>

<table class="table table-bordered mt-3">
<tr>
    <th>Invoice</th>
    <th>Date</th>
    <th>Sale Amount</th>
    <th>Profit</th>
</tr>

<?php while($row = $result->fetch_assoc()): ?>
<tr>
    <td><?= $row['invoice_no'] ?></td>
    <td><?= $row['sale_date'] ?></td>
    <td><?= $row['grand_total'] ?></td>
    <td><?= number_format($row['profit'],2) ?></td>
</tr>
<?php endwhile; ?>
</table>

<?php include '../includes/footer.php'; ?>
