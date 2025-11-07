<?php
$host = 'localhost';
$usuario = 'root';  // Padrão XAMPP
$senha_bd = '';     // Padrão XAMPP (vazio)
$banco = 'estoque';

$conn = new mysqli($host, $usuario, $senha_bd, $banco);

if ($conn->connect_error) {
    die("Erro de conexão: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");

// Opcional: Ativar exceções para erros (mas com try-catch para não quebrar)
mysqli_report(MYSQLI_REPORT_OFF);  // Evita fatal errors como o seu
?>