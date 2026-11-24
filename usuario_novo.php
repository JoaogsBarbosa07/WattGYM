<?php
/**
 * usuario_novo.php
 *
 * Cadastro de usuário pelo administrador (Sprint 01). Só para administradores ativos.
 *
 * - Os selects de perfil e setor (loja) vêm de SELECT real nas tabelas perfis e
 *   lojas, só com registros ativos. Se uma das tabelas estiver vazia, o
 *   formulário não é mostrado e a página explica o motivo.
 * - Valida em dois lugares: o navegador (required, minlength, type=email, min/max da
 *   data) avisa antes de enviar, e o servidor valida tudo de novo (campos, formatos,
 *   e-mail duplicado e se os ids de perfil e setor existem e estão ativos), porque
 *   dados enviados à mão não passam pelo navegador.
 * - Mostra um erro embaixo de cada campo e mantém os valores digitados (menos a senha).
 * - Grava com INSERT preparado, senha com password_hash(), e registra na auditoria.
 * - Depois de salvar, volta para usuarios.php já filtrando o usuário novo.
 *
 * @author João Gabriel
 */
session_start();
require_once 'includes/config.php';
require_once 'includes/funcoes.php';
 
$idAdmin = exigir_admin($conn);
 
$erros   = [];
$valores = ['nome' => '', 'email' => '', 'nascimento' => '', 'id_perfil' => '', 'id_loja' => ''];
$perfis  = [];
$setores = [];
 
// Opções dos selects: sempre do banco, só registros ativos
try {
    $perfis  = $conn->query("SELECT id_perfil, nome FROM perfis WHERE ativo = 1 ORDER BY nome")->fetchAll(PDO::FETCH_ASSOC);
    $setores = $conn->query("SELECT id_loja AS id, nome_fantasia AS nome FROM lojas WHERE ativo = 1 ORDER BY nome_fantasia")->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Erro ao carregar perfis e setores: " . $e->getMessage());
    $erros['geral'] = 'Não foi possível carregar as opções do formulário. Tente novamente mais tarde.';
}
 
$idsPerfis  = array_map('intval', array_column($perfis, 'id_perfil'));
$idsSetores = array_map('intval', array_column($setores, 'id'));
$formularioLiberado = !empty($perfis) && !empty($setores) && !isset($erros['geral']);
 
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $formularioLiberado) {
    $valores['nome']       = trim($_POST['nome'] ?? '');
    $valores['email']      = trim($_POST['email'] ?? '');
    $valores['nascimento'] = trim($_POST['nascimento'] ?? '');
    $valores['id_perfil']  = trim($_POST['id_perfil'] ?? '');
    $valores['id_loja']    = trim($_POST['id_loja'] ?? '');
    $senha                 = $_POST['senha'] ?? '';
 
    if (!csrf_valido($_POST['csrf_token'] ?? null)) {
        $erros['geral'] = 'A página expirou. Recarregue e tente de novo.';
    } else {
        // Nome
        if ($valores['nome'] === '') {
            $erros['nome'] = 'Informe o nome.';
        } elseif (mb_strlen($valores['nome']) < 2 || mb_strlen($valores['nome']) > 100) {
            $erros['nome'] = 'O nome deve ter entre 2 e 100 caracteres.';
        }
 
        // E-mail
        if ($valores['email'] === '') {
            $erros['email'] = 'Informe o e-mail.';
        } elseif (strlen($valores['email']) > 255 || !filter_var($valores['email'], FILTER_VALIDATE_EMAIL)) {
            $erros['email'] = 'Digite um e-mail válido.';
        }
 
        // Data de nascimento
        $data = DateTime::createFromFormat('!Y-m-d', $valores['nascimento']);
        if ($valores['nascimento'] === '') {
            $erros['nascimento'] = 'Informe a data de nascimento.';
        } elseif (!$data || $data->format('Y-m-d') !== $valores['nascimento']
                  || $data > new DateTime('today') || $data < new DateTime('1900-01-01')) {
            $erros['nascimento'] = 'Data de nascimento inválida.';
        }
 
        // Senha inicial (bcrypt só usa os 72 primeiros bytes)
        if ($senha === '') {
            $erros['senha'] = 'Informe a senha inicial.';
        } elseif (strlen($senha) < 6) {
            $erros['senha'] = 'A senha deve ter pelo menos 6 caracteres.';
        } elseif (strlen($senha) > 72) {
            $erros['senha'] = 'A senha pode ter no máximo 72 caracteres.';
        }
 
        // Perfil e setor: precisam ser números que existem e estão ativos (T04)
        if ($valores['id_perfil'] === '') {
            $erros['id_perfil'] = 'Escolha o perfil.';
        } elseif (!ctype_digit($valores['id_perfil']) || !in_array((int) $valores['id_perfil'], $idsPerfis, true)) {
            $erros['id_perfil'] = 'Perfil inválido.';
        }
 
        if ($valores['id_loja'] === '') {
            $erros['id_loja'] = 'Escolha o setor.';
        } elseif (!ctype_digit($valores['id_loja']) || !in_array((int) $valores['id_loja'], $idsSetores, true)) {
            $erros['id_loja'] = 'Setor inválido.';
        }
 
        // Só vai ao banco se não houve nenhum erro
        if (empty($erros)) {
            try {
                $check = $conn->prepare("SELECT id_usuario FROM usuarios WHERE email = :email");
                $check->execute([':email' => $valores['email']]);
 
                if ($check->fetch()) {
                    $erros['email'] = 'Este e-mail já está cadastrado.';
                    registrar_auditoria($conn, null, 'CADASTRO', $idAdmin, 'Rejeitado: e-mail já cadastrado');
                } else {
                    $stmt = $conn->prepare(
                        "INSERT INTO usuarios (nome, email, senha, cpf, telefone, data_de_nascimento, data_cadastro, id_perfil, id_loja)
                         VALUES (:nome, :email, :senha, NULL, '', :nascimento, NOW(), :perfil, :loja)"
                    );
                    $stmt->execute([
                        ':nome'       => $valores['nome'],
                        ':email'      => $valores['email'],
                        ':senha'      => password_hash($senha, PASSWORD_DEFAULT),
                        ':nascimento' => $valores['nascimento'],
                        ':perfil'     => (int) $valores['id_perfil'],
                        ':loja'       => (int) $valores['id_loja'],
                    ]);
 
                    registrar_auditoria($conn, (int) $conn->lastInsertId(), 'CADASTRO', $idAdmin, 'Sucesso');
                    guardar_aviso('sucesso', 'Usuário cadastrado com sucesso.');
                    header('Location: usuarios.php?' . http_build_query(['q' => $valores['email']]));
                    exit;
                }
            } catch (PDOException $e) {
                // 23000 = violação de integridade (e-mail repetido numa corrida, ou FK inválida)
                error_log("Erro ao cadastrar usuário: " . $e->getMessage());
                if ($e->getCode() === '23000') {
                    registrar_auditoria($conn, null, 'CADASTRO', $idAdmin, 'Rejeitado: violação de integridade');
                    $erros['geral'] = 'Não foi possível salvar: o e-mail já existe ou o perfil/setor não é válido.';
                } else {
                    $erros['geral'] = 'Erro ao salvar. Tente novamente mais tarde.';
                }
            }
        }
    }
}
 
