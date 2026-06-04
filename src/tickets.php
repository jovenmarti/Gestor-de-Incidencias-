<?php
require_once 'config/session.php';
require_once 'config/db.php';
requireLogin();

$usuario = usuarioActual();

if ($usuario['rol'] === 'usuario') {
    $stmt = $pdo->prepare("
        SELECT t.*, 
               u.nombre AS creado_por_nombre,
               a.nombre AS asignado_a_nombre
        FROM tickets t
        JOIN usuarios u ON t.creado_por = u.id
        LEFT JOIN usuarios a ON t.asignado_a = a.id
        WHERE t.creado_por = :id
        ORDER BY t.creado_en DESC
    ");
    $stmt->execute([':id' => $usuario['id']]);
} else {
    $stmt = $pdo->query("
        SELECT t.*, 
               u.nombre AS creado_por_nombre,
               a.nombre AS asignado_a_nombre
        FROM tickets t
        JOIN usuarios u ON t.creado_por = u.id
        LEFT JOIN usuarios a ON t.asignado_a = a.id
        ORDER BY t.creado_en DESC
    ");
}

$tickets = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Tickets — Gestor de Incidencias</title>
    <link rel="stylesheet" href="/css/main.css">
</head>
<body>
<?php include 'partials/nav.php'; ?>

<div class="container">
    <div class="page-header">
        <div>
            <h1>Tickets</h1>
            <p class="sub">Listado de incidencias</p>
        </div>
        <a href="/ticket-nuevo.php" class="btn">+ Nuevo ticket</a>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>#</th>
                <th>Título</th>
                <th>Estado</th>
                <th>Prioridad</th>
                <th>Creado por</th>
                <th>Asignado a</th>
                <th>Fecha</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
        <?php if (empty($tickets)): ?>
            <tr><td colspan="8" style="text-align:center; color:#71717a; padding:2rem;">No hay tickets todavía</td></tr>
        <?php else: ?>
            <?php foreach ($tickets as $t): ?>
            <tr>
                <td style="color:#71717a;">#<?= $t['id'] ?></td>
                <td><?= htmlspecialchars($t['titulo']) ?></td>
                <td><span class="badge estado-<?= $t['estado'] ?>"><?= $t['estado'] ?></span></td>
                <td><span class="badge prioridad-<?= $t['prioridad'] ?>"><?= $t['prioridad'] ?></span></td>
                <td><?= htmlspecialchars($t['creado_por_nombre']) ?></td>
                <td><?= $t['asignado_a_nombre'] ? htmlspecialchars($t['asignado_a_nombre']) : '<span style="color:#a1a1aa;">—</span>' ?></td>
                <td style="color:#71717a; font-size:0.85rem;"><?= date('d/m/Y', strtotime($t['creado_en'])) ?></td>
                <td><a href="/ticket-detalle.php?id=<?= $t['id'] ?>" class="link">Ver</a></td>
            </tr>
            <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
    </table>
</div>
</body>
</html>