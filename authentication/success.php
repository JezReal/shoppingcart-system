<?php
session_start();

echo "<h1>";

require_once("../database/database.php");

$pdo = connect();
$selectFirstName = "SELECT first_name FROM customers WHERE customer_id='".$_SESSION["user_id"]."'";
$statement = $pdo->prepare($selectFirstName);
$statement->execute();

while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
    $_SESSION['activeUserFirstName']=$row['first_name'];
}

header("Location: ../pages/home_page.php");
echo "</h1>";
