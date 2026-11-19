<?php
// Tableau de bord (page d'accueil après connexion)
require_once __DIR__ . '/includes/auth.php';
exiger_connexion();

// Statistiques
$nb_patients = $pdo->query('SELECT COUNT(*) FROM patients')->fetchColumn();
$nb_consultations = $pdo->query('SELECT COUNT(*) FROM consultations')->fetchColumn();

$requete = $pdo->prepare('SELECT COUNT(*) FROM consultations WHERE date_consultation = ?');
$requete->execute([date('Y-m-d')]);
$nb_aujourdhui = $requete->fetchColumn();

$nb_utilisateurs = 0;
if (role_actuel() === 'medecin') {
    $nb_utilisateurs = $pdo->query('SELECT COUNT(*) FROM utilisateurs')->fetchColumn();
}

// 5 dernières consultations (JOIN avec patients et utilisateurs)
$derniere = $pdo->query(
    'SELECT c.date_consultation, c.motif,
            p.nom AS patient_nom, p.prenom AS patient_prenom,
            u.nom AS medecin_nom, u.prenom AS medecin_prenom
     FROM consultations c
     JOIN patients p ON c.patient_id = p.id
     JOIN utilisateurs u ON c.medecin_id = u.id
     ORDER BY c.date_consultation DESC, c.id DESC
     LIMIT 5'
)->fetchAll();

$titre_page = 'Tableau de bord';
include __DIR__ . '/includes/header.php';
?>

<h1>Bienvenue, <?= e($_SESSION['prenom']) ?></h1>

<div class="statistiques">
    <div class="stat">
        <span class="stat-nombre"><?= (int) $nb_patients ?></span>
        <span class="stat-texte">Patients</span>
    </div>
    <div class="stat">
        <span class="stat-nombre"><?= (int) $nb_consultations ?></span>
        <span class="stat-texte">Consultations</span>
    </div>
    <div class="stat">
        <span class="stat-nombre"><?= (int) $nb_aujourdhui ?></span>
        <span class="stat-texte">Consultations aujourd'hui</span>
    </div>
    <?php if (role_actuel() === 'medecin') { ?>
        <div class="stat">
            <span class="stat-nombre"><?= (int) $nb_utilisateurs ?></span>
            <span class="stat-texte">Utilisateurs</span>
        </div>
    <?php } ?>
</div>

<div class="carte">
    <h2>Dernières consultations</h2>
    <?php if (empty($derniere)) { ?>
        <p class="vide">Aucune consultation pour le moment.</p>
    <?php } else { ?>
        <div class="tableau-conteneur">
            <table>
                <thead>
                    <tr><th>Date</th><th>Patient</th><th>Médecin</th><th>Motif</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($derniere as $ligne) { ?>
                        <tr>
                            <td><?= e(afficher_date($ligne['date_consultation'])) ?></td>
                            <td><?= e($ligne['patient_prenom'] . ' ' . $ligne['patient_nom']) ?></td>
                            <td>Dr <?= e($ligne['medecin_prenom'] . ' ' . $ligne['medecin_nom']) ?></td>
                            <td><?= e($ligne['motif']) ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    <?php } ?>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
