<?php
// Start session for cart tracking if needed
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart - Velvet Vogue</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/cart.css">
</head>
<body>
    <?php include 'includes/navbar.php'; // Optional: separate navbar ?>
    
    <section class="cart-section">
        <div class="container">
            <h1 class="page-title">SHOPPING CART</h1>

            <div class="cart-layout">
                <div class="cart-items" id="cartItems">
                    <?php
                    // Example: Display items if stored in session
                    if (!empty($_SESSION['cart'])) {
                        foreach ($_SESSION['cart'] as $item) {
                            echo '
                            <div class="cart-item">
                                <div class="item-info">
                                    <h4>' . htmlspecialchars($item['name']) . '</h4>
                                    <p>Quantity: ' . intval($item['quantity']) . '</p>
                                </div>
                                <div class="item-price">$' . number_format($item['price'], 2) . '</div>
                            </div>';
                        }
                    } else {
                        echo '<div class="loading">Your cart is empty.</div>';
                    }
                    ?>
                </div>

                <div class="cart-summary">
                    <h3>ORDER SUMMARY</h3>
                    <div class="summary-row">
                        <span>Subtotal:</span>
                        <span id="subtotal">
                            <?php
                            $subtotal = 0;
                            if (!empty($_SESSION['cart'])) {
                                foreach ($_SESSION['cart'] as $item) {
                                    $subtotal += $item['price'] * $item['quantity'];
                                }
                            }
                            echo '$' . number_format($subtotal, 2);
                            ?>
                        </span>
                    </div>
                    <div class="summary-row">
                        <span>Shipping:</span>
                        <span>FREE</span>
                    </div>
                    <div class="summary-row total">
                        <span>Total:</span>
                        <span id="total">$<?php echo number_format($subtotal, 2); ?></span>
                    </div>
                    <button class="btn btn-primary" id="checkoutBtn" style="width: 100%; margin-top: 24px;">PROCEED TO CHECKOUT</button>
                    <a href="shop.php" class="continue-shopping">Continue Shopping</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Checkout Modal -->
    <div class="modal" id="checkoutModal">
        <div class="modal-content">
            <span class="modal-close" id="modalClose">&times;</span>
            <h2>Checkout</h2>
            <form id="checkoutForm" method="POST" action="checkout_process.php">
                <div class="form-group">
                    <label for="shippingName">Full Name *</label>
                    <input type="text" id="shippingName" name="shippingName" required>
                </div>
                <div class="form-group">
                    <label for="shippingEmail">Email *</label>
                    <input type="email" id="shippingEmail" name="shippingEmail" required>
                </div>
                <div class="form-group">
                    <label for="shippingAddress">Shipping Address *</label>
                    <textarea id="shippingAddress" name="shippingAddress" rows="4" required></textarea>
                </div>
                <div class="form-group">
                    <label for="shippingPhone">Phone Number *</label>
                    <input type="tel" id="shippingPhone" name="shippingPhone" required>
                </div>
                <button type="submit" class="btn btn-primary" style="width: 100%;">PLACE ORDER</button>
            </form>
        </div>
    </div>

    <?php include 'includes/footer.php'; // Optional: separate footer ?>
    
    <script type="module" src="assets/js/main.js"></script>
    <script type="module" src="assets/js/cart.js"></script>
</body>
</html>
