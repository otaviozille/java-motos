<?php
session_start();
include "conexao.php";

if (!isset($_SESSION['id_usuario'])) {
    echo json_encode(['sucesso' => false, 'erro' => 'Você precisa estar logado.']);
    exit;
}

$id_comentario = $_POST['id_comentario'] ?? '';
$comentario = $_POST['comentario'] ?? '';

if (empty($id_comentario) || empty($comentario)) {
    echo json_encode(['sucesso' => false, 'erro' => 'Dados incompletos.']);
    exit;
}

// Verifica se o comentário pertence ao usuário logado
$sql = "SELECT id_usuario FROM comentarios WHERE id_comentario = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_comentario);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows === 0) {
    echo json_encode(['sucesso' => false, 'erro' => 'Comentário não encontrado.']);
    exit;
}

$stmt->bind_result($id_autor);
$stmt->fetch();

if ($id_autor != $_SESSION['id_usuario']) {
    echo json_encode(['sucesso' => false, 'erro' => 'Você não pode editar esse comentário.']);
    exit;
}

// Atualiza o comentário
$sql = "UPDATE comentarios SET comentario = ? WHERE id_comentario = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("si", $comentario, $id_comentario);

if ($stmt->execute()) {
    echo json_encode(['sucesso' => true]);
} else {
    echo json_encode(['sucesso' => false, 'erro' => 'Erro ao atualizar o comentário.']);
}

$conn->close();
?>
