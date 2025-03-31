<?php

/**
 * Generate the XML Inventory report
 */
function generateInventoryReport($conn)
{
    // Fetch inventory data with grouped sizes
    // Concatenates size and stock for each shoe
    $query = "
        SELECT 
            s.id AS shoe_id,
            s.brand,
            s.name,
            s.price,
            GROUP_CONCAT(
                CONCAT(
                    '<size>',
                    '<us_size>', i.shoe_us_size, '</us_size>',
                    '<stock>', i.stock, '</stock>',
                    '</size>'
                )
            ) AS sizes
        FROM 
            shoe_size_inventory i
        JOIN 
            shoes s ON i.shoe_id = s.id
        GROUP BY 
            s.id
        ORDER BY 
            s.brand, s.name;
    ";
    $result = $conn->query($query);

    // Create XML structure
    $xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><inventory></inventory>');

    // DTD declaration
    $xml->addAttribute('dtd:noNamespaceSchemaLocation', 'inventory.dtd');

    // Add shoes to XML
    while ($row = $result->fetch_assoc()) {
        $shoe = $xml->addChild('shoe');
        $shoe->addChild('brand', htmlspecialchars($row['brand']));
        $shoe->addChild('name', htmlspecialchars($row['name']));
        $shoe->addChild('price', htmlspecialchars($row['price']));

        // Sizes element to contain --> size and stock for shoe
        $sizes = $shoe->addChild('sizes');
        // Converts concatenated 'sizes' string to XML 
        $size_xml = simplexml_load_string("<sizes>{$row['sizes']}</sizes>");
        foreach ($size_xml->size as $size) {
            $size_element = $sizes->addChild('size');
            $size_element->addChild('us_size', (string)$size->us_size);
            $size_element->addChild('stock', (string)$size->stock);
        }
    }

    // Save XML to file
    $xml->asXML('BackEnd/inventory_report.xml');
}

/**
 * Generate the XML Sales Report
 */
function generateSalesReport($conn)
{
    // Produces detailed sales summaries (daily/weekly/monthly) using MySQL queries. 
    // Includes metrics like total revenue, and top-selling products by most sold products. 
    //Reports are exportable with XML output for integration with analytics tools.

    // Fetch total revenue
    $total_revenue_query = "SELECT SUM(total_price) AS total_revenue FROM orders;";
    $total_revenue_result = $conn->query($total_revenue_query);
    $total_revenue = $total_revenue_result->fetch_assoc()['total_revenue'];

    // Fetch top-selling products -- 5 Most frequent products sold 
    $top_selling_query = "
        SELECT 
            s.name AS product_name,
            SUM(oi.quantity) AS total_quantity_sold,
            (
                SELECT SUM(o.total_price)
                FROM orders o
                WHERE o.order_id = oi.order_id
            )   AS total_revenue
        FROM 
            order_items oi
        JOIN 
            shoes s ON oi.shoe_id = s.id
        GROUP BY 
            s.id
        ORDER BY 
            total_quantity_sold DESC
        LIMIT 5; 
    ";
    $top_selling_result = $conn->query($top_selling_query);

    // Fetch daily sales
    $daily_sales_query = "SELECT DATE(created_at) AS sale_date, SUM(total_price) AS daily_revenue FROM orders GROUP BY sale_date;";
    $daily_sales_result = $conn->query($daily_sales_query);

    // Fetch weekly sales
    $weekly_sales_query = "SELECT CONCAT(YEAR(created_at), '-W', WEEK(created_at, 1)) AS sale_week, SUM(total_price) AS weekly_revenue FROM orders GROUP BY sale_week;";
    $weekly_sales_result = $conn->query($weekly_sales_query);

    // Fetch monthly sales
    $monthly_sales_query = "SELECT DATE_FORMAT(created_at, '%Y-%m') AS sale_month, SUM(total_price) AS monthly_revenue FROM orders GROUP BY sale_month;";
    $monthly_sales_result = $conn->query($monthly_sales_query);


    // Create XML structure
    $xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><sales_report></sales_report>');

    // DTD declaration
    $xml->addAttribute('dtd:noNamespaceSchemaLocation', 'sales_report.dtd');

    // Add total revenue
    $xml->addChild('total_revenue', $total_revenue);

    // Add top-selling products
    $top_selling = $xml->addChild('top_selling_products');
    while ($row = $top_selling_result->fetch_assoc()) {
        $product = $top_selling->addChild('product');
        $product->addChild('product_name', htmlspecialchars($row['product_name']));
        $product->addChild('total_quantity_sold', $row['total_quantity_sold']);
        $product->addChild('total_revenue', $row['total_revenue']);
    }

    // Add daily sales
    $daily_sales = $xml->addChild('daily_sales');
    while ($row = $daily_sales_result->fetch_assoc()) {
        $sale = $daily_sales->addChild('sale');
        $sale->addChild('sale_date', $row['sale_date']);
        $sale->addChild('daily_revenue', $row['daily_revenue']);
    }

    // Add weekly sales
    $weekly_sales = $xml->addChild('weekly_sales');
    while ($row = $weekly_sales_result->fetch_assoc()) {
        $sale = $weekly_sales->addChild('sale');
        $sale->addChild('sale_week', $row['sale_week']);
        $sale->addChild('weekly_revenue', $row['weekly_revenue']);
    }

    // Add monthly sales
    $monthly_sales = $xml->addChild('monthly_sales');
    while ($row = $monthly_sales_result->fetch_assoc()) {
        $sale = $monthly_sales->addChild('sale');
        $sale->addChild('sale_month', $row['sale_month']);
        $sale->addChild('monthly_revenue', $row['monthly_revenue']);
    }

    // Save XML to file
    $xml->asXML('BackEnd/sales_report.xml');
}
