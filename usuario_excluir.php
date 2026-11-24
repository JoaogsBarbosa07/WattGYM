<?php
/**
 * usuario_excluir.php
 *
 * Processa a exclusão de usuário (Sprint 01). Não mostra tela: executa e volta
 * para usuarios.php. Chamado pelo botão "Confirmar" da caixa de confirmação.
 *
 * É uma exclusão lógica: o usuário recebe status 'Baixa' e a data em baixado_em,
 * e some da listagem e do login. Nada é apagado, então os pedidos e o histórico
 * continuam ligados a ele.
 *
 * Verificações, nesta ordem:
 *  1. Só aceita POST (GET recebe 405).
 *  2. Só administrador ativo (exigir_admin).
 *  3. Token CSRF válido (senão 403).
 *  4. Id numérico e positivo (senão 400).
 *  5. Não deixa o administrador excluir a si mesmo.
 *  6. A baixa só acontece se o usuário ainda estiver Ativo. Repetir o pedido
 *     não faz baixa duplicada: devolve um aviso e registra a rejeição.
 * A baixa e o registro de auditoria acontecem na mesma transação.
 *
 * @author João Gabriel
 */
session_start();
require_once 'includes/config.php';
require_once 'includes/funcoes.php';

// 1. Exclusão nunca por GET (um link ou uma imagem não podem apagar ninguém)
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    header('Allow: POST');
    exit('Método não permitido. A exclusão só é aceita pela tela de usuários.');
}

// 2. Permissão
$idAdmin = exigir_admin($conn);

// 3. CSRF
if (!csrf_valido($_POST['csrf_token'] ?? null)) {
    http_response_code(403);
    exit('Pedido recusado: o token de segurança é inválido ou expirou. Volte para a lista e tente de novo.');
}

// 4. Id
$id = filter_var($_POST['id_usuario'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
if ($id === false || $id === null) {
    http_response_code(400);
    exit('Pedido recusado: usuário inválido.');
}

$busca  = mb_substr(trim($_POST['q'] ?? ''), 0, 100);
$voltar = 'usuarios.php' . ($busca !== '' ? '?' . http_build_query(['q' => $busca]) : '');

// 5. Ninguém exclui a si mesmo
if ($id === $idAdmin) {
    registrar_auditoria($conn, $id, 'BAIXA', $idAdmin, 'Rejeitado: tentou excluir o próprio usuário');
    guardar_aviso('erro', 'Você não pode excluir o seu próprio usuário.');
    header('Location: ' . $voltar);
    exit;
}

try {
    $conn->beginTransaction();

    // 6. Condicional: só muda quem ainda está Ativo
    $stmt = $conn->prepare(
        "UPDATE usuarios SET status = 'Baixa', baixado_em = NOW()
         WHERE id_usuario = :id AND status = 'Ativo'"
    );
    $stmt->execute([':id' => $id]);

    if ($stmt->rowCount() === 1) {
        $aud = $conn->prepare(
            "INSERT INTO auditoria_usuarios (id_usuario_alvo, acao, id_autor, resultado)
             VALUES (:alvo, 'BAIXA', :autor, 'Sucesso')"
        );
        $aud->execute([':alvo' => $id, ':autor' => $idAdmin]);
        $conn->commit();

        guardar_aviso('sucesso', 'Usuário excluído com sucesso.');
    } else {
        // Ninguém foi alterado: ou o usuário não existe, ou já estava em Baixa
        $conn->rollBack();

        $consulta = $conn->prepare("SELECT 1 FROM usuarios WHERE id_usuario = :id");
        $consulta->execute([':id' => $id]);
        $existe = (bool) $consulta->fetchColumn();

        if ($existe) {
            registrar_auditoria($conn, $id, 'BAIXA', $idAdmin, 'Rejeitado: usuário já estava em Baixa');
            guardar_aviso('erro', 'Este usuário já tinha sido excluído. Nada foi alterado.');
        } else {
            registrar_auditoria($conn, null, 'BAIXA', $idAdmin, 'Rejeitado: usuário não encontrado');
            guardar_aviso('erro', 'Usuário não encontrado. Nada foi alterado.');
        }
    }
} catch (PDOException $e) {
    if ($conn->inTransaction()) {
        $conn->rollBack();
    }
    error_log("Erro ao excluir usuário: " . $e->getMessage());
    guardar_aviso('erro', 'Não foi possível excluir agora. Nada foi alterado.');
}

header('Location: ' . $voltar);
exit;
