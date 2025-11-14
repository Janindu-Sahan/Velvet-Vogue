<?php
header('Content-Type: application/json');
include '../includes/db_connect.php';

$sql = "SELECT * FROM categories ORDER BY name ASC";
$result = $conn->query($sql);

$categories = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $categories[] = [
            'id' => $row['id'],
            'name' => $row['name'],
            'slug' => $row['slug'],
            'description' => $row['description']
        ];
    }
}

$conn->close();

echo json_encode([
    'success' => true,
    'count' => count($categories),
    'data' => $categories
]);
?>
