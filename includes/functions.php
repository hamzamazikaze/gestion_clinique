<?php
// Fonctions utiles pour tout le projet

// Protéger l'affichage (contre XSS)
function e($texte)
{
    return htmlspecialchars((string) $texte, ENT_QUOTES, 'UTF-8');
}

// Rediriger vers une page du projet
function rediriger($page)
{
    header('Location: ' . BASE_URL . $page);
    exit;
}

// ----- CSRF -----
function creer_token()
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// Champ caché à mettre dans chaque formulaire
function champ_csrf()
{
    return '<input type="hidden" name="csrf_token" value="' . creer_token() . '">';
}

// Vérifier le token reçu en POST (arrête le script si faux)
function verifier_csrf()
{
    $recu = $_POST['csrf_token'] ?? '';
    $attendu = $_SESSION['csrf_token'] ?? '';
    if ($attendu === '' || !hash_equals($attendu, $recu)) {
        http_response_code(403);
        die('Requête invalide (jeton CSRF incorrect).');
    }
}

// ----- Messages (succès / erreur) -----
function definir_message($type, $texte)
{
    $_SESSION['message'] = ['type' => $type, 'texte' => $texte];
}

function afficher_message()
{
    if (isset($_SESSION['message'])) {
        $type = $_SESSION['message']['type'];
        $texte = $_SESSION['message']['texte'];
        unset($_SESSION['message']);
        echo '<div class="message message-' . e($type) . '">' . e($texte) . '</div>';
    }
}

// ----- Validation -----
function nettoyer($valeur)
{
    return trim((string) $valeur);
}

function date_valide($date)
{
    $d = DateTime::createFromFormat('Y-m-d', $date);
    return $d && $d->format('Y-m-d') === $date;
}

function telephone_valide($tel)
{
    return preg_match('/^[0-9 +]{8,15}$/', $tel) === 1;
}

function afficher_date($date)
{
    return date('d/m/Y', strtotime($date));
}

// Afficher la liste des erreurs d'un formulaire
function afficher_erreurs($erreurs)
{
    if (!empty($erreurs)) {
        echo '<div class="message message-erreur"><ul>';
        foreach ($erreurs as $erreur) {
            echo '<li>' . e($erreur) . '</li>';
        }
        echo '</ul></div>';
    }
}

// ----- Validation des formulaires (retournent un tableau d'erreurs) -----

function valider_patient($nom, $prenom, $date_naissance, $sexe, $telephone)
{
    $erreurs = [];
    if ($nom === '' || $prenom === '') {
        $erreurs[] = 'Le nom et le prénom sont obligatoires.';
    }
    if (!date_valide($date_naissance)) {
        $erreurs[] = 'La date de naissance est invalide.';
    } elseif ($date_naissance > date('Y-m-d')) {
        $erreurs[] = 'La date de naissance ne peut pas être dans le futur.';
    }
    if ($sexe !== 'M' && $sexe !== 'F') {
        $erreurs[] = 'Le sexe est invalide.';
    }
    if (!telephone_valide($telephone)) {
        $erreurs[] = 'Le téléphone est invalide (8 à 15 chiffres, espaces ou +).';
    }
    return $erreurs;
}

function valider_consultation($patient_id, $date_consultation, $motif)
{
    $erreurs = [];
    if ($patient_id <= 0) {
        $erreurs[] = 'Veuillez choisir un patient.';
    }
    if (!date_valide($date_consultation)) {
        $erreurs[] = 'La date de consultation est invalide.';
    }
    if ($motif === '') {
        $erreurs[] = 'Le motif est obligatoire.';
    }
    return $erreurs;
}

function valider_utilisateur($nom, $prenom, $email, $role)
{
    $erreurs = [];
    if ($nom === '' || $prenom === '') {
        $erreurs[] = 'Le nom et le prénom sont obligatoires.';
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erreurs[] = "L'adresse email n'est pas valide.";
    }
    if ($role !== 'assistante' && $role !== 'medecin') {
        $erreurs[] = 'Le rôle est invalide.';
    }
    return $erreurs;
}
