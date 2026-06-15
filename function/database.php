<?php

$dsn = 'pgsql:dbname=sowaichat;host=localhost';
$user = 'kobayashi';
$password = 'postgres';

try {
    $pdo = new PDO($dsn, $user, $password);
} catch (PDOException $e) {
    die('Connection failed: ' . $e->getMessage());
}

?>