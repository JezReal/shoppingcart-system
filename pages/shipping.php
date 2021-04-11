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
    <link rel="stylesheet" href="../styles/shipping.css">
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

    <section>

        <h1>This is the Shipping Page</h1>


        <div id="shipping_container">

            <form class="shippingInfoForm" action="shipping.php" method="post">
                <label for="fullName">Full name(FN,MI,LN)</label><br>
                <input type="text" name="fullName" required><br>
                <label for="address1">Address 1</label><br>
                <input type="text" name="address1" required><br>
                <label for="address2">Address 2 (optional)</label><br>
                <input type="text" name="address2"><br>
                <label for="address3">Address 3 (optional)</label><br>
                <input type="text" name="address3"><br>

                <label for="city">City</label><br>
                <input type="text" name="city" required><br>
                <label for="state">State</label><br>
                <input type="text" name="state"><br>
                <label for="country">Country</label><br>
                <input type="text" name="country"><br>
                <button id="checkOutButton" type="submit" name="continueButton">Continue</button>
            </form>

        </div>

    </section>
</body>
</html>