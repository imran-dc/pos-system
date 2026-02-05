<?php
require_once __DIR__ . '/config/db.php';

$barcode = $_POST['barcode'];

$result = $conn->query("SELECT * FROM products WHERE barcode='$barcode' LIMIT 1");

if($result && $result->num_rows > 0){
    echo json_encode($result->fetch_assoc());
} else {
    echo json_encode([]);
}
