<?php
/**
 * cadastro.php
 *
 * Tela e processamento do cadastro de usuários (RF01).
 * Recebe nome, nascimento, e-mail e senha por POST, valida os campos (RN01, RN02)
 * e grava em usuarios, com a senha criptografada por password_hash() (RNF01).
 * O CPF é gravado como NULL: a coluna tem índice UNIQUE, e vários usuários
 * sem CPF não podem repetir texto vazio.
 * Depois do cadastro, a pessoa entra por login.php.
 *
 * @author João Gabriel
 */
session_start();
require_once 'includes/config.php'; // mesmo caminho usado no login.php
require_once 'includes/funcoes.php';

// Quem já está logado não precisa se cadastrar
if (isset($_SESSION['id_usuario'])) {
    header('Location: inicio.php');
    exit;
}

$mensagem = "";
$tipo_alerta = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome           = trim($_POST['nome'] ?? '');
    $nascimento     = $_POST['nascimento'] ?? '';
    $email          = trim($_POST['email'] ?? '');
    $senha          = $_POST['senha'] ?? '';
    $confirmarSenha = $_POST['confirmarSenha'] ?? '';

    // Validações
    if (empty($nome) || empty($nascimento) || empty($email) || empty($senha)) {
        $mensagem = "Preencha todos os campos obrigatórios!";
        $tipo_alerta = "erro";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $mensagem = "Digite um e-mail válido!";
        $tipo_alerta = "erro";
    } elseif (strlen($senha) < 6) {
        $mensagem = "A senha deve ter pelo menos 6 caracteres!";
        $tipo_alerta = "erro";
    } elseif ($senha !== $confirmarSenha) {
        $mensagem = "As senhas não coincidem!";
        $tipo_alerta = "erro";
    } elseif (strtotime($nascimento) === false || strtotime($nascimento) > time()) {
        $mensagem = "Data de nascimento inválida!";
        $tipo_alerta = "erro";
    } else {
        try {
            // Verifica se o e-mail já existe
            $stmtCheck = $conn->prepare("SELECT id_usuario FROM usuarios WHERE email = :email");
            $stmtCheck->execute([':email' => $email]);

            if ($stmtCheck->rowCount() > 0) {
                $mensagem = "Este e-mail já está cadastrado!";
                $tipo_alerta = "erro";
                registrar_auditoria($conn, null, 'CADASTRO', null, 'Rejeitado: e-mail já cadastrado');
            } else {
                // Nunca salve a senha pura: salva só o hash
                $hash = password_hash($senha, PASSWORD_DEFAULT);

                // O cadastro público cria sempre o perfil 'cliente'. O perfil nunca vem do formulário,
                // senão qualquer visitante poderia se cadastrar como admin.
                $sql = "INSERT INTO usuarios (nome, email, senha, cpf, telefone, data_de_nascimento, data_cadastro, id_perfil)
                        VALUES (:nome, :email, :senha, NULL, '', :nascimento, NOW(),
                                (SELECT id_perfil FROM perfis WHERE nome = 'cliente'))";
                $stmt = $conn->prepare($sql);
                $stmt->execute([
                    ':nome'       => $nome,
                    ':email'      => $email,
                    ':senha'      => $hash,
                    ':nascimento' => $nascimento
                ]);

                registrar_auditoria($conn, (int) $conn->lastInsertId(), 'CADASTRO', null, 'Sucesso');

                $mensagem = "Cadastro realizado com sucesso!";
                $tipo_alerta = "sucesso";
            }
        } catch (PDOException $e) {
            error_log("Erro no cadastro: " . $e->getMessage()); // detalhe só no log
            $mensagem = "Erro ao cadastrar. Tente novamente mais tarde.";
            $tipo_alerta = "erro";
        }
    }
}

include 'header.php';
?>

<div class="cadastro">
    <img src="assets/IMG/braco_esq.png" class="braco esquerda" alt="">
    <img src="assets/IMG/braco_dir.png" class="braco direita" alt="">

    <form method="POST" action="cadastro.php" id="formCadastro">
        <h2>Cadastro</h2>

        <?php if (!empty($mensagem)): ?>
            <p class="alerta <?= $tipo_alerta ?>">
                <?= htmlspecialchars($mensagem) ?>
                <?php if ($tipo_alerta === 'sucesso'): ?>  
                <?php endif; ?>
            </p>
        <?php endif; ?>

        <div class="input-grupo">
            <div class="grupo">
                <label for="nome">Nome</label>
                <input type="text" id="nome" name="nome" placeholder="Maromba da Silva"
                       value="<?= htmlspecialchars($_POST['nome'] ?? '') ?>" required>
            </div>
            <div class="grupo">
                <label for="nascimento">Data de nascimento</label>
                <input type="date" id="nascimento" name="nascimento"
                       value="<?= htmlspecialchars($_POST['nascimento'] ?? '') ?>" required>
            </div>
        </div>

        <label for="email">Email</label>
        <input type="email" id="email" name="email" placeholder="yahweh@gmail.com"
               value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>

        <label for="senha">Senha</label>
        <div class="cadastro-senha-container">
            <input type="password" id="senha" name="senha" placeholder="********" minlength="6" required>
            <button type="button" id="mostrarSenha">
                <img id="iconeOlho" src="assets/IMG/olho-fechado.png" alt="Mostrar senha">
            </button>
        </div>

        <label for="confirmarSenha">Confirmar senha:</label>
        <div class="cadastro-senha-container">
            <input type="password" id="confirmarSenha" name="confirmarSenha" placeholder="********" required>
            <button type="button" id="mostrarConfirmarSenha">
                <img id="iconeOlhoConfirmar" src="assets/IMG/olho-fechado.png" alt="Mostrar senha">
            </button>
        </div>

        <button type="submit">Cadastrar</button>

        <p class="sobre-link">
            Já tem conta?
            <a href="login.php">Login</a>
        </p>
    </form>
</div>

<?php include 'footer.php'; ?>
