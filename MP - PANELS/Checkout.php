<?php
session_start();

// Initialize variables
$errors = [];
$subtotal = 0;
$shipping = 100.00;

// Sample cart data
$_SESSION['cart'] = [
    [
        'name' => 'Nike Dunk Low GS \'Panda\'',
        'price' => 5895.00,
        'quantity' => 1,
        'image' => '/images/nike-panda.jpg'
    ]
];

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Form validation logic 
    if (empty($errors)) {
        $_SESSION['cart'] = [];
        header("Location: order-success.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - SneakerHead</title>
    <style>

        body {
            background-color: #f3f4f6; 
            margin: 0; 
            font-family: Arial, sans-serif;
        }
        
        /* For the Header Styles */
        header {
            background-color: white; 
            padding: 16px; 
            border-bottom: 1px solid #e5e7eb;
        }

        .header-container {
            max-width: 1200px; 
            margin: 0 auto; 
            display: flex; 
            justify-content: space-between; 
            align-items: center;
        }

        .site-title {
            margin: 0; 
            color: #426b1f;
            font-family: Newsreader;
            font-size: xx-large;
        }

        .nav-link {
            margin-right: 16px; 
            color: #4B5563; 
            text-decoration: none;
        }

        .log-out-btn {
            padding: 8px 16px; 
            background-color: #4D7C0F; 
            color: white; 
            text-decoration: none; 
            border-radius: 4px;
        }
        
        /*For the Layout Styles */
        .main-container {
            max-width: 1200px; 
            margin: 32px auto; 
            padding: 0 16px;
        }
        .page-title {
            font-size: 32px; 
            margin-bottom: 32px;
        }
        .checkout-grid {
            display: grid; 
            grid-template-columns: 2fr 1fr; 
            gap: 32px;
        }
        
        /* Section Styles */
        .checkout-section {
            margin-bottom: 24px;
        }
        .section-title {
            font-size: 20px; 
            margin-bottom: 16px;
        }
        
        /* Form Styles */
        .radio-button {
            display: inline-flex;
            align-items: center;
            background-color: #4D7C0F;
            color: white;
            padding: 8px 16px;
            border-radius: 4px;
            cursor: pointer;
            margin-right: 8px;
        }

        .radio-button input[type="radio"] {
            margin-right: 8px;
        }

        .radio-button-full {
            width: 100%; 
            box-sizing: border-box;
        }

        .form-input {
            width: 100%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
            margin-bottom: 12px;
        }

        .form-select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
            margin-bottom: 12px;
            background-color: white;
        }

        .complete-order-btn {
            width: 100%;
            padding: 12px;
            background-color: #4D7C0F;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            margin-top: 24px;
        }

        .complete-order-btn:hover {
            background-color: #3F6C0F;
        }
        
        .checkbox-label {
            display: flex; 
            align-items: center; 
            margin-bottom: 12px;
        }

        .checkbox {
            margin-right: 8px;
        }

        .form-grid {
            display: grid; 
            grid-template-columns: 1fr 1fr; 
            gap: 12px;
        }

        .textarea {
            height: 100px; 
            resize: vertical;
        }
        
        /* Order Summary Styles */
        .order-summary {
            background-color: white; 
            padding: 24px; 
            border-radius: 8px;
        }

        .product-item {
            display: flex; 
            justify-content: space-between; 
            margin-bottom: 16px;
        }

        .product-details {
            display: flex; 
            align-items: center;
        }

        .product-image-container {
            width: 64px; 
            height: 64px; 
            background-color: #f3f4f6; 
            margin-right: 16px;
        }

        .product-image {
            width: 100%; 
            height: 100%; 
            object-fit: cover;
        }

        .product-name {
            margin: 0; 
            font-size: 16px;
        }

        .product-quantity {
            margin: 4px 0; 
            color: #6B7280;
        }

        .product-price {
            margin: 0; 
            font-weight: bold;
        }

        .order-summary-divider {
            border-top: 1px solid #e5e7eb; 
            margin-top: 16px; 
            padding-top: 16px;
        }

        .summary-row {
            display: flex; 
            justify-content: space-between; 
            margin-bottom: 8px;
        }

        .total-row {
            display: flex; 
            justify-content: space-between; 
            margin-top: 16px; 
            font-weight: bold; 
            font-size: 18px;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header>
        <div class="header-container">
            <h1 class="site-title">SNEAKERHEADS</h1>
            <nav>
                <a href="#" class="nav-link">Home</a>
                <a href="#" class="nav-link">Shop</a>
                <a href="#" class="nav-link">Check Out</a>
                <a href="#" class="log-out-btn">Log out</a>
            </nav>
        </div>
    </header>

    <div class="main-container">
        <h1 class="page-title">Check out</h1>
        
        <div class="checkout-grid">
            <!-- Left Column -->
            <div>
                <!-- Contact Section -->
                <div class="checkout-section">
                    <h2 class="section-title">Contact</h2>
                    <input type="email" placeholder="Email Address" class="form-input">
                    <label class="checkbox-label">
                        <input type="checkbox" class="checkbox">
                        Email me with news and offers
                    </label>
                    <input type="tel" placeholder="Mobile Number" class="form-input">
                </div>

                <!-- Billing Address -->
                <div class="checkout-section">
                    <h2 class="section-title">Billing Address</h2>
                    <div class="radio-button">
                        <input type="radio" name="billing" checked>
                        <span>Same as shipping address</span>
                    </div>
                    <div class="radio-button">
                        <input type="radio" name="billing">
                        <span>Use different billing address</span>
                    </div>
                </div>

                <!-- Delivery -->
                <div class="checkout-section">
                    <h2 class="section-title">Delivery</h2>
                    <div class="checkout-section">
                        <div class="radio-button">
                            <input type="radio" name="delivery" checked>
                            <span>Ship/Deliver</span>
                        </div>
                        <div class="radio-button">
                            <input type="radio" name="delivery">
                            <span>Pickup in Store</span>
                        </div>
                    </div>

                    <select class="form-select">
                        <option>Philippines</option>
                    </select>

                    <div class="form-grid">
                        <input type="text" placeholder="First Name (Optional)" class="form-input">
                        <input type="text" placeholder="Last Name" class="form-input">
                    </div>

                    <input type="text" placeholder="Address" class="form-input">
                    <input type="text" placeholder="Apartment, Suite, etc. (Optional)" class="form-input">

                    <div class="form-grid">
                        <input type="text" placeholder="Postal Code" class="form-input">
                        <input type="text" placeholder="City" class="form-input">
                    </div>

                    <select class="form-select">
                        <option>Metro Manila</option>
                    </select>

                    <label class="checkbox-label">
                        <input type="checkbox" class="checkbox">
                        Save this information for next time
                    </label>

                    <textarea placeholder="Instructions/Notes (Optional)" class="form-input textarea"></textarea>
                </div>

                <!-- Payment -->
                <div class="checkout-section">
                    <h2 class="section-title">Payment</h2>
                    <div class="form-grid">
                        <div class="radio-button radio-button-full">
                            <input type="radio" name="payment" checked>
                            <span>Bank Deposit</span>
                        </div>
                        <div class="radio-button radio-button-full">
                            <input type="radio" name="payment">
                            <span>GCash</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column - Order Summary -->
            <div class="order-summary">
                <h2 class="section-title">Order Summary</h2>
                
                <?php foreach ($_SESSION['cart'] as $item): 
                    $subtotal += $item['price'] * $item['quantity'];
                ?>
                    <div class="product-item">
                    <div class="product-details">
                        <div class="product-image-container">
                            <img src="<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" 
                                class="product-image">
                        </div>
                        <div>
                            <h3 class="product-name"><?php echo htmlspecialchars($item['name']); ?></h3>
                            <p class="product-quantity">Quantity: <?php echo $item['quantity']; ?></p>
                        </div>
                    </div>
                    <p class="product-price">₱<?php echo number_format($item['price'] * $item['quantity'], 2); ?></p>
                </div>
                <?php endforeach; ?>

                <div class="order-summary-divider">
                    <div class="summary-row">
                        <span>Subtotal</span>
                        <span>₱<?php echo number_format($subtotal, 2); ?></span>
                    </div>
                    <div class="summary-row">
                        <span>Shipping</span>
                        <span>₱<?php echo number_format($shipping, 2); ?></span>
                    </div>
                    <div class="total-row">
                        <span>TOTAL</span>
                        <span>₱<?php echo number_format($subtotal + $shipping, 2); ?></span>
                    </div>
                </div>

                <button type="submit" class="complete-order-btn">
                    Complete Order
                </button>
            </div>
        </div>
    </div>
</body>
</html></div>
