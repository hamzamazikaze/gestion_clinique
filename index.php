<?php
// Point d'entrée : redirige selon l'état de connexion
require_once __DIR__ . '/includes/auth.php';

if (est_connecte()) {
    rediriger('accueil.php');
}
rediriger('auth/connexion.php');
