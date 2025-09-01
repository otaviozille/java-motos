document.addEventListener("DOMContentLoaded", function () {
    const botao = document.getElementById("button");
    const fechar = document.getElementById("fechar");

    if (botao && fechar) {
        botao.addEventListener("click", () => {
            const modal = document.getElementById("modal");
            modal.style.display = "block";
        });

        fechar.addEventListener("click", () => {
            const modal = document.getElementById("modal");
            modal.style.display = "none";
        });
    }

    const likeButtons = document.querySelectorAll('.like-button');
    const deslikeButtons = document.querySelectorAll('.deslike-button');

    likeButtons.forEach((likeBtn, index) => {
        const deslikeBtn = deslikeButtons[index];
        const postId = likeBtn.getAttribute('data-id');

        likeBtn.addEventListener('click', () => {
            const isLiked = likeBtn.classList.contains('liked');

            if (isLiked) {
                likeBtn.classList.remove('liked');
                enviarInteracao(postId, 1);
            } else {
                likeBtn.classList.add('liked');
                deslikeBtn.classList.remove('desliked');
                enviarInteracao(postId, 1);
            }
        });

        deslikeBtn.addEventListener('click', () => {
            const isDesliked = deslikeBtn.classList.contains('desliked');

            if (isDesliked) {
                deslikeBtn.classList.remove('desliked');
                enviarInteracao(postId, 0);
            } else {
                deslikeBtn.classList.add('desliked');
                likeBtn.classList.remove('liked');
                enviarInteracao(postId, 0);
            }
        });
    });

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
});

function enviarInteracao(publicacaoId, tipo) {
    fetch('likedeslike.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `publicacao_id=${publicacaoId}&like_deslike=${tipo}`
    })
    .then(response => {
        if (response.status === 401) {
            const modal = document.getElementById("modal");
            modal.style.display = "block";
            throw new Error("Não autenticado");
        }
        return response.text();
    })
    .then(data => {
        console.log("Resposta:", data);
    })
    .catch(error => {
        console.error("Erro:", error);
    });
}
