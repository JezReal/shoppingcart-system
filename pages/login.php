<?php

require_once("../authentication/auth_status.php");

session_start();

$GLOBALS["errorMessage"] = "";

function logout()
{
    unset($_SESSION["user_id"]);
    header("Location: ./home_page.php");
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["logoutButton"])) {
    logout();
}

function loginError() {
    $GLOBALS["errorMessage"] = loginInvalidCredentials();
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
    <link rel="stylesheet" href="../styles/login.css">
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
        if (isset($_SESSION["user_id"])) {
            echo "<p>" . $_SESSION["user_id"] . "</p>";
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
    <?php
    if ($GLOBALS["errorMessage"] != "") {
        echo "<p>Invalid credentials</p>";
    }
    ?>
    <form action="../authentication/auth.php" method="POST">
        <label for="email">Email</label>
        <br>
        <input type="email" name="email" id="email" required>
        <br>

        <label for="password">Password</label>
        <br>
        <input type="password" name="password" id="password" required>
        <br>

        <input type="submit" name="loginButton" value="Submit"/>
    </form>
</section>
</body>
</html>
