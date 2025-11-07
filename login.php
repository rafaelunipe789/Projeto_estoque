<?php
session_start();
include("../db/conexao.php");

$erro = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $senha = $_POST['senha'];

    if (empty($email) || empty($senha)) {
        $erro = "Preencha todos os campos!";
    } else {
        $sql = "SELECT * FROM usuarios WHERE email = ?";
        try {
            $stmt = $conn->prepare($sql);
            if (!$stmt) {
                throw new Exception("Erro na query: " . $conn->error);
            }
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $resultado = $stmt->get_result();

            if ($resultado->num_rows > 0) {
                $user = $resultado->fetch_assoc();
                if (password_verify($senha, $user['senha'])) {
                    $_SESSION['usuario_id'] = $user['id'];
                    $_SESSION['usuario_nome'] = $user['nome'];
                    header("Location: dashboard.php");
                    exit;
                } else {
                    $erro = "Senha incorreta!";
                }
            } else {
                $erro = "Usuário não encontrado!";
            }
            $stmt->close();
        } catch (Exception $e) {
            error_log("Erro no login: " . $e->getMessage());
            $erro = "Erro no sistema. Tente novamente.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Login - Controle de Estoque</title>
    <link href="../assets/bootstrap/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex justify-content-center align-items-center vh-100">
    <div class="card shadow p-4" style="width: 22rem;">
        <h4 class="text-center mb-3">Login do Administrador</h4>
        <?php if (!empty($erro)): ?>
            <div class="alert alert-danger"><?php echo $erro; ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Email:</label>
                <input type="email" name="email" class="form-control" required value="<?php echo isset($_POST['email']) ? $_POST['email'] : ''; ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">Senha:</label>
                <input type="password" name="senha" class="form-control" required>
            </div>
            <button class="btn btn-primary w-100" type="submit">Entrar</button>
        </form>
    </div>
    <script src="../assets/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>