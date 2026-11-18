
<?php
/**
 * perfil.php
 
 * Tela e processamento da edição de perfil (RF04). Exige usuário logado.
 * Recebe dados pessoais e endereço por POST, valida e atualiza as tabelas
 * usuarios e enderecos dentro de uma transação: ou grava tudo, ou nada.
 * O CPF e o CEP são guardados só com números; a máscara existe só na tela.
 * Edita apenas o primeiro endereço do usuário.
 
 * @author João Gabriel
 */
session_start();
 
// se tiver sem login, vai pra o login
if (!isset($_SESSION['id_usuario'])) {
    header('Location: login.php');
    exit;
}
 
require_once 'includes/config.php';
 
$id_usuario = (int) $_SESSION['id_usuario'];
$mensagem = "";
$tipo_alerta = "";
 
/**
 * Remove tudo que não for número.
 *
 * Usada no CPF e no CEP: o banco guarda só os dígitos (CHAR(11) e CHAR(8)).
 *
 * @param string $valor Texto digitado, como "123.456.789-01".
 * @return string Só os números, como "12345678901".
 */
function somenteDigitos(string $valor): string {
    return preg_replace('/\D/', '', $valor);
}
 
/**
 * Coloca a máscara do CPF para mostrar na tela.
 *
 * @param string $d CPF só com números.
 * @return string CPF no formato 000.000.000-00. Se não tiver 11 dígitos,
 *                devolve o valor sem mudar (por exemplo, vazio).
 */
function formatarCpf(string $d): string {
    return strlen($d) === 11
        ? substr($d, 0, 3) . '.' . substr($d, 3, 3) . '.' . substr($d, 6, 3) . '-' . substr($d, 9, 2)
        : $d;
}
 
/**
 * Coloca a máscara do CEP para mostrar na tela.
 *
 * @param string $d CEP só com números.
 * @return string CEP no formato 00000-000. Se não tiver 8 dígitos,
 *                devolve o valor sem mudar.
 */
function formatarCep(string $d): string {
    return strlen($d) === 8 ? substr($d, 0, 5) . '-' . substr($d, 5) : $d;
}
 
/**
 * Devolve o valor de um campo pronto para ir dentro do HTML.
 *
 * Aplica htmlspecialchars() para o texto digitado nunca virar código na página (RNF05).
 *
 * @param array  $dados Dados do usuário e do endereço.
 * @param string $campo Nome do campo, como "nome" ou "cep".
 * @return string Valor protegido, ou texto vazio se o campo não existir.
 */
function v(array $dados, string $campo): string {
    return htmlspecialchars($dados[$campo] ?? '');
}
 
/**
 * Busca o usuário e o primeiro endereço dele.
 
 * Junta as linhas de usuarios e enderecos num só array e já deixa o CPF
 * e o CEP com máscara, prontos para os campos do formulário.
 
 * @param PDO $conn Conexão aberta com o banco.
 * @param int $id   Id do usuário logado.
 * @return array Dados do usuário e do endereço. Array vazio se não achar.
 * @throws PDOException Se a consulta falhar.
 */
