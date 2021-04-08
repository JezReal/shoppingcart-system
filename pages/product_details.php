<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Title</title>
</head>
<body>

    <?php
    require_once("../database/database.php");

    //Get the product id in the url
    $selectedProductID=$_GET['viewDetailsButton'];

    $pdo = connect();
    $sql = "SELECT * FROM products WHERE product_id='.$selectedProductID.'";
    $statement = $pdo->prepare($sql);
    $statement->execute();
    echo $selectedProductID;
    ?>

    <section>
        <h1>Products Details</h1>

        <div id="product">
            <?php
            while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
                echo "<div>";
                echo '<img src = "data:image/jpeg;base64,' . base64_encode($row['product_thumbnail]']) . '"/>';
                echo '<h3>' . $row['product_name'] . '</h3>';
                echo '<p>' . $row['product_description'] . '</p>';
                echo '<p>' . $row['product_price'] . '</p>';
                echo '<p' . $row['product_stock'] . '</p>';

                //set the product id in the url using get
                echo '<form action="" method="get">
                    <button type="submit" name="viewDetailsButton" value="'. $row["product_id"] .'">Add to Cart</button>
                  </form>';
                echo "</div>";
            }
            ?>
        </div>
    </section>



</body>
</html>

