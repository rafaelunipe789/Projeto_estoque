<?php
session_start();
if (!isset($_SESSION['usuario_id'])) { header("Location: login.php"); exit; }
include("../db/conexao.php");

$acao = $_GET['acao'] ?? 'listar';
$mensagem = '';

// Listar produtos
if ($acao == 'listar') {
    $sql = "SELECT * FROM produtos ORDER BY nome";
    $result = $conn->query($sql);
}

// Adicionar
if ($acao == 'adicionar' && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = trim($_POST['nome']);
    $descricao = trim($_POST['descricao']);
    $preco = floatval($_POST['preco']);
    $quantidade = intval($_POST['quantidade']);
    
    $sql = "INSERT INTO produtos (nome, descricao, preco, quantidade) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssdi", $nome, $descricao, $preco, $quantidade);
    if ($stmt->execute()) {
        $mensagem = "Produto adicionado com sucesso!";
        header("Location: produtos.php?acao=listar");
    } else {
        $mensagem = "Erro ao adicionar.";
    }
    $stmt->close();
}

// Editar (similar, mas com UPDATE - implemente se precisar)
if ($acao == 'editar' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    // Lógica de form de edição aqui (POST para UPDATE)
}

// Excluir
if ($acao == 'excluir' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $sql = "DELETE FROM produtos WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        $mensagem = "Produto excluído!";
    }
    $stmt->close();
    header("Location: produtos.php?acao=listar");
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Produtos - Controle de Estoque</title>
    <link href="../assets/bootstrap/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="dashboard.php">Dashboard</a>
        </div>
    </nav>

    <div class="container mt-4">
        <h2>Gerenciar Produtos</h2>
        <?php if ($mensagem): ?>
            <div class="alert alert-success"><?php echo $mensagem; ?></div>
        <?php endif; ?>

        <!-- Form Adicionar (simplificado) -->
        <div class="card mb-4">
            <div class="card-header">Adicionar Produto</div>
            <div class="card-body">
                <form method="POST" action="?acao=adicionar">
                    <input type="hidden" name="acao" value="adicionar">
                    <div class="row">
                        <div class="col-md-6">
                            <input type="text" name="nome" class="form-control mb-2" placeholder="Nome" required>
                        </div>
                        <div class="col-md-6">
                            <input type="number" step="0.01" name="preco" class="form-control mb-2" placeholder="Preço" required>
                        </div>
                        <div class="col-md-6">
                            <input type="number" name="quantidade" class="form-control mb-2" placeholder="Quantidade" required>
                        </div>
                        <div class="col-md-6">
                            <textarea name="descricao" class="form-control mb-2" placeholder="Descrição"></textarea>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">Adicionar</button>
                </form>
            </div>
        </div>

        <!-- Lista de Produtos -->
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Preço</th>
                    <th>Quantidade</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo htmlspecialchars($row['nome']); ?></td>
                    <td>R$ <?php echo number_format($row['preco'], 2, ',', '.'); ?></td>
                    <td><?php echo $row['quantidade']; ?></td>
                    <td>
                        <a href="?acao=editar&id=<?php echo $row['id']; ?>" class="btn btn-sm btn-warning">Editar</a>
                        <a href="?acao=excluir&id=<?php echo $row['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Confirmar exclusão?')">Excluir</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
    <script src="../assets/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>