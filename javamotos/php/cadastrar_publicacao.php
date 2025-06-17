<?php
include 'conexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = $_POST['titulo'];
    $legenda = $_POST['legenda'];
    $id_empresa = $_POST['id_empresa'];

    // Lida com upload de imagem
    if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] === 0) {
        $nome_img = time() . '_' . basename($_FILES['imagem']['name']);
        $destino = '../uploads/' . $nome_img;

        if (move_uploaded_file($_FILES['imagem']['tmp_name'], $destino)) {
            $stmt = $conn->prepare("INSERT INTO publicacoes (id_empresa, titulo, legenda, imagem) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("isss", $id_empresa, $titulo, $legenda, $nome_img);

            if ($stmt->execute()) {
                echo "Publicação cadastrada com sucesso!";
            } else {
                echo "Erro ao inserir no banco de dados.";
            }
        } else {
            echo "Erro ao mover o arquivo.";
        }
    } else {
        echo "Erro no upload da imagem.";
    }
}
?>
