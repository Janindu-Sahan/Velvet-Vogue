<?php include 'includes/db_connect.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Details - Velvet Vogue</title>
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
        <style>
        /* Minimal inline tweaks for product layout if product.css is missing */
        .product-detail { display: grid; grid-template-columns: 1fr 1fr; gap: 24px; align-items: start; }
        .product-image-container { text-align: center; }
        .product-main-image { max-width: 100%; height: auto; border: 1px solid #eee; }
        .product-info-container { padding: 8px 16px; }
        .product-list-quick ul { list-style: none; padding: 0; }
        .product-list-quick li { margin: 6px 0; }
        </style>
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
                // Static product list (9 items) - easy to edit
                $staticProducts = [
                    'essence-washed-cuffed-jogger' => [
                        'name' => 'Essence Washed Cuffed Jogger (Unisex)',
                        'price' => 8500.00,
                        'description' => 'Comfortable washed jogger with cuffed hem.',
                        'main_image' => 'carnage 1.jpg',
                    ],
                    'classic-twil-cargo-pant' => [
                        'name' => 'Classic Twil Cargo Pant',
                        'price' => 5550.00,
                        'description' => 'Durable cargo pant with multiple pockets.',
                        'main_image' => 'carnage 3.jpg',
                    ],
                    'vital-sculpt-bra' => [
                        'name' => 'Vital Sculpt Bra',
                        'price' => 1483.00,
                        'description' => 'Supportive sculpting bra for active wear.',
                        'main_image' => 'airbond-bra.jpg',
                    ],
                    'carnage-12' => [
                        'name' => 'Carnage 12',
                        'price' => 3200.00,
                        'description' => 'Limited edition design.',
                        'main_image' => 'carnage 12.webp',
                    ],
                    'carnage-14' => [
                        'name' => 'Carnage 14',
                        'price' => 3100.00,
                        'description' => 'Premium fabric and fit.',
                        'main_image' => 'carnage 14.webp',
                    ],
                    'carnage-15' => [
                        'name' => 'Carnage 15',
                        'price' => 2999.00,
                        'description' => 'Seasonal colorway.',
                        'main_image' => 'carnage 15.webp',
                    ],
                    'carnage-16' => [
                        'name' => 'Carnage 16',
                        'price' => 2750.00,
                        'description' => 'Lightweight and breathable.',
                        'main_image' => 'carnage 16.webp',
                    ],
                    'carnage-2' => [
                        'name' => 'Carnage 2',
                        'price' => 2400.00,
                        'description' => 'Streetwear staple.',
                        'main_image' => 'carnage 2.webp',
                    ],
                    'carnage-4' => [
                        'name' => 'Carnage 4',
                        'price' => 2600.00,
                        'description' => 'Relaxed fit with modern cut.',
                        'main_image' => 'carnage 4.webp',
                    ],
                ];

                function normalize_image_src($img) {
                    $img = $img ?? '';
                    if ($img === '') return 'assets/images/products/placeholder.svg';
                    // If it's already an absolute URL or starts with assets/ or /, return as-is
                    if (preg_match('/^(https?:\/\/|\/|assets\/)/i', $img)) return $img;
                    return 'assets/images/products/' . ltrim($img, '/');
                }

                function render_product_detail($productData, $slug = '') {
                    $image_src = normalize_image_src($productData['main_image'] ?? $productData['image'] ?? '');
                    $name = $productData['name'] ?? 'Product';
                    $price = isset($productData['price']) ? number_format($productData['price'], 2) : '0.00';
                    $description = $productData['description'] ?? '';
                    ?>
                    <div class="product-detail">
                        <div class="product-image-container">
                            <img class="product-main-image" src="<?php echo htmlspecialchars($image_src); ?>" alt="<?php echo htmlspecialchars($name); ?>" loading="lazy">
                        </div>
                        <div class="product-info-container">
                            <h2 class="product-title"><?php echo htmlspecialchars($name); ?></h2>
                            <p class="product-price-display">$<?php echo $price; ?></p>
                            <p class="product-description"><?php echo nl2br(htmlspecialchars($description)); ?></p>
                            <form method="POST" action="cart.php">
                                <input type="hidden" name="product_slug" value="<?php echo htmlspecialchars($slug); ?>">
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
                }

                // If slug provided, try DB then static fallback
                if (isset($_GET['slug'])) {
                    $slug = $_GET['slug'];
                    $productFromDb = null;
                    if ($conn) {
                        $stmt = @$conn->prepare("SELECT p.*, c.name AS category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE p.slug = ? LIMIT 1");
                        if ($stmt) {
                            $stmt->bind_param('s', $slug);
                            $stmt->execute();
                            $res = $stmt->get_result();
                            if ($res && $res->num_rows > 0) {
                                $productFromDb = $res->fetch_assoc();
                            }
                            $stmt->close();
                        }
                    }

                    if ($productFromDb) {
                        // Ensure image path normalized
                        $productFromDb['main_image'] = normalize_image_src($productFromDb['main_image'] ?? $productFromDb['image'] ?? '');
                        render_product_detail($productFromDb, $slug);
                    } elseif (isset($staticProducts[$slug])) {
                        render_product_detail($staticProducts[$slug], $slug);
                    } else {
                        echo '<p>Product not found.</p>';
                    }
                } else {
                    // No slug: show quick links
                    echo '<div class="product-list-quick">';
                    echo '<h3>Available products (click to view)</h3>';
                    echo '<ul>';
                    foreach ($staticProducts as $key => $p) {
                        echo '<li><a href="product.php?slug=' . urlencode($key) . '">' . htmlspecialchars($p['name']) . '</a></li>';
                    }
                    echo '</ul>';
                    echo '</div>';
                }
                ?>
            </div>
        </section>

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
    </body>
    </html>
