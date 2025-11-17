<?php include 'includes/db_connect.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account - Velvet Vogue</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/account.css">
</head>
<body>

    <!-- NAVBAR -->
    <nav class="navbar">
        <div class="container">
            <div class="nav-wrapper">
                <a href="index.php" class="logo">VELVET VOGUE</a>
                <ul class="nav-menu" id="navMenu">
                    <li><a href="index.php">HOME</a></li>
                    <li><a href="shop.php">SHOP</a></li>
                    <li><a href="contact.php">CONTACT</a></li>
                    <li><a href="account.php" class="active">ACCOUNT</a></li>
                    <li><a href="cart.php" class="cart-link">
                        CART <span class="cart-count" id="cartCount">0</span>
                    </a></li>
                </ul>
                <div class="hamburger" id="hamburger">
                    <span></span><span></span><span></span>
                </div>
            </div>
        </div>
    </nav>

    <!-- ACCOUNT SECTION -->
    <section class="account-section">
        <div class="container">

            <!-- AUTH SECTION (Login/Register) -->
            <div id="authContainer">
                <h1 class="page-title">MY ACCOUNT</h1>

                <div class="auth-tabs">
                    <button class="auth-tab active" data-tab="login">LOGIN</button>
                    <button class="auth-tab" data-tab="register">REGISTER</button>
                </div>

                <!-- LOGIN TAB -->
                <div class="tab-content active" id="loginTab">
                    <form id="loginForm">
                        <div class="form-group">
                            <label>Email *</label>
                            <input type="email" id="loginEmail" required>
                        </div>
                        <div class="form-group">
                            <label>Password *</label>
                            <input type="password" id="loginPassword" required>
                        </div>
                        <button type="submit" class="btn btn-primary" style="width: 100%;">LOGIN</button>
                    </form>
                </div>

                <!-- REGISTER TAB -->
                <div class="tab-content" id="registerTab">
                    <form id="registerForm">
                        <div class="form-group">
                            <label>Full Name *</label>
                            <input type="text" id="registerName" required>
                        </div>
                        <div class="form-group">
                            <label>Email *</label>
                            <input type="email" id="registerEmail" required>
                        </div>
                        <div class="form-group">
                            <label>Password *</label>
                            <input type="password" id="registerPassword" required minlength="6">
                        </div>
                        <div class="form-group">
                            <label>Confirm Password *</label>
                            <input type="password" id="confirmPassword" required minlength="6">
                        </div>
                        <button type="submit" class="btn btn-primary" style="width: 100%;">REGISTER</button>
                    </form>
                </div>
            </div>

            <!-- DASHBOARD SECTION -->
            <div id="dashboardContainer" style="display:none;">
                <h1 class="page-title">MY DASHBOARD</h1>

                <div class="dashboard-header">
                    <div id="userInfo"></div>
                    <button class="btn btn-outline" id="logoutBtn">LOGOUT</button>
                </div>

                <div class="dashboard-content">

                    <!-- LEFT SIDE BUTTONS -->
                    <div class="dashboard-nav">
                        <button class="dashboard-tab active" data-tab="orders">MY ORDERS</button>
                        <button class="dashboard-tab" data-tab="profile">PROFILE</button>
                        <button class="dashboard-tab admin-only" data-tab="admin" style="display:none;">ADMIN PANEL</button>
                    </div>

                    <!-- ORDERS PANEL -->
                    <div class="dashboard-panel active" id="ordersPanel">
                        <h2>My Orders</h2>
                        <div id="ordersList">
                            <div class="loading">Loading orders...</div>
                        </div>
                    </div>

                    <!-- PROFILE PANEL -->
                    <div class="dashboard-panel" id="profilePanel">
                        <h2>My Profile</h2>
                        <form id="profileForm">
                            <div class="form-group">
                                <label>Full Name</label>
                                <input type="text" id="profileName" required>
                            </div>
                            <div class="form-group">
                                <label>Email</label>
                                <input type="email" id="profileEmail" disabled>
                            </div>
                            <button type="submit" class="btn btn-primary">UPDATE PROFILE</button>
                        </form>
                    </div>

                    <!-- ADMIN PANEL -->
                    <div class="dashboard-panel" id="adminPanel">
                        <h2>Admin Panel - Manage Products</h2>

                        <div class="admin-actions">
                            <button class="btn btn-primary" id="addProductBtn">ADD NEW PRODUCT</button>
                        </div>

                        <div id="productsManagement">
                            <div class="loading">Loading products...</div>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- FOOTER -->
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

    <!-- JS FILES -->
    <script type="module" src="assets/js/main.js"></script>
    <script type="module" src="assets/js/account.js"></script>

</body>
</html>
