<?php

$host = "localhost";
$username = "root";
$password = "root";
$database = "aobrepository";
$port = 8889; // MAMP MySQL port

$conn = new mysqli(
    $host,
    $username,
    $password,
    $database,
    $port
);


if ($conn->connect_error) {
    die("Database Connection Failed: " . $conn->connect_error);
}

?>