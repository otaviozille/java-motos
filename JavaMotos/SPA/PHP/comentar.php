<?php
session_start();
include_once 'conexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario_id = $_SESSION['usuario_id'] ?? null;
    $publicacao_id = $_POST['publicacao_id'] ?? null;
    $texto = trim($_POST['comentario'] ?? '');

    if ($usuario_id && $publicacao_id && $texto !== '') {
        $stmt = $conexao->prepare("INSERT INTO comentarios (usuario_id, publicacao_id, texto) VALUES (?, ?, ?)");
        $stmt->bind_param("iis", $usuario_id, $publicacao_id, $texto);

        if ($stmt->execute()) {
            header("Location: page.php"); // ou a página principal
            exit();
        } else {
            echo "Erro ao salvar o comentário.";
        }

        $stmt->close();
    } else {
        echo "Dados inválidos.";
    }
} else {
    echo "Requisição inválida.";
}
?>
