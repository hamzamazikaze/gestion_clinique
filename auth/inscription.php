<?php
require_once __DIR__ . '/../includes/auth.php';
exiger_visiteur();

$erreurs = [];
$nom = '';
$prenom = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifier_csrf();

    $nom = nettoyer($_POST['nom'] ?? '');
    $prenom = nettoyer($_POST['prenom'] ?? '');
    $email = nettoyer($_POST['email'] ?? '');
    $mot_de_passe = $_POST['mot_de_passe'] ?? '';
    $confirmation = $_POST['confirmation'] ?? '';

    // Validation
    if ($nom === '' || $prenom === '') {
        $erreurs[] = 'Le nom et le prénom sont obligatoires.';
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erreurs[] = "L'adresse email n'est pas valide.";
    }
    if (strlen($mot_de_passe) < 6) {
        $erreurs[] = 'Le mot de passe doit contenir au moins 6 caractères.';
    }
    if ($mot_de_passe !== $confirmation) {
        $erreurs[] = 'Les deux mots de passe sont différents.';
    }

    // Email déjà utilisé ?
    if (empty($erreurs)) {
        $requete = $pdo->prepare('SELECT id FROM utilisateurs WHERE email = ?');
        $requete->execute([$email]);
        if ($requete->fetch()) {
            $erreurs[] = 'Cet email est déjà utilisé.';
        }
    }

    // Enregistrement : le rôle est toujours "assistante" (jamais médecin)
    if (empty($erreurs)) {
        $hash = password_hash($mot_de_passe, PASSWORD_DEFAULT);
        $requete = $pdo->prepare(
            "INSERT INTO utilisateurs (nom, prenom, email, mot_de_passe, role) VALUES (?, ?, ?, ?, 'assistante')"
        );
        $requete->execute([$nom, $prenom, $email, $hash]);

        definir_message('succes', 'Inscription réussie. Vous pouvez vous connecter.');
        rediriger('auth/connexion.php');
    }
}

$titre_page = 'Inscription';
include __DIR__ . '/../includes/header.php';
?>

<div class="carte-auth">
    <h1>Inscription</h1>
    <p class="sous-titre">Créer un compte assistante</p>

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
            <label for="mot_de_passe">Mot de passe</label>
            <input type="password" id="mot_de_passe" name="mot_de_passe" minlength="6" required>
        </div>
        <div class="champ">
            <label for="confirmation">Confirmer le mot de passe</label>
            <input type="password" id="confirmation" name="confirmation" minlength="6" required>
        </div>
        <button type="submit" class="btn btn-principal btn-large">S'inscrire</button>
    </form>

    <p class="lien-bas">Déjà un compte ? <a href="<?= BASE_URL ?>auth/connexion.php">Se connecter</a></p>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
