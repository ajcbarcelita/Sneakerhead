<?php
    require 'db_conn.php';
    session_start();
    
    // Never display warnings in the page!
    error_reporting(E_ALL ^ E_WARNING);

    $id = $_GET["id"];
    $user = $_SESSION['id'];

    // Use default value if ID is not specified in the URL
    if (!$id)
        $id = 1;
    
    // Use default value if ID is invalid or out-of-bounds
    if (!$conn->query("SELECT * FROM shoes WHERE id='".$id."'")->num_rows)
        $id = 1;

    if ($user) {
        $cart = $conn->query("SELECT cart_id FROM shopping_cart WHERE user_id='".$user."'")->fetch_array()[0];
        $name = $conn->query("SELECT CONCAT(fname, ' ', lname) FROM users WHERE id='".$user."'")->fetch_array()[0];
        $check = $conn->query("SELECT * FROM shoe_reviews WHERE user_id='".$user."' AND shoe_id='".$id."'")->fetch_array();
        $buy = $conn->query("SELECT COUNT(*) FROM orders JOIN order_items ON orders.order_id = order_items.order_id WHERE orders.user_id='".$user."' AND order_items.shoe_id='".$id."'")->fetch_array()[0];
    }
    else {
        $user = 0;
        $cart = 0;
    }

    $shoe = $conn->query("SELECT shoes.name, shoes.price, shoe_images.file_path FROM shoes JOIN shoe_images ON shoes.id = shoe_images.shoe_id WHERE shoes.id = '".$id."' AND shoes.is_deleted = '0'")->fetch_array();
    $r_stat = $conn->query("SELECT FORMAT(AVG(rating), 1), COUNT(*) FROM shoe_reviews WHERE shoe_id='".$id."'")->fetch_array();
    $inv = $conn->query("SELECT shoe_us_size, stock FROM shoe_size_inventory WHERE shoe_id='".$id."'");
    $review = $conn->query("SELECT CONCAT(users.fname, ' ', users.lname), shoe_reviews.updated_at, shoe_reviews.rating, shoe_reviews.review_text FROM shoe_reviews JOIN users ON shoe_reviews.user_id = users.id WHERE shoe_reviews.shoe_id = '".$id."'");

    $size = array();
    $sales = array();
    $stock = array();

    while ($s = $inv->fetch_array()) {
        $size[] = $s[0];
        $stock[] = $s[1];
        $sum = $conn->query("SELECT SUM(quantity) FROM order_items WHERE shoe_id='".$id."' AND shoe_size='".$s[0]."'")->fetch_array()[0];
        
        if (!$sum)
            $sales[] = 0;
        else
            $sales[] = $sum;
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Dynamic title -->
    <title><?php echo $shoe[0]; ?> | Sneakerheads</title>
    <link href="https://fonts.googleapis.com/css?family=Newsreader&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Inter&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="product.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar">
        <div class="logo">SNEAKERHEADS</div>
        <ul class="nav-links">
            <li><a href="index.php">Home</a></li>
            <li><a href="Checkout.php">Check Out</a></li>
            <li><a href="profile_page.php">My Profile</a></li>
            <li><a href="cart.php">Cart</a></li>
            <li class="button"><?php
                if (!$user)
                    echo "<a href=\"login.php\">Sign In</a>";
                else
                    echo "<a href=\"logout-handler.php\">Sign Out</a>";
            ?></li>
        </ul>
    </nav>
    <section id="product">
        <div id="image">
            <img src="<?php echo $shoe[2]; ?>" alt="A close-up view of the shoe">
            <span id="soldout">SOLD OUT</span>
        </div>
        <div id="info">
            <div id="name">
                <h1><?php echo $shoe[0]; ?></h1>
                <p><?php
                    if ($r_stat[1]) {
                        echo $r_stat[0]."/5 (".$r_stat[1]." rating";
                        if ($r_stat[1] > 1)
                            echo "s";
                        echo ")";
                    }
                    else
                        echo "Not yet rated";
            ?></p>
        </div>
        <p id="price">PHP <?php echo $shoe[1]; ?></p>
        <ul>
            <li class="button" id="add">Add to Cart</li>
            <li class="button noadd">Sold Out</li>
            <li class="button noadd">Added to Cart</li>
            <li class="inv"><span id="sales"></span> sold, <span id="stock"></span> in stock</li>
        </ul>
        <p>Select a size</p>
        <ul>
            <?php
                for ($i = 0; $i < count($size); $i++)
                    echo "<li class=\"button size\">".$size[$i]."</li>";
            ?>
        </ul>
        <ul id="qtyselect">
            <li id="qtylabel">Quantity</li>
            <li id="quantity"></li>
            <li class="button quantity">+</li>
            <li class="button quantity">-</li>
        </ul>
        <p id="max">Maximum limit reached</p>
    </section>
    <section id="write">
        <h2>Rate and review</h2>
        <?php
            if ($user) {
                if ($buy) {
                    echo "<textarea id=\"text\" maxlength=\"500\" placeholder=\"Tell us what you think about this product in 500 characters or less (optional). Leave this blank if you want to ";
                    if ($check)
                        echo "change your rating and/or remove your review";
                    else
                        echo "rate without reviewing";
                    echo ".\"></textarea><ul><li>Select a rating</li>";
                    for ($i = 1; $i <= 5; $i++)
                        echo "<li class=\"button rating\">".$i."</li>";
                    echo "</ul><ul id=\"review\"><li>Reviewing as ".$name.".";
                    if ($check)
                        echo " Your existing review for this product will be overwritten.";
                    echo "</li><li class=\"button right\" id=\"submit\">Submit</li></ul>";
                }
                else
                    echo "<p>You haven't bought this product yet. You may return to this page to rate and review after checking out.</p>";
            }
            else
                echo "<p>Already bought this product? Sign in to rate and review.</p>";
        ?>
    </section>
    <section id="reviews">
        <h2>Reviews</h2>
        <?php
            if (!$r_stat[1])
                echo "<p id=\"empty\">No reviews yet. Buy a pair or two and tell us what you think!</p>";
            else {
                while ($s = $review->fetch_array()) {
                    echo "<div class=\"review\"><h3>".$s[0]."</h3><span>".$s[1]."</span><span class=\"right\">Rating: ".$s[2]."</span><p>".$s[3]."</p></div>";
                }
            }
        ?>
    </section>
    <script>
        // Dynamically set values
        const user = <?php echo $user; ?>;
        const name = "<?php echo $name; ?>";
        const cart = [<?php echo $cart.",".$id.",".$shoe[1]; ?>];
        const buy = <?php
            echo $buy;
        ?>;
        const sales = [<?php
            for ($i = 0; $i < count($size); $i++)
                echo $sales[$i].",";
        ?>null];
        const stock = [<?php
            for ($i = 0; $i < count($size); $i++)
                echo $stock[$i].",";
        ?>null];
        const sizes = [<?php
            for ($i = 0; $i < count($size); $i++)
                echo $size[$i].",";
        ?>null];

        // DOM objects
        const a = document.getElementById("add");
        const i = document.getElementById("sales");
        const i1 = document.getElementById("stock");
        const m = document.getElementById("max");
        const n = document.getElementsByClassName("noadd");
        const s = document.getElementsByClassName("size");
        const so = document.getElementById("soldout");
        const q = document.getElementsByClassName("quantity");
        const qd = document.getElementById("quantity");
        const qs = document.getElementById("qtyselect");
        const r = document.getElementsByClassName("rating");
        const sr = document.getElementById("submit");
        const t = document.getElementById("text");
        const s_len = s.length;

        // AJAX
        const xhttp = new XMLHttpRequest();

        // Variables
        let selected_size = 0;
        let qty = 1;
        let rating = 4;

        // Event listeners
        a.addEventListener("click", function() {
            if (!cart[0])
                window.location.href = "login.php";
            else {
                xhttp.onload = function() {
                    if (this.responseText == "OK") {
                        a.style.display = "none";
                        n[1].style.display = "block";
                    }
                    else {
                        alert("Can't add this item to your cart due to an error!");
                    }
                }
                xhttp.open("POST", "add-to-cart.php");
                xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                xhttp.send("cart=" + cart[0] + "&shoe=" + cart[1] + "&size=" + sizes[selected_size] + "&qty=" + qty + "&price=" + cart[2]);
            }
        });

        for (let j = 0; j < s_len; j++) {
            s[j].addEventListener("click", function() {
                setSize(j);
            });
        }

        q[0].addEventListener("click", function() {
            qty++;
            if (qty <= stock[selected_size]) {
                qd.innerHTML = qty;
                q[1].style.display = "";
            }
            if (qty == stock[selected_size]) {
                m.style.visibility = "visible";
                q[0].style.display = "none";
            }
        });
        q[1].addEventListener("click", function() {
            qty--;
            m.style.visibility = "";
            if (qty >= 1) {
                qd.innerHTML = qty;
                q[0].style.display = "";
            }
            if (qty == 1)
                q[1].style.display = "none";
        });

        if (buy) {
            for (let j = 0; j < 5; j++) {
                r[j].addEventListener("click", function() {
                    rating = j;
                    btnSelect(r, j);
                });
            }

            sr.addEventListener("click", function() {
                if (!cart[0])
                    window.location.href = "login.php";
                else {
                    xhttp.onload = function() {
                        if (this.responseText == "OK")
                            location.reload();
                        else {
                            alert("Can't add your review due to an error! " + this.responseText);
                        }
                    }
                    xhttp.open("POST", "add-review.php");
                    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                    xhttp.send("user=" + user + "&shoe=" + cart[1] + "&rating=" + (rating + 1) + "&text=" + t.value);
                }
            });
        }

        function btnSelect(btn, val) {
            for (let j = 0; j < btn.length; j++) {
                if (j == val)
                    btn[j].style.backgroundColor = "black";
                else
                    btn[j].style.backgroundColor = "";
            }
        }

        function setSize(size) {
            selected_size = size;
            qty = 1;
            qd.innerHTML = qty;
            m.style.visibility = "";
            n[1].style.display = "";

            i.innerHTML = sales[size];
            i1.innerHTML = stock[size];

            if (!stock[size]) {
                so.style.display = "block";
                qs.style.display = "none";
                a.style.display = "none";
                n[0].style.display = "block";
            }
            else {
                so.style.display = "none";
                qs.style.display = "";
                a.style.display = "";
                n[0].style.display = "";
                q[1].style.display = "none";

                if (stock[size] == 1) {
                    m.style.visibility = "visible";
                    q[0].style.display = "none";
                }
                else
                    q[0].style.display = "";
            }

            btnSelect(s, size);
        }

        setSize(0);
        btnSelect(r, rating);
    </script>
</body>
</html>