<?php
    // Never display warnings in the page!
    error_reporting(E_ALL ^ E_WARNING);

    $id = $_GET["id"];

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
    $sales = "SELECT SUM(quantity) FROM order_items WHERE shoe_id='".$id."'";
    $review = "SELECT * FROM shoe_reviews WHERE shoe_id='".$id."'";

    $r_num = $conn->query("SELECT COUNT(*) FROM shoe_reviews WHERE shoe_id='".$id."'")->fetch_array()[0];
    $inv = $conn->query("SELECT * FROM shoe_size_inventory WHERE shoe_id='".$id."' AND stock > 0");

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Dynamic title -->
    <title><?php echo $conn->query($info)->fetch_assoc()["name"]; ?> | Sneakerheads</title>
    <link href="https://fonts.googleapis.com/css?family=Newsreader&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css?family=Inter&display=swap" rel="stylesheet" />
    <style>
        .alt {
            display: none;
        }
        @media only screen and (max-width: 980px) {
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
        .logo, .price, h1 {
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
            padding: 14px 16px;
            text-decoration: none;
            color: black;
        }
        header, section {
            margin-top: 50px;
        }
        header, h1, h2, #empty {
            text-align: center;
        }
        img {
            width: 100%;
        }
        .image {
            position: relative;
        }
        .soldout {
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
    </style>
</head>
<body>
    <nav>
        <ul class="main">
            <li class="logo fl"><a href=".">SNEAKERHEADS</a></div>
            <li class="button right">Log Out</li>
            <li class="alt right"><p>Menu</p></li>

            <li class="wide right"><a href="#news">Cart (0)</a></li><!-- Add PHP -->
            <li class="wide right"><a href="#contact">Account</a></li>
            <li class="wide right"><a class="active" href="#about">Shop</a></li>
        </ul>
    </nav>
    <header>
        <h1><?php echo $conn->query($info)->fetch_assoc()["name"]; ?></h1>
        <p><?php
            if ($r_num) {
                echo "5.0/5 (".$r_num." review";
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
            <img src="<?php echo $conn->query($img)->fetch_assoc()["file_path"]; ?>" alt="Close-up view of the shoe">
            <span class="soldout">SOLD OUT</span>
        </div>
        <ul>
            <li class="price">$<?php echo $conn->query($info)->fetch_assoc()["price"]; ?></li>
            <li class="wide button add">Add to Cart</li>
            <li class="wide button buy">Buy Now</li>
            <li class="wide">10 sold, 5 in stock</li><!-- Add PHP -->
            <?php
                while ($size = $inv->fetch_assoc())
                    echo "<li class=\"wide button size right\">".$size["shoe_us_size"]."</li>";
            ?>
            <li class="wide right">Select a size</li>
        </ul>
        <ul class="alt">
            <li class="button add">Add to Cart</li>
            <li class="button buy">Buy Now</li>
            <li>10 sold, 5 in stock</li><!-- Add PHP -->
        </ul>
        <p class="alt">Select a size</p>
        <ul class="alt">
            <li class="button size1">8</li>
            <li class="button size1">8.5</li>
            <li class="button size1">9</li>
            <li class="button size1">9.5</li>
        </ul>
        <ul>
            <li>Quantity</li>
            <li class="price" id="quantity">1</li>
            <li class="button quantity">+</li>
            <li class="button quantity">-</li>
            <li>Max limit</li>
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
        const xhttp = new XMLHttpRequest();
        const a = document.getElementsByClassName("add");
        const b = document.getElementsByClassName("buy");
        const s = document.getElementsByClassName("size");
        const s1 = document.getElementsByClassName("size1");
        const q = document.getElementsByClassName("quantity");
        const qd = document.getElementById("quantity");
        const s_len = s.length; // Available sizes can be changed in the future

        // Event listeners
        for (let i = 0; i < 2; i++) {
            a[i].addEventListener("click", function() {
                addToCart();
            });
            b[i].addEventListener("click", function() {
                buyNow();
            });
        }

        for (let i = 0; i < s_len; i++) {
            s[i].addEventListener("click", function() {
                setSize(s_len - 1 - i); // Elements are displayed right-to-left when float:right is used
            });
            s1[i].addEventListener("click", function() {
                setSize(i);
            });
        }

        q[0].addEventListener("click", function() {
            qty++;
            qd.innerHTML = qty;
        });
        q[1].addEventListener("click", function() {
            if (qty > 1) {
                qty--;
                qd.innerHTML = qty;
            }
        });

        function setSize(size) {
            selected_size = size;
            for (let i = 0; i < s_len; i++) {
                if (i == size) {
                    s[s_len - 1 - i].style.backgroundColor = "black";
                    s1[i].style.backgroundColor = "black";
                }
                else {
                    s[s_len - 1 - i].style.backgroundColor = "";
                    s1[i].style.backgroundColor = "";
                }
            }
        }

        function addToCart() {
            alert("Adding to cart is not yet implemented. Size is " + selected_size);
        }

        function buyNow() {
            alert("Buying is not yet implemented. Size is " + selected_size);
        }
    </script>
</body>
</html>