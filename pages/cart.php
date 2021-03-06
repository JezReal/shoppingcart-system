<?php

session_start();


require_once("../database/database.php");

$database = connect();
$query = $database->prepare("SELECT * FROM cart_items WHERE cart_id=:customerId");
$query->bindParam("customerId", $_SESSION['user_id']);
$query->execute();

function minusItemQuantity($productID, $costumerID)
{
    require_once("../database/database.php");

    $pdo = connect();
    $editItemQuantity = "UPDATE cart_items
                        JOIN carts ON cart_items.cart_id=carts.cart_id
                        SET quantity = quantity - '1' WHERE cart_items.product_id = '$productID' AND carts.customer_id='$costumerID'";
    $statement = $pdo->prepare($editItemQuantity);
    $statement->execute();
}

function plusItemQuantity($productID, $costumerID)
{
    require_once("../database/database.php");

    $pdo = connect();
    $editItemQuantity = "UPDATE cart_items
                        JOIN carts ON cart_items.cart_id=carts.cart_id
                        SET quantity = quantity + '1' WHERE cart_items.product_id = '$productID' AND carts.customer_id='$costumerID'";
    $statement = $pdo->prepare($editItemQuantity);
    $statement->execute();
}

function deleteCartItem($productID, $costumerID)
{
    require_once("../database/database.php");

    $pdo = connect();
    $editItemQuantity = "DELETE  cart_items FROM cart_items 
                        JOIN carts ON cart_items.cart_id=carts.cart_id 
                        WHERE cart_items.product_id = '$productID' AND carts.customer_id='$costumerID'";
    $statement = $pdo->prepare($editItemQuantity);
    $statement->execute();

    header("Location: cart.php");
}

if ($_SERVER['REQUEST_METHOD'] == "POST" and isset($_POST['minusItemButton'])) {

    minusItemQuantity($_POST['minusItemButton'], $_SESSION['user_id']);

    if ($_POST['lastQuantity'] <= 1) {
        deleteCartItem($_POST['minusItemButton'], $_SESSION['user_id']);
    }
}

unset($_POST['minusItemButton']);

if ($_SERVER['REQUEST_METHOD'] == "POST" and isset($_POST['plusItemButton'])) {

    plusItemQuantity($_POST['plusItemButton'], $_SESSION['user_id']);
}
unset($_POST['plusItemButton']);

if ($_SERVER['REQUEST_METHOD'] == "POST" and isset($_POST['deleteFromCartButton'])) {

    deleteCartItem($_POST['deleteFromCartButton'], $_SESSION['user_id']);
}
unset($_POST['deleteFromCartButton']);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cart</title>

    <style>
        <?php include "../styles/reset.css"?>
        <?php include "../styles/header.css"?>
        <?php include "../styles/cart.css"?>
        <?php include "../styles/navigation_styles.css"?>
    </style>
</head>
<body>
<nav>
    <div id="logo-container">
        <!-- Logo goes here -->
        <a href="./home_page.php"><img src="../resources/logo.png"></a>
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

<?php

require_once("../database/database.php");
$costumerID = $_SESSION["user_id"];

$pdo = connect();
$sql = "SELECT products.product_thumbnail, products.product_id, products.product_name, products.product_weight, carts.cart_id, cart_items.quantity, products.product_price
            FROM carts JOIN cart_items ON carts.cart_id=cart_items.cart_id
            JOIN customers ON carts.customer_id=customers.customer_id
            JOIN products ON cart_items.product_id = products.product_id
            WHERE customers.customer_id='$costumerID'";
$statement = $pdo->prepare($sql);
$statement->execute();
?>

