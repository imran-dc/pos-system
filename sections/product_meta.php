<?php
/* ===========================
   CATEGORY ADD / UPDATE / DELETE
=========================== */
if(isset($_POST['add_category'])){
    $name = trim($_POST['category_name']);
    if($name != ""){
        $stmt = $imwp_conn->prepare("INSERT INTO imwp_categories(name) VALUES(?)");
        $stmt->bind_param("s",$name);
        $stmt->execute();
    }
}

if(isset($_GET['delete_category'])){
    $id = (int)$_GET['delete_category'];

    $check = $imwp_conn->prepare("SELECT COUNT(*) as total FROM imwp_products WHERE category_id=?");
    $check->bind_param("i",$id);
    $check->execute();
    $res = $check->get_result()->fetch_assoc();

    if($res['total'] == 0){
        $stmt = $imwp_conn->prepare("DELETE FROM imwp_categories WHERE id=?");
        $stmt->bind_param("i",$id);
        $stmt->execute();
    }
    header("Location: products.php");
    exit;
}

/* ===========================
   SUBCATEGORY
=========================== */
if(isset($_POST['add_subcategory'])){
    $name = trim($_POST['subcategory_name']);
    $cat  = (int)$_POST['parent_category'];

    if($name != ""){
        $stmt = $imwp_conn->prepare("INSERT INTO imwp_subcategories(name,category_id) VALUES(?,?)");
        $stmt->bind_param("si",$name,$cat);
        $stmt->execute();
    }
}

if(isset($_GET['delete_subcategory'])){
    $id = (int)$_GET['delete_subcategory'];

    $check = $imwp_conn->prepare("SELECT COUNT(*) as total FROM imwp_products WHERE subcategory_id=?");
    $check->bind_param("i",$id);
    $check->execute();
    $res = $check->get_result()->fetch_assoc();

    if($res['total'] == 0){
        $stmt = $imwp_conn->prepare("DELETE FROM imwp_subcategories WHERE id=?");
        $stmt->bind_param("i",$id);
        $stmt->execute();
    }
    header("Location: products.php");
    exit;
}

/* ===========================
   BRAND
=========================== */
if(isset($_POST['add_brand'])){
    $name = trim($_POST['brand_name']);
    if($name != ""){
        $stmt = $imwp_conn->prepare("INSERT INTO imwp_brands(name) VALUES(?)");
        $stmt->bind_param("s",$name);
        $stmt->execute();
    }
}

if(isset($_GET['delete_brand'])){
    $id = (int)$_GET['delete_brand'];

    $check = $imwp_conn->prepare("SELECT COUNT(*) as total FROM imwp_products WHERE brand_id=?");
    $check->bind_param("i",$id);
    $check->execute();
    $res = $check->get_result()->fetch_assoc();

    if($res['total'] == 0){
        $stmt = $imwp_conn->prepare("DELETE FROM imwp_brands WHERE id=?");
        $stmt->bind_param("i",$id);
        $stmt->execute();
    }
    header("Location: products.php");
    exit;
}
?>

