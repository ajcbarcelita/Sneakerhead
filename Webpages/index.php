<?php
require 'db_conn.php';

$sneakers = [];
$sql = "SELECT s.name, s.brand, si.file_path AS image 
        FROM shoes s 
        JOIN shoe_images si ON s.id = si.shoe_id 
        WHERE s.is_deleted = 0"; // Added condition to exclude deleted shoes
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $sneakers[] = $row;
    }
}

$brands = array_unique(array_column($sneakers, 'brand'));
usort($brands, function($a, $b) {
    return strcmp($a, $b);
});

$selected_brand = $_GET['brand'] ?? '';

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SneakerHeads</title>
    <link rel="stylesheet" href="index.css">
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar">
        <div class="logo">SNEAKERHEADS</div>
        <ul class="nav-links">
            <li><a href="index.php">Home</a></li>
            <li><a href="Checkout.php">Check Out</a></li>
            <li><a href="profile_page.php">My Profile</a></li>
            <li><a href="#" class="btn">Sign in</a></li>
        </ul>
    </nav>

    <!-- Hero Section -->
    <header class="hero">
        <h1>"Step into style with SneakerHead – Where the rare meets the runway."</h1>
        <a href="#products" class="shop-btn">Browse our shop</a>
    </header>

    <!-- Filter Section -->
    <section class="filter">
        <form method="GET" action="#products">
            <label for="brand">Filter by Brand:</label>
            <select name="brand" id="brand" onchange="this.form.submit()">
                <option value="">All Brands</option>
                <?php foreach ($brands as $brand) : ?>
                    <option value="<?php echo $brand; ?>" <?php echo $selected_brand === $brand ? 'selected' : ''; ?>>
                        <?php echo $brand; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </form>
    </section>

    <!-- Products Grid -->
    <section id="products" class="products">
        <?php foreach ($sneakers as $sneaker) : ?>
            <?php if ($selected_brand === '' || $sneaker['brand'] === $selected_brand) : ?>
                <div class="product">
                    <a href="product.php?name=<?php echo urlencode($sneaker['name']); ?>">
                        <img src="<?php echo $sneaker['image']; ?>" alt="<?php echo $sneaker['name']; ?>">
                    </a>
                    <p><?php echo $sneaker['name']; ?></p>
                    <p><strong>Brand:</strong> <?php echo $sneaker['brand']; ?></p>
                </div>
            <?php endif; ?>
        <?php endforeach; ?>
    </section>  
    <!-- Footer -->
    <footer class="footer">
        <div class="footer-content">
            <h2>About Us</h2>
            <p>At Sneakerhead, we're passionate about bringing sneaker enthusiasts closer to the most coveted and exclusive kicks.</p>
            <p>Whether you're looking for the latest limited-edition releases or classic staples, our platform is dedicated to connecting you with high-quality, authentic sneakers.</p>
            <p>With a focus on trust, reliability, and community, Sneakerhead is your go-to destination for reselling, buying, and trading sneakers that define your style.</p>
            <p>Step up your sneaker game with us today!</p>
        </div>
    </footer>

</body>
</html>
