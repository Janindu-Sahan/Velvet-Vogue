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
                    <li><a href="cart.php" class="cart-link">CART <span class="cart-count" id="cartCount">0</span></a></li>
                </ul>
                <div class="hamburger" id="hamburger">
                    <span></span><span></span><span></span>
                </div>
            </div>
        </div>
    </nav>


                <!-- Products -->
                <div class="products-area">
                    <div class="products-header">
                        
                    </div>

                    <div class="products-grid" id="productsGrid">
                        <?php
                        $product_images = [
                            "airbond-bra.jpg",
                            "Classic-Twil-Cargo-Pant.jpg",
                            "Court-line-Wrap-Dress.jpg",
                            "Essence-Washed-Zip-Up-Hoodie.jpg",
                            "Essence-Washed-Cuffed-Jogger-(Unisex).jpg",
                            "Essential-Cut-Off-Crop.jpg",
                            "Legacy-Game-Tank.jpg",
                            "px-tee.jpg",
                            "Summer-Essentials-Tee.jpg",
                            "Vital-Sculpt-Bra.jpg"
                        ];
                        
                        if (!empty($product_images)) {
                            foreach ($product_images as $image_file) {
                                
                                $name = htmlspecialchars(ucwords(str_replace(['-', '.jpg'], [' ', ''], $image_file)));
                              
                                $image = 'assets/images/products/' . htmlspecialchars($image_file);
                                $slug = urlencode(strtolower(str_replace(' ', '-', $name)));

                                echo '<article class="product-card">';
                                echo '  <a href="product.php?slug=' . $slug . '">';
                                echo '      <img src="' . $image . '" alt="' . $name . '" class="product-image">';
                                echo '      <h4 class="product-title">' . $name . '</h4>';
                               
                                echo '  </a>';
                                echo '</article>';
                            }
                        } else {
                            echo '<p>No products found.</p>';
                        }
                        ?>

                        
                    </div>
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