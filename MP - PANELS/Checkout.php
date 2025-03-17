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
    // Form validation logic remains the same...
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
        }
        .complete-order-btn:hover {
            background-color: #3F6C0F;
        }
    </style>
</head>
<body style="background-color: #f3f4f6; margin: 0; font-family: Arial, sans-serif;">
    <!-- Header -->
    <header style="background-color: white; padding: 16px; border-bottom: 1px solid #e5e7eb;">
        <div style="max-width: 1200px; margin: 0 auto; display: flex; justify-content: space-between; align-items: center;">
            <h1 style="margin: 0; color: #4D7C0F;">SNEAKERHEADS</h1>
            <nav>
                <a href="index.php" style="margin-right: 16px; color: #4B5563; text-decoration: none;">Home</a>
                <a href="#" style="margin-right: 16px; color: #4B5563; text-decoration: none;">Shop</a>
                <a href="#" style="margin-right: 16px; color: #4B5563; text-decoration: none;">Check Out</a>
                <a href="#" style="padding: 8px 16px; background-color: #4D7C0F; color: white; text-decoration: none; border-radius: 4px;">Sign out</a>
            </nav>
        </div>
    </header>

    <div style="max-width: 1200px; margin: 32px auto; padding: 0 16px;">
        <h1 style="font-size: 32px; margin-bottom: 32px;">Check out</h1>
        
        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 32px;">
            <!-- Left Column -->
            <div>
                <!-- Contact Section -->
                <div style="margin-bottom: 24px;">
                    <h2 style="font-size: 20px; margin-bottom: 16px;">Contact</h2>
                    <input type="email" placeholder="Email Address" class="form-input">
                    <label style="display: flex; align-items: center; margin-bottom: 12px;">
                        <input type="checkbox" style="margin-right: 8px;">
                        Email me with news and offers
                    </label>
                    <input type="tel" placeholder="Mobile Number" class="form-input">
                </div>

                <!-- Billing Address -->
                <div style="margin-bottom: 24px;">
                    <h2 style="font-size: 20px; margin-bottom: 16px;">Billing Address</h2>
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
                <div style="margin-bottom: 24px;">
                    <h2 style="font-size: 20px; margin-bottom: 16px;">Delivery</h2>
                    <div style="margin-bottom: 16px;">
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

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                        <input type="text" placeholder="First Name (Optional)" class="form-input">
                        <input type="text" placeholder="Last Name" class="form-input">
                    </div>

                    <input type="text" placeholder="Address" class="form-input">
                    <input type="text" placeholder="Apartment, Suite, etc. (Optional)" class="form-input">

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                        <input type="text" placeholder="Postal Code" class="form-input">
                        <input type="text" placeholder="City" class="form-input">
                    </div>

                    <select class="form-select">
                        <option>Metro Manila</option>
                    </select>

                    <label style="display: flex; align-items: center; margin-bottom: 12px;">
                        <input type="checkbox" style="margin-right: 8px;">
                        Save this information for next time
                    </label>

                    <textarea placeholder="Instructions/Notes (Optional)" class="form-input" style="height: 100px; resize: vertical;"></textarea>
                </div>

                <!-- Payment -->
                <div style="margin-bottom: 24px;">
                    <h2 style="font-size: 20px; margin-bottom: 16px;">Payment</h2>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                        <div class="radio-button" style="width: 100%; box-sizing: border-box;">
                            <input type="radio" name="payment" checked>
                            <span>Bank Deposit</span>
                        </div>
                        <div class="radio-button" style="width: 100%; box-sizing: border-box;">
                            <input type="radio" name="payment">
                            <span>GCash</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column - Order Summary -->
            <div style="background-color: white; padding: 24px; border-radius: 8px;">
                <h2 style="font-size: 20px; margin-bottom: 16px;">Order Summary</h2>
                
                <?php foreach ($_SESSION['cart'] as $item): 
                    $subtotal += $item['price'] * $item['quantity'];
                ?>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 16px;">
                        <div style="display: flex; align-items: center;">
                            <div style="width: 64px; height: 64px; background-color: #f3f4f6; margin-right: 16px;"></div>
                            <div>
                                <h3 style="margin: 0; font-size: 16px;"><?php echo htmlspecialchars($item['name']); ?></h3>
                                <p style="margin: 4px 0; color: #6B7280;">Quantity: <?php echo $item['quantity']; ?></p>
                            </div>
                        </div>
                        <p style="margin: 0; font-weight: bold;">₱<?php echo number_format($item['price'] * $item['quantity'], 2); ?></p>
                    </div>
                <?php endforeach; ?>

                <div style="border-top: 1px solid #e5e7eb; margin-top: 16px; padding-top: 16px;">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                        <span>Subtotal</span>
                        <span>₱<?php echo number_format($subtotal, 2); ?></span>
                    </div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                        <span>Shipping</span>
                        <span>₱<?php echo number_format($shipping, 2); ?></span>
                    </div>
                    <div style="display: flex; justify-content: space-between; margin-top: 16px; font-weight: bold; font-size: 18px;">
                        <span>TOTAL</span>
                        <span>₱<?php echo number_format($subtotal + $shipping, 2); ?></span>
                    </div>
                </div>

                <button type="submit" class="complete-order-btn" style="margin-top: 24px;">
                    Complete Order
                </button>
            </div>
        </div>
    </div>
</body>
</html>