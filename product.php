<?php include 'includes/db_connect.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Details - Velvet Vogue</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/shop.css">
    <link rel="stylesheet" href="assets/css/product.css">
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

    <!-- Product Details Section -->
    <section class="product-section">
        <div class="container">
            <?php
            if (isset($_GET['slug'])) {
                $slug = $_GET['slug'];

                $stmt = $conn->prepare("SELECT p.*, c.name AS category_name 
                                        FROM products p 
                                        LEFT JOIN categories c ON p.category_id = c.id 
                                        WHERE p.slug = ?");
                $stmt->bind_param("s", $slug);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result && $result->num_rows > 0) {
                    $product = $result->fetch_assoc();
                    $name = htmlspecialchars($product['name']);
                    $price = number_format($product['price'], 2);
                    $desc = nl2br(htmlspecialchars($product['description']));
                    $category = htmlspecialchars($product['category_name'] ?? 'Uncategorized');

                    $imgFile = !empty($product['main_image']) ? htmlspecialchars($product['main_image']) : '';
                    $imgPath = ($imgFile && file_exists(__DIR__ . '/assets/images/products/' . $imgFile))
                        ? 'assets/images/products/' . $imgFile
                        : 'assets/images/placeholder.jpg';
            ?>
                    <div class="product-detail">
                        <div class="product-image-container">
                            <img class="product-main-image" src="<?php echo $imgPath; ?>" alt="<?php echo $name; ?>">
                        </div>
                        <div class="product-info-container">
                            <h2 class="product-title"><?php echo $name; ?></h2>
                            <p class="category"><?php echo $category; ?></p>
                            <p class="product-price-display">$<?php echo $price; ?></p>
                            <p class="product-description"><?php echo $desc; ?></p>
                            
                            <!-- Add to Cart Form -->
                            <form method="POST" action="cart.php">
                                <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">

                                <label for="size">Size:</label>
                                <select name="size" id="size" required>
                                    <option value="">Select Size</option>
                                    <option value="S">S</option>
                                    <option value="M">M</option>
                                    <option value="L">L</option>
                                    <option value="XL">XL</option>
                                </select>

                                <label for="quantity">Quantity:</label>
                                <input type="number" id="quantity" name="quantity" value="1" min="1" max="10" required>

                                <button type="submit" class="btn">Add to Cart</button>
                            </form>
                        </div>
                    </div>
            <?php
                } else {
                    echo "<p>Product not found.</p>";
                }
                $stmt->close();
            } else {
                echo "<p>Invalid product link.</p>";
            }
            ?>
        </div>
    </section>

    <!-- Related Products Section -->
    <?php if (!empty($product)): ?>
    <div class="products-area">
        <div class="container">
            <div class="products-header">
                <p>Related Products</p>
            </div>
            <div class="products-grid">
                <?php
                $category_id = $product['category_id'];
                $current_product_id = $product['id'];

                $stmt = $conn->prepare("SELECT p.*, c.name AS category_name 
                                        FROM products p 
                                        LEFT JOIN categories c ON p.category_id = c.id 
                                        WHERE p.category_id = ? AND p.id != ? 
                                        ORDER BY RAND() 
                                        LIMIT 4");
                $stmt->bind_param("ii", $category_id, $current_product_id);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result && $result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $rname = htmlspecialchars($row['name']);
                        $rprice = number_format($row['price'], 2);
                        $rslug = urlencode($row['slug']);
                        $rimage = !empty($row['main_image']) ? htmlspecialchars($row['main_image']) : '';
                        $rimagePath = ($rimage && file_exists(__DIR__ . '/assets/images/products/' . $rimage))
                            ? 'assets/images/products/' . $rimage
                            : 'assets/images/placeholder.jpg';

                        echo '<article class="product-card">';
                        echo '  <a href="product.php?slug=' . $rslug . '">';
                        echo '      <img src="' . $rimagePath . '" alt="' . $rname . '" class="product-image" loading="lazy">';
                        echo '      <h4 class="product-title">' . $rname . '</h4>';
                        echo '      <p class="price">$' . $rprice . '</p>';
                        echo '  </a>';
                        echo '</article>';
                    }
                } else {
                    echo '<p>No related products found.</p>';
                }
                $stmt->close();
                ?>
            </div>
        </div>
    </div>
    <?php endif; ?>

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

    <script type="module" src="assets/js/product.js"></script>
</body>
</html>
