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
            <a href="shop.php">Shop</a>
            <a href="Checkout.php">Check Out</a>
            <button class="sign-out">Sign Out</button>
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

    <!-- QUERY TO UPDATE PROFILE & SEE ORDER HISTORY -->
    <?php
    //session_start();
    include 'db_conn.php';

    // if (!isset($_SESSION['user_id'])) {
    //     // Redirect to login page if user is not logged in
    //     header("Location: login.php");
    //     exit();
    // }

    // $id = $_SESSION['user_id'];
    $id = 3;

    //SELECT QUERY FOR PROFILE DETAILS
    $sql = "SELECT username, fname, lname, phone_no, email, pw_hash, address_line, province, city_municipality
                FROM users
                WHERE id = ? AND is_deleted = 0";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $profile = $result->fetch_assoc();

    $stmt->close();

    // POST METHOD FOR UPDATING PROFILE DETAILS
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update'])) {

        // Check if each field is empty, if so, keep the original value
        $username = empty($_POST['username']) ? $profile['username'] : $_POST['username'];
        $fname = empty($_POST['fname']) ? $profile['fname'] : $_POST['fname'];
        $lname = empty($_POST['lname']) ? $profile['lname'] : $_POST['lname'];
        $phone_no = empty($_POST['phone_no']) ? $profile['phone_no'] : $_POST['phone_no'];
        $email = empty($_POST['email']) ? $profile['email'] : $_POST['email'];
        $address_line = empty($_POST['address_line']) ? $profile['address_line'] : $_POST['address_line'];
        $province = empty($_POST['province']) ? $profile['province'] : $_POST['province'];
        $city_municipality = empty($_POST['city_municipality']) ? $profile['city_municipality'] : $_POST['city_municipality'];

        $new_password = $_POST['pw_hash'];
        if (empty($new_password))
            $pw_hash = $profile['pw_hash'];
        else
            $pw_hash = password_hash($new_password, PASSWORD_DEFAULT);

        $sql = "UPDATE users 
                SET username = ?, fname = ?, lname = ?, phone_no = ?, email = ?, pw_hash = ?, address_line = ?, province = ?, city_municipality = ? 
                WHERE id = ? AND is_deleted = 0";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssssssi", $username, $fname, $lname, $phone_no, $email, $pw_hash, $address_line, $province, $city_municipality, $id);

        if ($stmt->execute())
            header("refresh:2; url=profile_page.php");
        else
            echo "Error updating record: " . $stmt->error;

        $stmt->close();
    }

    // SELECT QUERY FOR ORDER HISTORY
    $sql = "SELECT 
                o.order_id,
                GROUP_CONCAT(DISTINCT CONCAT(s.brand, ' - ', s.name) ORDER BY s.brand, s.name ASC SEPARATOR ', ') AS brand_name,
                o.total_price,
                o.promo_code,
                DATE_FORMAT(o.created_at, '%M %e, %Y') AS order_date,
                u.address_line
            FROM orders o
            JOIN users u ON o.user_id = u.id
            JOIN order_items oi ON o.order_id = oi.order_id
            JOIN shoes s ON oi.shoe_id = s.id
            LEFT JOIN promo_codes p ON o.promo_code = p.promo_code
            WHERE o.user_id = ? 
            GROUP BY o.order_id, o.total_price, o.promo_code, o.created_at, u.address_line";

    $order_stmt = $conn->prepare($sql);
    $order_stmt->bind_param("i", $id);
    $order_stmt->execute();
    $order_result = $order_stmt->get_result();

    $orders = [];
    while ($row = $order_result->fetch_assoc()) {
        $orders[] = $row;
    }
    $order_stmt->close();

    $conn->close();
    ?>

    <div id="content">
        <!-- Profile Details -->
        <div id="profile-details">
            <form method="post" action="profile_page.php">
                <div class="profile-container">
                    <h2>Edit Profile Details <span>SAVE TO EDIT. LEAVE BLANK TO NOT MODIFY.</span></h2>
                    <hr>
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
                            <span class="curr-value" data-original="<?php echo $profile['pw_hash']; ?>">**********</span>
                            <input type="checkbox" id="toggle-password" class="toggle">
                            <label for="toggle-password" class="eye-icon"></label>

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