<?php
if(isset($_POST['update_product'])){
    $id=(int)$_POST['edit_id'];

    $stmt=$imwp_conn->prepare("
        UPDATE imwp_products SET
        name=?,
        barcode=?,
        stock=?,
        purchase_price=?,
        sale_price=?
        WHERE id=?
    ");

    $stmt->bind_param("ssdddi",
        $_POST['edit_name'],
        $_POST['edit_barcode'],
        $_POST['edit_stock'],
        $_POST['edit_purchase_price'],
        $_POST['edit_sale_price'],
        $id
    );

    $stmt->execute();
}

/* ---------------- TOGGLE ---------------- */

if(isset($_GET['toggle'])){
    $id=(int)$_GET['toggle'];
    $imwp_conn->query("
        UPDATE imwp_products 
        SET status = IF(status='active','inactive','active')
        WHERE id=$id
    ");
}

/* ---------------- FILTER VALUES ---------------- */

$f_cat     = $_GET['f_cat']     ?? '';
$f_sub     = $_GET['f_sub']     ?? '';
$f_brand   = $_GET['f_brand']   ?? '';
$f_status  = $_GET['f_status']  ?? '';
$per_page  = isset($_GET['per_page']) ? (int)$_GET['per_page'] : 10;
$page      = isset($_GET['page']) ? (int)$_GET['page'] : 1;

$where="WHERE 1=1";

if($f_cat!='')
    $where.=" AND p.category_id=".(int)$f_cat;

if($f_sub!='')
    $where.=" AND p.subcategory_id=".(int)$f_sub;

if($f_brand!='')
    $where.=" AND p.brand_id=".(int)$f_brand;

if($f_status!='')
    $where.=" AND p.status='".$imwp_conn->real_escape_string($f_status)."'";

/* ---------------- PAGINATION ---------------- */

$start = ($page-1)*$per_page;

$totalRes=$imwp_conn->query("SELECT COUNT(*) as total FROM imwp_products p $where");
$total=$totalRes->fetch_assoc()['total'];
$total_pages=ceil($total/$per_page);

$products=$imwp_conn->query("
SELECT p.*,c.name as cat,s.name as sub,b.name as brand
FROM imwp_products p
LEFT JOIN imwp_categories c ON c.id=p.category_id
LEFT JOIN imwp_subcategories s ON s.id=p.subcategory_id
LEFT JOIN imwp_brands b ON b.id=p.brand_id
$where
ORDER BY p.id DESC
LIMIT $start,$per_page
");
?>

<h5 class="mt-4">Product List</h5>

<form class="row g-2 mb-3">

<div class="col-md-2">
<select name="f_cat" class="form-control">
<option value="">Category</option>
<?php
$cats=$imwp_conn->query("SELECT * FROM imwp_categories");
while($c=$cats->fetch_assoc()){
$selected = ($f_cat==$c['id'])?'selected':'';
echo "<option value='{$c['id']}' $selected>{$c['name']}</option>";
}
?>
</select>
</div>

<div class="col-md-2">
<select name="f_sub" class="form-control">
<option value="">Sub Category</option>
<?php
$subs=$imwp_conn->query("SELECT * FROM imwp_subcategories");
while($s=$subs->fetch_assoc()){
$selected = ($f_sub==$s['id'])?'selected':'';
echo "<option value='{$s['id']}' $selected>{$s['name']}</option>";
}
?>
</select>
</div>

<div class="col-md-2">
<select name="f_brand" class="form-control">
<option value="">Brand</option>
<?php
$brands=$imwp_conn->query("SELECT * FROM imwp_brands");
while($b=$brands->fetch_assoc()){
$selected = ($f_brand==$b['id'])?'selected':'';
echo "<option value='{$b['id']}' $selected>{$b['name']}</option>";
}
?>
</select>
</div>

<div class="col-md-2">
<select name="f_status" class="form-control">
<option value="">Status</option>
<option value="active" <?=($f_status=='active')?'selected':''?>>Active</option>
<option value="inactive" <?=($f_status=='inactive')?'selected':''?>>Inactive</option>
</select>
</div>

<div class="col-md-2">
<input type="number" name="per_page" value="<?=$per_page?>" class="form-control">
</div>

<div class="col-md-2">
<button class="btn btn-primary w-100">Filter</button>
<a href="products.php" class="btn btn-secondary w-100 mt-1">Reset</a>
</div>

</form>

<p><strong>Total Products: <?=$total?></strong></p>

<table class="table table-bordered table-striped">
<tr>
<th>Barcode</th>
<th>Name</th>
<th>Category</th>
<th>Brand</th>
<th>Stock</th>
<th>Sale Price</th>
<th>Status</th>
<th>Action</th>
</tr>

<?php while($row=$products->fetch_assoc()){ ?>
<tr>
<td><?=$row['barcode']?></td>
<td><?=$row['name']?></td>
<td><?=$row['cat']?></td>
<td><?=$row['brand']?></td>
<td><?=$row['stock']?></td>
<td><?=$row['sale_price']?></td>
<td><?=$row['status']?></td>
<td>
<a href="?toggle=<?=$row['id']?>" class="btn btn-sm btn-warning">Toggle</a>

<button class="btn btn-sm btn-info"
data-bs-toggle="modal"
data-bs-target="#editModal<?=$row['id']?>">Edit</button>
</td>
</tr>

<!-- ================= EDIT MODAL ================= -->

<div class="modal fade" id="editModal<?=$row['id']?>" tabindex="-1">
<div class="modal-dialog modal-lg">
<div class="modal-content">

<form method="post">

<div class="modal-header">
<h5>Edit Product</h5>
<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
</div>

<div class="modal-body row g-2">

<input type="hidden" name="edit_id" value="<?=$row['id']?>">

<div class="col-md-6">
<label>Product Name</label>
<input type="text" name="edit_name" value="<?=$row['name']?>" class="form-control">
</div>

<div class="col-md-6">
<label>Barcode</label>
<input type="text" name="edit_barcode" value="<?=$row['barcode']?>" class="form-control">
</div>

<div class="col-md-4">
<label>Stock</label>
<input type="number" step="0.01" name="edit_stock" value="<?=$row['stock']?>" class="form-control">
</div>

<div class="col-md-4">
<label>Purchase Price</label>
<input type="number" step="0.01" name="edit_purchase_price" value="<?=$row['purchase_price']?>" class="form-control">
</div>

<div class="col-md-4">
<label>Sale Price</label>
<input type="number" step="0.01" name="edit_sale_price" value="<?=$row['sale_price']?>" class="form-control">
</div>

</div>

<div class="modal-footer">
<button name="update_product" class="btn btn-success">Update</button>
</div>

</form>

</div>
</div>
</div>

<?php } ?>
</table>

<?php if($total_pages>1){ ?>
<ul class="pagination">
<?php for($i=1;$i<=$total_pages;$i++){ ?>
<li class="page-item <?=($i==$page)?'active':''?>">
<a class="page-link" href="?page=<?=$i?>&per_page=<?=$per_page?>"><?=$i?></a>
</li>
<?php } ?>
</ul>
<?php } ?>
