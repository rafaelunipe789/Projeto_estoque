<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}
include("../db/conexao.php");

// Exemplo: Buscar total de produtos
$sql_total = "SELECT COUNT(*) as total FROM produtos";
$result_total = $conn->query($sql_total);
$total_produtos = $result_total->fetch_assoc()['total'];
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - Controle de Estoque</title>
    <link href="../assets/bootstrap/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="#">Controle de Estoque</a>
            <div class="navbar-nav ms-auto">
                <span class="navbar-text me-3">Olá, <?php echo $_SESSION['usuario_nome']; ?>!</span>
                <a class="nav-link" href="?logout=1">Sair</a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <h2>Dashboard</h2>
        <div class="row">
            <div class="col-md-3">
                <div class="card text-white bg-primary">
                    <div class="card-body">
                        <h5>Total de Produtos</h5>
                        <h2><?php echo $total_produtos; ?></h2>
                    </div>
                </div>
            </div>
            <!-- Adicione mais cards para vendas, etc. -->
        </div>
        <div class="row mt-4">
            <div class="col-md-4">
                <a href="produtos.php" class="btn btn-success w-100">Gerenciar Produtos</a>
            </div>
            <div class="col-md-4">
                <a href="vendas.php" class="btn btn-info w-100">Nova Venda</a>
            </div>
            <div class="col-md-4">
                <a href="#" class="btn btn-warning w-100">Relatórios</a> <!-- Expanda na Parte 32 -->
            </div>
        </div>
    </div>

    <?php if (isset($_GET['logout'])) {
        session_destroy();
        header("Location: login.php");
        exit;
    } ?>

    <script src="../assets/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>