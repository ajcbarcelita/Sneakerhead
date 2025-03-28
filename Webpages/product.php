<?php
    // Never display warnings in the page!
    error_reporting(E_ALL ^ E_WARNING);

    $id = $_GET["id"];
    $user = 1; // Test

    // Use default value if ID is not specified in the URL
    if (!$id)
        $id = 1;

    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "sneakerhead";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
      die("Connection failed: ".$conn->connect_error);
    }
    
    // Use default value if ID is invalid or out-of-bounds
    if (!$conn->query("SELECT * FROM shoes WHERE id='".$id."'")->num_rows)
        $id = 1;

    $info = "SELECT * FROM shoes WHERE id='".$id."'";
    $img = "SELECT * FROM shoe_images WHERE shoe_id='".$id."'";

    $avg = $conn->query("SELECT FORMAT(AVG(rating), 1) FROM shoe_reviews WHERE shoe_id='".$id."'")->fetch_array()[0];
    $r_num = $conn->query("SELECT COUNT(*) FROM shoe_reviews WHERE shoe_id='".$id."'")->fetch_array()[0];
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
    <title><?php echo $conn->query($info)->fetch_assoc()["name"]; ?> | Sneakerheads</title>
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
            <li class="button"><a href="logout-handler.php">Sign Out</a></li>
        </ul>
    </nav>
    <section id="product">
        <div id="image">
            <img src="<?php echo $conn->query($img)->fetch_assoc()["file_path"]; ?>" alt="A close-up view of the shoe">
            <span id="soldout">SOLD OUT</span>
        </div>
        <div id="info">
            <div id="name">
                <h1><?php echo $conn->query($info)->fetch_assoc()["name"]; ?></h1>
                <p><?php
                    if ($r_num) {
                        echo $avg."/5 (".$r_num." review";
                        if ($r_num > 1)
                            echo "s";
                        echo ")";
                    }
                    else
                        echo "Not yet rated";
            ?></p>
        </div>
        <p id="price">PHP <?php echo $conn->query($info)->fetch_assoc()["price"]; ?></p>
        <ul>
            <li class="button" id="add">Add to Cart</li>
            <li class="button" id="noadd">Sold Out</li>
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
    <section id="reviews">
        <h2>Reviews</h2>
        <?php
            if (!$r_num)
                echo "<p id=\"empty\">No reviews yet. Buy a pair or two and tell us what you think!</p>";
            else {
                while ($s = $review->fetch_array()) {
                    echo "<div class=\"review\"><h3>".$s[0]."</h3><span>".$s[1]."</span><span class=\"right\">Rating: ".$s[2]."</span><p>".$s[3]."</p></div>";
                }
            }
        ?>
    </section>
    <script>
        let selected_size = 0;
        let qty = 1;

        // Dynamically set arrays
        const sales = [<?php
            for ($i = 0; $i < count($size); $i++)
                echo $sales[$i].",";
        ?>null];
        const stock = [1,<?php
            for ($i = 0; $i < count($size); $i++)
                echo $stock[$i].",";
        ?>null];

        // DOM objects
        const a = document.getElementById("add");
        const i = document.getElementById("sales");
        const i1 = document.getElementById("stock");
        const m = document.getElementById("max");
        const n = document.getElementById("noadd");
        const s = document.getElementsByClassName("size");
        const so = document.getElementById("soldout");
        const q = document.getElementsByClassName("quantity");
        const qd = document.getElementById("quantity");
        const qs = document.getElementById("qtyselect");
        const s_len = s.length;

        // Event listeners
        a.addEventListener("click", function() {
            alert("Adding to cart is not yet implemented. Size is " + selected_size);
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

        function setSize(size) {
            selected_size = size;
            qty = 1;
            qd.innerHTML = qty;
            m.style.visibility = "";

            i.innerHTML = sales[size];
            i1.innerHTML = stock[size];

            if (!stock[size]) {
                so.style.display = "block";
                qs.style.display = "none";
                a.style.display = "none";
                n.style.display = "block";
            }
            else {
                so.style.display = "none";
                qs.style.display = "";
                a.style.display = "";
                n.style.display = "";
                q[1].style.display = "none";

                if (stock[size] == 1) {
                    m.style.visibility = "visible";
                    q[0].style.display = "none";
                }
                else
                    q[0].style.display = "";
            }

            for (let j = 0; j < s_len; j++) {
                if (j == size)
                    s[j].style.backgroundColor = "black";
                else
                    s[j].style.backgroundColor = "";
            }
        }

        setSize(0);
    </script>
</body>
</html>