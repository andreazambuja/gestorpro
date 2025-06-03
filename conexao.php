<?php
$host = 'db'; // nome do serviço no docker-compose
$user = 'admin';
$password = 'admin';
$dbname = 'admin';

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}


