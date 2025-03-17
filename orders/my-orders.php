<?php
include(__DIR__ . '/../includes/db.php');
include(__DIR__ . '/../includes/header.php');

if (empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

$query = "SELECT o.id, o.total, o.created_at 
          FROM orders o 
          WHERE o.user_id = ? 
          ORDER BY o.created_at DESC";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

?>

<div class="container py-5">
    <h2>My Orders</h2>

    <?php while ($order = $result->fetch_assoc()): ?>
        <div class="card mb-3">
            <div class="card-header">
                <strong>Order #<?php echo $order['id']; ?></strong>
                <span class="float-end">$<?php echo number_format($order['total'], 2); ?></span>
            </div>
            <div class="card-body">
                <ul class="list-unstyled">
                    <?php
                    $order_id = $order['id'];
                    $item_query = "SELECT od.quantity, od.price, p.name, p.image 
                                   FROM order_details od 
                                   JOIN products p ON od.product_id = p.id 
                                   WHERE od.order_id = ?";
                    $stmt_items = $conn->prepare($item_query);
                    $stmt_items->bind_param("i", $order_id);
                    $stmt_items->execute();
                    $items = $stmt_items->get_result();

                    while ($item = $items->fetch_assoc()):
                    ?>
                        <li class="d-flex align-items-center mb-2">
                            <img src="<?php echo base_url('assets/images/products/'.htmlspecialchars($item['image'])); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" class="me-3" width="50" height="50" style="object-fit: cover; border-radius: 5px;">

                            <!-- ✅ Display product name and price -->
                            <div>
                                <strong><?php echo htmlspecialchars($item['name']); ?></strong>  
                                - <?php echo $item['quantity']; ?> x $<?php echo number_format($item['price'], 2); ?>
                            </div>
                        </li>
                    <?php endwhile; ?>
                </ul>
            </div>
        </div>
    <?php endwhile; ?>
</div>


<?php include(__DIR__ . '/../includes/footer.php'); ?>
