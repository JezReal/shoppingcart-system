<?php

session_start();

//function logout()
//{
//    unset($_SESSION['activeUserFirstName']);
//    unset($_SESSION["user_id"]);
//    header("Location: ./product_details.php");
//}
//
//if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["logoutButton"])) {
//    logout();
//}

if (isset($_SESSION['user_id'])) {
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

        $productQuantity = $_POST['quantityField'];
        $userID = $_SESSION['user_id'];
        $productID = $_POST['addToCartButton'];
        $cartID = getCartID($userID);
        $cartItemID = getCartItemId($userID);


        if (customerHasCart($userID)) {

            if (cartItemExist($userID, $productID)) {
                editItemQuantity($productQuantity, $productID, $userID);
            } else {
                insertToCartItems($cartID, $productID, $productQuantity);
            }

        } else {
            insertToCarts($userID);
            $cartID = getCartID($userID);
            insertToCartItems($cartID, $productID, 1);
        }
        header('location: cart.php');
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product details</title>

    <style>
        <?php include "../styles/reset.css" ?>
        <?php include "../styles/header.css" ?>
        <?php include "../styles/product_details.css" ?>
    </style>
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

//Get the product id in the url
$selectedProductID = $_POST['viewDetailsButton'];

$pdo = connect();
$sql = "SELECT product_id, product_name, product_description, product_price, product_stock, product_photo FROM products WHERE product_id='" . $selectedProductID . "'";
$statement = $pdo->prepare($sql);
$statement->execute();
?>

<section>
    <div id="product">
        <?php
        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
            echo "<div id='image-container'>";
            echo '<img src = "' . $row['product_photo'] . '"/>';
            echo "</div>";

            echo "<div id='text-container'>";
            echo '<h3>' . $row['product_name'] . '</h3>';
            echo '<p id="product-description">' . $row['product_description'] . '</p>';
            echo '<p class="generic-text">' . "₱ " . number_format($row['product_price'], 2) . '</p>';
            echo '<p class="generic-text">' . "Stock: " . $row['product_stock'] . '</p>';

            //set the product id in the url using get
            echo '<form action="product_details.php" method="post">';
            echo '<p id="quantity-input">Quantity: </p>';
            echo '<input class="generic-text" type="number" name="quantityField" value="1" max=' . $row["product_stock"] . ' >';
            echo '<br/>';
            echo '<button type="submit" name="addToCartButton" value="' . $row["product_id"] . '">Add to Cart</button>';
            echo '</form>';
            echo "</div>";
        }
        ?>

    </div>
</section>

</body>
</html>

