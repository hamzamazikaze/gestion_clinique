<?php
// Suppression d'un utilisateur : POST uniquement, réservé au médecin
require_once __DIR__ . '/../includes/auth.php';
exiger_role(['medecin']);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    rediriger('utilisateurs/liste_utilisateurs.php');
}
verifier_csrf();

$id = (int) ($_POST['id'] ?? 0);

// On ne peut pas supprimer son propre compte
if ($id === (int) $_SESSION['utilisateur_id']) {
    definir_message('erreur', 'Vous ne pouvez pas supprimer votre propre compte.');
    rediriger('utilisateurs/liste_utilisateurs.php');
}

try {
    $requete = $pdo->prepare('DELETE FROM utilisateurs WHERE id = ?');
    $requete->execute([$id]);

    if ($requete->rowCount() > 0) {
        definir_message('succes', 'Utilisateur supprimé.');
    } else {
        definir_message('erreur', 'Utilisateur introuvable.');
    }
} catch (PDOException $erreur) {
    // Clé étrangère : ce médecin a des consultations
    definir_message('erreur', 'Impossible de supprimer cet utilisateur : il a des consultations.');
}

rediriger('utilisateurs/liste_utilisateurs.php');
