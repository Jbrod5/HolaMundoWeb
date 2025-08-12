<?php
// index.php
// Conexión a la base de datos con PDO
$host = '127.0.0.1';
$db   = 'holamundo';
$user = 'root';
$pass = ''; // si cambias la contraseña del root, cámbiala aquí
$dsn  = "mysql:host=$host;dbname=$db;charset=utf8mb4";

$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (Exception $e) {
    die('Error de conexión: ' . $e->getMessage());
}

// Procesamiento (entrada): guardar mensaje si viene por POST
$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // validación simple
    $nombre = trim($_POST['nombre'] ?? '');
    $mensaje = trim($_POST['mensaje'] ?? '');

    if ($nombre === '') $errors[] = 'El nombre es requerido.';
    if ($mensaje === '') $errors[] = 'El mensaje es requerido.';

    if (!$errors) {
        // Insert seguro con prepared statement
        $stmt = $pdo->prepare("INSERT INTO mensajes (nombre, mensaje) VALUES (:nombre, :mensaje)");
        $stmt->execute([':nombre' => $nombre, ':mensaje' => $mensaje]);
        $success = true;
        // opcional: redirigir para evitar reenvío al refrescar
        header('Location: index.php?saved=1');
        exit;
    }
}

// Salidas: leer los mensajes
$stmt = $pdo->query("SELECT id, nombre, mensaje, creado_at FROM mensajes ORDER BY creado_at DESC LIMIT 20");
$mensajes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Hola Mundo Web - Ejemplo</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <main class="container">
    <h1>Hola Mundo Web</h1>

    <section class="card">
      <h2>Entrada — Enviar mensaje</h2>

      <?php if (!empty($_GET['saved'])): ?>
        <div class="success">Mensaje guardado correctamente.</div>
      <?php endif; ?>

      <?php if ($errors): ?>
        <div class="errors">
          <?php foreach ($errors as $e) echo "<div>- ".htmlspecialchars($e)."</div>"; ?>
        </div>
      <?php endif; ?>

      <form id="formMensaje" method="post" action="index.php" novalidate>
        <label>Nombre:
          <input type="text" name="nombre" id="nombre" maxlength="100" required>
        </label>

        <label>Mensaje:
          <textarea name="mensaje" id="mensaje" rows="4" required></textarea>
        </label>

        <button type="submit">Enviar</button>
      </form>
    </section>

    <section class="card">
      <h2>Salida — Mensajes recientes</h2>
      <?php if (empty($mensajes)): ?>
        <p>No hay mensajes aún.</p>
      <?php else: ?>
        <ul class="lista-mensajes">
          <?php foreach ($mensajes as $m): ?>
            <li>
              <div class="meta">
                <strong><?=htmlspecialchars($m['nombre'])?></strong>
                <span class="fecha"><?=htmlspecialchars($m['creado_at'])?></span>
              </div>
              <div class="texto"><?=nl2br(htmlspecialchars($m['mensaje']))?></div>
            </li>
          <?php endforeach; ?>
        </ul>
      <?php endif; ?>

      <p><a href="report.php">Ver reporte</a></p>
    </section>
  </main>

  <script src="script.js"></script>
</body>
</html>
