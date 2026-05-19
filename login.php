<?php
require 'db.php';
$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
    $stmt->execute([$_POST['username'], $_POST['username']]);
    $user = $stmt->fetch();

    if ($user && password_verify($_POST['password'], $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['fullname'] = $user['fullname'];
        header("Location: index.php");
        exit;
    } else {
        $error = "Неверные данные!";
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Вход | Sweet Paradise</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .auth-wrapper {
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background: var(--pink);
        }
        .auth-box {
            background: #fff;
            padding: 3rem;
            border-radius: 20px;
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }
        .auth-box h2 {
            font-family: var(--font-head);
            margin-bottom: 2rem;
        }
        .auth-box input {
            width: 100%;
            padding: 15px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 30px;
            outline: none;
            transition: 0.3s;
        }
        .auth-box input:focus {
            border-color: var(--gold);
        }
    </style>
</head>
<body>
    <nav class="navbar">
    <div class="logo">Sweet <span>Paradise</span></div>
    <div class="nav-links">
        <a href="index.php">Главная</a>
        <a href="catalog.php">Меню</a>
        <a href="index.php#about">О нас</a>
        <a href="index.php#contacts">Контакты</a>
        <a href="register.php" class="btn-outline">Регистрация</a>
    </div>
</nav>
    <div class="auth-wrapper">
        <div class="auth-box">
            <h2>Добро пожаловать</h2>
            <?php if ($error): ?>
                <p style="color:red; margin-bottom:15px;"><?= $error ?></p>
            <?php endif; ?>
            <form method="POST">
                <input type="text" name="username" placeholder="Логин или Email" required>
                <input type="password" name="password" placeholder="Пароль" required>
                <button type="submit" class="btn-primary" style="width: 100%;">Войти</button>
            </form>
            <p style="margin-top: 20px; font-size: 0.9rem;">Нет аккаунта? <a href="register.php" style="color: var(--gold);">Создать</a></p>
        </div>
    </div>
    <?php include 'footer.php'; ?>
</body>
</html>