<?php include 'includes/db_connect.php'; ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shop - Velvet Vogue</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/shop.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar">
        <div class="container">
            <div class="nav-wrapper">
                <a href="index.php" class="logo">VELVET VOGUE</a>
                <ul class="nav-menu" id="navMenu">
                    <li><a href="index.php">HOME</a></li>
                    <li><a href="shop.php" class="active">SHOP</a></li>
                    <li><a href="contact.php">CONTACT</a></li>
                    <li><a href="account.php">ACCOUNT</a></li>
                    <li><a href="cart.php" class="cart-link">CART <span class="cart-count" id="cartCount">0</span></a></li>
                </ul>
                <div class="hamburger" id="hamburger">
                    <span></span><span></span><span></span>
                </div>
            </div>
        </div>
    </nav>


    <!-- All Products Section -->
    <div class="products-area">
        <div class="products-header">
            <p>All Products</p>
        </div>

        <div class="products-grid">
            <?php
            // ✅ Fetch all products
            $sql = "SELECT * FROM products ORDER BY id DESC";
            $result = $conn->query($sql);

            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $name = htmlspecialchars($row['name']);
                    $price = number_format($row['price'], 2);
                    $slug = urlencode($row['slug']);
                    $imageFilename = htmlspecialchars($row['main_image']);

                    // ✅ Correct image path
                    $imagePath = "assets/images/products/" . $imageFilename;

                    // ✅ Fallback if image doesn’t exist
                    if (empty($imageFilename) || !file_exists($imagePath)) {
                        $imagePath = "assets/images/placeholder.jpg";
                    }

                    echo '
                    <article class="product-card">
                        <a href="product.php?slug=' . $slug . '">
                            <img src="' . $imagePath . '" alt="' . $name . '" class="product-image" loading="lazy">
                            <h4 class="product-title">' . $name . '</h4>
                            <p class="price">LKR ' . $price . '</p>
                        </a>
                    </article>';
                }
            } else {
                echo "<p>No products found.</p>";
            }
            ?>
        </div>
    </div>

    <!-- Footer -->
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
                <p>&copy; 2025 Velvet Vogue. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script type="module" src="assets/js/main.js"></script>
    <script type="module" src="assets/js/shop.js"></script>
</body>

</html>
