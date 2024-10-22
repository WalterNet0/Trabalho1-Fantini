<?php
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['idUsuario'])) {
    header("Location: login.php");
    exit;
}

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
} catch (\PDOException $e) {
    echo "Erro de conexão: " . $e->getMessage();
    exit;
}

// Captura o idUsuario da URL
if (isset($_GET['idUsuario'])) {
    $idUsuario = $_GET['idUsuario'];
} else {
    echo "ID do usuário não foi fornecido.";
    exit;
}

// Busca os dados do usuário para edição
$stmt = $conn->prepare("SELECT nome, email, tipo FROM Usuarios WHERE idUsuario = :idUsuario");
$stmt->execute(['idUsuario' => $idUsuario]);
$usuario = $stmt->fetch();

if (!$usuario) {
    echo "Usuário não encontrado.";
    exit;
}

// Verifica se o formulário foi enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $novoNome = trim($_POST['nome']);
    $novoEmail = trim($_POST['email']);
    $novaSenha = trim($_POST['senha']);

    // Atualiza o nome se fornecido
    if (!empty($novoNome)) {
        $stmt = $conn->prepare("UPDATE Usuarios SET nome = :nome WHERE idUsuario = :idUsuario");
        if (!$stmt->execute(['nome' => $novoNome, 'idUsuario' => $idUsuario])) {
            echo "Erro ao atualizar o nome.";
            print_r($stmt->errorInfo());
            exit;
        }
    }

    $_SESSION['nome'] = $novoNome;

    // Atualiza o email se fornecido
    if (!empty($novoEmail)) {
        $stmt = $conn->prepare("UPDATE Usuarios SET email = :email WHERE idUsuario = :idUsuario");
        if (!$stmt->execute(['email' => $novoEmail, 'idUsuario' => $idUsuario])) {
            echo "Erro ao atualizar o email.";
            print_r($stmt->errorInfo());
            exit;
        }
    }

    $_SESSION['email'] = $novoEmail;

    // Se o usuário for admin, atualiza o tipo de usuário
    if ($_SESSION['tipo'] == 3 && !empty($_POST['tipo'])) {
        $tipoUsuario = trim($_POST['tipo']);
        switch ($tipoUsuario) {
            case '1':
                $novoTipo = 1;
                break;
            case '2':
                $novoTipo = 2;
                break;
            case '3':
                $novoTipo = 3;
                break;
            default:
                echo "Tipo de usuário inválido. Digite 1 para Usuário, 2 para Gerente ou 3 para Administrador.";
                exit;
        }

        $stmt = $conn->prepare("UPDATE Usuarios SET tipo = :tipo WHERE idUsuario = :idUsuario");
        if (!$stmt->execute(['tipo' => $novoTipo, 'idUsuario' => $idUsuario])) {
            echo "Erro ao atualizar o tipo.";
            print_r($stmt->errorInfo());
            exit;
        }
    }

    // Atualiza a senha se fornecida
    if (!empty($novaSenha)) {
        $hashedPassword = password_hash($novaSenha, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE Senhas SET senha = :senha WHERE idUsuario = :idUsuario");
        if (!$stmt->execute(['senha' => $hashedPassword, 'idUsuario' => $idUsuario])) {
            echo "Erro ao atualizar a senha.";
            exit;
        }
    }

    // Redireciona para a home correspondente
    if ($_SESSION['tipo'] == 3) {
        header("Location: home_admin.php");
    } elseif($_SESSION['tipo'] == 2) {
        header("Location: home_gerente.php");
    } else{
        header("Location: home.php");
    }
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Usuário</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            height: 100vh; /* Define a altura total da tela */
            background-color: #f8f9fa; /* Cor de fundo da página */
            margin: 0; /* Remove margens padrão */
        }
        .container {
            background-color: #e7f3fe; /* Cor de fundo azul claro */
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 80%; /* Largura ajustável */
            max-width: 600px; /* Largura máxima para a caixa */
            border: 3px solid #007bff; /* Borda azul espessa */
        }
        h2 {
            color: #007bff; /* Cor do título */
            text-align: center; /* Centraliza o título */
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h2>Editar Usuário</h2>
        <form method="post">
            <div class="form-group">
                <label for="nome">Nome:</label>
                <input type="text" class="form-control" id="nome" name="nome" value="<?php echo htmlspecialchars($usuario['nome']); ?>">
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($usuario['email']); ?>">
            </div>
            
            <!-- Exibe o campo Tipo de Usuário somente para administradores -->
            <?php if ($_SESSION['tipo'] == 3): ?>
            <div class="form-group">
                <label for="tipo">Tipo de Usuário (digite 1-3):</label>
                <input type="text" class="form-control" id="tipo" name="tipo" placeholder="1: Usuário, 2: Gerente, 3: Administrador">
            </div>
            <?php endif; ?>

            <div class="form-group">
                <label for="senha">Nova Senha (deixe em branco para não alterar):</label>
                <input type="password" class="form-control" id="senha" name="senha">
            </div>

            <div class="d-flex justify-content-between">
                <button type="submit" class="btn btn-primary">Atualizar</button>
                <a href="<?php echo ($_SESSION['tipo'] == 3) ? 'home_admin.php' : ($_SESSION['tipo'] == 2 ? 'home_gerente.php' : 'home.php'); ?>" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</body>
</html>

