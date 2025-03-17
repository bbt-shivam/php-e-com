<?php
include(__DIR__ . '/../includes/db.php');
include(__DIR__ . '/../includes/header.php');

// Get product ID from the query string
$product_id = $_GET['id'] ?? null;

if (!$product_id) {
    header('Location: ../products');
    exit;
}

// Fetch product details from the database
$query = "SELECT * FROM products WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $product_id);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();

if (!$product) {
    header('Location: ../products');
    exit;
}

// Function to get related products based on price proximity
function getRelatedProducts($conn, $current_product_id, $price) {
    $query = "
        SELECT * 
        FROM products 
        WHERE id != ? 
        ORDER BY ABS(price - ?) 
        LIMIT 3
    ";

    $stmt = $conn->prepare($query);
    $stmt->bind_param('id', $current_product_id, $price);
    $stmt->execute();
    $result = $stmt->get_result();

    return $result->fetch_all(MYSQLI_ASSOC);
}

$related_products = getRelatedProducts($conn, $product['id'], $product['price']);
?>

<div class="container py-5">
    <div class="row">
        <div class="col-md-6">
            <img src="../assets/images/products/<?php echo htmlspecialchars($product['image']); ?>" class="img-fluid" alt="<?php echo htmlspecialchars($product['name']); ?>">
        </div>
        <div class="col-md-6">
            <h2><?php echo htmlspecialchars($product['name']); ?></h2>
            <p class="text-muted">Price: <strong>$<?php echo number_format($product['price'], 2); ?></strong></p>
            <p><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
            
            <!-- Add to Cart Button -->
            <form method="POST" action="../cart/add.php">
                <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                <div class="mb-3">
                    <label for="quantity" class="form-label">Quantity:</label>
                    <input type="number" name="quantity" value="1" min="1" class="form-control" style="width: 100px;">
                </div>
                <button type="submit" class="btn btn-success">Add to Cart</button>
            </form>

            <a href="../products/list.php" class="btn btn-secondary mt-3">Back to Products</a>
        </div>
    </div>

    <!-- Related Products Section -->
    <?php if (!empty($related_products)): ?>
        <div class="mt-5">
            <h3 class="mb-4">Related Products</h3>
            <div class="row">
                <?php foreach ($related_products as $related): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card">
                            <img src="../assets/images/products/<?php echo htmlspecialchars($related['image']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($related['name']); ?>">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($related['name']); ?></h5>
                                <p class="card-text"><?php echo substr($related['description'], 0, 50) . '...'; ?></p>
                                <p class="card-text"><strong>$<?php echo number_format($related['price'], 2); ?></strong></p>
                                <a href="detail.php?id=<?php echo $related['id']; ?>" class="btn btn-primary">View Details</a>
                                <a href="../cart/add.php?product_id=<?php echo $related['id']; ?>" class="btn btn-success">Add to Cart</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php include(__DIR__ . '/../includes/footer.php'); ?>
