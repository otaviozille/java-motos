<?php
session_start();
include_once 'conexao.php';
include_once 'index.php';

$likes = $likes ?? 0;
$dislikes = $dislikes ?? 0;
$base64Image = $base64Image ?? '';

$postagens = "SELECT * FROM publicacoes";
$resultadoPostagens = $conexao->query($postagens);

$usuario_id = $_SESSION['usuario_id'] ?? null;
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../CSS/style.css">
    <title>Java Motos</title>
</head>
<body>

<div class="aside2">
    <?php if ($usuario_id): ?>
        <form action="logout.php" method="POST">
            <button type="submit"><strong>Sair</strong></button>
        </form>
    <?php else: ?>
        <button id="button"><strong>Entrar</strong></button>
    <?php endif; ?>
</div>



<main>

    <h2>Publicações</h2>
    <div id="modal">
        <form action="./login.php" method="POST">
            <h2>Login</h2>
            <input type="email" name="email" placeholder="Email" required><br>
            <input type="password" name="senha" placeholder="Senha" required><br>
            <button type="submit" id="entrar">Entrar</button>
        </form>
        <button id="fechar">Cancelar</button>
    </div>

    <?php
    if ($resultadoPostagens && $resultadoPostagens->num_rows > 0) {
        while ($row = $resultadoPostagens->fetch_assoc()) {
            $publicacao_id = $row['id'];
            $like_class = '';
            $deslike_class = '';

            if ($usuario_id) {
                $stmt = $conexao->prepare("SELECT like_deslike FROM interacoes WHERE usuario_id = ? AND publicacao_id = ?");
                $stmt->bind_param("ii", $usuario_id, $publicacao_id);
                $stmt->execute();
                $res = $stmt->get_result();
                if ($interacao = $res->fetch_assoc()) {
                    if ($interacao['like_deslike'] == 1) {
                        $like_class = 'liked';
                    } else {
                        $deslike_class = 'desliked';
                    }
                }
                $stmt->close();
            }

            echo "<div class='postagem'>";
            echo "<h4>" . htmlspecialchars($row['titulo']) . "</h4>";
            echo "<div class='conteudo-postagem'>";
            echo "  <div class='imagem'>";
            echo "    <img src='data:image/jpeg;base64," . base64_encode($row['foto']) . "' alt='Imagem da postagem'>";
            echo "  </div>";
            echo "  <p>" . htmlspecialchars($row['legenda']) . "</p>";
            echo "</div>";

// Contar likes e deslikes
$stmtLikes = $conexao->prepare("SELECT COUNT(*) as total FROM interacoes WHERE publicacao_id = ? AND like_deslike = 1");
$stmtLikes->bind_param("i", $publicacao_id);
$stmtLikes->execute();
$resultLikes = $stmtLikes->get_result()->fetch_assoc()['total'];
$stmtLikes->close();

$stmtDeslikes = $conexao->prepare("SELECT COUNT(*) as total FROM interacoes WHERE publicacao_id = ? AND like_deslike = 0");
$stmtDeslikes->bind_param("i", $publicacao_id);
$stmtDeslikes->execute();
$resultDeslikes = $stmtDeslikes->get_result()->fetch_assoc()['total'];
$stmtDeslikes->close();

echo "<div class='botoes-interacao'>";
echo "  <div class='botoes-grupo-esquerda'>";
echo "    <button class='like-button $like_class' data-id='" . $publicacao_id . "'></button><span class='contagem-interacao'>$resultLikes</span>";
echo "    <button class='deslike-button $deslike_class' data-id='" . $publicacao_id . "'></button><span class='contagem-interacao'>$resultDeslikes</span>";
echo "  </div>";
echo "  <button class='comentario-button' data-modal='modal-comentario-$publicacao_id'></button>";
echo "</div>";



            // Modal de Comentários
            echo "<div class='modal-comentario' id='modal-comentario-$publicacao_id' style='display:none;'>";
            echo "  <form action='comentar.php' method='POST'>";
            echo "      <input type='hidden' name='publicacao_id' value='$publicacao_id'>";
            echo "      <textarea name='comentario' placeholder='Escreva seu comentário...' required></textarea><br>";
            echo "      <button type='submit'>Enviar Comentário</button>";
            echo "  </form>";

            echo "  <div class='comentarios-existentes'>";
            $stmtComentarios = $conexao->prepare("
                SELECT c.id, c.texto, u.nome_usuario, c.usuario_id 
                FROM comentarios c
                JOIN usuarios u ON c.usuario_id = u.id
                WHERE c.publicacao_id = ?
            ");
            $stmtComentarios->bind_param("i", $publicacao_id);
            $stmtComentarios->execute();
            $resultComentarios = $stmtComentarios->get_result();

            if ($resultComentarios->num_rows > 0) {
                while ($comentario = $resultComentarios->fetch_assoc()) {
                    $comentarioId = $comentario['id'];
                    echo "<div class='comentario'>";
                    echo "<div class='comentario-header'>";
                    echo "<strong>" . htmlspecialchars($comentario['nome_usuario']) . "</strong>";
                    
                    if ($usuario_id == $comentario['usuario_id']) {
                        echo "<div class='comentario-acoes'>";
                        echo "
                            <form action='deletar_comentario.php' method='POST' onsubmit='return confirmarExclusao();'>
                                <input type='hidden' name='comentario_id' value='$comentarioId'>
                                <button type='submit' class='excluir-comentario'></button>
                            </form>
                            <button onclick='mostrarEdicao($comentarioId)' class='editar-comentario'></button>
                        ";
                        echo "</div>";
                    }
                    echo "</div>"; // fim .comentario-header

                    echo "<div class='comentario-texto' id='comentario-texto-$comentarioId'>";
                    echo "<p>" . htmlspecialchars($comentario['texto']) . "</p>";
                    echo "</div>";

                    if ($usuario_id == $comentario['usuario_id']) {
                        echo "
                            <form id='form-editar-$comentarioId' action='editar_comentario.php' method='POST' style='display:none;'>
                                <input type='hidden' name='comentario_id' value='$comentarioId'>
                                <textarea name='novo_texto' required>" . htmlspecialchars($comentario['texto']) . "</textarea><br>
                                <button type='submit'>Atualizar</button>
                                <button type='button' onclick='cancelarEdicao($comentarioId)'>Cancelar</button>
                            </form>
                        ";
                    }
                    echo "</div>"; // fim .comentario
                }
            } else {
                echo "<p>Nenhum comentário ainda.</p>";
            }

            $stmtComentarios->close();
            echo "  </div>"; // fim comentarios-existentes
            echo "  <button class='fechar-modal'>Fechar</button>";
            echo "</div>"; // fim modal
            echo "</div>"; // fim postagem
        }
    } else {
        echo "<p>Nenhuma postagem encontrada.</p>";
    }
    ?>
</main>


<aside>
<?php if (isset($_SESSION['usuario_id'], $_SESSION['foto_usuario'], $_SESSION['nome_usuario'])): ?>
    <img src="data:image/jpeg;base64,<?= $_SESSION['foto_usuario'] ?>" alt="Foto do usuário" class="foto-usuario">
    <h3><?= htmlspecialchars($_SESSION['nome_usuario']) ?></h3>
<?php else: ?>
    <img src="data:Imagens/jpeg;base64,<?= $base64Image ?>" alt="Logo da empresa" class="logoempresa">
    <h3>Java Motos</h3>
<?php endif; ?>

<hr>
<div class="quantidadelikesdeslikes">
    <div class="quantidadelikes">
        <p><?= $likes ?><br>Quantidade de<br> likes</p>
    </div>
    <div class="quantidadedeslikes">
        <p><?= $dislikes ?><br>Quantidade de<br> deslikes</p>
    </div>
</div>
</aside>


<footer>
    <div class="footer-container">
        <p>Java Motos</p>
        <div class="social-icons">
            <img src="../../Imagens/Instagram.svg" alt="Instagram" class="social-icon">
            <img src="../../Imagens/Twitter.svg" alt="Twitter" class="social-icon">
            <img src="../../Imagens/whatsapp.svg" alt="Whatsapp" class="social-icon">
        </div>
        <p>Copyright - 2024</p>
    </div>
</footer>

<script src="../JS/script.js"></script>
<script>
document.querySelectorAll('.comentario-button').forEach(button => {
    button.addEventListener('click', () => {
        const modalId = button.getAttribute('data-modal');
        const modal = document.getElementById(modalId);
        if (modal) modal.style.display = 'block';
    });
});

document.querySelectorAll('.fechar-modal').forEach(btn => {
    btn.addEventListener('click', () => {
        btn.parentElement.style.display = 'none';
    });
});

document.getElementById('button')?.addEventListener('click', () => {
    document.getElementById('modal').style.display = 'block';
});

document.getElementById('fechar')?.addEventListener('click', () => {
    document.getElementById('modal').style.display = 'none';
});

function confirmarExclusao() {
    return confirm("Tem certeza que deseja excluir este comentário?");
}

function mostrarEdicao(id) {
    document.getElementById('comentario-texto-' + id).style.display = 'none';
    document.getElementById('form-editar-' + id).style.display = 'block';
}

function cancelarEdicao(id) {
    document.getElementById('comentario-texto-' + id).style.display = 'block';
    document.getElementById('form-editar-' + id).style.display = 'none';
}
</script>

</body>
</html>
