<?php include 'profile_page_handler.php'; ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <link rel="stylesheet" href="profile_styles.css">
</head>

<body>
    <!-- Heading - Brand, link to other panels, etc. -->
    <header>
        <div class="brand">SNEAKERHEADS</div>
        <!-- Other Panels -->
        <nav>
            <a href="index.php">Home</a>
            <a href="Checkout.php">Check Out</a>
            <a href="#">My Profile</a>
            <a href="cart.php">Cart</a>
            <button class="sign-out" onclick="window.location.href='logout-handler.php'">Sign Out</button>
        </nav>
    </header>

    <!-- Sidebars -->
    <div class="sidebar-container">
        <div class="sidebar">
            <input type="radio" id="profile-tab" name="tab" checked>
            <label for="profile-tab" class="sidebar-item" onclick="showProfile()">EDIT PROFILE DETAILS</label>
        </div>

        <div class="sidebar">
            <input type="radio" id="history-tab" name="tab">
            <label for="history-tab" class="sidebar-item" onclick="showHistory()">SEE ORDER HISTORY</label>
        </div>
    </div>

    <div id="content">
        <!-- Profile Details -->
        <div id="profile-details">
            <form method="post" action="profile_page.php">
                <div class="profile-container">
                    <h2>Edit Profile Details
                        <span>SAVE TO EDIT. LEAVE BLANK TO NOT MODIFY.
                            <hr>
                            <!-- Display Error/ Success -->
                            <?php
                            if (isset($_SESSION['success'])) {
                                echo "<span>" . $_SESSION['success'] . "</span>";
                                unset($_SESSION['success']);
                            }
                            if (isset($_SESSION['error'])) {
                                echo "<span>" . $_SESSION['error'] . "</span>";
                                unset($_SESSION['error']);
                            }
                            ?>
                        </span>
                    </h2>
                    <div class="text-form">
                        <label for="username">Username:</label>
                        <div class="input-container">
                            <input type="text" name="username" placeholder="Enter New Username">
                            <span class="curr-value" data-original="<?php echo $profile['username']; ?>">**********</span>
                            <input type="checkbox" id="toggle-username" class="toggle">
                            <label for="toggle-username" class="eye-icon"></label>
                        </div>
                    </div>

                    <div class="grid-container">
                        <div class="text-form">
                            <label for="fname">First Name:</label>
                            <div class="input-container">
                                <input type="text" name="fname" placeholder="Enter New Full Name">
                                <span class="curr-value" data-original="<?php echo $profile['fname']; ?>">**********</span>
                                <input type="checkbox" id="toggle-first" class="toggle">
                                <label for="toggle-first" class="eye-icon"></label>
                            </div>
                        </div>

                        <div class="text-form">
                            <label for="lname">Last Name:</label>
                            <div class="input-container">
                                <input type="text" name="lname" placeholder="Enter New Last Name">
                                <span class="curr-value" data-original="<?php echo $profile['lname']; ?>">**********</span>
                                <input type="checkbox" id="toggle-last" class="toggle">
                                <label for="toggle-last" class="eye-icon"></label>
                            </div>
                        </div>
                    </div>

                    <div class="grid-container">
                        <div class="text-form">
                            <label for="phone_no">Contact Number: </label>
                            <div class="input-container">
                                <input type="text" name="phone_no" placeholder="Enter New Contact Number">
                                <span class="curr-value" data-original="<?php echo $profile['phone_no']; ?>">**********</span>
                                <input type="checkbox" id="toggle-contact" class="toggle">
                                <label for="toggle-contact" class="eye-icon"></label>
                            </div>
                        </div>

                        <div class="text-form">
                            <label for="email">Email Address:</label>
                            <div class="input-container">
                                <input type="text" name="email" placeholder="Enter New Email Address">
                                <span class="curr-value" data-original="<?php echo $profile['email']; ?>">**********</span>
                                <input type="checkbox" id="toggle-email" class="toggle">
                                <label for="toggle-email" class="eye-icon"></label>
                            </div>
                        </div>
                    </div>

                    <div class="text-form">
                        <label for="pw_hash">Password:</label>
                        <div class="input-container">
                            <input type="password" name="pw_hash" placeholder="Enter New Password.">
                        </div>
                    </div>

                    <div class="text-form">
                        <label for="address_line">Address Line:</label>
                        <div class="input-container">
                            <input type="text" name="address_line" placeholder="Enter New Address Line">
                            <span class="curr-value" data-original="<?php echo $profile['address_line']; ?>">**********</span>
                            <input type="checkbox" id="toggle-address" class="toggle">
                            <label for="toggle-address" class="eye-icon"></label>
                        </div>
                    </div>

                    <!-- Province & City -->
                    <div class="grid-container">
                        <div class="text-form">
                            <label for="province">Province:</label>
                            <div class="input-container">
                                <input type="text" name="province" placeholder="Enter New Province">
                                <span class="curr-value" data-original="<?php echo $profile['province']; ?>">**********</span>
                                <input type="checkbox" id="toggle-province" class="toggle">
                                <label for="toggle-province" class="eye-icon"></label>
                            </div>
                        </div>

                        <div class="text-form">
                            <label for="city_municipality">City/Municipality:</label>
                            <div class="input-container">
                                <input type="text" name="city_municipality" placeholder="Enter New City">
                                <span class="curr-value" data-original="<?php echo $profile['city_municipality']; ?>">**********</span>
                                <input type="checkbox" id="toggle-city" class="toggle">
                                <label for="toggle-city" class="eye-icon"></label>
                            </div>
                        </div>
                    </div>

                    <!-- Save -->
                    <button class="save" type="submit" name="update" value="Save Changes">Save</button>
                </div>
            </form>
        </div>

        <!-- Order History -->
        <div id="order-history" style="display: none;">
            <div class="profile-container">
                <h2>ORDER HISTORY <span><?php echo isset($orders) ? count($orders) : 0; ?> Orders</span></h2>
                <hr>
                <?php if (isset($orders) && !empty($orders)) : ?>
                    <?php foreach ($orders as $order) : ?>
                        <div class="order-card">
                            <div class="order-header">
                                <p class="order-brand"><strong><?php echo $order["brand_name"]; ?></strong></p>
                                <p class="order-price"><strong>₱<?php echo number_format($order["total_price"], 2); ?></strong></p>
                            </div>
                            <p class="promo-code">
                                <?php
                                if ($order["promo_code"])
                                    echo "'{$order["promo_code"]}' Promo Code Used";
                                else
                                    echo "No Promo Code Used";
                                ?>
                            </p>
                            <div class="order-meta">
                                <span class="status"><?php echo 'Address: ' . $order["address_line"]; ?></span>
                                <p class="order-date"><?php echo date("d.m.Y", strtotime($order["order_date"])); ?></p>
                            </div>
                        </div>
                        <hr>
                    <?php endforeach; ?>
                <?php else : ?>
                    <p>No orders found. Please go Order from our Shop now.</p>
                <?php endif; ?>
            </div>
        </div>

    </div>

    <script>
        function showProfile() {
            document.getElementById('profile-details').style.display = 'block';
            document.getElementById('order-history').style.display = 'none';
        }

        function showHistory() {
            document.getElementById('profile-details').style.display = 'none';
            document.getElementById('order-history').style.display = 'block';
        }

        // To not show History when page load initially
        document.addEventListener("DOMContentLoaded", function() {
            document.getElementById('profile-details').style.display = 'block';
            document.getElementById('order-history').style.display = 'none';
        });

        document.addEventListener("DOMContentLoaded", function() {
            function toggleVisibility(event) {
                const span = event.target.closest(".input-container").querySelector(".curr-value");
                if (span) {
                    span.textContent = event.target.checked ? span.dataset.original : "**********";
                }
            }

            document.querySelectorAll(".toggle").forEach(toggle => {
                toggle.addEventListener("change", toggleVisibility);
                toggle.checked = false; // Ensure checkboxes are unchecked by default
            });
        });
    </script>
</body>

</html>