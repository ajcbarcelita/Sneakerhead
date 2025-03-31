<?php
    session_start();
    if (!isset( $_SESSION["role_id"]) || $_SESSION["role_id"] != 1) {
        header("Location: login.php");
        exit();
    }
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sneakerheads Admin</title>
    <link rel="stylesheet" href="show_reports.css">
</head>

<body>
    <header>
        <h1>SNEAKERHEADS (Server Side)</h1>
        <nav>
            <a href="server_product.php">Products</a>
            <a href="server_promo_codes.php">Promo Codes</a>
            <a href="#">Reports</a>
            <form action="logout-handler.php" method="post" style="display:inline;">
                <button type="submit" class="sign-in">Sign Out</button>
            </form>
        </nav>
    </header>

    <?php
    include 'db_conn.php';
    include 'generate_reports.php';

    // Generate both XML reports before showing details
    generateSalesReport($conn);
    generateInventoryReport($conn);

    // Display Inventory Report
    $xmlFile = 'BackEnd/inventory_report.xml';

    if (file_exists($xmlFile)) {
        $xml = simplexml_load_file($xmlFile);

        if ($xml === false) {
            echo "Failed to load XML file.";
        } else {
            // Group shoes by brand
            $brands = [];
            foreach ($xml->shoe as $shoe) {
                // Gets the brand from the shoe node
                $brand = (string)$shoe->brand;
                // If brand is not yet in the array, create an empty array
                if (!isset($brands[$brand])) {
                    $brands[$brand] = [];
                }
                // Add shoe to the brand array
                $brands[$brand][] = $shoe;
            }

            // Header
            echo '
               <head>
                   <title>Sneaker Inventory</title>
                   <link rel="stylesheet" href="show_reports.css"> 
               </head>
               <body>
                   <h2>~ Sneaker Inventory Report ~</h2>';

            // For each brand display shoes accordingly
            foreach ($brands as $brand => $shoes) {
                echo '<div class="brand-section">';
                echo '<div class="brand-header"><h3>' . $brand . '</h3></div>';
                echo '<table>';
                echo '<tr><th>Model</th><th>Price</th><th>Available Sizes</th><th>Total Stock</th></tr>';

                foreach ($shoes as $shoe) {
                    $totalStock = 0;
                    $sizesTable = '<table class="sizes-table"><tr><th>Size</th><th>Stock</th></tr>';

                    // Display available sizes from size node
                    foreach ($shoe->sizes->size as $size) {
                        $sizesTable .= '<tr><td>' . $size->us_size . '</td><td>' . $size->stock . '</td></tr>';
                        $totalStock += (int)$size->stock;
                    }
                    $sizesTable .= '</table>';

                    echo '<tr>';
                    echo '<td>' . $shoe->name . '</td>';
                    echo '<td class="price">₱' . number_format((float)$shoe->price, 2) . '</td>';
                    echo '<td>' . $sizesTable . '</td>';
                    echo '<td>' . $totalStock . 'pc.' . '</td>';
                    echo '</tr>';
                }

                echo '</table></div>';
            }
        }
    } else {
        echo "XML file not found.";
    }

    // Sales Report Section
    echo '<h2>~ Sales Report ~</h2>';
    $salesXmlFile = 'BackEnd/sales_report.xml';

    if (file_exists($salesXmlFile)) {
        $salesXml = simplexml_load_file($salesXmlFile);

        if ($salesXml === false) {
            echo "Failed to load sales XML file.";
        } else {
            // Display total revenue
            echo '<div class="sales-section">';
            echo '<div class="sales-header"><h3>Total Revenue: ₱' . number_format((float)$salesXml->total_revenue, 2) . '</h3></div>';

            // Display top-selling products
            echo '<h3>Top Selling Products</h3>';
            echo '<table>';
            echo '<tr><th>Product Name</th><th>Total Quantity Sold</th></tr>';
            foreach ($salesXml->top_selling_products->product as $product) {
                echo '<tr>';
                echo '<td>' . $product->product_name . '</td>';
                echo '<td>' . $product->total_quantity_sold . '</td>';
                echo '</tr>';
            }
            echo '</table>';

            // Display daily sales
            echo '<h3>Daily Sales</h3>';
            echo '<table>';
            echo '<tr><th>Sale Date</th><th>Daily Revenue</th></tr>';
            foreach ($salesXml->daily_sales->sale as $sale) {
                echo '<tr>';
                echo '<td>' . $sale->sale_date . '</td>';
                echo '<td>₱' . number_format((float)$sale->daily_revenue, 2) . '</td>';
                echo '</tr>';
            }
            echo '</table>';

            // Display weekly sales
            echo '<h3>Weekly Sales</h3>';
            echo '<table>';
            echo '<tr><th>Sale Week</th><th>Weekly Revenue</th></tr>';
            foreach ($salesXml->weekly_sales->sale as $sale) {
                echo '<tr>';
                echo '<td>' . $sale->sale_week . '</td>';
                echo '<td>₱' . number_format((float)$sale->weekly_revenue, 2) . '</td>';
                echo '</tr>';
            }
            echo '</table>';

            // Display monthly sales
            echo '<h3>Monthly Sales</h3>';
            echo '<table>';
            echo '<tr><th>Sale Month</th><th>Monthly Revenue</th></tr>';
            foreach ($salesXml->monthly_sales->sale as $sale) {
                echo '<tr>';
                echo '<td>' . $sale->sale_month . '</td>';
                echo '<td>₱' . number_format((float)$sale->monthly_revenue, 2) . '</td>';
                echo '</tr>';
            }
            echo '</table>';
            echo '</div>';
        }
    } else {
        echo "Sales XML file not found.";
    }
    ?>

</body>

</html>