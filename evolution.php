<?php 
include 'conexao.php';
date_default_timezone_set('America/Sao_Paulo');

// Datas para vencimento
$hoje = date('Y-m-d');
$data_3_dias = date('Y-m-d', strtotime('+3 days'));
$data_5_dias = date('Y-m-d', strtotime('+5 days'));

// Meses em português
$meses = [
    '01' => 'Janeiro', '02' => 'Fevereiro', '03' => 'Março',
    '04' => 'Abril', '05' => 'Maio', '06' => 'Junho',
    '07' => 'Julho', '08' => 'Agosto', '09' => 'Setembro',
    '10' => 'Outubro', '11' => 'Novembro', '12' => 'Dezembro'
];

// Buscar URL e API KEY do banco de dados
$configs = [];
$sql_config = "SELECT nome_config, valor FROM configuracoes";
$result_config = $conn->query($sql_config);
if ($result_config->num_rows > 0) {
    while ($row = $result_config->fetch_assoc()) {
        $configs[$row['nome_config']] = $row['valor'];
    }
}
$url = isset($configs['url_api']) ? $configs['url_api'] : '';
$apikey = isset($configs['api_key']) ? $configs['api_key'] : '';
if (empty($url) || empty($apikey)) {
    die("Erro: URL da API ou API Key não configurados no banco.");
}

// Consulta dos clientes com vencimento
$query = "SELECT nome, telefone, vencimento, valor FROM clientes WHERE vencimento IN ('$hoje', '$data_3_dias', '$data_5_dias')";
$result = $conn->query($query);

if ($result->num_rows > 0) {
    while ($cliente = $result->fetch_assoc()) {
        $nome = $cliente['nome'];
        $telefone = preg_replace('/\D/', '', $cliente['telefone']); // Remove caracteres não numéricos
        $telefone = (strpos($telefone, '55') === 0) ? $telefone : '55' . $telefone;
        $valor_servico = number_format($cliente['valor'], 2, ',', '.');
        $formatted_due_date = date('d/m/Y', strtotime($cliente['vencimento']));
        $mes_num = date('m', strtotime($cliente['vencimento']));
        $mes = $meses[$mes_num] ?? '';
        $nome_empresa = "Facil TV";

        // Mensagem inicial baseada na data de vencimento
        $mensagem_inicial = "";
        $mensagem_pix = "👉 Chave Pix : 21984968082 André Luiz - Banco Bradesco";

        if ($cliente['vencimento'] == $data_5_dias) {
            $mensagem_inicial = "Fala aí, $nome! 👋 Aqui é a Joice, a inteligência artificial do Grupo Facil TV. 🙂

⏳ Faltam só 5 dias pra sua assinatura vencer! Sua fatura do mês de *$mes*.

Seu plano atual é de R\$ $valor_servico e expira em $formatted_due_date.

Abraço da equipe $nome_empresa (Mensagem automática, não precisa responder)";
        } elseif ($cliente['vencimento'] == $data_3_dias) {
            $mensagem_inicial = "Fala aí, $nome! 👋 Aqui é a Joice, a inteligência artificial do Grupo Facil TV. 🙂

⏳ Faltam só 3 dias pra sua assinatura vencer! Sua fatura do mês de *$mes*.

Seu plano atual é de R\$ $valor_servico e expira em $formatted_due_date.

Abraço da equipe $nome_empresa (Mensagem automática, não precisa responder)";
        } elseif ($cliente['vencimento'] == $hoje) {
            $mensagem_inicial = "E aí, tudo certo? 👋 $nome Aqui é a Joice, a inteligência artificial do Grupo Facil TV. 🙂

📅 Sua fatura do mês de *$mes* vence hoje!

O seu plano atual no valor de R\$ $valor_servico expira em $formatted_due_date.

Assim que fizer o pagamento, manda o comprovante pra gente, beleza?

Abraço da equipe $nome_empresa (Mensagem automática, não precisa responder)";
        }

        $mensagem_final = $mensagem_inicial . "\n\n" . $mensagem_pix;

        // Enviar via API
        $data = [
            "number" => $telefone,
            "textMessage" => ["text" => $mensagem_final]
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'apikey: ' . $apikey,
            'Authorization: Bearer ' . $apikey
        ]);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            echo "Erro ao enviar para $nome ($telefone): " . curl_error($ch) . "<br>";
        } else {
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            if ($http_code == 200) {
                echo "Mensagem enviada para $nome ($telefone).<br>";
            } else {
                echo "Erro ao enviar para $nome ($telefone). Código HTTP: $http_code. Resposta: $response<br>";
            }
        }

        curl_close($ch);
    }
} else {
    echo "Nenhum cliente com vencimento hoje, em 3 ou 5 dias.";
}

$conn->close();
?>
