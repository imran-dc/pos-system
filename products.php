<?php
require_once "config/db.php";

/* ================= DELETE ================= */
if(isset($_GET['delete'])){
    $id = (int)$_GET['delete'];
    $used = 0;

    $check = $imwp_conn->prepare("SELECT COUNT(*) as total FROM imwp_sales_items WHERE product_id=?");
    if($check){
        $check->bind_param("i",$id);
        $check->execute();
        $res = $check->get_result();
        if($res){
            $row = $res->fetch_assoc();
            $used = $row['total'];
        }
    }

    if($used == 0){
        $stmt = $imwp_conn->prepare("DELETE FROM imwp_products WHERE id=?");
        if($stmt){
            $stmt->bind_param("i",$id);
            $stmt->execute();
        }
    }

    header("Location: products.php");
    exit;
}

/* ================= TOGGLE ================= */
if(isset($_GET['toggle'])){
    $id = (int)$_GET['toggle'];

    $stmt = $imwp_conn->prepare("
        UPDATE imwp_products
        SET status = CASE
            WHEN status='active' THEN 'inactive'
            ELSE 'active'
        END
        WHERE id=?
    ");

    if($stmt){
        $stmt->bind_param("i",$id);
        $stmt->execute();
    }

    header("Location: products.php");
    exit;
}

/* ================= UPDATE PRODUCT ================= */
if(isset($_POST['update_product'])){
    $stmt=$imwp_conn->prepare("
        UPDATE imwp_products SET
        name=?,barcode=?,category_id=?,subcategory_id=?,brand_id=?,stock=?,
        main_unit=?,sub_unit=?,sub_unit_size=?,purchase_price=?,sale_price=?,
        alert_level=?,status=? WHERE id=?");

    if($stmt){
        $stmt->bind_param("ssiiidssddddsi",
            $_POST['edit_name'],
            $_POST['edit_barcode'],
            $_POST['edit_category'],
            $_POST['edit_subcategory'],
            $_POST['edit_brand'],
            $_POST['edit_stock'],
            $_POST['edit_main_unit'],
            $_POST['edit_sub_unit'],
            $_POST['edit_sub_unit_size'],
            $_POST['edit_purchase_price'],
            $_POST['edit_sale_price'],
            $_POST['edit_alert_level'],
            $_POST['edit_status'],
            $_POST['edit_id']
        );
        $stmt->execute();
    }

    header("Location: products.php");
    exit;
}

include "includes/header.php";
?>

<div class="container-fluid py-4">

    <!-- TOP ROW -->
    <div class="row g-4 mb-4">

        <!-- LEFT: META -->
        <div class="col-lg-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-dark text-white">
                    <h6 class="mb-0">Category / Subcategory / Brand</h6>
                </div>
                <div class="card-body">
                    <?php include "sections/product_meta.php"; ?>
                </div>
            </div>
        </div>

        <!-- RIGHT: PRODUCT FORM -->
        <div class="col-lg-8">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0">Add New Product</h6>
                </div>
                <div class="card-body">
                    <?php include "sections/product_form.php"; ?>
                </div>
            </div>
        </div>

    </div>

    <!-- FULL WIDTH PRODUCT LIST -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">
                    <h6 class="mb-0">Product List</h6>
                </div>
                <div class="card-body">
                    <?php include "sections/product_list.php"; ?>
                </div>
            </div>
        </div>
    </div>

</div>

<?php
include "sections/product_popup.php";
include "includes/footer.php";
?>
