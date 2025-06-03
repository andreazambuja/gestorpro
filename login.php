<?php
session_start(); // Iniciar a sessão

$erro = ''; // ← adicionado aqui

// Verifique se o usuário já está logado e redirecione para a dashboard
if (isset($_SESSION['email'])) {
    header("Location: dashboard.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    include 'conexao.php'; // Conectar ao banco de dados

    // Recuperando os dados do formulário
    $email = $_POST['email'];
    $senha = $_POST['senha'];

    // Verificando as credenciais do usuário
    $sql = "SELECT * FROM usuarios WHERE email = '$email' AND senha = '$senha'";
    $resultado = $conn->query($sql);

    if ($resultado->num_rows > 0) {
        $usuario = $resultado->fetch_assoc();

        // Armazenar informações do usuário na sessão
        $_SESSION['email'] = $usuario['email'];
        $_SESSION['nome'] = $usuario['nome'];

        // Redireciona para a dashboard
        header("Location: dashboard.php");
        exit();
    } else {
        $erro = "E-mail ou senha incorretos!";
    }
}
?>


<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Login</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <style>
    * {
      box-sizing: border-box;
    }

    body {
      margin: 0;
      padding: 0;
      background: linear-gradient(145deg, #0f2027, #203a43, #2c5364);
      font-family: 'Poppins', sans-serif;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      color: white;
      animation: fadeIn 1s ease-in;
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
    }

    .login-box {
      background-color: #1e1e1e;
      padding: 40px;
      border-radius: 16px;
      box-shadow: 0 10px 25px rgba(0,0,0,0.4);
      width: 100%;
      max-width: 380px;
    }

    .login-box h2 {
      text-align: center;
      margin-bottom: 25px;
      font-weight: 600;
      color: #00C853;
    }

    input[type="email"],
    input[type="password"] {
      width: 100%;
      padding: 14px;
      margin-bottom: 15px;
      border: none;
      border-radius: 10px;
      background-color: #2c2c2c;
      color: #fff;
      transition: 0.3s;
    }

    input:focus {
      outline: none;
      background-color: #3a3a3a;
      box-shadow: 0 0 0 2px #00C853;
    }

    button {
      width: 100%;
      padding: 14px;
      background-color: #00C853;
      border: none;
      color: white;
      border-radius: 10px;
      cursor: pointer;
      font-weight: bold;
      font-size: 16px;
      transition: background-color 0.3s ease;
    }

    button:hover {
      background-color: #00b34a;
    }

    .erro {
      color: #ff5252;
      text-align: center;
      margin-bottom: 15px;
      font-weight: 500;
    }
  </style>
</head>
<body>
  <div class="login-box">
    <h2>Bem Vindo (a)!</h2>
    <?php if ($erro): ?>
      <div class="erro"><?= $erro ?></div>
    <?php endif; ?>
    <form method="POST">
      <input type="email" name="email" placeholder="Digite seu e-mail" required>
      <input type="password" name="senha" placeholder="Digite sua senha" required>
      <button type="submit">Entrar</button>
      <a href="alterar_senha.php" style="color:#00C853; display:block; text-align:center; margin-top:15px;">Alterar senha</a>
    </form>
  </div>
</body>
</html>