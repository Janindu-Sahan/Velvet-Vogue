<?php include 'includes/db_connect.php'; ?>
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
        <ul class="nav-menu">
          <li><a href="index.php">HOME</a></li>
          <li><a href="shop.php" class="active">SHOP</a></li>
          <li><a href="contact.php">CONTACT</a></li>
          <li><a href="account.php">ACCOUNT</a></li>
          <li><a href="cart.php" class="cart-link">CART <span class="cart-count" id="cartCount">0</span></a></li>
        </ul>
      </div>
    </div>
  </nav>

  <!-- Shop Section -->
  <section class="shop-section">
    <div class="container">
      <h2>Our Collection</h2>
      <div class="products-grid">
        <?php
        // Fetch products with category
        $sql = "SELECT p.*, c.name AS category_name FROM products p 
                LEFT JOIN categories c ON p.category_id = c.id
                ORDER BY p.id DESC";
        $result = $conn->query($sql);

        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                // product image path
                $imageFile = !empty($row['image']) ? $row['image'] : 'placeholder.jpg';
             $imgPath = 'assets/images/products/' . htmlspecialchars($imageFile);


                echo '<article class="product-card">';
                echo '  <a href="product.php?slug=' . urlencode($row['slug']) . '">';
                echo '    <img src="' . $imgPath . '" alt="' . htmlspecialchars($row['name']) . '" loading="lazy">';
                echo '    <h4 class="product-title">' . htmlspecialchars($row['name']) . '</h4>';
                echo '    <p class="price">$' . number_format($row['price'], 2) . '</p>';
                echo '  </a>';
                echo '</article>';
            }
        } else {
            echo '<p>No products found.</p>';
        }
        ?>
      </div>
    </div>
  </section>

  <footer class="footer">
    <div class="container">
      <p>&copy; 2025 Velvet Vogue. All rights reserved.</p>
    </div>
  </footer>

</body>
</html>
