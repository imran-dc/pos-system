<?php
if(session_status()===PHP_SESSION_NONE) session_start();

function setMsg($text,$type="danger"){
    $_SESSION['meta_msg']=['text'=>$text,'type'=>$type];
}
function showMsg(){
    if(isset($_SESSION['meta_msg'])){
        $m=$_SESSION['meta_msg'];
        echo "<div class='alert alert-{$m['type']} alert-dismissible fade show'>
                {$m['text']}
                <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
              </div>";
        unset($_SESSION['meta_msg']);
    }
}
function safePrepare($conn,$sql){
    $stmt=$conn->prepare($sql);
    if(!$stmt){ die("SQL Error: ".$conn->error); }
    return $stmt;
}

/* ================= CATEGORY SAVE ================= */
if(isset($_POST['save_category'])){
    $id=(int)$_POST['cat_id'];
    $name=trim($_POST['category_name']);

    if($name!=""){
        $check=safePrepare($imwp_conn,"SELECT id FROM imwp_categories WHERE name=? AND id!=?");
        $check->bind_param("si",$name,$id);
        $check->execute();
        $check->store_result();

        if($check->num_rows>0){
            setMsg("Category already exists.","warning");
        }else{
            if($id==0){
                $stmt=safePrepare($imwp_conn,"INSERT INTO imwp_categories(name) VALUES(?)");
                $stmt->bind_param("s",$name);
            }else{
                $stmt=safePrepare($imwp_conn,"UPDATE imwp_categories SET name=? WHERE id=?");
                $stmt->bind_param("si",$name,$id);
            }
            $stmt->execute();
            setMsg("Category saved successfully.","success");
        }
    }
    header("Location: products.php#catCollapse");
    exit;
}

/* ================= DELETE CATEGORY ================= */
if(isset($_GET['delete_category'])){
    $id=(int)$_GET['delete_category'];
    $check=safePrepare($imwp_conn,"SELECT COUNT(*) FROM imwp_products WHERE category_id=?");
    $check->bind_param("i",$id);
    $check->execute();
$check->store_result();
$check->bind_result($total);
$check->fetch();
$check->close();

    if($total==0){
        $stmt=safePrepare($imwp_conn,"DELETE FROM imwp_categories WHERE id=?");
        $stmt->bind_param("i",$id);
        $stmt->execute();
        setMsg("Category deleted.","success");
    }else{
        setMsg("Cannot delete. Category used in products.","danger");
    }
    header("Location: products.php#catCollapse");
    exit;
}

/* ================= SUBCATEGORY SAVE ================= */
if(isset($_POST['save_subcategory'])){
    $id=(int)$_POST['sub_id'];
    $name=trim($_POST['subcategory_name']);
    $cat=(int)$_POST['parent_category'];

    if($name!=""){
        $check=safePrepare($imwp_conn,"SELECT id FROM imwp_subcategories WHERE name=? AND id!=?");
        $check->bind_param("si",$name,$id);
        $check->execute();
        $check->store_result();

        if($check->num_rows>0){
            setMsg("Subcategory already exists.","warning");
        }else{
            if($id==0){
                $stmt=safePrepare($imwp_conn,"INSERT INTO imwp_subcategories(name,category_id) VALUES(?,?)");
                $stmt->bind_param("si",$name,$cat);
            }else{
                $stmt=safePrepare($imwp_conn,"UPDATE imwp_subcategories SET name=?,category_id=? WHERE id=?");
                $stmt->bind_param("sii",$name,$cat,$id);
            }
            $stmt->execute();
            setMsg("Subcategory saved.","success");
        }
    }
    header("Location: products.php#subCollapse");
    exit;
}

/* ================= DELETE SUBCATEGORY ================= */
if(isset($_GET['delete_subcategory'])){
    $id=(int)$_GET['delete_subcategory'];
    $check=safePrepare($imwp_conn,"SELECT COUNT(*) FROM imwp_products WHERE subcategory_id=?");
    $check->bind_param("i",$id);
    $check->execute();
$check->store_result();
$check->bind_result($total);
$check->fetch();
$check->close();


    if($total==0){
        $stmt=safePrepare($imwp_conn,"DELETE FROM imwp_subcategories WHERE id=?");
        $stmt->bind_param("i",$id);
        $stmt->execute();
        setMsg("Subcategory deleted.","success");
    }else{
        setMsg("Cannot delete. Subcategory used in products.","danger");
    }
    header("Location: products.php#subCollapse");
    exit;
}

