<?php

session_start();


function logout()
{
    unset($_SESSION["user_id"]);
    header("Location: ./home_page.php");
}

function getWeightLimit(){

    $weight=0;

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

    $limit=getWeightLimit();

    do{
        require_once("../database/database.php");

        if($totalWeight>$limit){
            $tempWeight=$limit;
            $totalWeight-=$limit;
        }else{
            $tempWeight=$totalWeight;
        }

        $pdo = connect();
        $selectShippingID = "SELECT price FROM shipping WHERE '$tempWeight' BETWEEN min_weight AND max_weight";
        $statement = $pdo->prepare($selectShippingID);
        $statement->execute();

        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
            $result+=$row['price'];
            echo $row['price'];
        }

    }while($totalWeight>$limit);

    $pdo = connect();
    $selectShippingID = "SELECT price FROM shipping WHERE '$totalWeight' BETWEEN min_weight AND max_weight";
    $statement = $pdo->prepare($selectShippingID);
    $statement->execute();

    while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
        $result+=$row['price'];
        echo $row['price'];
    }

        return $result;

}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["logoutButton"])) {
    logout();
}

if ($_SERVER['REQUEST_METHOD'] == "POST" and isset($_POST['continueButton'])) {

    $totalWeight = $_SESSION['total_weight'];

    $_SESSION['weight_limit']=getWeightLimit();

    $_SESSION['shipping_fee'] = getShippingPrice($totalWeight);
    $_SESSION['shippingFullName'] = $_POST['fullName'];
    $_SESSION['address'] = $_POST['address1'];
    $_SESSION['city'] = $_POST['city1'];
    $_SESSION['province'] = $_POST['province1'];
    $_SESSION['country'] = $_POST['country1'];

    if($_POST['address2']!=''){
        $_SESSION['address2'] = $_POST['address2'];
        $_SESSION['city2'] = $_POST['city2'];
        $_SESSION['province2'] = $_POST['province2'];
        $_SESSION['country2'] = $_POST['country2'];
    }

    if($_POST['address3']!=''){
        $_SESSION['address3'] = $_POST['address3'];
        $_SESSION['city3'] = $_POST['city3'];
        $_SESSION['province3'] = $_POST['province3'];
        $_SESSION['country3'] = $_POST['country3'];
    }



    header("location: payment.php");
}

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

    <h3>Shipping information</h3>

    <div id="container">


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
                        <label for="city1">Municipality/City</label><br>
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
                        <label for="city2">Municipality/City</label><br>
                        <input type="text" name="city2"><br>
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
                        <label for="city3">Municipality/City</label><br>
                        <input type="text" name="city3"><br>
                    </div>

                    <div class="field_container">
                        <label for="province3">Province</label><br>
                        <input type="text" name="province3"><br>
                    </div>

                    <div class="field_container">
                        <label for="country3">Country</label><br>
                        <input type="text" name="country3"><br>
                        <button class="continueButton" id="continue_button" type="submit" name="continueButton">Continue</button>
                    </div>
                </div>

            </form>

        </div>

    </div>

</section>
</body>
</html>