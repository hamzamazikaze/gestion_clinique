<?php
// Sidebar (menu à gauche) + navbar (barre en haut)
// Le menu change selon le rôle de l'utilisateur
$role = role_actuel();
$page_courante = basename($_SERVER['PHP_SELF']);
$dossier_courant = basename(dirname($_SERVER['PHP_SELF']));

// Marquer le lien actif : accueil.php ou un dossier (patients, ...)
function lien_actif($nom, $page_courante, $dossier_courant)
{
    return ($nom === $page_courante || $nom === $dossier_courant) ? 'actif' : '';
}
?>
<div class="layout">
    <aside class="sidebar" id="sidebar">
        <div class="logo">
            <span class="logo-icone">+</span>
            <span>Clinique</span>
        </div>
        <nav>
            <a class="<?= lien_actif('accueil.php', $page_courante, $dossier_courant) ?>" href="<?= BASE_URL ?>accueil.php">Tableau de bord</a>

            <a class="<?= lien_actif('patients', $page_courante, $dossier_courant) ?>" href="<?= BASE_URL ?>patients/liste_patients.php">Patients</a>
            <?php if ($role === 'assistante') { ?>
                <a href="<?= BASE_URL ?>patients/ajouter_patient.php">+ Ajouter un patient</a>
            <?php } ?>

            <a class="<?= lien_actif('consultations', $page_courante, $dossier_courant) ?>" href="<?= BASE_URL ?>consultations/liste_consultations.php">Consultations</a>
            <?php if ($role === 'medecin') { ?>
                <a href="<?= BASE_URL ?>consultations/ajouter_consultation.php">+ Ajouter une consultation</a>

                <a class="<?= lien_actif('utilisateurs', $page_courante, $dossier_courant) ?>" href="<?= BASE_URL ?>utilisateurs/liste_utilisateurs.php">Utilisateurs</a>
                <a href="<?= BASE_URL ?>utilisateurs/ajouter_utilisateur.php">+ Ajouter un utilisateur</a>
            <?php } ?>
        </nav>
    </aside>

    <div class="principal">
        <header class="topbar">
            <button type="button" class="btn-menu" id="btn-menu">&#9776;</button>
            <div class="topbar-titre"><?= e($titre_page) ?></div>
            <div class="topbar-user">
                <span><?= e($_SESSION['prenom'] . ' ' . $_SESSION['nom']) ?></span>
                <span class="badge badge-<?= e($role) ?>"><?= e(ucfirst($role)) ?></span>
                <a class="btn btn-clair" href="<?= BASE_URL ?>auth/deconnexion.php">Déconnexion</a>
            </div>
        </header>
        <main class="contenu">
