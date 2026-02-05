<?php
session_start();
require_once 'config/db.php';

if (!isset($_SESSION['imwp_user_id'])) {
    header("Location: login.php");
    exit;
}

function esc($v){
    global $imwp_conn;
    return $imwp_conn->real_escape_string(trim($v));
}

/* ============================
   HANDLE CATEGORY
============================ */

if(isset($_POST['add_category'])){
    $name = esc($_POST['cat_name']);
    if($name!=''){
        $imwp_conn->query("INSERT INTO imwp_categories(name) VALUES('$name')");
    }
}

if(isset($_POST['edit_category'])){
    $id = (int)$_POST['cat_id'];
    $name = esc($_POST['cat_name']);
    $imwp_conn->query("UPDATE imwp_categories SET name='$name' WHERE id=$id");
}

if(isset($_GET['delete_category'])){
    $id = (int)$_GET['delete_category'];
    $check = $imwp_conn->query("SELECT id FROM imwp_products WHERE category_id=$id")->num_rows;
    if($check==0){
        $imwp_conn->query("DELETE FROM imwp_categories WHERE id=$id");
    }
}

/* ============================
   HANDLE SUB CATEGORY
============================ */

if(isset($_POST['add_sub'])){
    $name = esc($_POST['sub_name']);
    $cat = (int)$_POST['sub_cat_id'];
    if($name!=''){
        $imwp_conn->query("INSERT INTO imwp_subcategories(name,category_id) VALUES('$name',$cat)");
    }
}

if(isset($_POST['edit_sub'])){
    $id = (int)$_POST['sub_id'];
    $name = esc($_POST['sub_name']);
    $imwp_conn->query("UPDATE imwp_subcategories SET name='$name' WHERE id=$id");
}

if(isset($_GET['delete_sub'])){
    $id = (int)$_GET['delete_sub'];
    $check = $imwp_conn->query("SELECT id FROM imwp_products WHERE subcategory_id=$id")->num_rows;
    if($check==0){
        $imwp_conn->query("DELETE FROM imwp_subcategories WHERE id=$id");
    }
}

/* ============================
   HANDLE BRAND
============================ */

if(isset($_POST['add_brand'])){
    $name = esc($_POST['brand_name']);
    if($name!=''){
        $imwp_conn->query("INSERT INTO imwp_brands(name) VALUES('$name')");
    }
}

if(isset($_POST['edit_brand'])){
    $id = (int)$_POST['brand_id'];
    $name = esc($_POST['brand_name']);
    $imwp_conn->query("UPDATE imwp_brands SET name='$name' WHERE id=$id");
}

if(isset($_GET['delete_brand'])){
    $id = (int)$_GET['delete_brand'];
    $check = $imwp_conn->query("SELECT id FROM imwp_products WHERE brand_id=$id")->num_rows;
    if($check==0){
        $imwp_conn->query("DELETE FROM imwp_brands WHERE id=$id");
    }
}

/* ============================
   HANDLE PRODUCT ADD
============================ */

