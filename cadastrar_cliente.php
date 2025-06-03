<?php
include 'conexao.php';

$mensagem = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $telefone = $_POST['telefone'];
    $vencimento = $_POST['vencimento'];
    $valor = $_POST['valor'];

    // Obter mês por extenso
    $meses = [
        '01' => 'Janeiro', '02' => 'Fevereiro', '03' => 'Março',
        '04' => 'Abril', '05' => 'Maio', '06' => 'Junho',
        '07' => 'Julho', '08' => 'Agosto', '09' => 'Setembro',
        '10' => 'Outubro', '11' => 'Novembro', '12' => 'Dezembro'
    ];
    $mesNumero = date('m', strtotime($vencimento));
    $mes = $meses[$mesNumero];

    // Inserir no banco de dados com o mês
    $stmt = $conn->prepare("INSERT INTO clientes (nome, email, telefone, vencimento, valor, mes) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $nome, $email, $telefone, $vencimento, $valor, $mes);

    if ($stmt->execute()) {
        $mensagem = 'Cliente cadastrado com sucesso!';
    } else {
        $mensagem = 'Erro ao cadastrar cliente: ' . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Cliente</title>
    <style>
        body {
            background-color: #121212;
            color: #ffffff;
            font-family: 'Segoe UI', sans-serif;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 100%;
            max-width: 600px;
            margin: 50px auto;
            padding: 30px;
            background-color: #1e1e1e;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 255, 204, 0.2);
        }

        h2 {
            text-align: center;
            color: #00ffcc;
            margin-bottom: 30px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            color: #ccc;
            font-size: 14px;
            margin-bottom: 5px;
        }

        input[type="text"], input[type="date"], input[type="number"], input[type="email"] {
            width: 100%;
            padding: 12px;
            background-color: #333;
            color: #fff;
            border: 1px solid #444;
            border-radius: 5px;
            font-size: 14px;
        }

        button {
            width: 100%;
            padding: 12px;
            background-color: #00ffcc;
            color: #121212;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
        }

        button:hover {
            background-color: #00cc99;
        }

        .message {
            text-align: center;
            font-weight: bold;
            margin-bottom: 20px;
        }

        .success {
            color: #2ecc71;
        }

        .error {
            color: #e74c3c;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Cadastro de Cliente</h2>

        <?php if ($mensagem): ?>
            <div class="message <?php echo $mensagem == 'Cliente cadastrado com sucesso!' ? 'success' : 'error'; ?>">
                <?= $mensagem ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label for="nome">Nome</label>
                <input type="text" id="nome" name="nome" required>
            </div>
            
            <div class="form-group">
                <label for="email">E-mail</label>
                <input type="email" id="email" name="email" required>
            </div>

            <div class="form-group">
                <label for="telefone">Telefone</label>
                <input type="text" id="telefone" name="telefone" required>
            </div>

            <div class="form-group">
                <label for="vencimento">Data de Vencimento</label>
                <input type="date" id="vencimento" name="vencimento" required>
            </div>

            <div class="form-group">
                <label for="valor">Valor do Serviço</label>
                <input type="text" id="valor" name="valor" required>
            </div>

            <button type="submit">Cadastrar</button>
        </form>
    </div>
</body>
</html>
