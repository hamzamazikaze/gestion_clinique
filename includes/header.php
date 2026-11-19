<?php
// En-tête commun. La page peut définir $titre_page avant de l'inclure.
$titre_page = $titre_page ?? 'Gestion Clinique';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($titre_page) ?> - Clinique</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/style.css">
</head>
<body>
<?php if (est_connecte()) { ?>
    <?php include __DIR__ . '/navbar.php'; ?>
<?php } else { ?>
    <main class="page-auth">
<?php } ?>
        <?php afficher_message(); ?>
