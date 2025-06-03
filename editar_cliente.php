<?php
include 'conexao.php';

if (!isset($_GET['id'])) {
    echo "ID do cliente não fornecido.";
    exit();
}

$id = $_GET['id'];
$cliente = $conn->query("SELECT * FROM clientes WHERE id = $id")->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = $_POST['nome'];
    $telefone = $_POST['telefone'];
    $valor = $_POST['valor'];
    $vencimento = $_POST['vencimento'];

    $conn->query("UPDATE clientes SET nome='$nome', telefone='$telefone', valor='$valor', vencimento='$vencimento' WHERE id=$id");
    header("Location: dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Editar Cliente</title>
    <style>
        body {
            background-color: #121212;
            color: #ffffff;
            font-family: 'Segoe UI', sans-serif;
            padding: 20px;
        }
        form {
            max-width: 400px;
            margin: auto;
            background-color: #1e1e1e;
            padding: 20px;
            border-radius: 8px;
        }
        input, label {
            display: block;
            width: 100%;
            margin-bottom: 10px;
        }
        input {
            padding: 10px;
            border: none;
            border-radius: 4px;
            background-color: #2c2c2c;
            color: #fff;
        }
        button {
            background-color: #00ffcc;
            color: #000;
            border: none;
            padding: 10px;
            border-radius: 4px;
            cursor: pointer;
        }
        a {
            color: #00ffcc;
            display: block;
            text-align: center;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <h2 style="text-align: center;">Editar Cliente</h2>
    <form method="post">
        <label>Nome:</label>
        <input type="text" name="nome" value="<?= htmlspecialchars($cliente['nome']) ?>" required>

        <label>Telefone:</label>
        <input type="text" name="telefone" value="<?= htmlspecialchars($cliente['telefone']) ?>" required>

        <label>Valor:</label>
        <input type="number" step="0.01" name="valor" value="<?= htmlspecialchars($cliente['valor']) ?>" required>

        <label>Vencimento:</label>
        <input type="date" name="vencimento" value="<?= htmlspecialchars($cliente['vencimento']) ?>" required>

        <button type="submit">Salvar Alterações</button>
    </form>
    <a href="dashboard.php">← Voltar para o Painel</a>
</body>
</html>
