<?php
/* =========================
   FILTER VALUES
========================= */
$category     = isset($_GET['category']) ? (int)$_GET['category'] : 0;
$subcategory  = isset($_GET['subcategory']) ? (int)$_GET['subcategory'] : 0;
$brand        = isset($_GET['brand']) ? (int)$_GET['brand'] : 0;
$status       = $_GET['status'] ?? '';
$search       = $_GET['search'] ?? '';
$per_page     = isset($_GET['per_page']) ? (int)$_GET['per_page'] : 10;
$page         = isset($_GET['page']) ? (int)$_GET['page'] : 1;

if($per_page <= 0) $per_page = 10;
if($page <= 0) $page = 1;

$offset = ($page - 1) * $per_page;

/* =========================
   BUILD WHERE
========================= */
$where = [];
$params = [];
$types = "";

if ($category > 0) {
    $where[] = "p.category_id = ?";
    $params[] = $category;
    $types .= "i";
}

if ($subcategory > 0) {
    $where[] = "p.subcategory_id = ?";
    $params[] = $subcategory;
    $types .= "i";
}

if ($brand > 0) {
    $where[] = "p.brand_id = ?";
    $params[] = $brand;
    $types .= "i";
}

if ($status != '') {
    $where[] = "p.status = ?";
    $params[] = $status;
    $types .= "s";
}

if ($search != '') {
    $where[] = "p.name LIKE ?";
    $params[] = "%".$search."%";
    $types .= "s";
}

$where_sql = "";
if(count($where) > 0){
    $where_sql = " WHERE " . implode(" AND ", $where);
}

/* =========================
   COUNT QUERY
========================= */
$count_sql = "SELECT COUNT(*) as total FROM imwp_products p $where_sql";
$count_stmt = $imwp_conn->prepare($count_sql);

if($count_stmt){
    if(!empty($params)){
        $count_stmt->bind_param($types, ...$params);
    }
    $count_stmt->execute();
    $count_result = $count_stmt->get_result();
    $total = $count_result->fetch_assoc()['total'];
    $count_stmt->close();
}else{
    die("Count Query Error: ".$imwp_conn->error);
}

$total_pages = ceil($total / $per_page);

/* =========================
   MAIN QUERY
========================= */
$sql = "SELECT 
        p.*,
        c.name as category,
        s.name as subcategory,
        b.name as brand
        FROM imwp_products p
        LEFT JOIN imwp_categories c ON p.category_id=c.id
        LEFT JOIN imwp_subcategories s ON p.subcategory_id=s.id
        LEFT JOIN imwp_brands b ON p.brand_id=b.id
        $where_sql
        ORDER BY p.id DESC
        LIMIT ?, ?";

$stmt = $imwp_conn->prepare($sql);

if(!$stmt){
    die("Main Query Error: ".$imwp_conn->error);
}

$types_main = $types . "ii";
$params_main = $params;
$params_main[] = $offset;
$params_main[] = $per_page;

$stmt->bind_param($types_main, ...$params_main);
$stmt->execute();
$result = $stmt->get_result();
?>

<!-- =========================
     FILTER UI
========================= -->
<div class="card shadow-sm mb-3">
<div class="card-body">

<form method="GET" class="row g-2 align-items-center">

<div class="col-md-2">
<select name="category" class="form-select">
<option value="">Category</option>
<?php
$res=$imwp_conn->query("SELECT * FROM imwp_categories ORDER BY name ASC");
while($r=$res->fetch_assoc()){
$sel=($category==$r['id'])?"selected":"";
echo "<option value='{$r['id']}' $sel>{$r['name']}</option>";
}
?>
</select>
</div>

<div class="col-md-2">
<select name="subcategory" class="form-select">
<option value="">Subcategory</option>
<?php
$res=$imwp_conn->query("SELECT * FROM imwp_subcategories ORDER BY name ASC");
while($r=$res->fetch_assoc()){
$sel=($subcategory==$r['id'])?"selected":"";
echo "<option value='{$r['id']}' $sel>{$r['name']}</option>";
}
?>
</select>
</div>

<div class="col-md-2">
<select name="brand" class="form-select">
<option value="">Brand</option>
<?php
$res=$imwp_conn->query("SELECT * FROM imwp_brands ORDER BY name ASC");
while($r=$res->fetch_assoc()){
$sel=($brand==$r['id'])?"selected":"";
echo "<option value='{$r['id']}' $sel>{$r['name']}</option>";
}
?>
</select>
</div>

<div class="col-md-2">
<select name="status" class="form-select">
<option value="">Status</option>
<option value="active" <?= $status=='active'?'selected':'' ?>>Active</option>
<option value="inactive" <?= $status=='inactive'?'selected':'' ?>>Inactive</option>
</select>
</div>

<div class="col-md-2">
<input type="text" name="search"
value="<?= htmlspecialchars($search) ?>"
class="form-control"
placeholder="Search product">
</div>

<div class="col-md-1">
<input type="number" name="per_page"
value="<?= $per_page ?>"
class="form-control" min="1">
</div>

<div class="col-md-1 d-flex gap-1">
<button class="btn btn-primary btn-sm">Filter</button>
<a href="products.php" class="btn btn-secondary btn-sm">Reset</a>
</div>

</form>
</div>
</div>

<!-- =========================
     PRODUCT TABLE
========================= -->
<div class="card shadow-sm">
<div class="card-body table-responsive">

<table class="table table-hover align-middle">
<thead>
<tr>
<th>ID</th>
<th>Name</th>
<th>Category</th>
<th>Brand</th>
<th>Stock</th>
<th>Sale</th>
<th>Status</th>
<th width="200">Action</th>
</tr>
</thead>
<tbody>

<?php if($result->num_rows > 0): ?>
<?php while($p=$result->fetch_assoc()): ?>

<tr>
<td><?= $p['id'] ?></td>
<td><?= htmlspecialchars($p['name']) ?></td>
<td><?= $p['category'] ?></td>
<td><?= $p['brand'] ?></td>
<td><?= $p['stock'] ?></td>
<td><?= $p['sale_price'] ?></td>

<td>
<span class="badge <?= $p['status']=='active'?'bg-success':'bg-secondary' ?>">
<?= ucfirst($p['status']) ?>
</span>
</td>

<td>
<div class="d-flex gap-1 flex-nowrap">

<button class="btn btn-sm btn-primary"
data-bs-toggle="modal"
data-bs-target="#editModal">
Edit
</button>

<a href="products.php?toggle=<?= $p['id'] ?>"
class="btn btn-sm <?= $p['status']=='active'?'btn-dark':'btn-secondary' ?>">
Toggle
</a>

<a href="products.php?delete=<?= $p['id'] ?>"
class="btn btn-sm btn-danger"
onclick="return confirm('Delete this product?')">
Delete
</a>

</div>
</td>

</tr>

<?php endwhile; ?>
<?php else: ?>

<tr>
<td colspan="8" class="text-center text-muted py-4">
No products found
</td>
</tr>

<?php endif; ?>

</tbody>
</table>

</div>
</div>

<?php $stmt->close(); ?>

<!-- =========================
     PAGINATION
========================= -->
<?php if($total_pages > 1): ?>
<nav class="mt-3">
<ul class="pagination justify-content-end">
<?php for($i=1;$i<=$total_pages;$i++): ?>
<li class="page-item <?= $page==$i?'active':'' ?>">
<a class="page-link"
href="?page=<?= $i ?>&per_page=<?= $per_page ?>">
<?= $i ?>
</a>
</li>
<?php endfor; ?>
</ul>
</nav>
<?php endif; ?>
