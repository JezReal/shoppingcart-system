<?php
session_start();

unset($_SESSION["login_error_message"]);
unset($_SESSION["emailExists"]);
unset($_SESSION["passwordMismatch"]);

function addCart($userID)
{
    require_once("../database/database.php");

    $pdo = connect();
    $insertToCart = "INSERT INTO carts(customer_id) VALUES ('$userID')";
    $statement = $pdo->prepare($insertToCart);
    $statement->execute();
}

function customerHasCart($userID)
{
    require_once("../database/database.php");

    $pdo = connect();
    $check_duplicates = "SELECT * FROM carts WHERE customer_id = '" . $userID . "' LIMIT 1";
    $statement1 = $pdo->prepare($check_duplicates);
    $statement1->execute();
    $res = $statement1->fetch(PDO::FETCH_ASSOC);

    if ($res) {
        return true;
    } else {
        return false;
    }
}

function cartItemExist($costumerID, $productID)
{
    require_once("../database/database.php");

    $pdo = connect();
    $check_duplicates = "SELECT cart_items.product_id FROM cart_items
                        JOIN carts ON cart_items.cart_id = carts.cart_id
                        WHERE cart_items.product_id = '$productID' AND carts.customer_id='$costumerID'";
    $statement1 = $pdo->prepare($check_duplicates);
    $statement1->execute();
    $res = $statement1->fetch(PDO::FETCH_ASSOC);

    if ($res) {
        return true;
    } else {
        return false;
    }
}

function insertToCarts($userID)
{
    require_once("../database/database.php");

    $pdo = connect();
    $insertToCart = "INSERT INTO carts(customer_id) VALUES ('$userID')";
    $statement = $pdo->prepare($insertToCart);
    $statement->execute();
}

function insertToCartItems($cartID, $productID, $quantity)
{
    require_once("../database/database.php");

    $pdo = connect();
    $insertToCartItems = "INSERT INTO cart_items (cart_id, product_id, quantity) VALUES ('$cartID','$productID','$quantity')";
    $statement = $pdo->prepare($insertToCartItems);
    $statement->execute();
}

function getCartItemId($userID)
{

    $result = '';

    require_once("../database/database.php");

    $pdo = connect();
    $insertToCartItems = "SELECT cart_item_id FROM cart_items WHERE cart_id='$userID'";
    $statement = $pdo->prepare($insertToCartItems);
    $statement->execute();

    while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
        $result = $row['cart_item_id'];
    }
    return $result;
}

function editItemQuantity($quantity, $productID, $costumerID)
{
    require_once("../database/database.php");

    $pdo = connect();
    $editItemQuantity = "UPDATE cart_items
                        JOIN carts ON cart_items.cart_id=carts.cart_id
                        SET quantity = quantity + '$quantity' WHERE cart_items.product_id = '$productID' AND carts.customer_id='$costumerID'";
    $statement = $pdo->prepare($editItemQuantity);
    $statement->execute();
}

function getCartID($userID)
{
    $result = '';
    require_once("../database/database.php");

    $pdo = connect();
    $insertToCartItems = "SELECT cart_id FROM carts WHERE customer_id='$userID'";
    $statement = $pdo->prepare($insertToCartItems);
    $statement->execute();

    while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
        $result = $row['cart_id'];
    }
    return $result;
}

if ($_SERVER['REQUEST_METHOD'] == "POST" and isset($_POST['addToCartButton'])) {

    $quantity =
    $userID = $_SESSION['user_id'];
    $productID = $_POST['addToCartButton'];
    $cartID = getCartID($userID);
    $_SESSION['cartID'] = $cartID;
    $cartItemID = getCartItemId($userID);


    if (customerHasCart($userID)) {
        if (cartItemExist($userID, $productID)) {
            editItemQuantity(1, $productID, $userID);
        } else {
            insertToCartItems($cartID, $productID, 1);
        }
    } else {
        insertToCarts($userID);
        $cartID = getCartID($userID);
        insertToCartItems($cartID, $productID, 1);
    }
    header('location: cart.php');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping cart</title>

    <style>
        <?php include "../styles/reset.css"?>
        <?php include "../styles/header.css"?>
        <?php include "../styles/home_page.css"?>
    </style>
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
        <?php

        require_once("../authentication/Authenticator.php");
        if (isset($_SESSION["user_id"])) {
            $firstname = Authenticator::getLoggedInUserFirstname($_SESSION["user_id"]);
            echo "<p>" . $firstname . "</p>";
            ?>

            <a href="./cart.php">Cart</a>

            <form action="../authentication/auth.php" method="post">
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

<section>
    <h1>Products available</h1>

    <div id="products">
        <?php
        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
            echo "<div class='product-item'>";
            echo '<img src = "' . $row['product_thumbnail'] . '"width = "100px" height = "100px"/>';
            echo '<h3>' . $row['product_name'] . '</h3>';
            echo '<p>' . "₱ " . number_format($row['product_price'], 2) . '</p>';
            echo '<p' . $row['product_stock'] . '</p>';

            echo '<div id="button-container">';
            //set the product id in the url using get
            echo '<form action="product_details.php" method="post">
                    <button type="submit" name="viewDetailsButton" value="' . $row["product_id"] . '">View Details</button>
                  </form>';

            if (isset($_SESSION["user_id"])) {
                echo '<form action="home_page.php" method="post">
                    <button type="submit" name="addToCartButton" value="' . $row["product_id"] . '">Add to Cart</button>
                  </form>';
            }
            echo '</div>';
            echo "</div>";
        }
        ?>

    </div>
</section>
</body>
</html>