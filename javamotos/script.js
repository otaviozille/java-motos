let modoCadastro = false;

function fecharLogin() {
  document.getElementById('modalLogin').style.display = 'none';
  resetarFormulario();
}

function resetarFormulario() {
  document.getElementById('nome').value = '';
  document.getElementById('email').value = '';
  document.getElementById('senha').value = '';
  document.getElementById('erroMsg').style.display = 'none';
  document.getElementById('nome').classList.remove('error');
  document.getElementById('email').classList.remove('error');
  document.getElementById('senha').classList.remove('error');
}

function alternarModo() {
  modoCadastro = !modoCadastro;

  const titulo = document.getElementById('modalTitulo');
  const nome = document.getElementById('nome');
  const btnEntrar = document.getElementById('btnEntrar');
  const alternar = document.getElementById('alternarBtn');

  if (modoCadastro) {
    titulo.textContent = 'Cadastro';
    nome.style.display = 'block';
    btnEntrar.textContent = 'Cadastrar';
    alternar.textContent = 'Já tem conta? Fazer login';
  } else {
    titulo.textContent = 'Login';
    nome.style.display = 'none';
    btnEntrar.textContent = 'Entrar';
    alternar.textContent = 'Ainda não tem conta? Cadastre-se';
  }
}

function enviarFormulario() {
  const nome = document.getElementById('nome');
  const email = document.getElementById('email');
  const senha = document.getElementById('senha');
  const erro = document.getElementById('erroMsg');

  nome.classList.remove('error');
  email.classList.remove('error');
  senha.classList.remove('error');

  let valido = true;

  if (modoCadastro && nome.value.trim() === '') {
    nome.classList.add('error');
    valido = false;
  }

  if (email.value.trim() === '') {
    email.classList.add('error');
    valido = false;
  }

  if (senha.value.trim() === '') {
    senha.classList.add('error');
    valido = false;
  }

  if (!valido) {
    erro.textContent = 'Preencha todos os campos.';
    erro.style.display = 'block';
    return;
  }

  const url = modoCadastro ? 'php/cadastrar.php' : 'php/login.php';
  const formData = new URLSearchParams();

  if (modoCadastro) formData.append('nome', nome.value);
  formData.append('email', email.value);
  formData.append('senha', senha.value);

  fetch(url, {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: formData.toString()
  })
  .then(res => res.text())
  .then(response => {
    if (!modoCadastro) {
      try {
        const json = JSON.parse(response);
        if (json.status === 'sucesso') {
          location.reload();
        } else {
          erro.textContent = json.mensagem;
          erro.style.display = 'block';
        }
      } catch {
        erro.textContent = 'Erro inesperado.';
        erro.style.display = 'block';
      }
    } else {
      alert('Cadastro realizado com sucesso! Faça o login.');
      alternarModo();
    }
  });
}




function abrirModal() {
  document.getElementById('auth-container').style.display = 'block';
}

document.getElementById('irCadastro').addEventListener('click', () => {
  document.getElementById('auth-container').classList.add("right-panel-active");
});

document.getElementById('voltarLogin').addEventListener('click', () => {
  document.getElementById('auth-container').classList.remove("right-panel-active");
});


function abrirLogin() {
  const container = document.getElementById('auth-container');
  container.style.display = 'block';
  container.classList.remove("right-panel-active"); // Garante que abre no modo login
}
document.getElementById('fecharModal').addEventListener('click', () => {
  document.getElementById('auth-container').style.display = 'none';
});
function abrirLogin() {
  const container = document.getElementById('auth-container');
  container.style.display = 'flex';
  container.classList.remove("right-panel-active");
}

document.getElementById('fecharModal').addEventListener('click', () => {
  document.getElementById('auth-container').style.display = 'none';
});

document.getElementById('irCadastro').addEventListener('click', () => {
  document.getElementById('container').classList.add("right-panel-active");
});

document.getElementById('voltarLogin').addEventListener('click', () => {
  document.getElementById('container').classList.remove("right-panel-active");
});

document.getElementById('formCadastro').addEventListener('submit', async function (e) {
  e.preventDefault();
  const form = new FormData(this);
  const response = await fetch('php/cadastrar.php', {
    method: 'POST',
    body: new URLSearchParams(form)
  });
  const resultado = await response.text();
  alert('Cadastro realizado com sucesso!');
  document.getElementById('voltarLogin').click();
});

document.getElementById('formLogin').addEventListener('submit', async function (e) {
  e.preventDefault();
  const form = new FormData(this);
  const response = await fetch('php/login.php', {
    method: 'POST',
    body: new URLSearchParams(form)
  });
  const json = await response.json();
if (json.status === 'sucesso') {
  window.location.href = json.destino;
} else {
  alert(json.mensagem || 'Erro ao fazer login.');
}

});

