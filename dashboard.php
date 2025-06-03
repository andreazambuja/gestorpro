<?php 
include 'conexao.php';

session_start();

// Verifique se o usuário está logado, caso contrário, redirecione para o login
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

// Informações do usuário logado
$nomeUsuario = $_SESSION['nome'];

// Excluir cliente
if (isset($_GET['excluir'])) {
    $id = $_GET['excluir'];
    $conn->query("DELETE FROM clientes WHERE id = $id");
    header("Location: dashboard.php");
    exit();
}

// Renovar cliente
if (isset($_GET['renovar'])) {
    $id = $_GET['renovar'];
    $nova_data = date('Y-m-d', strtotime('+30 days'));
    $conn->query("UPDATE clientes SET vencimento = '$nova_data' WHERE id = $id");
    header("Location: dashboard.php");
    exit();
}

// Dados
$resultado = $conn->query("SELECT * FROM clientes ORDER BY vencimento ASC");
$totalClientes = $conn->query("SELECT COUNT(*) as total FROM clientes")->fetch_assoc()['total'];
$valorTotal = $conn->query("SELECT SUM(valor) as total FROM clientes")->fetch_assoc()['total'];
$hoje = date('Y-m-d');
$valorVencido = $conn->query("SELECT SUM(valor) as total FROM clientes WHERE vencimento < '$hoje'")->fetch_assoc()['total'];
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <link rel="shortcut icon" href="https://i.postimg.cc/252ytMgs/favicon-fw.png" type="image/x-icon">
    <meta charset="UTF-8">
    <title>Painel Administrativo</title>
    <style>
        body {
            background-color: #121212;
            color: #ffffff;
            font-family: 'Segoe UI', sans-serif;
            padding: 20px;
        }

        h2 {
            text-align: left;
            color: #00ffcc;
        }

        .cards {
            display: flex;
            gap: 20px;
            justify-content: center;
            margin-bottom: 30px;
            flex-wrap: wrap;
        }

        .card {
            background-color: #1e1e1e;
            border-left: 5px solid #00ffcc;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 255, 204, 0.1);
            flex: 1;
            min-width: 200px;
            max-width: 300px;
        }

        .card h3 {
            margin: 0;
            font-size: 16px;
            color: #ccc;
        }

        .card p {
            margin: 5px 0 0;
            font-size: 22px;
            font-weight: bold;
            color: #00ffcc;
        }

        .card-icon {
            font-size: 24px;
            margin-bottom: 5px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background-color: #1e1e1e;
            border-radius: 8px;
            overflow: hidden;
        }

        th, td {
            padding: 12px 15px;
            text-align: left;
        }

        th {
            background-color: #2c2c2c;
            color: #00ffcc;
        }

        tr:nth-child(even) {
            background-color: #2a2a2a;
        }

        tr:hover {
            background-color: #333;
        }

        a {
            color: #00ffcc;
            text-decoration: none;
            font-weight: bold;
        }

        a:hover {
            text-decoration: underline;
        }

        .container {
            max-width: 1100px;
            margin: auto;
        }

        .status {
            font-weight: bold;
            padding: 6px 10px;
            border-radius: 4px;
            color: white;
        }

        .verde {
            background-color: #2ecc71;
        }

        .vermelho {
            background-color: #e74c3c;
        }

        .laranja {
            background-color: #f39c12;
        }

        .button {
            background-color: #00ffcc;
            color: #121212;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            margin-top: 20px;
            display: inline-block;
            font-weight: bold;
        }

        .button:hover {
            background-color: #00b899;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>Gestor Financeiro</h2>

    <div class="cards">
        <div class="card">
            <div class="card-icon">👥</div>
            <h3>Total de Clientes</h3>
            <p><?= $totalClientes ?></p>
        </div>
        <div class="card">
            <div class="card-icon">💰</div>
            <h3>Valor Total a Receber</h3>
            <p>R$ <?= number_format($valorTotal ?? 0, 2, ',', '.') ?></p>
        </div>
        <div class="card">
            <div class="card-icon">⚠️</div>
            <h3>Valores em Aberto</h3>
            <p>R$ <?= number_format($valorVencido ?? 0, 2, ',', '.') ?></p>
        </div>
    </div>

    <table>
        <tr>
            <th>Nome</th>
            <th>Email</th>
            <th>Telefone</th>
            <th>Vencimento</th>
            <th>Valor</th>
            <th>Status</th>
            <th>Ações</th>
        </tr>
        <?php while ($cliente = $resultado->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($cliente['nome']) ?></td>
            <td><?= htmlspecialchars($cliente['email']) ?></td>
            <td><?= htmlspecialchars($cliente['telefone']) ?></td>
            <td><?= date('d/m/Y', strtotime($cliente['vencimento'])) ?></td>
            <td>R$ <?= number_format($cliente['valor'], 2, ',', '.') ?></td>
            <td>
                <?php
                    $data_venc = $cliente['vencimento'];
                    if ($data_venc < $hoje) {
                        echo '<span class="status vermelho">❌ Vencido</span>';
                    } elseif ($data_venc == $hoje) {
                        echo '<span class="status laranja">🟠 Vence Hoje</span>';
                    } else {
                        echo '<span class="status verde">✅ Em Dia</span>';
                    }
                ?>
            </td>
            <td>
               <a href="editar_cliente.php?id=<?= $cliente['id'] ?>">✏️ Editar</a> |
               <a href="?renovar=<?= $cliente['id'] ?>">🔄 Renovar</a> |
               <a href="?excluir=<?= $cliente['id'] ?>" onclick="return confirm('Tem certeza?')">🗑️ Excluir</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
    <a href="configuracoes.php" class="button">Credencias</a>
    <a href="logout.php" class="button">Sair</a>
    <a href="cadastrar_cliente.php" class="button">Cadastrar Usuário</a>
    <a href="send.php" class="button">Notificar</a>
</div>
</body>
</html>