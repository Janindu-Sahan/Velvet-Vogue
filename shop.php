<?php
session_start();
include 'includes/db_connect.php';

// Initialize cart session
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Function to resolve image path
function resolveImagePath($image) {
    if (empty($image)) return 'assets/images/products/placeholder.jpg';
    if (preg_match('/^(https?:\/\/|assets\/)/i', $image)) return $image;
    return 'assets/images/products/' . $image;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shop - Velvet Vogue</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/shop.css">
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
                    <li><a href="cart.php" class="cart-link">CART <span class="cart-count" id="cartCount"><?php echo count($_SESSION['cart']); ?></span></a></li>
                </ul>
                <div class="hamburger" id="hamburger">
                    <span></span><span></span><span></span>
                </div>
            </div>
        </div>
    </nav>

    <!-- Products Section -->
    <section class="products-section">
        <div class="container">
            <div class="products-area">
                <div class="products-header">
                    <h1>Our Collection</h1>
                </div>

                <!-- Filters and Search -->
                <div class="filters-container">
                    <form method="GET" action="shop.php" class="filters-form">
                        <div class="search-box">
                            <input type="text" name="search" placeholder="Search products..." 
                                   value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>" 
                                   class="search-input">
                            <button type="submit" class="search-btn">🔍</button>
                        </div>
                        
                        <div class="filter-group">
                            <label for="category">Category:</label>
                            <select name="category" id="category" class="filter-select" onchange="this.form.submit()">
                                <option value="">All Categories</option>
                                <?php
                                $cat_query = "SELECT id, name FROM categories ORDER BY name";
                                $cat_result = $conn->query($cat_query);
                                $selected_category = isset($_GET['category']) ? (int)$_GET['category'] : 0;
                                
                                if ($cat_result && $cat_result->num_rows > 0) {
                                    while ($cat = $cat_result->fetch_assoc()) {
                                        $selected = ($selected_category == $cat['id']) ? 'selected' : '';
                                        echo '<option value="' . $cat['id'] . '" ' . $selected . '>' . htmlspecialchars($cat['name']) . '</option>';
                                    }
                                }
                                ?>
                            </select>
                        </div>
                        
                        <div class="filter-group">
                            <label for="gender">Gender:</label>
                            <select name="gender" id="gender" class="filter-select" onchange="this.form.submit()">
                                <option value="">All</option>
                                <option value="men" <?php echo (isset($_GET['gender']) && $_GET['gender'] == 'men') ? 'selected' : ''; ?>>Men</option>
                                <option value="women" <?php echo (isset($_GET['gender']) && $_GET['gender'] == 'women') ? 'selected' : ''; ?>>Women</option>
                                <option value="unisex" <?php echo (isset($_GET['gender']) && $_GET['gender'] == 'unisex') ? 'selected' : ''; ?>>Unisex</option>
                            </select>
                        </div>
                        
                        <div class="filter-group">
                            <label for="sort">Sort By:</label>
                            <select name="sort" id="sort" class="filter-select" onchange="this.form.submit()">
                                <option value="featured" <?php echo (!isset($_GET['sort']) || $_GET['sort'] == 'featured') ? 'selected' : ''; ?>>Featured</option>
                                <option value="price_low" <?php echo (isset($_GET['sort']) && $_GET['sort'] == 'price_low') ? 'selected' : ''; ?>>Price: Low to High</option>
                                <option value="price_high" <?php echo (isset($_GET['sort']) && $_GET['sort'] == 'price_high') ? 'selected' : ''; ?>>Price: High to Low</option>
                                <option value="name" <?php echo (isset($_GET['sort']) && $_GET['sort'] == 'name') ? 'selected' : ''; ?>>Name: A-Z</option>
                            </select>
                        </div>
                        
                        <?php if (!empty($_GET['search']) || !empty($_GET['category']) || !empty($_GET['gender']) || !empty($_GET['sort'])): ?>
                            <a href="shop.php" class="clear-filters-btn">Clear Filters</a>
                        <?php endif; ?>
                    </form>
                </div>

                <?php
                // Build query with filters
                $query = "SELECT id, name, slug, price, main_image, stock, category_id, gender FROM products WHERE 1=1";
                    
                    // Search filter
                    if (isset($_GET['search']) && !empty($_GET['search'])) {
                        $search = $conn->real_escape_string($_GET['search']);
                        $query .= " AND (name LIKE '%$search%' OR description LIKE '%$search%')";
                    }
                    
                    // Category filter
                    if (isset($_GET['category']) && !empty($_GET['category'])) {
                        $category = (int)$_GET['category'];
                        $query .= " AND category_id = $category";
                    }
                    
                    // Gender filter
                    if (isset($_GET['gender']) && !empty($_GET['gender'])) {
                        $gender = $conn->real_escape_string($_GET['gender']);
                        $query .= " AND gender = '$gender'";
                    }
                    
                    // Sorting
                    $sort = isset($_GET['sort']) ? $_GET['sort'] : 'featured';
                    switch ($sort) {
                        case 'price_low':
                            $query .= " ORDER BY price ASC";
                            break;
                        case 'price_high':
                            $query .= " ORDER BY price DESC";
                            break;
                        case 'name':
                            $query .= " ORDER BY name ASC";
                            break;
                        default:
                            $query .= " ORDER BY featured DESC, created_at DESC";
                    }
                    
                    $result = $conn->query($query);
                    
                    if ($result && $result->num_rows > 0) {
                        $product_count = $result->num_rows;
                        echo '<div class="results-count">' . $product_count . ' product' . ($product_count != 1 ? 's' : '') . ' found</div>';
                        echo '</div>'; // Close filters-container
                        echo '<div class="products-grid" id="productsGrid">';
                        
                        while ($product = $result->fetch_assoc()) {
                            $name = htmlspecialchars($product['name']);
                            $slug = htmlspecialchars($product['slug']);
                            $price = number_format($product['price'], 2);
                            $image = resolveImagePath($product['main_image']);
                            $stock = isset($product['stock']) ? (int)$product['stock'] : 0;

                            echo '<article class="product-card">';
                            echo '  <a href="product.php?slug=' . urlencode($slug) . '">';
                            echo '      <img src="' . $image . '" alt="' . $name . '" class="product-image" loading="lazy">';
                            echo '      <h4 class="product-title">' . $name . '</h4>';
                            echo '      <p class="product-price">$' . $price . '</p>';
                            if ($stock <= 0) {
                                echo '      <p class="product-stock out-of-stock">Out of Stock</p>';
                            }
                            echo '  </a>';
                            echo '</article>';
                        }
                    } else {
                        echo '<div class="no-results">';
                        echo '  <h3>No products found</h3>';
                        echo '  <p>Try adjusting your filters or search criteria.</p>';
                        echo '  <a href="shop.php" class="btn btn-primary">View All Products</a>';
                        echo '</div>';
                    }
                    ?>

                </div>
            </div>
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

    <script  src="assets/js/main.js"></script>
    <script src="assets/js/shop.js"></script>
</body>
</html>