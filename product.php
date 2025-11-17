<?php
session_start();
include 'includes/db_connect.php';

// Initialize cart session
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

function resolveImagePath($image) {
    if (empty($image)) return ;
    if (preg_match('/^(https?:\/\/|assets\/)/i', $image)) return $image;
    return 'assets/images/products/' . $image;
}
?>


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
                    <li><a href="shop.php">SHOP</a></li>
                    <li><a href="contact.php">CONTACT</a></li>
                    <li><a href="account.php">ACCOUNT</a></li>
                    <li><a href="cart.php" class="cart-link">CART <span class="cart-count" id="cartCount"><?php echo count($_SESSION['cart']); ?></span></a></li>
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
            $product = null;

            if (isset($_GET['slug'])) {
                $slug = $_GET['slug'];

                $stmt = $conn->prepare("SELECT p.*, c.name AS category_name 
                                        FROM products p 
                                        LEFT JOIN categories c ON p.category_id = c.id 
                                        WHERE p.slug = ? LIMIT 1");
                $stmt->bind_param("s", $slug);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result && $result->num_rows > 0) {
                    $product = $result->fetch_assoc();

                    // safe values
                    $name = htmlspecialchars($product['name']);
                    $price = number_format((float)$product['price'], 2);
                    $desc = nl2br(htmlspecialchars($product['description']));
                    $category = htmlspecialchars($product['category_name'] ?? 'Uncategorized');

                    // image path check
                     $imgPath = resolveImagePath($product['main_image']);


                    $sizes = [];
                    if (!empty($product['sizes'])) {
                        
                        $sizes = array_map('trim', explode(',', $product['sizes']));
                        
                        $sizes = array_filter($sizes);
                    }

                    
                    $stock = isset($product['stock']) ? (int)$product['stock'] : null;
                    $maxQty = $stock && $stock > 0 ? $stock : 10;
                     ?>



<div class="product-details-container">
    <div class="product-image">
        <img src="<?php echo $imgPath; ?>" 
             alt="<?php echo $name; ?>" 
             id="productImage">
    </div>

    <div class="product-info">
        <h2 class="product-title"><?php echo $name; ?></h2>
        <p class="product-price">Rs. <?php echo $price; ?></p>
        <p class="product-description"><?php echo $desc; ?></p>

<form id="addToCartForm" action="cart.php" method="POST">
            <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
            <?php if (!empty($sizes)): ?>
            <div class="size-group">
                <label for="sizeSelect">Size:</label>
                <select id="sizeSelect" name="size" class="size-dropdown" required>
                    <option value="" disabled selected>Select Size</option>
                    <?php foreach ($sizes as $sz): ?>
                        <option value="<?php echo htmlspecialchars($sz); ?>"><?php echo htmlspecialchars($sz); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <?php else: ?>
                <input type="hidden" name="size" value="N/A">
            <?php endif; ?>

            <div class="quantity-group">
                <label for="quantityInput">Quantity:</label>
                <div class="quantity-controls">
                    <button type="button" class="qty-btn minus" aria-label="Decrease">−</button>
                    <input id="quantityInput" name="quantity" class="quantity-input" value="1" min="1" max="<?php echo (int)$maxQty; ?>" required>
                    <button type="button" class="qty-btn plus" aria-label="Increase">+</button>
                </div>
                <?php if ($stock !== null): ?>
                    <p class="stock-indicator"><?php echo $stock > 0 ? "{$stock} in stock" : "Out of stock"; ?></p>
                <?php endif; ?>
            </div>

            <div class="add-to-cart-section">
                <button type="submit" id="addToCartBtn" class="btn add-to-cart-btn"<?php echo ($stock === 0 ? ' disabled' : ''); ?>>
                    <?php echo ($stock === 0 ? 'OUT OF STOCK' : 'ADD TO CART'); ?>
                </button>
            </div>
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

    <!-- Related Products -->
    <?php if (!empty($product)): ?>
    <div class="products-area">
        <div class="container">
            <div class="products-header">
                <p>Related Products</p>
            </div>
            <div class="products-grid">
                <?php
                $category_id = $product['category_id'] ?? null;
                $current_product_id = $product['id'] ?? null;

                if ($category_id && $current_product_id) {
                    $stmt = $conn->prepare("SELECT p.* FROM products p WHERE p.category_id = ? AND p.id != ? ORDER BY RAND() LIMIT 4");
                    $stmt->bind_param("ii", $category_id, $current_product_id);
                    $stmt->execute();
                    $res = $stmt->get_result();
                    if ($res && $res->num_rows > 0) {
                        while ($row = $res->fetch_assoc()) {
                            $rname = htmlspecialchars($row['name']);
                            $rslug = urlencode($row['slug']);
                            
                       
                            $rimagePath = resolveImagePath($row['main_image']);



                            echo '<article class="product-card">';
                            echo '  <a href="product.php?slug=' . $rslug . '">';
                            echo '      <img src="' . $rimagePath . '" alt="' . $rname . '" class="product-image" loading="lazy">';
                            echo '      <h4 class="product-title">' . $rname . '</h4>';
                            echo '  </a>';
                            echo '</article>';
                        }
                    } else {
                        echo '<p>No related products found.</p>';
                    }
                    $stmt->close();
                } else {
                    echo '<p>No related products found.</p>';
                }
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

    <script type="module" src="assets/js/main.js"></script>
    <script type="module" src="assets/js/product.js"></script>

</body>
</html>
