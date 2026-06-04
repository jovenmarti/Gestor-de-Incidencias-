<?php $usuario = usuarioActual(); ?>
<nav>
    <div style="display:flex; gap:1.5rem; align-items:center;">
        <strong>Gestor de Incidencias</strong>
        <a href="/dashboard.php" class="nav-link">Dashboard</a>
        <a href="/tickets.php" class="nav-link">Tickets</a>
    </div>
    <span>
        <?= htmlspecialchars($usuario['nombre']) ?>
        <span class="badge <?= $usuario['rol'] ?>"><?= $usuario['rol'] ?></span>
        &nbsp;·&nbsp;
        <a href="/logout.php">Cerrar sesión</a>
    </span>
</nav>