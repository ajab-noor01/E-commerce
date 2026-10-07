<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "my_store"; // Jo database aapne phpMyAdmin mein banaya hai

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
// echo "Connected successfully"; // Sirf check karne ke liye, baad mein ise mita dena
?>