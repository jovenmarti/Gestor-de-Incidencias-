<?php
session_start();

function requireLogin() {
    if (!isset($_SESSION['usuario_id'])) {
        header('Location: /login.php');
        exit;
    }
}

function requireRol(array $roles) {
    requireLogin();
    if (!in_array($_SESSION['rol'], $roles)) {
        http_response_code(403);
        die('<h1>403 — No tienes permiso para ver esta página</h1>');
    }
}

function usuarioActual(): array {
    return [
        'id'     => $_SESSION['usuario_id'],
        'nombre' => $_SESSION['nombre'],
        'rol'    => $_SESSION['rol'],
    ];
}