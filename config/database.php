<?php
// Configuration de la base de données (XAMPP par défaut)
$hote = 'localhost';
$base = 'gestion_clinique';
$utilisateur = 'root';
$mot_de_passe_bd = '';

// Dossier du projet dans htdocs (à changer si vous renommez le dossier)
define('BASE_URL', '/gestion_clinique/');

// Démarrer la session une seule fois
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Connexion PDO
try {
    $pdo = new PDO(
        "mysql:host=$hote;dbname=$base;charset=utf8mb4",
        $utilisateur,
        $mot_de_passe_bd,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );
} catch (PDOException $erreur) {
    die('Erreur de connexion à la base de données. Vérifiez que MySQL est démarré et que database.sql est importé.');
}
