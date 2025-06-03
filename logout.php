<?php
session_start();
session_destroy(); // Destrói todas as sessões
header("Location: login.php"); // Redireciona para o login
exit();
