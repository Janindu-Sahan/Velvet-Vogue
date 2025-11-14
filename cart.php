<?php
include 'includes/db_connect.php';
session_start();

// Initialize cart session
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Add product to cart
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['product_id'])) {
    $product_id = (int) $_POST['product_id'];
    $size = $_POST['size'] ?? '';
    $quantity = (int) ($_POST['quantity'] ?? 1);

    // Fetch product details from DB
    $stmt = $conn->prepare("SELECT id, name, price, image FROM products WHERE id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();
    $stmt->close();

    if ($product) {
        $cartKey = $product_id . '-' . $size;

        // Resolve image path
        $imagePath = $product['image'];
        if (!preg_match('/^(https?:\/\/|assets\/)/i', $imagePath)) {
            $imagePath = 'assets/images/products/' . $imagePath;
        }

        if (isset($_SESSION['cart'][$cartKey])) {
            $_SESSION['cart'][$cartKey]['quantity'] += $quantity;
        } else {
            $_SESSION['cart'][$cartKey] = [
                'id' => $product['id'],
                'name' => $product['name'],
                'price' => $product['price'],
                'image' => $imagePath,
                'size' => $size,
                'quantity' => $quantity
            ];
        }
    }

    header("Location: cart.php");
    exit();
}

// Update quantity in cart
if (isset($_GET['update']) && isset($_GET['key']) && isset($_GET['qty'])) {
    $updateKey = $_GET['key'];
    $newQty = max(1, (int)$_GET['qty']);
    
    if (isset($_SESSION['cart'][$updateKey])) {
        $_SESSION['cart'][$updateKey]['quantity'] = $newQty;
    }
    header("Location: cart.php");
    exit();
}

// Remove item from cart
if (isset($_GET['remove'])) {
    $removeKey = $_GET['remove'];
    unset($_SESSION['cart'][$removeKey]);
    header("Location: cart.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Cart - Velvet Vogue</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/cart.css">
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
                    <li><a href="cart.php" class="cart-link active">CART 
                        <span class="cart-count"><?php echo count($_SESSION['cart']); ?></span>
                    </a></li>
                </ul>
                <div class="hamburger" id="hamburger">
                    <span></span><span></span><span></span>
                </div>
            </div>
        </div>
    </nav>

    <!-- Cart Section -->
    <section class="cart-section">
        <div class="container">
            <h1>Your Shopping Cart</h1>

            <?php if (!empty($_SESSION['cart'])): ?>
                <table class="cart-table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Size</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th>Subtotal</th>
                            <th>Remove</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $total = 0;
                        foreach ($_SESSION['cart'] as $key => $item): 
                            $subtotal = $item['price'] * $item['quantity'];
                            $total += $subtotal;
                        ?>
                        <tr>
                            <td class="cart-product">
                                <img src="<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>">
                                <span><?php echo htmlspecialchars($item['name']); ?></span>
                            </td>
                            <td><?php echo htmlspecialchars($item['size']); ?></td>
                            <td>$<?php echo number_format($item['price'], 2); ?></td>
                            <td>
                                <div class="cart-qty-controls">
                                    <button class="qty-btn" onclick="updateQty('<?php echo $key; ?>', <?php echo max(1, $item['quantity'] - 1); ?>)">-</button>
                                    <span class="qty-display"><?php echo (int)$item['quantity']; ?></span>
                                    <button class="qty-btn" onclick="updateQty('<?php echo $key; ?>', <?php echo $item['quantity'] + 1; ?>)">+</button>
                                </div>
                            </td>
                            <td>$<?php echo number_format($subtotal, 2); ?></td>
                            <td><a href="cart.php?remove=<?php echo urlencode($key); ?>" class="remove-btn" onclick="return confirm('Remove this item from cart?')">✖</a></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <div class="cart-summary">
                    <h3>Total: $<?php echo number_format($total, 2); ?></h3>
                    <a href="checkout.php" class="btn btn-primary" style="text-decoration: none; display: inline-block; text-align: center;">Proceed to Checkout</a>
                </div>
            <?php else: ?>
                <p>Your cart is currently empty. <a href="shop.php">Shop now</a>.</p>
            <?php endif; ?>
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

    <script>
        // Update cart quantity
        function updateQty(key, qty) {
            if (qty < 1) return;
            window.location.href = `cart.php?update=1&key=${encodeURIComponent(key)}&qty=${qty}`;
        }

        // Hamburger menu toggle
        document.addEventListener('DOMContentLoaded', () => {
            const hamburger = document.getElementById('hamburger');
            const navMenu = document.getElementById('navMenu');
            
            if (hamburger && navMenu) {
                hamburger.addEventListener('click', () => {
                    navMenu.classList.toggle('active');
                });
            }
        });
    </script>
</body>
</html>
