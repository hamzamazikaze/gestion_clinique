<?php
// Le médecin peut modifier un utilisateur
require_once __DIR__ . '/../includes/auth.php';
exiger_role(['medecin']);

$id = (int) ($_GET['id'] ?? $_POST['id'] ?? 0);

// Charger l'utilisateur
$requete = $pdo->prepare('SELECT * FROM utilisateurs WHERE id = ?');
$requete->execute([$id]);
$utilisateur = $requete->fetch();

if (!$utilisateur) {
    definir_message('erreur', 'Utilisateur introuvable.');
    rediriger('utilisateurs/liste_utilisateurs.php');
}

$erreurs = [];
$nom = $utilisateur['nom'];
$prenom = $utilisateur['prenom'];
$email = $utilisateur['email'];
$role = $utilisateur['role'];

// Un médecin ne peut pas changer son propre rôle (évite de se retirer ses droits)
$est_moi = ($id === (int) $_SESSION['utilisateur_id']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifier_csrf();

    $nom = nettoyer($_POST['nom'] ?? '');
    $prenom = nettoyer($_POST['prenom'] ?? '');
    $email = nettoyer($_POST['email'] ?? '');
    $role = $est_moi ? $utilisateur['role'] : nettoyer($_POST['role'] ?? '');
    $mot_de_passe = $_POST['mot_de_passe'] ?? '';

    $erreurs = valider_utilisateur($nom, $prenom, $email, $role);

    // Mot de passe facultatif : s'il est rempli, il doit être valide
    if ($mot_de_passe !== '' && strlen($mot_de_passe) < 6) {
        $erreurs[] = 'Le nouveau mot de passe doit contenir au moins 6 caractères.';
    }

    // Email unique (sauf pour cet utilisateur)
    if (empty($erreurs)) {
        $requete = $pdo->prepare('SELECT id FROM utilisateurs WHERE email = ? AND id <> ?');
        $requete->execute([$email, $id]);
        if ($requete->fetch()) {
            $erreurs[] = 'Cet email est déjà utilisé.';
        }
    }

    if (empty($erreurs)) {
        if ($mot_de_passe !== '') {
            $hash = password_hash($mot_de_passe, PASSWORD_DEFAULT);
            $requete = $pdo->prepare(
                'UPDATE utilisateurs SET nom = ?, prenom = ?, email = ?, role = ?, mot_de_passe = ? WHERE id = ?'
            );
            $requete->execute([$nom, $prenom, $email, $role, $hash, $id]);
        } else {
            $requete = $pdo->prepare(
                'UPDATE utilisateurs SET nom = ?, prenom = ?, email = ?, role = ? WHERE id = ?'
            );
            $requete->execute([$nom, $prenom, $email, $role, $id]);
        }

        // Mettre à jour le nom affiché si c'est le compte connecté
        if ($est_moi) {
            $_SESSION['nom'] = $nom;
            $_SESSION['prenom'] = $prenom;
        }

        definir_message('succes', 'Utilisateur modifié avec succès.');
        rediriger('utilisateurs/liste_utilisateurs.php');
    }
}

$titre_page = 'Modifier un utilisateur';
include __DIR__ . '/../includes/header.php';
?>

<h1>Modifier un utilisateur</h1>

<div class="carte carte-formulaire">
    <?php afficher_erreurs($erreurs); ?>
    <form method="post" action="">
        <?= champ_csrf() ?>
        <input type="hidden" name="id" value="<?= (int) $id ?>">
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
            <select id="role" name="role" <?= $est_moi ? 'disabled' : '' ?>>
                <option value="assistante" <?= $role === 'assistante' ? 'selected' : '' ?>>Assistante</option>
                <option value="medecin" <?= $role === 'medecin' ? 'selected' : '' ?>>Médecin</option>
            </select>
            <?php if ($est_moi) { ?>
                <small>Vous ne pouvez pas modifier votre propre rôle.</small>
            <?php } ?>
        </div>
        <div class="champ">
            <label for="mot_de_passe">Nouveau mot de passe</label>
            <input type="password" id="mot_de_passe" name="mot_de_passe" minlength="6">
            <small>Laisser vide pour garder l'ancien mot de passe.</small>
        </div>
        <div class="boutons">
            <button type="submit" class="btn btn-principal">Enregistrer les modifications</button>
            <a class="btn btn-clair" href="<?= BASE_URL ?>utilisateurs/liste_utilisateurs.php">Annuler</a>
        </div>
    </form>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
