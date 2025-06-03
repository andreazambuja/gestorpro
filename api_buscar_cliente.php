<?php
header('Content-Type: application/json');
include 'conexao.php';

if (!isset($_GET['telefone'])) {
    echo json_encode(['error' => 'Parâmetro telefone não fornecido']);
    exit;
}

$telefone = $_GET['telefone'];
$telefone = preg_replace('/\D/', '', $telefone); // Remove tudo que não for número

if (empty($telefone)) {
    echo json_encode(['error' => 'Telefone inválido']);
    exit;
}

// Preparar a consulta para evitar SQL Injection
$stmt = $conn->prepare("SELECT * FROM clientes WHERE telefone LIKE ?");
$likeTelefone = "%$telefone%";
$stmt->bind_param('s', $likeTelefone);
$stmt->execute();

$result = $stmt->get_result();
$clientes = [];

while ($row = $result->fetch_assoc()) {
    $clientes[] = $row;
}

if (empty($clientes)) {
    echo json_encode(['message' => 'Nenhum cliente encontrado com esse telefone']);
} else {
    echo json_encode(['clientes' => $clientes]);
}

$stmt->close();
$conn->close();
