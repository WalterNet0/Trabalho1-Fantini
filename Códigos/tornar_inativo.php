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
    exit; // Saia do script em caso de erro de conexão
}

// Obtém o ID do usuário a ser tornado inativo
$idUsuario = $_GET['idUsuario'] ?? null; // Captura o ID da URL

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

// Verifica se o formulário foi enviado para confirmar a inativação
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Executa a atualização para tornar o usuário inativo
    $stmt = $conn->prepare("UPDATE Usuarios SET ativo = 0 WHERE idUsuario = :idUsuario");
    $stmt->bindParam(':idUsuario', $idUsuario, PDO::PARAM_INT);

    if ($stmt->execute()) {
        // Se o usuário está inativando a própria conta
        if ($_SESSION['idUsuario'] == $idUsuario) {
            // Redireciona para a página de login
            header("Location: login.php");
            exit;
        } else {

            if($_SESSION['tipo'] == 3){
                header("Location: home_admin.php");
            } else if($_SESSION['tipo'] == 2){
                header("Location: home_gerente.php");
            }
            exit;
        }
    } else {
        echo "Erro ao tornar o usuário inativo.";
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tornar Usuário Inativo</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            height: 100vh; /* Define a altura total da tela */
            background-color: #f8f9fa;
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
        }
        .button-container {
            margin-top: 20px;
            display: flex; /* Adiciona um layout flexível */
            justify-content: space-between; /* Distribui o espaço entre os botões */
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Tornar Usuário Inativo</h2>
        <p>Você tem certeza que deseja tornar o usuário <strong><?php echo htmlspecialchars($usuario['nome']); ?></strong> inativo?</p>
        <p>Email: <?php echo htmlspecialchars($usuario['email']); ?></p>
        
        <div class="button-container">
            <form action="tornar_inativo.php?idUsuario=<?php echo urlencode($idUsuario); ?>" method="POST">
                <input type="hidden" name="idUsuario" value="<?php echo htmlspecialchars($idUsuario); ?>">
                <button type="submit" class="btn btn-danger">Confirmar Inativação</button>
            </form>
            <a href="home_admin.php" class="btn btn-secondary">Cancelar</a>
        </div>
    </div>
</body>
</html>

