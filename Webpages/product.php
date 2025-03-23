<!DOCTYPE html>
<html>
<head>
    <title>Product Page</title>
    <link href="https://fonts.googleapis.com/css?family=Newsreader&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css?family=Inter&display=swap" rel="stylesheet" />
    <style>
        .alt {
            display: none;
        }
        @media only screen and (max-width: 980px) {
            .wd {
                display: none;
            }
            .alt {
                display: block;
            }
            .price p {
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
        }
        .logo, .price, h1 {
            margin: 0;
            font-family: Newsreader;
            font-size: xx-large;
        }
        .logo a {
            color: #426b1f;
        }
        .fr {
            float: right;
        }
        .button {
            background-color: #426b1f;
            border-radius: 8px;
        }
        .button a, .button p {
            color: white;
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
        #product li {
            margin-left: 1%;
            margin-top: 1%;
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
        header, h1, h2 {
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
            <li class="logo fl"><a href="#">SNEAKERHEADS</a></div>
            <li class="button fr"><a href="#home">Log Out</a></li>
            <li class="alt fr"><p>Menu</p></li>

            <li class="wd fr"><a href="#news">Cart (0)</a></li>
            <li class="wd fr"><a href="#contact">Account</a></li>
            <li class="wd fr"><a class="active" href="#about">Shop</a></li>
        </ul>
    </nav>
    <header>
        <h1>Nike Air Force 1</h1>
        <p>5.0 (1)</p>
    </header>
    <section id="product">
        <img src="images/airforce1.jpg"><!-- Should be set using PHP -->
        <ul>
            <li class="price"><p>PHP 5000.00</p></li><!-- Should be set using PHP -->
            <li class="wd button"><a href="#home">Add to Cart</a></li>
            <li class="wd button"><a href="#home">Buy Now</a></li>
            <li class="wd"><p>10 sold, 5 in stock</p></li><!-- Should be set using PHP -->
            <li class="wd button size fr"><p>8</p></li>
            <li class="wd button size fr"><p>8.5</p></li>
            <li class="wd button size fr"><p>8</p></li>
            <li class="wd button size fr"><p>8.5</p></li>
            <li class="wd fr"><p>Select a size</p></li>
        </ul>
        <ul class="alt">
            <li class="button"><a href="#home">Add to Cart</a></li>
            <li class="button"><a href="#home">Buy Now</a></li>
            <li><p>10 sold, 5 in stock</p></li><!-- Should be set using PHP -->
        </ul>
        <p class="alt">Select a size</p>
        <ul class="alt">
            <li class="button size"><p>8</p></li>
            <li class="button size"><p>8.5</p></li>
            <li class="button size"><p>8</p></li>
            <li class="button size"><p>8.5</p></li>
        </ul>
        </div>
    </section>
    <section id="reviews">
        <h2>Reviews</h2><!-- Should be set using PHP -->
    </section>
</body>

</html>