<?php
require_once __DIR__ . '/../includes/auth.php';

// Vider la session et supprimer le cookie de session
$_SESSION = [];
if (ini_get('session.use_cookies')) {
    $p = session_get_cookie_params();
    setcookie(session_name(), '', time() - 3600, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
}
session_destroy();

// Nouvelle session pour afficher le message de départ
session_start();
definir_message('succes', 'Vous êtes déconnecté.');
rediriger('auth/connexion.php');
