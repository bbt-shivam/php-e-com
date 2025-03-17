<?php
include('../includes/db.php');
include('../includes/header.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Use prepare + bind_param for security
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    // Verify password using password_verify
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['is_admin'] = $user['is_admin'];

        // ✅ Redirect back to stored URL or default to home page
        if (!empty($_SESSION['redirect_to'])) {
            $redirect_to = $_SESSION['redirect_to'];
            unset($_SESSION['redirect_to']);
            header("Location: $redirect_to");
            exit;
        } else {
            header('Location: ../index.php');
            exit;
        }
    } else {
        $error = "Invalid email or password.";
    }

    $stmt->close(); // Close statement
}
?>

<div class="container d-flex justify-content-center align-items-center" style="min-height: 80vh;">
    <div class="card shadow-lg" style="width: 100%; max-width: 400px;">
        <div class="card-body">
            <h2 class="text-center mb-4">Login</h2>
            <?php if (isset($error)): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>
            <form method="POST">
                <div class="mb-3">
                    <label for="email" class="form-label">Email address</label>
                    <input type="email" name="email" class="form-control" id="email" required placeholder="Enter your email">
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" id="password" required placeholder="Enter your password">
                </div>
                <button type="submit" class="btn btn-primary w-100">Login</button>
            </form>
            <p class="text-center mt-3">
                Don't have an account? <a href="register.php">Register here</a>
            </p>
        </div>
    </div>
</div>

<?php include('../includes/footer.php'); ?>
