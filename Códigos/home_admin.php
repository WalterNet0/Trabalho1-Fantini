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

// Consulta todos os usuários em ordem decrescente por tipo
$stmt = $conn->query("SELECT * FROM Usuarios ORDER BY tipo DESC");
$usuarios = $stmt->fetchAll();

// Mapeamento de tipos de usuários
$tiposUsuario = [
    1 => 'Usuário',
    2 => 'Gerente',
    3 => 'Administrador'
];

$loggedUserId = $_SESSION['idUsuario']; // ID do usuário logado
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home do Administrador</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
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
        <!-- Exibir o nome do administrador -->
        <h2>Bem-vindo, <?php echo htmlspecialchars($_SESSION['nomeAdmin']); ?></h2>
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

                <?php if ($usuario['tipo'] == 3): // Se for administrador ?>
                    <div class="d-flex justify-content-between">
                        <?php if ($usuario['idUsuario'] == $loggedUserId): // Se for o próprio admin ?>
                            <?php if (!$usuario['ativo']): ?>
                                <a href="tornar_ativo.php?idUsuario=<?php echo $usuario['idUsuario']; ?>" class="btn btn-success">Ativar</a>
                            <?php else: ?>
                                <a href="tornar_inativo.php?idUsuario=<?php echo $usuario['idUsuario']; ?>" class="btn btn-secondary mr-2">Tornar Inativo</a>
                                <!-- Botões de edição e remoção disponíveis somente para o administrador logado -->
                                <a href="editar_usuario.php?idUsuario=<?php echo $usuario['idUsuario']; ?>" class="btn btn-warning mr-2">Editar</a>
                                <a href="remover_usuario.php?idUsuario=<?php echo $usuario['idUsuario']; ?>" class="btn btn-danger">Remover</a>
                            <?php endif; ?>
                        <?php else: // Se não for o próprio admin ?>
                            <?php if (!$usuario['ativo']): ?>
                                <a href="tornar_ativo.php?idUsuario=<?php echo $usuario['idUsuario']; ?>" class="btn btn-success">Ativar</a>
                            <?php else: ?>
                                <span class="text-muted">Opções não disponíveis.</span>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                <?php else: // Se não for administrador ?>
                    <div class="d-flex justify-content-between">
                        <?php if ($usuario['ativo']): ?>
                            <a href="tornar_inativo.php?idUsuario=<?php echo $usuario['idUsuario']; ?>" class="btn btn-secondary mr-2">Tornar Inativo</a>
                        <?php else: ?>
                            <a href="tornar_ativo.php?idUsuario=<?php echo $usuario['idUsuario']; ?>" class="btn btn-success mr-2">Tornar Ativo</a>
                        <?php endif; ?>
                        
                        <a href="editar_usuario.php?idUsuario=<?php echo $usuario['idUsuario']; ?>" class="btn btn-warning mr-2">Editar</a>
                        <a href="remover_usuario.php?idUsuario=<?php echo $usuario['idUsuario']; ?>" class="btn btn-danger">Remover</a>
                    </div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
        
        <!-- Links para Cadastro e Login dentro do container -->
        <div class="mt-3 d-flex justify-content-between">
            <a href="cadastro.php" class="btn btn-success">Cadastrar</a>
            <a href="login.php" class="btn btn-danger ml-auto">Sair</a>
        </div>
    </div>
</body>
</html>

