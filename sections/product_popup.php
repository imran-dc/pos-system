<div class="modal fade" id="editModal">
<div class="modal-dialog modal-lg">
<div class="modal-content">

<form method="POST">

<div class="modal-header">
<h5>Edit Product</h5>
<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
</div>

<div class="modal-body row g-2">

<input type="hidden" name="edit_id" id="edit_id">

<div class="col-md-6">
<label>Name</label>
<input type="text" name="edit_name" id="edit_name" class="form-control">
</div>

<div class="col-md-6">
<label>Barcode</label>
<input type="text" name="edit_barcode" id="edit_barcode" class="form-control">
</div>

<div class="col-md-4">
<label>Category</label>
<select name="edit_category" id="edit_category" class="form-select">
<?php
$res=$imwp_conn->query("SELECT * FROM imwp_categories");
while($r=$res->fetch_assoc())
echo "<option value='{$r['id']}'>{$r['name']}</option>";
?>
</select>
</div>

<div class="col-md-4">
<label>Subcategory</label>
<select name="edit_subcategory" id="edit_subcategory" class="form-select">
<?php
$res=$imwp_conn->query("SELECT * FROM imwp_subcategories");
while($r=$res->fetch_assoc())
echo "<option value='{$r['id']}'>{$r['name']}</option>";
?>
</select>
</div>

<div class="col-md-4">
<label>Brand</label>
<select name="edit_brand" id="edit_brand" class="form-select">
<?php
$res=$imwp_conn->query("SELECT * FROM imwp_brands");
while($r=$res->fetch_assoc())
echo "<option value='{$r['id']}'>{$r['name']}</option>";
?>
</select>
</div>

<div class="col-md-4">
<label>Stock</label>
<input type="number" step="0.01" name="edit_stock" id="edit_stock" class="form-control">
</div>

<div class="col-md-4">
<label>Main Unit</label>
<input type="text" name="edit_main_unit" id="edit_main_unit" class="form-control">
</div>

<div class="col-md-4">
<label>Sub Unit</label>
<input type="text" name="edit_sub_unit" id="edit_sub_unit" class="form-control">
</div>

<div class="col-md-4">
<label>Sub Unit Size</label>
<input type="number" step="0.01" name="edit_sub_unit_size" id="edit_sub_unit_size" class="form-control">
</div>

<div class="col-md-4">
<label>Purchase Price</label>
<input type="number" step="0.01" name="edit_purchase_price" id="edit_purchase_price" class="form-control">
</div>

<div class="col-md-4">
<label>Sale Price</label>
<input type="number" step="0.01" name="edit_sale_price" id="edit_sale_price" class="form-control">
</div>

<div class="col-md-6">
<label>Alert Level</label>
<input type="number" step="0.01" name="edit_alert_level" id="edit_alert_level" class="form-control">
</div>

<div class="col-md-6">
<label>Status</label>
<select name="edit_status" id="edit_status" class="form-select">
<option value="active">Active</option>
<option value="inactive">Inactive</option>
</select>
</div>

</div>

<div class="modal-footer">
<button type="submit" name="update_product" class="btn btn-primary">Update</button>
</div>

</form>

</div>
</div>
</div>

<script>
function fillEdit(id,name,barcode,cat,sub,brand,stock,mainu,subu,size,pur,sale,alert,status){
document.getElementById('edit_id').value=id;
document.getElementById('edit_name').value=name;
document.getElementById('edit_barcode').value=barcode;
document.getElementById('edit_category').value=cat;
document.getElementById('edit_subcategory').value=sub;
document.getElementById('edit_brand').value=brand;
document.getElementById('edit_stock').value=stock;
document.getElementById('edit_main_unit').value=mainu;
document.getElementById('edit_sub_unit').value=subu;
document.getElementById('edit_sub_unit_size').value=size;
document.getElementById('edit_purchase_price').value=pur;
document.getElementById('edit_sale_price').value=sale;
document.getElementById('edit_alert_level').value=alert;
document.getElementById('edit_status').value=status;
}
</script>
