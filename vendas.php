<?php
session_start();
if (!isset($_SESSION['usuario_id'])) { header("Location: login.php"); exit; }
include("../db/conexao.php");

$mensagem = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $cliente_nome = trim($_POST['cliente_nome']);
    $produto_id = intval($_POST['produto_id']);
    $quantidade = intval($_POST['quantidade']);

    // Buscar produto para preço e estoque
    $sql_prod = "SELECT nome, preco, quantidade FROM produtos WHERE id = ?";
    $stmt_prod = $conn->prepare($sql_prod);
    $stmt_prod->bind_param("i", $produto_id);
    $stmt_prod->execute();
    $prod = $stmt_prod->get_result()->fetch_assoc();
    $stmt_prod->close();

    if ($prod && $quantidade <= $prod['quantidade']) {
        $total = $quantidade * $prod['preco'];

        // Inserir venda (simplificado, sem cliente ID por enquanto)
        $sql_venda = "INSERT INTO vendas (produto_id, quantidade, total) VALUES (?, ?, ?)";
        $stmt_venda = $conn->prepare($sql_venda);
        $stmt_venda->bind_param("iid", $produto_id, $quantidade, $total);
        if ($stmt_venda->execute()) {
            // Atualizar estoque
            $nova_qtd = $prod['quantidade'] - $quantidade;
            $sql_update = "UPDATE produtos SET quantidade = ? WHERE id = ?";
            $stmt_update = $conn->prepare($sql_update);
            $stmt_update->bind_param("ii", $nova_qtd, $produto_id);
            $stmt_update->execute();
            $stmt_update->close();

            $mensagem = "Venda registrada! Total: R$ " . number_format($total, 2, ',', '.');
        }
        $stmt_venda->close();
    } else {
        $mensagem = "Estoque insuficiente ou produto inválido!";
    }
}

// Listar produtos para select
$sql_prods = "SELECT id, nome, preco FROM produtos WHERE quantidade > 0";
$prods_result = $conn->query($sql_prods);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Nova Venda</title>
    <link href="../assets/bootstrap/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="dashboard.php">Dashboard</a>
        </div>
    </nav>

    <div class="container mt-4">
        <h2>Nova Venda</h2>
        <?php if ($mensagem): ?>
            <div class="alert alert-info"><?php echo $mensagem; ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Nome do Cliente:</label>
                <input type="text" name="cliente_nome" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Produto:</label>
                <select name="produto_id" class="form-select" required>
                    <option value="">Selecione...</option>
                    <?php while ($prod = $prods_result->fetch_assoc()): ?>
                        <option value="<?php echo $prod['id']; ?>">
                            <?php echo htmlspecialchars($prod['nome']) . ' (R$ ' . number_format($prod['preco'], 2) . ')'; ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Quantidade:</label>
                <input type="number" name="quantidade" class="form-control" min="1" required>
            </div>
            <button type="submit" class="btn btn-success">Registrar Venda</button>
            <a href="dashboard.php" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>
    <script src="../assets/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>