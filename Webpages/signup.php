<?php 
    session_start(); 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sneakerheads Signup</title>
    <link rel="stylesheet" href="login-signup.css">
</head>
<body>
    <div class="signup-container">
    <a href="index.php" class="logo-link"><h1>SNEAKERHEADS</h1></a>
        <h2>Create Your Account</h2>
        <h3>Join Sneakerheads to access exclusive deals, track your orders, <br> and personalize your shopping experience.</h3>
        <?php
            if (isset($_SESSION["error"])) {
                echo "<p class='error-message'>" . htmlspecialchars($_SESSION["error"]) . "</p>";
                unset($_SESSION["error"]); // Clear error after displaying it
            }
            if (isset($_SESSION["message"])) {
                echo "<p class='logout-message'>" . htmlspecialchars($_SESSION["logout_message"]) . "</p>";
                unset($_SESSION["logout_message"]); // Clear message after displaying it
            }
        ?>
        <!-- Signup form -->
        <form id="signin-form" method="post" action="signup-handler.php">
            <input type="text" name="username" placeholder="Username" class="input-field" required>
            <input type="text" name="email" placeholder="Email" class="input-field" required>
            <input type="password" name="password" placeholder="Password" class="input-field" required>
            <input type="password" name="password_2" placeholder="Confirm Password" class="input-field" required>

            <div class="input-pair">
                <input type="text" name="fname" placeholder="First Name" class="signup-small-fields">
                <input type="text" name="lname" placeholder="Last Name" class="signup-small-fields">
            </div>

            <div class="input-pair">
                <input type="text" name="mname" placeholder="Middle Name" class="signup-small-fields">
                <input type="text" name="mobile_no" placeholder="Mobile No." class="signup-small-fields">
            </div>

            <input type="text" name="address" placeholder="Address Line" class="input-field" required>

            <div class="input-pair">
                <input type="text" name="city" placeholder="City" class="signup-small-fields">
                <input type="text" name="province" placeholder="Province" class="signup-small-fields">
            </div>

            <button type="submit" class="signup-button">Sign Up</button>
        </form>
        
        <!--Link to login page-->
        <p class="signup-text">Already have an account? <a href="signup.php" class="signup-link">Login!</a></p>
    </div>
</body>
</html>