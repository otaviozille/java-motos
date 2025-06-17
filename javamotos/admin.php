<?php
session_start();
include 'php/conexao.php';

if (!isset($_SESSION['id_usuario']) || $_SESSION['tipo'] !== 'admin') {
    header("Location: index.php");
    exit;
}

$id_usuario = $_SESSION['id_usuario'];
$nome_usuario = $_SESSION['nome'];
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Painel Administrativo - Kakaú Doces</title>
  <link rel="stylesheet" href="style.css" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" />
</head>
<body>
  <header>
    <h1>Kakaú Doces</h1>
  </header>

  <div class="perfil">
    <img src="image/kakau.doces.png" alt="Logo Kakaú Doces" />
    <h3>Painel Admin</h3>
    <div class="linha-laranja"></div>
    <p>Olá, <strong><?= htmlspecialchars($nome_usuario) ?></strong></p>

    <button onclick="abrirModalPublicacao()"><i class="bi bi-plus-circle"></i> Nova Publicação</button>
    <button onclick="location.href='relatorio.php'"><i class="bi bi-bar-chart-line"></i> Ver Relatórios</button>
    <form action="php/logout.php" method="post" style="margin-top: 1rem;">
      <button type="submit"><i class="bi bi-box-arrow-right"></i> Sair</button>
    </form>
  </div>

  <div class="main">
    <h2>Publicações</h2>
    <div id="publicacoes-container"></div>
  </div>

  <footer>
    <div class="footer-texto">
      <i class="bi bi-c-circle"></i> Kakaú Doces do Brasil - 2024
    </div>
    <div class="footer-icones">
      <i class="bi bi-instagram"></i>
      <i class="bi bi-twitter"></i>
      <i class="bi bi-whatsapp"></i>
    </div>
  </footer>

  <!-- MODAL DE NOVA PUBLICAÇÃO -->
  <div id="modalPublicacao" class="modal-auth" style="display: none;">
    <div class="container" style="max-width: 500px; padding: 2rem;">
      <button onclick="fecharModalPublicacao()" class="fechar-modal">&times;</button>
      <h2>Nova Publicação</h2>
      <form id="formNovaPublicacao" enctype="multipart/form-data">
        <input type="text" name="titulo" placeholder="Título" required />
        <textarea name="legenda" placeholder="Legenda" rows="4" required></textarea>
        <input type="file" name="imagem" accept="image/*" required onchange="previewImagem(this)">
<img id="preview" src="#" alt="Pré-visualização" style="max-width: 100%; margin-top: 10px; display: none;" />

        <button type="submit">Cadastrar</button>
      </form>
    </div>
  </div>

  <script>
    function abrirModalPublicacao() {
      document.getElementById('modalPublicacao').style.display = 'flex';
    }

    function fecharModalPublicacao() {
      document.getElementById('modalPublicacao').style.display = 'none';
    }

    async function carregarPublicacoes() {
      const resposta = await fetch('php/carregar_publicacoes.php');
      const publicacoes = await resposta.json();
      const container = document.getElementById('publicacoes-container');
      container.innerHTML = '';

      if (publicacoes.length === 0) {
        container.innerHTML = '<p>Nenhuma publicação cadastrada.</p>';
        return;
      }

      publicacoes.forEach(pub => {
        const div = document.createElement('div');
        div.classList.add('publicacao');
        div.innerHTML = `
          <img src="uploads/${pub.imagem}" alt="Imagem do doce">
          <h3>${pub.titulo}</h3>
          <p>${pub.legenda}</p>
          <div class="interacoes">
            <i class="bi bi-hand-thumbs-up"></i> ${pub.likes}
            <i class="bi bi-hand-thumbs-down"></i> ${pub.dislikes}
          </div>
          <div class="acoes-admin">
            <button onclick="editarPublicacao(${pub.id_publicacao})"><i class="bi bi-pencil-square"></i> Editar</button>
            <button onclick="excluirPublicacao(${pub.id_publicacao})"><i class="bi bi-trash"></i> Excluir</button>
          </div>
        `;
        container.appendChild(div);
      });
    }

    document.getElementById('formNovaPublicacao').addEventListener('submit', async function (e) {
      e.preventDefault();
      const formData = new FormData(this);
      const response = await fetch('php/cadastrar_publicacao.php', {
        method: 'POST',
        body: formData
      });
      const resultado = await response.text();
      alert(resultado.includes("sucesso") ? "Publicação adicionada com sucesso!" : resultado);
      fecharModalPublicacao();
      carregarPublicacoes();
    });

    async function excluirPublicacao(id) {
      if (!confirm("Tem certeza que deseja excluir esta publicação?")) return;

      const res = await fetch('php/excluir_publicacao.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `id_publicacao=${id}`
      });

      const result = await res.text();
      alert(result);
      carregarPublicacoes();
    }

    function editarPublicacao(id) {
      // Redireciona para página de edição (caso queira implementar futuramente)
      window.location.href = `editar_publicacao.php?id=${id}`;
    }

    window.onload = carregarPublicacoes;
    function previewImagem(input) {
  const preview = document.getElementById('preview');
  const file = input.files[0];

  if (file) {
    const reader = new FileReader();
    reader.onload = function (e) {
      preview.src = e.target.result;
      preview.style.display = 'block';
    };
    reader.readAsDataURL(file);
  } else {
    preview.src = '#';
    preview.style.display = 'none';
  }
}

  </script>
</body>
</html>
