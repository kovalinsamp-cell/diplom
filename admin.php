<?php
require 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    die("Доступ запрещен.");
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $desc = $_POST['description'];
    $price = $_POST['price'];
    $id = $_POST['id'] ?? null;
    $imageName = $_POST['old_image'] ?? 'default.jpg';

    
    if (!empty($_FILES['image']['name'])) {
        $imageName = time() . '_' . basename($_FILES['image']['name']);
        move_uploaded_file($_FILES['image']['tmp_name'], "assets/uploads/" . $imageName);
    }

    if (isset($_POST['create'])) {
        $stmt = $pdo->prepare("INSERT INTO products (name, description, price, image) VALUES (?, ?, ?, ?)");
        $stmt->execute([$name, $desc, $price, $imageName]);
    } elseif (isset($_POST['update'])) {
        $stmt = $pdo->prepare("UPDATE products SET name=?, description=?, price=?, image=? WHERE id=?");
        $stmt->execute([$name, $desc, $price, $imageName, $id]);
    }
    header("Location: admin.php");
    exit;
}


if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM products WHERE id=?");
    $stmt->execute([$_GET['delete']]);
    header("Location: admin.php");
    exit;
}

$products = $pdo->query("SELECT * FROM products ORDER BY id DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Панель управления</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<nav class="navbar admin-nav">
    <div class="logo">Admin <span>Panel</span></div>
    <div class="nav-links">
    <a href="admin.php" style="color: var(--gold);">Товары</a>
    <a href="admin_orders.php">Заказы</a>
    <a href="index.php" class="btn-outline">На сайт</a>
</div>
</nav>

    <div class="admin-container">
        <div class="admin-card">
            <h2>Добавить десерт</h2>
            <form method="POST" enctype="multipart/form-data" class="form-grid">
                <input type="text" name="name" placeholder="Название торта" required>
                <input type="number" name="price" placeholder="Цена (₽)" required>
                <textarea name="description" placeholder="Описание..." required></textarea>
                <input type="file" name="image" accept="image/*">
                <button type="submit" name="create" class="btn-primary">Опубликовать</button>
            </form>
        </div>

        <h2 style="margin-top: 2rem;">Список товаров</h2>
        <div class="admin-list">
            <?php foreach($products as $p): ?>
                <form method="POST" enctype="multipart/form-data" class="admin-item">
                    <input type="hidden" name="id" value="<?= $p['id'] ?>">
                    <input type="hidden" name="old_image" value="<?= $p['image'] ?>">
                    
                    <img src="assets/uploads/<?= $p['image'] ?>" alt="img" class="admin-thumb">
                    <div class="admin-inputs">
                        <input type="text" name="name" value="<?= htmlspecialchars($p['name']) ?>">
                        <input type="number" name="price" value="<?= $p['price'] ?>">
                        <input type="file" name="image" accept="image/*">
                    </div>
                    <textarea name="description"><?= htmlspecialchars($p['description']) ?></textarea>
                    
                    <div class="admin-actions">
                        <button type="submit" name="update" class="btn-edit">Обновить</button>
                        <a href="admin.php?delete=<?= $p['id'] ?>" class="btn-delete" onclick="return confirm('Точно удалить?')">Удалить</a>
                    </div>
                </form>
            <?php endforeach; ?>
        </div>
    </div>
    <?php include 'footer.php'; ?>
</body>
</html>