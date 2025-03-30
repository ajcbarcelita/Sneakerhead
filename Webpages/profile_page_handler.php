<!-- QUERY TO UPDATE PROFILE & SEE ORDER HISTORY -->
<?php
session_start();

if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

// Get user ID from session
$id = $_SESSION['id'];

include 'db_conn.php';
// Testing purpose
// $id = 3;

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

    // Sanitize and validate inputs -- If empty, the original value from db is used. If not empty, the new value is used (trimmed and sanitized).
    $username = empty($_POST['username']) ? $profile['username'] : htmlspecialchars(trim($_POST['username']));
    $fname = empty($_POST['fname']) ? $profile['fname'] : htmlspecialchars(trim($_POST['fname']));
    $lname = empty($_POST['lname']) ? $profile['lname'] : htmlspecialchars(trim($_POST['lname']));
    $phone_no = empty($_POST['phone_no']) ? $profile['phone_no'] : htmlspecialchars(trim($_POST['phone_no']));
    $email = empty($_POST['email']) ? $profile['email'] : filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $address_line = empty($_POST['address_line']) ? $profile['address_line'] : htmlspecialchars(trim($_POST['address_line']));
    $province = empty($_POST['province']) ? $profile['province'] : htmlspecialchars(trim($_POST['province']));
    $city_municipality = empty($_POST['city_municipality']) ? $profile['city_municipality'] : htmlspecialchars(trim($_POST['city_municipality']));
    $new_password = trim($_POST['pw_hash']);

    // Validate inputs
    $errors = [];

    // Check if username is unique in the db
    if (!empty($username) && strlen($username) >= 3) {
        $sql = "SELECT id FROM users WHERE username = ? AND id != ? AND is_deleted = 0";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $username, $id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0)
            $errors[] = "Username is already taken. Please choose another.";

        $stmt->close();
    }

    // Check if email is unique in the db
    if (!empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $sql = "SELECT id FROM users WHERE email = ? AND id != ? AND is_deleted = 0";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $email, $id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0)
            $errors[] = "Email is already registered. Please use another.";

        $stmt->close();
    }

    // Validate other fields
    if (!empty($username) && strlen($username) < 3)
        $errors[] = "Username must be at least 3 characters long.";
    if (!empty($fname) && !preg_match("/^[a-zA-Z\s]+$/", $fname))
        $errors[] = " First name can only contain letters and spaces.";
    if (!empty($lname) && !preg_match("/^[a-zA-Z\s]+$/", $lname))
        $errors[] = " Last name can only contain letters and spaces.";
    if (!empty($phone_no) && !preg_match("/^\d{10,15}$/", $phone_no))
        $errors[] = " Phone number must be between 10 and 15 digits.";
    if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL))
        $errors[] = " Invalid email address.";
    if (!empty($new_password) && strlen($new_password) < 8)
        $errors[] = " Password must be at least 8 characters long.";

    // If there are errors, store them in the session and reload the page
    if (!empty($errors)) {
        $_SESSION['error'] = implode("<br>", $errors);
        header("Location: profile_page.php");
        exit();
    } else {
        // If no errors, hash the password (if provided) and update the db
        $pw_hash = empty($new_password) ? $profile['pw_hash'] : password_hash($new_password, PASSWORD_DEFAULT);

        $sql = "UPDATE users 
                SET username = ?, fname = ?, lname = ?, phone_no = ?, email = ?, pw_hash = ?, address_line = ?, province = ?, city_municipality = ? 
                WHERE id = ? AND is_deleted = 0";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssssssi", $username, $fname, $lname, $phone_no, $email, $pw_hash, $address_line, $province, $city_municipality, $id);

        if ($stmt->execute())
            $_SESSION['success'] = "Profile updated successfully!";
        else
            $_SESSION['error'] = "Error updating record: " . $stmt->error;

        $stmt->close();
        header("Location: profile_page.php");
        exit();
    }
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