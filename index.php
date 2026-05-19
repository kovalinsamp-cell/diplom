<?php
require 'db.php';
?>
<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sweet Paradise | Элитная кондитерская</title>
    <link
        href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;1,400&family=Poppins:wght@300;400;500;600&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>

    
    <div class="overlay" id="overlay" onclick="toggleCart()"></div>
    <div class="cart-sidebar" id="cart-sidebar">
        <div class="cart-header">
            <h2>Ваш заказ</h2>
            <button class="close-cart" onclick="toggleCart()">&times;</button>
        </div>
        <div class="cart-body" id="cart-items">
            <div class="spinner-small"></div>
        </div>
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
            <a href="#about">О нас</a>
            <a href="#contacts">Контакты</a>
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

    
    <header class="hero">
        <div class="hero-content reveal">
            <h1>Искусство в каждом кусочке</h1>
            <p>Авторские десерты из натуральных ингредиентов, созданные с любовью для ваших лучших моментов.</p>
            <a href="catalog.php" class="btn-primary" style="font-size: 1.2rem; padding: 15px 40px;">Смотреть меню</a>
            
        </div>
    </header>

  
    <section id="about" class="about" style="padding-top: 120px; margin-top: 0;">
        <h2 class="section-title reveal">Искусство кондитера</h2>
        <div class="about-container reveal">
            <div class="about-text">
                <p>Добро пожаловать в <b>Sweet Paradise</b> — уютную кондитерскую, где каждый десерт рассказывает свою историю. Мы верим, что сладкое — это не просто еда, а маленький праздник, который случается здесь и сейчас.</p>
                <p>Наш шеф-кондитер учился у лучших мастеров Парижа и привёз с собой не только рецепты, но и философию: <b>только натуральные ингредиенты, только ручная работа, только с любовью</b>. Никаких компромиссов.</p>
                <ul class="features">
                    <li><b>Премиум ингредиенты:</b> Бельгийский шоколад, фермерские сливки, свежие ягоды и фрукты — мы не экономим на качестве, потому что вы достойны лучшего.</li>
                    <li><b>Ручная работа:</b> Каждый торт, пирожное и десерт создаётся руками наших кондитеров. Никаких конвейеров — только душа и мастерство.</li>
                    <li><b>Готовим как для семьи:</b> Представьте, что ваша бабушка была шеф-кондитером в Париже. Вот так мы и готовим — уютно, честно и очень вкусно.</li>
                </ul>
            </div>
            <div class="about-image" style="background-image: url('assets/uploads/cake.jpg'); background-size: cover; background-position: center; background-repeat: no-repeat;"></div>
        </div>
    </section>

   
    <section id="contacts" class="contacts">
        <h2 class="section-title reveal">Ждем вас в гости</h2>
        <div class="contacts-grid reveal">
            <div class="contact-info">
                <h3>Свяжитесь с нами</h3>
                <div class="contact-item">
                    <span>Адрес бутика</span>
                    <p>г. Омск, ул. Гагарина, д. 10</p>
                </div>
                <div class="contact-item">
                    <span>Телефон</span>
                    <p><a href="tel:+79991234567" style="color:#666; text-decoration:none;">+7 (999) 123-45-67</a></p>
                </div>
                <div class="contact-item">
                    <span>Время работы</span>
                    <p>Ежедневно с 09:00 до 21:00<br>Без перерывов и выходных</p>
                </div>
                <div class="contact-item">
                    <span>Почта для заказов</span>
                    <p>hello@sweetparadise.ru</p>
                </div>
            </div>
            <div class="contact-map">
                <iframe
                    src="https://yandex.ru/map-widget/v1/?um=constructor%3A9c8a1b32d1d054238db31ab910dcdb120c915f02f928a38b3fa734c56855b46e&amp;source=constructor"
                    width="100%" height="400" frameborder="0"></iframe>
            </div>
        </div>
    </section>

   <?php include 'footer.php'; ?>

   
    <div id="toast"></div>

    <script src="assets/js/main.js"></script>
</body>

</html>