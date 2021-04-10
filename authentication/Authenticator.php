<?php
require_once "../database/database.php";

class Authenticator
{

    public function login($email, $password)
    {
        $database = connect();

        $query = $database->prepare("SELECT customer_id FROM customers WHERE customer_email=:email AND customer_password=:password");
        $query->bindParam("email", $email);
        $query->bindParam("password", $password);
        $query->execute();

        if ($query->rowCount() > 0) {
            $result = $query->fetch(PDO::FETCH_OBJ);
            return $result->customer_id;
        } else {
            return false;
        }

    }

    public static function getLoggedInUserFirstname($id)
    {
        $database = connect();

        $query = $database->prepare("SELECT first_name FROM customers WHERE customer_id=:id");
        $query->bindParam("id", $id);
        $query->execute();

        if ($query->rowCount() > 0) {
            $result = $query->fetch(PDO::FETCH_OBJ);
            return $result->first_name;
        } else {
            return false;
        }
    }
}
