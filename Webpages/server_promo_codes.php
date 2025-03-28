<?php
    include "db_conn.php";
    include "BackEnd/sql_queries.php";

    session_start();
    if (!isset( $_SESSION["role_id"]) || $_SESSION["role_id"] != 1) {
        header("Location: login.php");
        exit();
    }
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sneakerheads Admin</title>
    <link rel="stylesheet" href="server_product.css">
    <script>
        function fetchPromoCodeDetails(promoCode) {
            if (!promoCode) return;

            fetch(`BackEnd/fetch_promo_code_details.php?promo_code=${promoCode}`)
                .then(response => response.json())
                .then(data => {
                    if (Object.keys(data).length === 0) {
                        alert('No details found for the selected promo code.');
                        return;
                    }

                    // Populate the fields in the Update Promo Codes section
                    document.getElementById('update_discount_type').value = data.discount_type;
                    document.getElementById('update_discount_value').value = data.discount_value;
                    document.getElementById('update_min_purchase').value = data.min_purchase;
                    document.getElementById('update_is_active').value = data.is_active;
                })
                .catch(error => {
                    console.error('Error fetching promo code details:', error);
                    alert('An error occurred while fetching promo code details. Please try again.');
                });
        }

        function softDeletePromoCode() {
            // Get the selected promo code from the dropdown
            const promoCode = document.getElementById('delete_promo_code_select').value;

            // Check if a promo code is selected
            if (!promoCode) {
                alert('Please select a promo code to delete.');
                return;
            }

            // Confirm deletion
            const confirmDelete = confirm(`Are you sure you want to delete the promo code "${promoCode}"?`);
            if (!confirmDelete) {
                alert('Deletion canceled.');
                return;
            }

            // Redirect to the backend PHP file for deletion
            window.location.href = `BackEnd/soft_delete_promo_code.php?promo_code=${encodeURIComponent(promoCode)}`;
        }
</script>
</head>
<body>
    <!-- Navigation Bar for Server Side -->
    <header>
            <h1>SNEAKERHEADS (Server Side)</h1>
            <nav>
                <a href="server_product.php">Products</a>
                <a href="#">Promo Codes</a>
                <a href="show_reports.php">Reports</a>
                <button class="sign-in" onclick="window.location.href='logout-handler.php'">Sign Out</button> <!-- Updated button -->
            </nav>
    </header>
    <?php
        // Display success message
        if (isset($_SESSION['success'])) {
            echo "<div class='success-message'>" . htmlspecialchars($_SESSION['success']) . "</div>";
            unset($_SESSION['success']); // Clear the message after displaying it
        }

        // Display error message
        if (isset($_SESSION['error'])) {
            echo "<div class='error-message'>" . htmlspecialchars($_SESSION['error']) . "</div>";
            unset($_SESSION['error']); // Clear the message after displaying it
        }
    ?>

    <!-- Will define 3 sections in this page: Add Promo Codes, Modify Promo Codes, (Soft) Delete Promo Codes -->
     <section class="admin-panel">
        <!-- Add Promo Codes -->
        <div class="form-container">
            <h2>ADD PROMO CODES</h2>
            <form action="BackEnd/add_promo_code.php" method="post">
                <!-- Promo Code -->
                <div class="input-group">
                    <label for="promo_code">Promo Code:</label>
                    <input type="text" name="promo_code" id="promo_code" placeholder="Enter Promo Code" required>
                </div>

                <!-- Discount Type -->
                <div class="input-group">
                    <label for="discount_type">Discount Type:</label>
                    <select name="discount_type" id="discount_type" required>
                        <option value="" disabled selected>Select Discount Type</option>
                        <option value="Fixed">Fixed</option>
                        <option value="Percentage">Percentage</option>
                    </select>
                </div>

                <!-- Discount Value -->
                <div class="input-group">
                    <label for="discount_value">Discount Value:</label>
                    <input type="number" name="discount_value" id="discount_value" placeholder="Enter Discount Value" required min="0" step="0.01">
                </div>

                <!-- Minimum Purchase -->
                <div class="input-group">
                    <label for="min_purchase">Minimum Purchase:</label>
                    <input type="number" name="min_purchase" id="min_purchase" placeholder="Enter Minimum Purchase" required min="0" step="0.01">
                </div>

                <!-- Is Active -->
                <div class="input-group">
                    <label for="is_active">Is Active:</label>
                    <select name="is_active" id="is_active" required>
                        <option value="1" selected>Yes</option>
                        <option value="0">No</option>
                    </select>
                </div>

                <button type="submit">Add Promo Code</button>
            </form>
        </div>

        <!-- Soft Delete Promo Codes -->
        <div class="form-container">
        TODO: SoftDel Promo Codes Backend, Displaying Error Messages Using Sessions, Adding a Confirmation Dialog
            <h2>(SOFT) DELETE PROMO CODES</h2>
            <form onsubmit="event.preventDefault(); softDeletePromoCode();"> <!-- Prevent the form from submitting directly by confirming action first -->
                <div class="input-group">
                    <label for="delete_promo_code_select">Select Promo Code:</label>
                    <select name="promo_code" id="delete_promo_code_select" required>
                        <option value="" disabled selected>Select Promo Code</option>
                        <?php
                            // Fetch active promo codes (not soft-deleted)
                            echo fetchPromoCodes($conn); // Use the reusable function to populate the dropdown
                        ?>
                    </select>
                </div>

                <!-- Submit Button -->
                <button type="submit">Soft Delete Promo Code</button>
            </form>
        </div>

        <!-- Modify Promo Codes -->
        <div class="form-container">
        TODO: Update Promo Codes Backend, Displaying Error Messages Using Sessions, Adding a Confirmation Dialog
            <h2>UPDATE PROMO CODES</h2>
            <form action="BackEnd/update_promo_code.php" method="post">
                <!-- Select Promo Code -->
                <div class="input-group">
                    <label for="update_promo_code_select">Select Promo Code:</label>
                    <select name="promo_code" id="update_promo_code_select" required onchange="fetchPromoCodeDetails(this.value)">
                        <option value="" disabled selected>Select Promo Code</option>
                        <?php
                            echo fetchPromoCodes($conn);
                        ?>
                    </select>
                </div>

                <!-- Discount Type -->
                <div class="input-group">
                    <label for="update_discount_type">Discount Type:</label>
                    <select name="discount_type" id="update_discount_type" required>
                        <option value="Fixed">Fixed</option>
                        <option value="Percentage">Percentage</option>
                    </select>
                </div>

                <!-- Discount Value -->
                <div class="input-group">
                    <label for="update_discount_value">Discount Value:</label>
                    <input type="number" name="discount_value" id="update_discount_value" placeholder="Enter Discount Value" required min="0" step="0.01">
                </div>

                <!-- Minimum Purchase -->
                <div class="input-group">
                    <label for="update_min_purchase">Minimum Purchase:</label>
                    <input type="number" name="min_purchase" id="update_min_purchase" placeholder="Enter Minimum Purchase" required min="0" step="0.01">
                </div>

                <!-- Is Active -->
                <div class="input-group">
                    <label for="update_is_active">Is Active:</label>
                    <select name="is_active" id="update_is_active" required>
                        <option value="1">Yes</option>
                        <option value="0">No</option>
                    </select>
                </div>

                <button type="submit">Update Promo Code</button>
            </form>
        </div>
     </section>
</body>
</html>