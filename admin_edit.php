<?php
require 'db.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    die('Доступ запрещен');
}

$id = $_GET['id'] ?? 0;
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch();

if (!$product) {
    die('Товар не найден');
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Редактирование товара</title>
    <<link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="header">
        <div class="logo">
            <h1>Редактирование товара</h1>
        </div>
        <div class="admin-menu">
            <a href="admin.php">Назад</a>
        </div>
    </div>

    <div class="form-container">
        <form method="post" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?= $product['id'] ?>">
            <input type="hidden" name="old_image" value="<?= $product['image'] ?>">
            
            <div class="form-group">
                <label>Название:</label>
                <input type="text" name="name" value="<?= htmlspecialchars($product['name']) ?>" required>
            </div>
            
            <div class="form-group">
                <label>Описание:</label>
                <textarea name="description" rows="3" required><?= htmlspecialchars($product['description']) ?></textarea>
            </div>
            
            <div class="form-group">
                <label>Цена (₽):</label>
                <input type="number" name="price" value="<?= $product['price'] ?>" required>
            </div>
            
            <?php if ($product['image'] != 'default.jpg'): ?>
                <div class="form-group">
                    <label>Текущее изображение:</label>
                    <img src="uploads/<?= $product['image'] ?>" width="200">
                </div>
            <?php endif; ?>
            
            <div class="form-group">
                <label>Новое изображение (оставьте пустым, если не меняете):</label>
                <input type="file" name="image">
            </div>
            
            <button type="submit" name="update_product" class="button">Сохранить</button>
        </form>
    </div>

    <div class="footer">
        <p>&copy; 2024 Sweet Paradise</p>
    </div>
</body>
</html>