function buscarDados(PDO $conn, int $id): array {
    $stmt = $conn->prepare("SELECT * FROM usuarios WHERE id_usuario = :id");
    $stmt->execute([':id' => $id]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
 
    $stmt = $conn->prepare("SELECT * FROM enderecos WHERE id_usuario = :id ORDER BY id_endereco LIMIT 1");
    $stmt->execute([':id' => $id]);
    $endereco = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
 
    $dados = array_merge($usuario, $endereco);
    $dados['cpf'] = formatarCpf($dados['cpf'] ?? '');
    $dados['cep'] = formatarCep($dados['cep'] ?? '');
    return $dados;
}
 
try {
    $dados = buscarDados($conn, $id_usuario);
} catch (PDOException $e) {
    error_log("Erro ao buscar perfil: " . $e->getMessage());
    $dados = [];
}
 
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $enviado = [
        'nome'        => trim($_POST['nome'] ?? ''),
        'email'       => trim($_POST['email'] ?? ''),
        'cpf'         => somenteDigitos($_POST['cpf'] ?? ''),
        'telefone'    => trim($_POST['telefone'] ?? ''),
        'cep'         => somenteDigitos($_POST['cep'] ?? ''),
        'rua'         => trim($_POST['rua'] ?? ''),
        'numero'      => trim($_POST['numero'] ?? ''),
        'complemento' => trim($_POST['complemento'] ?? ''),
        'bairro'      => trim($_POST['bairro'] ?? ''),
        'cidade'      => trim($_POST['cidade'] ?? ''),
        'uf'          => strtoupper(trim($_POST['uf'] ?? '')),
    ];
 
    // Se qualquer campo do endereço foi preenchido, os principais passam a ser obrigatórios
    $temEndereco = $enviado['cep'] !== '' || $enviado['rua'] !== '' || $enviado['numero'] !== ''
                || $enviado['bairro'] !== '' || $enviado['cidade'] !== '' || $enviado['uf'] !== '';
 
    if (empty($enviado['nome']) || empty($enviado['email'])) {
        $mensagem = "Preencha nome e e-mail.";
    } elseif (!filter_var($enviado['email'], FILTER_VALIDATE_EMAIL)) {
        $mensagem = "Digite um e-mail válido.";
    } elseif ($enviado['cpf'] !== '' && strlen($enviado['cpf']) !== 11) {
        $mensagem = "O CPF deve ter 11 números.";
    } elseif (strlen($enviado['telefone']) > 15) {
        $mensagem = "Telefone inválido.";
    } elseif ($temEndereco && (
        strlen($enviado['cep']) !== 8 || $enviado['rua'] === '' || $enviado['numero'] === ''
        || $enviado['bairro'] === '' || $enviado['cidade'] === '' || !preg_match('/^[A-Z]{2}$/', $enviado['uf'])
    )) {
        $mensagem = "Para salvar o endereço, preencha CEP (8 números), rua, número, bairro, cidade e UF (2 letras).";
    }
 
    if ($mensagem !== "") {
        $tipo_alerta = "erro";
    } else {
        try {
            // O email não pode ser de outro usuário
            $check = $conn->prepare("SELECT id_usuario FROM usuarios WHERE email = :email AND id_usuario != :id");
            $check->execute([':email' => $enviado['email'], ':id' => $id_usuario]);
 
            if ($check->rowCount() > 0) {
                $mensagem = "Este e-mail já está em uso por outra conta.";
                $tipo_alerta = "erro";
            } else {
                // Transação, salva tudo ou nada
                $conn->beginTransaction();
 
                $stmt = $conn->prepare(
                    "UPDATE usuarios
                     SET nome = :nome, email = :email, cpf = :cpf, telefone = :telefone
                     WHERE id_usuario = :id"
                );
                $stmt->execute([
                    ':nome'     => $enviado['nome'],
                    ':email'    => $enviado['email'],
                    ':cpf'      => $enviado['cpf'] !== '' ? $enviado['cpf'] : null, 
                    ':telefone' => $enviado['telefone'],
                    ':id'       => $id_usuario
                ]);
 
                if ($temEndereco) {
                    $campos = [
                        ':cep'         => $enviado['cep'],
                        ':rua'         => $enviado['rua'],
                        ':numero'      => $enviado['numero'],
                        ':complemento' => $enviado['complemento'] !== '' ? $enviado['complemento'] : null,
                        ':bairro'      => $enviado['bairro'],
                        ':cidade'      => $enviado['cidade'],
                        ':uf'          => $enviado['uf'],
                    ];
 
                    if (!empty($dados['id_endereco'])) {
                        // Atualiza o endereço 
                        $stmt = $conn->prepare(
                            "UPDATE enderecos
                             SET cep = :cep, rua = :rua, numero = :numero, complemento = :complemento,
                                 bairro = :bairro, cidade = :cidade, uf = :uf
                             WHERE id_endereco = :id_endereco AND id_usuario = :id_usuario"
                        );
                        $campos[':id_endereco'] = $dados['id_endereco'];
                    } else {
                        // Primeiro endereço do usuário
                        $stmt = $conn->prepare(
                            "INSERT INTO enderecos (id_usuario, cep, rua, numero, complemento, bairro, cidade, uf)
                             VALUES (:id_usuario, :cep, :rua, :numero, :complemento, :bairro, :cidade, :uf)"
                        );
                    }
                    $campos[':id_usuario'] = $id_usuario;
                    $stmt->execute($campos);
                }
 
                $conn->commit();
 
                $_SESSION['nome_usuario'] = $enviado['nome'];
 
                $mensagem = "Alterações salvas com sucesso!";
                $tipo_alerta = "sucesso";
                $dados = buscarDados($conn, $id_usuario);
            }
        } catch (PDOException $e) {
            if ($conn->inTransaction()) {
                $conn->rollBack();
            }
            error_log("Erro ao atualizar perfil: " . $e->getMessage());
            $mensagem = "Erro ao salvar. Tente novamente mais tarde.";
            $tipo_alerta = "erro";
        }
    }
 
    // Em caso de erro, vai manter oq a pessoa digitou
    if ($tipo_alerta === 'erro') {
        $enviado['cpf'] = formatarCpf($enviado['cpf']);
        $enviado['cep'] = formatarCep($enviado['cep']);
        $dados = array_merge($dados, $enviado);
    }
}
 
