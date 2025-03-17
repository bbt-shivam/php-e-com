<?php
include(__DIR__ . '/../includes/header.php');

// Handle cart updates or removals
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $product_id = $_POST['product_id'] ?? null;

    if ($action === 'update' && isset($_POST['quantity'])) {
        $quantity = intval($_POST['quantity']);
        if ($quantity > 0 && isset($_SESSION['cart'][$product_id])) {
            $_SESSION['cart'][$product_id]['quantity'] = $quantity;
        }
    }

    if ($action === 'remove' && isset($_SESSION['cart'][$product_id])) {
        unset($_SESSION['cart'][$product_id]);
    }

    header('Location: list.php');
    exit;
}

// Calculate total price
$total = 0;
if (!empty($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        $total += $item['price'] * $item['quantity'];
    }
}

?>

<div class="container py-5">
    <h2 class="mb-4">Shopping Cart</h2>

    <?php if (!empty($_SESSION['cart'])): ?>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Image</th>
                    <th>Product</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Total</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($_SESSION['cart'] as $item): ?>
                    <tr>
                        <td>
                            <img src="../assets/images/products/<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" style="width: 80px;">
                        </td>
                        <td><?php echo htmlspecialchars($item['name']); ?></td>
                        <td>$<?php echo number_format($item['price'], 2); ?></td>
                        <td>
                            <form method="POST" action="list.php" style="display: inline-block;">
                                <input type="hidden" name="product_id" value="<?php echo $item['id']; ?>">
                                <input type="hidden" name="action" value="update">
                                <input type="number" name="quantity" value="<?php echo $item['quantity']; ?>" min="1" class="form-control" style="width: 60px; display: inline-block;">
                                <button type="submit" class="btn btn-sm btn-primary">Update</button>
                            </form>
                        </td>
                        <td>$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                        <td>
                            <form method="POST" action="list.php">
                                <input type="hidden" name="product_id" value="<?php echo $item['id']; ?>">
                                <input type="hidden" name="action" value="remove">
                                <button type="submit" class="btn btn-sm btn-danger">Remove</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="4" class="text-end"><strong>Total:</strong></td>
                    <td colspan="2"><strong>$<?php echo number_format($total, 2); ?></strong></td>
                </tr>
            </tfoot>
        </table>

        <a href="../products/list.php" class="btn btn-secondary">Continue Shopping</a>
        <a href="checkout.php" class="btn btn-success">Checkout</a>
    <?php else: ?>
        <p>Your cart is empty.</p>
        <a href="../products/list.php" class="btn btn-secondary">Continue Shopping</a>
    <?php endif; ?>
</div>

<?php include(__DIR__ . '/../includes/footer.php'); ?>
