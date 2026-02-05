<?php
require_once "config/db.php";

$id = intval($_POST['id']);
$name = $_POST['name'];
$category = intval($_POST['category']);
$subcategory = intval($_POST['subcategory']);
$brand = intval($_POST['brand']);
$sale_price = floatval($_POST['sale_price']);

$stmt = $imwp_conn->prepare("
UPDATE imwp_products 
SET name=?, category_id=?, subcategory_id=?, brand_id=?, sale_price=? 
WHERE id=?");

$stmt->bind_param("siiidi",
$name,$category,$subcategory,$brand,$sale_price,$id);

$stmt->execute();

header("Location: products.php");
exit;
