<?php
require_once 'config/session.php';
require_once 'config/db.php';
requireLogin();

$usuario = usuarioActual();

$stats = $pdo->query("
    SELECT
        COUNT(*) FILTER (WHERE estado = 'abierto')  AS abiertos,
        COUNT(*) FILTER (WHERE estado = 'en_curso') AS en_curso,
        COUNT(*) FILTER (WHERE estado = 'cerrado')  AS cerrados,
        COUNT(*)                                     AS total
    FROM tickets
")->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Dashboard — Gestor de Incidencias</title>
    <link rel="stylesheet" href="/css/main.css">
</head>
<body>
<?php include 'partials/nav.php'; ?>

<div class="container">
    <div class="page-header">
        <div>
            <h1>Dashboard</h1>
            <p class="sub">Resumen del estado actual de las incidencias</p>
        </div>
        <a href="/ticket-nuevo.php" class="btn">+ Nuevo ticket</a>
    </div>

    <div class="stats">
        <div class="stat abierto">
            <div class="num"><?= $stats['abiertos'] ?></div>
            <div class="label">Tickets abiertos</div>
        </div>
        <div class="stat en-curso">
            <div class="num"><?= $stats['en_curso'] ?></div>
            <div class="label">En curso</div>
        </div>
        <div class="stat cerrado">
            <div class="num"><?= $stats['cerrados'] ?></div>
            <div class="label">Cerrados</div>
        </div>
        <div class="stat">
            <div class="num"><?= $stats['total'] ?></div>
            <div class="label">Total</div>
        </div>
    </div>

    <div style="margin-top: 1rem;">
        <a href="/tickets.php" class="link">Ver todos los tickets →</a>
    </div>
</div>
</body>
</html>