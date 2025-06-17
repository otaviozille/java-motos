<?php
session_start();
include "conexao.php";

$id_usuario_logado = $_SESSION['id_usuario'] ?? null;

$sql = "SELECT p.*, e.nome AS empresa_nome FROM publicacoes p
        JOIN empresa e ON p.id_empresa = e.id_empresa
        ORDER BY p.data_postagem DESC";
$result = $conn->query($sql);

$publicacoes = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $id_publicacao = $row['id_publicacao'];

        // Busca os comentários da publicação
        $comentarios = [];
        $sqlComentarios = "SELECT c.*, u.nome FROM comentarios c
                           JOIN usuarios u ON c.id_usuario = u.id_usuario
                           WHERE c.id_publicacao = $id_publicacao
                           ORDER BY c.data_comentario ASC";
        $resComentarios = $conn->query($sqlComentarios);

        if ($resComentarios->num_rows > 0) {
            while ($comentario = $resComentarios->fetch_assoc()) {
                $comentarios[] = [
                    'id_comentario' => $comentario['id_comentario'],
                    'id_usuario' => $comentario['id_usuario'],
                    'nome' => $comentario['nome'],
                    'comentario' => htmlspecialchars($comentario['comentario']),
                    'data' => $comentario['data_comentario'],
                    'pode_editar' => $comentario['id_usuario'] == $id_usuario_logado
                ];
            }
        }

        $publicacoes[] = [
            'id_publicacao' => $id_publicacao,
            'titulo' => htmlspecialchars($row['titulo']),
            'legenda' => nl2br(htmlspecialchars($row['legenda'])),
            'imagem' => htmlspecialchars($row['imagem']),
            'likes' => (int)$row['likes'],
            'dislikes' => (int)$row['dislikes'],
            'comentarios' => $comentarios
        ];
    }
}

header('Content-Type: application/json');
echo json_encode($publicacoes);

$conn->close();
