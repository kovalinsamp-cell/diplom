<?php
require 'db.php';
$products = $pdo->query("SELECT * FROM products ORDER BY id DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Наше Меню | Sweet Paradise</title>
    <link
        href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;1,400&family=Poppins:wght@300;400;500;600&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .catalog-page {
            padding: 8rem 5% 5rem;
            min-height: 80vh;
        }

        .page-header {
            text-align: center;
            margin-bottom: 3rem;
        }

        .page-header h1 {
            font-family: var(--font-head);
            font-size: 3rem;
            color: var(--dark);
        }

        .page-header p {
            color: #666;
            font-size: 1.1rem;
        }
    </style>
</head>

<body style="background-color: var(--cream);">
    <div class="overlay" id="overlay" onclick="toggleCart()"></div>
    <div class="cart-sidebar" id="cart-sidebar">
        <div class="cart-header">
            <h2>Ваш заказ</h2>
            <button class="close-cart" onclick="toggleCart()">&times;</button>
        </div>
        <div class="cart-body" id="cart-items"></div>
        <div class="cart-footer">
            <h3>Итого: <span id="cart-total">0</span> ₽</h3>
            <a href="checkout.php" class="btn-primary w-100"
                style="text-decoration:none; display:block; text-align:center;">Оформить заказ</a>
        </div>
    </div>

   <nav class="navbar">
    <div class="logo">Sweet <span>Paradise</span></div>
    <div class="nav-links">
        <a href="index.php">Главная</a>
        <a href="catalog.php" style="color: var(--gold); font-weight: 600;">Меню</a>
        <a href="index.php#about">О нас</a>
        <a href="index.php#contacts">Контакты</a>
        <a href="#" class="cart-btn" onclick="toggleCart()">
            Корзина <span id="cart-badge" class="badge">0</span>
        </a>

        <?php if (isset($_SESSION['user_id'])): ?>
            <a href="profile.php">Профиль</a>
            <?php if ($_SESSION['role'] === 'admin'): ?>
                <a href="admin.php">Админка</a>
            <?php endif; ?>
            <a href="logout.php" class="btn-outline">Выйти</a>
        <?php else: ?>
            <a href="login.php" class="btn-outline">Войти</a>
        <?php endif; ?>
    </div>
</nav>
    <main class="catalog-page">
        <div class="page-header reveal">
            <h1>Меню десертов</h1>
            <p>Выберите свои идеальные сладости. Мы доставим их свежими!</p>
        </div>

        <div class="grid">
            <?php foreach ($products as $p): ?>
                <?php
                $img = strpos($p['image'], 'http') === 0 ? $p['image'] : 'assets/uploads/' . $p['image'];
                ?>
                <div class="card reveal">
                    <div class="card-img" style="background-image: url('<?= htmlspecialchars($img) ?>');"></div>
                    <div class="card-body">
                        <h3><?= htmlspecialchars($p['name']) ?></h3>
                        <p><?= htmlspecialchars($p['description']) ?></p>
                        <div class="card-footer">
                            <span class="price"><?= number_format($p['price'], 0, ',', ' ') ?> ₽</span>
                            <button class="btn-cart" onclick="addToCart(<?= $p['id'] ?>)">В корзину</button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </main>

    <?php include 'footer.php'; ?>
    <div id="toast"></div>

    <script src="assets/js/main.js"></script>
</body>

</html>