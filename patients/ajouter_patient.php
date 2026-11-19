<?php
// Seule l'assistante peut ajouter un patient
require_once __DIR__ . '/../includes/auth.php';
exiger_role(['assistante']);

$erreurs = [];
$nom = $prenom = $date_naissance = $telephone = $adresse = '';
$sexe = 'M';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifier_csrf();

    $nom = nettoyer($_POST['nom'] ?? '');
    $prenom = nettoyer($_POST['prenom'] ?? '');
    $date_naissance = nettoyer($_POST['date_naissance'] ?? '');
    $sexe = nettoyer($_POST['sexe'] ?? '');
    $telephone = nettoyer($_POST['telephone'] ?? '');
    $adresse = nettoyer($_POST['adresse'] ?? '');

    $erreurs = valider_patient($nom, $prenom, $date_naissance, $sexe, $telephone);

    if (empty($erreurs)) {
        $requete = $pdo->prepare(
            'INSERT INTO patients (nom, prenom, date_naissance, sexe, telephone, adresse) VALUES (?, ?, ?, ?, ?, ?)'
        );
        $requete->execute([$nom, $prenom, $date_naissance, $sexe, $telephone, $adresse]);

        definir_message('succes', 'Patient ajouté avec succès.');
        rediriger('patients/liste_patients.php');
    }
}

$titre_page = 'Ajouter un patient';
include __DIR__ . '/../includes/header.php';
?>

<h1>Ajouter un patient</h1>

<div class="carte carte-formulaire">
    <?php afficher_erreurs($erreurs); ?>
    <form method="post" action="">
        <?= champ_csrf() ?>
        <div class="champ">
            <label for="nom">Nom</label>
            <input type="text" id="nom" name="nom" value="<?= e($nom) ?>" maxlength="50" required>
        </div>
        <div class="champ">
            <label for="prenom">Prénom</label>
            <input type="text" id="prenom" name="prenom" value="<?= e($prenom) ?>" maxlength="50" required>
        </div>
        <div class="champ">
            <label for="date_naissance">Date de naissance</label>
            <input type="date" id="date_naissance" name="date_naissance" value="<?= e($date_naissance) ?>" max="<?= date('Y-m-d') ?>" required>
        </div>
        <div class="champ">
            <label for="sexe">Sexe</label>
            <select id="sexe" name="sexe">
                <option value="M" <?= $sexe === 'M' ? 'selected' : '' ?>>Masculin</option>
                <option value="F" <?= $sexe === 'F' ? 'selected' : '' ?>>Féminin</option>
            </select>
        </div>
        <div class="champ">
            <label for="telephone">Téléphone</label>
            <input type="text" id="telephone" name="telephone" value="<?= e($telephone) ?>" maxlength="15" required>
        </div>
        <div class="champ">
            <label for="adresse">Adresse</label>
            <textarea id="adresse" name="adresse" rows="3" maxlength="255"><?= e($adresse) ?></textarea>
        </div>
        <div class="boutons">
            <button type="submit" class="btn btn-principal">Enregistrer</button>
            <a class="btn btn-clair" href="<?= BASE_URL ?>patients/liste_patients.php">Annuler</a>
        </div>
    </form>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