function interagir(elemento, tipo) {
  const id_publicacao = elemento.getAttribute('data-id');

  fetch('php/interagir.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: `id_publicacao=${id_publicacao}&tipo=${tipo}`
  })
  .then(res => res.json())
  .then(data => {
    if (data.sucesso) {
      carregarPublicacoes(); // Recarrega os dados atualizados
    } else {
      alert(data.erro || 'Erro ao interagir.');
    }
  });
}
function comentar(botao) {
  const id = botao.getAttribute('data-id');
  const texto = document.getElementById('textoComentario-' + id).value;

  fetch('php/comentar.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: `id_publicacao=${id}&comentario=${encodeURIComponent(texto)}`
  })
  .then(res => res.json())
  .then(data => {
    if (data.sucesso) {
      alert('Comentário enviado!');
      carregarPublicacoes();
    } else {
      alert(data.erro || 'Erro ao comentar.');
    }
  });
}
async function carregarPublicacoes() {
  const resposta = await fetch('php/carregar_publicacoes.php');
  const publicacoes = await resposta.json();
  const container = document.getElementById('publicacoes-container');
  container.innerHTML = '';

  publicacoes.forEach(pub => {
    const div = document.createElement('div');
    div.classList.add('publicacao');
    div.innerHTML = `
      <img src="uploads/${pub.imagem}" alt="Imagem do doce">
      <h3>${pub.titulo}</h3>
      <p>${pub.legenda}</p>
      <div class="interacoes">
        <button onclick="interagir(${pub.id_publicacao}, 'like')">
          <i class="bi bi-hand-thumbs-up"></i> <span>${pub.likes}</span>
        </button>
        <button onclick="interagir(${pub.id_publicacao}, 'dislike')">
          <i class="bi bi-hand-thumbs-down"></i> <span>${pub.dislikes}</span>
        </button>
        <button onclick="abrirComentarios(${pub.id_publicacao})">
          <i class="bi bi-chat"></i> <span>${pub.comentarios || 0}</span>
        </button>
      </div>
      <div class="area-comentarios" id="comentarios-${pub.id_publicacao}" style="display:none;">
        <div class="lista-comentarios"></div>
        <textarea placeholder="Digite um comentário..."></textarea>
        <button onclick="comentar(${pub.id_publicacao})">Enviar</button>
      </div>
    `;
    if (pub.comentarios && pub.comentarios.length > 0) {
  const listaComentarios = div.querySelector('.lista-comentarios');

  pub.comentarios.forEach(com => {
    const comentarioItem = document.createElement('div');
    comentarioItem.classList.add('comentario');
    comentarioItem.innerHTML = `
      <strong>${com.nome}:</strong> ${com.comentario}
      <small> (${com.data})</small>
      ${com.pode_editar ? `
        <button onclick="editarComentario(${com.id_comentario})">Editar</button>
        <button onclick="excluirComentario(${com.id_comentario})">Excluir</button>
      ` : ''}
    `;
    listaComentarios.appendChild(comentarioItem);
  });
}

    container.appendChild(div);
  });
}

async function interagir(idPublicacao, tipo) {
  const resposta = await fetch('php/interagir.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: `id_publicacao=${idPublicacao}&tipo=${tipo}`
  });

  const result = await resposta.json();
  if (result.status === 'erro') {
    alert(result.mensagem || 'Você precisa estar logado.');
    return;
  }

  carregarPublicacoes();
}

function abrirComentarios(idPublicacao) {
  const area = document.getElementById(`comentarios-${idPublicacao}`);
  area.style.display = area.style.display === 'none' ? 'block' : 'none';

  if (area.style.display === 'block') {
    fetch(`php/comentar.php?id_publicacao=${idPublicacao}`)
      .then(res => res.text())
      .then(html => {
        area.querySelector('.lista-comentarios').innerHTML = html;
      });
  }
}

async function comentar(idPublicacao) {
  const area = document.getElementById(`comentarios-${idPublicacao}`);
  const textarea = area.querySelector('textarea');
  const comentario = textarea.value.trim();
  if (comentario === '') return;

  const response = await fetch('php/comentar.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: `id_publicacao=${idPublicacao}&comentario=${encodeURIComponent(comentario)}`
  });

  const resultado = await response.text();
  if (resultado.includes('erro')) {
    alert('Você precisa estar logado.');
    return;
  }

  textarea.value = '';
  abrirComentarios(idPublicacao); // recarrega
}
function editarComentario(id) {
  const texto = prompt("Editar comentário:");
  if (!texto) return;

  fetch('php/editar_comentario.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: `id_comentario=${id}&comentario=${encodeURIComponent(texto)}`
  })
  .then(res => res.json())
  .then(data => {
    if (data.sucesso) {
      alert("Comentário editado com sucesso!");
      carregarPublicacoes();
    } else {
      alert(data.erro || "Erro ao editar comentário.");
    }
  });
}

function excluirComentario(id) {
  if (!confirm("Tem certeza que deseja excluir este comentário?")) return;

  fetch('php/excluir_comentario.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: `id_comentario=${id}`
  })
  .then(res => res.json())
  .then(data => {
    if (data.sucesso) {
      alert("Comentário excluído.");
      carregarPublicacoes();
    } else {
      alert(data.erro || "Erro ao excluir comentário.");
    }
  });
}
