<?php
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$orders = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
$orders->execute([$user_id]);
$my_orders = $orders->fetchAll();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Личный кабинет | Sweet Paradise</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>

html, body {
    height: 100%;
    margin: 0;
    padding: 0;
}

body {
    display: flex;
    flex-direction: column;
    min-height: 100vh;
}

.profile-container {
    flex: 1;
}
@media (max-width: 768px) {
    .footer-container {
        grid-template-columns: 1fr;
        text-align: center;
    }
}
        .profile-container {
            max-width: 800px;
            margin: 8rem auto 2rem;
            padding: 2rem;
            background: #fff;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        }
        .order-history table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }
        .order-history th,
        .order-history td {
            padding: 15px;
            border-bottom: 1px solid #eee;
            text-align: left;
        }
        .status-badge {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: bold;
        }
        .status-new { background: #ffebee; color: #c62828; }
        .status-processing { background: #fff3e0; color: #ef6c00; }
        .status-completed { background: #e8f5e9; color: #2e7d32; }
        .status-cancelled { background: #fce4ec; color: #880e4f; }
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
        <a href="#" class="cart-btn" onclick="toggleCart()">🛒 Корзина <span id="cart-badge" class="badge">0</span></a>
        <a href="logout.php" class="btn-outline">Выйти</a>
    </div>
</nav>

<div class="profile-container">
    <h2>Добро пожаловать, <?= htmlspecialchars($_SESSION['fullname'] ?? $_SESSION['username'] ?? 'Дорогой гость') ?>! </h2>
    <p>Здесь хранится история ваших сладких покупок.</p>

    <div class="order-history">
        <h3>Мои заказы</h3>
        <?php if (count($my_orders) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>№ Заказа</th>
                        <th>Дата</th>
                        <th>Сумма</th>
                        <th>Статус</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($my_orders as $o): ?>
                        <tr>
                            <td>#<?= $o['id'] ?></td>
                            <td><?= date('d.m.Y H:i', strtotime($o['created_at'])) ?></td>
                            <td class="price"><?= number_format($o['total_price'], 0, ',', ' ') ?> ₽</td>
                            <td>
                                <span class="status-badge status-<?= $o['status'] ?>">
                                    <?php
                                    $statuses = [
                                        'new' => 'Новый',
                                        'processing' => 'Готовится',
                                        'completed' => 'Выполнен',
                                        'cancelled' => 'Отменен'
                                    ];
                                    echo $statuses[$o['status']];
                                    ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p style="color:#888; margin-top: 1rem;">Вы еще ничего не заказывали. <a href="catalog.php" style="color:var(--gold);">Перейти в меню</a></p>
        <?php endif; ?>
    </div>
</div>

<?php include 'footer.php'; ?>

<script src="assets/js/main.js"></script>
</body>
</html>