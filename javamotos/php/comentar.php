<?php
session_start();
require 'conexao.php';

if (!isset($_SESSION['id_usuario'])) {
    echo json_encode(['erro' => 'Você precisa estar logado.']);
    exit;
}

$id_usuario = $_SESSION['id_usuario'];
$id_publicacao = $_POST['id_publicacao'];
$comentario = $_POST['comentario'];

$sql = "INSERT INTO comentarios (id_usuario, id_publicacao, comentario) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->execute([$id_usuario, $id_publicacao, $comentario]);

echo json_encode(['sucesso' => true]);
