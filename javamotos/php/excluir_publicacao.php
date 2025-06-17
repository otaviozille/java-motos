<?php
include 'conexao.php';

$id = $_POST['id_publicacao'];

$stmt = $conn->prepare("DELETE FROM publicacoes WHERE id_publicacao = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    echo json_encode(['sucesso' => true]);
} else {
    echo json_encode(['sucesso' => false, 'erro' => 'Erro ao excluir publicação.']);
}
$stmt->close();
$conn->close();
