<?php
require 'db.php';
if (isset($_POST['add'])) {
    $_SESSION['cart'][] = (int)$_POST['add'];
    echo count($_SESSION['cart']);
}
?>