<section>

    <?php
    function getWeightLimit()
    {

        $weight = 0;

        require_once("../database/database.php");

        $pdo = connect();
        $selectShippingID = "SELECT max_weight FROM shipping";
        $statement = $pdo->prepare($selectShippingID);
        $statement->execute();

        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
            $weight = $row['max_weight'];
        }

        return $weight;
    }

    function getShippingPrice($totalWeight)
    {
        $result = 0;
        $limit = getWeightLimit();

        if ($totalWeight > $limit) {
            $result = 0;

            do {
                require_once("../database/database.php");

                if ($totalWeight > $limit) {
                    $tempWeight = $limit;
                    $totalWeight -= $limit;
                } else {
                    $tempWeight = $totalWeight;
                }

                $pdo = connect();
                $selectShippingID = "SELECT price FROM shipping WHERE '$tempWeight' BETWEEN min_weight AND max_weight";
                $statement = $pdo->prepare($selectShippingID);
                $statement->execute();

                while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
                    $result += $row['price'];
                }

            } while ($totalWeight > $limit);

        }


        $pdo = connect();
        $selectShippingID = "SELECT price FROM shipping WHERE '$totalWeight' BETWEEN min_weight AND max_weight";
        $statement = $pdo->prepare($selectShippingID);
        $statement->execute();

        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
            $result += $row['price'];
        }
        return $result;
    }



    ?>
    <?php
    if ($query->rowCount() == 0) {
        ?>

        <h1 id="cart-empty">Cart is empty :((</h1>

        <?php
    } else {
        ?>
        <div id="cart_items_holder">

            <table style="width:100%">
                <tr>
                    <th class="header_info">Item</th>
                    <th class="header_info">Quantity</th>
                    <th class="header_info">Unit Price</th>
                    <th class="header_info">Price</th>
                    <th class="header_info">Action</th>

                </tr>

                <?php

                $totalPrice = 0;
                $totalQuantity = 0;
                $totalWeight = 0;

                while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {

                    $weight = $row['product_weight'];
                    $quantity = $row['quantity'];
                    $unitPrice = $row['product_price'];
                    $_cartItemWeight = $quantity * $weight;
                    $price = $quantity * $unitPrice;
                    $totalPrice += $price;
                    $totalQuantity += $quantity;
                    $totalWeight += $_cartItemWeight;

                    echo '<tr>';
                    echo '<td class="item_column"><img id="thumbnailHolder" src = "' . $row['product_thumbnail'] . '"width = "50px" height = "50px">' . $row['product_name'] . '</td>';
                    echo '<td class="info_column">' . $quantity . '</td>';
                    echo '<td class="info_column">' . "??? " . number_format($unitPrice, 2) . '</td>';
                    echo '<td class="info_column">' . "??? " . number_format($price, 2) . '</td>';
                    echo '<td class="remove_button">
                             <form action="cart.php" method="post">
                             <input type="hidden" name="lastQuantity" value="' . $quantity . '">
                             <button class="delete_button" type="submit" name="plusItemButton" value="' . $row['product_id'] . '">+</button>
                             <button class="delete_button" type="submit" name="minusItemButton" value="' . $row['product_id'] . '">-</button>
                             <button class="delete_button" type="submit" name="deleteFromCartButton" value="' . $row['product_id'] . '"><img src="../icons/remove%20icon.png"></button>
                             </form>
                         </td>';
                    echo ' </tr>';
                }

                $_SESSION['total_weight'] = $totalWeight;
                $_SESSION['shipping_fee'] = getShippingPrice($totalWeight);
                ?>

                <tr>
                    <td id="total_column" class="item_column"></td>
                    <td id="total_column"
                        class="info_column"><?php echo 'total quantity: ' . $totalQuantity . " pcs." ?></td>
                    <td id="total_column" class="info_column"></td>
                    <td id="total_column"
                        class="info_column"><?php echo 'sub total: ' . "??? " . number_format($totalPrice, 2) ?></td>
                    <td id="total_column" class="info_column"></td>
                </tr>

                <?php
                if (isset($_SESSION['shipping_fee'])) {
                    $shippingFee = $_SESSION['shipping_fee'];
                    $grandTotal = $shippingFee + $totalPrice;

                    echo '<tr>';
                    echo '<td id="total_column" class="item_column"></td>';
                    echo '<td id="total_column" class="info_column">' . 'shipping fee: ' . "??? " . number_format($shippingFee, 2) . '</td>';
                    echo '<td id="total_column" class="info_column"></td>';
                    echo '<td id="total_column" class="info_column">' . 'grand total: ' . "??? " . number_format($grandTotal, 2) . '</td>';
                    echo '<td id="total_column" class="info_column"></td>';
                    echo '</tr>';
                } else {
                    echo '<tr>';
                    echo '<td id="total_column" class="item_column"></td>';
                    echo '<td id="total_column" class="info_column">' . 'shipping fee: ---- ' . '</td>';
                    echo '<td id="total_column" class="info_column"></td>';
                    echo '<td id="total_column" class="info_column">' . 'grand total: ---- ' . '</td>';
                    echo '<td id="total_column" class="info_column"></td>';
                    echo '</tr>';
                }

                ?>

            </table>

        </div>

        <form action="shipping.php" method="post">
            <button id="checkOutButton" type="submit" name="checkOutButton">Checkout</button>
        </form>

        <?php
    }
    ?>

</section>

</body>
</html>
