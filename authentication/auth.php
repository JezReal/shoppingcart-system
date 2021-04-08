<?php

require_once("Authenticator.php");

session_start();
$auth = new Authenticator();
$login_error_message = "";
$register_error_message = "";

$email = trim($_POST["email"]);
$password = trim($_POST["password"]);

$user_id = $auth->login($email, $password);

if ($_POST["loginButton"]) {
    if ($user_id > 0) {
        $_SESSION["user_id"] = $user_id;
        header("Location: ../pages/home_page.php");
//        header("Location: success.php");
    } else {
        header("Location: ./failure.php");
    }
    exit();
}
