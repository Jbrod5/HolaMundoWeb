<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

// Conexión a la base de datos con PDO
$host = '127.0.0.1';
$db   = 'holamundo';
$user = 'root';
$pass = '';
$dsn  = "mysql:host=$host;dbname=$db;charset=utf8mb4";

$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (Exception $e) {
    die('Error de conexión: ' . $e->getMessage());
}

// Inicializar arrays de errores y mensajes
$errors = [];
$success = '';

// Registro
if (isset($_POST['action']) && $_POST['action'] === 'register') {
    $nombre = trim($_POST['nombre'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (!$nombre) $errors[] = 'El nombre es requerido.';
    if (!$email) $errors[] = 'El email es requerido.';
    if (!$password) $errors[] = 'La contraseña es requerida.';

    if (!$errors) {
        $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = :email");
        $stmt->execute([':email' => $email]);
        if ($stmt->fetch()) {
            $errors[] = 'El email ya está registrado.';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO usuarios (nombre, email, password) VALUES (:nombre, :email, :password)");
            $stmt->execute([':nombre'=>$nombre, ':email'=>$email, ':password'=>$hash]);
            $success = 'Usuario registrado correctamente. Ahora puedes iniciar sesión.';
        }
    }
}

// Login
if (isset($_POST['action']) && $_POST['action'] === 'login') {
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (!$email || !$password) {
        $errors[] = 'Email y contraseña son requeridos.';
    } else {
        $stmt = $pdo->prepare("SELECT id, nombre, password FROM usuarios WHERE email = :email");
        $stmt->execute([':email'=>$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['nombre'];
            header('Location: index.php');
            exit;
        } else {
            $errors[] = 'Email o contraseña incorrectos.';
        }
    }
}

// Comentario
if (isset($_POST['action']) && $_POST['action'] === 'comment') {
    if (!isset($_SESSION['user_id'])) {
        $errors[] = 'Debes iniciar sesión para comentar.';
    } else {
        $mensaje = trim($_POST['mensaje'] ?? '');
        if (!$mensaje) $errors[] = 'El mensaje no puede estar vacío.';
        if (!$errors) {
            $stmt = $pdo->prepare("INSERT INTO mensajes (usuario_id, mensaje) VALUES (:uid, :mensaje)");
            $stmt->execute([':uid'=>$_SESSION['user_id'], ':mensaje'=>$mensaje]);
            header('Location: index.php?saved=1');
            exit;
        }
    }
}

// Leer todos los comentarios
$stmt = $pdo->query("
    SELECT m.id, m.mensaje, m.creado_at, u.nombre 
    FROM mensajes m 
    LEFT JOIN usuarios u ON m.usuario_id = u.id
    ORDER BY m.creado_at DESC
");
$mensajes = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Hola Mundo Web - Login/Register</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
<main class="container">
  <h1>Hola Mundo Web!</h1>

  <?php if ($errors): ?>
    <div class="errors">
      <?php foreach ($errors as $e) echo "<div>- ".htmlspecialchars($e)."</div>"; ?>
    </div>
  <?php endif; ?>

  <?php if ($success): ?>
    <div class="success"><?=htmlspecialchars($success)?></div>
  <?php endif; ?>

  <?php if (!isset($_SESSION['user_id'])): ?>
    <!-- LOGIN -->
    <section class="card">
      <h2>Login</h2>
      <form method="post">
        <input type="hidden" name="action" value="login">
        <label>Email:
          <input type="email" name="email" required>
        </label>
        <label>Contraseña:
          <input type="password" name="password" required>
        </label>
        <button type="submit">Iniciar sesión</button>
      </form>
    </section>

    <!-- REGISTER -->
    <section class="card">
      <h2>Registro</h2>
      <form method="post">
        <input type="hidden" name="action" value="register">
        <label>Nombre:
          <input type="text" name="nombre" required>
        </label>
        <label>Email:
          <input type="email" name="email" required>
        </label>
        <label>Contraseña:
          <input type="password" name="password" required>
        </label>
        <button type="submit">Registrarse</button>
      </form>
    </section>
  <?php else: ?>
    <section class="card">
      <h2>Bienvenido, <?=htmlspecialchars($_SESSION['user_name'])?> <a href="logout.php">(Salir)</a></h2>
      <form method="post">
        <input type="hidden" name="action" value="comment">
        <label>Escribe tu comentario:
          <textarea name="mensaje" rows="4" required></textarea>
        </label>
        <button type="submit">Enviar comentario</button>
      </form>
    </section>
  <?php endif; ?>

  <!-- COMENTARIOS -->
  <section class="card">
    <h2>Comentarios que han realizado los usuarios:</h2>
    <?php if (empty($mensajes)): ?>
      <p>No hay comentarios aún.</p>
    <?php else: ?>
      <ul class="lista-mensajes">
        <?php foreach ($mensajes as $m): ?>
          <li>
            <div class="meta">
              <strong><?=htmlspecialchars($m['nombre'] ?? 'Anónimo')?></strong>
              <span class="fecha"><?=htmlspecialchars($m['creado_at'])?></span>
            </div>
            <div class="texto"><?=nl2br(htmlspecialchars($m['mensaje']))?></div>
          </li>
        <?php endforeach; ?>
      </ul>
    <?php endif; ?>
  </section>

</main>
</body>
</html>
