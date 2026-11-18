
<?php
/**
 * logout.php
 
 * Encerra a sessão do usuário (RF02): limpa $_SESSION, apaga o cookie da
 * sessão e destrói a sessão no servidor. Não mostra tela.
 * Chamado pelo link "Sair" do menu. Redireciona para login.php.
 
 * @author João Gabriel
 */
// Precisa iniciar a sessão para poder destruí-la
session_start();
 
// Limpa todas as variáveis da sessão
$_SESSION = [];
 
// Apaga o cookie da sessão no navegador
if (ini_get('session.use_cookies')) {
    $p = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
}
 
// Destrói a sessão no servidor
session_destroy();
 
header('Location: login.php');
exit;
 