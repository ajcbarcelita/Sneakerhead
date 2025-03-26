<?php 
    session_start(); 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sneakerheads Login</title>
    <link rel="stylesheet" href="login-signup.css">
</head>
<body>

    <div class="login-container">
    <a href="index.php" class="logo-link"><h1>SNEAKERHEADS</h1></a>
        <h2>Login to Your Account</h2>
        <h3>Log in to pick up where you left off and make shopping easier.</h3>
        <?php
            if (isset($_SESSION["error"])) {
                echo "<p class='error-message'>" . htmlspecialchars($_SESSION["error"]) . "</p>";
                unset($_SESSION["error"]); // Clear error after displaying it
            }
            // Display success messages (e.g., logout success)
            if (isset($_SESSION["message"])) {
                echo "<p class='logout-message'>" . htmlspecialchars($_SESSION["logout_message"]) . "</p>";
                unset($_SESSION["logout_message"]); // Clear message after displaying it
            }
        ?>
        <!-- Login form -->
        <form id="login-form" method="post" action="login-handler.php">
            <input type="text" name="id" placeholder="Username or Email" class="input-field" required>
            <input type="password" name="password" placeholder="Password" class="input-field" required>
            <button type="submit" class="login-button">Login</button>
        </form>
        
        <!--Link to sign up page-->
        <p class="signup-text">Don't have an account? <a href="signup.php" class="signup-link">Sign up!</a></p>
    </div>
</body>
</html>

