<?php
// Start session if needed (for cart or user session)
session_start();

// Include database connection
include 'includes/db_connect.php'; // <-- create this file to connect to your MySQL database

// Get product ID from URL
$product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Fetch product details from database
$product = null;
if ($product_id > 0) {
    $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $product ? htmlspecialchars($product['name']) . " - Velvet Vogue" : "Product Not Found - Velvet Vogue"; ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/product.css">
</head>
<body>
    <?php include 'includes/navbar.php'; ?>

    <section class="product-section">
        <div class="container">
            <div class="breadcrumb">
                <a href="index.php">Home</a> / 
                <a href="shop.php">Shop</a> / 
                <span id="breadcrumbProduct"><?php echo $product ? htmlspecialchars($product['name']) : "Product"; ?></span>
            </div>

            <div class="product-detail" id="productDetail">
                <?php if ($product): ?>
                    <div class="product-container">
                        <div class="product-image">
                            <img src="assets/images/products/<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                        </div>
                        <div class="product-info">
                            <h1><?php echo htmlspecialchars($product['name']); ?></h1>
                            <p class="product-description"><?php echo htmlspecialchars($product['description']); ?></p>
                            <p class="product-price">$<?php echo number_format($product['price'], 2); ?></p>
                            <form method="POST" action="cart.php?action=add&id=<?php echo $product['id']; ?>">
                                <input type="number" name="quantity" value="1" min="1" required>
                                <button type="submit" class="btn btn-primary">ADD TO CART</button>
                            </form>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="loading">Product not found.</div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <?php include 'includes/footer.php'; ?>

    <script type="module" src="assets/js/main.js"></script>
    <script type="module" src="assets/js/product.js"></script>
</body>
</html>
