<?php session_start(); ?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Java Motos</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <header>
    <h1>Java Motos</h1>
  </header>

  <div class="perfil">
    <img src="image/logo_javamotos.png" alt="Logo javamotos">
    <h3>Kakaú Doces</h3>
    <div class="linha-laranja"></div>
    <p>Likes: <span id="likes-gerais">0</span></p>
    <p>Dislikes: <span id="dislikes-gerais">0</span></p>
  </div>

  <div class="main">
    <h2>Publicações</h2>
    <div id="publicacoes-container"></div>
  </div>

  <div class="login">
    <?php if (isset($_SESSION['id_usuario'])): ?>
      <p>Olá, <strong><?= htmlspecialchars($_SESSION['nome']) ?></strong>!</p>
      <form action="php/logout.php" method="POST">
        <button type="submit">Sair</button>
      </form>
    <?php else: ?>
      <button onclick="abrirLogin()">Entrar</button>
    <?php endif; ?>
  </div>

  

  <footer>
    <div class="footer-texto">
      <i class="bi bi-c-circle"></i> Java Motos do Brasil - 2024
    </div>
    <div class="footer-icones">
      <i class="bi bi-instagram"></i>
      <i class="bi bi-twitter"></i>
      <i class="bi bi-whatsapp"></i>
    </div>
  </footer>

  <!-- MODAL DE LOGIN/CADASTRO COM SLIDER -->
  <div id="auth-container" class="modal-auth" style="display: none;">
    <div class="container" id="container">
      <button id="fecharModal" class="fechar-modal">&times;</button>
      <div class="form-container sign-up-container">
        <form id="formCadastro">
          <h2>Criar Conta</h2>
          <input type="text" name="nome" placeholder="Nome" required />
          <input type="email" name="email" placeholder="Email" required />
          <input type="password" name="senha" placeholder="Senha" required />
          <button type="submit">Cadastrar</button>
        </form>
      </div>
      <div class="form-container sign-in-container">
        <form id="formLogin">
          <h2>Login</h2>
          <input type="email" name="email" placeholder="Email" required />
          <input type="password" name="senha" placeholder="Senha" required />
          <button type="submit">Entrar</button>
        </form>
      </div>
      <div class="overlay-container"> 
        <div class="overlay">
          <div class="overlay-panel overlay-left">
            <h1>Bem-vindo de volta!</h1>
            <p>Para continuar, faça login com seus dados</p>
            <button class="ghost" id="voltarLogin">Login</button>
          </div>
          <div class="overlay-panel overlay-right">
            <h1>Olá, amigo!</h1>
            <p>Insira seus dados e comece sua jornada doce conosco</p>
            <button class="ghost" id="irCadastro">Cadastre-se</button>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Scripts -->
  <script src="script.js"></script>
  
</body>
</html>
