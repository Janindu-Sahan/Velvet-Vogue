<?php
// Optional: start session for user or cart tracking
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact - Velvet Vogue</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/contact.css">
</head>
<body>
    
<nav class="navbar">
        <div class="container">
            <div class="nav-wrapper">
                <a href="index.php" class="logo">VELVET VOGUE</a>
                <ul class="nav-menu" id="navMenu">
                    <li><a href="index.php" class="active">HOME</a></li>
                    <li><a href="shop.php">SHOP</a></li>
                    <li><a href="contact.php">CONTACT</a></li>
                    <li><a href="account.php">ACCOUNT</a></li>
                    <li><a href="cart.php" class="cart-link">CART <span class="cart-count" id="cartCount">0</span></a></li>
                </ul>
                <div class="hamburger" id="hamburger">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
            </div>
        </div>
    </nav>
    
    <section class="contact-section">
        <div class="container">
            <h1 class="page-title">GET IN TOUCH</h1><br>

            <div class="contact-layout">
                <div class="contact-info">
                    <div class="info-card">
                        <h3>EMAIL</h3>
                        <p>info@velvetvogue.com</p>
                    </div>
                    <div class="info-card">
                        <h3>PHONE</h3>
                        <p>+1 (555) 123-4567</p>
                    </div>
                    <div class="info-card">
                        <h3>ADDRESS</h3>
                        <p>123 Fashion Avenue<br>New York, NY 10001<br>United States</p>
                    </div>
                    <div class="info-card">
                        <h3>BUSINESS HOURS</h3>
                        <p>
                            Monday - Friday: 9:00 AM - 6:00 PM<br>
                            Saturday: 10:00 AM - 4:00 PM<br>
                            Sunday: Closed
                        </p>
                    </div>
                </div>

                <div class="contact-form-container">
                    <?php
                    // Handle form submission (basic example)
                    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                        $name = htmlspecialchars($_POST['contactName']);
                        $email = htmlspecialchars($_POST['contactEmail']);
                        $subject = htmlspecialchars($_POST['contactSubject']);
                        $message = htmlspecialchars($_POST['contactMessage']);

                        // Example: save or email message
                        $to = "info@velvetvogue.com";
                        $body = "Name: $name\nEmail: $email\nSubject: $subject\n\nMessage:\n$message";
                        $headers = "From: $email";

                        if (mail($to, $subject, $body, $headers)) {
                            echo "<p class='success-message'>Thank you, $name! Your message has been sent successfully.</p>";
                        } else {
                            echo "<p class='error-message'>Sorry, something went wrong. Please try again later.</p>";
                        }
                    }
                    ?>

                    <form id="contactForm" method="POST" action="contact.php">
                        <div class="form-group">
                            <label for="contactName">Name *</label>
                            <input type="text" id="contactName" name="contactName" required>
                        </div>
                        <div class="form-group">
                            <label for="contactEmail">Email *</label>
                            <input type="email" id="contactEmail" name="contactEmail" required>
                        </div>
                        <div class="form-group">
                            <label for="contactSubject">Subject *</label>
                            <input type="text" id="contactSubject" name="contactSubject" required>
                        </div>
                        <div class="form-group">
                            <label for="contactMessage">Message *</label>
                            <textarea id="contactMessage" name="contactMessage" rows="6" required></textarea>
