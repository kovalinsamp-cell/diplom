<?php
require 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    die("Доступ запрещен.");
}

$order_id = $_GET['id'] ?? 0;
$stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ?");
$stmt->execute([$order_id]);
$order = $stmt->fetch();

if (!$order) {
    die("Заказ не найден");
}

$stmt = $pdo->prepare("
    SELECT oi.*, p.name, p.image 
    FROM order_items oi 
    JOIN products p ON oi.product_id = p.id 
    WHERE oi.order_id = ?
");
$stmt->execute([$order_id]);
$items = $stmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
    $stmt->execute([$_POST['status'], $order_id]);
    header("Location: admin_order_detail.php?id=" . $order_id);
    exit;
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Детали заказа #<?= $order_id ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .detail-container {
            max-width: 800px;
            margin: 7rem auto 2rem;
            background: #fff;
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        }
        .detail-header {
            border-bottom: 2px solid var(--gold);
            padding-bottom: 1rem;
            margin-bottom: 1.5rem;
        }
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
            margin-bottom: 2rem;
        }
        .info-item {
            background: #f9f9f9;
            padding: 1rem;
            border-radius: 10px;
        }
        .info-item label {
            font-weight: bold;
            color: var(--gold);
            display: block;
            margin-bottom: 5px;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin: 1rem 0;
        }
        .items-table th, .items-table td {
            padding: 10px;
            border-bottom: 1px solid #eee;
            text-align: left;
        }
        .total-price {
            font-size: 1.5rem;
            font-weight: bold;
            color: var(--gold);
            text-align: right;
            margin-top: 1rem;
        }
        .status-form {
            margin-top: 2rem;
            padding-top: 1rem;
            border-top: 1px solid #eee;
        }
        .back-link {
            display: inline-block;
            margin-top: 1rem;
            color: var(--gold);
            text-decoration: none;
        }
    </style>
</head>
<body class="admin-body">
    <nav class="navbar admin-nav">
        <div class="logo">Заказ #<?= $order_id ?></div>
        <div class="nav-links">
            <a href="admin_orders.php">← Назад к заказам</a>
        </div>
    </nav>

    <div class="detail-container">
        <div class="detail-header">
            <h2>Детали заказа #<?= $order_id ?></h2>
            <p>Дата: <?= date('d.m.Y H:i', strtotime($order['created_at'])) ?></p>
        </div>

        <div class="info-grid">
            <div class="info-item">
                <label>Клиент</label>
                <p><?= htmlspecialchars($order['name']) ?></p>
            </div>
            <div class="info-item">
                <label>Телефон</label>
                <p><?= htmlspecialchars($order['phone']) ?></p>
            </div>
            <div class="info-item">
                <label>Адрес</label>
                <p><?= htmlspecialchars($order['address']) ?></p>
            </div>
            <div class="info-item">
                <label>Статус</label>
                <p>
                    <?php
                    $statuses = [
                        'new' => 'Новый',
                        'processing' => 'Готовится',
                        'completed' => 'Выполнен',
                        'cancelled' => 'Отменен'
                    ];
                    echo $statuses[$order['status']];
                    ?>
                </p>
            </div>
        </div>

        <h3>Состав заказа</h3>
        <table class="items-table">
            <thead>
                <tr>
                    <th>Товар</th>
                    <th>Цена</th>
                    <th>Количество</th>
                    <th>Сумма</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($items as $item): ?>
                <tr>
                    <td><?= htmlspecialchars($item['name']) ?></td>
                    <td><?= number_format($item['price'], 0, ',', ' ') ?> ₽</td>
                    <td><?= $item['quantity'] ?> шт.</td>
                    <td><?= number_format($item['price'] * $item['quantity'], 0, ',', ' ') ?> ₽</td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="total-price">
            Итого: <?= number_format($order['total_price'], 0, ',', ' ') ?> ₽
        </div>

        <form method="POST" class="status-form">
            <label><strong>Изменить статус:</strong></label>
            <select name="status">
                <option value="new" <?= $order['status'] == 'new' ? 'selected' : '' ?>>Новый</option>
                <option value="processing" <?= $order['status'] == 'processing' ? 'selected' : '' ?>>Готовится</option>
                <option value="completed" <?= $order['status'] == 'completed' ? 'selected' : '' ?>>Выполнен</option>
                <option value="cancelled" <?= $order['status'] == 'cancelled' ? 'selected' : '' ?>>Отменен</option>
            </select>
            <button type="submit" name="update_status" class="btn-primary">Обновить статус</button>
        </form>

        <a href="admin_orders.php" class="back-link">← Вернуться к списку заказов</a>
    </div>
</body>
</html>