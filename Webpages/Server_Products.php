<?php
require "db_conn.php";

// Fetch products from the database
$sql = "SELECT s.id, s.name AS shoe_name, s.brand AS brand_name, si.file_path AS shoe_image 
        FROM shoes s 
        JOIN shoe_images si ON s.id = si.shoe_id 
        WHERE s.is_deleted = 0"; // Added condition to exclude deleted shoes
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sneakerheads Admin</title>
    <link rel="stylesheet" href="Server_Products.css">
</head>
<body>

    <header>
        <h1>SNEAKERHEADS (Server Side)</h1>
        <nav>
            <a href="#">Products</a>
            <a href="#">Promo Codes</a>
            <a href="#">Reports</a>
            <button class="sign-in">Sign Out</button>
        </nav>
    </header>

    <section class="admin-panel">
        <div class="form-container">
            <h2>ADD PRODUCT</h2>
            <form action="add_product.php" method="post" enctype="multipart/form-data">
                <input type="text" name="shoe_name" placeholder="Shoe Name" required>
                <input type="text" name="brand_name" placeholder="Brand Name" required>
                <input type="number" step="0.1" name="shoe_size" placeholder="Shoe Size" required> <!-- Changed to accept float values -->
                <input type="number" name="shoe_count" placeholder="Shoe Count" required>
                <input type="number" step="0.01" name="price" placeholder="Price(₱)" required> <!-- Added price input -->
                <input type="file" name="shoe_image" accept=".jpeg" required> <!-- Changed to accept only JPEG files -->
                <button type="submit">Add</button>
            </form>
        </div>

        <div class="product-list">
            <h2>DELETE PRODUCT</h2>
            <div class="grid">
                <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="product">
                        <img src="<?= $row['shoe_image'] ?>" alt="<?= $row['shoe_name'] ?>">
                        <p><?= $row['shoe_name'] ?></p>
                        <form action="BackEnd/delete_product.php" method="post"> <!-- Updated action -->
                            <input type="hidden" name="id" value="<?= $row['id'] ?>">
                            <button type="submit">Delete</button>
                        </form>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>

        <div class="form-container">
            <h2>UPDATE PRODUCT</h2>
            <form action="update_product.php" method="post" enctype="multipart/form-data">
                <input type="number" name="id" placeholder="Product ID" required>
                <input type="text" name="shoe_name" placeholder="Shoe Name">
                <input type="text" name="brand_name" placeholder="Brand Name">
                <input type="number" step="0.1" name="shoe_size" placeholder="Shoe Size"> <!-- Changed to accept float values -->
                <input type="number" name="shoe_count" placeholder="Shoe Count">
                <input type="number" step="0.01" name="price" placeholder="Price(₱)"> <!-- Added price input -->
                <input type="file" name="shoe_image" accept=".jpeg"> <!-- Changed to accept only JPEG files -->
                <button type="submit">Update</button>
            </form>
        </div>
    </section>

</body>
</html>

<?php $conn->close(); ?>
