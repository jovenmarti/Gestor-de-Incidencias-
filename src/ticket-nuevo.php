<?php
require_once 'config/session.php';
require_once 'config/db.php';
requireLogin();

$usuario = usuarioActual();
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo      = trim($_POST['titulo'] ?? '');
    $descripcion = trim($_POST['descripcion'] ?? '');
    $prioridad   = $_POST['prioridad'] ?? 'media';

    if (!$titulo || !$descripcion) {
        $error = 'El título y la descripción son obligatorios.';
    } else {
        $stmt = $pdo->prepare("
            INSERT INTO tickets (titulo, descripcion, prioridad, creado_por)
            VALUES (:titulo, :descripcion, :prioridad, :creado_por)
        ");
        $stmt->execute([
            ':titulo'      => $titulo,
            ':descripcion' => $descripcion,
            ':prioridad'   => $prioridad,
            ':creado_por'  => $usuario['id'],
        ]);
        header('Location: /tickets.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Nuevo ticket — Gestor de Incidencias</title>
    <link rel="stylesheet" href="/css/main.css">
</head>
<body>
<?php include 'partials/nav.php'; ?>

<div class="container" style="max-width: 600px;">
    <div class="page-header">
        <div>
            <h1>Nuevo ticket</h1>
            <p class="sub">Describe la incidencia</p>
        </div>
        <a href="/tickets.php" class="link">← Volver</a>
    </div>

    <?php if ($error): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" class="form-card">
        <label for="titulo">Título</label>
        <input type="text" id="titulo" name="titulo" placeholder="Ej: El ordenador no arranca" required>

        <label for="descripcion">Descripción</label>
        <textarea id="descripcion" name="descripcion" rows="5" placeholder="Describe el problema con el máximo detalle posible..." required></textarea>

        <label for="prioridad">Prioridad</label>
        <select id="prioridad" name="prioridad">
            <option value="baja">Baja</option>
            <option value="media" selected>Media</option>
            <option value="alta">Alta</option>
            <option value="critica">Crítica</option>
        </select>

        <button type="submit">Crear ticket</button>
    </form>
</div>
</body>
</html>