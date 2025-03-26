<?php
session_start();
// Removed clearing of promo code-related session data
require "db_conn.php";
require_once 'promo_code_handler.php'; // Updated file name
// I removed the comment for hiding the login part of the page
if (!isset($_SESSION['id'])) {
    // Redirect to login page if not logged in
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['id'];
$user_query = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($user_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user_result = $stmt->get_result();
$user = $user_result->fetch_assoc();

// Check if user has a shopping cart, if not create one
$cart_check_query = "SELECT cart_id FROM shopping_cart WHERE user_id = ?";
$stmt = $conn->prepare($cart_check_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$cart_check_result = $stmt->get_result();

if ($cart_check_result->num_rows == 0) {

    // This Create a new cart for the user
    $create_cart_query = "INSERT INTO shopping_cart (user_id) VALUES (?)";
    $stmt = $conn->prepare($create_cart_query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
}

// Get cart items from database
$cart_query = "SELECT sc.cart_id, sci.shoe_id, sci.shoe_us_size, sci.quantity, sci.price_at_addition, 
               s.name, s.brand, si.file_path AS image_path
               FROM shopping_cart sc
               JOIN shopping_cart_items sci ON sc.cart_id = sci.cart_id
               JOIN shoes s ON sci.shoe_id = s.id
               JOIN shoe_images si ON s.id = si.shoe_id
               WHERE sc.user_id = ?";
$stmt = $conn->prepare($cart_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$cart_result = $stmt->get_result();

$cart_items = [];
$subtotal = 0;
$shipping = 100.00;

if ($cart_result->num_rows > 0) {
    while ($item = $cart_result->fetch_assoc()) {
        $cart_items[] = $item;
        $subtotal += $item['price_at_addition'] * $item['quantity'];
    }
}

// Get promo codes
$promo_query = "SELECT * FROM promo_codes WHERE is_active = 1 AND is_deleted = 0";
$promo_result = $conn->query($promo_query);
$promo_codes = [];

if ($promo_result && $promo_result->num_rows > 0) {
    while ($promo = $promo_result->fetch_assoc()) {
        $promo_codes[] = $promo;
    }
}

// Check for order success message
$order_success = isset($_SESSION['order_success']) ? $_SESSION['order_success'] : false;
$order_id = isset($_SESSION['order_id']) ? $_SESSION['order_id'] : '';

// Clear success messages after displaying
if ($order_success) {
    unset($_SESSION['order_success']);
    unset($_SESSION['order_id']);
}

// Check for error messages
$errors = isset($_SESSION['checkoutErrors']) ? $_SESSION['checkoutErrors'] : [];
if (!empty($errors)) {
    unset($_SESSION['checkoutErrors']);
}

$promo_code = isset($_SESSION['promo_code']) ? $_SESSION['promo_code'] : '';
$discount = isset($_SESSION['discount']) ? $_SESSION['discount'] : 0;
$promo_message = isset($_SESSION['promo_message']) ? $_SESSION['promo_message'] : '';
$promo_error = isset($_SESSION['promo_error']) ? $_SESSION['promo_error'] : '';
$new_total = $subtotal + $shipping - $discount;

// Clear promo messages after displaying
unset($_SESSION['promo_message'], $_SESSION['promo_error']);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - SneakerHead</title>
    <link rel="stylesheet" href="checkout.css">
    <style>
        /* Additional styles if needed */
    </style>
</head>

<body>
    <!-- Header -->
    <header>
        <div class="header-container">
            <h1 class="site-title">SNEAKERHEADS</h1>
            <nav>
                <a href="index.php" class="nav-link">Home</a>
                <a href="index.php#products" class="nav-link">Shop</a>
                <a href="profile_page.php" class="nav-link">My Profile</a>
                <a href="logout.php" class="log-out-btn">Log out</a>
            </nav>
        </div>
    </header>

    <div class="main-container">
        <h1 class="page-title">Check out</h1>

        <?php if ($order_success): ?>
            <div class="success-message">
                <p>Your order has been placed successfully! Order ID: <?php echo $order_id; ?></p>
                <p>You will be redirected to your profile page shortly...</p>
            </div>
            <script>
                setTimeout(function () {
                    window.location.href = "profile_page.php";
                }, 3000);
            </script>
        <?php endif; ?>

        <?php if (!empty($errors) && isset($errors['system'])): ?>
            <div class="error-message">
                <p><?php echo $errors['system']; ?></p>
            </div>
        <?php endif; ?>

        <div class="checkout-grid">
            <!-- Left Column -->
            <div>
                <form action="checkout-handler.php" method="POST" id="checkout-form">
                    <!-- Add hidden field for cart_id -->
                    <?php if (!empty($cart_items)): ?>
                        <input type="hidden" name="cart_id" value="<?php echo $cart_items[0]['cart_id']; ?>">
                    <?php endif; ?>

                    <!-- Contact Section -->
                    <div class="checkout-section">
                        <h2 class="section-title">Contact</h2>
                        <input type="email" name="email" placeholder="Email Address" class="form-input"
                            value="<?php echo isset($user['email']) ? $user['email'] : ''; ?>" required>
                        <?php if (isset($errors['email'])): ?>
                            <div class="error-message"><?php echo $errors['email']; ?></div>
                        <?php endif; ?>

                        <input type="tel" name="phone" placeholder="Mobile Number" class="form-input"
                            value="<?php echo isset($user['phone_no']) ? $user['phone_no'] : ''; ?>" required>
                        <?php if (isset($errors['phone'])): ?>
                            <div class="error-message"><?php echo $errors['phone']; ?></div>
                        <?php endif; ?>
                    </div>

                    <!-- Billing Address -->
                    <div class="checkout-section">
                        <h2 class="section-title">Billing Address</h2>
                        <div class="radio-button" style="opacity: 0.7;">
                            <input type="radio" name="billing" value="same" checked>
                            <span>Same as shipping address</span>
                            <input type="hidden" name="billing" value="same">
                        </div>
                    </div>

                    <!-- Delivery -->
                    <div class="checkout-section">
                        <h2 class="section-title">Delivery</h2>
                        <div class="radio-section">
                        <div class="radio-button" style="opacity: 0.7;">
                                <input type="radio" name="delivery" value="ship" checked>
                                <span>Ship/Deliver</span>
                            </div>
                        </div>

                        <select name="country" class="form-select">
                            <option value="Philippines">Philippines</option>
                        </select>

                        <div class="form-grid">
                            <input type="text" name="firstName" placeholder="First Name" class="form-input"
                                value="<?php echo isset($user['fname']) ? $user['fname'] : ''; ?>" required>
                            <?php if (isset($errors['firstName'])): ?>
                                <div class="error-message"><?php echo $errors['firstName']; ?></div>
                            <?php endif; ?>

                            <input type="text" name="middleName" placeholder="Middle Name" class="form-input"
                                value="<?php echo isset($user['mname']) ? $user['mname'] : ''; ?>">
                        </div>

                        <input type="text" name="lastName" placeholder="Last Name" class="form-input"
                            value="<?php echo isset($user['lname']) ? $user['lname'] : ''; ?>" required>
                        <?php if (isset($errors['lastName'])): ?>
                            <div class="error-message"><?php echo $errors['lastName']; ?></div>
                        <?php endif; ?>

                        <input type="text" name="address" placeholder="Address" class="form-input"
                            value="<?php echo isset($user['address_line']) ? $user['address_line'] : ''; ?>" required>
                        <?php if (isset($errors['address'])): ?>
                            <div class="error-message"><?php echo $errors['address']; ?></div>
                        <?php endif; ?>

                        <div class="form-grid">
                            <input type="text" name="city" placeholder="City" class="form-input"
                                value="<?php echo isset($user['city_municipality']) ? $user['city_municipality'] : ''; ?>"
                                required>
                            <?php if (isset($errors['city'])): ?>
                                <div class="error-message"><?php echo $errors['city']; ?></div>
                            <?php endif; ?>

                            <input type="text" name="province" placeholder="Province" class="form-input"
                                value="<?php echo isset($user['province']) ? $user['province'] : ''; ?>" required>
                            <?php if (isset($errors['province'])): ?>
                                <div class="error-message"><?php echo $errors['province']; ?></div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Promo Code Section -->
                    <div class="checkout-section">
                        <h2 class="section-title">Promo Code</h2>
                        <div class="form-grid">
                            <input type="text" name="promo_code" placeholder="Enter Promo Code" class="form-input" value="<?php echo htmlspecialchars($promo_code); ?>">
                            <button type="submit" name="apply_promo" value="1" class="apply-btn">Apply</button>
                        </div>
                        <?php if (!empty($promo_error)): ?>
                            <p class="error-message"><?php echo $promo_error; ?></p>
                        <?php elseif (!empty($promo_code)): ?>
                            <p class="success-message">Promo code applied successfully! Discount: ₱<?php echo number_format($discount, 2); ?></p>
                        <?php endif; ?>
                    </div>
                </form>
            </div>

            <!-- Right Column - Order Summary -->
            <div class="order-summary">
                <h2 class="section-title">Order Summary</h2>

                <div id="cart-items">
                    <?php if (count($cart_items) > 0): ?>
                        <?php foreach ($cart_items as $item): ?>
                            <div class="product-item">
                                <div class="product-details">
                                    <div class="product-image-container">
                                        <img src="<?php echo $item['image_path']; ?>" alt="<?php echo $item['name']; ?>" class="product-image">
                                    </div>
                                    <div>
                                        <h3 class="product-name"><?php echo $item['name']; ?></h3>
                                        <p class="product-quantity">Size: <?php echo $item['shoe_us_size']; ?> | Quantity: <?php echo $item['quantity']; ?></p>
                                    </div>
                                </div>
                                <p class="product-price"> ₱<?php echo number_format($item['price_at_addition'] * $item['quantity'], 2); ?></p>
                            </div>
                        <?php endforeach; ?>

                    <?php else: ?>
                        <p>Your cart is empty.</p>
                    <?php endif; ?>

                </div>

                <div class="order-summary-divider">
                    <div class="summary-row">
                        <span>Subtotal</span>
                        <span id="subtotal">₱<?php echo number_format($subtotal, 2); ?></span>
                    </div>
                    <div class="summary-row">
                        <span>Shipping</span>
                        <span id="shipping">₱<?php echo number_format($shipping, 2); ?></span>
                    </div>
                    <?php if ($discount > 0): ?>
                        <div class="summary-row">
                            <span>Discount</span>
                            <span id="discount">-₱<?php echo number_format($discount, 2); ?></span>
                        </div>
                    <?php endif; ?>
                    <div class="total-row">
                        <span>TOTAL</span>
                        <span id="total">₱<?php echo number_format($new_total, 2); ?></span>
                    </div>
                </div>
                <button type="submit" class="complete-order-btn" form="checkout-form" <?php echo count($cart_items) == 0 ? 'disabled' : ''; ?>>
                    Complete Order
                </button>
            </div>
        </div>
    </div>
    <script>
        document.querySelector('.apply-btn').addEventListener('click', function (e) {
            e.preventDefault();
            const promoCode = document.querySelector('input[name="promo_code"]').value.trim();
            const subtotal = <?php echo $subtotal; ?>;
            const shipping = <?php echo $shipping; ?>;

            if (!promoCode) {
                alert('Please enter a promo code.');
                return;
            }

            fetch('checkout-handler.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `apply_promo=1&promo_code=${encodeURIComponent(promoCode)}&subtotal=${subtotal}`
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! Status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                if (data.valid) {
                    location.reload(); // Reload the page on success
                } else {
                    location.reload(); // Reload the page on error
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while applying the promo code. Please try again.');
            });
        });
    </script>
</body>

</html>
