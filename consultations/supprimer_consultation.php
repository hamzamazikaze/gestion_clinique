<?php
// Suppression d'une consultation : POST uniquement, réservé au médecin
require_once __DIR__ . '/../includes/auth.php';
exiger_role(['medecin']);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    rediriger('consultations/liste_consultations.php');
}
verifier_csrf();

$id = (int) ($_POST['id'] ?? 0);

$requete = $pdo->prepare('DELETE FROM consultations WHERE id = ?');
$requete->execute([$id]);

if ($requete->rowCount() > 0) {
    definir_message('succes', 'Consultation supprimée.');
} else {
    definir_message('erreur', 'Consultation introuvable.');
}

rediriger('consultations/liste_consultations.php');
