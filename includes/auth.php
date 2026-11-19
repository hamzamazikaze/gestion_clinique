<?php
// Gestion de l'authentification et des rôles
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/functions.php';

function est_connecte()
{
    return isset($_SESSION['utilisateur_id']);
}

function role_actuel()
{
    return $_SESSION['role'] ?? '';
}

// Page réservée aux utilisateurs connectés
function exiger_connexion()
{
    if (!est_connecte()) {
        definir_message('erreur', 'Veuillez vous connecter.');
        rediriger('auth/connexion.php');
    }
}

// Page réservée à certains rôles, ex : exiger_role(['medecin'])
function exiger_role($roles)
{
    exiger_connexion();
    if (!in_array(role_actuel(), $roles, true)) {
        definir_message('erreur', "Vous n'avez pas l'autorisation d'accéder à cette page.");
        rediriger('accueil.php');
    }
}

// Page réservée aux visiteurs (inscription, connexion)
function exiger_visiteur()
{
    if (est_connecte()) {
        rediriger('accueil.php');
    }
}
