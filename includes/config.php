<?php

$host = 'localhost';
$dbname = 'wattgym';
$username = 'deca';
$password = '';

try {
    //conexão usando PDO
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    
    //em caso de erro
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Tá aplicado!";

} catch(PDOException $e) {
    echo "Catabolizou: " . $e->getMessage();
}
?>