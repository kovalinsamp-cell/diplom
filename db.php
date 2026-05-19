<?php
session_start();
$host = 'localhost';           
$db   = 'sweet_paradise_pro';   
$user = 'root';                 
$pass = 'root';                     
try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass,[
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false
    ]);
} catch (PDOException $e) {
    die("<div style='font-family: sans-serif; padding: 20px; background: #ffebee; color: #c62828; border-radius: 5px; margin: 20px;'>
            <h3>Ошибка подключения к БД</h3>
            <p><b>Причина:</b> " . $e->getMessage() . "</p>
            <hr>
         </div>");
}
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}
?>