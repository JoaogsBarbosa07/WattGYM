<?php
/**
 * header.php
 *
 * Topo e menu de todas as páginas. Inicia a sessão (precisa ser antes de
 * qualquer HTML) e mostra os links certos: Login para visitante, Perfil e Sair
 * para quem entrou, e Dashboard e Usuários só para administrador.
 *
 * @author João Gabriel
 */
// Precisa ficar ANTES de qualquer HTML, senão dá "headers already sent"
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$logado = isset($_SESSION['id_usuario']);
$admin  = $logado && ($_SESSION['tipo_usuario'] ?? '') === 'admin';
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WattGYM</title>
    <link rel="icon" href="assets/IMG/watt_.jpg">
    <link rel="stylesheet" href="assets/CSS/style.css">
    <script src="assets/JS/script.js" defer></script>
</head>

<body>
    <header>
        <div class="logo">
            <img src="assets/IMG/watt_.jpg" alt="Logo WattGYM">
            <h3>Watt<span>GYM</span></h3>
        </div>

        <nav>
            <ul>
                <li><a href="inicio.php">Home</a></li>
                <li>
                    <a href="produtos.php">Produtos</a>
                    <ul>
                        <li><a href="produtos.php#suplementos">Suplementos</a></li>
                        <li><a href="produtos.php#acessorios">Acessórios</a></li>
                        <li><a href="produtos.php#creatinas">Creatinas</a></li>
                        <li><a href="produtos.php#pre-treinos">Pré-treinos</a></li>
                        <li><a href="produtos.php#vitaminas">Vitaminas</a></li>
                    </ul>
                </li>
                <li><a href="sobre.php">Sobre</a></li>
                <li><a href="contato.php">Contato</a></li>

                <?php if ($logado): ?>
                    <?php if ($admin): ?>
                        <li><a href="dashboard.php">Dashboard</a></li>
                        <li><a href="usuarios.php">Usuários</a></li>
                    <?php endif; ?>
                    <li><a href="perfil.php">Perfil</a></li>
                    <li><a href="logout.php">Sair</a></li>
                <?php else: ?>
                    <li><a href="login.php">Login</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>
