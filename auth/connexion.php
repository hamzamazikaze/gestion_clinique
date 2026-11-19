<?php
require_once __DIR__ . '/../includes/auth.php';
exiger_visiteur();

$erreurs = [];
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifier_csrf();

    $email = nettoyer($_POST['email'] ?? '');
    $mot_de_passe = $_POST['mot_de_passe'] ?? '';

    if ($email === '' || $mot_de_passe === '') {
        $erreurs[] = 'Veuillez remplir tous les champs.';
    } else {
        // Chercher l'utilisateur par email
        $requete = $pdo->prepare('SELECT * FROM utilisateurs WHERE email = ?');
        $requete->execute([$email]);
        $utilisateur = $requete->fetch();

        if ($utilisateur && password_verify($mot_de_passe, $utilisateur['mot_de_passe'])) {
            // Nouvelle session pour plus de sécurité
            session_regenerate_id(true);
            $_SESSION['utilisateur_id'] = $utilisateur['id'];
            $_SESSION['nom'] = $utilisateur['nom'];
            $_SESSION['prenom'] = $utilisateur['prenom'];
            $_SESSION['role'] = $utilisateur['role'];
            rediriger('accueil.php');
        } else {
            $erreurs[] = 'Email ou mot de passe incorrect.';
        }
    }
}

$titre_page = 'Connexion';
include __DIR__ . '/../includes/header.php';
?>

<div class="carte-auth">
    <h1>Connexion</h1>
    <p class="sous-titre">Clinique - Espace du personnel</p>

    <?php afficher_erreurs($erreurs); ?>

    <form method="post" action="">
        <?= champ_csrf() ?>
        <div class="champ">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="<?= e($email) ?>" required>
        </div>
        <div class="champ">
            <label for="mot_de_passe">Mot de passe</label>
            <input type="password" id="mot_de_passe" name="mot_de_passe" required>
        </div>
        <button type="submit" class="btn btn-principal btn-large">Se connecter</button>
    </form>

    <p class="lien-bas">Pas encore de compte ? <a href="<?= BASE_URL ?>auth/inscription.php">S'inscrire</a></p>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
