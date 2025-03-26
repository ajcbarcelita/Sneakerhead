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
        .alt, .noadd, #max {
            display: none;
        }
        @media only screen and (max-width: 900px) {
            .wide {
                display: none;
            }
            .alt {
                display: block;
            }
            .price {
            padding: 0;
        }
        }
        body {
            margin: 2% 5%;
            font-family: Inter;
        }
        li {
            margin: 0;
            float: left;
            padding: 14px 16px;
        }
        .logo, .price, h1, #quantity {
            margin: 0;
            font-family: Newsreader;
            font-size: xx-large;
        }
        .logo a {
            color: #426b1f;
        }
        .right {
            float: right;
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
        nav li {
            margin-left: 5%;
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
        header, section {
            margin-top: 50px;
        }
        header, h1, h2, #empty, #quantity {
            text-align: center;
        }
        img {
            width: 100%;
        }
        .image {
            position: relative;
        }
        #soldout {
            position: absolute;
            display: inline;
            background-color: red;
            font-size: 80px;
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
            width: 3%;
        }
        h2 {
            font-family: Newsreader;
        }
        .noadd {
            background-color: black;
        }
    </style>
</head>
<body>
    <nav>
        <ul class="main">
            <li class="logo fl"><a href=".">SNEAKERHEADS</a></div>
            <li class="button right">Log Out</li>
            <li class="right"><a href="#news">Cart (0)</a></li><!-- Add PHP -->
            <li class="right"><a href="#contact">Account</a></li>
        </ul>
    </nav>
    <header>
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
    </header>
    <section id="product">
        <div class="image">
            <img src="<?php echo $conn->query($img)->fetch_assoc()["file_path"]; ?>" alt="A close-up view of the shoe">
            <span id="soldout">SOLD OUT</span>
        </div>
        <ul>
            <li class="price">PHP <?php echo $conn->query($info)->fetch_assoc()["price"]; ?></li>
            <li class="wide button add">Add to Cart</li>
            <li class="wide button noadd">Sold Out</li>
            <li class="wide inv"><span class="sales"></span> sold, <span class="stock"></span> in stock</li>
            <?php
                for ($i = count($size)-1; $i >= 0; $i--)
                    echo "<li class=\"wide button size right\">".$size[$i]."</li>";
            ?>
            <li class="wide right">Select a size</li>
        </ul>
        <ul class="alt">
            <li class="button add">Add to Cart</li>
            <li class="button noadd">Sold Out</li>
            <li class="inv1"><span class="sales"></span> sold, <span class="stock"></span> in stock</li>
        </ul>
        <p class="alt">Select a size</p>
        <ul class="alt">
            <?php
                for ($i = 0; $i < count($size); $i++)
                    echo "<li class=\"button size1\">".$size[$i]."</li>";
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
        const a = document.getElementsByClassName("add");
        const i = document.getElementsByClassName("sales");
        const i1 = document.getElementsByClassName("stock");
        const m = document.getElementById("max");
        const n = document.getElementsByClassName("noadd");
        const s = document.getElementsByClassName("size");
        const s1 = document.getElementsByClassName("size1");
        const so = document.getElementById("soldout");
        const q = document.getElementsByClassName("quantity");
        const qd = document.getElementById("quantity");
        const qs = document.getElementById("qtyselect");
        const s_len = s.length;

        // Event listeners
        for (let j = 0; j < 2; j++) {
            a[j].addEventListener("click", function() {
                addToCart();
            });
        }

        for (let j = 0; j < s_len; j++) {
            s[j].addEventListener("click", function() {
                setSize(s_len - 1 - j);
            });
            s1[j].addEventListener("click", function() {
                setSize(j);
            });
        }

        q[0].addEventListener("click", function() {
            if (qty < stock[selected_size]) {
                qty++;
                qd.innerHTML = qty;
            }
            else
                m.style.display = "block";
        });
        q[1].addEventListener("click", function() {
            m.style.display = "";
            if (qty > 1) {
                qty--;
                qd.innerHTML = qty;
            }
        });

        // Button functions
        function setSize(size) {
            selected_size = size;
            qty = 1;
            qd.innerHTML = qty;
            m.style.display = "";

            for (let j = 0; j < 2; j++) {
                i[j].innerHTML = sales[size];
                i1[j].innerHTML = stock[size];
            }

            if (!stock[size]) {
                so.style.display = "block";
                qs.style.display = "none";
                for (let j = 0; j < 2; j++) {
                    a[j].style.display = "none";
                    n[j].style.display = "block";
                }
            }
            else {
                so.style.display = "none";
                qs.style.display = "";
                for (let j = 0; j < 2; j++) {
                    a[j].style.display = "";
                    n[j].style.display = "";
                }
            }

            for (let j = 0; j < s_len; j++) {
                if (j == size) {
                    s[s_len - 1 - j].style.backgroundColor = "black";
                    s1[j].style.backgroundColor = "black";
                }
                else {
                    s[s_len - 1 - j].style.backgroundColor = "";
                    s1[j].style.backgroundColor = "";
                }
            }
        }

        function addToCart() {
            alert("Adding to cart is not yet implemented. Size is " + selected_size);
        }

        setSize(0);
    </script>
</body>
</html>