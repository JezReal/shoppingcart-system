<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['checkOutButton'])) {
    require_once("../database/database.php");

    $customerID = $_SESSION['user_id'];

    $database = connect();

    $cartID = getCartID();

    deductFromStock($cartID);

    $query = $database->prepare("INSERT INTO job_orders (customer_id) VALUES(:customerID)");
    $query->bindParam("customerID", $customerID);
    $query->execute();

    $items = $database->prepare("SELECT product_id FROM cart_items WHERE cart_id=:cartId");
    $items->bindParam("cartId", $cartID);
    $items->execute();

    $jobOrderId = getJobOrderId();

    while ($item = $items->fetch(PDO::FETCH_ASSOC)) {
        $currentItem = $item['product_id'];

        $jobItem = $database->prepare("INSERT INTO job_items (job_order_id, product_id) VALUES (:jobOrderId, :productId)");
        $jobItem->bindParam("jobOrderId", $jobOrderId);
        $jobItem->bindParam("productId", $currentItem);
        $jobItem->execute();
    }

    header("Location: ./order_confirmation.php");
}

function getJobOrderId()
{
    $customerID = $_SESSION['user_id'];

    $database = connect();
    $jobOrderId = $database->prepare("SELECT job_order_id from job_orders WHERE customer_id=:customerID ORDER BY job_order_id desc limit 1;");
    $jobOrderId->bindParam("customerID", $customerID);
    $jobOrderId->execute();

    if ($jobOrderId->rowCount() > 0) {
        $result = $jobOrderId->fetch(PDO::FETCH_OBJ);

        return $result->job_order_id;
    }

    return false;
}

function getCartId()
{

    $database = connect();

    $cartId = $database->prepare("SELECT cart_id FROM carts WHERE customer_id=:customerId");
    $cartId->bindParam("customerId", $_SESSION["user_id"]);
    $cartId->execute();

    if ($cartId->rowCount() > 0) {
        $result = $cartId->fetch(PDO::FETCH_OBJ);

        return $result->cart_id;
    }

    return false;
}

function deductFromStock($cartId)
{
    $database = connect();

    $query = $database->prepare("SELECT product_id, quantity FROM cart_items WHERE cart_id=:cartId");
    $query->bindParam("cartId", $cartId);
    $query->execute();

    while ($item = $query->fetch(PDO::FETCH_ASSOC)) {
        $currentId = $item['product_id'];
        $currentQuantity = $item['quantity'];

        $stockQuantity = 0;

        $quantityQuery = $database->prepare("SELECT product_stock from products WHERE product_id=:currentId");
        $quantityQuery->bindParam("currentId", $currentId);
        $quantityQuery->execute();

        if ($quantityQuery->rowCount() > 0) {
            $quantityResult = $quantityQuery->fetch(PDO::FETCH_OBJ);

            $stockQuantity = $quantityResult->product_stock;
        }

        $difference = $stockQuantity - $currentQuantity;

        $stockQuery = $database->prepare("UPDATE products SET product_stock=:newStock WHERE product_id=:productId");
        $stockQuery->bindParam("newStock", $difference);
        $stockQuery->bindParam("productId", $currentId);
        $stockQuery->execute();

    }

}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment</title>

    <style>
        <?php include "../styles/reset.css" ?>
        <?php include "../styles/header.css" ?>
        <?php include "../styles/cart.css" ?>
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

    <div id="cart_items_holder">

        <table style="width:100%">
            <tr>
                <th class="header_info">Item</th>
                <th class="header_info">Quantity</th>
                <th class="header_info">Unit Price</th>
                <th class="header_info">Price</th>

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

            }

            $_SESSION['total_weight'] = $totalWeight;
            ?>

            <tr>
                <td id="total_column" class="item_column"></td>
                <td id="total_column"
                    class="info_column"><?php echo 'total quantity: ' . $totalQuantity . " pcs." ?></td>
                <td id="total_column" class="info_column"></td>
                <td id="total_column"
                    class="info_column"><?php echo 'sub total: ' . "??? " . number_format($totalPrice, 2) ?></td>
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
                echo '</tr>';
            } else {
                echo '<tr>';
                echo '<td id="total_column" class="item_column"></td>';
                echo '<td id="total_column" class="info_column">' . 'shipping fee: ---- ' . '</td>';
                echo '<td id="total_column" class="info_column"></td>';
                echo '<td id="total_column" class="info_column">' . 'grand total: ---- ' . '</td>';
                echo '</tr>';
            }

            ?>

        </table>

    </div>

    <form action="./payment.php" method="post">
        <button id="checkOutButton" type="submit" name="checkOutButton">Checkout</button>
    </form>

</section>

</body>
</html>
