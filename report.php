<?php
// report.php
$host = '127.0.0.1';
$db   = 'holamundo';
$user = 'root';
$pass = '';
$dsn  = "mysql:host=$host;dbname=$db;charset=utf8mb4";
$options = [ PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION ];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (Exception $e) {
    die('Error de conexión: ' . $e->getMessage());
}

// Reportes: 1) total mensajes 2) mensajes por día (simple)
$total_stmt = $pdo->query("SELECT COUNT(*) AS total FROM mensajes");
$total = $total_stmt->fetch(PDO::FETCH_ASSOC)['total'];

// últimos 5 mensajes
$last_stmt = $pdo->query("SELECT nombre, mensaje, creado_at FROM mensajes ORDER BY creado_at DESC LIMIT 5");
$ultimos = $last_stmt->fetchAll(PDO::FETCH_ASSOC);

// mensajes por fecha
$bydate_stmt = $pdo->query("SELECT DATE(creado_at) as dia, COUNT(*) as cnt FROM mensajes GROUP BY dia ORDER BY dia DESC LIMIT 10");
$por_dia = $bydate_stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Reporte - Hola Mundo Web</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <main class="container">
    <h1>Reporte</h1>
    <section class="card">
      <h2>Resumen</h2>
      <p>Total de mensajes guardados: <strong><?=htmlspecialchars($total)?></strong></p>
    </section>

    <section class="card">
      <h2>Últimos 5 mensajes</h2>
      <?php if ($ultimos): ?>
        <ul class="lista-mensajes">
          <?php foreach ($ultimos as $m): ?>
            <li>
              <div class="meta"><strong><?=htmlspecialchars($m['nombre'])?></strong>
                <span class="fecha"><?=htmlspecialchars($m['creado_at'])?></span>
              </div>
              <div class="texto"><?=nl2br(htmlspecialchars($m['mensaje']))?></div>
            </li>
          <?php endforeach; ?>
        </ul>
      <?php else: ?>
        <p>No hay mensajes aún.</p>
      <?php endif; ?>
    </section>

    <section class="card">
      <h2>Mensajes por día (últimos 10 días con actividad)</h2>
      <?php if ($por_dia): ?>
        <table>
          <tr><th>Fecha</th><th>Cantidad</th></tr>
          <?php foreach ($por_dia as $r): ?>
            <tr><td><?=htmlspecialchars($r['dia'])?></td><td><?=htmlspecialchars($r['cnt'])?></td></tr>
          <?php endforeach; ?>
        </table>
      <?php else: ?>
        <p>No hay datos.</p>
      <?php endif; ?>
    </section>

    <p><a href="index.php">Volver</a></p>
  </main>
</body>
</html>
