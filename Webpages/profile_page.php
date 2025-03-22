<?php

$profile = [
    "First" => "Enzo",
    "Last" => "Ewan ko",
    "Contactno" => "0999929192",
    "Email" => "josh@gmail.com",
    "Password" => "1212313",
    "Address" => "1234 Street",
    "Province" => "Silang",
    "City" => "Cavite",
    "Username" => "AKALAKSLAK"
];

// TESTING PURPOSES
$orders = [
    [
        "brandname" => "Nike - FORCE AIR, ADIDAS - CROCS, YEEZY - BOOSTER",
        "promo_code" => "2025PROMO",
        "address" => "1234 StreASDASDASDASDASDASDASDASDASDASXet",
        "amount" => 5500.00,
        "order_date" => "2025-02-08"
    ],
    [
        "brandname" => "Nike, Adidas",
        "promo_code" => null, // No promo used
        "address" => "1234 StreetASDADSASDADSA",
        "amount" => 3300.00,
        "order_date" => "2025-02-07"
    ],
    [
        "brandname" => "Puma, Reebok",
        "promo_code" => "FREESHIP",
        "address" => "1234 Street BALIKBAYAN",
        "amount" => 2500.00,
        "order_date" => "2025-02-06"
    ],
    [
        "brandname" => "Puma, Nike, Adidas",
        "promo_code" => null,
        "address" => "1234 Street",
        "amount" => 8000.00,
        "order_date" => "2025-02-07"
    ]
];

