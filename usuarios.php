<?php
/**
 * usuarios.php
 *
 * Gestão de usuários (Sprint 01). Só para administradores ativos.
 *
 * - Lista os usuários Ativos com SELECT real e JOIN em perfis e lojas (setor).
 * - Busca por nome ou e-mail pelo parâmetro GET "q" (consulta preparada).
 * - O link "Excluir" chama esta mesma página com ?excluir=ID, que mostra a
 *   caixa "Deseja realmente excluir este usuário?" com Cancelar e Confirmar.
 *   Cancelar só volta à lista, sem enviar nada. Confirmar faz POST para
 *   usuario_excluir.php, com token CSRF.
 * - Usuários em Baixa não aparecem aqui.
 *
 * @author João Gabriel
 */
session_start();
require_once 'includes/config.php';
require_once 'includes/funcoes.php';

$idAdmin = exigir_admin($conn);

$busca       = mb_substr(trim($_GET['q'] ?? ''), 0, 100);
$idConfirmar = (int) ($_GET['excluir'] ?? 0);
$aviso       = pegar_aviso();
$usuarios    = [];
$paraExcluir = null;

// JOIN: cada usuário ganha o nome do perfil e do setor (loja) a partir das chaves estrangeiras.
//  - JOIN perfis: u.id_perfil = p.id_perfil (todo usuário tem perfil, então é JOIN simples)
//  - LEFT JOIN lojas: u.id_loja = l.id_loja (o setor é opcional: quem se cadastra
//    pelo site não tem setor, e não pode sumir da lista por isso)
$sqlBase = "SELECT u.id_usuario, u.nome, u.email, u.data_cadastro,
                   p.nome AS perfil, l.nome_fantasia AS setor
            FROM usuarios u
            JOIN perfis p ON p.id_perfil = u.id_perfil
            LEFT JOIN lojas l ON l.id_loja = u.id_loja
            WHERE u.status = 'Ativo'";

try {
    $sql = $sqlBase;
    $params = [];

    if ($busca !== '') {
        // Os curingas % e _ digitados pela pessoa viram texto comum
        $termo = '%' . addcslashes($busca, '%_\\') . '%';
        $sql .= " AND (u.nome LIKE :q1 OR u.email LIKE :q2)";
        $params = [':q1' => $termo, ':q2' => $termo];
    }

    $stmt = $conn->prepare($sql . " ORDER BY u.nome LIMIT 200");
    $stmt->execute($params);
    $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Pedido de exclusão: busca o usuário escolhido para mostrar na caixa de confirmação
    if ($idConfirmar > 0) {
        if ($idConfirmar === $idAdmin) {
            $aviso = ['tipo' => 'erro', 'mensagem' => 'Você não pode excluir o seu próprio usuário.'];
        } else {
            $stmt = $conn->prepare($sqlBase . " AND u.id_usuario = :id");
            $stmt->execute([':id' => $idConfirmar]);
            $paraExcluir = $stmt->fetch(PDO::FETCH_ASSOC) ?: null;

            if ($paraExcluir === null) {
                $aviso = ['tipo' => 'erro', 'mensagem' => 'Usuário não encontrado ou já excluído.'];
            }
        }
    }
} catch (PDOException $e) {
    error_log("Erro na listagem de usuários: " . $e->getMessage());
    $aviso = ['tipo' => 'erro', 'mensagem' => 'Não foi possível consultar os usuários agora. Tente novamente mais tarde.'];
}

// Link de volta para a lista, mantendo a busca
$voltar = 'usuarios.php' . ($busca !== '' ? '?' . http_build_query(['q' => $busca]) : '');

include 'header.php';
?>

<div class="gestao-usuarios">
    <h2>Usuários</h2>

    <?php if ($aviso): ?>
        <p class="alerta <?= e($aviso['tipo']) ?>"><?= e($aviso['mensagem']) ?></p>
    <?php endif; ?>

    <div class="barra-acoes">
        <form method="GET" action="usuarios.php" class="form-busca">
            <label for="q" class="so-leitor">Buscar por nome ou e-mail</label>
            <input type="search" id="q" name="q" placeholder="Buscar por nome ou e-mail"
                   maxlength="100" value="<?= e($busca) ?>">
            <button type="submit">Buscar</button>
            <?php if ($busca !== ''): ?>
                <a href="usuarios.php" class="link-limpar">Limpar</a>
            <?php endif; ?>
        </form>
        <a href="usuario_novo.php" class="btn-novo">Novo usuário</a>
    </div>

    <p class="contagem">
        <?= count($usuarios) ?> usuário(s) ativo(s)<?= $busca !== '' ? ' para "' . e($busca) . '"' : '' ?>
    </p>

    <table>
        <thead>
            <tr>
                <th>id</th>
                <th>nome</th>
                <th>e-mail</th>
                <th>perfil</th>
                <th>setor</th>
                <th>cadastro</th>
                <th>ações</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($usuarios)): ?>
                <tr>
                    <td colspan="7">
                        <?= $busca !== '' ? 'Nenhum usuário encontrado para essa busca.' : 'Nenhum usuário ativo cadastrado.' ?>
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($usuarios as $u): ?>
                    <tr>
                        <td>#<?= (int) $u['id_usuario'] ?></td>
                        <td><?= e($u['nome']) ?></td>
                        <td><?= e($u['email']) ?></td>
                        <td><?= e($u['perfil']) ?></td>
                        <td><?= $u['setor'] !== null ? e($u['setor']) : '—' ?></td>
                        <td><?= $u['data_cadastro'] ? e(date('d/m/Y', strtotime($u['data_cadastro']))) : '—' ?></td>
                        <td>
                            <?php if ((int) $u['id_usuario'] === $idAdmin): ?>
                                (você)
                            <?php else: ?>
                                <a class="link-excluir"
                                   href="usuarios.php?<?= e(http_build_query(['excluir' => $u['id_usuario'], 'q' => $busca])) ?>">Excluir</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php if ($paraExcluir): ?>
    <!-- Caixa de confirmação: só existe para o usuário escolhido na URL -->
    <div class="modal-fundo">
        <div class="modal" role="alertdialog" aria-modal="true"
             aria-labelledby="modal-titulo" aria-describedby="modal-texto">
            <h3 id="modal-titulo">Deseja realmente excluir este usuário?</h3>
            <p id="modal-texto">
                <strong><?= e($paraExcluir['nome']) ?></strong><br>
                <?= e($paraExcluir['email']) ?> · <?= e($paraExcluir['perfil']) ?> · ID #<?= (int) $paraExcluir['id_usuario'] ?>
            </p>
            <p class="modal-aviso">O usuário sai da lista e não consegue mais entrar. O histórico é mantido (exclusão lógica).</p>

            <form method="POST" action="usuario_excluir.php" class="modal-botoes">
                <?= csrf_campo() ?>
                <input type="hidden" name="id_usuario" value="<?= (int) $paraExcluir['id_usuario'] ?>">
                <input type="hidden" name="q" value="<?= e($busca) ?>">
                <a href="<?= e($voltar) ?>" class="btn-cancelar">Cancelar</a>
                <button type="submit" class="btn-confirmar">Confirmar</button>
            </form>
        </div>
    </div>
<?php endif; ?>

<?php include 'footer.php'; ?>
