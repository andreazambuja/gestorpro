<?php
include 'conexao.php';

$mensagem = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];

    // Verifica se o e-mail existe
    $sql = "SELECT * FROM usuarios WHERE email = '$email'";
    $resultado = $conn->query($sql);

    if ($resultado->num_rows > 0) {
        // Gera token e define validade
        $token = bin2hex(random_bytes(32));
        $expira_em = date('Y-m-d H:i:s', strtotime('+1 hour'));

        // Armazena no banco
        $conn->query("INSERT INTO recuperacao_senha (email, token, expira_em) VALUES ('$email', '$token', '$expira_em')");

        // Envia e-mail (ajuste seu servidor de e-mail real)
        $link = "http:193.203.182.22:8080/alterar_senha.php?token=$token";
        $assunto = "Recuperação de Senha";
        $mensagem_email = "Clique no link abaixo para redefinir sua senha:\n\n$link";

        mail($email, $assunto, $mensagem_email, "From: suporte@seusite.com");

        $mensagem = "Um link de recuperação foi enviado para seu e-mail.";
    } else {
        $mensagem = "E-mail não encontrado.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Esqueci a Senha</title>
</head>
<body>
  <h2>Recuperar Senha</h2>
  <form method="POST">
    <input type="email" name="email" placeholder="Digite seu e-mail" required>
    <button type="submit">Enviar link de recuperação</button>
  </form>
  <p><?= $mensagem ?></p>
</body>
</html>