?>

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
    //include 'database_conn.php';

    // SELECT QUERY FOR PROFILE DETAILS
    // $sql = "SELECT username, fname, lname, phone_no, email, pw_hash, address_line, province, city_municipality
    //             FROM users
    //             WHERE id = ? AND is_deleted = 0";

    // $stmt = $conn->prepare($sql);
    // $stmt->bind_param("i", $id);
    // $stmt->execute();
    // $result = $stmt->get_result();
    // $profile = $result->fetch_assoc();

    // $stmt->close();

    // POST METHOD FOR UPDATING PROFILE DETAILS
    // if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update'])) {
    //     $username = $_POST['username'];
    //     $fname = $_POST['fname'];
    //     $lname = $_POST['lname'];
    //     $phone_no = $_POST['phone_no'];
    //     $email = $_POST['email'];
    //     $pw_hash = $_POST['pw_hash'];
    //     $address_line = $_POST['address_line'];
    //     $province = $_POST['province'];
    //     $city_municipality = $_POST['city_municipality'];

    //     $sql = "UPDATE users 
    //             SET username = ?, fname = ?, lname = ?, phone_no = ?, email = ?, pw_hash = ?, address_line = ?, province = ?, city_municipality = ? 
    //             WHERE id = ? AND is_deleted = 0";

    //     $stmt = $conn->prepare($sql);
    //     $stmt->bind_param("sssisssssi", $username, $fname, $lname, $phone_no, $email, $pw_hash, $address_line, $province, $city_municipality, $id);

    //     if ($stmt->execute()) {
    //         echo "Record updated successfully";
    //         header("refresh:2; url=profile_page.php");
    //     } else {
    //         echo "Error updating record: " . $stmt->error;
    //     }
    //     $stmt->close();
    // }


    // SELECT QUERY FOR ORDER HISTORY
    // $sql = "SELECT 
    //              o.order_id,
    //              GROUP_CONCAT(DISTINCT CONCAT(s.brand, ' - ', s.name) ORDER BY s.brand, s.name ASC) AS brand_name,
    //              o.total_price,
    //              p.promo_code,
    //              o.created_at AS order_date,
    //              u.address_line
    //        FROM orders o
    //        LEFT JOIN promo_codes p ON o.promo_code = p.promo_code_id
    //        JOIN order_items oi ON o.order_id = oi.order_id
    //        JOIN shoe_size_inventory ssi ON oi.shoe_size = ssi.shoe_us_size   // specific shoe size instead of shoe_id
    //        JOIN shoes s ON ssi.shoe_id = s.id
    //        JOIN users u ON o.user_id = u.id
    //        WHERE o.user_id = ? 
    //        GROUP BY o.order_id, o.created_at";

    // $order_stmt = $conn->prepare($sql);
    // $order_stmt->bind_param("i", $id);
    // $order_stmt->execute();
    // $order_result = $order_stmt->get_result();  
    // $orders = $order_result->fetch_all(MYSQLI_ASSOC);  

    // INPUT DATA INTO $orders ARRAY
    // $orders = [];
    // while ($row = $order_result->fetch_assoc()) 
    //     $orders[] = $row;

    // $order_stmt->close();

    // $conn->close();
    ?>

    <div id="content">
        <!-- Profile Details -->
        <div id="profile-details">
            <form method="post" action="profile_page.php">
                <div class="profile-container">
                    <h2>Edit Profile Details <span>SAVE TO EDIT</span></h2>
                    <hr>

                    <div class="text-form">
                        <label for="username">Username:</label>
                        <div class="input-container">
                            <input type="text" name="username" placeholder="Enter New Username" required>
                            <span class="curr-value" data-original="<?php echo $profile['Username']; ?>">**********</span>
                            <input type="checkbox" id="toggle-username" class="toggle">
                            <label for="toggle-username" class="eye-icon"></label>
                        </div>
                    </div>

                    <div class="grid-container">
                        <div class="text-form">
                            <label for="first-name">First Name:</label>
                            <div class="input-container">
                                <input type="text" name="full-name" placeholder="Enter New Full Name" required>
                                <span class="curr-value" data-original="<?php echo $profile['First']; ?>">**********</span>
                                <input type="checkbox" id="toggle-first" class="toggle">
                                <label for="toggle-first" class="eye-icon"></label>
                            </div>
                        </div>

                        <div class="text-form">
                            <label for="last-name">Last Name:</label>
                            <div class="input-container">
                                <input type="text" name="last-name" placeholder="Enter New Last Name" required>
                                <span class="curr-value" data-original="<?php echo $profile['Last']; ?>">**********</span>
                                <input type="checkbox" id="toggle-last" class="toggle">
                                <label for="toggle-last" class="eye-icon"></label>
                            </div>
                        </div>
                    </div>

                    <div class="grid-container">
                        <div class="text-form">
                            <label for="contact-no">Contact Number: </label>
                            <div class="input-container">
                                <input type="text" name="contact-no" placeholder="Enter New Contact Number" required>
                                <span class="curr-value" data-original="<?php echo $profile['Contactno']; ?>">**********</span>
                                <input type="checkbox" id="toggle-contact" class="toggle">
                                <label for="toggle-contact" class="eye-icon"></label>
                            </div>
                        </div>

                        <div class="text-form">
                            <label for="email-address">Email Address:</label>
                            <div class="input-container">
                                <input type="text" name="email-address" placeholder="Enter New Email Address" required>
                                <span class="curr-value" data-original="<?php echo $profile['Email']; ?>">**********</span>
                                <input type="checkbox" id="toggle-email" class="toggle">
                                <label for="toggle-email" class="eye-icon"></label>
                            </div>
                        </div>

                    </div>

                    <div class="text-form">
                        <label for="password">Password:</label>
                        <div class="input-container">
                            <input type="password" name="password" placeholder="Enter New Password" required>
                            <span class="curr-value" data-original="<?php echo $profile['Password']; ?>">**********</span>
                            <input type="checkbox" id="toggle-password" class="toggle">
                            <label for="toggle-password" class="eye-icon"></label>

                        </div>
                    </div>

                    <div class="text-form">
                        <label for="address-line">Address Line:</label>
                        <div class="input-container">
                            <input type="text" name="address-line" placeholder="Enter New Address Line" required>
                            <span class="curr-value" data-original="<?php echo $profile['Address']; ?>">**********</span>
                            <input type="checkbox" id="toggle-address" class="toggle">
                            <label for="toggle-address" class="eye-icon"></label>
                        </div>
                    </div>

                    <!-- Province & City -->
                    <div class="grid-container">
                        <div class="text-form">
                            <label for="province">Province:</label>
                            <div class="input-container">
                                <input type="text" name="province" placeholder="Enter New Province" required>
                                <span class="curr-value" data-original="<?php echo $profile['Province']; ?>">**********</span>
                                <input type="checkbox" id="toggle-province" class="toggle">
                                <label for="toggle-province" class="eye-icon"></label>
                            </div>
                        </div>

                        <div class="text-form">
                            <label for="city">City/Municipality:</label>
                            <div class="input-container">
                                <input type="text" name="city_municipality" placeholder="Enter New City" required>
                                <span class="curr-value" data-original="<?php echo $profile['City']; ?>">**********</span>
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
                <h2>ORDER HISTORY <span><?php echo count($orders); ?> Orders</span></h2>
                <hr>
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
                            <span class="status"><?php echo $order["address_line"]; ?></span>
                            <p class="order-date"><?php echo date("d.m.Y", strtotime($order["order_date"])); ?></p>
                        </div>
                    </div>
                    <hr>
                <?php endforeach; ?>
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
                // Span which is the previous sibling of the checkbox (span) <-- (checkbox)
                const span = event.target.previousElementSibling;
                if (event.target.checked)
                    span.textContent = span.dataset.original;
                else
                    span.textContent = "**********";
            }

            // Select all checkbox with class 'toggle'
            document.querySelectorAll(".toggle").forEach(toggle => {
                // Add event listener to each checkbox that calls toggleVisibility 
                toggle.addEventListener("change", toggleVisibility);
                toggle.checked = false; // Checkboxes unchecked by default
            });
        });
    </script>
</body>

</html>