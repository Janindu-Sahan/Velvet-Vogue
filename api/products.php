<?php
header('Content-Type: application/json');
include 'includes/db_connect.php';

// Get all products or filter by category
$category = isset($_GET['category']) ? $_GET['category'] : '';
$featured = isset($_GET['featured']) ? (bool)$_GET['featured'] : false;

$sql = "SELECT p.*, c.name AS category_name, c.slug AS category_slug 
        FROM products p 
        LEFT JOIN categories c ON p.category_id = c.id 
        WHERE 1=1";

if (!empty($category)) {
    $sql .= " AND c.slug = ?";
}

if ($featured) {
    $sql .= " AND p.featured = 1";
}

$sql .= " ORDER BY p.name ASC";

$stmt = $conn->prepare($sql);

if (!empty($category)) {
    $stmt->bind_param("s", $category);
}

$stmt->execute();
$result = $stmt->get_result();

$products = [];

while ($row = $result->fetch_assoc()) {
    // Process image path
    $image = $row['main_image'] ?? $row['image'] ?? '';
    if (!empty($image)) {
        if (!preg_match('/^(https?:\/\/|assets\/)/i', $image)) {
            $image = PRODUCT_IMG_PATH . $image;
        }
    } else {
        $image = 'assets/images/products/placeholder.jpg';
    }
    
    $products[] = [
        'id' => $row['id'],
        'name' => $row['name'],
        'slug' => $row['slug'],
        'description' => $row['description'],
        'price' => (float)$row['price'],
        'category_id' => $row['category_id'],
        'category_name' => $row['category_name'],
        'category_slug' => $row['category_slug'],
        'gender' => $row['gender'],
        'sizes' => !empty($row['sizes']) ? explode(',', $row['sizes']) : [],
        'colors' => !empty($row['colors']) ? explode(',', $row['colors']) : [],
        'image' => $image,
        'stock' => (int)$row['stock'],
        'featured' => (bool)$row['featured']
    ];
}

$stmt->close();
$conn->close();

echo json_encode([
    'success' => true,
    'count' => count($products),
    'data' => $products
]);
?>
