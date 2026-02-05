<?php

if(isset($_POST['add_product'])){

    $stmt=$imwp_conn->prepare("INSERT INTO imwp_products
    (barcode,name,category_id,subcategory_id,brand_id,
    stock,purchase_price,sale_price,
    main_unit,sub_unit,sub_unit_size,
    alert_level,status)
    VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)");

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
        $_POST['status']
    );

    $stmt->execute();
}
?>

<div class="card shadow-sm mb-4 border-0">
    <div class="card-header bg-dark text-white fw-semibold">
        Add New Product
    </div>

    <div class="card-body bg-light">

        <form method="post" class="row g-3">

            <!-- BASIC INFO -->
            <div class="col-md-4">
                <label class="form-label fw-semibold">Barcode *</label>
                <input type="text" name="barcode" class="form-control" required>
            </div>

            <div class="col-md-4">
                <label class="form-label fw-semibold">Product Name *</label>
                <input type="text" name="name" class="form-control" required>
            </div>

            <div class="col-md-4">
                <label class="form-label fw-semibold">Category *</label>
                <select name="category" class="form-select" required>
                    <option value="">Select Category</option>
                    <?php
                    $cats=$imwp_conn->query("SELECT * FROM imwp_categories ORDER BY name ASC");
                    while($c=$cats->fetch_assoc()){
                        echo "<option value='{$c['id']}'>{$c['name']}</option>";
                    }
                    ?>
                </select>
            </div>

            <!-- OPTIONAL META -->
            <div class="col-md-4">
                <label class="form-label">Sub Category</label>
                <select name="subcategory" class="form-select">
                    <option value="">Select</option>
                    <?php
                    $subs=$imwp_conn->query("SELECT * FROM imwp_subcategories ORDER BY name ASC");
                    while($s=$subs->fetch_assoc()){
                        echo "<option value='{$s['id']}'>{$s['name']}</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="col-md-4">
                <label class="form-label">Brand</label>
                <select name="brand" class="form-select">
                    <option value="">Select</option>
                    <?php
                    $brands=$imwp_conn->query("SELECT * FROM imwp_brands ORDER BY name ASC");
                    while($b=$brands->fetch_assoc()){
                        echo "<option value='{$b['id']}'>{$b['name']}</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="col-md-4">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="active" selected>Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>

            <!-- STOCK + PRICING -->
            <div class="col-md-3">
                <label class="form-label">Stock</label>
                <input type="number" step="0.01" name="stock" class="form-control" value="0">
            </div>

            <div class="col-md-3">
                <label class="form-label">Purchase Price</label>
                <input type="number" step="0.01" name="purchase_price" class="form-control" value="0">
            </div>

            <div class="col-md-3">
                <label class="form-label">Sale Price</label>
                <input type="number" step="0.01" name="sale_price" class="form-control" value="0">
            </div>

           <div class="col-md-3">
				<label class="form-label">Alert Level</label>
				<input type="number"
					name="alert_level"
					class="form-control"
					value="0"
					min="0"
					step="1">
			</div>

            <!-- UNITS -->
            <div class="col-md-4">
                <label class="form-label">Main Unit</label>
                <input type="text" name="main_unit" class="form-control" placeholder="CTN / Roll">
            </div>

            <div class="col-md-4">
                <label class="form-label">Sub Unit</label>
                <input type="text" name="sub_unit" class="form-control" placeholder="PCS / Yard">
            </div>

            <div class="col-md-4">
                <label class="form-label">Sub Unit Size</label>
                <input type="number" step="0.01" name="sub_unit_size" class="form-control" value="0">
            </div>

            <!-- BUTTON -->
            <div class="col-12 mt-3 text-end">
                <button type="submit" name="add_product" class="btn btn-dark px-4">
                    Save Product
                </button>
            </div>

        </form>

    </div>
</div>
