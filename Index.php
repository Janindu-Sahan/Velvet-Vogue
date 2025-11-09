<?php
// You can add PHP logic here if needed (like fetching data from database)
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Velvet Vogue - Luxury Fashion</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <div class="nav-wrapper">
                <a href="index.php" class="logo">VELVET VOGUE</a>
                <ul class="nav-menu" id="navMenu">
                    <li><a href="index.php" class="active">HOME</a></li>
                    <li><a href="shop.php">SHOP</a></li>
                    <li><a href="contact.php">CONTACT</a></li>
                    <li><a href="account.php">ACCOUNT</a></li>
                    <li><a href="cart.php" class="cart-link">CART <span class="cart-count" id="cartCount">0</span></a></li>
                </ul>
                <div class="hamburger" id="hamburger">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
            </div>
        </div>
    </nav>

    <section class="hero">
        <div class="hero-content">
            <h1 class="hero-title">ELEGANCE REDEFINED</h1>
            <p class="hero-subtitle">Discover the perfect blend of luxury and comfort</p>
            <a href="shop.php" class="btn btn-primary">EXPLORE COLLECTION</a>
        </div>
    </section>

    <section class="featured-section">
        <div class="container">
            <h2 class="section-title">FEATURED COLLECTIONS</h2>
            <div class="featured-grid" id="featuredProducts">
                <img src="./assets/images/products/carnage 1.webp" alt="Men's tee 1">
                <img src="./assets/images/products/carnage 5.webp" alt="Womens tee 1">
                <img src="./assets/images/products/carnage 7.webp" alt="Men's tee 2">
                <img src="./assets/images/products/carnage 6.webp" alt="Womens tee 2">
                <img src="./assets/images/products/carnage 3.webp" alt="Womens tee 2">
                <img src="./assets/images/products/carnage 10.webp" alt="Womens tee 2">
            </div>
        </div>
    </section>

    <section class="categories-section">
        <div class="container">
            <h2 class="section-title">SHOP BY CATEGORY</h2>
            <div class="categories-grid">
                <a href="shop.php?category=mens-formal" class="category-card">
                    <img src="https://images.pexels.com/photos/2182970/pexels-photo-2182970.jpeg?auto=compress&cs=tinysrgb&w=600" alt="Men's Formal">
                    <div class="category-overlay">
                        <h3>MEN'S FORMAL</h3>
                    </div>
                </a>
                <a href="shop.php?category=mens-casual" class="category-card">
                    <img src="https://images.pexels.com/photos/1040945/pexels-photo-1040945.jpeg?auto=compress&cs=tinysrgb&w=600" alt="Men's Casual">
                    <div class="category-overlay">
                        <h3>MEN'S CASUAL</h3>
                    </div>
                </a>
                <a href="shop.php?category=womens-formal" class="category-card">
                    <img src="https://images.pexels.com/photos/1926769/pexels-photo-1926769.jpeg?auto=compress&cs=tinysrgb&w=600" alt="Women's Formal">
                    <div class="category-overlay">
                        <h3>WOMEN'S FORMAL</h3>
                    </div>
                </a>
                <a href="shop.php?category=womens-casual" class="category-card">
                    <img src="https://images.pexels.com/photos/1055691/pexels-photo-1055691.jpeg?auto=compress&cs=tinysrgb&w=600" alt="Women's Casual">
                    <div class="category-overlay">
                        <h3>WOMEN'S CASUAL</h3>
                    </div>
                </a>
            </div>
        </div>
    </section>

    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3>VELVET VOGUE</h3>
                    <p>Redefining luxury fashion for modern individuals.</p>
                </div>
                <div class="footer-section">
                    <h4>QUICK LINKS</h4>
                    <ul>
                        <li><a href="shop.php">Shop</a></li>
                        <li><a href="contact.php">Contact</a></li>
                        <li><a href="account.php">My Account</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h4>CONTACT</h4>
                    <p>Email: info@velvetvogue.com</p>
                    <p>Phone: +1 (555) 123-4567</p>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; <?php echo date("Y"); ?> Velvet Vogue. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script type="module" src="assets/js/main.js"></script>
    <script type="module" src="assets/js/home.js"></script>
</body>
</html>
