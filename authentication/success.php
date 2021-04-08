<?php
session_start();

echo "<h1>";
var_dump($_SESSION["user_id"]);
echo "</h1>";
