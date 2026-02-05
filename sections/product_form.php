<?php

if(isset($_POST['add_product'])){

    $stmt=$imwp_conn->prepare("INSERT INTO imwp_products
    (barcode,name,category_id,subcategory_id,brand_id,
    stock,purchase_price,sale_price,
    main_unit,sub_unit,sub_unit_size,
    alert_level,status)
    VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)");

    $status="active";

    $stmt->bind_param("ssiiidddssdds",
        $_POST['barcode'],
        $_POST['name'],
        $_POST['category'],
        $_POST['subcategory'],
        $_POST['brand'],
        $_POST['stock'],
        $_POST['purchase_price'],
        $_POST['sale_price'],
        $_POST['main_unit'],
        $_POST['sub_unit'],
        $_POST['sub_unit_size'],
        $_POST['alert_level'],
        $status
    );

    $stmt->execute();
}
?>

<h5>Add Product</h5>

<form method="post" class="row g-2">

<div class="col-md-3">
<label>Barcode</label>
<input type="text" name="barcode" class="form-control">
</div>

<div class="col-md-3">
<label>Product Name</label>
<input type="text" name="name" class="form-control" required>
</div>

<div class="col-md-3">
<label>Category</label>
<select name="category" class="form-control" required>
<option value="">Select</option>
<?php
$cats=$imwp_conn->query("SELECT * FROM imwp_categories");
while($c=$cats->fetch_assoc()){
echo "<option value='{$c['id']}'>{$c['name']}</option>";
}
?>
</select>
</div>

<div class="col-md-3">
<label>Sub Category</label>
<select name="subcategory" class="form-control">
<option value="">Select</option>
<?php
$subs=$imwp_conn->query("SELECT * FROM imwp_subcategories");
while($s=$subs->fetch_assoc()){
echo "<option value='{$s['id']}'>{$s['name']}</option>";
}
?>
</select>
</div>

<div class="col-md-3">
<label>Brand</label>
<select name="brand" class="form-control">
<option value="">Select</option>
<?php
$brands=$imwp_conn->query("SELECT * FROM imwp_brands");
while($b=$brands->fetch_assoc()){
echo "<option value='{$b['id']}'>{$b['name']}</option>";
}
?>
</select>
</div>

<div class="col-md-2">
<label>Quantity (Stock)</label>
<input type="number" step="0.01" name="stock" class="form-control" required>
</div>

<div class="col-md-2">
<label>Purchase Price</label>
<input type="number" step="0.01" name="purchase_price" class="form-control">
</div>

<div class="col-md-2">
<label>Sale Price</label>
<input type="number" step="0.01" name="sale_price" class="form-control">
</div>

<div class="col-md-2">
<label>Main Unit</label>
<input type="text" name="main_unit" class="form-control" placeholder="CTN / Roll">
</div>

<div class="col-md-2">
<label>Sub Unit</label>
<input type="text" name="sub_unit" class="form-control" placeholder="PCS / Yard">
</div>

<div class="col-md-2">
<label>Sub Unit Size</label>
<input type="number" step="0.01" name="sub_unit_size" class="form-control">
</div>

<div class="col-md-2">
<label>Alert Level</label>
<input type="number" step="0.01" name="alert_level" class="form-control">
</div>

<div class="col-md-12 mt-2">
<button name="add_product" class="btn btn-success">Save Product</button>
</div>

</form>
