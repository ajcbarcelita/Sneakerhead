<?php
    // Set secure session cookie parameters 
    ini_set('session.cookie_lifetime', 0); // Session cookie will expire when the browser is closed
    ini_set('session.cookie_httponly', 1); // Prevent JavaScript access to session cookie
    ini_set('session.cookie_secure', 1); // Ensure the session cookie is sent over HTTPS
    ini_set('session.use_strict_mode', 1); // Use strict mode to prevent session fixation

    session_start();
    require "db_conn.php";
    
    if(!isset($_SESSION['login_attempts'])) {
        $_SESSION['login_attempts'] = 0;
    }

    //not sure if this properly ends not just the db connection but the session as well
    // add logic such that afte X minutes has passed, person is allowed to login again
    if($_SESSION['login_attempts'] >= 5) {
        die("Too many login attempts. Please try again later.");
    }

    //Doing non-AJAX way first
    /*
        This function sanitizes user input by converting special characters 
        to HTML entities (htmlspecialchars), and removing HTML and PHP tags (strip_tags).
    */
    function sanitizeUserInput($input) {
        return htmlspecialchars(strip_tags($input));
    }

    /*
        This function checks if the email is valid by using the filter_var function with the FILTER_VALIDATE_EMAIL flag.
        The filter_var function returns the filtered data if the email is valid; otherwise, it returns false.
        The FILTER_VALIDATE_EMAIL flag checks if the email is valid according to the email address syntax, 
        that is, if it has the format: [name]@[domain].[top-level domain]

        If this function returns false, the entry should be treated as a username.
    */
    function isEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    function getUserByUsername($conn, $username) {
        $stmt = $conn->prepare("SELECT * FROM users WHERE username = ? AND is_deleted = 0");
        $stmt->bind_param("s", $username); // s means string
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc(); 
    }

    // may return null if no user is found
    function getUserByEmail($conn, $email) {
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? AND is_deleted = 0");
        $stmt->bind_param("s", $email); // s means string
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc(); 
    }

    function getUserShoppingCart($conn, $user_id) {
        $stmt = $conn->prepare("SELECT * FROM shopping_cart WHERE user_id = ?");
        $stmt->bind_param("s", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc(); 
    }

    // fucking login logic (non-AJAX)

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $id = sanitizeUserInput($_POST["id"]);
        $password = trim($_POST["password"]);

        // Check if either id or password is empty
        if (empty($id) || empty($password)) {
            header("Location: login.php?error=Please fill in all required fields.");
            exit();
        }
        
        // Check if id entered is an email; if T, it is an email; otherwise, treat as a username
        if(isEmail($id) == true) {
            $user = getUserByEmail($conn, $id);
        } else {
            $user = getUserByUsername($conn, $id);
        }

        if ($user != null && password_verify($password, $user["pw_hash"])) {
            // Set session variables
            $_SESSION["id"] = $user["id"];
            $_SESSION["username"] = $user["username"];
            $_SESSION["email"] = $user["email"];
            $_SESSION["role_id"] = $user["role_id"];
            
            //add shopping cart PK in da session, by running a query to determine which shopping cart a user is linked to
            $user_cart = getUserShoppingCart($conn, $user["id"]);
            $_SESSION["cart_id"] = $user_cart["cart_id"];
            $_SESSION["login_attempts"] = 0; // Reset login attempts on successful login
            
            session_regenerate_id(); // Regenerate session id to prevent session fixation attacks

            // put logic here to check roles if either user or admin and redirect accordingly
            //SECOND FLAG FOR CHECKING WHY THE LOGIN FOR USERS IS NOT WORKING
            switch($_SESSION["role_id"]) {
                case "1": // Admin
                    header("Location: server_product.php");
                    break;
                case "2": // User
                    header("Location: index.php");
                    break;
                default: 
                    header("Location: login.php?error=Unknown role. Please contact the administrator.");
                    break;
            }
            exit();
        } else {
            header("Location: login.php?error=Invalid username/email or password.");
            exit();
        }
    }
?>