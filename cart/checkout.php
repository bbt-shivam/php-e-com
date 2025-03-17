<?php
include(__DIR__ . '/../includes/db.php');
include(__DIR__ . '/../includes/header.php');

if (empty($_SESSION['user_id'])) {
    $_SESSION['redirect_to'] = $_SERVER['REQUEST_URI'];
    header("Location: " . base_url('user/login.php'));
    exit;
}

if (empty($_SESSION['cart'])) {
    header('Location: list.php');
    exit;
}

$total = 0;
foreach ($_SESSION['cart'] as $product) {
    $total += $product['price'] * $product['quantity'];
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $address = trim($_POST['address']);
    $city = trim($_POST['city']);
    $state = trim($_POST['state']);
    $postal_code = trim($_POST['postal_code']);
    $phone = trim($_POST['phone']);

    // ✅ Validate Shipping Details
    if (empty($name)) $errors[] = "Full name is required.";
    if (empty($address)) $errors[] = "Address is required.";
    if (empty($city)) $errors[] = "City is required.";
    if (empty($state)) $errors[] = "State is required.";
    if (empty($postal_code) || !preg_match('/^\d{5,6}$/', $postal_code)) $errors[] = "Invalid postal code.";
    if (empty($phone) || !preg_match('/^\+?[0-9]{10,15}$/', $phone)) $errors[] = "Invalid phone number format.";

    if (empty($errors)) {
        $conn->begin_transaction();

        try {
            // ✅ Insert order into `orders` table
            $stmt = $conn->prepare("INSERT INTO orders (user_id, total) VALUES (?, ?)");
            $stmt->bind_param("id", $_SESSION['user_id'], $total);
            $stmt->execute();
            $order_id = $stmt->insert_id;
            $stmt->close();

            // ✅ Insert shipping details into `shipping_details` table
            $stmt = $conn->prepare("INSERT INTO shipping_details 
                (user_id, order_id, name, address, city, state, postal_code, phone) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)");

            $stmt->bind_param(
                "iissssss",
                $_SESSION['user_id'],
                $order_id,
                $name,
                $address,
                $city,
                $state,
                $postal_code,
                $phone
            );
            $stmt->execute();
            $stmt->close();

            // ✅ Insert order items into `order_details` table
            foreach ($_SESSION['cart'] as $product) {
                $stmt = $conn->prepare("INSERT INTO order_details 
                    (order_id, product_id, quantity, price) 
                    VALUES (?, ?, ?, ?)");

                $stmt->bind_param(
                    "iiid",
                    $order_id,
                    $product['id'],
                    $product['quantity'],
                    $product['price']
                );
                $stmt->execute();
                $stmt->close();
            }

            // ✅ Commit transaction
            $conn->commit();

            // ✅ Clear cart
            $_SESSION['cart'] = [];

            // ✅ Redirect to success page
            header('Location: success.php');
            exit;
        } catch (Exception $e) {
            $conn->rollback(); // Rollback transaction on failure
            $errors[] = "Error: " . $e->getMessage();
        }
    }
}
?>

<div class="container py-5">
    <h2>Checkout</h2>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?php echo htmlspecialchars($error); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <p>Total Amount: <strong>$<?php echo number_format($total, 2); ?></strong></p>

    <form method="POST" action="checkout.php">
        <!-- ✅ Shipping Form -->
        <div class="mb-3">
            <label for="name" class="form-label">Full Name</label>
            <input type="text" name="name" id="name" class="form-control"
                value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>" required>
        </div>

        <div class="mb-3">
            <label for="address" class="form-label">Address</label>
            <textarea name="address" id="address" class="form-control" required><?php echo htmlspecialchars($_POST['address'] ?? ''); ?></textarea>
        </div>

        <div class="mb-3">
            <label for="city" class="form-label">City</label>
            <input type="text" name="city" id="city" class="form-control"
                value="<?php echo htmlspecialchars($_POST['city'] ?? ''); ?>" required>
        </div>

        <div class="mb-3">
            <label for="state" class="form-label">State</label>
            <input type="text" name="state" id="state" class="form-control"
                value="<?php echo htmlspecialchars($_POST['state'] ?? ''); ?>" required>
        </div>

        <div class="mb-3">
            <label for="postal_code" class="form-label">Postal Code</label>
            <input type="text" name="postal_code" id="postal_code" class="form-control"
                value="<?php echo htmlspecialchars($_POST['postal_code'] ?? ''); ?>" required>
        </div>

        <div class="mb-3">
            <label for="phone" class="form-label">Phone Number</label>
            <input type="text" name="phone" id="phone" class="form-control"
                value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>" required>
        </div>

        <button type="submit" class="btn btn-success">Confirm Order</button>
    </form>
</div>

<?php include(__DIR__ . '/../includes/footer.php'); ?>