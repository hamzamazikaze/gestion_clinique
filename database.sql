-- Base de données du projet Gestion Clinique
-- Importer ce fichier dans phpMyAdmin (onglet "Importer")

CREATE DATABASE IF NOT EXISTS gestion_clinique
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE gestion_clinique;

-- On supprime les anciennes tables (ordre inverse des clés étrangères)
DROP TABLE IF EXISTS consultations;
DROP TABLE IF EXISTS patients;
DROP TABLE IF EXISTS utilisateurs;

-- Table des utilisateurs (assistante ou médecin)
CREATE TABLE utilisateurs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(50) NOT NULL,
    prenom VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    mot_de_passe VARCHAR(255) NOT NULL,
    role ENUM('assistante', 'medecin') NOT NULL DEFAULT 'assistante',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Table des patients
CREATE TABLE patients (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(50) NOT NULL,
    prenom VARCHAR(50) NOT NULL,
    date_naissance DATE NOT NULL,
    sexe ENUM('M', 'F') NOT NULL,
    telephone VARCHAR(15) NOT NULL,
    adresse VARCHAR(255) DEFAULT '',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Table des consultations (liée aux patients et aux médecins)
CREATE TABLE consultations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    patient_id INT NOT NULL,
    medecin_id INT NOT NULL,
    date_consultation DATE NOT NULL,
    motif VARCHAR(255) NOT NULL,
    diagnostic TEXT,
    traitement TEXT,
    FOREIGN KEY (patient_id) REFERENCES patients(id) ON DELETE RESTRICT ON UPDATE CASCADE,
    FOREIGN KEY (medecin_id) REFERENCES utilisateurs(id) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB;

-- Comptes de démonstration (mots de passe hachés avec bcrypt)
-- medecin@clinique.com    : Medecin123
-- assistante@clinique.com : Assistante123
INSERT INTO utilisateurs (nom, prenom, email, mot_de_passe, role) VALUES
('Benali', 'Karim', 'medecin@clinique.com', '$2y$12$hETH6RncylZ37xALJRfZ6.PSPjunBXmkfOJibvN4//ac/9j82pReu', 'medecin'),
('Alaoui', 'Sara', 'assistante@clinique.com', '$2y$12$EA07zp2YhA9e3Zx2GAQOU.wPJw5TQd35sLo0DMhvtqDXiqysyqx76', 'assistante');

-- Patients de test
INSERT INTO patients (nom, prenom, date_naissance, sexe, telephone, adresse) VALUES
('Idrissi', 'Youssef', '1990-04-12', 'M', '0612345678', '12 rue des Fleurs, Nador'),
('Tahiri', 'Amina', '1985-09-30', 'F', '0623456789', '5 avenue Hassan II, Nador'),
('Amrani', 'Omar', '2001-01-18', 'M', '0634567890', '8 rue Atlas, Nador'),
('Bennani', 'Lina', '1978-11-05', 'F', '0645678901', '20 boulevard Mohammed V, Nador');

-- Consultations de test
INSERT INTO consultations (patient_id, medecin_id, date_consultation, motif, diagnostic, traitement) VALUES
(1, 1, '2026-11-10', 'Fièvre et toux', 'Grippe saisonnière', 'Repos, paracétamol, boire beaucoup d''eau'),
(2, 1, '2026-11-12', 'Maux de tête', 'Migraine', 'Antalgique et repos'),
(3, 1, '2026-11-15', 'Douleur au dos', 'Lombalgie légère', 'Anti-inflammatoire pendant 5 jours');
