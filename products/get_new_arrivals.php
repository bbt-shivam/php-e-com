<?php
include(__DIR__ . '/../includes/db.php');

function getNewArrivals($limit = 3) {
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM products ORDER BY created_at DESC LIMIT ?");
    $stmt->bind_param("i", $limit);
    $stmt->execute();
    $result = $stmt->get_result();
    $products = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    return $products;
}
?>