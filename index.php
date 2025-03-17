<?php include('includes/header.php'); ?>

<!-- Hero Section -->
<div class="hero-section text-center bg-light py-5">
    <div class="container">
        <h1 class="display-4">Discover Timeless Elegance</h1>
        <p class="lead">Explore our exclusive collection of premium watches.</p>
        <img src="assets/hero.jpg" alt="Watch Banner" class="img-fluid" style="max-height: 400px;">
        <a href="products/list.php" class="btn btn-primary mt-3">Shop Now</a>
    </div>
</div>

<!-- New Arrival Section -->

<?php
include('products/get_new_arrivals.php');
$newArrivals = getNewArrivals();
?>
<div class="new-arrival-section py-5">
    <div class="container">
        <h2 class="text-center mb-4">New Arrivals</h2>
        <div class="row">
            <?php foreach ($newArrivals as $product): ?>
                <div class="col-md-4 mb-4">
                    <div class="card">
                        <img src="assets/images/products/<?php echo $product['image']; ?>" class="card-img-top" alt="<?php echo $product['name']; ?>">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo $product['name']; ?></h5>
                            <p class="card-text"><?php echo substr($product['description'], 0, 50) . '...'; ?></p>
                            <p class="card-text"><strong>$<?php echo number_format($product['price'], 2); ?></strong></p>
                            <a href="products/detail.php?id=<?php echo $product['id']; ?>" class="btn btn-primary">View Details</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>


<!-- Promises Section -->
<div class="promises-section bg-light py-5">
    <div class="container">
        <h2 class="text-center mb-4">Why Shop With Us?</h2>
        <div class="row text-center">
            <div class="col-md-4">
                <h4>✔ Free Delivery</h4>
                <p>Fast and reliable delivery worldwide.</p>
            </div>
            <div class="col-md-4">
                <h4>✔ 1000+ Products</h4>
                <p>A wide selection of premium watches.</p>
            </div>
            <div class="col-md-4">
                <h4>✔ Easy Returns</h4>
                <p>30-day money-back guarantee.</p>
            </div>
        </div>
    </div>
</div>

<!-- Testimonials Section -->
<div class="testimonials-section py-5 bg-dark text-white">
    <div class="container">
        <h2 class="text-center mb-4">What Our Customers Say</h2>
        <div class="row">
            <div class="col-md-4">
                <blockquote>
                    <p>"Amazing quality and fast delivery!"</p>
                    <footer>- John Doe</footer>
                </blockquote>
            </div>
            <div class="col-md-4">
                <blockquote>
                    <p>"Love the design and the build quality!"</p>
                    <footer>- Sarah Smith</footer>
                </blockquote>
            </div>
            <div class="col-md-4">
                <blockquote>
                    <p>"Best watch collection I've ever seen!"</p>
                    <footer>- Alex Johnson</footer>
                </blockquote>
            </div>
        </div>
    </div>
</div>

<?php include('includes/footer.php'); ?>