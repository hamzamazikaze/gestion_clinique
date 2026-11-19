<?php
// Assistante et médecin peuvent consulter les patients
require_once __DIR__ . '/../includes/auth.php';
exiger_role(['assistante', 'medecin']);

$patients = $pdo->query('SELECT * FROM patients ORDER BY nom, prenom')->fetchAll();
$peut_modifier = (role_actuel() === 'assistante');

$titre_page = 'Liste des patients';
include __DIR__ . '/../includes/header.php';
?>

<div class="entete-page">
    <h1>Patients</h1>
    <?php if ($peut_modifier) { ?>
        <a class="btn btn-principal" href="<?= BASE_URL ?>patients/ajouter_patient.php">+ Ajouter un patient</a>
    <?php } ?>
</div>

<div class="carte">
    <?php if (empty($patients)) { ?>
        <p class="vide">Aucun patient enregistré.</p>
    <?php } else { ?>
        <div class="tableau-conteneur">
            <table>
                <thead>
                    <tr>
                        <th>Nom</th><th>Prénom</th><th>Naissance</th><th>Sexe</th><th>Téléphone</th><th>Adresse</th>
                        <?php if ($peut_modifier) { ?><th>Actions</th><?php } ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($patients as $patient) { ?>
                        <tr>
                            <td><?= e($patient['nom']) ?></td>
                            <td><?= e($patient['prenom']) ?></td>
                            <td><?= e(afficher_date($patient['date_naissance'])) ?></td>
                            <td><?= e($patient['sexe']) ?></td>
                            <td><?= e($patient['telephone']) ?></td>
                            <td><?= e($patient['adresse']) ?></td>
                            <?php if ($peut_modifier) { ?>
                                <td class="actions">
                                    <a class="btn btn-petit btn-clair" href="<?= BASE_URL ?>patients/modifier_patient.php?id=<?= (int) $patient['id'] ?>">Modifier</a>
                                    <form method="post" action="<?= BASE_URL ?>patients/supprimer_patient.php" class="form-suppression" data-message="Supprimer ce patient ?">
                                        <?= champ_csrf() ?>
                                        <input type="hidden" name="id" value="<?= (int) $patient['id'] ?>">
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
