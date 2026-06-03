<?php
$host = "localhost";
$user = "root";
$pass = "";
$db_name = "sport_booking_db";

$conn = new mysqli($host, $user, $pass, $db_name);

// utf-8 
$conn->set_charset("utf8mb4");

if ($conn->connect_error) {
    die("Konekcija sa bazom nije uspjela: " . $conn->connect_error);
}
?>