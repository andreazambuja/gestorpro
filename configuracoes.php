<?php
// Mostrar erros na tela (para debug)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Conexão com o banco
$host = 'db';
$dbname = 'admin';
$username = 'admin';
$password = 'admin';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erro na conexão: " . $e->getMessage());
}

// Atualiza as configurações ao enviar o formulário
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $server_url = $_POST['server_url'] ?? '';
    $instance = $_POST['instance'] ?? '';
    $api_key = $_POST['api_key'] ?? '';

    // Verifica se o registro existe
    $check = $pdo->query("SELECT COUNT(*) FROM configuracoes WHERE id = 1")->fetchColumn();

    if ($check) {
        // Atualiza se existir
        $sql = "UPDATE configuracoes SET server_url = ?, instance = ?, api_key = ? WHERE id = 1";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$server_url, $instance, $api_key]);
    } else {
        // Insere se não existir
        $sql = "INSERT INTO configuracoes (id, server_url, instance, api_key) VALUES (1, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$server_url, $instance, $api_key]);
    }

    $mensagem = "Configurações atualizadas com sucesso!";
}

// Busca dados atuais
$config = $pdo->query("SELECT * FROM configuracoes WHERE id = 1")->fetch(PDO::FETCH_ASSOC);
$server_url = $config['server_url'] ?? '';
$instance = $config['instance'] ?? '';
$api_key = $config['api_key'] ?? '';
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Editar Configurações da API</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f7f7f7; padding: 40px; }
        form { max-width: 600px; margin: auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        label { display: block; margin-top: 15px; font-weight: bold; }
        input[type="text"], textarea {
            width: 100%; padding: 10px; margin-top: 5px;
            border: 1px solid #ccc; border-radius: 4px;
        }
        button {
            margin-top: 20px; padding: 10px 20px;
            background: #007BFF; color: white; border: none; border-radius: 4px;
            cursor: pointer;
        }
        .mensagem { color: green; margin-top: 15px; }
    </style>
</head>
<body>
    <form method="POST">
        <h2>Configurações da API</h2>

        <?php if (isset($mensagem)): ?>
            <p class="mensagem"><?= htmlspecialchars($mensagem) ?></p>
        <?php endif; ?>

        <label for="server_url">Server URL</label>
        <input type="text" name="server_url" id="server_url" value="<?= htmlspecialchars($server_url) ?>">

        <label for="instance">Instância</label>
        <input type="text" name="instance" id="instance" value="<?= htmlspecialchars($instance) ?>">

        <label for="api_key">API Key</label>
        <textarea name="api_key" id="api_key" rows="4"><?= htmlspecialchars($api_key) ?></textarea>

        <button type="submit">Salvar</button>
    </form>
</body>
</html>
