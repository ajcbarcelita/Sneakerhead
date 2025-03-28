<?php
include '../db_conn.php'; // Include your database connection

if (isset($_GET['promo_code'])) {
    $promo_code = $_GET['promo_code'];

    $query = "SELECT discount_type, discount_value, min_purchase, is_active FROM promo_codes WHERE promo_code = ? AND is_deleted = 0";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "s", $promo_code);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($result)) {
        echo json_encode([
            "success" => true,
            "discount_type" => $row['discount_type'],
            "discount_value" => $row['discount_value'],
            "min_purchase" => $row['min_purchase'],
            "is_active" => $row['is_active']
        ]);
    } else {
        echo json_encode(["success" => false, "message" => "Promo code not found."]);
    }

    mysqli_stmt_close($stmt);
} else {
    echo json_encode(["success" => false, "message" => "Promo code not provided."]);
}

mysqli_close($conn);
?>
