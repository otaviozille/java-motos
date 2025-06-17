<?php
session_start();
include "conexao.php";

// Pega os dados do formulário
$email = $_POST['email'] ?? '';
$senha = $_POST['senha'] ?? '';

// Verifica se o usuário existe
$sql = "SELECT * FROM usuarios WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows === 1) {
    $usuario = $resultado->fetch_assoc();

    // Verifica a senha
    if (password_verify($senha, $usuario['senha'])) {
        // Armazena dados na sessão
        $_SESSION['id_usuario'] = $usuario['id_usuario'];
        $_SESSION['nome'] = $usuario['nome'];
        $_SESSION['tipo'] = $usuario['tipo']; // 'admin' ou 'comum'

        // Retorna JSON com o destino
        if ($usuario['tipo'] === 'admin') {
            echo json_encode(['status' => 'sucesso', 'destino' => 'admin.php']);
        } else {
            echo json_encode(['status' => 'sucesso', 'destino' => 'index.php']);
        }
    } else {
        echo json_encode(['status' => 'erro', 'mensagem' => 'Senha incorreta.']);
    }
} else {
    echo json_encode(['status' => 'erro', 'mensagem' => 'Usuário não encontrado.']);
}

$conn->close();
?>
