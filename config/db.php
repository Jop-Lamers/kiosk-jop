<?php
$host = 'localhost';
$user = 'u240561_happy';
$password = 'cSuxtm4RBnph8667kY4b';
$dbname = 'u240561_happy';

// $host = 'localhost';
// $user = 'root';
// $password = '';
// $dbname = 'happy_herbivore';

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
