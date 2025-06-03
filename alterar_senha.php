<?php
session_start();
include 'conexao.php';

$mensagem = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $senha_atual = $_POST['senha_atual'];
    $nova_senha = $_POST['nova_senha'];

    // Verificar se a conta existe com essa senha atual
    $sql = "SELECT * FROM usuarios WHERE email = '$email' AND senha = '$senha_atual'";
    $resultado = $conn->query($sql);

    if ($resultado->num_rows > 0) {
        // Atualizar a senha
        $sql_update = "UPDATE usuarios SET senha = '$nova_senha' WHERE email = '$email'";
        if ($conn->query($sql_update) === TRUE) {
            $mensagem = "Senha alterada com sucesso!";
        } else {
            $mensagem = "Erro ao atualizar a senha.";
        }
    } else {
        $mensagem = "E-mail ou senha atual incorretos.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Alterar Senha</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background: #121212;
      color: white;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
    }
    .form-box {
      background: #1e1e1e;
      padding: 40px;
      border-radius: 12px;
      box-shadow: 0 0 20px rgba(0,0,0,0.3);
      max-width: 400px;
      width: 100%;
    }
    input {
      width: 100%;
      padding: 12px;
      margin: 10px 0;
      background: #2c2c2c;
      border: none;
      border-radius: 8px;
      color: white;
    }
    button {
      width: 100%;
      padding: 12px;
      background-color: #00C853;
      border: none;
      color: white;
      font-weight: bold;
      border-radius: 8px;
      cursor: pointer;
      margin-top: 10px;
    }
    .mensagem {
      text-align: center;
      margin-top: 15px;
      color: #00C853;
    }
  </style>
</head>
<body>
  <div class="form-box">
    <h2>Alterar Senha</h2>
    <form method="POST">
      <input type="email" name="email" placeholder="Seu e-mail" required>
      <input type="password" name="senha_atual" placeholder="Senha atual" required>
      <input type="password" name="nova_senha" placeholder="Nova senha" required>
      <button type="submit">Atualizar Senha</button>
    </form>
    <?php if ($mensagem): ?>
      <div class="mensagem"><?= $mensagem ?></div>
    <?php endif; ?>
  </div>
</body>
</html>
