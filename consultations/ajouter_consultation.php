<?php
// Seul le médecin peut ajouter une consultation
require_once __DIR__ . '/../includes/auth.php';
exiger_role(['medecin']);

$erreurs = [];
$patient_id = 0;
$date_consultation = date('Y-m-d');
$motif = $diagnostic = $traitement = '';

// Liste des patients pour la liste déroulante
$patients = $pdo->query('SELECT id, nom, prenom FROM patients ORDER BY nom, prenom')->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifier_csrf();

    $patient_id = (int) ($_POST['patient_id'] ?? 0);
    $date_consultation = nettoyer($_POST['date_consultation'] ?? '');
    $motif = nettoyer($_POST['motif'] ?? '');
    $diagnostic = nettoyer($_POST['diagnostic'] ?? '');
    $traitement = nettoyer($_POST['traitement'] ?? '');

    $erreurs = valider_consultation($patient_id, $date_consultation, $motif);

    // Vérifier que le patient existe
    if (empty($erreurs)) {
        $requete = $pdo->prepare('SELECT id FROM patients WHERE id = ?');
        $requete->execute([$patient_id]);
        if (!$requete->fetch()) {
            $erreurs[] = 'Patient introuvable.';
        }
    }

    if (empty($erreurs)) {
        // Le médecin connecté est enregistré automatiquement
        $requete = $pdo->prepare(
            'INSERT INTO consultations (patient_id, medecin_id, date_consultation, motif, diagnostic, traitement)
             VALUES (?, ?, ?, ?, ?, ?)'
        );
        $requete->execute([$patient_id, $_SESSION['utilisateur_id'], $date_consultation, $motif, $diagnostic, $traitement]);

        definir_message('succes', 'Consultation ajoutée avec succès.');
        rediriger('consultations/liste_consultations.php');
    }
}

$titre_page = 'Ajouter une consultation';
include __DIR__ . '/../includes/header.php';
?>

<h1>Ajouter une consultation</h1>

<div class="carte carte-formulaire">
    <?php afficher_erreurs($erreurs); ?>
    <?php if (empty($patients)) { ?>
        <p class="vide">Aucun patient disponible. L'assistante doit d'abord ajouter un patient.</p>
    <?php } else { ?>
    <form method="post" action="">
        <?= champ_csrf() ?>
        <div class="champ">
            <label for="patient_id">Patient</label>
            <select id="patient_id" name="patient_id" required>
                <option value="">-- Choisir un patient --</option>
                <?php foreach ($patients as $p) { ?>
                    <option value="<?= (int) $p['id'] ?>" <?= $patient_id === (int) $p['id'] ? 'selected' : '' ?>>
                        <?= e($p['nom'] . ' ' . $p['prenom']) ?>
                    </option>
                <?php } ?>
            </select>
        </div>
        <div class="champ">
            <label for="date_consultation">Date de consultation</label>
            <input type="date" id="date_consultation" name="date_consultation" value="<?= e($date_consultation) ?>" required>
        </div>
        <div class="champ">
            <label for="motif">Motif</label>
            <input type="text" id="motif" name="motif" value="<?= e($motif) ?>" maxlength="255" required>
        </div>
        <div class="champ">
            <label for="diagnostic">Diagnostic</label>
            <textarea id="diagnostic" name="diagnostic" rows="3"><?= e($diagnostic) ?></textarea>
        </div>
        <div class="champ">
            <label for="traitement">Traitement</label>
            <textarea id="traitement" name="traitement" rows="3"><?= e($traitement) ?></textarea>
        </div>
        <div class="boutons">
            <button type="submit" class="btn btn-principal">Enregistrer</button>
            <a class="btn btn-clair" href="<?= BASE_URL ?>consultations/liste_consultations.php">Annuler</a>
        </div>
    </form>
    <?php } ?>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