if(isset($_POST['add_product'])){

    $code = esc($_POST['code']);
    $name = esc($_POST['name']);
    $cat = (int)$_POST['category_id'];
    $sub = (int)$_POST['subcategory_id'];
    $brand = (int)$_POST['brand_id'];
    $stock = (float)$_POST['stock'];
    $main_unit = esc($_POST['main_unit']);
    $sub_unit = esc($_POST['sub_unit']);
    $sub_size = (float)$_POST['sub_unit_size'];
    $purchase = (float)$_POST['purchase_price'];
    $sale = (float)$_POST['sale_price'];
    $alert = (float)$_POST['alert_level'];

    $imwp_conn->query("
        INSERT INTO imwp_products
        (code,name,category_id,subcategory_id,brand_id,stock,main_unit,sub_unit,sub_unit_size,purchase_price,sale_price,alert_level,status)
        VALUES
        ('$code','$name',$cat,$sub,$brand,$stock,'$main_unit','$sub_unit',$sub_size,$purchase,$sale,$alert,'active')
    ");
}

/* ============================
   HANDLE PRODUCT STATUS TOGGLE
============================ */

if(isset($_GET['toggle'])){
    $id=(int)$_GET['toggle'];
    $row=$imwp_conn->query("SELECT status FROM imwp_products WHERE id=$id")->fetch_assoc();
    $new = $row['status']=='active'?'inactive':'active';
    $imwp_conn->query("UPDATE imwp_products SET status='$new' WHERE id=$id");
}

/* ============================
   FILTER & PAGINATION
============================ */

$where="WHERE 1=1";

if(!empty($_GET['f_cat'])){
    $where.=" AND p.category_id=".(int)$_GET['f_cat'];
}
if(!empty($_GET['f_sub'])){
    $where.=" AND p.subcategory_id=".(int)$_GET['f_sub'];
}
if(!empty($_GET['f_brand'])){
    $where.=" AND p.brand_id=".(int)$_GET['f_brand'];
}

$perPage = isset($_GET['per_page']) ? max(1,(int)$_GET['per_page']) : 10;
$page = isset($_GET['page']) ? max(1,(int)$_GET['page']) : 1;
$offset = ($page-1)*$perPage;

$totalRow = $imwp_conn->query("
SELECT COUNT(*) as total
FROM imwp_products p
$where
")->fetch_assoc()['total'];

$totalPages = ceil($totalRow/$perPage);

$products = $imwp_conn->query("
SELECT p.*,c.name as cat, s.name as sub, b.name as brand
FROM imwp_products p
LEFT JOIN imwp_categories c ON p.category_id=c.id
LEFT JOIN imwp_subcategories s ON p.subcategory_id=s.id
LEFT JOIN imwp_brands b ON p.brand_id=b.id
$where
ORDER BY p.id DESC
LIMIT $offset,$perPage
");

include "includes/header.php";
?>

<div class="container-fluid">

<h4>PRODUCT MANAGEMENT</h4>

<hr>

<!-- ================= CATEGORY SECTION ================= -->

<h5>Categories</h5>
<form method="post" class="row g-2 mb-3">
<div class="col-md-4">
<input type="text" name="cat_name" class="form-control" placeholder="Category Name" required>
</div>
<div class="col-md-2">
<button name="add_category" class="btn btn-primary">Add</button>
</div>
</form>

<?php
$cats=$imwp_conn->query("SELECT * FROM imwp_categories");
while($c=$cats->fetch_assoc()):
?>
<div>
<?= $c['name'] ?>
<a href="?delete_category=<?= $c['id'] ?>" class="text-danger">Delete</a>
</div>
<?php endwhile; ?>

<hr>

<!-- ================= PRODUCT ADD ================= -->

<h5>Add Product</h5>
<form method="post" class="row g-2">

<div class="col-md-2">
<label>Code</label>
<input name="code" class="form-control" required>
</div>

<div class="col-md-3">
<label>Name</label>
<input name="name" class="form-control" required>
</div>

<div class="col-md-2">
<label>Category</label>
<select name="category_id" class="form-control">
<?php
$cats=$imwp_conn->query("SELECT * FROM imwp_categories");
while($c=$cats->fetch_assoc()):
?>
<option value="<?= $c['id'] ?>"><?= $c['name'] ?></option>
<?php endwhile; ?>
</select>
</div>

<div class="col-md-2">
<label>Stock</label>
<input type="number" step="0.01" name="stock" class="form-control">
</div>

<div class="col-md-2">
<label>Sale Price</label>
<input type="number" step="0.01" name="sale_price" class="form-control">
</div>

<div class="col-md-2 mt-4">
<button name="add_product" class="btn btn-success">Save</button>
</div>

</form>

<hr>

<!-- ================= FILTER ================= -->

<form class="row g-2 mb-3">

<div class="col-md-2">
<input type="number" name="per_page" value="<?= $perPage ?>" class="form-control">
</div>

<div class="col-md-2">
<button class="btn btn-secondary">Filter</button>
<a href="products.php" class="btn btn-warning">Reset</a>
</div>

<div class="col-md-3">
Total Products: <?= $totalRow ?>
</div>

</form>

<!-- ================= PRODUCT LIST ================= -->

<table class="table table-bordered table-striped">
<tr>
<th>ID</th>
<th>Name</th>
<th>Category</th>
<th>Stock</th>
<th>Price</th>
<th>Status</th>
<th>Action</th>
</tr>

<?php while($p=$products->fetch_assoc()): ?>
<tr>
<td><?= $p['id'] ?></td>
<td><?= $p['name'] ?></td>
<td><?= $p['cat'] ?></td>
<td><?= $p['stock'] ?></td>
<td><?= $p['sale_price'] ?></td>
<td><?= $p['status'] ?></td>
<td>
<a href="?toggle=<?= $p['id'] ?>" class="btn btn-sm btn-info">Toggle</a>
</td>
</tr>
<?php endwhile; ?>
</table>

<?php if($totalPages>1): ?>
<nav>
<ul class="pagination">
<?php for($i=1;$i<=$totalPages;$i++): ?>
<li class="page-item <?= $i==$page?'active':'' ?>">
<a class="page-link" href="?page=<?= $i ?>&per_page=<?= $perPage ?>"><?= $i ?></a>
</li>
<?php endfor; ?>
</ul>
</nav>
<?php endif; ?>

</div>

<?php include "includes/footer.php"; ?>
