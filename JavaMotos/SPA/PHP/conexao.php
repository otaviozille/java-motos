<?php
@session_start();
$host = 'localhost';
$usuario = 'root';
$senha = '';
$dbname = 'javamotos';

// Criar a conexão
$conexao = new mysqli($host, $usuario, $senha, $dbname);
// Verificar a conexão
if ($conexao->connect_error) {
    die("Conexão falhou: " . $conexao->connect_error);
} else {
    // echo "Conexão bem-sucedida!";
}
?>