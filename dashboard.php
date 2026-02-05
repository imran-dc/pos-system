<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);



require_once "config/db.php";
include "includes/header.php";

$totalProducts = 0;
$totalSales = 0;
$totalExpense = 0;

$res1 = $imwp_conn->query("SELECT COUNT(*) as total FROM imwp_products");
if($res1){ $totalProducts = $res1->fetch_assoc()['total'] ?? 0; }

$res2 = $imwp_conn->query("SELECT SUM(grand_total) as total FROM imwp_sales");
if($res2){ $totalSales = $res2->fetch_assoc()['total'] ?? 0; }

$res3 = $imwp_conn->query("SELECT SUM(amount) as total FROM imwp_expenses");
if($res3){ $totalExpense = $res3->fetch_assoc()['total'] ?? 0; }
?>

<h4>Dashboard</h4>

<div class="row mt-4">

<div class="col-md-4">
<div class="card bg-primary text-white p-3">
Total Products
<h3><?= $totalProducts ?></h3>
</div>
</div>

<div class="col-md-4">
<div class="card bg-success text-white p-3">
Total Sales
<h3><?= number_format($totalSales,2) ?></h3>
</div>
</div>

<div class="col-md-4">
<div class="card bg-danger text-white p-3">
Total Expenses
<h3><?= number_format($totalExpense,2) ?></h3>
</div>
</div>

</div>

<?php include "includes/footer.php"; ?>
