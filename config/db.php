<?php
$imwp_conn = new mysqli("localhost", "root", "", "imwp_pos_system");

if ($imwp_conn->connect_error) {
    die("Connection failed: " . $imwp_conn->connect_error);
}
?>
