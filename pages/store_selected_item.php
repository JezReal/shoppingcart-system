<?php

session_start();

$_SESSION['selectedItemID']=$_POST['addToCartButton'];

echo $_SESSION['selectedItemID'];

header('location: product_details.php');

