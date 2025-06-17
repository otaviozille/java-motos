<?php
session_start();
require 'conexao.php';

if (!isset($_SESSION['id_usuario'])) {
    echo json_encode(['erro' => 'Você precisa estar logado para interagir.']);
    exit;
}

$id_usuario = $_SESSION['id_usuario'];
$id_publicacao = $_POST['id_publicacao'];
$tipo = $_POST['tipo']; // like ou dislike

// Verifica se o usuário já interagiu
$sql = "SELECT * FROM interacoes WHERE id_usuario = ? AND id_publicacao = ?";
$stmt = $conn->prepare($sql);
$stmt->execute([$id_usuario, $id_publicacao]);

if ($stmt->rowCount() > 0) {
    // Atualiza a interação
    $sql = "UPDATE interacoes SET tipo = ? WHERE id_usuario = ? AND id_publicacao = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$tipo, $id_usuario, $id_publicacao]);
} else {
    // Insere nova interação
    $sql = "INSERT INTO interacoes (id_usuario, id_publicacao, tipo) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$id_usuario, $id_publicacao, $tipo]);
}

echo json_encode(['sucesso' => true]);
