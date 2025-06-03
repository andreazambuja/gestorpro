<?php
date_default_timezone_set('America/Sao_Paulo');

// Configurações do banco de dados
$host = 'db';
$dbname = 'admin';
$username = 'admin';
$password = 'admin';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erro na conexão com o banco de dados: " . $e->getMessage());
}

$config = $pdo->query("SELECT * FROM configuracoes LIMIT 1")->fetch(PDO::FETCH_ASSOC);

$serverUrl = $config['server_url'];
$instance = $config['instance'];
$apiKey = $config['api_key'];
$apiUrl = "$serverUrl/message/sendText/$instance";

// Mensagens de lembrete
$mensagens_padrao = [
    5 => 'Fala aí, {nome}! 👋 Aqui é a Joice, a inteligência artificial do Grupo Facil TV. 🙂

⏳ Faltam só 5 dias pra sua assinatura vencer! Sua fatura do mês de *{mes}*.

Seu plano atual é de R$ {valor} e expira em {data}.

👉 https://gestor.faciltvgestor.online/checkout

Abraço da equipe {empresa} (Mensagem automática, não precisa responder)',

    3 => 'Fala aí, {nome}! 👋 Aqui é a Joice, a inteligência artificial do Grupo Facil TV. 🙂

⏳ Faltam só 3 dias pra sua assinatura vencer! Sua fatura do mês de *{mes}*.

Seu plano atual é de R$ {valor} e expira em {data}.

👉 https://gestor.faciltvgestor.online/checkout

Abraço da equipe {empresa} (Mensagem automática, não precisa responder)',

    0 => 'Fala aí, {nome}! 👋 Aqui é a Joice, a inteligência artificial do Grupo Facil TV. 🙂

📅 Sua fatura do mês de *{mes}* vence hoje!

Seu plano atual é de R$ {valor} e expira em {data}.

Assim que fizer o pagamento, manda o comprovante pra gente, beleza?

👉 https://gestor.faciltvgestor.online/checkout

Abraço da equipe {empresa} (Mensagem automática, não precisa responder)',
];

// Função para enviar mensagem via API
function sendMessage($number, $text, $apiUrl, $apiKey) {
    $data = [
        "number" => $number,
        "options" => [
            "delay" => 0,
            "presence" => "composing",
            "linkPreview" => true,
        ],
        "text" => $text
    ];

    $jsonData = json_encode($data);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $apiUrl);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Content-Type: application/json",
        "apikey: $apiKey"
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
    curl_setopt($ch, CURLOPT_TIMEOUT, 60);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Cuidado em produção

    $result = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_errno($ch) ? 'Erro na requisição: ' . curl_error($ch) : null;

    // Log
    file_put_contents('api_log.txt', date('Y-m-d H:i:s') . " - Número: $number, Texto: $text, Resposta: $result, HTTP: $httpCode, Erro: $error\n", FILE_APPEND);

    curl_close($ch);

    return ['response' => $result, 'httpCode' => $httpCode, 'error' => $error];
}

// Data atual
$today = new DateTime();
$today->setTime(0, 0, 0);

// Nome da empresa
$empresa = "FacilTV";

// Buscar clientes
try {
    $stmt = $pdo->query("SELECT nome, telefone, valor, vencimento, mes FROM clientes");
    $clientes = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erro ao buscar clientes: " . $e->getMessage());
}

$log = [];

foreach ($clientes as $cliente) {
    $nome = $cliente['nome'];
    $telefone = preg_replace('/[^0-9]/', '', $cliente['telefone']);
    if (strlen($telefone) == 10 || strlen($telefone) == 11) {
        $telefone = "55$telefone";
    }
    $valor = number_format($cliente['valor'], 2, ',', '');
    $vencimento = new DateTime($cliente['vencimento']);
    $vencimento->setTime(0, 0, 0);
    $mes = $cliente['mes'] ?? 'N/A';
    $data = $vencimento->format('d/m/Y');

    $interval = $today->diff($vencimento);
    $dias_ate_vencimento = $interval->days * ($interval->invert ? -1 : 1);

    if (in_array($dias_ate_vencimento, [5, 3, 0]) && $dias_ate_vencimento >= 0) {
        $template = $mensagens_padrao[$dias_ate_vencimento];

        $mensagem = str_replace(
            ['{nome}', '{mes}', '{valor}', '{data}', '{empresa}'],
            [$nome, $mes, $valor, $data, $empresa],
            $template
        );

        $result = sendMessage($telefone, $mensagem, $apiUrl, $apiKey);

        $log[] = [
            'cliente' => $nome,
            'telefone' => $telefone,
            'mensagem' => $mensagem,
            'httpCode' => $result['httpCode'],
            'response' => $result['response'],
            'error' => $result['error'],
            'apiUrl' => $apiUrl
        ];
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Envio de Lembretes</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; background: #f0f0f0; }
        h1 { color: #333; }
        .log { margin-top: 20px; }
        .log-entry { padding: 15px; margin-bottom: 10px; border-radius: 6px; }
        .success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .error { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
    </style>
</head>
<body>
    <h1>Relatório de Envio de Lembretes - <?= date('d/m/Y H:i:s') ?></h1>
    <div class="log">
        <?php foreach ($log as $entry): ?>
            <div class="log-entry <?= $entry['error'] ? 'error' : 'success' ?>">
                <strong>Cliente:</strong> <?= htmlspecialchars($entry['cliente']) ?><br>
                <strong>Telefone:</strong> <?= htmlspecialchars($entry['telefone']) ?><br>
                <strong>Mensagem:</strong> <?= nl2br(htmlspecialchars($entry['mensagem'])) ?><br>
                <strong>API URL:</strong> <?= htmlspecialchars($entry['apiUrl']) ?><br>
                <strong>Status:</strong> HTTP <?= $entry['httpCode'] ?><br>
                <strong>Resposta:</strong> <?= htmlspecialchars($entry['response']) ?><br>
                <?php if ($entry['error']): ?>
                    <strong>Erro:</strong> <?= htmlspecialchars($entry['error']) ?><br>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
        <?php if (empty($log)): ?>
            <p>Nenhum lembrete enviado hoje.</p>
        <?php endif; ?>
    </div>
</body>
</html>
