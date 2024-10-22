<?php
session_start();

if (!isset($_SESSION['idUsuario'])) {
    // Se a sessão não existir, redireciona para a página de login
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

// Obtém o ID do usuário a ser ativado
$idUsuario = $_GET['idUsuario'] ?? null;

if ($idUsuario) {
    // Consulta para obter informações do usuário
    $stmt = $conn->prepare("SELECT * FROM Usuarios WHERE idUsuario = :idUsuario");
    $stmt->bindParam(':idUsuario', $idUsuario, PDO::PARAM_INT);
    $stmt->execute();
    $usuario = $stmt->fetch();

    if (!$usuario) {
        echo "Usuário não encontrado.";
        exit;
    }
} else {
    echo "ID do usuário não fornecido.";
    exit;
}

// Verifica se o formulário foi enviado para confirmar a ação
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Executa a alteração do status do usuário para ativo
    $stmt = $conn->prepare("UPDATE Usuarios SET ativo = 1 WHERE idUsuario = :idUsuario");
    $stmt->bindParam(':idUsuario', $idUsuario, PDO::PARAM_INT);

    if ($stmt->execute()) {
        // Redireciona para a página correta de acordo com o tipo de usuário
        if ($_SESSION['tipo'] == 2) {
            header("Location: home_gerente.php?mensagem=Usuário ativado com sucesso");
        } else if ($_SESSION['tipo'] == 3) {
            header("Location: home_admin.php?mensagem=Usuário ativado com sucesso");
        }
        exit;
    } else {
        echo "Erro ao ativar o usuário.";
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tornar Ativo</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            height: 100vh;
            background-color: #f8f9fa;
            margin: 0;
        }
        .container {
            background-color: #e7f3fe; /* Cor de fundo azul claro */
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 80%;
            max-width: 600px;
            border: 3px solid #007bff; /* Borda azul espessa */
        }
        h2 {
            color: #007bff; /* Cor do título */
        }
        .button-container {
            margin-top: 20px;
            display: flex;
            justify-content: space-between;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Tornar Ativo</h2>
        <p>Você tem certeza que deseja ativar o usuário <strong><?php echo htmlspecialchars($usuario['nome']); ?></strong>?</p>
        <p>Email: <?php echo htmlspecialchars($usuario['email']); ?></p>
        
        <div class="button-container">
            <form action="tornar_ativo.php?idUsuario=<?php echo urlencode($idUsuario); ?>" method="POST">
                <input type="hidden" name="idUsuario" value="<?php echo htmlspecialchars($idUsuario); ?>">
                <button type="submit" class="btn btn-success">Confirmar Ativação</button>
            </form>

            <!-- Botão de Cancelar redirecionando para a página correta -->
            <a href="<?php echo $_SESSION['tipo'] == 2 ? 'home_gerente.php' : 'home_admin.php'; ?>" class="btn btn-secondary">Cancelar</a>
        </div>
    </div>
</body>
</html>

