
<?php
/**
 * config.php
 *
 * Conexão com o banco de dados (PDO). Ao ser incluído, deixa a variável $conn
 * pronta para as páginas (RNF02). Se a conexão falhar, grava o erro no log do
 * servidor, mostra só uma mensagem genérica e interrompe a página (RNF04).
 *
 * @author João Gabriel
 */
 
$host = 'localhost';
$dbname = 'wattgym';
$username = 'root';
$password = '';
 
try {
    // Conexão usando PDO
    $conn = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $username,
        $password
    );
 
    // Lança exceções em caso de erro de SQL
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
 
    // Prepared statements de verdade (mais seguro contra SQL injection)
    $conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
 
} catch (PDOException $e) {
    // O detalhe técnico vai só para o log do servidor
    error_log("Erro de conexão com o banco: " . $e->getMessage());
 
    // O usuário vê uma mensagem genérica e o script para aqui
    http_response_code(500);
    die("Não foi possível conectar ao sistema. Tente novamente mais tarde.");
}
 