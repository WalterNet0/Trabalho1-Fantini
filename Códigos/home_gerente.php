<?php
session_start();

if (!isset($_SESSION['idUsuario']) || $_SESSION['tipo'] != 2) {
    // Se a sessão não existir ou não for um gerente, redireciona para a página de login
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

// Consulta todos os usuários em ordem decrescente de acordo com o cargo
$stmt = $conn->query("
    SELECT * FROM Usuarios
    ORDER BY 
        CASE tipo
            WHEN 3 THEN 1  -- Administrador
            WHEN 2 THEN 2  -- Gerente
            WHEN 1 THEN 3  -- Usuário
        END
");
$usuarios = $stmt->fetchAll();

// Mapeamento de tipos de usuários
$tiposUsuario = [
    1 => 'Usuário',
    2 => 'Gerente',
    3 => 'Administrador'
];
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home do Gerente</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: flex-start;
            flex-direction: column;
            background-color: #f8f9fa;
            padding: 20px;
        }
        .container {
            background-color: #e7f3fe; /* Cor de fundo azul claro */
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 80%; /* Largura ajustável */
            border: 3px solid #007bff; /* Adiciona a borda azul espessa */
        }
        .user-card {
            border: 1px solid #007bff; /* Bordas azul */
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 10px;
        }
        h2 {
            color: #007bff; /* Cor do título */
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Exibir o nome do gerente -->
        <h2>Bem-vindo, <?php echo htmlspecialchars($_SESSION['nome']); ?></h2>
        <h3>Lista de Usuários</h3>

        <?php foreach ($usuarios as $usuario): ?>
            <div class="user-card">
                <h5><?php echo htmlspecialchars($usuario['nome']); ?></h5>
                <p>Email: <?php echo htmlspecialchars($usuario['email']); ?></p>
                <p>Tipo: <?php echo htmlspecialchars($tiposUsuario[$usuario['tipo']]); ?></p>

                <p>Status: 
                    <?php if ($usuario['ativo']): ?>
                        <span class="text-success">Ativo</span>
                    <?php else: ?>
                        <span class="text-danger">Inativo</span>
                    <?php endif; ?>
                </p>

                <div class="d-flex justify-content-between">
                    <?php if ($usuario['tipo'] == 1): // Usuário comum ?>
                        <?php if ($usuario['ativo']): ?>
                            <a href="tornar_inativo.php?idUsuario=<?php echo $usuario['idUsuario']; ?>" class="btn btn-secondary mr-2">Tornar Inativo</a>
                        <?php else: ?>
                            <a href="tornar_ativo.php?idUsuario=<?php echo $usuario['idUsuario']; ?>" class="btn btn-success mr-2">Tornar Ativo</a>
                        <?php endif; ?>
                    
                    <?php elseif ($usuario['tipo'] == 2 && $usuario['idUsuario'] == $_SESSION['idUsuario']): // O próprio gerente ?>
                        <a href="tornar_inativo.php?idUsuario=<?php echo $usuario['idUsuario']; ?>" class="btn btn-secondary">Tornar Inativo</a>
                        <a href="editar_usuario.php?idUsuario=<?php echo $usuario['idUsuario']; ?>" class="btn btn-warning mr-2">Editar</a>
                        <a href="remover_usuario.php?idUsuario=<?php echo $usuario['idUsuario']; ?>" class="btn btn-danger mr-2">Remover</a>
                    <?php elseif ($usuario['tipo'] == 3 || $usuario['idUsuario'] != $_SESSION['idUsuario']): // Administrador, não exibe botões ?>
                        <!-- Nenhum botão será exibido para administrador -->
                        <span class="text-muted">Opções não disponíveis.</span>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>

        <!-- Links para Cadastro e Logout -->
        <div class="mt-3 d-flex justify-content-between">
            <a href="cadastro.php" class="btn btn-success">Cadastrar</a>
            <a href="login.php" class="btn btn-danger">Sair</a>
        </div>
    </div>
</body>
</html>

