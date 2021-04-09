<?php

session_start();

function func()
{
    require_once("../database/database.php");

    //trial query only
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

    <nav>
        <div id="logo-container">
            <!-- Logo goes here -->
            <a href="./home_page.php">Logo here</a>
        </div>

        <div class="nav-container">
            <!-- Display first name if user is logged in -->
            <?php
            if (isset($_SESSION['activeUserFirstName'])) {
                echo "<p>" . $_SESSION['activeUserFirstName'] . "</p>";
                ?>

                <a href="./cart.php">Cart</a>

                <form action="./home_page.php" method="post">
                    <button type="submit" name="logoutButton">Logout</button>
                </form>
                <?php
            } else {
                echo "<a href='./login.php'>Login</a>";
                echo "<a href='./registration.php'>Register</a>";
            }
            ?>
        </div>
    </nav>

    <?php
    require_once("../database/database.php");

    //Get the product id in the url
    $selectedProductID=$_GET['viewDetailsButton'];

    $pdo = connect();
    $sql = "SELECT product_id, product_name, product_description, product_price, product_stock, product_photo FROM products WHERE product_id='".$selectedProductID."'";
    $statement = $pdo->prepare($sql);
    $statement->execute();
    echo "Product ID= ".$selectedProductID;
    ?>

    <section>
        <h1>Products Details</h1>

        <div id="product">
            <?php
            while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
                echo "<div>";
                echo '<img src = "'.$row['product_photo'].'"/>';
                echo '<h3>' . $row['product_name'] . '</h3>';
                echo '<p>' . $row['product_description'] . '</p>';
                echo '<p>' . $row['product_price'] . '</p>';
                echo '<p' . $row['product_stock'] . '</p>';

                //set the product id in the url using get
                echo '<form action="product_details.php" method="post">
                    <input type="text" name="quantityField" value="1">
                    <button type="submit" name="addToCartButton" value="'. $row["product_id"] .'">Add to Cart</button>
                  </form>';
                echo "</div>";
            }
            ?>


        </div>
    </section>



</body>
</html>