include 'header.php';
?>
 
<div class="container_perfil">
    <h2>Editar Perfil</h2>
 
    <?php if (!empty($mensagem)): ?>
        <p class="alerta <?= $tipo_alerta ?>"><?= htmlspecialchars($mensagem) ?></p>
    <?php endif; ?>
 
    <form method="POST" action="perfil.php" id="formPerfil">
 
        <h3>Dados Pessoais</h3>
        <div class="form-grupo">
            <label for="nome">Nome Completo</label>
            <input type="text" id="nome" name="nome" placeholder="Maromba da Silva"
                   value="<?= v($dados, 'nome') ?>" required>
 
            <label for="email">E-mail</label>
            <input type="email" id="email" name="email" placeholder="yeshua@email.com"
                   value="<?= v($dados, 'email') ?>" required>
 
            <label for="cpf">CPF</label>
            <input type="text" id="cpf" name="cpf" placeholder="000.000.000-00" maxlength="14"
                   value="<?= v($dados, 'cpf') ?>">
 
            <label for="telefone">Telefone / WhatsApp</label>
            <input type="tel" id="telefone" name="telefone" placeholder="(00) 00000-0000" maxlength="15"
                   value="<?= v($dados, 'telefone') ?>">
        </div>
 
        <h3>Endereço</h3>
        <div class="form-grupo">
            <label for="cep">CEP</label>
            <input type="text" id="cep" name="cep" placeholder="00000-000" maxlength="9"
                   value="<?= v($dados, 'cep') ?>">
 
            <label for="rua">Rua</label>
            <input type="text" id="rua" name="rua" placeholder="Rua do Jasmim" maxlength="100"
                   value="<?= v($dados, 'rua') ?>">
 
            <label for="numero">Número</label>
            <input type="text" id="numero" name="numero" placeholder="123" maxlength="10"
                   value="<?= v($dados, 'numero') ?>">
 
            <label for="complemento">Complemento</label>
            <input type="text" id="complemento" name="complemento" placeholder="Apto 101 (opcional)" maxlength="50"
                   value="<?= v($dados, 'complemento') ?>">
 
            <label for="bairro">Bairro</label>
            <input type="text" id="bairro" name="bairro" placeholder="Centro" maxlength="50"
                   value="<?= v($dados, 'bairro') ?>">
 
            <label for="cidade">Cidade</label>
            <input type="text" id="cidade" name="cidade" placeholder="Sua cidade" maxlength="50"
                   value="<?= v($dados, 'cidade') ?>">
 
            <label for="uf">UF</label>
            <input type="text" id="uf" name="uf" placeholder="PE" maxlength="2"
                   value="<?= v($dados, 'uf') ?>" style="text-transform: uppercase;">
        </div>
 
        <button type="submit" class="btn-salvar">Salvar Alterações</button>
    </form>
</div>
 
<?php include 'footer.php'; ?>
 