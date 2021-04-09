<?php

require_once("Authenticator.php");
require_once("../pages/login.php");

session_start();
$auth = new Authenticator();

$email = trim($_POST["email"]);
$password = trim($_POST["password"]);

$user_id = $auth->login($email, $password);

if ($_POST["loginButton"]) {
    if ($user_id > 0) {
        $_SESSION["user_id"] = $user_id;
        header("Location: ../pages/home_page.php");
    } else {
        loginError();
        header("Location: ../pages/login.php");
    }
    exit();
}
