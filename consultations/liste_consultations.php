<?php
// Assistante et médecin peuvent consulter ; seul le médecin modifie
require_once __DIR__ . '/../includes/auth.php';
exiger_role(['assistante', 'medecin']);

// JOIN : consultations + patients + utilisateurs (médecin)
$consultations = $pdo->query(
    'SELECT c.id, c.date_consultation, c.motif, c.diagnostic, c.traitement,
            p.nom AS patient_nom, p.prenom AS patient_prenom,
            u.nom AS medecin_nom, u.prenom AS medecin_prenom
     FROM consultations c
     JOIN patients p ON c.patient_id = p.id
     JOIN utilisateurs u ON c.medecin_id = u.id
     ORDER BY c.date_consultation DESC, c.id DESC'
)->fetchAll();

$peut_modifier = (role_actuel() === 'medecin');

$titre_page = 'Liste des consultations';
include __DIR__ . '/../includes/header.php';
?>

<div class="entete-page">
    <h1>Consultations</h1>
    <?php if ($peut_modifier) { ?>
        <a class="btn btn-principal" href="<?= BASE_URL ?>consultations/ajouter_consultation.php">+ Ajouter une consultation</a>
    <?php } ?>
</div>

<div class="carte">
    <?php if (empty($consultations)) { ?>
        <p class="vide">Aucune consultation enregistrée.</p>
    <?php } else { ?>
        <div class="tableau-conteneur">
            <table>
                <thead>
                    <tr>
                        <th>Date</th><th>Patient</th><th>Médecin</th><th>Motif</th><th>Diagnostic</th><th>Traitement</th>
                        <?php if ($peut_modifier) { ?><th>Actions</th><?php } ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($consultations as $c) { ?>
                        <tr>
                            <td><?= e(afficher_date($c['date_consultation'])) ?></td>
                            <td><?= e($c['patient_prenom'] . ' ' . $c['patient_nom']) ?></td>
                            <td>Dr <?= e($c['medecin_prenom'] . ' ' . $c['medecin_nom']) ?></td>
                            <td><?= e($c['motif']) ?></td>
                            <td><?= e($c['diagnostic']) ?></td>
                            <td><?= e($c['traitement']) ?></td>
                            <?php if ($peut_modifier) { ?>
                                <td class="actions">
                                    <a class="btn btn-petit btn-clair" href="<?= BASE_URL ?>consultations/modifier_consultation.php?id=<?= (int) $c['id'] ?>">Modifier</a>
                                    <form method="post" action="<?= BASE_URL ?>consultations/supprimer_consultation.php" class="form-suppression" data-message="Supprimer cette consultation ?">
                                        <?= champ_csrf() ?>
                                        <input type="hidden" name="id" value="<?= (int) $c['id'] ?>">
                                        <button type="submit" class="btn btn-petit btn-danger">Supprimer</button>
                                    </form>
                                </td>
                            <?php } ?>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    <?php } ?>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
