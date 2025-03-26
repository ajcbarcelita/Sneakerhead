<?php
    session_start();
    session_regenerate_id(true);

    // destroy the session
    session_destroy();
    
    // Clear the session cookie in the browser
    if (ini_get("session.use_cookies")) { // Check if cookies are being used for sessions
        $params = session_get_cookie_params(); // Get the current session cookie parameters
        setcookie(
            session_name(), // The name of the session cookie (e.g., PHPSESSID)
            '',             // Set the cookie value to an empty string
            time() - 42000, // Set the cookie expiration time to a point in the past
            $params["path"],    // Use the same path as the original cookie
            $params["domain"],  // Use the same domain as the original cookie
            $params["secure"],  // Use the same secure flag (HTTPS only if true)
            $params["httponly"] // Use the same HttpOnly flag (inaccessible to JavaScript)
        );
    }

    // start a new session and display message
    session_start();
    $_SESSION["logout_message"] = "You have been logged out successfully.";

    //Redirect to login page
    header("Location: login.php");
    exit();
?>