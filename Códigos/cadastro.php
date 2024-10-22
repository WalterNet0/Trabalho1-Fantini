<?php
// Conexão com o banco de dados
$host = '127.0.0.1';
$db   = 'a2023952500@teiacoltec.org';
$user = 'a2023952500@teiacoltec.org';
$pass = '@Coltec2024';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $conn = new PDO($dsn, $user, $pass, $options);
    //echo "Conexão bem-sucedida!";
} catch (\PDOException $e) {
    echo "Erro de conexão: " . $e->getMessage();
    exit; // Saia do script em caso de erro de conexão
}

// Inicializa as variáveis para mensagens
$mensagemErro = '';
$mensagemSucesso = ''; // Nova variável para a mensagem de sucesso

// Verifica se o formulário foi enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = trim($_POST['nome']);
    $email = trim($_POST['email']);
    $senha = trim($_POST['senha']);
    $tipo = 1; // Define um tipo padrão para o usuário (1)

    // Verifica se os campos estão preenchidos
    if (empty($nome) || empty($email) || empty($senha)) {
        $mensagemErro = "Por favor, preencha todos os campos.";
    } else {
        // Verifica se o email já está cadastrado
        $stmt = $conn->prepare("SELECT COUNT(*) FROM Usuarios WHERE email = :email");
        $stmt->execute(['email' => $email]);
        $emailExists = $stmt->fetchColumn();

        if ($emailExists) {
            $mensagemErro = "Este email já está cadastrado.";
        } else {
            // Inicia a transação
            $conn->beginTransaction();

            try {
                // Insere o usuário na tabela Usuarios
                $stmt = $conn->prepare("INSERT INTO Usuarios (nome, email, tipo) VALUES (:nome, :email, :tipo)");
                $stmt->execute(['nome' => $nome, 'email' => $email, 'tipo' => $tipo]);

                // Recupera o idUsuario gerado
                $idUsuario = $conn->lastInsertId();

                // Insere a senha na tabela Senhas
                $stmt = $conn->prepare("INSERT INTO Senhas (idUsuario, senha) VALUES (:idUsuario, :senha)");
                $stmt->execute(['idUsuario' => $idUsuario, 'senha' => password_hash($senha, PASSWORD_DEFAULT)]);

                // Confirma a transação
                $conn->commit();
                $mensagemSucesso = "Cadastro realizado com sucesso!"; // Atribui a mensagem de sucesso
            } catch (Exception $e) {
                // Reverte a transação em caso de erro
                $conn->rollBack();
                $mensagemErro = "Erro ao cadastrar: " . $e->getMessage();
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Usuário</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f8f9fa;
        }
        .container {
            background-color: #e7f3fe; /* Cor de fundo azul claro */
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 300px;
            text-align: center;
            border: 2px solid blue; /* Borda azul */
        }
        h2 {
            color: #007bff; /* Cor do título */
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h2>Cadastro de Usuário</h2>
        <form method="post">
            <div class="form-group">
                <label for="nome">Nome:</label>
                <input type="text" class="form-control" id="nome" name="nome" required>
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="senha">Senha:</label>
                <input type="password" class="form-control" id="senha" name="senha" required>
            </div>
            <button type="submit" class="btn btn-primary">Cadastrar</button>
        </form>

        <!-- Mensagem de erro exibida abaixo do botão "Cadastrar" -->
        <?php if ($mensagemErro): ?>
            <div class="mt-4 alert alert-danger"><?= $mensagemErro ?></div>
        <?php endif; ?>

        <!-- Mensagem de sucesso exibida abaixo do botão "Cadastrar" -->
        <?php if ($mensagemSucesso): ?>
            <div class="mt-4 alert alert-success"><?= $mensagemSucesso ?></div>
        <?php endif; ?>

        <p class="mt-3">Já tem uma conta? <a href="login.php">Faça login aqui!</a></p>
    </div>
</body>
</html>

