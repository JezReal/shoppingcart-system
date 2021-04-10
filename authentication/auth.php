<?php

require_once("Authenticator.php");

session_start();
$auth = new Authenticator();

if (!empty($_POST["loginButton"])) {

    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);

    $user_id = $auth->login($email, $password);

    if ($user_id > 0) {
        $_SESSION["user_id"] = $user_id;
        header("Location: ../pages/home_page.php");
    } else {
        $_SESSION["login_error_message"] = "Invalid credentials";
        header("Location: ../pages/login.php");
    }
}

if (!empty($_POST["registerButton"])) {
    $email = $_POST["email"];
    $password = $_POST["password"];
    $confirmPassword = $_POST["confirmPassword"];
    $firstName = $_POST["firstName"];
    $lastName = $_POST["lastName"];

    if (!validateEmail($email)) {
        $_SESSION["emailExists"] = "email exists";
        header("Location: ../pages/registration.php");
    } else if (!validatePassword($password, $confirmPassword)) {
        $_SESSION["passwordMismatch"] = "password mismatch";
        header("Location: ../pages/registration.php");
    } else if (!validateEmail($email) && !validatePassword($password, $confirmPassword)) {
        $_SESSION["emailExists"] = "email exists";
        $_SESSION["passwordMismatch"] = "password mismatch";
        header("Location: ../pages/registration.php");
    }

    if (validateEmail($email) && validatePassword($password, $confirmPassword)) {
        addUserToDatabase($firstName, $lastName, $email, $password);
    }
}


function validateEmail($email)
{
    if (isExistEmail($email)) {
        return false;
    }

    return true;
}

function validatePassword($pass, $confirm)
{
    if ($pass == $confirm) {
        return true;
    }

    return false;
}


function addUserToDatabase($firstName, $lastName, $email, $password)
{
    $database = connect();

    $query = $database->prepare("INSERT INTO customers (first_name, last_name, customer_email, customer_password) VALUES (:firstName, :lastName, :email, :password)");
    $query->bindParam("firstName", $firstName);
    $query->bindParam("lastName", $lastName);
    $query->bindParam("email", $email);
    $query->bindParam("password", $password);

    $query->execute();
    $auth = new Authenticator();

    $user_id = $auth->login($email, $password);

    if ($user_id > 0) {
        $_SESSION["user_id"] = $user_id;
        header("Location: ../pages/home_page.php");
    } else {
        header('Location: ../pages/registration.php');
    }
}

function isExistEmail($email)
{
    $database = connect();

    $query = $database->prepare("SELECT customer_email FROM customers WHERE customer_email=:email");
    $query->bindParam("email", $email);
    $query->execute();

    if ($query->rowCount() > 0) {
        return true;
    }

    return false;
}