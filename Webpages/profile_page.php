<?php

$row = [
    "Name" => "Enzo",
    "Email" => "josh@gmail.com",
    "Password" => "1212313",
    "Address" => "1234 Street",
    "Province" => "Silang",
    "City" => "Cavite",
    "Postal" => "1001"
];

// TESTING PURPOSES
$orders = [
    [
        "brand" => "Nike, Adidas, Puma",
        "promo_code" => "2025PROMO",
        "status" => "Delivered",
        "amount" => 5500.00,
        "order_date" => "2025-02-08"
    ],
    [
        "brand" => "Nike, Adidas",
        "promo_code" => null, // No promo used
        "status" => "Pending",
        "amount" => 3300.00,
        "order_date" => "2025-02-07"
    ],
    [
        "brand" => "Puma, Reebok",
        "promo_code" => "FREESHIP",
        "status" => "Shipped",
        "amount" => 2500.00,
        "order_date" => "2025-02-06"
    ],
    [
        "brand" => "Puma, Nike, Adidas",
        "promo_code" => null,
        "status" => "Shipped",
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

    <!-- DB -->

    <?php
    /*
    include 'db_connection.php';

    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update'])) {
        $name = $_POST['name'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        $address = $_POST['address'];
        $province = $_POST['province'];
        $city = $_POST['city'];
        $postal = $_POST['postal'];

        $update = "UPDATE tblstudent SET Name='$name', Email='$email', Password='$password', Address='$address', Province='$province', City='$city', Postal='$postal' WHERE ID='$userId'";

        if (mysqli_query($conn, $update)) {
            echo "Record updated successfully";
            header("Location: profile_page.php"); // Redirect to the same page to see the updated details
            exit();
        } else {
            echo "Error updating record: " . mysqli_error($conn);
        }
    }

    $conn->close();
    */
    ?>

    <div id="content">
        <!-- Profile Details -->
        <div id="profile-details">
            <form>
                <div class="profile-container">
                    <h2>Edit Profile Details <span>SAVE TO EDIT</span></h2>
                    <hr>
                    <div class="text-form">
                        <label for="full-name">Full Name:</label>
                        <div class="input-container">
                            <input type="text" id="full-name" placeholder="Enter New Full Name">
                            <span class="curr-value" data-original="<?php echo $row['Name']; ?>">**********</span>
                            <input type="checkbox" id="toggle-name" class="toggle">
                            <label for="toggle-name" class="eye-icon"></label>
                        </div>
                    </div>

                    <div class="text-form">
                        <label for="email-address">Email Address:</label>
                        <div class="input-container">
                            <input type="text" id="email-address" placeholder="Enter New Email Address">
                            <span class="curr-value" data-original="<?php echo $row['Email']; ?>">**********</span>
                            <input type="checkbox" id="toggle-email" class="toggle">
                            <label for="toggle-email" class="eye-icon"></label>
                        </div>
                    </div>

                    <div class="text-form">
                        <label for="password">Password:</label>
                        <div class="input-container">
                            <input type="password" id="password" placeholder="Enter New Password">
                            <span class="curr-value" data-original="<?php echo $row['Password']; ?>">**********</span>
                            <input type="checkbox" id="toggle-password" class="toggle">
                            <label for="toggle-password" class="eye-icon"></label>

                        </div>
                    </div>

                    <div class="text-form">
                        <label for="address-line">Address Line:</label>
                        <div class="input-container">
                            <input type="text" id="address-line" placeholder="Enter New Address Line">
                            <span class="curr-value" data-original="<?php echo $row['Address']; ?>">**********</span>
                            <input type="checkbox" id="toggle-address" class="toggle">
                            <label for="toggle-address" class="eye-icon"></label>
                        </div>
                    </div>

                    <!-- Province & City -->
                    <div class="province-city-container">
                        <div class="text-form">
                            <label>Province:</label>
                            <div class="input-container">
                                <input type="text" placeholder="Enter New Province">
                                <span class="curr-value" data-original="<?php echo $row['Province']; ?>">**********</span>
                                <input type="checkbox" id="toggle-province" class="toggle">
                                <label for="toggle-province" class="eye-icon"></label>
                            </div>
                        </div>

                        <div class="text-form">
                            <label>City:</label>
                            <div class="input-container">
                                <input type="text" placeholder="Enter New City">
                                <span class="curr-value" data-original="<?php echo $row['City']; ?>">**********</span>
                                <input type="checkbox" id="toggle-city" class="toggle">
                                <label for="toggle-city" class="eye-icon"></label>
                            </div>
                        </div>
                    </div>

                    <div class="text-form">
                        <label for="postal-code">Postal Code:</label>
                        <div class="input-container">
                            <input type="text" id="postal-code" placeholder="Enter New Postal Code">
                            <span class="curr-value" data-original="<?php echo $row['Postal']; ?>">**********</span>
                            <input type="checkbox" id="toggle-postal" class="toggle">
                            <label for="toggle-postal" class="eye-icon"></label>
                        </div>
                    </div>

                    <!-- Save -->
                    <button class="save" value="Save Changes">Save</button>
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
                            <p class="order-brand"><strong><?php echo $order["brand"]; ?></strong></p>
                            <p class="order-price"><strong>₱<?php echo number_format($order["amount"], 2); ?></strong></p>
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
                            <span class="status"><?php echo strtoupper($order["status"]); ?></span>
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