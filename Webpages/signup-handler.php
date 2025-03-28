<?php
    // Set secure session cookie parameters to enhance security
    ini_set('session.cookie_lifetime', 0); // Session cookie expires when the browser is closed
    ini_set('session.cookie_httponly', 1); // Prevent JavaScript access to session cookie
    ini_set('session.cookie_secure', 1); // Ensure session cookie is sent over HTTPS
    ini_set('session.use_strict_mode', 1); // Use strict mode to prevent session fixation attacks
    
    // Start the session
    session_start();
    include "db_conn.php";
    
    /*
        - A check for if username and email already exist in the database (BOTH MUST BE UNIQUE)
        - A check for if the password is at least 8 characters long
    */
    // Function to sanitize user input to prevent XSS and other attacks
    function sanitizeUserInput($input) {
        return htmlspecialchars(strip_tags($input));
    }

    function isEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    
    /*
        Function to check if the proposed password for new user meets the ff. criteria:
            - At least 8 characters long
            - Contains at least one uppercase letter
            - Contains at least one lowercase letter
            - Contains at least one number
            - Contains at least one special character
    */
    function isPasswordValid($password) {
        return preg_match('/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[\W_]).{8,}$/', $password);
    }
    
    /*
        We use the 2 functions to check if the username or email already exists in the database
        - doesUserNameExist($conn, $username)  
        - doesEmailExist($conn, $email)

        If either function returns true, we display an error message to the user and prevent registration.
        It is policy that username and email must be unique.
    */
    function doesUserNameExist($conn, $username) {
        $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    function doesEmailExist($conn, $email) {
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    function registerUser($conn, $username, $email, $mobile_no, $pw_hash, $lname, $fname, $mname, $address, $city_municipality, $province) {
        $role_id = 2;

        $stmt = $conn->prepare("INSERT INTO users (username, email, phone_no, pw_hash, role_id, lname, fname, mname, address_line, city_municipality, province) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");        
        if (!$stmt) { // if prepare() fails, a syntax error occurred
            return false;
        }
        
        $stmt->bind_param("ssssissssss", $username, $email, $mobile_no, $pw_hash, $role_id, $lname, $fname, $mname, $address, $city_municipality, $province);
        $success = $stmt->execute();
        $stmt->close();

        return (bool)$success;
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Sanitize all user inputs
        $username = sanitizeUserInput($_POST["username"]);
        $email = sanitizeUserInput($_POST["email"]);
        $password = sanitizeUserInput($_POST["password"]);
        $confirm_password = sanitizeUserInput($_POST["password_2"]);
        $fname = sanitizeUserInput($_POST["fname"]);
        $lname = sanitizeUserInput($_POST["lname"]);
        $mname = sanitizeUserInput($_POST["mname"]);
        $mobile_no = sanitizeUserInput($_POST["mobile_no"]);
        $address = sanitizeUserInput($_POST["address"]);
        $city = sanitizeUserInput($_POST["city"]);
        $province = sanitizeUserInput($_POST["province"]);

        if (empty($username) || empty($email) || empty($password) || empty($confirm_password) || empty($fname) || empty($lname) || empty($mobile_no) || empty($address) || empty($city) || empty($province)) {
            $_SESSION["error"] = "Please fill in all fields.";
            header("Location: signup.php");
            exit();
        }

        if (!isEmail($email)) {
            $_SESSION["error"] = "Error: Invalid email format.";
            header("Location: signup.php");
            exit();
        }

        if ($password != $confirm_password) {
            $_SESSION["error"] = "Error: Passwords do not match.";
            header("Location: signup.php");
            exit();
        }

        if (!isPasswordValid($password)) {
            $_SESSION["error"] = "Error: Password must be at least 8 characters long and contain at least one uppercase letter, one lowercase letter, one number, and one special character.";
            header("Location: signup.php");
            exit();
        }
        
        if (doesEmailExist($conn, $email) || doesUserNameExist($conn,$username)) {
            $_SESSION["error"] = "Error: Username or email must be unique.";
            header("Location: signup.php");
            exit();
        }

        $pw_hash = password_hash($password, PASSWORD_DEFAULT);
        $mname = !empty($mname) ? $mname : NULL;

        if (registerUser($conn, $username, $email, $mobile_no, $pw_hash, $lname, $fname, $mname, $address, $city, $province)) {
            session_regenerate_id(true);
            $_SESSION["message"] = "Signup successful. Please log in.";
            header("Location: login.php");
            exit();
        } else {
            $_SESSION["error"] = "Error: Unable to register user.";
            header("Location: signup.php");
            $conn->close();
            exit();
        }

    } else {
        header("Location: signup.php");
        $conn->close();
        exit();
    }
?>