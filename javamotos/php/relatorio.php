<?php
include "conexao.php";

// Proteção: só permite acesso se estiver logado como admin (opcional, dependendo do projeto)
if (!isset($_SESSION['id_usuario']) || $_SESSION['tipo'] !== 'admin') {
    header("Location: index.html");
    exit;
}
// Top 5 publicações mais curtidas
$sql1 = "SELECT titulo, likes FROM publicacoes ORDER BY likes DESC LIMIT 5";
$topLikes = $conn->query($sql1);

// Top 5 publicações com mais dislikes
$sql2 = "SELECT titulo, dislikes FROM publicacoes ORDER BY dislikes DESC LIMIT 5";
$topDislikes = $conn->query($sql2);

// Top 5 publicações com mais interações (likes + dislikes + comentários)
$sql3 = "
  SELECT p.titulo, 
         (p.likes + p.dislikes + COUNT(c.id_comentario)) AS interacoes
  FROM publicacoes p
  LEFT JOIN comentarios c ON p.id_publicacao = c.id_publicacao
  GROUP BY p.id_publicacao
  ORDER BY interacoes DESC
  LIMIT 5
";
$topInteracoes = $conn->query($sql3);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Relatório - Kakaú Doces</title>
  <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
  <link rel="stylesheet" href="style.css">
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background: #fafafa;
      padding: 40px;
    }

    h1 {
      color: #5A0C0C;
      text-align: center;
    }

    .grafico {
      margin: 40px auto;
      width: 90%;
      max-width: 1000px;
    }

    .voltar {
      text-align: center;
      margin-top: 30px;
    }

    .voltar a {
      background-color: #5A0C0C;
      color: white;
      padding: 10px 18px;
      border-radius: 5px;
      text-decoration: none;
    }

    .voltar a:hover {
      background-color: #8B0000;
    }
  </style>

  <script type="text/javascript">
    google.charts.load('current', {'packages':['corechart']});
    google.charts.setOnLoadCallback(drawCharts);

    function drawCharts() {
      drawLikesChart();
      drawDislikesChart();
      drawInteracoesChart();
    }

    function drawLikesChart() {
      var data = google.visualization.arrayToDataTable([
        ['Publicação', 'Likes'],
        <?php while ($row = $topLikes->fetch_assoc()) {
          echo "['" . addslashes($row['titulo']) . "', " . $row['likes'] . "],";
        } ?>
      ]);

      var options = {
        title: 'Top 5 Publicações Mais Curtidas',
        legend: { position: 'none' },
        colors: ['#4CAF50']
      };

      var chart = new google.visualization.ColumnChart(document.getElementById('likes_chart'));
      chart.draw(data, options);
    }

    function drawDislikesChart() {
      var data = google.visualization.arrayToDataTable([
        ['Publicação', 'Dislikes'],
        <?php while ($row = $topDislikes->fetch_assoc()) {
          echo "['" . addslashes($row['titulo']) . "', " . $row['dislikes'] . "],";
        } ?>
      ]);

      var options = {
        title: 'Top 5 Publicações com Mais Dislikes',
        legend: { position: 'none' },
        colors: ['#e74c3c']
      };

      var chart = new google.visualization.ColumnChart(document.getElementById('dislikes_chart'));
      chart.draw(data, options);
    }

    function drawInteracoesChart() {
      var data = google.visualization.arrayToDataTable([
        ['Publicação', 'Interações'],
        <?php while ($row = $topInteracoes->fetch_assoc()) {
          echo "['" . addslashes($row['titulo']) . "', " . $row['interacoes'] . "],";
        } ?>
      ]);

      var options = {
        title: 'Top 5 Publicações com Mais Interações',
        legend: { position: 'none' },
        colors: ['#f39c12']
      };

      var chart = new google.visualization.ColumnChart(document.getElementById('interacoes_chart'));
      chart.draw(data, options);
    }
  </script>
</head>
<body>
  <h1>Relatório de Publicações</h1>

  <div class="grafico" id="likes_chart"></div>
  <div class="grafico" id="dislikes_chart"></div>
  <div class="grafico" id="interacoes_chart"></div>

  <div class="voltar">
    <a href="admin.php">⬅️ Voltar ao Painel Admin</a>
  </div>
</body>
</html>
