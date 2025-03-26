<?php
require 'db_conn.php';
session_start();

if (!isset($_SESSION['id'])) {
    header("Location: login.php?error=Please log in to view your cart.");
    exit();
}

$user_id = $_SESSION['id'];

// Fetch cart items for the user
$stmt = $conn->prepare("
    SELECT sci.cart_item_id, s.name AS shoe_name, sci.shoe_us_size AS size, sci.quantity, sci.price_at_addition AS unit_price, 
           (sci.quantity * sci.price_at_addition) AS total_price, si.file_path AS image_path
    FROM shopping_cart_items sci
    JOIN shopping_cart sc ON sci.cart_id = sc.cart_id
    JOIN shoes s ON sci.shoe_id = s.id
    LEFT JOIN shoe_images si ON s.id = si.shoe_id
    WHERE sc.user_id = ?
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$cart_items = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SneakerHeads</title>
    <link rel="stylesheet" href="index.css">
    <link rel="stylesheet" href="cart.css">
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar">
        <div class="logo">SNEAKERHEADS</div>
        <ul class="nav-links">
            <li><a href="index.php">Home</a></li>
            <li><a href="Checkout.php">Check Out</a></li>
            <li><a href="profile_page.php">My Profile</a></li>
            <li><a href="cart.php">Cart</a></li>
            <li><a href="#" class="btn">Sign Out</a></li>
        </ul>
    </nav>

    <!-- Cart Section -->
    <div class="cart">
        <div class="cart-header">
            <span>Shopping Cart</span>
            <?php if (!empty($cart_items)): ?>
                <form action="remove_all_from_cart.php" method="POST" style="text-align: right; margin-bottom: 10px;">
                    <button type="submit" class="remove">Remove all</button>
                </form>
            <?php endif; ?>
        </div>
        
        <?php if (empty($cart_items)): ?>
            <p>Your cart is currently empty. Add items to your cart to see them here.</p>
        <?php else: ?>
            <?php 
            $total_price = 0;
            foreach ($cart_items as $item): 
                $total_price += $item['total_price'];
            ?>
                <div class="cart-item">
                    <img src="<?php echo htmlspecialchars($item['image_path']); ?>" alt="<?php echo htmlspecialchars($item['shoe_name']); ?>">
                    <div class="cart-item-info">
                        <div class="cart-item-name"><?php echo htmlspecialchars($item['shoe_name']); ?></div>
                        <div class="cart-item-size"><strong>Size:</strong> <?php echo htmlspecialchars($item['size']); ?></div> <!-- Display shoe size -->
                        <div class="cart-item-price">₱<?php echo number_format($item['unit_price'], 2); ?></div>
                    </div>
                    <div class="cart-item-actions">
                        <form action="update_cart_quantity.php" method="POST" style="display: inline;">
                            <input type="hidden" name="cart_item_id" value="<?php echo htmlspecialchars($item['cart_item_id']); ?>">
                            <button type="submit" name="action" value="decrease">-</button>
                        </form>
                        <span class="quantity"><?php echo htmlspecialchars($item['quantity']); ?></span>
                        <form action="update_cart_quantity.php" method="POST" style="display: inline;">
                            <input type="hidden" name="cart_item_id" value="<?php echo htmlspecialchars($item['cart_item_id']); ?>">
                            <button type="submit" name="action" value="increase">+</button>
                        </form>
                    </div>
                    <form action="remove_from_cart.php" method="POST" style="display: inline;">
                        <input type="hidden" name="cart_item_id" value="<?php echo htmlspecialchars($item['cart_item_id']); ?>">
                        <button type="submit" class="remove">Remove</button>
                    </form>
                </div>
            <?php endforeach; ?>
            <div class="subtotal">Sub-Total: ₱<?php echo number_format($total_price, 2); ?></div>
            <a href="Checkout.php" class="checkout">Checkout</a>
        <?php endif; ?>
    </div>

</body>
</html>
