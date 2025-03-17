<?php
include(__DIR__ . '/../includes/db.php');
include(__DIR__ . '/../includes/header.php');

// Get filter and sorting parameters
$sort = $_GET['sort'] ?? 'name_asc';
$min_price = $_GET['min_price'] ?? '';
$max_price = $_GET['max_price'] ?? '';
$page = $_GET['page'] ?? 1;
$limit = 6; // Products per page
$offset = ($page - 1) * $limit;

// Base query
$query = "SELECT * FROM products WHERE 1";

// Apply price filter
if ($min_price !== '' && $max_price !== '') {
    $query .= " AND price BETWEEN $min_price AND $max_price";
} elseif ($min_price !== '') {
    $query .= " AND price >= $min_price";
} elseif ($max_price !== '') {
    $query .= " AND price <= $max_price";
}

// Apply sorting
switch ($sort) {
    case 'name_asc':
        $query .= " ORDER BY name ASC";
        break;
    case 'name_desc':
        $query .= " ORDER BY name DESC";
        break;
    case 'price_asc':
        $query .= " ORDER BY price ASC";
        break;
    case 'price_desc':
        $query .= " ORDER BY price DESC";
        break;
}

// Get total count for pagination
$count_query = str_replace("SELECT *", "SELECT COUNT(*) as total", $query);
$total_result = $conn->query($count_query);
$total_count = $total_result->fetch_assoc()['total'];
$total_pages = ceil($total_count / $limit);

// Add LIMIT and OFFSET for pagination
$query .= " LIMIT $limit OFFSET $offset";

$result = $conn->query($query);
$products = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];

?>

<div class="container py-5">
    <h2 class="text-center mb-4">All Products</h2>

    <!-- Filter and Sort Form -->
    <form method="GET" class="mb-4">
        <div class="row">
            <div class="col-md-3">
                <input type="number" name="min_price" value="<?php echo htmlspecialchars($min_price); ?>" class="form-control" placeholder="Min Price">
            </div>
            <div class="col-md-3">
                <input type="number" name="max_price" value="<?php echo htmlspecialchars($max_price); ?>" class="form-control" placeholder="Max Price">
            </div>
            <div class="col-md-3">
                <select name="sort" class="form-select">
                    <option value="name_asc" <?php echo $sort === 'name_asc' ? 'selected' : ''; ?>>Name (A-Z)</option>
                    <option value="name_desc" <?php echo $sort === 'name_desc' ? 'selected' : ''; ?>>Name (Z-A)</option>
                    <option value="price_asc" <?php echo $sort === 'price_asc' ? 'selected' : ''; ?>>Price (Low to High)</option>
                    <option value="price_desc" <?php echo $sort === 'price_desc' ? 'selected' : ''; ?>>Price (High to Low)</option>
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary w-100">Apply</button>
            </div>
        </div>
    </form>

    <div class="row">
        <?php if (!empty($products)): ?>
            <?php foreach ($products as $product): ?>
                <div class="col-md-4 mb-4">
                    <div class="card">
                        <img src="../assets/images/products/<?php echo htmlspecialchars($product['image']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($product['name']); ?>">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($product['name']); ?></h5>
                            <p class="card-text"><?php echo substr($product['description'], 0, 50) . '...'; ?></p>
                            <p class="card-text"><strong>$<?php echo number_format($product['price'], 2); ?></strong></p>
                            <a href="../products/detail.php?id=<?php echo $product['id']; ?>" class="btn btn-primary">View Details</a>
                            <form action="../cart/add.php" method="POST" class="d-inline">
                                <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                <button type="submit" class="btn btn-success">Add to Cart</button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>

        <?php else: ?>
            <p class="text-center">No products found.</p>
        <?php endif; ?>
    </div>

    <!-- Pagination -->
    <?php if ($total_pages > 1): ?>
        <nav>
            <ul class="pagination justify-content-center mt-4">
                <?php if ($page > 1): ?>
                    <li class="page-item">
                        <a class="page-link" href="?page=<?php echo ($page - 1); ?>&sort=<?php echo $sort; ?>&min_price=<?php echo $min_price; ?>&max_price=<?php echo $max_price; ?>">Previous</a>
                    </li>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                        <a class="page-link" href="?page=<?php echo $i; ?>&sort=<?php echo $sort; ?>&min_price=<?php echo $min_price; ?>&max_price=<?php echo $max_price; ?>"><?php echo $i; ?></a>
                    </li>
                <?php endfor; ?>

                <?php if ($page < $total_pages): ?>
                    <li class="page-item">
                        <a class="page-link" href="?page=<?php echo ($page + 1); ?>&sort=<?php echo $sort; ?>&min_price=<?php echo $min_price; ?>&max_price=<?php echo $max_price; ?>">Next</a>
                    </li>
                <?php endif; ?>
            </ul>
        </nav>
    <?php endif; ?>
</div>

<?php include(__DIR__ . '/../includes/footer.php'); ?>