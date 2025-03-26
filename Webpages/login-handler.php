<?php
    // Set secure session cookie parameters to enhance security
    ini_set('session.cookie_lifetime', 0); // Session cookie expires when the browser is closed
    ini_set('session.cookie_httponly', 1); // Prevent JavaScript access to session cookie
    ini_set('session.cookie_secure', 1); // Ensure session cookie is sent over HTTPS
    ini_set('session.use_strict_mode', 1); // Use strict mode to prevent session fixation attacks

    // Start the session
    session_start();

    // Include the database connection file
    require "db_conn.php";

    // Initialize login attempts if not already set
    if (!isset($_SESSION['login_attempts'])) {
        $_SESSION['login_attempts'] = 0;
    }

    // Check if the last login attempt was more than 5 minutes ago
    if (isset($_SESSION['last_attempt_time'])) {
        $timeSinceLastAttempt = time() - $_SESSION['last_attempt_time'];
        if ($timeSinceLastAttempt > 300) { // 300 seconds = 5 minutes
            $_SESSION['login_attempts'] = 0; // Reset login attempts
            unset($_SESSION['last_attempt_time']); // Clear the last attempt time
        }
    }

    // Block login if the user has exceeded the maximum number of attempts
    if ($_SESSION['login_attempts'] >= 5) {
        $_SESSION["error"] = "Too many login attempts. Please try again in 5 minutes.";
        header("Location: login.php");
        exit();
    }

    // Function to sanitize user input to prevent XSS and other attacks
    function sanitizeUserInput($input) {
        return htmlspecialchars(strip_tags($input));
    }

    // Function to validate if the input is a valid email address
    function isEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    // Function to retrieve a user by username from the database
    function getUserByUsername($conn, $username) {
        $stmt = $conn->prepare("SELECT * FROM users WHERE username = ? AND is_deleted = 0 LIMIT 1");
        $stmt->bind_param("s", $username); // Bind the username as a string
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc(); // Return the user data as an associative array
    }

    // Function to retrieve a user by email from the database
    function getUserByEmail($conn, $email) {
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? AND is_deleted = 0 LIMIT 1");
        $stmt->bind_param("s", $email); // Bind the email as a string
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc(); // Return the user data as an associative array
    }

    // Function to retrieve the user's shopping cart from the database
    function getUserShoppingCart($conn, $user_id) {
        $stmt = $conn->prepare("SELECT * FROM shopping_cart WHERE user_id = ? LIMIT 1");
        $stmt->bind_param("s", $user_id); // Bind the user ID as a string
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc(); // Return the shopping cart data as an associative array
    }

    // Handle the login form submission
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Sanitize and retrieve the input values
        $id = sanitizeUserInput($_POST["id"]);
        $password = trim($_POST["password"]);

        // Check if the input fields are empty
        if (empty($id) || empty($password)) {
            $_SESSION["error"] = "Please fill in all fields.";
            header("Location: login.php");
            exit();
        }

        // Determine if the input is an email or username and retrieve the user
        if (isEmail($id)) {
            $user = getUserByEmail($conn, $id);
        } else {
            $user = getUserByUsername($conn, $id);
        }

        // Check if the user exists and the password is correct
        if ($user != null && password_verify($password, $user["pw_hash"])) {
            // Reset login attempts on successful login
            $_SESSION["login_attempts"] = 0;
            session_regenerate_id(true); // Regenerate session ID to prevent session fixation attacks

            // Store user information in the session
            $_SESSION["id"] = $user["id"];
            $_SESSION["username"] = $user["username"];
            $_SESSION["email"] = $user["email"];
            $_SESSION["role_id"] = $user["role_id"];

            // Retrieve and store the user's shopping cart if they are a regular user
            if ($user['role_id'] == 2) {
                $user_cart = getUserShoppingCart($conn, $user["id"]);
                $_SESSION["cart_id"] = $user_cart["cart_id"] ?? null;
            } else {
                $_SESSION["cart_id"] = null;
            }

            // Redirect the user based on their role
            switch ($_SESSION["role_id"]) {
                case "1": // Admin
                    header("Location: server_product.php");
                    break;
                case "2": // User
                    header("Location: index.php");
                    break;
                default:
                    $_SESSION["error"] = "Unknown role. Please contact the administrator.";
                    header("Location: login.php");
                    break;
            }
            exit();
        } else {
            // Increment login attempts and set an error message for invalid credentials
            $_SESSION["error"] = "Invalid login credentials.";
            $_SESSION['login_attempts']++;
            $_SESSION['last_attempt_time'] = time();

            // Redirect back to the login page
            header("Location: login.php");
            exit();
        }
    }
?>