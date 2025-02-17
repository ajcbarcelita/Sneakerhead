<?php
// index.php
$sneakers = [
    ["name" => "Nike Air Force 1", "image" => "airforce1.jpg", "brand" => "Nike"],
    ["name" => "Nike Dunk Low", "image" => "dunklow.jpg", "brand" => "Nike"],
    ["name" => "Air Jordan 1 Low", "image" => "jordan1low.jpg", "brand" => "Jordan"],
    ["name"=> "Adidas Ultraboost Light", "image" => "ultraboost.jpg", "brand" => "Adidas"],
    ["name"=> "Kobe 6 Proto 'Grinch'", "image" => "kobe6.jpg", "brand" => "Nike"],
    ["name"=> "Adidas OG Samba", "image" => "samba.jpg", "brand" => "Adidas"]
];

$brands = array_unique(array_column($sneakers, 'brand'));
sort($brands);

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
            <li><a href="#">Home</a></li>
            <li><a href="#">Check Out</a></li>
            <li><a href="#">My Profile</a></li>
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
                    <img src="images/<?php echo $sneaker['image']; ?>" alt="<?php echo $sneaker['name']; ?>">
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
