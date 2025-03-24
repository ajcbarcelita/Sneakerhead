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
    $review = "SELECT * FROM shoe_reviews WHERE shoe_id='".$id."'";

?>

<!DOCTYPE html>
<html>
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
        header, h1, h2, #reviews p {
            text-align: center;
        }
        img {
            width: 100%;
        }
    </style>
</head>
<body>
    <nav>
        <ul class="main">
            <li class="logo fl"><a href=".">SNEAKERHEADS</a></div>
            <li class="button right"><a href="#home">Log Out</a></li>
            <li class="alt right"><p>Menu</p></li>

            <li class="wide right"><a href="#news">Cart (0)</a></li><!-- Add PHP -->
            <li class="wide right"><a href="#contact">Account</a></li>
            <li class="wide right"><a class="active" href="#about">Shop</a></li>
        </ul>
    </nav>
    <header>
        <h1><?php echo $conn->query($info)->fetch_assoc()["name"]; ?></h1>
        <p>5.0/5 (1)</p><!-- Add PHP -->
    </header>
    <section id="product">
        <img src="<?php echo $conn->query($img)->fetch_assoc()["file_path"]; ?>">
        <ul>
            <li class="price">$<?php echo $conn->query($info)->fetch_assoc()["price"]; ?></li>
            <li class="wide button">Add to Cart</li>
            <li class="wide button">Buy Now</li>
            <li class="wide">10 sold, 5 in stock</li><!-- Add PHP -->
            <li class="wide button size right">9.5</li>
            <li class="wide button size right">9</li>
            <li class="wide button size right">8.5</li>
            <li class="wide button size right">8</li>
            <li class="wide right">Select a size</li>
        </ul>
        <ul class="alt">
            <li class="button">Add to Cart</li>
            <li class="button">Buy Now</li>
            <li>10 sold, 5 in stock</li><!-- Add PHP -->
        </ul>
        <p class="alt">Select a size</p>
        <ul class="alt">
            <li class="button size1">8</li>
            <li class="button size1">8.5</li>
            <li class="button size1">9</li>
            <li class="button size1">9.5</li>
        </ul>
    </section>
    <section id="reviews">
        <h2>Reviews</h2><!-- Add PHP -->
        <p>No reviews yet. Buy a pair or two and tell us what you think!</p>
    </section>
    <script>
        let selected_size = 0;
        const sizes = document.getElementsByClassName("size");
        const sizes_alt = document.getElementsByClassName("size1");
        const s_len = sizes.length;

        for (let i = 0; i < s_len; i++) {
            sizes[i].addEventListener("click", function() {
                setSize(s_len - 1 - i);
            });
            sizes_alt[i].addEventListener("click", function() {
                setSize(i);
            });
        }

        function setSize(s) {
            selected_size = s;
            for (let i = 0; i < s_len; i++) {
                if (i == s) {
                    sizes[s_len - 1 - i].style.backgroundColor = "black";
                    sizes_alt[i].style.backgroundColor = "black";
                }
                else {
                    sizes[s_len - 1 - i].style.backgroundColor = "";
                    sizes_alt[i].style.backgroundColor = "";
                }
            }
        }
    </script>
</body>
</html>