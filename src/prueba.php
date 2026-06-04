<?php
require_once 'config/db.php';

$result = $pdo->query("SELECT COUNT(*) as total FROM usuarios");
$row = $result->fetch();
?>
<!DOCTYPE html>
<html>
<head><title>Gestor de Incidencias</title></head>
<body>
    <h1>Gestor de Incidencias IT</h1>
    <p>Conexion a base de datos: OK</p>
    <p>Usuarios en BD: <?= $row['total'] ?></p>
</body>
</html>