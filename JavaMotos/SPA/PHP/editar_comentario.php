<?php
session_start();
include_once 'conexao.php';

if (isset($_POST['comentario_id'], $_POST['novo_texto'], $_SESSION['usuario_id'])) {
    $comentario_id = $_POST['comentario_id'];
    $novo_texto = trim($_POST['novo_texto']);
    $usuario_id = $_SESSION['usuario_id'];

    // Só permite editar se o comentário for do usuário logado
    $stmt = $conexao->prepare("UPDATE comentarios SET texto = ? WHERE id = ? AND usuario_id = ?");
    $stmt->bind_param("sii", $novo_texto, $comentario_id, $usuario_id);
    $stmt->execute();
    $stmt->close();
}

header("Location: page.php"); // ou para a página com os comentários
exit;
