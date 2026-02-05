<?php
require_once "config/db.php";
include "includes/header.php";

/* =====================================================
   TOGGLE ACTIVE / INACTIVE
===================================================== */
if(isset($_GET['toggle'])){
    $id = intval($_GET['toggle']);
    $imwp_conn->query("UPDATE imwp_products 
                       SET status = IF(status='active','inactive','active') 
                       WHERE id=$id");
    header("Location: products.php");
    exit;
}

/* =====================================================
   FETCH FILTERS
===================================================== */
$filter_category = $_GET['category'] ?? '';
$filter_subcategory = $_GET['subcategory'] ?? '';
$filter_brand = $_GET['brand'] ?? '';
$per_page = isset($_GET['per_page']) ? intval($_GET['per_page']) : 10;
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;

if($per_page <= 0) $per_page = 10;
if($page <= 0) $page = 1;

$where = " WHERE 1=1 ";

if($filter_category != '')
    $where .= " AND p.category_id = ".intval($filter_category);

if($filter_subcategory != '')
    $where .= " AND p.subcategory_id = ".intval($filter_subcategory);

if($filter_brand != '')
    $where .= " AND p.brand_id = ".intval($filter_brand);

/* =====================================================
   PAGINATION
===================================================== */
$countRes = $imwp_conn->query("SELECT COUNT(*) as total 
    FROM imwp_products p $where");
$totalRows = $countRes->fetch_assoc()['total'];

$totalPages = ceil($totalRows / $per_page);
$offset = ($page - 1) * $per_page;

/* =====================================================
   FETCH PRODUCTS
===================================================== */
$sql = "SELECT p.*, 
        c.name as category_name,
        s.name as subcategory_name,
        b.name as brand_name
        FROM imwp_products p
        LEFT JOIN imwp_categories c ON p.category_id = c.id
        LEFT JOIN imwp_subcategories s ON p.subcategory_id = s.id
        LEFT JOIN imwp_brands b ON p.brand_id = b.id
        $where
        ORDER BY p.id DESC
        LIMIT $offset, $per_page";

$products = $imwp_conn->query($sql);

/* =====================================================
   FETCH DROPDOWNS
===================================================== */
$categories = $imwp_conn->query("SELECT * FROM imwp_categories ORDER BY name");
$subcategories = $imwp_conn->query("SELECT * FROM imwp_subcategories ORDER BY name");
$brands = $imwp_conn->query("SELECT * FROM imwp_brands ORDER BY name");
?>

<h4 class="mb-4">Product Management</h4>

<!-- ================= FILTER SECTION ================= -->
<div class="card p-3 mb-4">
<form method="GET" class="row g-3">

<div class="col-md-2">
<select name="category" class="form-control">
<option value="">All Categories</option>
<?php while($c=$categories->fetch_assoc()): ?>
<option value="<?= $c['id'] ?>" <?= $filter_category==$c['id']?'selected':'' ?>>
<?= $c['name'] ?>
</option>
<?php endwhile; ?>
</select>
</div>

<div class="col-md-2">
<select name="subcategory" class="form-control">
<option value="">All Sub Categories</option>
<?php while($s=$subcategories->fetch_assoc()): ?>
<option value="<?= $s['id'] ?>" <?= $filter_subcategory==$s['id']?'selected':'' ?>>
<?= $s['name'] ?>
</option>
<?php endwhile; ?>
</select>
</div>

<div class="col-md-2">
<select name="brand" class="form-control">
<option value="">All Brands</option>
<?php while($b=$brands->fetch_assoc()): ?>
<option value="<?= $b['id'] ?>" <?= $filter_brand==$b['id']?'selected':'' ?>>
<?= $b['name'] ?>
</option>
<?php endwhile; ?>
</select>
</div>

<div class="col-md-2">
<input type="number" name="per_page" class="form-control" 
value="<?= $per_page ?>" min="1">
</div>

<div class="col-md-4">
<button class="btn btn-primary">Filter</button>
<a href="products.php" class="btn btn-secondary">Reset</a>
<span class="ms-3">Total: <?= $totalRows ?></span>
</div>

</form>
</div>

<!-- ================= PRODUCT TABLE ================= -->
<div class="card p-3">
<table class="table table-bordered table-hover">
<thead class="table-dark">
<tr>
<th>ID</th>
<th>Name</th>
<th>Category</th>
<th>Sub Category</th>
<th>Brand</th>
<th>Stock</th>
<th>Status</th>
<th>Action</th>
</tr>
</thead>
<tbody>

<?php while($row=$products->fetch_assoc()): ?>
<tr>
<td><?= $row['id'] ?></td>
<td><?= $row['name'] ?></td>
<td><?= $row['category_name'] ?></td>
<td><?= $row['subcategory_name'] ?></td>
<td><?= $row['brand_name'] ?></td>
<td><?= $row['stock'] ?></td>
<td>
<span class="badge bg-<?= $row['status']=='active'?'success':'danger' ?>">
<?= ucfirst($row['status']) ?>
</span>
</td>
<td>
<a href="?toggle=<?= $row['id'] ?>" 
class="btn btn-sm btn-warning">
Toggle
</a>

<button 
class="btn btn-sm btn-info"
data-bs-toggle="modal"
data-bs-target="#editModal<?= $row['id'] ?>">
Edit
</button>
</td>
</tr>

<!-- ================= EDIT MODAL ================= -->
<div class="modal fade" id="editModal<?= $row['id'] ?>">
<div class="modal-dialog">
<div class="modal-content">

<form method="POST" action="product_update.php">

<div class="modal-header">
<h5>Edit Product</h5>
<button type="button" class="btn-close" 
data-bs-dismiss="modal"></button>
</div>

<div class="modal-body">
<input type="hidden" name="id" value="<?= $row['id'] ?>">

<input type="text" name="name" 
class="form-control mb-2"
value="<?= $row['name'] ?>" required>

<select name="category" class="form-control mb-2">
<?php
$cat2 = $imwp_conn->query("SELECT * FROM imwp_categories");
while($c2=$cat2->fetch_assoc()):
?>
<option value="<?= $c2['id'] ?>" 
<?= $row['category_id']==$c2['id']?'selected':'' ?>>
<?= $c2['name'] ?>
</option>
<?php endwhile; ?>
</select>

<select name="subcategory" class="form-control mb-2">
<?php
$sub2 = $imwp_conn->query("SELECT * FROM imwp_subcategories");
while($s2=$sub2->fetch_assoc()):
?>
<option value="<?= $s2['id'] ?>" 
<?= $row['subcategory_id']==$s2['id']?'selected':'' ?>>
<?= $s2['name'] ?>
</option>
<?php endwhile; ?>
</select>

<select name="brand" class="form-control mb-2">
<?php
$br2 = $imwp_conn->query("SELECT * FROM imwp_brands");
while($b2=$br2->fetch_assoc()):
?>
<option value="<?= $b2['id'] ?>" 
<?= $row['brand_id']==$b2['id']?'selected':'' ?>>
<?= $b2['name'] ?>
</option>
<?php endwhile; ?>
</select>

<input type="number" step="0.01" name="sale_price" 
class="form-control mb-2"
value="<?= $row['sale_price'] ?>" required>

</div>

<div class="modal-footer">
<button class="btn btn-success">Update</button>
</div>

</form>

</div>
</div>
</div>
<!-- ================= END MODAL ================= -->

<?php endwhile; ?>

</tbody>
</table>
</div>

<!-- ================= PAGINATION ================= -->
<?php if($totalPages > 1): ?>
<nav class="mt-3">
<ul class="pagination">
<?php for($i=1;$i<=$totalPages;$i++): ?>
<li class="page-item <?= $i==$page?'active':'' ?>">
<a class="page-link" 
href="?page=<?= $i ?>&per_page=<?= $per_page ?>
&category=<?= $filter_category ?>
&subcategory=<?= $filter_subcategory ?>
&brand=<?= $filter_brand ?>">
<?= $i ?>
</a>
</li>
<?php endfor; ?>
</ul>
</nav>
<?php endif; ?>

<?php include "includes/footer.php"; ?>
