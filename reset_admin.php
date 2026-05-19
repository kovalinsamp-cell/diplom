<?php
require 'db.php';


$new_password = password_hash('admin123', PASSWORD_DEFAULT);


$stmt = $pdo->prepare("UPDATE users SET password = ? WHERE username = 'admin'");
$stmt->execute([$new_password]);

if ($stmt->rowCount() > 0) {
    echo "✅ Пароль для admin сброшен на: <b>admin123</b><br>";
    echo "<a href='login.php'>Перейти к входу</a>";
} else {
    echo "❌ Пользователь с именем 'admin' не найден.<br>";
    echo "Создайте админа: <br><br>";
    
    
    $stmt = $pdo->prepare("INSERT INTO users (fullname, username, email, phone, password, role) VALUES (?, ?, ?, ?, ?, 'admin')");
    $stmt->execute(['Администратор', 'admin', 'admin@site.ru', '+79991234567', $new_password]);
    
    echo "✅ Администратор создан!<br>";
    echo "Логин: <b>admin</b><br>";
    echo "Пароль: <b>admin123</b><br>";
    echo "<a href='login.php'>Перейти к входу</a>";
}
?>