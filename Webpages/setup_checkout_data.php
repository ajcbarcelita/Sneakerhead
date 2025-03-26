<?php
// filepath: c:\xampp\htdocs\myapp\Sneakerhead-main\Webpages\setup_checkout_data.php
session_start();
require_once 'db_conn.php';

// Insert data only for checkout testing
try {
    // Set a test user ID for checkout
    $_SESSION['id'] = 2;
    
    // 1. Make sure we have test product data
    // Check if products already exist
    $product_check = $conn->query("SELECT COUNT(*) as count FROM shoes WHERE id IN (1, 2)");
    $product_count = $product_check->fetch_assoc()['count'];
    
    // Insert products if needed
    if ($product_count < 2) {
        $conn->query("INSERT IGNORE INTO shoes (id, name, brand, price) 
                     VALUES (1, 'Air Force 1', 'Nike', 7395.00),
                            (2, 'Dunk Low', 'Nike', 7395.00)");
                     
        $conn->query("INSERT IGNORE INTO shoe_images (shoe_id, image_name, file_path) 
                     VALUES (1, 'airforce1.jpg', 'images/airforce1.jpg'),
                            (2, 'dunklow.jpg', 'images/dunklow.jpg')");
        
        // Create sample images if they don't exist
        if (!file_exists('images/airforce1.jpg')) {
            if (!is_dir('images')) {
                mkdir('images', 0777, true);
            }
            // Create placeholder image
            $img = imagecreate(300, 300);
            $backgroundColor = imagecolorallocate($img, 240, 240, 240);
            $textColor = imagecolorallocate($img, 0, 0, 0);
            imagestring($img, 5, 100, 140, 'Nike Air Force 1', $textColor);
            imagejpeg($img, 'images/airforce1.jpg');
            imagedestroy($img);
        }
        
        if (!file_exists('images/dunklow.jpg')) {
            if (!is_dir('images')) {
                mkdir('images', 0777, true);
            }
            // Create placeholder image
            $img = imagecreate(300, 300);
            $backgroundColor = imagecolorallocate($img, 240, 240, 240);
            $textColor = imagecolorallocate($img, 0, 0, 0);
            imagestring($img, 5, 100, 140, 'Nike Dunk Low', $textColor);
            imagejpeg($img, 'images/dunklow.jpg');
            imagedestroy($img);
        }
    }
    
    // 2. Make sure we have shoe sizes and inventory
    $conn->query("INSERT IGNORE INTO ref_us_sizes (shoe_size) VALUES 
                 (8), (9), (10)");
                 
    $conn->query("INSERT IGNORE INTO shoe_size_inventory (shoe_id, shoe_us_size, stock) 
                 VALUES (1, 8, 10), (1, 9, 15), (1, 10, 12),
                        (2, 8, 8), (2, 9, 12), (2, 10, 14)");
    
    // 3. Make sure user has a shopping cart
    $cart_check = $conn->query("SELECT cart_id FROM shopping_cart WHERE user_id = 2");
    if ($cart_check->num_rows == 0) {
        $conn->query("INSERT INTO shopping_cart (user_id) VALUES (2)");
    }
    
    // Get the cart ID
    $cart_result = $conn->query("SELECT cart_id FROM shopping_cart WHERE user_id = 2");
    $cart = $cart_result->fetch_assoc();
    $cart_id = $cart['cart_id'];
    
    // 4. Clear existing cart items and add new ones
    $conn->query("DELETE FROM shopping_cart_items WHERE cart_id = $cart_id");
    $conn->query("INSERT INTO shopping_cart_items (cart_id, shoe_id, shoe_us_size, quantity, price_at_addition) 
                 VALUES ($cart_id, 1, 9, 1, 7395.00),
                        ($cart_id, 2, 8, 2, 7395.00)");
    
    // 5. Make sure we have promo codes for testing
    $conn->query("INSERT IGNORE INTO promo_codes (promo_code, discount_type, discount_value, min_purchase, is_active, is_deleted) 
                 VALUES ('SNEAK10', 'Percentage', 10.00, 0.00, 1, 0),
                        ('BOOST1K', 'Fixed', 1000.00, 7000.00, 1, 0),
                        ('GRAB1500', 'Fixed', 1500.00, 10000.00, 1, 0)");
    
    // Success message and redirect to checkout
    $_SESSION['setup_message'] = "Test data has been added successfully!";
    header("Location: Checkout.php");
    exit();
    
} catch (Exception $e) {
    // Display error and link to checkout
    echo '<!DOCTYPE html>
    <html>
    <head>
        <title>Setup Error</title>
        <style>
            body { font-family: Arial, sans-serif; margin: 20px; line-height: 1.6; }
            .error { color: #a94442; background-color: #f2dede; border: 1px solid #ebccd1; padding: 15px; border-radius: 4px; margin-bottom: 20px; }
            .button { display: inline-block; background-color: #4D7C0F; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px; }
        </style>
    </head>
    <body>
        <h1>Error Setting Up Test Data</h1>
        <div class="error">
            <p>' . $e->getMessage() . '</p>
        </div>
        <p>You can still try to proceed to checkout, but it may not work correctly without test data.</p>
        <a href="Checkout.php" class="button">Go to Checkout</a>
    </body>
    </html>';
}
?>