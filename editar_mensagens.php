<?php
include 'conexao.php';

$mensagem = "";

// Mensagens padrão
$mensagens_padrao = [
    '5_dias' => 'Fala aí, $nome! 👋 Aqui é a Joice, a inteligência artificial do Grupo Facil TV. 🙂

⏳ Faltam só 5 dias pra sua assinatura vencer! Sua fatura do mês de *$mes*.

Seu plano atual é de R$ $valor_servico e expira em $formatted_due_date.

👉 https://gestor.faciltvgestor.online/checkout

Abraço da equipe $nome_empresa (Mensagem automática, não precisa responder)',

    '3_dias' => 'Fala aí, $nome! 👋 Aqui é a Joice, a inteligência artificial do Grupo Facil TV. 🙂

⏳ Faltam só 3 dias pra sua assinatura vencer! Sua fatura do mês de *$mes*.

Seu plano atual é de R$ $valor_servico e expira em $formatted_due_date.

👉 https://gestor.faciltvgestor.online/checkout

Abraço da equipe $nome_empresa (Mensagem automática, não precisa responder)'
];

// Salvar edições
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['mensagens'])) {
    foreach ($_POST['mensagens'] as $tipo => $conteudo) {
        $check = $conn->prepare("SELECT COUNT(*) FROM mensagens WHERE tipo = ?");
        $check->bind_param("s", $tipo);
        $check->execute();
        $check->bind_result($count);
        $check->fetch();
        $check->close();

        if ($count > 0) {
            $stmt = $conn->prepare("UPDATE mensagens SET conteudo = ? WHERE tipo = ?");
            $stmt->bind_param("ss", $conteudo, $tipo);
        } else {
            $stmt = $conn->prepare("INSERT INTO mensagens (tipo, conteudo) VALUES (?, ?)");
            $stmt->bind_param("ss", $tipo, $conteudo);
        }
        $stmt->execute();
        $stmt->close();
    }
    $mensagem = "Mensagens atualizadas com sucesso!";
}

// Restaurar padrão
if (isset($_POST['reset_padrao'])) {
    foreach ($mensagens_padrao as $tipo => $conteudo) {
        $check = $conn->prepare("SELECT COUNT(*) FROM mensagens WHERE tipo = ?");
        $check->bind_param("s", $tipo);
        $check->execute();
        $check->bind_result($count);
        $check->fetch();
        $check->close();

        if ($count > 0) {
            $stmt = $conn->prepare("UPDATE mensagens SET conteudo = ? WHERE tipo = ?");
            $stmt->bind_param("ss", $conteudo, $tipo);
        } else {
            $stmt = $conn->prepare("INSERT INTO mensagens (tipo, conteudo) VALUES (?, ?)");
            $stmt->bind_param("ss", $tipo, $conteudo);
        }
        $stmt->execute();
        $stmt->close();
    }
    $mensagem = "Mensagens restauradas para o padrão!";
}

// Buscar mensagens existentes
$mensagens = [];
$result = $conn->query("SELECT tipo, conteudo FROM mensagens");
while ($row = $result->fetch_assoc()) {
    $mensagens[$row['tipo']] = $row['conteudo'];
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Editar Mensagens</title>
    <style>
        body {
            background-color: #121212;
            color: #eee;
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            padding: 40px;
        }
        form {
            background: #1e1e1e;
            padding: 20px;
            border-radius: 10px;
            width: 800px;
        }
        textarea {
            width: 100%;
            height: 200px;
            margin-bottom: 20px;
            padding: 10px;
            background: #2c2c2c;
            color: #fff;
            border: none;
            border-radius: 5px;
            font-family: monospace;
        }
        input[type="submit"], button {
            background-color: #28a745;
            border: none;
            color: white;
            padding: 12px 20px;
            font-size: 16px;
            border-radius: 5px;
            cursor: pointer;
            margin-right: 10px;
        }
        button[name="reset_padrao"] {
            background-color: #dc3545;
        }
        .mensagem {
            margin-top: 10px;
            background: #2c2c2c;
            padding: 10px;
            border-left: 5px solid #28a745;
        }
    </style>
</head>
<body>
    <form method="POST">
        <h2>Editar Mensagens de Notificação</h2>

        <label for="5_dias">Mensagem - 5 Dias Antes:</label>
        <textarea name="mensagens[5_dias]"><?= htmlspecialchars($mensagens['5_dias'] ?? '') ?></textarea>

        <label for="3_dias">Mensagem - 3 Dias Antes:</label>
        <textarea name="mensagens[3_dias]"><?= htmlspecialchars($mensagens['3_dias'] ?? '') ?></textarea>

        <input type="submit" value="Salvar Mensagens">
        <button type="submit" name="reset_padrao">Restaurar Padrão</button>

        <?php if ($mensagem): ?>
            <div class="mensagem"><?= $mensagem ?></div>
        <?php endif; ?>
    </form>
</body>
</html>
