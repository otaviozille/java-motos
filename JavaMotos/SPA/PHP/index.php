<?php
@session_start();
include_once 'conexao.php';

// Buscar dados do usuário
$sql = "SELECT * FROM usuarios WHERE id = 1";
$resultado = $conexao->query($sql);
if ($resultado->num_rows > 0) {
    $usuario = $resultado->fetch_assoc();
    $base64Image = base64_encode($usuario['foto']);
} else {
    echo "Nenhum usuário encontrado.";
}

// Buscar total de likes
$likestotal = "SELECT count(*) as likes FROM interacoes WHERE like_deslike = 1";
$resultado2 = $conexao->query($likestotal);
$likes = 0;
if ($resultado2 && $resultado2->num_rows > 0) {
    $row2 = $resultado2->fetch_assoc();
    $likes = $row2['likes'];
}

// Buscar total de dislikes
$dislikestotal = "SELECT count(*) as dislikes FROM interacoes WHERE like_deslike = 0";
$resultado3 = $conexao->query($dislikestotal);
$dislikes = 0;
if ($resultado3 && $resultado3->num_rows > 0) {
    $row3 = $resultado3->fetch_assoc();
    $dislikes = $row3['dislikes'];
}
?>

