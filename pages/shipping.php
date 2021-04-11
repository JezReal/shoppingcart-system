<?php

session_start();

function logout()
{
    unset($_SESSION["user_id"]);
    header("Location: ./home_page.php");
}

function getShippingPrice($totalWeight)
{
    $result = '';

    require_once("../database/database.php");

    $pdo = connect();
    $selectShippingID = "SELECT price FROM shipping WHERE '$totalWeight' BETWEEN min_weight AND max_weight";
    $statement = $pdo->prepare($selectShippingID);
    $statement->execute();

    while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
        $result = $row['price'];
    }
    return $result;
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["logoutButton"])) {
    logout();
}

if ($_SERVER['REQUEST_METHOD'] == "POST" and isset($_POST['continueButton'])) {
    $totalWeight = $_SESSION['total_weight'];
    $_SESSION['shipping_fee'] = getShippingPrice($totalWeight);
    $_SESSION['shippingFullName'] = $_POST['fullName'];
    $_SESSION['address'] = $_POST['address1'];
    $_SESSION['city'] = $_POST['city1'];
    $_SESSION['province'] = $_POST['province1'];
    $_SESSION['country'] = $_POST['country1'];

    header("location: payment.php");
}

$_SESSION['totalWeight'] = "dfsdf";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shipping</title>
    
    <style>
        <?php include "../styles/reset.css" ?>
        <?php include "../styles/header.css" ?>
        <?php include "../styles/shipping.css" ?>
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

<section>

    <h1>This is the Shipping Page</h1>


    <div id="shipping_container">

        <form class="shippingInfoForm" action="shipping.php" method="post">

            <label for="fullName">Full name(FN,MI,LN)</label><br>
            <input type="text" name="fullName" required><br>

            <div class="address_container">

                <div class="field_container">
                    <label for="address1">Address 1</label><br>
                    <input type="text" name="address1" required><br>
                </div>

                <div class="field_container">
                    <label for="city1">City</label><br>
                    <input type="text" name="city1" required><br>
                </div>

                <div class="field_container">
                    <label for="province1">Province</label><br>
                    <input type="text" name="province1" required><br>
                </div>

                <div class="field_container">
                    <label for="country1">Country</label><br>
                    <input type="text" name="country1" required><br>
                </div>

            </div>

            <div class="address_container">
                <div class="field_container">
                    <label for="address2">Address 2 (optional)</label><br>
                    <input type="text" name="address2"><br>
                </div>
                <div class="field_container">
                    <label for="city">City</label><br>
                    <input type="text" name="city"><br>
                </div>

                <div class="field_container">
                    <label for="province1province2">Province</label><br>
                    <input type="text" name="province2"><br>
                </div>

                <div class="field_container">
                    <label for="country2">Country</label><br>
                    <input type="text" name="country2"><br>
                </div>
            </div>

            <div class="address_container">
                <div class="field_container">
                    <label for="address3">Address 3 (optional)</label><br>
                    <input type="text" name="address3"><br>
                </div>
                <div class="field_container">
                    <label for="city">City</label><br>
                    <input type="text" name="city"><br>
                </div>

                <div class="field_container">
                    <label for="province3">Province</label><br>
                    <input type="text" name="province3"><br>
                </div>

                <div class="field_container">
                    <label for="country3">Country</label><br>
                    <input type="text" name="country3"><br>
                </div>
            </div>
            <button id="continue_button" type="submit" name="continueButton">Continue</button>
        </form>

    </div>

</section>
</body>
</html>