/* ================= BRAND SAVE ================= */
if(isset($_POST['save_brand'])){
    $id=(int)$_POST['brand_id'];
    $name=trim($_POST['brand_name']);

    if($name!=""){
        $check=safePrepare($imwp_conn,"SELECT id FROM imwp_brands WHERE name=? AND id!=?");
        $check->bind_param("si",$name,$id);
        $check->execute();
        $check->store_result();

        if($check->num_rows>0){
            setMsg("Brand already exists.","warning");
        }else{
            if($id==0){
                $stmt=safePrepare($imwp_conn,"INSERT INTO imwp_brands(name) VALUES(?)");
                $stmt->bind_param("s",$name);
            }else{
                $stmt=safePrepare($imwp_conn,"UPDATE imwp_brands SET name=? WHERE id=?");
                $stmt->bind_param("si",$name,$id);
            }
            $stmt->execute();
            setMsg("Brand saved.","success");
        }
    }
    header("Location: products.php#brandCollapse");
    exit;
}

/* ================= DELETE BRAND ================= */
if(isset($_GET['delete_brand'])){
    $id = (int)$_GET['delete_brand'];

    $total = 0;

    $check = $imwp_conn->prepare("SELECT COUNT(*) FROM imwp_products WHERE brand_id=?");
    if($check){
        $check->bind_param("i",$id);
        $check->execute();
        $check->bind_result($total);
        $check->fetch();
        $check->close();   // VERY IMPORTANT
    }

    if($total == 0){
        $stmt = $imwp_conn->prepare("DELETE FROM imwp_brands WHERE id=?");
        if($stmt){
            $stmt->bind_param("i",$id);
            $stmt->execute();
            $stmt->close();   // ALSO IMPORTANT
        }
    }

    header("Location: products.php");
    exit;
}

?>

