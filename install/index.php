<?php
include '..' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'config.inc.php';
$file = file_get_contents( __DIR__ . "/bancodedados.sql");

$options = [
    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8; SET time_zone='America/Sao_Paulo';",
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
    PDO::ATTR_CASE => PDO::CASE_NATURAL             
];

try {
    $conn = new PDO('mysql:host=' . DB_HOST . ';dbname=mysql', DB_NAME, DB_PASS, $options);
    $stmt = $conn->prepare($file);
    $stmt->execute();

    header("Location:" . BASE_URL . "remove");
} catch (PDOException $e) {
    die("Erro de conexão: " . $e->getMessage());
}