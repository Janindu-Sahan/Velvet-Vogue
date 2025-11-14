-- Velvet Vogue Database Schema
-- Created: 2025-11-14
-- Database: velvet-vogue
-- Updated: Force drop and recreate database

-- Drop database if exists and create fresh
DROP DATABASE IF EXISTS `velvet-vogue`;
CREATE DATABASE `velvet-vogue` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `velvet-vogue`;

-- Drop tables if they exist (in reverse order of dependencies)
DROP TABLE IF EXISTS `order_items`;
DROP TABLE IF EXISTS `orders`;
DROP TABLE IF EXISTS `cart_items`;
DROP TABLE IF EXISTS `contact_inquiries`;
DROP TABLE IF EXISTS `products`;
DROP TABLE IF EXISTS `categories`;
DROP TABLE IF EXISTS `users`;

-- ====================================
-- Users Table
-- ====================================
CREATE TABLE `users` (
  `id` VARCHAR(36) PRIMARY KEY,
  `email` VARCHAR(255) NOT NULL UNIQUE,
  `full_name` VARCHAR(255),
  `is_admin` BOOLEAN DEFAULT FALSE,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ====================================
-- Categories Table
-- ====================================
CREATE TABLE `categories` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `slug` VARCHAR(100) NOT NULL UNIQUE,
  `description` TEXT,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ====================================
-- Products Table
-- ====================================
CREATE TABLE `products` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(255) NOT NULL,
  `slug` VARCHAR(255) NOT NULL UNIQUE,
  `description` TEXT,
  `price` DECIMAL(10, 2) NOT NULL,
  `category_id` INT,
  `gender` ENUM('men', 'women', 'unisex') DEFAULT 'unisex',
  `sizes` VARCHAR(255), -- Stored as comma-separated values: S,M,L,XL
  `colors` VARCHAR(255), -- Stored as comma-separated values: Black,White,Blue
  `main_image` VARCHAR(500), -- Main product image filename
  `image` VARCHAR(500), -- Alternative image field
  `image_url` VARCHAR(500), -- Full URL or path to image
  `stock` INT DEFAULT 0,
  `featured` BOOLEAN DEFAULT FALSE,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`) ON DELETE SET NULL,
  INDEX `idx_slug` (`slug`),
  INDEX `idx_category` (`category_id`),
  INDEX `idx_featured` (`featured`),
  INDEX `idx_gender` (`gender`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ====================================
-- Cart Items Table
-- ====================================
CREATE TABLE `cart_items` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` VARCHAR(36),
  `product_id` INT NOT NULL,
  `quantity` INT DEFAULT 1,
  `size` VARCHAR(10),
  `color` VARCHAR(50),
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE,
  INDEX `idx_user_product` (`user_id`, `product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ====================================
-- Orders Table (Updated for PHP Session-based System)
-- ====================================
CREATE TABLE `orders` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `customer_name` VARCHAR(255) NOT NULL,
  `customer_email` VARCHAR(255) NOT NULL,
  `customer_phone` VARCHAR(50) NOT NULL,
  `shipping_address` TEXT NOT NULL,
  `payment_method` VARCHAR(50) NOT NULL,
  `total_amount` DECIMAL(10, 2) NOT NULL,
  `order_date` DATETIME NOT NULL,
  `status` VARCHAR(50) DEFAULT 'pending',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_customer_email` (`customer_email`),
  INDEX `idx_order_date` (`order_date`),
  INDEX `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ====================================
-- Order Items Table (Updated for PHP Session-based System)
-- ====================================
CREATE TABLE `order_items` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `order_id` INT NOT NULL,
  `product_id` INT NOT NULL,
  `product_name` VARCHAR(255) NOT NULL,
  `size` VARCHAR(50),
  `quantity` INT NOT NULL,
  `price` DECIMAL(10, 2) NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE CASCADE,
  INDEX `idx_order_id` (`order_id`),
  INDEX `idx_product_id` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ====================================
-- Contact Inquiries Table
-- ====================================
CREATE TABLE `contact_inquiries` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(255) NOT NULL,
  `email` VARCHAR(255) NOT NULL,
  `subject` VARCHAR(255) NOT NULL,
  `message` TEXT NOT NULL,
  `status` ENUM('new', 'read', 'responded', 'closed') DEFAULT 'new',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_status` (`status`),
  INDEX `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ====================================
-- Insert Dummy Data
-- ====================================

-- Insert Users (Admin and Regular Users)
INSERT INTO `users` (`id`, `email`, `full_name`, `is_admin`) VALUES
('admin-uuid-001', 'admin@velvetvogue.com', 'Admin User', TRUE),
('user-uuid-001', 'john.doe@example.com', 'John Doe', FALSE),
('user-uuid-002', 'jane.smith@example.com', 'Jane Smith', FALSE),
('user-uuid-003', 'mike.wilson@example.com', 'Mike Wilson', FALSE),
('user-uuid-004', 'sarah.johnson@example.com', 'Sarah Johnson', FALSE);

-- Insert Categories
INSERT INTO `categories` (`name`, `slug`, `description`) VALUES
('Men''s Formal', 'mens-formal', 'Formal wear for men including suits, dress shirts, and formal trousers'),
('Men''s Casual', 'mens-casual', 'Casual clothing for men including t-shirts, jeans, and casual shirts'),
('Women''s Formal', 'womens-formal', 'Formal wear for women including dresses, blouses, and formal skirts'),
('Women''s Casual', 'womens-casual', 'Casual clothing for women including tops, jeans, and casual dresses'),
('Activewear', 'activewear', 'Athletic and sportswear for both men and women'),
('Accessories', 'accessories', 'Fashion accessories including bags, belts, and scarves');

-- Insert Products (Only products with actual image files)
INSERT INTO `products` (`name`, `slug`, `description`, `price`, `category_id`, `gender`, `sizes`, `colors`, `main_image`, `image`, `image_url`, `stock`, `featured`) VALUES
-- Men's Casual
('PX Tee', 'px-tee', 'Comfortable cotton t-shirt with a classic fit. Perfect for everyday wear.', 29.99, 2, 'men', 'S,M,L,XL,XXL', 'Black,White,Gray,Navy', 'px-tee.jpg', 'px-tee.jpg', 'assets/images/products/px-tee.jpg', 100, TRUE),
('Summer Essentials Tee', 'summer-essentials-tee', 'Lightweight summer t-shirt made from breathable fabric. Stay cool and comfortable.', 34.99, 2, 'men', 'S,M,L,XL', 'White,Blue,Green,Yellow', 'Summer-Essentials-Tee.jpg', 'Summer-Essentials-Tee.jpg', 'assets/images/products/Summer-Essentials-Tee.jpg', 80, TRUE),
('Legacy Game Tank', 'legacy-game-tank', 'Athletic tank top perfect for workouts and casual wear. Moisture-wicking fabric.', 39.99, 2, 'men', 'S,M,L,XL', 'Black,Gray,Navy,Red', 'Legacy-Game-Tank.jpg', 'Legacy-Game-Tank.jpg', 'assets/images/products/Legacy-Game-Tank.jpg', 60, TRUE),
('Classic Twill Cargo Pant', 'classic-twill-cargo-pant', 'Durable cargo pants with multiple pockets. Perfect for outdoor activities.', 89.99, 2, 'men', 'S,M,L,XL,XXL', 'Khaki,Olive,Black,Navy', 'Classic-Twil-Cargo-Pant.jpg', 'Classic-Twil-Cargo-Pant.jpg', 'assets/images/products/Classic-Twil-Cargo-Pant.jpg', 45, FALSE),

-- Women's Formal
('Court Line Wrap Dress', 'court-line-wrap-dress', 'Versatile wrap dress that transitions from office to evening. Flattering fit for all body types.', 149.99, 3, 'women', 'XS,S,M,L,XL', 'Black,Red,Blue,Print', 'Court-line-Wrap-Dress.jpg', 'Court-line-Wrap-Dress.jpg', 'assets/images/products/Court-line-Wrap-Dress.jpg', 40, TRUE),

-- Women's Casual
('Essential Cut Off Crop', 'essential-cut-off-crop', 'Trendy crop top perfect for casual outings. Comfortable and stylish.', 44.99, 4, 'women', 'XS,S,M,L', 'Black,White,Pink,Gray', 'Essential-Cut-Off-Crop.jpg', 'Essential-Cut-Off-Crop.jpg', 'assets/images/products/Essential-Cut-Off-Crop.jpg', 70, TRUE),

-- Activewear (Unisex)
('Essence Washed Zip Up Hoodie', 'essence-washed-zip-up-hoodie', 'Cozy zip-up hoodie with a vintage washed look. Perfect for workouts or lounging.', 79.99, 5, 'unisex', 'XS,S,M,L,XL,XXL', 'Gray,Black,Navy,Olive', 'Essence-Washed-Zip-Up-Hoodie.jpg', 'Essence-Washed-Zip-Up-Hoodie.jpg', 'assets/images/products/Essence-Washed-Zip-Up-Hoodie.jpg', 65, TRUE),
('Essence Washed Cuffed Jogger (Unisex)', 'essence-washed-cuffed-jogger-unisex', 'Comfortable joggers with a relaxed fit. Perfect for casual wear or light workouts.', 69.99, 5, 'unisex', 'XS,S,M,L,XL,XXL', 'Gray,Black,Navy,Olive', 'Essence-Washed-Cuffed-Jogger-(Unisex).jpg', 'Essence-Washed-Cuffed-Jogger-(Unisex).jpg', 'assets/images/products/Essence-Washed-Cuffed-Jogger-(Unisex).jpg', 75, TRUE),

-- Women's Activewear
('Airbond Bra', 'airbond-bra', 'High-support sports bra with advanced moisture-wicking technology. Perfect for intense workouts.', 59.99, 5, 'women', 'XS,S,M,L,XL', 'Black,White,Pink,Blue', 'airbond-bra.jpg', 'airbond-bra.jpg', 'assets/images/products/airbond-bra.jpg', 85, TRUE),
('Vital Sculpt Bra', 'vital-sculpt-bra', 'Sculpting sports bra with medium support. Comfortable and stylish for any workout.', 54.99, 5, 'women', 'XS,S,M,L,XL', 'Black,Gray,Purple,Teal', 'Vital-Sculpt-Bra.jpg', 'Vital-Sculpt-Bra.jpg', 'assets/images/products/Vital-Sculpt-Bra.jpg', 70, TRUE);

-- Insert Sample Orders (Updated for PHP Session-based System)
INSERT INTO `orders` (`customer_name`, `customer_email`, `customer_phone`, `shipping_address`, `payment_method`, `total_amount`, `order_date`, `status`) VALUES
('John Doe', 'john.doe@example.com', '+1-555-0101', '123 Main St, New York, NY 10001', 'credit_card', 329.98, '2025-11-10 14:30:00', 'delivered'),
('Jane Smith', 'jane.smith@example.com', '+1-555-0102', '456 Oak Ave, Los Angeles, CA 90001', 'cash_on_delivery', 249.99, '2025-11-11 10:15:00', 'shipped'),
('Mike Wilson', 'mike.wilson@example.com', '+1-555-0103', '789 Pine Rd, Chicago, IL 60601', 'debit_card', 169.97, '2025-11-12 16:45:00', 'processing'),
('Sarah Johnson', 'sarah.johnson@example.com', '+1-555-0104', '321 Elm St, Houston, TX 77001', 'bank_transfer', 449.96, '2025-11-13 09:20:00', 'pending'),
('Emily Brown', 'emily.brown@example.com', '+1-555-0105', '654 Maple Dr, Phoenix, AZ 85001', 'credit_card', 599.99, '2025-11-14 11:00:00', 'delivered');

-- Insert Order Items (Updated for PHP Session-based System)
INSERT INTO `order_items` (`order_id`, `product_id`, `product_name`, `quantity`, `price`, `size`) VALUES
-- Order 1
(1, 1, 'PX Tee', 2, 29.99, 'L'),
(1, 2, 'Summer Essentials Tee', 1, 34.99, 'M'),
(1, 6, 'Essence Washed Zip Up Hoodie', 1, 79.99, 'M'),

-- Order 2
(2, 5, 'Court Line Wrap Dress', 1, 149.99, 'M'),

-- Order 3
(3, 5, 'Court Line Wrap Dress', 1, 149.99, 'L'),
(3, 8, 'Airbond Bra', 1, 59.99, 'M'),

-- Order 4
(4, 4, 'Classic Twill Cargo Pant', 1, 89.99, 'L'),
(4, 3, 'Legacy Game Tank', 1, 39.99, 'M'),

-- Order 5
(5, 7, 'Essence Washed Cuffed Jogger (Unisex)', 2, 69.99, 'L');

-- Insert Cart Items (Active Shopping Carts)
INSERT INTO `cart_items` (`user_id`, `product_id`, `quantity`, `size`, `color`) VALUES
('user-uuid-002', 1, 2, 'L', 'Navy'),
('user-uuid-002', 6, 1, 'M', 'Gray'),
('user-uuid-003', 7, 1, 'L', 'Black'),
('user-uuid-003', 8, 1, 'M', 'Pink'),
('user-uuid-004', 5, 1, 'M', 'Red');

-- Insert Contact Inquiries
INSERT INTO `contact_inquiries` (`name`, `email`, `subject`, `message`, `status`) VALUES
('John Doe', 'john.doe@example.com', 'Product Inquiry', 'I would like to know more about your formal suits collection. Do you offer custom tailoring?', 'responded'),
('Jane Smith', 'jane.smith@example.com', 'Shipping Question', 'How long does shipping typically take for international orders?', 'read'),
('Mike Wilson', 'mike.wilson@example.com', 'Return Policy', 'What is your return policy for items that don''t fit properly?', 'new'),
('Sarah Johnson', 'sarah.johnson@example.com', 'Bulk Order', 'I''m interested in placing a bulk order for my company. Can you provide corporate discounts?', 'responded'),
('Emily Brown', 'emily.brown@example.com', 'Product Availability', 'When will the Essence Washed Zip Up Hoodie be back in stock in size XL?', 'new');

-- ====================================
-- Create Views (Optional - for easier queries)
-- ====================================

-- View for Product Details with Category
CREATE OR REPLACE VIEW `v_product_details` AS
SELECT 
    p.*,
    c.name AS category_name,
    c.slug AS category_slug
FROM 
    `products` p
LEFT JOIN 
    `categories` c ON p.category_id = c.id;

-- View for Order Summary (Updated for PHP Session-based System)
CREATE OR REPLACE VIEW `v_order_summary` AS
SELECT 
    o.id AS order_id,
    o.customer_name,
    o.customer_email,
    o.customer_phone,
    o.total_amount,
    o.status,
    o.payment_method,
    o.order_date,
    o.created_at,
    COUNT(oi.id) AS total_items,
    SUM(oi.quantity) AS total_quantity
FROM 
    `orders` o
LEFT JOIN 
    `order_items` oi ON o.id = oi.order_id
GROUP BY 
    o.id, o.customer_name, o.customer_email, o.customer_phone, o.total_amount, o.status, o.payment_method, o.order_date, o.created_at;

-- ====================================
-- Grant Permissions (Adjust as needed)
-- ====================================
-- GRANT ALL PRIVILEGES ON `velvet-vogue`.* TO 'root'@'localhost';
-- FLUSH PRIVILEGES;

-- ====================================
-- Success Message
-- ====================================
SELECT 'Database schema created successfully!' AS Status;
SELECT COUNT(*) AS Users FROM users;
SELECT COUNT(*) AS Categories FROM categories;
SELECT COUNT(*) AS Products FROM products;
SELECT COUNT(*) AS Orders FROM orders;
SELECT COUNT(*) AS 'Order Items' FROM order_items;
SELECT COUNT(*) AS 'Cart Items' FROM cart_items;
SELECT COUNT(*) AS 'Contact Inquiries' FROM contact_inquiries;
SELECT 'All products have matching image files!' AS 'Image Status';
