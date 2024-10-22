<?php
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['idUsuario'])) {
    // Se a sessão não existir, redireciona para a página de login
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home do Usuário</title>
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
            background-color: #e7f3fe; /* Cor de fundo da "caixinha" */
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
    <div class="container">
        <h2>Bem-vindo!</h2>
        <p>Login realizado com sucesso!</p>
        <p>Nome: <?php echo isset($_SESSION['nome']) ? htmlspecialchars($_SESSION['nome']) : 'Desconhecido'; ?></p>
        <p>Email: <?php echo isset($_SESSION['email']) ? htmlspecialchars($_SESSION['email']) : 'Desconhecido'; ?></p>
        
        <?php
        // Mapeamento de tipos de usuários
        $tiposUsuario = [
            1 => 'Usuário',
            2 => 'Gerente',
            3 => 'Administrador'
        ];
        ?>

        <p>Tipo: <?php echo isset($_SESSION['tipo']) && array_key_exists($_SESSION['tipo'], $tiposUsuario) ? $tiposUsuario[$_SESSION['tipo']] : 'Desconhecido'; ?></p>
    
        <!-- Links para Mudar Senha e Cadastro -->
        <div class="mt-3">
            <div class="btn-group-vertical">
                <a href="editar_usuario.php?idUsuario=<?php echo $_SESSION['idUsuario']; ?>" class="btn btn-warning mb-2">Editar</a>
                <a href="cadastro.php" class="btn btn-success mb-2">Cadastro</a>
                <a href="login.php" class="btn btn-danger">Sair</a>
            </div>
        </div>
    </div>
</body>
</html>

