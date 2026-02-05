<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['imwp_user_id']) && basename($_SERVER['PHP_SELF']) != "login.php") {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>IMWP POS</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
<div class="container-fluid">
    <a class="navbar-brand" href="dashboard.php">IMWP POS</a>

    <div class="navbar-nav">

        <a href="dashboard.php" class="nav-link text-white">Dashboard</a>

        <!-- PRODUCTS DROPDOWN -->
        <div class="nav-item dropdown">
            <a class="nav-link dropdown-toggle text-white" href="#" data-bs-toggle="dropdown">
                Products
            </a>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="products.php">Manage Products</a></li>
                <li><a class="dropdown-item" href="low_stock.php">Low Stock</a></li>
                <li><a class="dropdown-item" href="audit_log.php">Audit Log</a></li>
            </ul>
        </div>

        <a href="pos.php" class="nav-link text-white">POS</a>
        <a href="sales_report.php" class="nav-link text-white">Reports</a>
        <a href="expenses.php" class="nav-link text-white">Expenses</a>
        <a href="settings.php" class="nav-link text-white">Settings</a>

        <a href="logout.php" class="nav-link text-danger">Logout</a>

    </div>
</div>
</nav>

<div class="container mt-4">
