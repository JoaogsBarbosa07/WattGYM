
<?php
/**
 * contato.php
 *
 * Página de contato (RF05). Recebe e-mail, assunto e mensagem por POST,
 * valida e grava em mensagens_contato. Não exige login.
 * Fica na mesma página e mostra a mensagem de sucesso ou de erro.
 *
 * @author João Gabriel
 */
session_start();
require_once 'includes/config.php';
 
$mensagem = "";
$tipo_alerta = "";
$email = $assunto = $texto = "";
 
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email   = trim($_POST['email'] ?? '');
    $assunto = trim($_POST['assunto'] ?? '');
    $texto   = trim($_POST['mensagem'] ?? '');
 
    if (empty($email) || empty($assunto) || empty($texto)) {
        $mensagem = "Preencha todos os campos.";
        $tipo_alerta = "erro";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $mensagem = "Digite um e-mail válido.";
        $tipo_alerta = "erro";
    } else {
        try {
            $stmt = $conn->prepare(
                "INSERT INTO mensagens_contato (email, assunto, mensagem)
                 VALUES (:email, :assunto, :mensagem)"
            );
            $stmt->execute([
                ':email'    => $email,
                ':assunto'  => $assunto,
                ':mensagem' => $texto
            ]);
 
            $mensagem = "Mensagem enviada! Responderemos em breve.";
            $tipo_alerta = "sucesso";
 
            // Limpa os campos depois de enviar
            $email = $assunto = $texto = "";
        } catch (PDOException $e) {
            error_log("Erro no contato: " . $e->getMessage());
            $mensagem = "Não foi possível enviar agora. Tente novamente mais tarde.";
            $tipo_alerta = "erro";
        }
    }
}
 
include 'header.php';
?>
 
<div class="alinha">
    <div class="info">
        <h3>Informações</h3>
        <h4>Email</h4>
        <p>RicardoSexy@gmail.com</p>
 
        <h4>Telefone</h4>
        <p>(81)1945-1500</p>
 
        <h4>Endereço</h4>
        <p>Rua do Embaço, 200</p>
    </div>
 
    <div class="contato">
        <form method="POST" action="contato.php">
            <h2>Contate-nos</h2>
 
            <?php if (!empty($mensagem)): ?>
                <p class="alerta <?= $tipo_alerta ?>"><?= htmlspecialchars($mensagem) ?></p>
            <?php endif; ?>
 
            <h5><label for="email">Email</label></h5>
            <input type="email" id="email" name="email" placeholder="Digite seu email"
                   maxlength="150" value="<?= htmlspecialchars($email) ?>" required>
 
            <h5><label for="assunto">Assunto</label></h5>
            <input type="text" id="assunto" name="assunto" placeholder="Digite o assunto"
                   maxlength="150" value="<?= htmlspecialchars($assunto) ?>" required>
 
            <h5><label for="mensagem">Mensagem</label></h5>
            <textarea id="mensagem" name="mensagem" rows="5" placeholder="Digite sua mensagem"
                      maxlength="2000" required><?= htmlspecialchars($texto) ?></textarea>
 
            <button type="submit">Enviar</button>
 
            <p class="sobre-link">
                Quer Saber Mais?
                <a href="sobre.php">Sobre</a>
            </p>
        </form>
    </div>
</div>
 
<?php include 'footer.php'; ?>
 