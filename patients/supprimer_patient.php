<?php
// Suppression d'un patient : POST uniquement, réservé à l'assistante
require_once __DIR__ . '/../includes/auth.php';
exiger_role(['assistante']);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    rediriger('patients/liste_patients.php');
}
verifier_csrf();

$id = (int) ($_POST['id'] ?? 0);

try {
    $requete = $pdo->prepare('DELETE FROM patients WHERE id = ?');
    $requete->execute([$id]);

    if ($requete->rowCount() > 0) {
        definir_message('succes', 'Patient supprimé.');
    } else {
        definir_message('erreur', 'Patient introuvable.');
    }
} catch (PDOException $erreur) {
    // Clé étrangère : le patient a des consultations
    definir_message('erreur', 'Impossible de supprimer ce patient : il a des consultations.');
}

rediriger('patients/liste_patients.php');
