<?php
session_start();
include "conexao.php";

// Verifica se os dados foram enviados
if (!isset($_POST['nome'], $_POST['email'], $_POST['senha'])) {
    echo json_encode(['status' => 'erro', 'mensagem' => 'Dados incompletos.']);
    exit;
}

$nome = trim($_POST['nome']);
$email = trim($_POST['email']);
$senha = trim($_POST['senha']);

// Verificação de dados vazios
if (empty($nome) || empty($email) || empty($senha)) {
    echo json_encode(['status' => 'erro', 'mensagem' => 'Todos os campos são obrigatórios.']);
    exit;
}

// Verifica se o e-mail já existe
$stmt = $conn->prepare("SELECT id_usuario FROM usuarios WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    echo json_encode(['status' => 'erro', 'mensagem' => 'Email já cadastrado.']);
    exit;
}
$stmt->close();

// Criptografa a senha
$senhaHash = password_hash($senha, PASSWORD_DEFAULT);

// Insere o novo usuário
$stmt = $conn->prepare("INSERT INTO usuarios (nome, email, senha) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $nome, $email, $senhaHash);

if ($stmt->execute()) {
    $_SESSION['id_usuario'] = $stmt->insert_id;
    $_SESSION['nome'] = $nome;
    echo json_encode(['status' => 'sucesso']);
} else {
    echo json_encode(['status' => 'erro', 'mensagem' => 'Erro ao cadastrar.']);
}

$stmt->close();
$conn->close();
