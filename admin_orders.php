<?php
require 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    die("Доступ запрещен.");
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
    $stmt->execute([$_POST['status'], $_POST['order_id']]);
    header("Location: admin_orders.php");
    exit;
}
$orders = $pdo->query("SELECT * FROM orders ORDER BY created_at DESC")->fetchAll();
?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <title>Управление заказами</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .order-card {
            background: #fff;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        }

        .order-header {
            display: flex;
            justify-content: space-between;
            border-bottom: 1px solid #eee;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }

        .order-details {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .status-form {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .status-select {
            padding: 8px;
            border-radius: 5px;
            border: 1px solid #ddd;
        }

        .badge-status {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: bold;
        }

        .status-new {
            background: #ffebee;
            color: #c62828;
        }

        .status-processing {
            background: #fff3e0;
            color: #ef6c00;
        }

        .status-completed {
            background: #e8f5e9;
            color: #2e7d32;
        }
    </style>
</head>

<body class="admin-body">
    <nav class="navbar admin-nav">
    <div class="logo">Admin <span>Orders</span></div>
    <div class="nav-links">
        <a href="admin.php">Товары</a>
        <a href="admin_orders.php" style="color: var(--gold);">Заказы</a>
        <a href="index.php" class="btn-outline">На сайт</a>
    </div>
</nav>

    <div class="admin-container">
        <h2>Последние заказы</h2>

        <?php foreach ($orders as $o): ?>
            <div class="order-card">
                <div class="order-header">
    <h3>
        <a href="admin_order_detail.php?id=<?= $o['id'] ?>" style="color: var(--gold); text-decoration: none;">
            Заказ #<?= $o['id'] ?>
        </a>
        <span style="color:#888; font-size:0.9rem;">(<?= $o['created_at'] ?>)</span>
    </h3>
    <span class="badge-status status-<?= $o['status'] ?>">
        <?= strtoupper($o['status']) ?>
    </span>
</div>
                <div class="order-details">
                    <div>
                        <p><b>Клиент:</b>
                            <?= htmlspecialchars($o['name']) ?>
                        </p>
                        <p><b>Телефон:</b>
                            <?= htmlspecialchars($o['phone']) ?>
                        </p>
                        <p><b>Адрес:</b>
                            <?= htmlspecialchars($o['address']) ?>
                        </p>
                        <p><b>Сумма:</b> <span class="price">
                                <?= number_format($o['total_price'], 0, ',', ' ') ?> ₽
                            </span></p>
                            <p><b>Оплата:</b> <?= $o['payment_method'] == 'card' ? 'Картой онлайн' : 'Наличными' ?></p>
<p><b>
    Доставка:</b> <?= $o['delivery_method'] == 'delivery' ? 'Курьером' : 'Самовывоз' ?></p>
                    </div>
                    <div>
                        <h4>Состав заказа:</h4>
                        <ul style="list-style: none; padding: 0;">
                            <?php
                            $stmt = $pdo->prepare("SELECT p.name, oi.quantity, oi.price FROM order_items oi JOIN products p ON oi.product_id = p.id WHERE oi.order_id = ?");
                            $stmt->execute([$o['id']]);
                            $items = $stmt->fetchAll();
                            foreach ($items as $item) {
                                echo "<li style='border-bottom: 1px dashed #eee; margin-bottom: 5px;'>{$item['name']} — {$item['quantity']} шт. (x {$item['price']} ₽)</li>";
                            }
                            ?>
                        </ul>

                        <form method="POST" class="status-form" style="margin-top: 15px;">
                            <input type="hidden" name="order_id" value="<?= $o['id'] ?>">
                            <select name="status" class="status-select">
                                <option value="new" <?= $o['status'] == 'new' ? 'selected' : '' ?>>Новый</option>
                                <option value="processing" <?= $o['status'] == 'processing' ? 'selected' : '' ?>>Готовится
                                    </option>
                                <option value="completed" <?= $o['status'] == 'completed' ? 'selected' : '' ?>>Выполнен
                                    </option>
                                <option value="cancelled" <?= $o['status'] == 'cancelled' ? 'selected' : '' ?>>Отменен</option>
                            </select>
                            <button type="submit" name="update_status" class="btn-primary"
                                style="padding: 8px 15px; font-size: 0.9rem;">Обновить</button>
                        </form>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>

        <?php if (empty($orders)): ?>
            <p>Заказов пока нет.</p>
        <?php endif; ?>
    </div>
    <?php include 'footer.php'; ?>
</body>

</html>