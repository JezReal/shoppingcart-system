<!--<html>-->
<!--    <head>-->
<!--        <title>Products</title>-->
<!---->
<!--    </head>-->
<!---->
<!--    <body>-->
<!---->
<!--    --><?php
//
//        require_once '../database/database.php';
//
//        $pdo = connect();
//
//        $sql = "SELECT * FROM products";
//        $stmt = $pdo->prepare($sql);
//        $stmt->execute();
//    ?>
<!---->
<!--        <div id="products-container">-->
<!--            --><?php
//                while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
//                    echo "<div>";
//                    echo '<img src = "data:image/jpg;base64,' . base64_encode($row['product_thumbnail']) . '" width = "50px" height = "50px"/>';
//                    echo '<h3>' . $row['product_name'] . '</h3>';
//                    echo '<p>' .$row['product_description'] . '</p>';
//                    echo '<p>' . $row['product_price'] . '</p>';
//                    echo '<p' . $row['product_stock'] . '</p>';
//                    echo '<button onclick=click>Add to cart </button>';
//                    echo "</div>";
//                }
//
//            ?>
<!---->
<!--        </div>-->
<!--    <form method="POST">-->
<!--        <input type="button" name="add" id="add" value="add"/>-->
<!--    </form>-->
<!--    --><?php
//        function alert() {
//            echo "it magically works";
//        }
//
//        if (array_key_exists('add', $_POST)) {
//            alert();
//        }
//    ?>
<!---->
<!--    </body>-->
<!--</html>-->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping cart</title>
    <link rel="stylesheet" href="../styles/reset.css">
    <link rel="stylesheet" href="../styles/header.css">
    <link rel="stylesheet" href="../styles/home_page.css">
</head>
<body>

<?php
require_once("../database/database.php");
$pdo = connect();
$sql = "SELECT * FROM products";
$statement = $pdo->prepare($sql);
$statement->execute();
?>

<nav>
    <div id="logo-container">
        <!-- Logo goes here -->
        <a href="./home_page.php">Logo here</a>
    </div>

    <div class="nav-container">
        <!-- Display first name if user is logged in -->
        <p>User first name</p>

        <!-- Display either login/logout -->
        <a href="./cart.php">Cart</a>
        <a href="./login.php">Login</a>
        <a href="./registration.php">Register</a>
    </div>
</nav>

<section>
    <h1>Products available</h1>

    <div id="products">
        <?php
        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
            echo "<div>";
            echo '<img src = "data:image/jpeg;base64,' . base64_encode($row['product_thumbnail']) . '" width = "100px" height = "100px"/>';
            echo '<h3>' . $row['product_name'] . '</h3>';
            echo '<p>' . $row['product_description'] . '</p>';
            echo '<p>' . $row['product_price'] . '</p>';
            echo '<p' . $row['product_stock'] . '</p>';

            //set the product id in the url using get
            echo '<form action="product_details.php" method="get">
                    <button type="submit" name="viewDetailsButton" value="'. $row["product_id"] .'">View Details</button>
                  </form>';

            echo '<form action="home_page.php" method="post">
                    <button type="submit" name="addToCartButton" value="'. $row["product_id"] .'">Add to Cart</button>
                  </form>';
            echo "</div>";

        }

        function func()
        {
            require_once("../database/database.php");

            $pdo = connect();
            $cartID=$_POST['addToCartButton'];
            $insertToCart = "INSERT INTO carts(customer_id) VALUES ( '$cartID')";
            $statement = $pdo->prepare($insertToCart);
            $statement->execute();
        }

        if($_SERVER['REQUEST_METHOD'] == "POST" and isset($_POST['addToCartButton']))
        {
            func();
        }

        ?>
    </div>
</section>
</body>
</html>