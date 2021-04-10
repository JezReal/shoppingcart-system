<?php

session_start();

function logout()
{
    unset($_SESSION["user_id"]);
    header("Location: ./home_page.php");
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["logoutButton"])) {
    logout();
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
    <link rel="stylesheet" href="../styles/cart.css">
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

            require_once("../authentication/Authenticator.php");
            if (isset($_SESSION["user_id"])) {
                $firstname = Authenticator::getLoggedInUserFirstname($_SESSION["user_id"]);
                echo "<p>" . $firstname . "</p>";
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
    $costumerID=$_SESSION["user_id"];

    $pdo = connect();
    $sql = "SELECT products.product_thumbnail, products.product_id, products.product_name, carts.cart_id, cart_items.quantity, products.product_price
            FROM carts JOIN cart_items ON carts.cart_id=cart_items.cart_id
            JOIN customers ON carts.customer_id=customers.customer_id
            JOIN products ON cart_items.product_id = products.product_id
            WHERE customers.customer_id='$costumerID'";
    $statement = $pdo->prepare($sql);
    $statement->execute();
    ?>

    <section>

        <h1>This is the Shipping Page</h1>


        <div id="shipping_container">

            <form>
                <button id="checkOutButton" type="submit" name="continueButton">Continue</button>
            </form>

        </div>

    </section>

</body>
</html>

