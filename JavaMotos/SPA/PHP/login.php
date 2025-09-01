<?php
session_start();
include_once 'conexao.php';

$email = $_POST['email'] ?? '';
$senha = $_POST['senha'] ?? '';

$sql = "SELECT id, nome_usuario, foto, senha FROM usuarios WHERE email = ?";
$stmt = $conexao->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    if ($senha === $row['senha']) {
        $_SESSION['usuario_id'] = $row['id'];
        $_SESSION['nome_usuario'] = $row['nome_usuario'];
        $_SESSION['foto_usuario'] = base64_encode($row['foto']); // se a coluna 'foto' for blob
        header("Location: page.php?login=sucesso");
        exit;
    } else {
        header("Location: page.php?erro=senha");
        exit;
    }
} else {
    header("Location: page.php?erro=email");
    exit;
}
