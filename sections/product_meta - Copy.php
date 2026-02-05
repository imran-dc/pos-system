<?php
/* =====================================================
   PRODUCT META MANAGEMENT
   ===================================================== */

/* ---------------- CATEGORY ---------------- */

if(isset($_POST['add_category'])){
    $name = trim($_POST['category_name']);
    if($name != ''){
        $stmt = $imwp_conn->prepare("INSERT INTO imwp_categories(name) VALUES(?)");
        $stmt->bind_param("s",$name);
        $stmt->execute();
    }
}


if(isset($_POST['update_category'])){
    $stmt=$imwp_conn->prepare("UPDATE imwp_categories SET name=? WHERE id=?");
    $stmt->bind_param("si", $_POST['edit_category_name'], $_POST['edit_category_id']);
    $stmt->execute();
}

if(isset($_GET['delete_category'])){
    $id=(int)$_GET['delete_category'];
    $check=$imwp_conn->query("SELECT COUNT(*) as t FROM imwp_products WHERE category_id=$id")->fetch_assoc();
    if($check['t']==0){
        $imwp_conn->query("DELETE FROM imwp_categories WHERE id=$id");
    }
}

/* ---------------- SUBCATEGORY ---------------- */

if(isset($_POST['add_subcategory'])){
    $stmt=$imwp_conn->prepare("INSERT INTO imwp_subcategories(name,category_id) VALUES(?,?)");
    $stmt->bind_param("si", $_POST['subcategory_name'], $_POST['parent_category']);
    $stmt->execute();
}

if(isset($_POST['update_subcategory'])){
    $stmt=$imwp_conn->prepare("UPDATE imwp_subcategories SET name=?,category_id=? WHERE id=?");
    $stmt->bind_param("sii", $_POST['edit_subcategory_name'], $_POST['edit_parent_category'], $_POST['edit_subcategory_id']);
    $stmt->execute();
}

if(isset($_GET['delete_subcategory'])){
    $id=(int)$_GET['delete_subcategory'];
    $check=$imwp_conn->query("SELECT COUNT(*) as t FROM imwp_products WHERE subcategory_id=$id")->fetch_assoc();
    if($check['t']==0){
        $imwp_conn->query("DELETE FROM imwp_subcategories WHERE id=$id");
    }
}

/* ---------------- BRAND ---------------- */

if(isset($_POST['add_brand'])){
    $stmt=$imwp_conn->prepare("INSERT INTO imwp_brands(name) VALUES(?)");
    $stmt->bind_param("s", $_POST['brand_name']);
    $stmt->execute();
}

if(isset($_POST['update_brand'])){
    $stmt=$imwp_conn->prepare("UPDATE imwp_brands SET name=? WHERE id=?");
    $stmt->bind_param("si", $_POST['edit_brand_name'], $_POST['edit_brand_id']);
    $stmt->execute();
}

if(isset($_GET['delete_brand'])){
    $id=(int)$_GET['delete_brand'];
    $check=$imwp_conn->query("SELECT COUNT(*) as t FROM imwp_products WHERE brand_id=$id")->fetch_assoc();
    if($check['t']==0){
        $imwp_conn->query("DELETE FROM imwp_brands WHERE id=$id");
    }
}
?>

<h4 class="mt-3">Product Metadata</h4>

<div class="accordion" id="metaAccordion">

<!-- ================= CATEGORY ================= -->

<div class="accordion-item">
<h2 class="accordion-header">
<button class="accordion-button" data-bs-toggle="collapse" data-bs-target="#catPanel">
Categories
</button>
</h2>
<div id="catPanel" class="accordion-collapse collapse show">
<div class="accordion-body">

<input type="text" id="catSearch" class="form-control mb-2" placeholder="Search Category">

<form method="post" class="mb-3">
<input type="text" name="category_name" class="form-control mb-2" placeholder="New Category">
<button name="add_category" class="btn btn-primary w-100">Add</button>
</form>

