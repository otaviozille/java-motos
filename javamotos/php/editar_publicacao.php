<?php
include 'conexao.php';

$id = $_POST['id_publicacao'];
$titulo = $_POST['titulo'];
$legenda = $_POST['legenda'];

$stmt = $conn->prepare("UPDATE publicacoes SET titulo = ?, legenda = ? WHERE id_publicacao = ?");
$stmt->bind_param("ssi", $titulo, $legenda, $id);

if ($stmt->execute()) {
    echo json_encode(['sucesso' => true]);
} else {
    echo json_encode(['sucesso' => false, 'erro' => 'Erro ao editar publicação.']);
}
$stmt->close();
$conn->close();
