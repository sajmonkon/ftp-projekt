<?php
$servername = "localhost";
$username = "konecnys";
$password = "Simon2006";
$dbname = "konecnys_filmy";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Chyba připojení: " . $conn->connect_error);
}
?>