<style>
.meta-card {
    border: none;
    border-radius: 12px;
    box-shadow: 0 6px 20px rgba(0,0,0,0.06);
}
.meta-header {
    background: linear-gradient(135deg, #1e293b, #334155);
    color: #fff;
    padding: 14px 18px;
    border-radius: 12px 12px 0 0;
    font-weight: 600;
}
.meta-body {
    padding: 18px;
    background: #f8fafc;
}
.meta-search {
    border-radius: 8px;
    font-size: 14px;
}
.meta-list {
    max-height: 240px;
    overflow-y: auto;
    font-size: 14px;
}
.meta-list li {
    padding: 6px 8px;
    border-radius: 6px;
    transition: 0.2s;
}
.meta-list li:hover {
    background: #e2e8f0;
}
.disabled-delete {
    pointer-events: none;
    opacity: 0.5;
}
</style>

<div class="card meta-card mb-4">
<div class="meta-header">Product Meta Management</div>
<div class="meta-body">

<div class="accordion" id="metaAccordion">

<!-- ================= CATEGORY ================= -->
<div class="accordion-item mb-3">
<h2 class="accordion-header">
<button class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#catCollapse">
Categories
</button>
</h2>
<div id="catCollapse" class="accordion-collapse collapse">
<div class="accordion-body">

<form method="POST" class="mb-3">
<input type="text" name="category_name" class="form-control mb-2" placeholder="New Category">
<button type="submit" name="add_category" class="btn btn-dark btn-sm">Add Category</button>
</form>

<input type="text" class="form-control meta-search mb-2" placeholder="Search..." onkeyup="filterMeta(this,'catList')">

<ul class="list-unstyled meta-list" id="catList">
<?php
$res=$imwp_conn->query("SELECT * FROM imwp_categories ORDER BY name ASC");
while($r=$res->fetch_assoc()){
$count=$imwp_conn->query("SELECT COUNT(*) as t FROM imwp_products WHERE category_id=".$r['id'])->fetch_assoc()['t'];

echo "<li class='d-flex justify-content-between align-items-center'>
{$r['name']}
<div>
<span class='badge bg-secondary me-2'>$count</span>";

if($count==0){
echo "<a href='?delete_category={$r['id']}' class='btn btn-sm btn-danger'>Delete</a>";
}else{
echo "<button class='btn btn-sm btn-danger disabled-delete'>Delete</button>";
}

echo "</div></li>";
}
?>
</ul>

</div>
</div>
</div>

<!-- ================= SUBCATEGORY ================= -->
<div class="accordion-item mb-3">
<h2 class="accordion-header">
<button class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#subCollapse">
Subcategories
</button>
</h2>
<div id="subCollapse" class="accordion-collapse collapse">
<div class="accordion-body">

<form method="POST" class="mb-3">
<input type="text" name="subcategory_name" class="form-control mb-2" placeholder="New Subcategory">
<select name="parent_category" class="form-select mb-2">
<?php
$res=$imwp_conn->query("SELECT * FROM imwp_categories ORDER BY name ASC");
while($r=$res->fetch_assoc())
echo "<option value='{$r['id']}'>{$r['name']}</option>";
?>
</select>
<button type="submit" name="add_subcategory" class="btn btn-dark btn-sm">Add Subcategory</button>
</form>

<input type="text" class="form-control meta-search mb-2" placeholder="Search..." onkeyup="filterMeta(this,'subList')">

<ul class="list-unstyled meta-list" id="subList">
<?php
$res=$imwp_conn->query("SELECT s.*,c.name as cname FROM imwp_subcategories s
LEFT JOIN imwp_categories c ON c.id=s.category_id ORDER BY s.name ASC");
while($r=$res->fetch_assoc()){
$count=$imwp_conn->query("SELECT COUNT(*) as t FROM imwp_products WHERE subcategory_id=".$r['id'])->fetch_assoc()['t'];

echo "<li class='d-flex justify-content-between align-items-center'>
{$r['name']} <small class='text-muted'>({$r['cname']})</small>
<div>
<span class='badge bg-secondary me-2'>$count</span>";

if($count==0){
echo "<a href='?delete_subcategory={$r['id']}' class='btn btn-sm btn-danger'>Delete</a>";
}else{
echo "<button class='btn btn-sm btn-danger disabled-delete'>Delete</button>";
}

echo "</div></li>";
}
?>
</ul>

</div>
</div>
</div>

<!-- ================= BRAND ================= -->
<div class="accordion-item">
<h2 class="accordion-header">
<button class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#brandCollapse">
Brands
</button>
</h2>
<div id="brandCollapse" class="accordion-collapse collapse">
<div class="accordion-body">

<form method="POST" class="mb-3">
<input type="text" name="brand_name" class="form-control mb-2" placeholder="New Brand">
<button type="submit" name="add_brand" class="btn btn-dark btn-sm">Add Brand</button>
</form>

<input type="text" class="form-control meta-search mb-2" placeholder="Search..." onkeyup="filterMeta(this,'brandList')">

<ul class="list-unstyled meta-list" id="brandList">
<?php
$res=$imwp_conn->query("SELECT * FROM imwp_brands ORDER BY name ASC");
while($r=$res->fetch_assoc()){
$count=$imwp_conn->query("SELECT COUNT(*) as t FROM imwp_products WHERE brand_id=".$r['id'])->fetch_assoc()['t'];

echo "<li class='d-flex justify-content-between align-items-center'>
{$r['name']}
<div>
<span class='badge bg-secondary me-2'>$count</span>";

if($count==0){
echo "<a href='?delete_brand={$r['id']}' class='btn btn-sm btn-danger'>Delete</a>";
}else{
echo "<button class='btn btn-sm btn-danger disabled-delete'>Delete</button>";
}

echo "</div></li>";
}
?>
</ul>

</div>
</div>
</div>

</div>
</div>
</div>

<script>
/* SEARCH */
function filterMeta(input, listId){
let filter = input.value.toLowerCase();
let li = document.getElementById(listId).getElementsByTagName("li");
for (let i = 0; i < li.length; i++) {
let txt = li[i].textContent || li[i].innerText;
li[i].style.display = txt.toLowerCase().includes(filter) ? "" : "none";
}
}

/* FIX AUTO CLOSE USING LOCAL STORAGE */
document.addEventListener("DOMContentLoaded", function(){

const sections = ["catCollapse","subCollapse","brandCollapse"];

sections.forEach(id=>{
let el = document.getElementById(id);

el.addEventListener("shown.bs.collapse", function(){
localStorage.setItem("metaOpen", id);
});

});

/* Restore open panel */
let saved = localStorage.getItem("metaOpen");
if(saved){
let target = document.getElementById(saved);
if(target){
new bootstrap.Collapse(target, {toggle:true});
}
}

});
</script>
