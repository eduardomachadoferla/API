<?php

$SERVER_HOST = '127.0.0.1';
$SERVER_PORT = 3306;
$SERVER_USER = 'root';
$SERVER_PASS = '';
$SERVER_BASE = 'api_project';

try{
    $conn = new PDO("mysql:host=$SERVER_HOST;port=$SERVER_PORT;
    dbname=$SERVER_BASE;charset=utf8",$SERVER_USER, $SERVER_PASS);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e){
    echo 'Erro na coneçao com o banco de dados: ' . $e->getMessage();
    exit;
} 