<?php
$category   = $_GET['category'] ?? '';
$subcategory= $_GET['subcategory'] ?? '';
$brand      = $_GET['brand'] ?? '';
$status     = $_GET['status'] ?? '';
$search     = $_GET['search'] ?? '';
$per_page   = isset($_GET['per_page']) ? (int)$_GET['per_page'] : 10;
$page       = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset     = ($page - 1) * $per_page;

$where = " WHERE 1=1 ";

if ($category)     $where .= " AND p.category_id=".(int)$category;
if ($subcategory)  $where .= " AND p.subcategory_id=".(int)$subcategory;
if ($brand)        $where .= " AND p.brand_id=".(int)$brand;
if ($status)       $where .= " AND p.status='".$imwp_conn->real_escape_string($status)."'";
if ($search)       $where .= " AND p.name LIKE '%".$imwp_conn->real_escape_string($search)."%'";

$count_sql = "SELECT COUNT(*) as total FROM imwp_products p $where";
$total = $imwp_conn->query($count_sql)->fetch_assoc()['total'];
$total_pages = ceil($total / $per_page);

$sql = "SELECT p.*, 
        c.name as category, 
        s.name as subcategory, 
        b.name as brand
        FROM imwp_products p
        LEFT JOIN imwp_categories c ON p.category_id=c.id
        LEFT JOIN imwp_subcategories s ON p.subcategory_id=s.id
        LEFT JOIN imwp_brands b ON p.brand_id=b.id
        $where
        ORDER BY p.id DESC
        LIMIT $offset,$per_page";

$result = $imwp_conn->query($sql);
?>

<div class="card mb-3">
<div class="card-body">
<form method="GET" action="products.php" class="row g-2">

<div class="col-md-2">
<select name="category" class="form-select">
<option value="">Category</option>
<?php
$res=$imwp_conn->query("SELECT * FROM imwp_categories");
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
$res=$imwp_conn->query("SELECT * FROM imwp_subcategories");
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
$res=$imwp_conn->query("SELECT * FROM imwp_brands");
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
<input type="text" name="search" value="<?= htmlspecialchars($search) ?>" class="form-control" placeholder="Search">
</div>

<div class="col-md-1">
<input type="number" name="per_page" value="<?= $per_page ?>" class="form-control" min="1">
</div>

<div class="col-md-1 d-flex gap-1">
<button class="btn btn-primary btn-sm">Filter</button>
<a href="products.php" class="btn btn-secondary btn-sm">Reset</a>
</div>

</form>
</div>
</div>

<div class="card">
<div class="card-body table-responsive">

<table class="table table-bordered">
<thead>
<tr>
<th>ID</th>
<th>Name</th>
<th>Barcode</th>
<th>Category</th>
<th>Sub</th>
<th>Brand</th>
<th>Stock</th>
<th>Purchase</th>
<th>Sale</th>
<th>Status</th>
<th>Action</th>
</tr>
</thead>
<tbody>

<?php while($p=$result->fetch_assoc()){ 

// check sales usage
$used = 0;

$check = $imwp_conn->prepare("SELECT COUNT(*) as total FROM imwp_sales_items WHERE product_id=?");

if($check){
    $check->bind_param("i",$p['id']);
    $check->execute();
    $res = $check->get_result();
    if($res){
        $row = $res->fetch_assoc();
        $used = $row['total'];
    }
}

?>

<tr>
<td><?= $p['id'] ?></td>
<td><?= $p['name'] ?></td>
<td><?= $p['barcode'] ?></td>
<td><?= $p['category'] ?></td>
<td><?= $p['subcategory'] ?></td>
<td><?= $p['brand'] ?></td>
<td><?= $p['stock'] ?></td>
<td><?= $p['purchase_price'] ?></td>
<td><?= $p['sale_price'] ?></td>
<td><?= ucfirst($p['status']) ?></td>

<td>

<button class="btn btn-sm btn-primary"
data-bs-toggle="modal"
data-bs-target="#editModal"
onclick="fillEdit(
'<?= $p['id'] ?>',
'<?= htmlspecialchars($p['name'],ENT_QUOTES) ?>',
'<?= htmlspecialchars($p['barcode'],ENT_QUOTES) ?>',
'<?= $p['category_id'] ?>',
'<?= $p['subcategory_id'] ?>',
'<?= $p['brand_id'] ?>',
'<?= $p['stock'] ?>',
'<?= htmlspecialchars($p['main_unit'],ENT_QUOTES) ?>',
'<?= htmlspecialchars($p['sub_unit'],ENT_QUOTES) ?>',
'<?= $p['sub_unit_size'] ?>',
'<?= $p['purchase_price'] ?>',
'<?= $p['sale_price'] ?>',
'<?= $p['alert_level'] ?>',
'<?= $p['status'] ?>'
)">
Edit
</button>

<a href="products.php?toggle=<?= $p['id'] ?>"
class="btn btn-sm <?= $p['status']=='active'?'btn-success':'btn-secondary' ?>">
Toggle
</a>

<?php if($used == 0){ ?>
<a href="products.php?delete=<?= $p['id'] ?>"
class="btn btn-sm btn-danger"
onclick="return confirm('Delete this product permanently?')">
Delete
</a>
<?php } else { ?>
<button class="btn btn-sm btn-danger" disabled title="Cannot delete. Product has sales history">
Delete
</button>
<?php } ?>

</td>
</tr>

<?php } ?>

</tbody>
</table>
</div>
</div>
