<?php
session_start();
include('functions.php');
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>E-Commerce Website</title>
    <!-- Bootstrap 5 CSS -->
    <!-- <link href="/assets/bootstrap.min.css" rel="stylesheet" /> -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link href="/assets/style.css" rel="stylesheet" />
    <!-- jQuery CDN -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="<?php echo base_url(); ?>">MyShop</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo base_url(); ?>">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo base_url('products/list.php'); ?>">Products</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo base_url('about-us.php'); ?>">About Us</a>
                    </li>

                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo base_url('orders/my-orders.php'); ?>">My Orders</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo base_url('user/logout.php'); ?>">Logout</a>
                        </li>
                        
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo base_url('user/login.php'); ?>">Login</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo base_url('user/register.php'); ?>">Register</a>
                        </li>
                        
                    <?php endif; ?>
                </ul>
                <li class="nav-item">
                            <?php
                            $cart_count = isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0;
                            ?>

                            <a href="<?php echo base_url('cart/list.php'); ?>" class="btn btn-outline-primary">
                                Cart (<?php echo $cart_count; ?>)
                            </a>
                        </li>
            </div>
        </div>
    </nav>

    <div class="container mt-4">