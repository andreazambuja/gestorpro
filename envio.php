<?php
date_default_timezone_set('America/Sao_Paulo');
include 'conexao.php';

$hoje = date('Y-m-d');
$data_5_dias = date('Y-m-d', strtotime('+5 days'));
$data_3_dias = date('Y-m-d', strtotime('+3 days'));

$meses = [
    '01' => 'Janeiro', '02' => 'Fevereiro', '03' => 'Março',
    '04' => 'Abril', '05' => 'Maio', '06' => 'Junho',
    '07' => 'Julho', '08' => 'Agosto', '09' => 'Setembro',
    '10' => 'Outubro', '11' => 'Novembro', '12' => 'Dezembro'
];

$query = "SELECT * FROM clientes WHERE vencimento IN ('$data_5_dias', '$data_3_dias', '$hoje')";
$resultado = $conn->query($query);

while ($cliente = $resultado->fetch_assoc()) {
    $nome = $cliente['nome'];
    $telefone = preg_replace('/[^0-9]/', '', $cliente['telefone']);
    $valor_servico = number_format($cliente['valor'], 2, ',', '.');
    $formatted_due_date = date('d/m/Y', strtotime($cliente['vencimento']));
    $mes_numero = date('m', strtotime($cliente['vencimento']));
    $mes = $meses[$mes_numero];
    $nome_empresa = "FacilTV";

    // Define o tipo de lembrete
    if ($cliente['vencimento'] == $data_5_dias) {
        $tipo = "5_dias";
    } elseif ($cliente['vencimento'] == $data_3_dias) {
        $tipo = "3_dias";
    } elseif ($cliente['vencimento'] == $hoje) {
        $tipo = "hoje";
    } else {
        continue; // ignora se não for um dos três casos
    }

    // Prepara os dados para enviar à IA Groc
    $dados_ia = [
        "nome" => $nome,
        "vencimento_formatado" => $formatted_due_date,
        "valor" => $valor_servico,
        "mes" => $mes,
        "empresa" => $nome_empresa,
        "tipo" => $tipo
    ];

    // Envia os dados à IA Groc (ajuste a URL e os headers conforme a sua API)
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => "https://api.groc.ai/v1/gerar-mensagem", // exemplo fictício
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode($dados_ia),
        CURLOPT_HTTPHEADER => array(
            "Content-Type: application/json",
            "Authorization: Bearer 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"
        ),
    ));
    $resposta_ia = curl_exec($curl);
    curl_close($curl);

    $resposta = json_decode($resposta_ia, true);
    $mensagem = $resposta['mensagem'] ?? null;

    if (!$mensagem) {
        continue; // ignora se a IA não respondeu corretamente
    }

    // Envia mensagem via WhatsApp
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => "http://193.203.182.22:31000/v3/bot/adb3761d-be93-47c0-bd9c-48a636182b9d/sendtext/55{$telefone}",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => json_encode(["text" => $mensagem]),
        CURLOPT_HTTPHEADER => array("Content-Type: application/json"),
    ));
    $response = curl_exec($curl);
    curl_close($curl);
}

echo "Mensagens enviadas com sucesso!";
?>
