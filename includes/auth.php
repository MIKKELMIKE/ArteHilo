<?php
require_once __DIR__ . '/db.php';

function isLoggedIn() {
    return isset($_SESSION['usuario_id']) && !empty($_SESSION['usuario_id']);
}

function isAdmin() {
    return isLoggedIn() && isset($_SESSION['rol']) && $_SESSION['rol'] === 'admin';
}

function getUserId() {
    return $_SESSION['usuario_id'] ?? null;
}

function getUserName() {
    return $_SESSION['nombre'] ?? '';
}

function getUserEmail() {
    return $_SESSION['email'] ?? '';
}

function requireLogin($mensaje = 'Debes iniciar sesión para continuar') {
    if (!isLoggedIn()) {
        $_SESSION['mensaje_error'] = $mensaje;
        $_SESSION['redirect_after_login'] = $_GET['page'] ?? 'inicio';
        if (!headers_sent()) {
            header('Location: index.php?page=login');
            exit;
        }
    }
}

function loginUser($usuario) {
    if (!headers_sent()) {
        session_regenerate_id(true);
    }
    $_SESSION['usuario_id'] = $usuario['id'];
    $_SESSION['nombre'] = $usuario['nombre'];
    $_SESSION['email'] = $usuario['email'];
    $_SESSION['rol'] = $usuario['rol'];
    $_SESSION['login_time'] = time();
}

function logoutUser() {
    $_SESSION = array();
    if (isset($_COOKIE[session_name()])) {
        setcookie(session_name(), '', time() - 3600, '/');
    }
    session_destroy();
    session_start();
    $_SESSION['mensaje_exito'] = 'Sesión cerrada correctamente';
}

function getRedirectAfterLogin() {
    $redirect = $_SESSION['redirect_after_login'] ?? 'inicio';
    unset($_SESSION['redirect_after_login']);
    return $redirect;
}

function getFullRedirectURL() {
    return "index.php?page=" . urlencode(getRedirectAfterLogin());
}
