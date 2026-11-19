<?php
// Gestion des utilisateurs : réservée au médecin
require_once __DIR__ . '/../includes/auth.php';
exiger_role(['medecin']);

$utilisateurs = $pdo->query('SELECT id, nom, prenom, email, role, created_at FROM utilisateurs ORDER BY nom, prenom')->fetchAll();

$titre_page = 'Liste des utilisateurs';
include __DIR__ . '/../includes/header.php';
?>

<div class="entete-page">
    <h1>Utilisateurs</h1>
    <a class="btn btn-principal" href="<?= BASE_URL ?>utilisateurs/ajouter_utilisateur.php">+ Ajouter un utilisateur</a>
</div>

<div class="carte">
    <div class="tableau-conteneur">
        <table>
            <thead>
                <tr><th>Nom</th><th>Prénom</th><th>Email</th><th>Rôle</th><th>Créé le</th><th>Actions</th></tr>
            </thead>
            <tbody>
                <?php foreach ($utilisateurs as $u) { ?>
                    <tr>
                        <td><?= e($u['nom']) ?></td>
                        <td><?= e($u['prenom']) ?></td>
                        <td><?= e($u['email']) ?></td>
                        <td><span class="badge badge-<?= e($u['role']) ?>"><?= e(ucfirst($u['role'])) ?></span></td>
                        <td><?= e(afficher_date($u['created_at'])) ?></td>
                        <td class="actions">
                            <a class="btn btn-petit btn-clair" href="<?= BASE_URL ?>utilisateurs/modifier_utilisateur.php?id=<?= (int) $u['id'] ?>">Modifier</a>
                            <?php if ((int) $u['id'] !== (int) $_SESSION['utilisateur_id']) { ?>
                                <form method="post" action="<?= BASE_URL ?>utilisateurs/supprimer_utilisateur.php" class="form-suppression" data-message="Supprimer cet utilisateur ?">
                                    <?= champ_csrf() ?>
                                    <input type="hidden" name="id" value="<?= (int) $u['id'] ?>">
                                    <button type="submit" class="btn btn-petit btn-danger">Supprimer</button>
                                </form>
                            <?php } ?>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
