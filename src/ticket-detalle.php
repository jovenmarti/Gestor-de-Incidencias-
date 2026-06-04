<?php
require_once 'config/session.php';
require_once 'config/db.php';
requireLogin();

$usuario = usuarioActual();
$id = (int)($_GET['id'] ?? 0);

if (!$id) {
    header('Location: /tickets.php');
    exit;
}

$stmt = $pdo->prepare("
    SELECT t.*,
           u.nombre AS creado_por_nombre,
           a.nombre AS asignado_a_nombre
    FROM tickets t
    JOIN usuarios u ON t.creado_por = u.id
    LEFT JOIN usuarios a ON t.asignado_a = a.id
    WHERE t.id = :id
");
$stmt->execute([':id' => $id]);
$ticket = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$ticket) {
    http_response_code(404);
    die('<h1>Ticket no encontrado</h1>');
}

$historial = $pdo->prepare("
    SELECT h.*, u.nombre AS usuario_nombre
    FROM historial h
    JOIN usuarios u ON h.usuario_id = u.id
    WHERE h.ticket_id = :id
    ORDER BY h.fecha DESC
");
$historial->execute([':id' => $id]);
$cambios = $historial->fetchAll(PDO::FETCH_ASSOC);

$tecnicos = $pdo->query("SELECT id, nombre FROM usuarios WHERE rol IN ('tecnico','admin') ORDER BY nombre")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ticket #<?= $id ?> — Gestor de Incidencias</title>
    <link rel="stylesheet" href="/css/main.css">
</head>
<body>
<?php include 'partials/nav.php'; ?>

<div class="container">
    <div class="page-header">
        <div>
            <h1>Ticket #<?= $id ?> <span class="badge estado-<?= $ticket['estado'] ?>"><?= $ticket['estado'] ?></span></h1>
            <p class="sub"><?= htmlspecialchars($ticket['titulo']) ?></p>
        </div>
        <a href="/tickets.php" class="link">← Volver</a>
    </div>

    <div class="detail-grid">
        <div class="detail-main">
            <div class="form-card">
                <p style="font-size:0.82rem; color:#71717a; margin-bottom:0.5rem;">Descripción</p>
                <p><?= nl2br(htmlspecialchars($ticket['descripcion'])) ?></p>

                <div class="meta-row">
                    <span><strong>Prioridad:</strong> <span class="badge prioridad-<?= $ticket['prioridad'] ?>"><?= $ticket['prioridad'] ?></span></span>
                    <span><strong>Creado por:</strong> <?= htmlspecialchars($ticket['creado_por_nombre']) ?></span>
                    <span><strong>Asignado a:</strong> <?= $ticket['asignado_a_nombre'] ? htmlspecialchars($ticket['asignado_a_nombre']) : '—' ?></span>
                    <span><strong>Fecha:</strong> <?= date('d/m/Y H:i', strtotime($ticket['creado_en'])) ?></span>
                </div>
            </div>

            <?php if (!empty($cambios)): ?>
            <h2 style="font-size:1rem; font-weight:600; margin: 1.5rem 0 0.75rem;">Historial de cambios</h2>
            <div class="historial">
                <?php foreach ($cambios as $c): ?>
                <div class="historial-item">
                    <span class="historial-usuario"><?= htmlspecialchars($c['usuario_nombre']) ?></span>
                    cambió <strong><?= $c['campo_modificado'] ?></strong>
                    de <span class="badge"><?= $c['valor_anterior'] ?></span>
                    a <span class="badge"><?= $c['valor_nuevo'] ?></span>
                    <span class="historial-fecha"><?= date('d/m/Y H:i', strtotime($c['fecha'])) ?></span>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>

        <?php if (in_array($usuario['rol'], ['tecnico', 'admin'])): ?>
        <div class="detail-side">
            <form method="POST" action="/ticket-actualizar.php" class="form-card">
                <input type="hidden" name="id" value="<?= $id ?>">

                <label for="estado">Estado</label>
                <select id="estado" name="estado">
                    <option value="abierto"  <?= $ticket['estado'] === 'abierto'   ? 'selected' : '' ?>>Abierto</option>
                    <option value="en_curso" <?= $ticket['estado'] === 'en_curso'  ? 'selected' : '' ?>>En curso</option>
                    <option value="cerrado"  <?= $ticket['estado'] === 'cerrado'   ? 'selected' : '' ?>>Cerrado</option>
                </select>

                <label for="prioridad">Prioridad</label>
                <select id="prioridad" name="prioridad">
                    <option value="baja"    <?= $ticket['prioridad'] === 'baja'    ? 'selected' : '' ?>>Baja</option>
                    <option value="media"   <?= $ticket['prioridad'] === 'media'   ? 'selected' : '' ?>>Media</option>
                    <option value="alta"    <?= $ticket['prioridad'] === 'alta'    ? 'selected' : '' ?>>Alta</option>
                    <option value="critica" <?= $ticket['prioridad'] === 'critica' ? 'selected' : '' ?>>Crítica</option>
                </select>

                <label for="asignado_a">Asignar a</label>
                <select id="asignado_a" name="asignado_a">
                    <option value="">— Sin asignar —</option>
                    <?php foreach ($tecnicos as $t): ?>
                    <option value="<?= $t['id'] ?>" <?= $ticket['asignado_a'] == $t['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($t['nombre']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>

                <button type="submit">Guardar cambios</button>
            </form>
        </div>
        <?php endif; ?>
    </div>
</div>
</body>
</html>