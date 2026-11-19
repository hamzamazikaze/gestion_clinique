<?php
// Le médecin peut créer des comptes (assistante ou médecin)
require_once __DIR__ . '/../includes/auth.php';
exiger_role(['medecin']);

$erreurs = [];
$nom = $prenom = $email = '';
$role = 'assistante';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifier_csrf();

    $nom = nettoyer($_POST['nom'] ?? '');
    $prenom = nettoyer($_POST['prenom'] ?? '');
    $email = nettoyer($_POST['email'] ?? '');
    $role = nettoyer($_POST['role'] ?? '');
    $mot_de_passe = $_POST['mot_de_passe'] ?? '';

    $erreurs = valider_utilisateur($nom, $prenom, $email, $role);
    if (strlen($mot_de_passe) < 6) {
        $erreurs[] = 'Le mot de passe doit contenir au moins 6 caractères.';
    }

    // Email unique
    if (empty($erreurs)) {
        $requete = $pdo->prepare('SELECT id FROM utilisateurs WHERE email = ?');
        $requete->execute([$email]);
        if ($requete->fetch()) {
            $erreurs[] = 'Cet email est déjà utilisé.';
        }
    }

    if (empty($erreurs)) {
        $hash = password_hash($mot_de_passe, PASSWORD_DEFAULT);
        $requete = $pdo->prepare(
            'INSERT INTO utilisateurs (nom, prenom, email, mot_de_passe, role) VALUES (?, ?, ?, ?, ?)'
        );
        $requete->execute([$nom, $prenom, $email, $hash, $role]);

        definir_message('succes', 'Utilisateur ajouté avec succès.');
        rediriger('utilisateurs/liste_utilisateurs.php');
    }
}

$titre_page = 'Ajouter un utilisateur';
include __DIR__ . '/../includes/header.php';
?>

<h1>Ajouter un utilisateur</h1>

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
            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="<?= e($email) ?>" maxlength="100" required>
        </div>
        <div class="champ">
            <label for="role">Rôle</label>
            <select id="role" name="role">
                <option value="assistante" <?= $role === 'assistante' ? 'selected' : '' ?>>Assistante</option>
                <option value="medecin" <?= $role === 'medecin' ? 'selected' : '' ?>>Médecin</option>
            </select>
        </div>
        <div class="champ">
            <label for="mot_de_passe">Mot de passe</label>
            <input type="password" id="mot_de_passe" name="mot_de_passe" minlength="6" required>
        </div>
        <div class="boutons">
            <button type="submit" class="btn btn-principal">Enregistrer</button>
            <a class="btn btn-clair" href="<?= BASE_URL ?>utilisateurs/liste_utilisateurs.php">Annuler</a>
        </div>
    </form>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
