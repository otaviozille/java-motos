<?php
session_start();
include_once 'conexao.php';

if (!isset($_SESSION['usuario_id'])) {
    http_response_code(401);
    exit;
}

$usuario_id = intval($_SESSION['usuario_id']);

if (isset($_POST['publicacao_id']) && isset($_POST['like_deslike'])) {
    $publicacao_id = intval($_POST['publicacao_id']);
    $tipo = ($_POST['like_deslike'] == '1') ? 1 : 0;

    $check_sql = "SELECT like_deslike FROM interacoes WHERE usuario_id = ? AND publicacao_id = ?";
    $check_stmt = $conexao->prepare($check_sql);
    $check_stmt->bind_param("ii", $usuario_id, $publicacao_id);
    $check_stmt->execute();
    $result = $check_stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        if ((int)$row['like_deslike'] === $tipo) {
            // Mesmo tipo já existe: remover
            $delete_sql = "DELETE FROM interacoes WHERE usuario_id = ? AND publicacao_id = ?";
            $delete_stmt = $conexao->prepare($delete_sql);
            $delete_stmt->bind_param("ii", $usuario_id, $publicacao_id);
            $delete_stmt->execute();
            $delete_stmt->close();
            echo "Reação removida.";
        } else {
            // Atualiza para o novo tipo
            $update_sql = "UPDATE interacoes SET like_deslike = ? WHERE usuario_id = ? AND publicacao_id = ?";
            $update_stmt = $conexao->prepare($update_sql);
            $update_stmt->bind_param("iii", $tipo, $usuario_id, $publicacao_id);
            $update_stmt->execute();
            $update_stmt->close();
            echo "Interação atualizada.";
        }
    } else {
        // Nenhuma interação: inserir nova
        $insert_sql = "INSERT INTO interacoes (like_deslike, publicacao_id, usuario_id) VALUES (?, ?, ?)";
        $insert_stmt = $conexao->prepare($insert_sql);
        $insert_stmt->bind_param("iii", $tipo, $publicacao_id, $usuario_id);
        $insert_stmt->execute();
        $insert_stmt->close();
        echo "Interação registrada.";
    }

    $check_stmt->close();
} else {
    http_response_code(400);
}
