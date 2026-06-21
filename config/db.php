<?php

$host = "sql113.infinityfree.com";
$username = "if0_42136091";
$password = "cR9Tgm9xBzO";
$database = "if0_42136091_iiitr";

$conn = new mysqli(
    $host,
    $username,
    $password,
    $database
);

if ($conn->connect_error) {
    die("Connection Failed: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");

?>