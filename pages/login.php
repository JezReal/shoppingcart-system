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
        <p>User first name</p>

        <!-- Display either login/logout -->
        <a href="./cart.php">Cart</a>
        <a href="./login.php">Login</a>
        <a href="./registration.php">Register</a>
    </div>
</nav>

<section>
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
