<?php
require 'db.php';

if (empty($_SESSION['cart'])) {
    header("Location: index.php");
    exit;
}

$success = false;
$error = '';


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['apply_promo'])) {
    $promo = trim($_POST['promo_code']);
    if ($promo === 'SWEET10' || $promo === 'WELCOME') {
        $_SESSION['promo_code'] = $promo;
        header("Location: checkout.php");
        exit;
    } else {
        $error = "Неверный промокод!";
    }
}


if (isset($_GET['remove_promo'])) {
    unset($_SESSION['promo_code']);
    header("Location: checkout.php");
    exit;
}


$total = 0;
$ids = array_keys($_SESSION['cart']);
$ids = array_filter($ids, 'is_numeric');
if (!empty($ids)) {
    $ids_str = implode(',', $ids);
    $stmt = $pdo->query("SELECT id, price FROM products WHERE id IN ($ids_str)");
    $products = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
    foreach ($_SESSION['cart'] as $id => $qty) {
        if (isset($products[$id])) {
            $total += $products[$id] * $qty;
        }
    }
}


$discount = 0;
$promo_code = $_SESSION['promo_code'] ?? '';
if ($promo_code === 'SWEET10') {
    $discount = $total * 0.1;
} elseif ($promo_code === 'WELCOME') {
    $discount = 150;
}
$final_total = max(0, $total - $discount);


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['place_order'])) {
    $name = trim($_POST['name']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);
    $payment_method = $_POST['payment_method'];
    $delivery_method = $_POST['delivery_method'];
    $user_id = $_SESSION['user_id'] ?? null;
    
    $errors = [];
    if (empty($name)) $errors[] = "Введите имя";
    if (empty($phone)) $errors[] = "Введите телефон";
    if ($delivery_method === 'delivery' && empty($address)) $errors[] = "Введите адрес";
    
    if (empty($errors)) {
        $phone_clean = preg_replace('/[^0-9]/', '', $phone);
        if (strlen($phone_clean) == 11 && substr($phone_clean, 0, 1) == '8') {
            $phone_clean = '7' . substr($phone_clean, 1);
        }
        
        $stmt = $pdo->prepare("INSERT INTO orders (user_id, name, phone, address, total_price, payment_method, delivery_method) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$user_id, $name, $phone_clean, $address, $final_total, $payment_method, $delivery_method]);
        $order_id = $pdo->lastInsertId();
        
        $stmtItem = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
        foreach ($_SESSION['cart'] as $id => $qty) {
            if (isset($products[$id])) {
                $stmtItem->execute([$order_id, $id, $qty, $products[$id]]);
            }
        }
        
        unset($_SESSION['cart']);
        unset($_SESSION['promo_code']);
        $success = true;
    } else {
        $error = implode(', ', $errors);
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Оформление заказа | Sweet Paradise</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .checkout-wrapper {
            max-width: 1200px;
            margin: 8rem auto 2rem;
            padding: 0 20px;
        }
        .checkout-grid {
            display: grid;
            grid-template-columns: 1fr 1.2fr;
            gap: 2rem;
        }
        .order-summary {
            background: #fff;
            border-radius: 20px;
            padding: 1.5rem;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            position: sticky;
            top: 100px;
            height: fit-content;
        }
        .order-summary h3 {
            font-family: var(--font-head);
            margin-bottom: 1rem;
            border-bottom: 2px solid var(--gold);
            padding-bottom: 0.5rem;
        }
        .order-items {
            max-height: 400px;
            overflow-y: auto;
            margin-bottom: 1rem;
        }
        .order-item {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }
        .promo-box {
            display: flex;
            gap: 10px;
            margin: 15px 0;
        }
        .promo-box input {
            flex: 1;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 8px;
        }
        .totals {
            border-top: 1px solid #eee;
            padding-top: 15px;
        }
        .totals p {
            display: flex;
            justify-content: space-between;
            margin: 8px 0;
        }
        .totals .final {
            font-size: 1.3rem;
            font-weight: bold;
            color: var(--gold);
            border-top: 2px solid var(--gold);
            padding-top: 10px;
            margin-top: 5px;
        }
        .checkout-form {
            background: #fff;
            border-radius: 20px;
            padding: 1.5rem;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        }
        .checkout-form h3 {
            font-family: var(--font-head);
            margin-bottom: 1.5rem;
        }
        .form-group {
            margin-bottom: 1rem;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
        }
        .form-group input, .form-group select, .form-group textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 10px;
            font-family: var(--font-body);
        }
        .radio-group {
            display: flex;
            gap: 20px;
            margin-top: 5px;
        }
        .radio-group label {
            display: flex;
            align-items: center;
            gap: 5px;
            font-weight: normal;
            cursor: pointer;
        }
        .btn-order {
            width: 100%;
            padding: 15px;
            font-size: 1.2rem;
            margin-top: 1rem;
        }
        .success-page {
            text-align: center;
            padding: 3rem;
            background: #fff;
            border-radius: 20px;
            max-width: 500px;
            margin: 10rem auto;
        }
        .success-page h2 {
            color: var(--gold);
            font-size: 2rem;
            margin-bottom: 1rem;
        }
        @media (max-width: 768px) {
            .checkout-grid {
                grid-template-columns: 1fr;
            }
            .order-summary {
                position: static;
                order: 2;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="logo">Sweet <span>Paradise</span></div>
        <div style="display: flex; gap: 15px; justify-content: center; margin-top: 20px;">
    <a href="catalog.php" class="btn-primary">Смотреть меню</a>
    <a href="index.php" class="btn-outline">На главную</a>
</div>
    </nav>

    <?php if ($success): ?>
        <div class="success-page">
            <h2>✨ Заказ успешно оформлен!</h2>
            <p>Наш кондитер уже начал собирать ваши сладости.</p>
            <p>Мы свяжемся с вами в ближайшее время.</p>
            <br>
            <a href="index.php" class="btn-primary">На главную</a>
        </div>
    <?php else: ?>
        <div class="checkout-wrapper">
            <h1 style="font-family: var(--font-head); margin-bottom: 2rem;">Оформление заказа</h1>
            
            <?php if($error): ?>
                <div class="alert-error" style="color:red; background:#ffebee; padding:10px; border-radius:10px; margin-bottom:1rem;"><?= $error ?></div>
            <?php endif; ?>
            
            <div class="checkout-grid">
                <form method="POST" class="checkout-form">
                    <h3>Контактные данные</h3>
                    
                    <div class="form-group">
                        <label>Ваше имя *</label>
                        <input type="text" name="name" required value="<?= $_SESSION['username'] ?? '' ?>">
                    </div>
                    
                    <div class="form-group">
                        <label>Телефон *</label>
                        <input type="tel" name="phone" id="phone" required placeholder="+7(___)-___-__-__">
                    </div>
                    
                    <div class="form-group">
                        <label>Способ доставки *</label>
                        <div class="radio-group">
                            <label>
                                <input type="radio" name="delivery_method" value="delivery" checked>  Доставка
                            </label>
                            <label>
                                <input type="radio" name="delivery_method" value="pickup">  Самовывоз (бесплатно)
                            </label>
                        </div>
                    </div>
                    
                    <div class="form-group" id="address-group">
                        <label>Адрес доставки</label>
                        <textarea name="address" rows="2" placeholder="Город, улица, дом, квартира"></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label>Способ оплаты *</label>
                        <div class="radio-group">
                            <label>
                                <input type="radio" name="payment_method" value="card" checked>  Онлайн картой
                            </label>
                            <label>
                                <input type="radio" name="payment_method" value="cash">  Наличными при получении
                            </label>
                        </div>
                    </div>
                    
                    <button type="submit" name="place_order" class="btn-primary btn-order">Подтвердить заказ</button>
                </form>
                
                <div class="order-summary">
                    <h3>Ваш заказ</h3>
                    <div class="order-items">
                        <?php
                        if (!empty($ids)) {
                            $stmt = $pdo->query("SELECT * FROM products WHERE id IN ($ids_str)");
                            while ($p = $stmt->fetch()) {
                                $qty = $_SESSION['cart'][$p['id']];
                                echo "<div class='order-item'>
                                        <span>{$p['name']} × {$qty}</span>
                                        <span>" . number_format($p['price'] * $qty, 0, ',', ' ') . " ₽</span>
                                      </div>";
                            }
                        }
                        ?>
                    </div>
                    
                    <form method="POST" class="promo-box">
                        <input type="text" name="promo_code" placeholder="Промокод">
                        <button type="submit" name="apply_promo" class="btn-primary" style="padding: 10px 20px;">Применить</button>
                    </form>
                    
                    <?php if($promo_code): ?>
                        <div style="font-size:0.8rem; color:green; margin-bottom:10px;">
                            Промокод применён 
                            <a href="?remove_promo=1" style="color:red; text-decoration:none;">[×]</a>
                        </div>
                    <?php endif; ?>
                    
                    <div class="totals">
                        <p><span>Сумма:</span><span><?= number_format($total, 0, ',', ' ') ?> ₽</span></p>
                        <?php if($discount > 0): ?>
                            <p><span>Скидка:</span><span style="color:green;">-<?= number_format($discount, 0, ',', ' ') ?> ₽</span></p>
                        <?php endif; ?>
                        <p class="final"><span>Итого к оплате:</span><span><?= number_format($final_total, 0, ',', ' ') ?> ₽</span></p>
                    </div>
                </div>
            </div>
        </div>
        
        <script>
            
            document.querySelectorAll('input[name="delivery_method"]').forEach(radio => {
                radio.addEventListener('change', function() {
                    const addressGroup = document.getElementById('address-group');
                    if (this.value === 'delivery') {
                        addressGroup.style.display = 'block';
                    } else {
                        addressGroup.style.display = 'none';
                    }
                });
            });
        </script>
    <?php endif; ?>
    
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#phone').mask('+7(000)-000-00-00');
        });
    </script>
    <?php include 'footer.php'; ?>
</body>
</html>