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
    $review = "SELECT * FROM shoe_reviews WHERE shoe_id='".$id."'";

    $avg = $conn->query("SELECT AVG(rating) FROM shoe_reviews WHERE shoe_id='".$id."'")->fetch_array()[0];
    $r_num = $conn->query("SELECT COUNT(*) FROM shoe_reviews WHERE shoe_id='".$id."'")->fetch_array()[0];
    $inv = $conn->query("SELECT shoe_us_size, stock FROM shoe_size_inventory WHERE shoe_id='".$id."'");

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
    <style>
        #noadd, #max {
            display: none;
        }
        body {
            margin: 2%;
            font-family: Inter;
        }
        li {
            margin: 0;
            float: left;
            padding: 14px 16px;
        }
        .logo, .price, h1, #quantity {
            font-family: Newsreader;
            font-size: xx-large;
        }
        .logo a {
            color: #426b1f;
        }
        .right {
            float: right;
            margin-left: 5%;
        }
        .button {
            background-color: #426b1f;
            border-radius: 8px;
            color: white;
            text-align: center;
            cursor: pointer;
            user-select: none;
        }
        ul {
            list-style-type: none;
            margin: 0;
            padding: 0;
            overflow: hidden;
        }
        #product li, p.alt {
            margin-left: 1%;
            margin-top: 3%;
        }
        li a, li p {
            margin: 0;
            display: block;
            text-align: center;
            text-decoration: none;
            color: black;
        }
        section {
            margin-top: 2%;
        }
        #product {
            display: flex;
            justify-content: center;
        }
        #product div {
            width: 40%;
            margin: 0 5%;
        }
        @media only screen and (max-width: 900px) {
            #product {
                flex-direction: column;
            }
            #product div {
                width: 100%;
                margin: 0;
            }
        }
        h2, #empty, #quantity {
            text-align: center;
        }
        img {
            width: 100%;
            border: 5px solid #426b1f;
            border-radius: 10px;
        }
        .image {
            position: relative;
        }
        #soldout {
            position: absolute;
            display: inline;
            background-color: red;
            font-size: 50px;
            text-align: center;
            color: white;
            width: 100%;
            left: 0;
            top: 50%;
            transform: rotate(-20deg);
        }
        .review {
            margin: 3% 0;
        }
        .review h3, .review span {
            display: inline;
        }
        .review span {
            margin-left: 10px;
        }
        #max {
            color: red;
        }
        #quantity {
            width: 5%;
        }
        h2 {
            font-family: Newsreader;
            margin: 0;
        }
        #noadd {
            background-color: black;
        }
    </style>
    <link rel="stylesheet" href="product.css">
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
            <li><a href="logout-handler.php" class="btn">Sign Out</a></li>
        </ul>
    </nav>
    <!--<header>
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
    </header>-->
    <section id="product">
        <div class="image">
            <img src="<?php echo $conn->query($img)->fetch_assoc()["file_path"]; ?>" alt="A close-up view of the shoe">
            <span id="soldout">SOLD OUT</span>
        </div>
        <div>
        <!--
        <ul>
            <li class="wide button add">Add to Cart</li>
            <li class="wide button noadd">Sold Out</li>
            <li class="wide inv"><span class="sales"></span> sold, <span class="stock"></span> in stock</li>
            <?php
                for ($i = count($size)-1; $i >= 0; $i--)
                    echo "<li class=\"wide button size right\">".$size[$i]."</li>";
            ?>
            <li class="wide right">Select a size</li>
        </ul>-->
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
        <p class="price">PHP <?php echo $conn->query($info)->fetch_assoc()["price"]; ?></p>
        <ul class="alt">
            <li class="button" id="add">Add to Cart</li>
            <li class="button" id="noadd">Sold Out</li>
            <li class="inv"><span id="sales"></span> sold, <span id="stock"></span> in stock</li>
        </ul>
        <p class="alt">Select a size</p>
        <ul class="alt">
            <?php
                for ($i = 0; $i < count($size); $i++)
                    echo "<li class=\"button size\">".$size[$i]."</li>";
            ?>
        </ul>
        <ul id="qtyselect">
            <li>Quantity</li>
            <li id="quantity"></li>
            <li class="button quantity">+</li>
            <li class="button quantity">-</li>
            <li id="max">Maximum limit reached</li>
        </ul>
    </section>
    <section id="reviews">
        <h2>Reviews</h2>
        <?php
            if (!$r_num)
                echo "<p id=\"empty\">No reviews yet. Buy a pair or two and tell us what you think!</p>";
        ?>
        <!-- Dummy reviews; to be implemented as PHP -->
        <div class="review">
            <h3>Unusually Long User Name</h3>
            <span>3/26/2025, Size: 8, Rating: 5</span>
            <p>Although the Japanese had a completely different religious background (Buddhism and Shintoism), they were tolerant of Catholicism during their brief occupation of the Philippines. They were reluctant at first, but they eventually embraced Catholicism to avoid being perceived as an "enemy" by the Filipino people in spite of the numerous atrocities they have committed, though they would not take part in religious sacraments and masses. Instead, they used Catholic priests and nuns to introduce Japanese language and culture to the locals. They believed that religious beliefs would not disappear overnight, so their strategy was to let it gradually disappear over time along with Western cultural influences.</p>
        </div>
        <hr>
        <div class="review">
            <h3>User</h3>
            <span>3/26/2025, Size: 8, Rating: 5</span>
            <p>Although the Japanese had a completely different religious background (Buddhism and Shintoism), they were tolerant of Catholicism during their brief occupation of the Philippines. They were reluctant at first, but they eventually embraced Catholicism to avoid being perceived as an "enemy" by the Filipino people in spite of the numerous atrocities they have committed, though they would not take part in religious sacraments and masses. Instead, they used Catholic priests and nuns to introduce Japanese language and culture to the locals. They believed that religious beliefs would not disappear overnight, so their strategy was to let it gradually disappear over time along with Western cultural influences.</p>
        </div>
    </section>
    <script>
        let selected_size = 0;
        let qty = 1;

        // Dynamically set arrays
        const sales = [<?php
            for ($i = 0; $i < count($size); $i++)
                echo $sales[$i].",";
        ?>null];
        const stock = [0,<?php
            for ($i = 0; $i < count($size); $i++)
                echo $stock[$i].",";
        ?>null];

        // DOM
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
                m.style.display = "block";
                q[0].style.display = "none";
            }
        });
        q[1].addEventListener("click", function() {
            qty--;
            m.style.display = "";
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
            m.style.display = "";

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