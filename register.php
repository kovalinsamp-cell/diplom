<?php
require 'db.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $fullname = trim($_POST['fullname'] ?? '');
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $password = $_POST['password'];
    $password_confirm = $_POST['password_confirm'];

  
    if (empty($fullname)) {
        $error = "Введите ваше полное имя!";
    } elseif (empty($username)) {
        $error = "Введите логин!";
    } elseif (empty($email)) {
        $error = "Введите email!";
    } elseif (empty($phone)) {
        $error = "Введите номер телефона!";
    } elseif ($password !== $password_confirm) {
        $error = "Пароли не совпадают!";
    } elseif (mb_strlen($password) < 6) {
        $error = "Пароль должен содержать минимум 6 символов.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Введите корректный email!";
    } elseif (!preg_match('/^[\+\d\s\(\)\-]{10,20}$/', $phone)) {
        $error = "Введите корректный номер телефона (например: +7 999 123-45-67)";
    } else {
      
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $email]);
        
        if ($stmt->rowCount() > 0) {
            $error = "Пользователь с таким логином или email уже существует!";
        } else {
            
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (fullname, username, email, phone, password, role) VALUES (?, ?, ?, ?, ?, 'user')");
            
            if ($stmt->execute([$fullname, $username, $email, $phone, $hashed_password])) {
                $success = "Регистрация прошла успешно! Теперь вы можете войти.";
            } else {
                $error = "Произошла ошибка базы данных. Попробуйте позже.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Регистрация | Sweet Paradise</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .auth-wrapper { 
            min-height: 100vh; display: flex; justify-content: center; align-items: center; 
            background: var(--pink); padding: 20px; 
        }
        .auth-box { 
            background: #fff; padding: 3rem 2.5rem; border-radius: 20px; 
            box-shadow: 0 15px 30px rgba(0,0,0,0.1); width: 100%; max-width: 450px; text-align: center; 
        }
        .auth-box h2 { font-family: var(--font-head); margin-bottom: 2rem; color: var(--dark); }
        .auth-box input, 
        .auth-box select { 
            width: 100%; padding: 15px; margin-bottom: 15px; border: 1px solid #ddd; 
            border-radius: 30px; outline: none; transition: 0.3s; font-family: var(--font-body); 
        }
        .auth-box input:focus { border-color: var(--gold); box-shadow: 0 0 10px rgba(212, 175, 55, 0.2); }
        .alert-error { 
            color: #c62828; background: #ffebee; padding: 12px; 
            border-radius: 10px; margin-bottom: 20px; font-size: 0.9rem; 
        }
        .alert-success { 
            color: #2e7d32; background: #e8f5e9; padding: 12px; 
            border-radius: 10px; margin-bottom: 20px; font-size: 0.9rem; 
        }
        .w-100 { width: 100%; }
        .form-row {
            display: flex;
            gap: 10px;
        }
        .form-row input {
            flex: 1;
        }
        @media (max-width: 480px) {
            .auth-box { padding: 2rem 1.5rem; }
            .form-row { flex-direction: column; gap: 0; }
        }
    </style>
</head>
<body>
    <div class="auth-wrapper">
        <div class="auth-box">
            <h2>Создать аккаунт</h2>
            
            <?php if($error): ?>
                <div class="alert-error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            
            <?php if($success): ?>
                <div class="alert-success">
                    <?= htmlspecialchars($success) ?><br><br>
                    <a href="login.php" class="btn-primary" style="padding: 8px 20px; font-size:0.9rem;">Войти</a>
                </div>
            <?php else: ?>
                <form method="POST">
                    <input type="text" name="fullname" placeholder="Ваше полное имя" required value="<?= htmlspecialchars($_POST['fullname'] ?? '') ?>">
                    <input type="text" name="username" placeholder="Логин" required value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
                    <input type="email" name="email" placeholder="Email" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                    <input type="tel" name="phone" placeholder="Телефон (например: +7 999 123-45-67)" required value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>">
                    <input type="password" name="password" placeholder="Пароль (минимум 6 символов)" required minlength="6">
                    <input type="password" name="password_confirm" placeholder="Повторите пароль" required minlength="6">
                    
                    <button type="submit" class="btn-primary w-100" style="margin-top: 10px; padding: 15px; font-size: 1.1rem;">Зарегистрироваться</button>
                </form>
            <?php endif; ?>
            
            <p style="margin-top: 25px; font-size: 0.95rem;">
                Уже есть аккаунт? <a href="login.php" style="color: var(--gold); font-weight: bold; text-decoration: none;">Войти</a>
            </p>
            <p style="margin-top: 15px;">
                <a href="index.php" style="color: #888; text-decoration: none; font-size: 0.85rem;">&larr; Вернуться в магазин</a>
            </p>
        </div>
    </div>
    <?php include 'footer.php'; ?>
</body>
</html>