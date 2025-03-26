<?php
    include "db_conn.php";
    // Set secure session cookie parameters to enhance security
    ini_set('session.cookie_lifetime', 0); // Session cookie expires when the browser is closed
    ini_set('session.cookie_httponly', 1); // Prevent JavaScript access to session cookie
    ini_set('session.cookie_secure', 1); // Ensure session cookie is sent over HTTPS
    ini_set('session.use_strict_mode', 1); // Use strict mode to prevent session fixation attacks
    
    // Start the session
    session_start();
    
?>