<style>
.meta-card{border:0;border-radius:12px;box-shadow:0 8px 25px rgba(0,0,0,0.06);}
.meta-header{background:#111827;color:#fff;padding:16px 20px;font-weight:600;border-radius:12px 12px 0 0;}
.meta-body{padding:20px;background:#f9fafb;}
.meta-row{display:flex;justify-content:space-between;align-items:center;background:#fff;border:1px solid #e5e7eb;padding:10px 14px;border-radius:8px;margin-bottom:6px;}
.meta-actions button,.meta-actions a{margin-left:6px;}
.meta-list{max-height:240px;overflow:auto;}
</style>

<div class="card meta-card mb-4">
<div class="meta-header">Product Meta Management</div>
<div class="meta-body">

<?php showMsg(); ?>

<div class="accordion" id="metaAccordion">

<!-- CATEGORY -->
<div class="accordion-item">
<h2 class="accordion-header">
<button class="accordion-button" data-bs-toggle="collapse" data-bs-target="#catCollapse">Categories</button>
</h2>
<div id="catCollapse" class="accordion-collapse collapse show">
<div class="accordion-body">

<form method="POST" class="d-flex mb-3">
<input type="hidden" name="cat_id" id="cat_id" value="0">
<input type="text" name="category_name" id="category_name" class="form-control me-2" placeholder="Category Name">
<button class="btn btn-dark" name="save_category" id="cat_btn">Add</button>
</form>

<input type="text" class="form-control mb-2" placeholder="Search..." onkeyup="filterMeta(this,'catList')">

<div id="catList" class="meta-list">
<?php
$res=$imwp_conn->query("SELECT * FROM imwp_categories ORDER BY name ASC");
while($r=$res->fetch_assoc()){
$count=$imwp_conn->query("SELECT COUNT(*) as t FROM imwp_products WHERE category_id=".$r['id'])->fetch_assoc()['t'];
echo "<div class='meta-row'>
<span>{$r['name']}</span>
<div class='meta-actions'>
<span class='badge bg-secondary'>$count</span>
<button class='btn btn-sm btn-primary' onclick=\"editCategory({$r['id']},'".addslashes($r['name'])."')\">Edit</button>";
echo $count==0
? "<a href='?delete_category={$r['id']}' class='btn btn-sm btn-danger'>Delete</a>"
: "<button class='btn btn-sm btn-secondary' disabled>Delete</button>";
echo "</div></div>";
}
?>
</div>

</div>
</div>
</div>

<!-- SUBCATEGORY -->
<div class="accordion-item">
<h2 class="accordion-header">
<button class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#subCollapse">Subcategories</button>
</h2>
<div id="subCollapse" class="accordion-collapse collapse">
<div class="accordion-body">

<form method="POST" class="row g-2 mb-3">
<input type="hidden" name="sub_id" id="sub_id" value="0">
<div class="col-md-5">
<input type="text" name="subcategory_name" id="subcategory_name" class="form-control" placeholder="Subcategory Name">
</div>
<div class="col-md-5">
<select name="parent_category" id="parent_category" class="form-select">
<?php
$cats=$imwp_conn->query("SELECT * FROM imwp_categories ORDER BY name ASC");
while($c=$cats->fetch_assoc()){
echo "<option value='{$c['id']}'>{$c['name']}</option>";
}
?>
</select>
</div>
<div class="col-md-2 d-grid">
<button class="btn btn-dark" name="save_subcategory" id="sub_btn">Add</button>
</div>
</form>

<input type="text" class="form-control mb-2" placeholder="Search..." onkeyup="filterMeta(this,'subList')">

<div id="subList" class="meta-list">
<?php
$res=$imwp_conn->query("SELECT s.*,c.name as cname FROM imwp_subcategories s LEFT JOIN imwp_categories c ON c.id=s.category_id ORDER BY s.name ASC");
while($r=$res->fetch_assoc()){
$count=$imwp_conn->query("SELECT COUNT(*) as t FROM imwp_products WHERE subcategory_id=".$r['id'])->fetch_assoc()['t'];
echo "<div class='meta-row'>
<span>{$r['name']} <small class='text-muted'>({$r['cname']})</small></span>
<div class='meta-actions'>
<span class='badge bg-secondary'>$count</span>
<button class='btn btn-sm btn-primary' onclick=\"editSub({$r['id']},'".addslashes($r['name'])."',{$r['category_id']})\">Edit</button>";
echo $count==0
? "<a href='?delete_subcategory={$r['id']}' class='btn btn-sm btn-danger'>Delete</a>"
: "<button class='btn btn-sm btn-secondary' disabled>Delete</button>";
echo "</div></div>";
}
?>
</div>

</div>
</div>
</div>

<!-- BRAND -->
<div class="accordion-item">
<h2 class="accordion-header">
<button class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#brandCollapse">Brands</button>
</h2>
<div id="brandCollapse" class="accordion-collapse collapse">
<div class="accordion-body">

<form method="POST" class="d-flex mb-3">
<input type="hidden" name="brand_id" id="brand_id" value="0">
<input type="text" name="brand_name" id="brand_name" class="form-control me-2" placeholder="Brand Name">
<button class="btn btn-dark" name="save_brand" id="brand_btn">Add</button>
</form>

<input type="text" class="form-control mb-2" placeholder="Search..." onkeyup="filterMeta(this,'brandList')">

<div id="brandList" class="meta-list">
<?php
$res=$imwp_conn->query("SELECT * FROM imwp_brands ORDER BY name ASC");
while($r=$res->fetch_assoc()){
$count=$imwp_conn->query("SELECT COUNT(*) as t FROM imwp_products WHERE brand_id=".$r['id'])->fetch_assoc()['t'];
echo "<div class='meta-row'>
<span>{$r['name']}</span>
<div class='meta-actions'>
<span class='badge bg-secondary'>$count</span>
<button class='btn btn-sm btn-primary' onclick=\"editBrand({$r['id']},'".addslashes($r['name'])."')\">Edit</button>";
echo $count==0
? "<a href='?delete_brand={$r['id']}' class='btn btn-sm btn-danger'>Delete</a>"
: "<button class='btn btn-sm btn-secondary' disabled>Delete</button>";
echo "</div></div>";
}
?>
</div>

</div>
</div>
</div>

</div>
</div>
</div>

<script>
function filterMeta(input,id){
let f=input.value.toLowerCase();
let rows=document.getElementById(id).children;
for(let i=0;i<rows.length;i++){
rows[i].style.display=rows[i].innerText.toLowerCase().includes(f)?'':'none';
}
}
function editCategory(id,name){
document.getElementById('cat_id').value=id;
document.getElementById('category_name').value=name;
document.getElementById('cat_btn').innerText="Update";
}
function editSub(id,name,cat){
document.getElementById('sub_id').value=id;
document.getElementById('subcategory_name').value=name;
document.getElementById('parent_category').value=cat;
document.getElementById('sub_btn').innerText="Update";
}
function editBrand(id,name){
document.getElementById('brand_id').value=id;
document.getElementById('brand_name').value=name;
document.getElementById('brand_btn').innerText="Update";
}
</script>
