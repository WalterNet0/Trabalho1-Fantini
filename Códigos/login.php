<?php
session_start(); // Inicie a sessão

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

// Página normal para usuários regulares
try {
    $conn = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    echo "Erro de conexão: " . $e->getMessage();
    exit; // Saia do script em caso de erro de conexão
}

// Inicializa as variáveis para as mensagens
$mensagemInativa = '';
$mensagemErro = '';

// Verifica se o formulário foi enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $senha = trim($_POST['senha']);

    // Verifica se os campos estão preenchidos
    if (empty($email) || empty($senha)) {
        echo "Por favor, preencha todos os campos.";
    } else {
        // Verifica se o email está cadastrado
        $stmt = $conn->prepare("SELECT idUsuario, nome, email, tipo, ativo FROM Usuarios WHERE email = :email");
        $stmt->execute(['email' => $email]);
        $usuario = $stmt->fetch();
        
        // Verifica se o usuário existe
        if ($usuario) {
            // Verifica se o usuário está ativo
            if (!$usuario['ativo']) {
                // Define a mensagem de conta inativa
                $mensagemInativa = '<div class="alert alert-danger">Sua conta está inativa.</div>';
            } else {
                // Agora, verifique a senha na tabela Senhas
                $stmt = $conn->prepare("SELECT * FROM Senhas WHERE idUsuario = :idUsuario");
                $stmt->execute(['idUsuario' => $usuario['idUsuario']]);
                $senhaDb = $stmt->fetch();

                // Verifica se a senha está correta
                if ($senhaDb && password_verify($senha, $senhaDb['senha'])) {
                    // Se a senha estiver correta, inicia a sessão
                    $_SESSION['idUsuario'] = $usuario['idUsuario'];
                    $_SESSION['nome'] = $usuario['nome'];
                    $_SESSION['email'] = $usuario['email'];
                    $_SESSION['tipo'] = $usuario['tipo'];
            
                    // Redireciona com base no tipo de usuário
                    if ($usuario['tipo'] == 1) {
                        header("Location: home.php"); 
                    } else if($usuario['tipo'] == 2){
                        $_SESSION['nomeGerente'] = $usuario['nome'];
                        header("Location: home_gerente.php");
                    }else if ($usuario['tipo'] == 3){
                        $_SESSION['nomeAdmin'] = $usuario['nome'];
                        header("Location: home_admin.php");
                    }
                    exit;
                } else {
                    // Define a mensagem de erro para email ou senha incorretos
                    $mensagemErro = '<div class="alert alert-danger">Email ou senha incorretos.</div>';
                }
            }
        } else {
            // Define a mensagem de erro para email ou senha incorretos
            $mensagemErro = '<div class="alert alert-danger">Email ou senha incorretos.</div>';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh; /* Altura total da tela */
            background-color: #f8f9fa; /* Cor de fundo */
        }
        .container {
            background-color: #e7f3fe; /* Cor de fundo azul claro */
            padding: 20px; /* Espaçamento interno */
            border-radius: 8px; /* Bordas arredondadas */
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Sombra */
            width: 300px; /* Largura fixa */
            text-align: center; /* Alinhamento do texto */
            border: 2px solid blue; /* Borda azul */
        }
        h2 {
            color: #007bff; /* Cor do título */
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h2>Login</h2>

        <form method="post">
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="senha">Senha:</label>
                <input type="password" class="form-control" id="senha" name="senha" required>
            </div>
            <button type="submit" class="btn btn-primary">Entrar</button>
        </form>
        
        <!-- Mensagem de conta inativa exibida abaixo do botão "Entrar" -->
        <?php if ($mensagemInativa): ?>
            <div class="mt-4"><?= $mensagemInativa ?></div>
        <?php endif; ?>
        
        <!-- Mensagem de erro exibida abaixo do botão "Entrar" -->
        <?php if ($mensagemErro): ?>
            <div class="mt-4"><?= $mensagemErro ?></div>
        <?php endif; ?>

        <p class="mt-3">Não tem uma conta? <a href="cadastro.php">Cadastre-se aqui!</a></p>
    </div>
</body>
</html>

