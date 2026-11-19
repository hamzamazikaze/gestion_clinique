<?php
// Seul le médecin peut modifier une consultation
require_once __DIR__ . '/../includes/auth.php';
exiger_role(['medecin']);

$id = (int) ($_GET['id'] ?? $_POST['id'] ?? 0);

// Charger la consultation
$requete = $pdo->prepare('SELECT * FROM consultations WHERE id = ?');
$requete->execute([$id]);
$consultation = $requete->fetch();

if (!$consultation) {
    definir_message('erreur', 'Consultation introuvable.');
    rediriger('consultations/liste_consultations.php');
}

$erreurs = [];
$patient_id = (int) $consultation['patient_id'];
$date_consultation = $consultation['date_consultation'];
$motif = $consultation['motif'];
$diagnostic = $consultation['diagnostic'];
$traitement = $consultation['traitement'];

$patients = $pdo->query('SELECT id, nom, prenom FROM patients ORDER BY nom, prenom')->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifier_csrf();

    $patient_id = (int) ($_POST['patient_id'] ?? 0);
    $date_consultation = nettoyer($_POST['date_consultation'] ?? '');
    $motif = nettoyer($_POST['motif'] ?? '');
    $diagnostic = nettoyer($_POST['diagnostic'] ?? '');
    $traitement = nettoyer($_POST['traitement'] ?? '');

    $erreurs = valider_consultation($patient_id, $date_consultation, $motif);

    if (empty($erreurs)) {
        $requete = $pdo->prepare('SELECT id FROM patients WHERE id = ?');
        $requete->execute([$patient_id]);
        if (!$requete->fetch()) {
            $erreurs[] = 'Patient introuvable.';
        }
    }

    if (empty($erreurs)) {
        $requete = $pdo->prepare(
            'UPDATE consultations
             SET patient_id = ?, date_consultation = ?, motif = ?, diagnostic = ?, traitement = ?
             WHERE id = ?'
        );
        $requete->execute([$patient_id, $date_consultation, $motif, $diagnostic, $traitement, $id]);

        definir_message('succes', 'Consultation modifiée avec succès.');
        rediriger('consultations/liste_consultations.php');
    }
}

$titre_page = 'Modifier une consultation';
include __DIR__ . '/../includes/header.php';
?>

<h1>Modifier une consultation</h1>

<div class="carte carte-formulaire">
    <?php afficher_erreurs($erreurs); ?>
    <form method="post" action="">
        <?= champ_csrf() ?>
        <input type="hidden" name="id" value="<?= (int) $id ?>">
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
            <button type="submit" class="btn btn-principal">Enregistrer les modifications</button>
            <a class="btn btn-clair" href="<?= BASE_URL ?>consultations/liste_consultations.php">Annuler</a>
        </div>
    </form>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
