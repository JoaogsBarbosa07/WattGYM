<?php
include 'config.php';

// Simulando dados
$nome = "Trembo da Silva";
$email = "formulamagica@email.com";

try {
    $sql = "INSERT INTO usuarios (nome, email) VALUES (:nome, :email)";
    
    $stmt = $conn->prepare($sql);
    
    $stmt->execute([
        ':nome' => $nome,
        ':email' => $email
    ]);

    echo "Usuário cadastrado com sucesso!";

} catch (PDOException $e) {
    echo "Erro ao cadastrar: " . $e->getMessage();
}
?>

<?php
include 'conexao.php';

try {
    $stmt = $conn->query("SELECT id, nome, email FROM usuarios");
    
    $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

    //exibe os dados
    foreach ($usuarios as $usuario) {
        echo "<p>ID: {$usuario['id']} - Nome: {$usuario['nome']} - E-mail: {$usuario['email']}</p>";
    }

} catch (PDOException $e) {
    echo "Erro ao buscar dados: " . $e->getMessage();
}
?>

