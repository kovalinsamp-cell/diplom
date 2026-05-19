<?php
require 'db.php';
$action = $_POST['action'] ?? '';
$id = (int) ($_POST['id'] ?? 0);
if (!isset($_SESSION['cart']))
    $_SESSION['cart'] = [];
if ($action === 'update_qty' && $id > 0) {
    $qty = max(1, (int)($_POST['qty'] ?? 1));
    $_SESSION['cart'][$id] = $qty;
} 
elseif ($action === 'add' && $id > 0) {
    $_SESSION['cart'][$id] = ($_SESSION['cart'][$id] ?? 0) + 1;
} 
elseif ($action === 'remove' && $id > 0) {
    unset($_SESSION['cart'][$id]);
}
$total = 0;
$count = 0;
$html = '';
if (empty($_SESSION['cart'])) {
    $html = '<div class="empty-cart">Ваша корзина пока пуста.<br>Самое время добавить десерт!</div>';
} else {
    $ids = array_keys($_SESSION['cart']);
    $ids = array_filter($ids, 'is_numeric');
    if (!empty($ids)) {
        $ids_str = implode(',', $ids);
        $stmt = $pdo->query("SELECT * FROM products WHERE id IN ($ids_str)");
        while ($p = $stmt->fetch()) {
            $qty = $_SESSION['cart'][$p['id']];
            $subtotal = $p['price'] * $qty;
            $total += $subtotal;
            $count += $qty;
            $image = htmlspecialchars($p['image']);
            $imagePath = strpos($image, 'http') === 0 ? $image : "assets/uploads/{$image}";
            $name = htmlspecialchars($p['name']);
            $html .= "
            <div class='cart-item' data-id='{$p['id']}'>
                <img src='{$imagePath}' alt='{$name}'>
                <div class='cart-item-info'>
                    <h4>{$name}</h4>S
                    <div class='cart-item-price'>{$p['price']} ₽</div>
                    <div class='cart-item-qty'>
                        <button class='qty-btn minus' data-id='{$p['id']}'>-</button>
                        <input type='number' value='{$qty}' min='1' class='qty-input' data-id='{$p['id']}'>
                        <button class='qty-btn plus' data-id='{$p['id']}'>+</button>
                    </div>
                </div>
                <div class='cart-item-subtotal'>
                    <span class='subtotal-price'>" . number_format($subtotal, 0, ',', ' ') . "</span> ₽
                </div>
                <button onclick='removeFromCart({$p['id']})' class='btn-remove'>&times;</button>
            </div>";
        }
    }
}
$discount = 0;
$promo_code = $_SESSION['promo_code'] ?? '';
$promo_message = '';
if ($promo_code === 'SWEET10') {
    $discount = $total * 0.1; 
    $promo_message = 'Промокод SWEET10 применён (-10%)';
} elseif ($promo_code === 'WELCOME') {
    $discount = 150; 
    $promo_message = 'Промокод WELCOME применён (-150 ₽)';
}
$final_total = max(0, $total - $discount);
header('Content-Type: application/json');
echo json_encode([
    'html' => $html,
    'total' => number_format($total, 0, ',', ' '),
    'discount' => number_format($discount, 0, ',', ' '),
    'final_total' => number_format($final_total, 0, ',', ' '),
    'count' => $count,
    'promo_message' => $promo_message
]);
?>