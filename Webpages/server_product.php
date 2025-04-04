<?php
    session_start();
    if (!isset( $_SESSION["role_id"]) || $_SESSION["role_id"] != 1) {
        header("Location: login.php");
        exit();
    }

    require "db_conn.php";
    require "BackEnd/sql_queries.php"; // Include the new file containing SQL scripts
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sneakerheads Admin</title>
    <link rel="stylesheet" href="server_product.css">
    <script>
        function fetchProductDetails(productId) {
            if (productId) {
                fetch('BackEnd/fetch_product_details.php?id=' + productId)
                    .then(response => response.json())
                    .then(data => {
                        document.querySelector('.update-form input[name="shoe_name"]').value = data.shoe_name;
                        document.querySelector('.update-form input[name="brand_name"]').value = data.brand_name;
                        document.querySelector('.update-form input[name="price"]').value = data.price;
                        // Add more fields as needed
                    });
            } else {
                document.querySelector('.update-form input[name="shoe_name"]').value = '';
                document.querySelector('.update-form input[name="brand_name"]').value = '';
                document.querySelector('.update-form input[name="price"]').value = '';
                // Clear more fields as needed
            }
        }
    </script>
</head>
<body>

    <header>
        <h1>SNEAKERHEADS (Server Side)</h1>
        <nav>
            <a href="#">Products</a>
            <a href="server_promo_codes.php">Promo Codes</a>
            <a href="show_reports.php">Reports</a>
            <button class="sign-in" onclick="window.location.href='logout-handler.php'">Sign Out</button> <!-- Updated button -->
        </nav>
    </header>

    <?php
        // Display success message
        if (isset($_SESSION['success'])) {
            echo "<div class='success-message'>" . htmlspecialchars($_SESSION['success']) . "</div>";
            unset($_SESSION['success']); // Clear the message after displaying it
        }

        // Display error message
        if (isset($_SESSION['error'])) {
            echo "<div class='error-message'>" . htmlspecialchars($_SESSION['error']) . "</div>";
            unset($_SESSION['error']); // Clear the message after displaying it
        }
    ?>

    <section class="admin-panel">
        <div class="form-container">
            <h2>ADD PRODUCT</h2>
            <form action="BackEnd/add_product.php" method="post" enctype="multipart/form-data"> <!-- Updated action -->
                <div class="input-group">
                    <label for="shoe_name">Shoe Name: </label> <!-- Added space after colon -->
                    <input type="text" name="shoe_name" placeholder="Shoe Name" required>
                </div>
                <div class="input-group">
                    <label for="brand_name">Brand Name: </label> <!-- Added space after colon -->
                    <input type="text" name="brand_name" placeholder="Brand Name" required>
                </div>
                <div class="input-group">
                    <label for="shoe_size">Shoe Size: </label> <!-- Added space after colon -->
                    <select name="shoe_size" required class="full-width">
                        <option value="" disabled selected>Select Size</option> <!-- Default null option -->
                        <?php foreach ($sizes as $size): ?>
                            <option value="<?= $size ?>"><?= $size ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="input-group">
                    <label for="shoe_count">Shoe Count: </label> <!-- Added space after colon -->
                    <input type="number" name="shoe_count" placeholder="Shoe Count" required min="0" oninput="validity.valid||(value='');">
                </div>
                <div class="input-group">
                    <label for="price">Price(₱): </label> <!-- Added space after colon -->
                    <input type="number" step="0.01" name="price" placeholder="Price(₱)" required min="0" oninput="validity.valid||(value='');">
                </div>
                <div class="input-group">
                    <label for="shoe_image">Shoe Image: </label> <!-- Added space after colon -->
                    <input type="file" name="shoe_image" accept=".jpeg,.jpg" required> <!-- Allow .jpg files -->
                </div>
                <button type="submit">Add</button>
            </form>
        </div>

        <div class="product-list">
            <h2>DELETE PRODUCT</h2>
            <div class="filter-container">
                <form method="get" action="">
                    <div class="filter-group">
                        <label for="brand_filter">Filter by Brand: </label> <!-- Added space after colon -->
                        <select name="brand_filter" id="brand_filter" onchange="this.form.submit()">
                            <option value="">All Brands</option>
                            <?php foreach ($brands as $brand): ?>
                                <option value="<?= $brand ?>" <?= isset($_GET['brand_filter']) && $_GET['brand_filter'] == $brand ? 'selected' : '' ?>><?= $brand ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label for="deleted_filter">Show Deleted: </label> <!-- Added space after colon -->
                        <select name="deleted_filter" id="deleted_filter" onchange="this.form.submit()">
                            <option value="">All</option>
                            <option value="1" <?= isset($_GET['deleted_filter']) && $_GET['deleted_filter'] == '1' ? 'selected' : '' ?>>Yes</option>
                            <option value="0" <?= isset($_GET['deleted_filter']) && $_GET['deleted_filter'] == '0' ? 'selected' : '' ?>>No</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="grid">
                <?php
                $brand_filter = isset($_GET['brand_filter']) ? $_GET['brand_filter'] : '';
                $deleted_filter = isset($_GET['deleted_filter']) ? $_GET['deleted_filter'] : '';
                $sql = "SELECT s.id, s.name AS shoe_name, s.brand AS brand_name, si.file_path AS shoe_image, s.is_deleted 
                        FROM shoes s 
                        JOIN shoe_images si ON s.id = si.shoe_id";
                $conditions = [];
                if ($brand_filter) {
                    $conditions[] = "s.brand = '$brand_filter'";
                }
                if ($deleted_filter !== '') {
                    $conditions[] = "s.is_deleted = $deleted_filter";
                }
                if ($conditions) {
                    $sql .= " WHERE " . implode(' AND ', $conditions);
                }
                $result = $conn->query($sql);

                $count = 0;
                while ($row = $result->fetch_assoc()): 
                    if ($count % 5 == 0 && $count != 0): ?>
                        </div><div class="grid">
                    <?php endif; ?>
                    <div class="product">
                        <img src="<?= $row['shoe_image'] ?>" class="product-image <?= $row['is_deleted'] ? 'greyscale' : '' ?>" alt="<?= $row['shoe_name'] ?>">
                        <p>Product ID: <?= $row['id'] ?><br>Shoe Name: <?= $row['shoe_name'] ?></p> <!-- Corrected display of product ID and shoe name -->
                        <form action="BackEnd/<?= $row['is_deleted'] ? 'restore_product.php' : 'delete_product.php' ?>" method="post">
                            <input type="hidden" name="id" value="<?= $row['id'] ?>">
                            <button type="submit"><?= $row['is_deleted'] ? 'Restore' : 'Delete' ?></button>
                        </form>
                    </div>
                    <?php 
                    $count++;
                endwhile; ?>
                <?php if ($count % 5 != 0): ?>
                    <?php for ($i = $count % 5; $i < 5; $i++): ?>
                        <div class="product empty"></div>
                    <?php endfor; ?>
                <?php endif; ?>
            </div>
        </div>

        <div class="form-container">
            <h2>UPDATE PRODUCT</h2>
            <form class="update-form" action="BackEnd/update_product.php" method="post" enctype="multipart/form-data"> <!-- Updated action -->
                <div class="input-group">
                    <label for="id">Product ID: </label> <!-- Changed to dropdown -->
                    <select name="id" required onchange="fetchProductDetails(this.value)">
                        <option value="" disabled selected>Select Product ID</option> <!-- Default null option -->
                        <?php foreach ($products as $product): ?>
                            <option value="<?= $product['id'] ?>"><?= $product['id'] ?> - <?= $product['name'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="input-group">
                    <label for="shoe_name">Shoe Name: </label> <!-- Added space after colon -->
                    <input type="text" name="shoe_name" placeholder="Shoe Name">
                </div>
                <div class="input-group">
                    <label for="brand_name">Brand Name: </label> <!-- Added space after colon -->
                    <input type="text" name="brand_name" placeholder="Brand Name">
                </div>
                <div class="input-group">
                    <label for="price">Price(₱): </label> <!-- Added space after colon -->
                    <input type="number" step="0.01" name="price" placeholder="Price(₱)" min="0" oninput="validity.valid||(value='');">
                </div>
                <div class="input-group">
                    <label for="shoe_image">Shoe Image: </label> <!-- Added space after colon -->
                    <input type="file" name="shoe_image" accept=".jpeg,.jpg"> <!-- Allow .jpg files -->
                </div>
                <button type="submit">Update</button>
            </form>
        </div>

        <div class="form-container">
            <h2>UPDATE SHOE STOCK</h2>
            <form class="update-stock-form" action="BackEnd/update_shoe_stock.php" method="post">
                <div class="input-group">
                    <label for="id">Product ID: </label>
                    <select name="id" id="product_id" required onchange="fetchShoeStock()">
                        <option value="" disabled selected>Select Product ID</option>
                        <?php foreach ($products as $product): ?>
                            <option value="<?= $product['id'] ?>"><?= $product['id'] ?> - <?= $product['name'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="input-group">
                    <label for="shoe_size">Shoe Size: </label>
                    <select name="shoe_size" id="shoe_size" class="full-width" required onchange="fetchShoeStock()">
                        <option value="" disabled selected>Select Size</option> <!-- Default null option -->
                        <?php foreach ($sizes as $size): ?>
                            <option value="<?= $size ?>"><?= $size ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="input-group">
                    <label for="current_stock">Stock: </label>
                    <button type="button" onclick="adjustStock(-1)">-</button>
                    <input type="number" id="current_stock" name="current_stock" value="0" min="0" oninput="updateHiddenStock()" />
                    <button type="button" onclick="adjustStock(1)">+</button>
                </div>
                <input type="hidden" name="shoe_count" id="shoe_count" value="0">
                <button type="submit">Update Stock</button>
            </form>
        </div>
        <script>
            function fetchShoeStock() {
                const productId = document.getElementById('product_id').value;
                const shoeSize = document.getElementById('shoe_size').value;

                if (productId && shoeSize) {
                    fetch(`BackEnd/fetch_shoe_stock.php?product_id=${productId}&shoe_size=${shoeSize}`)
                        .then(response => response.json())
                        .then(data => {
                            const stock = data.stock || 0;
                            document.getElementById('current_stock').value = stock;
                            document.getElementById('shoe_count').value = stock;
                        });
                }
            }

            function adjustStock(amount) {
                const currentStockElement = document.getElementById('current_stock');
                const shoeCountInput = document.getElementById('shoe_count');
                let currentStock = parseInt(currentStockElement.value);

                currentStock = Math.max(0, currentStock + amount); // Prevent negative stock
                currentStockElement.value = currentStock;
                shoeCountInput.value = currentStock;
            }

            function updateHiddenStock() {
                const currentStockElement = document.getElementById('current_stock');
                const shoeCountInput = document.getElementById('shoe_count');
                shoeCountInput.value = currentStockElement.value;
            }
        </script>
    </section>

</body>
</html>

<?php $conn->close(); ?>
