<?php

session_start();

unset($_SESSION["login_error_message"]);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>

    <style>
        <?php include "../styles/reset.css" ?>
        <?php include "../styles/header.css" ?>
        <?php include "../styles/registration.css" ?>
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


<section>
    <?php
    if (isset($_SESSION["emailExists"])) {
        echo "<p class='error-message'>Email already exists!</p>";
    }

    if (isset($_SESSION["passwordMismatch"])) {
        echo "<p class='error-message'>Password does not match!</p>";
    }

    ?>
    <form action="../authentication/auth.php" method="POST">
        <label for="firstName">First name</label>
        <br>
        <input type="text" name="firstName" id="firstName" required>
        <br>

        <label for="lastName">Last name</label>
        <br>
        <input type="text" name="lastName" id="lastName" required>
        <br>

        <label for="email">Email address</label>
        <br>
        <input type="email" name="email" id="email" required>
        <br>

        <label for="password">Password</label>
        <br>
        <input type="password" name="password" id="password" required>
        <br>

        <label for="confirmPassword">Confirm password</label>
        <br>
        <input type="password" name="confirmPassword" id="confirmPassword" required>
        <br>

        <button type="submit" name="registerButton" value="Register">Register</button>
    </form>
</section>
</body>
</html>