/** Mostra a mensagem de erro de um campo, se houver. */
function erro_campo(array $erros, string $campo): string
{
    return isset($erros[$campo]) ? '<small class="erro-campo">' . e($erros[$campo]) . '</small>' : '';
}
 
include 'header.php';
?>
 
<div class="container_perfil">
    <h2>Novo usuário</h2>
 
    <?php if (isset($erros['geral'])): ?>
        <p class="alerta erro"><?= e($erros['geral']) ?></p>
    <?php endif; ?>
 
    <?php if (!$formularioLiberado && !isset($erros['geral'])): ?>
        <p class="alerta erro">
            Não há perfil ou setor ativo cadastrado. Cadastre pelo menos um de cada
            antes de criar usuários.
        </p>
        <p><a href="usuarios.php">Voltar para a lista</a></p>
    <?php elseif ($formularioLiberado): ?>
        <form method="POST" action="usuario_novo.php">
            <?= csrf_campo() ?>
 
            <div class="form-grupo">
                <label for="nome">Nome</label>
                <input type="text" id="nome" name="nome" maxlength="100" required
                       value="<?= e($valores['nome']) ?>">
                <?= erro_campo($erros, 'nome') ?>
 
                <label for="email">E-mail</label>
                <input type="email" id="email" name="email" maxlength="255" required
                       value="<?= e($valores['email']) ?>">
                <?= erro_campo($erros, 'email') ?>
 
                <label for="nascimento">Data de nascimento</label>
                <input type="date" id="nascimento" name="nascimento" required
                       min="1900-01-01" max="<?= date('Y-m-d') ?>"
                       value="<?= e($valores['nascimento']) ?>">
                <?= erro_campo($erros, 'nascimento') ?>
 
                <label for="senha">Senha inicial</label>
                <input type="password" id="senha" name="senha" minlength="6" maxlength="72" required placeholder="********">
                <?= erro_campo($erros, 'senha') ?>
 
                <label for="id_perfil">Perfil</label>
                <select id="id_perfil" name="id_perfil" required>
                    <option value="">Selecione...</option>
                    <?php foreach ($perfis as $p): ?>
                        <option value="<?= (int) $p['id_perfil'] ?>"
                            <?= (string) $p['id_perfil'] === $valores['id_perfil'] ? 'selected' : '' ?>>
                            <?= e($p['nome']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <?= erro_campo($erros, 'id_perfil') ?>
 
                <label for="id_loja">Setor</label>
                <select id="id_loja" name="id_loja" required>
                    <option value="">Selecione...</option>
                    <?php foreach ($setores as $s): ?>
                        <option value="<?= (int) $s['id'] ?>"
                            <?= (string) $s['id'] === $valores['id_loja'] ? 'selected' : '' ?>>
                            <?= e($s['nome']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <?= erro_campo($erros, 'id_loja') ?>
            </div>
 
            <button type="submit" class="btn-salvar">Cadastrar usuário</button>
            <p class="sobre-link"><a href="usuarios.php">Voltar para a lista</a></p>
        </form>
    <?php endif; ?>
</div>
 
<?php include 'footer.php'; ?>
 