<?php
$cats=$imwp_conn->query("
SELECT c.*, (SELECT COUNT(*) FROM imwp_products p WHERE p.category_id=c.id) as total
FROM imwp_categories c ORDER BY c.id DESC");

while($c=$cats->fetch_assoc()):
$disabled=$c['total']>0?'disabled':'';
?>

<div class="border p-2 mb-2 meta-cat">
<strong><?=$c['name']?></strong>
<small class="text-muted">(<?=$c['total']?> products)</small>

<form method="post" class="d-flex mt-2 gap-1">
<input type="hidden" name="edit_category_id" value="<?=$c['id']?>">
<input type="text" name="edit_category_name" value="<?=$c['name']?>" class="form-control">
<button name="update_category" class="btn btn-sm btn-success">Update</button>
<a href="?delete_category=<?=$c['id']?>" class="btn btn-sm btn-danger <?=$disabled?>">Delete</a>
</form>
</div>

<?php endwhile; ?>

</div></div></div>

<!-- ================= SUBCATEGORY ================= -->

<div class="accordion-item">
<h2 class="accordion-header">
<button class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#subPanel">
Sub Categories
</button>
</h2>
<div id="subPanel" class="accordion-collapse collapse">
<div class="accordion-body">

<input type="text" id="subSearch" class="form-control mb-2" placeholder="Search SubCategory">

<form method="post" class="mb-3">
<input type="text" name="subcategory_name" class="form-control mb-2" placeholder="New SubCategory" required>

<select name="parent_category" class="form-control mb-2" required>
<option value="">Select Parent Category</option>
<?php
$cats=$imwp_conn->query("SELECT * FROM imwp_categories");
while($c=$cats->fetch_assoc()){
echo "<option value='{$c['id']}'>{$c['name']}</option>";
}
?>
</select>

<button name="add_subcategory" class="btn btn-primary w-100">Add</button>
</form>

<?php
$subs=$imwp_conn->query("
SELECT s.*,c.name as parent,
(SELECT COUNT(*) FROM imwp_products p WHERE p.subcategory_id=s.id) as total
FROM imwp_subcategories s
LEFT JOIN imwp_categories c ON c.id=s.category_id
ORDER BY s.id DESC");

while($s=$subs->fetch_assoc()):
$disabled=$s['total']>0?'disabled':'';
?>

<div class="border p-2 mb-2 meta-sub">
<strong><?=$s['name']?></strong>
<small class="text-muted">(<?=$s['parent']?> | <?=$s['total']?>)</small>

<form method="post" class="d-flex mt-2 gap-1">
<input type="hidden" name="edit_subcategory_id" value="<?=$s['id']?>">
<input type="text" name="edit_subcategory_name" value="<?=$s['name']?>" class="form-control">

<select name="edit_parent_category" class="form-control">
<?php
$cats=$imwp_conn->query("SELECT * FROM imwp_categories");
while($c=$cats->fetch_assoc()){
$sel=$c['id']==$s['category_id']?'selected':'';
echo "<option value='{$c['id']}' $sel>{$c['name']}</option>";
}
?>
</select>

<button name="update_subcategory" class="btn btn-sm btn-success">Update</button>
<a href="?delete_subcategory=<?=$s['id']?>" class="btn btn-sm btn-danger <?=$disabled?>">Delete</a>
</form>
</div>

<?php endwhile; ?>

</div></div></div>

<!-- ================= BRAND ================= -->

<div class="accordion-item">
<h2 class="accordion-header">
<button class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#brandPanel">
Brands
</button>
</h2>
<div id="brandPanel" class="accordion-collapse collapse">
<div class="accordion-body">

<input type="text" id="brandSearch" class="form-control mb-2" placeholder="Search Brand">

<form method="post" class="mb-3">
<input type="text" name="brand_name" class="form-control mb-2" placeholder="New Brand" required>
<button name="add_brand" class="btn btn-primary w-100">Add</button>
</form>

<?php
$brands=$imwp_conn->query("
SELECT b.*, (SELECT COUNT(*) FROM imwp_products p WHERE p.brand_id=b.id) as total
FROM imwp_brands b ORDER BY b.id DESC");

while($b=$brands->fetch_assoc()):
$disabled=$b['total']>0?'disabled':'';
?>

<div class="border p-2 mb-2 meta-brand">
<strong><?=$b['name']?></strong>
<small class="text-muted">(<?=$b['total']?>)</small>

<form method="post" class="d-flex mt-2 gap-1">
<input type="hidden" name="edit_brand_id" value="<?=$b['id']?>">
<input type="text" name="edit_brand_name" value="<?=$b['name']?>" class="form-control">
<button name="update_brand" class="btn btn-sm btn-success">Update</button>
<a href="?delete_brand=<?=$b['id']?>" class="btn btn-sm btn-danger <?=$disabled?>">Delete</a>
</form>
</div>

<?php endwhile; ?>

</div></div></div>

</div>

<script>
function liveSearch(inputId,className){
document.getElementById(inputId).addEventListener("keyup",function(){
let val=this.value.toLowerCase();
document.querySelectorAll("."+className).forEach(function(row){
row.style.display=row.innerText.toLowerCase().includes(val)?"block":"none";
});
});
}
liveSearch("catSearch","meta-cat");
liveSearch("subSearch","meta-sub");
liveSearch("brandSearch","meta-brand");
</script>
