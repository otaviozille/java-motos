<?php
session_start();
include_once 'conexao.php';

$comentario_id = $_POST['comentario_id'] ?? null;
$usuario_id = $_SESSION['usuario_id'] ?? null;

if ($comentario_id && $usuario_id) {
    $stmt = $conexao->prepare("DELETE FROM comentarios WHERE id = ? AND usuario_id = ?");
    $stmt->bind_param("ii", $comentario_id, $usuario_id);
    $stmt->execute();
}

header("Location: page.php");
exit;
