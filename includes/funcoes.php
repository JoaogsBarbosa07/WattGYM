<?php
/**
 * funcoes.php
 *
 * Funções compartilhadas pelo módulo de gestão de usuários (Sprint 01):
 * controle de acesso do administrador, proteção CSRF, auditoria e mensagens
 * de aviso entre páginas. Inclua depois de config.php.
 *
 * @author João Gabriel
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// A extensão mbstring vem ligada no XAMPP, mas nem todo servidor PHP tem.
// Estas versões simples só são criadas se ela estiver faltando (usadas apenas
// para medir e cortar textos a partir do início).
if (!function_exists('mb_strlen')) {
    function mb_strlen(string $texto): int
    {
        return (int) preg_match_all('/./us', $texto);
    }
}
if (!function_exists('mb_substr')) {
    function mb_substr(string $texto, int $inicio, ?int $tamanho = null): string
    {
        preg_match('/^.{0,' . max(0, (int) $tamanho) . '}/us', $texto, $parte);
        return $parte[0] ?? '';
    }
}

/**
 * Protege o HTML: transforma < > & e aspas em texto seguro (RNF05).
 *
 * @param string|null $texto Texto vindo do banco ou digitado pelo usuário.
 * @return string Texto seguro para ir dentro do HTML.
 */
function e(?string $texto): string
{
    return htmlspecialchars($texto ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * Exige um administrador ativo; senão, redireciona e encerra a página.
 *
 * Confere no banco, a cada página, o status e o perfil atuais do usuário.
 * Assim, quem foi colocado em Baixa ou perdeu o perfil admin perde o acesso
 * na hora, mesmo com a sessão ainda aberta.
 *
 * @param PDO $conn Conexão aberta com o banco.
 * @return int Id do administrador logado (serve como autor da auditoria).
 */
function exigir_admin(PDO $conn): int
{
    if (!isset($_SESSION['id_usuario'])) {
        header('Location: login.php');
        exit;
    }

    $id = (int) $_SESSION['id_usuario'];

    try {
        $stmt = $conn->prepare(
            "SELECT u.status, p.nome AS perfil
             FROM usuarios u
             JOIN perfis p ON p.id_perfil = u.id_perfil
             WHERE u.id_usuario = :id"
        );
        $stmt->execute([':id' => $id]);
        $dados = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Erro ao conferir administrador: " . $e->getMessage());
        http_response_code(500);
        exit("Não foi possível verificar o seu acesso. Tente novamente mais tarde.");
    }

    // Usuário apagado ou em Baixa: derruba a sessão
    if (!$dados || $dados['status'] !== 'Ativo') {
        $_SESSION = [];
        session_destroy();
        header('Location: login.php');
        exit;
    }

    // Logado, mas sem perfil de administrador
    if ($dados['perfil'] !== 'admin') {
        header('Location: inicio.php');
        exit;
    }

    return $id;
}

/**
 * Devolve o token CSRF da sessão, criando um se ainda não existir.
 *
 * O token prova que o formulário foi mostrado por este site. Um site mal-
 * intencionado não consegue ler esse valor para forjar um POST.
 *
 * @return string Token aleatório de 64 caracteres.
 */
function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Gera o campo escondido com o token, para colocar dentro de um formulário POST.
 *
 * @return string HTML do campo oculto.
 */
function csrf_campo(): string
{
    return '<input type="hidden" name="csrf_token" value="' . e(csrf_token()) . '">';
}

/**
 * Confere se o token recebido é o mesmo da sessão.
 *
 * Usa hash_equals() para a comparação não vazar informação pelo tempo de resposta.
 *
 * @param string|null $recebido Valor de $_POST['csrf_token'].
 * @return bool true se for válido.
 */
function csrf_valido(?string $recebido): bool
{
    return is_string($recebido)
        && !empty($_SESSION['csrf_token'])
        && hash_equals($_SESSION['csrf_token'], $recebido);
}

/**
 * Grava uma linha na tabela auditoria_usuarios.
 *
 * Nunca recebe senha, e-mail nem outros dados pessoais: só ids, o nome da ação
 * e o resultado. Se a gravação falhar, só registra no log do servidor e não
 * derruba a operação principal.
 *
 * @param PDO      $conn      Conexão aberta com o banco.
 * @param int|null $idAlvo    Usuário que sofreu a ação (null se não existe).
 * @param string   $acao      'CADASTRO' ou 'BAIXA'.
 * @param int|null $idAutor   Quem executou (null no cadastro feito pelo visitante).
 * @param string   $resultado 'Sucesso' ou o motivo da rejeição.
 * @return void
 */
function registrar_auditoria(PDO $conn, ?int $idAlvo, string $acao, ?int $idAutor, string $resultado): void
{
    try {
        $stmt = $conn->prepare(
            "INSERT INTO auditoria_usuarios (id_usuario_alvo, acao, id_autor, resultado)
             VALUES (:alvo, :acao, :autor, :resultado)"
        );
        $stmt->execute([
            ':alvo'      => $idAlvo,
            ':acao'      => $acao,
            ':autor'     => $idAutor,
            ':resultado' => mb_substr($resultado, 0, 150),
        ]);
    } catch (PDOException $e) {
        error_log("Erro ao gravar auditoria: " . $e->getMessage());
    }
}

/**
 * Guarda uma mensagem para ser mostrada na próxima página (depois de um redirect).
 *
 * @param string $tipo     'sucesso' ou 'erro' (vira a classe CSS do aviso).
 * @param string $mensagem Texto do aviso.
 * @return void
 */
function guardar_aviso(string $tipo, string $mensagem): void
{
    $_SESSION['aviso'] = ['tipo' => $tipo, 'mensagem' => $mensagem];
}

/**
 * Lê e apaga a mensagem guardada por guardar_aviso().
 *
 * @return array|null ['tipo' => ..., 'mensagem' => ...] ou null se não houver.
 */
function pegar_aviso(): ?array
{
    $aviso = $_SESSION['aviso'] ?? null;
    unset($_SESSION['aviso']);
    return $aviso;
}
