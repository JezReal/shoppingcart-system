<?php

require_once("../authentication/auth_status.php");

session_start();

unset($_SESSION["emailExists"]);
unset($_SESSION["passwordMismatch"]);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>

    <style>
        <?php include "../styles/reset.css" ?>
        <?php include "../styles/header.css" ?>
        <?php include "../styles/login.css" ?>
    </style>
</head>
<body>
<nav>
    <div id="logo-container">
        <!-- Logo goes here -->
        <a href="./home_page.php"><img src="../resources/logo.png"></a>
    </div>

    <div class="nav-container">
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
    <?php
    if (isset($_SESSION["login_error_message"])) {
        echo "<div id='error-message'>";
        echo "<p> Invalid credentials </p>";
        echo "</div>";
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
        <input type="submit" name="loginButton" value="Log in"/>
    </form>
</section>
</body>
</html>
