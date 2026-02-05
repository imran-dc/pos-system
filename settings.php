<?php
include 'includes/header.php';
require_once __DIR__ . '/config/db.php';

$result = $conn->query("SELECT * FROM settings LIMIT 1");
$settings = $result ? $result->fetch_assoc() : null;

if(isset($_POST['save_settings'])){

    $shop = $_POST['shop_name'];
    $address = $_POST['address'];
    $phone = $_POST['phone'];
    $printer = $_POST['printer_type'];
    $footer = $_POST['footer_msg'];

    $logoName = $settings['logo'] ?? '';

    if(!empty($_FILES['logo']['name'])){
        $logoName = time()."_".$_FILES['logo']['name'];
        move_uploaded_file($_FILES['logo']['tmp_name'], "uploads/".$logoName);
    }

    if($settings){
        $conn->query("UPDATE settings SET 
            shop_name='$shop',
            address='$address',
            phone='$phone',
            printer_type='$printer',
            footer_msg='$footer',
            logo='$logoName'
        WHERE id=".$settings['id']);
    } else {
        $conn->query("INSERT INTO settings 
        (shop_name,address,phone,printer_type,footer_msg,logo)
        VALUES 
        ('$shop','$address','$phone','$printer','$footer','$logoName')");
    }

    header("Location: settings.php");
    exit;
}
?>

<div class="container mt-4">
<h3>Settings</h3>

<form method="POST" enctype="multipart/form-data">

<div class="mb-3">
<label>Shop Name</label>
<input type="text" name="shop_name" class="form-control"
value="<?= $settings['shop_name'] ?? '' ?>">
</div>

<div class="mb-3">
<label>Address</label>
<textarea name="address" class="form-control"><?= $settings['address'] ?? '' ?></textarea>
</div>

<div class="mb-3">
<label>Phone</label>
<input type="text" name="phone" class="form-control"
value="<?= $settings['phone'] ?? '' ?>">
</div>

<div class="mb-3">
<label>Upload Logo</label>
<input type="file" name="logo" class="form-control">
<?php if(!empty($settings['logo'])){ ?>
<img src="uploads/<?= $settings['logo'] ?>" height="60">
<?php } ?>
</div>

<div class="mb-3">
<label>Footer Message</label>
<textarea name="footer_msg" class="form-control"><?= $settings['footer_msg'] ?? 'Thank you for shopping with us!' ?></textarea>
</div>

<div class="mb-3">
<label>Default Printer</label>
<select name="printer_type" class="form-control">
<option value="thermal" <?= ($settings['printer_type'] ?? '')=='thermal'?'selected':'' ?>>Thermal 80mm</option>
<option value="a4" <?= ($settings['printer_type'] ?? '')=='a4'?'selected':'' ?>>A4</option>
</select>
</div>

<button name="save_settings" class="btn btn-primary">Save Settings</button>

</form>
</div>

<?php include 'includes/footer.php'; ?>
