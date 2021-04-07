<!--<html>-->
<!--    <head>-->
<!--        <title>Products</title>-->
<!---->
<!--    </head>-->
<!---->
<!--    <body>-->
<!---->
<!--    --><?php
//
//        require_once '../database/database.php';
//
//        $pdo = connect();
//
//        $sql = "SELECT * FROM products";
//        $stmt = $pdo->prepare($sql);
//        $stmt->execute();
//    ?>
<!---->
<!--        <div id="products-container">-->
<!--            --><?php
//                while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
//                    echo "<div>";
//                    echo '<img src = "data:image/jpg;base64,' . base64_encode($row['product_thumbnail']) . '" width = "50px" height = "50px"/>';
//                    echo '<h3>' . $row['product_name'] . '</h3>';
//                    echo '<p>' .$row['product_description'] . '</p>';
//                    echo '<p>' . $row['product_price'] . '</p>';
//                    echo '<p' . $row['product_stock'] . '</p>';
//                    echo '<button onclick=click>Add to cart </button>';
//                    echo "</div>";
//                }
//
//            ?>
<!---->
<!--        </div>-->
<!--    <form method="POST">-->
<!--        <input type="button" name="add" id="add" value="add"/>-->
<!--    </form>-->
<!--    --><?php
//        function alert() {
//            echo "it magically works";
//        }
//
//        if (array_key_exists('add', $_POST)) {
//            alert();
//        }
//    ?>
<!---->
<!--    </body>-->
<!--</html>-->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping cart</title>
    <link rel="stylesheet" href="../styles/reset.css">
    <link rel="stylesheet" href="../styles/home_page.css">
</head>
<body>
<nav>
    <div id="logo-container">
        <!-- Logo goes here -->
        <a href="home_page.php">Logo here</a>
    </div>

    <div class="nav-container">
        <!-- Display first name if user is logged in -->
        <p>User first name</p>

        <!-- Display either login/logout -->
        <a href="cart.php">Cart</a>
        <a href="login.php">Login</a>
        <a href="register.php">Register</a>
    </div>
</nav>
</body>
</html>
