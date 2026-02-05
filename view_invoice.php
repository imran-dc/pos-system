<?php
require_once __DIR__ . '/config/db.php';
include 'includes/header.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Sale not found");
}

$sale_id = intval($_GET['id']);

$saleQuery = $conn->prepare("SELECT * FROM sales WHERE id = ?");
$saleQuery->bind_param("i", $sale_id);
$saleQuery->execute();
$saleResult = $saleQuery->get_result();

if ($saleResult->num_rows == 0) {
    die("Sale not found");
}

$sale = $saleResult->fetch_assoc();

$itemQuery = $conn->prepare("
    SELECT si.*, p.name, p.code 
    FROM sale_items si
    JOIN products p ON si.product_id = p.id
    WHERE si.sale_id = ?
");
$itemQuery->bind_param("i", $sale_id);
$itemQuery->execute();
$items = $itemQuery->get_result();
?>

<div class="container mt-4" id="invoiceArea">

    <h3>Invoice #<?= $sale['id'] ?></h3>
    <p>Date: <?= $sale['created_at'] ?></p>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Code</th>
                <th>Item</th>
                <th>Qty</th>
                <th>Price</th>
                <th>Disc</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
        <?php while($row = $items->fetch_assoc()): ?>
            <?php
                $itemTotal = ($row['price'] * $row['qty']) - $row['discount'];
            ?>
            <tr>
                <td><?= $row['code'] ?></td>
                <td><?= $row['name'] ?></td>
                <td><?= $row['qty'] ?></td>
                <td><?= number_format($row['price'],2) ?></td>
                <td><?= number_format($row['discount'],2) ?></td>
                <td><?= number_format($itemTotal,2) ?></td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>

    <div style="text-align:right">
        <p>Sub Total: <?= number_format($sale['subtotal'],2) ?></p>
        <p>Extra Discount: <?= number_format($sale['extra_discount'],2) ?></p>
        <h4>Grand Total: <?= number_format($sale['grand_total'],2) ?></h4>
        <p>Paid: <?= number_format($sale['paid'],2) ?></p>
        <p>Change: <?= number_format($sale['change_amount'],2) ?></p>
    </div>

    <hr>
    <p style="text-align:center">
        <?= $sale['footer_msg'] ?? 'Thank you for your business!' ?>
    </p>

    <button onclick="window.print()" class="btn btn-primary">Print</button>
</div>

<?php include 'includes/footer.php'; ?>
