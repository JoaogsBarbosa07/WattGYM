
<?php
/**
 * login.php
 *
 * Tela e processamento do login (RF02).
 * Recebe e-mail e senha por POST e confere a senha com password_verify() (RNF01).
 * Se estiver certa, abre a sessão: administrador vai para dashboard.php (RN03)
 * e cliente vai para inicio.php. Quem já está logado é redirecionado.
 * Em caso de erro, mostra uma mensagem genérica, sem revelar se o e-mail existe.
 *
 * @author João Gabriel
 */
// session_start() sempre na primeira linha, antes de qualquer HTML
session_start();
require_once 'includes/config.php'; // padronize este caminho em todos os arquivos
 
// Se já estiver logado, não precisa ver o login de novo
if (isset($_SESSION['id_usuario'])) {
    header('Location: inicio.php');
    exit;
}
 
$erro = "";
 
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';
 
    if (empty($email) || empty($senha)) {
        $erro = "Preencha e-mail e senha.";
    } else {
        try {
            $stmt = $conn->prepare("SELECT * FROM usuarios WHERE email = :email");
            $stmt->execute([':email' => $email]);
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
 
            // Confere a senha com o hash salvo no banco
            if ($usuario && password_verify($senha, $usuario['senha'] ?? '')) {
                // Gera novo ID de sessão (evita session fixation)
                session_regenerate_id(true);
 
                $_SESSION['id_usuario']   = $usuario['id_usuario'];
                $_SESSION['nome_usuario'] = $usuario['nome'];
                $_SESSION['tipo_usuario'] = $usuario['tipo'] ?? 'cliente';
 
                // Admin vai para o dashboard; cliente vai para o início
                if ($_SESSION['tipo_usuario'] === 'admin') {
                    header('Location: dashboard.php');
                } else {
                    header('Location: inicio.php');
                }
                exit;
            } else {
                // Mensagem genérica: não revela se o e-mail existe
                $erro = "E-mail ou senha incorretos.";
            }
        } catch (PDOException $e) {
            error_log("Erro no login: " . $e->getMessage()); // detalhe vai para o log
            $erro = "Erro ao entrar. Tente novamente mais tarde.";
        }
    }
}
 
include 'header.php';
?>
 
<div class="login">
    <img src="assets/IMG/braco_esq_login.png" class="braco_esquerdo" alt="">
    <img src="assets/IMG/braco_dir_login.png" class="braco_direito" alt="">
 
    <form class="formulario_login" method="POST" action="login.php">
        <h2>Login</h2>
 
        <?php if (!empty($erro)): ?>
            <p class="alerta erro"><?= htmlspecialchars($erro) ?></p>
        <?php endif; ?>
 
        <label for="email">Email</label>
        <input type="email" id="email" name="email" placeholder="adonai@gmail.com"
               value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
 
        <label for="senha">Senha</label>
        <div class="senha-container">
            <input type="password" id="senha" name="senha" placeholder="********" required>
            <button type="button" id="mostrarSenha">
                <img id="iconeOlho" src="assets/IMG/olho-fechado.png" alt="Mostrar senha">
            </button>
        </div>
 
        <p class="sobre-link">
            Não tem conta?
            <a href="cadastro.php">Cadastre-se</a>
        </p>
 
        <button type="submit">Entrar</button>
    </form>
</div>
 
<?php include 'footer.php'; ?>
 