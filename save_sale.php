<?php
require_once __DIR__ . '/config/db.php';

header("Content-Type: application/json");

$data = json_decode(file_get_contents("php://input"), true);

if(!$data || !isset($data['cart'])){
    echo json_encode(["status"=>"error","msg"=>"Invalid Data"]);
    exit;
}

$conn->begin_transaction();

try{

    $invoice_no = "INV".time();
    $extra_discount = floatval($data['extra_discount']);
    $subtotal = 0;

    foreach($data['cart'] as $item){
        $subtotal += ($item['price'] * $item['qty']) - $item['discount'];
    }

    $grand_total = $subtotal - $extra_discount;
    $paid = $grand_total;
    $change = 0;

    $conn->query("INSERT INTO sales 
        (invoice_no, subtotal, extra_discount, grand_total, paid, change_amount, sale_date)
        VALUES 
        ('$invoice_no','$subtotal','$extra_discount','$grand_total','$paid','$change',CURDATE())");

    $sale_id = $conn->insert_id;

    foreach($data['cart'] as $item){

        $product_id = intval($item['id']);
        $price = floatval($item['price']);
        $qty = intval($item['qty']);
        $discount = floatval($item['discount']);
        $total = ($price * $qty) - $discount;

        $conn->query("INSERT INTO sale_items 
            (sale_id, product_id, price, qty, discount, total)
            VALUES 
            ('$sale_id','$product_id','$price','$qty','$discount','$total')");

        // Reduce stock
        $conn->query("UPDATE products SET stock = stock - $qty WHERE id = $product_id");
    }

    $conn->commit();

    echo json_encode(["status"=>"success","sale_id"=>$sale_id]);

}catch(Exception $e){
    $conn->rollback();
    echo json_encode(["status"=>"error","msg"=>$e->getMessage()]);
}
