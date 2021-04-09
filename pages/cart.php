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
    <h1>This is the cart page</h1>


    <div id="cart_items_holder">

        <table style="width:100%">
            <tr>
                <th id="item_holder">Item</th>
                <th>Quantity</th>
                <th>Unit Price</th>
                <th>Price</th>
                <th>Remove</th>

            </tr>

                <?php
                $totalPrice=0;
                $totalQuantity=0;

                while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {

                    $quantity=$row['quantity'];
                    $unitPrice=$row['product_price'];
                    $price=$quantity*$unitPrice;
                    $totalPrice+=$price;
                    $totalQuantity+=$quantity;

                    echo'<tr>';
                    echo'<td><div id="itemHolder"><img id="thumbnailHolder" src = "' . $row['product_thumbnail'] . '"width = "50px" height = "50px"/><p>'. $row['product_name'] .'</p></div></td>';
                    echo'<td>'.$quantity.'</td>';
                    echo'<td>'.$unitPrice.'</td>';
                    echo'<td>'.$price.'</td>';
                    echo'<td>
                             <form>
                             <button type="submit" name="deleteFromCartButton" value="'.$row['product_id'].'">Delete</button>
                             </form>
                         </td>';
                    echo' </tr>';
                }
                ?>

            <tr>
                <td>total</td>
                <td><?php echo $totalQuantity?></td>
                <td></td>
                <td><?php echo $totalPrice?></td>
                <td></td>
            </tr>

        </table>

        <form>
            <button id="checkOutButton" type="submit" name="checkOutButton">Checkout</button>
        </form>

    </div>

</section>





</body>
</html>
