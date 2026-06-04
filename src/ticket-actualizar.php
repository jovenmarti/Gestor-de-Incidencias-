<?php
require_once 'config/session.php';
require_once 'config/db.php';
requireLogin();
requireRol(['tecnico', 'admin']);

$usuario = usuarioActual();
$id = (int)($_POST['id'] ?? 0);

if (!$id) {
    header('Location: /tickets.php');
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM tickets WHERE id = :id");
$stmt->execute([':id' => $id]);
$ticket = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$ticket) {
    header('Location: /tickets.php');
    exit;
}

$nuevo_estado     = $_POST['estado']      ?? $ticket['estado'];
$nueva_prioridad  = $_POST['prioridad']   ?? $ticket['prioridad'];
$nuevo_asignado   = $_POST['asignado_a']  !== '' ? (int)$_POST['asignado_a'] : null;

$campos = [
    'estado'      => [$ticket['estado'],      $nuevo_estado],
    'prioridad'   => [$ticket['prioridad'],   $nueva_prioridad],
    'asignado_a'  => [$ticket['asignado_a'],  $nuevo_asignado],
];

$insert_historial = $pdo->prepare("
    INSERT INTO historial (ticket_id, usuario_id, campo_modificado, valor_anterior, valor_nuevo)
    VALUES (:ticket_id, :usuario_id, :campo, :anterior, :nuevo)
");

foreach ($campos as $campo => [$anterior, $nuevo]) {
    if ((string)$anterior !== (string)$nuevo) {
        $insert_historial->execute([
            ':ticket_id'  => $id,
            ':usuario_id' => $usuario['id'],
            ':campo'      => $campo,
            ':anterior'   => $anterior ?? '—',
            ':nuevo'      => $nuevo    ?? '—',
        ]);
    }
}

$update = $pdo->prepare("
    UPDATE tickets
    SET estado = :estado, prioridad = :prioridad, asignado_a = :asignado_a
    WHERE id = :id
");
$update->execute([
    ':estado'      => $nuevo_estado,
    ':prioridad'   => $nueva_prioridad,
    ':asignado_a'  => $nuevo_asignado,
    ':id'          => $id,
]);

header("Location: /ticket-detalle.php?id=$id");
exit;