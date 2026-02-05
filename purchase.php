<?php include 'includes/header.php'; ?>
<?php require_once __DIR__ . '/config/db.php'; ?>

<h3>New Purchase</h3>

<?php
if(isset($_POST['save'])){
    $invoice = $_POST['invoice'];
    $vendor = $_POST['vendor'];
    $date = $_POST['date'];
    $pid = $_POST['product_id'];
    $qty = $_POST['quantity'];
    $cost = $_POST['cost_price'];
    $total = $qty * $cost;

    $conn->query("INSERT INTO purchases
        (invoice_no,vendor_name,purchase_date,product_id,quantity,cost_price,total_amount)
        VALUES('$invoice','$vendor','$date','$pid','$qty','$cost','$total')");

    $conn->query("UPDATE products SET stock = stock + $qty WHERE id = $pid");
}
?>

<form method="POST">

<input type="text" name="invoice" class="form-control mb-2" placeholder="Invoice No" required>
<input type="text" name="vendor" class="form-control mb-2" placeholder="Vendor Name" required>
<input type="date" name="date" class="form-control mb-2" required>

<select name="product_id" class="form-control mb-2" required>
<option value="">Select Product</option>
<?php
$products = $conn->query("SELECT * FROM products");
while($row = $products->fetch_assoc()){
    echo "<option value='{$row['id']}'>{$row['name']}</option>";
}
?>
</select>

<input type="number" name="quantity" class="form-control mb-2" placeholder="Quantity" required>
<input type="number" step="0.01" name="cost_price" class="form-control mb-2" placeholder="Cost Price" required>

<button type="submit" name="save" class="btn btn-primary">Save Purchase</button>

</form>

<?php include 'includes/footer.php'; ?>
