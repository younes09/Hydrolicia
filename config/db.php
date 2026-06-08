<?php
// config/db.php

$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'hydrolicia';

try {
    // 1. First connect to MySQL server without selecting db to create it if it doesn't exist
    $pdo = new PDO("mysql:host=$host", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Create database if not exists
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbname` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    
    // 2. Re-connect to the created database
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    // 3. Create tables if they do not exist
    
    // Registrations table
    $pdo->exec("CREATE TABLE IF NOT EXISTS `registrations` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `name` VARCHAR(255) NOT NULL,
        `email` VARCHAR(255) NOT NULL,
        `course_id` VARCHAR(50) NOT NULL,
        `phone` VARCHAR(50) DEFAULT NULL,
        `status` VARCHAR(50) DEFAULT 'En attente',
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB;");

    // Consultations table
    $pdo->exec("CREATE TABLE IF NOT EXISTS `consultations` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `name` VARCHAR(255) NOT NULL,
        `email` VARCHAR(255) NOT NULL,
        `expert_name` VARCHAR(100) NOT NULL,
        `date` DATE NOT NULL,
        `time` TIME NOT NULL,
        `topic` VARCHAR(255) NOT NULL,
        `status` VARCHAR(50) DEFAULT 'En attente',
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB;");

    // Studies table
    $pdo->exec("CREATE TABLE IF NOT EXISTS `studies` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `name` VARCHAR(255) NOT NULL,
        `organization` VARCHAR(255) NOT NULL,
        `email` VARCHAR(255) NOT NULL,
        `study_type` VARCHAR(100) NOT NULL,
        `description` TEXT NOT NULL,
        `status` VARCHAR(50) DEFAULT 'Reçu',
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB;");

    // Forum Questions table
    $pdo->exec("CREATE TABLE IF NOT EXISTS `forum_questions` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `title` VARCHAR(255) NOT NULL,
        `author` VARCHAR(255) NOT NULL,
        `role` VARCHAR(50) NOT NULL, -- 'Etudiant', 'Professionnel', 'Expert'
        `category` VARCHAR(100) NOT NULL,
        `content` TEXT NOT NULL,
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB;");

    // Forum Replies table
    $pdo->exec("CREATE TABLE IF NOT EXISTS `forum_replies` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `question_id` INT NOT NULL,
        `author` VARCHAR(255) NOT NULL,
        `role` VARCHAR(50) NOT NULL,
        `content` TEXT NOT NULL,
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (`question_id`) REFERENCES `forum_questions`(`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB;");

    // Admins table
    $pdo->exec("CREATE TABLE IF NOT EXISTS `admins` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `username` VARCHAR(100) NOT NULL UNIQUE,
        `password_hash` VARCHAR(255) NOT NULL,
        `email` VARCHAR(255) NOT NULL,
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB;");

    // Migration: Add status column to registrations if it does not exist
    try {
        $checkStatus = $pdo->query("SHOW COLUMNS FROM `registrations` LIKE 'status'");
        if ($checkStatus->rowCount() == 0) {
            $pdo->exec("ALTER TABLE `registrations` ADD `status` VARCHAR(50) DEFAULT 'En attente' AFTER `course_id`");
        }
    } catch (Exception $e) {
        // Ignore
    }

    // Migration: Add phone column to registrations if it does not exist
    try {
        $checkPhone = $pdo->query("SHOW COLUMNS FROM `registrations` LIKE 'phone'");
        if ($checkPhone->rowCount() == 0) {
            $pdo->exec("ALTER TABLE `registrations` ADD `phone` VARCHAR(50) DEFAULT NULL AFTER `email`");
        }
    } catch (Exception $e) {
        // Ignore
    }

    // Trainings table
    $pdo->exec("CREATE TABLE IF NOT EXISTS `trainings` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `code` VARCHAR(50) NOT NULL UNIQUE,
        `title` VARCHAR(255) NOT NULL,
        `duration` VARCHAR(100) NOT NULL,
        `description` TEXT NOT NULL,
        `price` VARCHAR(100) NOT NULL,
        `badge` VARCHAR(100) DEFAULT NULL,
        `badge_style` VARCHAR(100) DEFAULT 'bg-primary',
        `program` TEXT NOT NULL,
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB;");

    // Experts table
    $pdo->exec("CREATE TABLE IF NOT EXISTS `experts` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `name` VARCHAR(255) NOT NULL UNIQUE,
        `specialty` VARCHAR(255) NOT NULL,
        `bio` TEXT NOT NULL,
        `avatar_class` VARCHAR(100) DEFAULT 'bi-person-fill-gear',
        `avatar_color_class` VARCHAR(100) DEFAULT 'primary',
        `status` VARCHAR(50) DEFAULT 'Disponible',
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB;");

    // Seed trainings if empty
    $stmtTrainings = $pdo->query("SELECT COUNT(*) FROM `trainings`");
    if ($stmtTrainings->fetchColumn() == 0) {
        $insertTraining = $pdo->prepare("INSERT INTO `trainings` (`code`, `title`, `duration`, `description`, `price`, `badge`, `badge_style`, `program`) VALUES (:code, :title, :duration, :description, :price, :badge, :badge_style, :program)");
        
        $defaultTrainings = [
            [
                'code' => 'aep',
                'title' => 'Alimentation en Eau Potable (EPANET)',
                'duration' => '24 Heures',
                'description' => 'Conception, calage et dimensionnement des réseaux de distribution sous pression.',
                'price' => '15 000 DA',
                'badge' => 'Modélisation AEP',
                'badge_style' => 'bg-primary',
                'program' => "Calcul des débits de pointe & dimensionnement des conduites\nCalcul des réservoirs de stockage & stations de pompage\nModélisation dynamique et calage des pressions sous EPANET\nAnalyse des transitoires hydrauliques (coup de bélier)"
            ],
            [
                'code' => 'assainissement',
                'title' => "Réseaux d'Assainissement (SewerGEMS)",
                'duration' => '30 Heures',
                'description' => 'Dimensionnement des réseaux de collecte des eaux usées et pluviales urbaines.',
                'price' => '18 000 DA',
                'badge' => 'Assainissement & Drainage',
                'badge_style' => 'bg-teal',
                'program' => "Calcul des débits d'eaux usées domestiques (normes algériennes)\nÉvaluation des débits d'orage par la méthode rationnelle\nTracé en plan et profil en long (Covadis / Mensura)\nModélisation hydraulique gravitaire sous SewerGEMS"
            ],
            [
                'code' => 'irrigation',
                'title' => "Irrigation & Économie d'Eau (CROPWAT)",
                'duration' => '20 Heures',
                'description' => "Dimensionnement des réseaux d'irrigation et gestion optimale des ressources en eau.",
                'price' => '12 000 DA',
                'badge' => 'Irrigation & Écologie',
                'badge_style' => 'bg-success',
                'program' => "Calcul des besoins en eau des cultures sous CROPWAT\nConception de systèmes goutte-à-goutte et aspersion\nCalcul hydraulique des conduites de distribution\nRéutilisation des eaux épurées pour l'agriculture (REUSE)"
            ],
            [
                'code' => 'hecras',
                'title' => "Hydraulique des Cours d'Eau (HEC-RAS)",
                'duration' => '28 Heures',
                'description' => "Modélisation des écoulements à surface libre et délimitation des zones inondables.",
                'price' => '20 000 DA',
                'badge' => 'Ouvrages & Inondations',
                'badge_style' => 'bg-warning text-dark',
                'program' => "Modélisation hydraulique 1D et 2D permanente/non permanente\nÉtudes d'inondation & protection des agglomérations\nDimensionnement des digues, seuils et canaux d'évacuation\nÉtude de rupture de barrages (scénarios de sécurité)"
            ]
        ];
        
        foreach ($defaultTrainings as $t) {
            $insertTraining->execute($t);
        }
    }

    // Seed experts if empty
    $stmtExperts = $pdo->query("SELECT COUNT(*) FROM `experts`");
    if ($stmtExperts->fetchColumn() == 0) {
        $insertExpert = $pdo->prepare("INSERT INTO `experts` (`name`, `specialty`, `bio`, `avatar_class`, `avatar_color_class`, `status`) VALUES (:name, :specialty, :bio, :avatar_class, :avatar_color_class, :status)");
        
        $defaultExperts = [
            [
                'name' => 'Dr. Salim Rahal',
                'specialty' => 'Modélisation AEP, Réseaux sous pression & EPANET',
                'bio' => "Plus de 15 ans d'expérience dans la conception d'infrastructures hydrauliques majeures en Algérie. Expert en diagnostic de réseaux d'eau potable et maîtrise des fuites.",
                'avatar_class' => 'bi-person-fill-gear',
                'avatar_color_class' => 'primary',
                'status' => 'Disponible'
            ],
            [
                'name' => 'Ing. Karima Ould-Kadi',
                'specialty' => 'Hydrologie, Crues & Simulation HEC-RAS',
                'bio' => "Spécialiste de la protection des agglomérations contre les risques d'inondations et de l'aménagement des cours d'eau (oueds). Conception de digues et de bassins d'orage.",
                'avatar_class' => 'bi-person-fill-lock',
                'avatar_color_class' => 'warning',
                'status' => 'Disponible'
            ],
            [
                'name' => 'Ing. Mourad Benyahia',
                'specialty' => 'Assainissement, Traitement (STEP) & Réutilisation des eaux',
                'bio' => "Concepteur de stations d'épuration avec intégration d'éco-technologies pour l'agriculture. Expert en étude d'impact environnemental des eaux résiduaires.",
                'avatar_class' => 'bi-person-fill-check',
                'avatar_color_class' => 'success',
                'status' => 'Disponible'
            ]
        ];
        
        foreach ($defaultExperts as $e) {
            $insertExpert->execute($e);
        }
    }

    // Seed admin if empty
    $stmtAdmin = $pdo->query("SELECT COUNT(*) FROM `admins`");
    if ($stmtAdmin->fetchColumn() == 0) {
        $username = 'admin';
        $email = 'admin@hydrolicia.dz';
        $passHash = password_hash('AdminHydro2026!', PASSWORD_BCRYPT);
        $insertAdmin = $pdo->prepare("INSERT INTO `admins` (`username`, `password_hash`, `email`) VALUES (:username, :password, :email)");
        $insertAdmin->execute([
            'username' => $username,
            'password' => $passHash,
            'email' => $email
        ]);
    }

    // Add some sample questions to the forum if it is empty
    $stmt = $pdo->query("SELECT COUNT(*) FROM `forum_questions`");
    if ($stmt->fetchColumn() == 0) {
        $questions = [
            [
                'title' => 'Dimensionnement d\'une conduite de refoulement AEP',
                'author' => 'Amina Bendjamil',
                'role' => 'Étudiante',
                'category' => 'AEP (Alimentation en Eau Potable)',
                'content' => 'Bonjour, pour dimensionner une conduite de refoulement en AEP entre une station de pompage et un réservoir, quelle est la vitesse économique généralement recommandée en Algérie ? Y a-t-il des limites de pression à respecter pour le choix de la classe PN ? Merci.'
            ],
            [
                'title' => 'Réutilisation des eaux usées épurées en agriculture',
                'author' => 'Yacine Meziani',
                'role' => 'Professionnel',
                'category' => 'Assainissement & Environnement',
                'content' => 'Bonjour, j\'étudie la faisabilité de l\'irrigation d\'une zone arboricole à partir des eaux épurées de la station STEP de Boumerdès. Quels sont les paramètres biologiques réglementaires stricts à surveiller en Algérie (norme NA 17099) avant distribution ?'
            ]
        ];
        
        $insertQ = $pdo->prepare("INSERT INTO `forum_questions` (`title`, `author`, `role`, `category`, `content`) VALUES (:title, :author, :role, :category, :content)");
        
        foreach ($questions as $q) {
            $insertQ->execute($q);
        }
        
        // Add sample reply to first question
        $qId = $pdo->lastInsertId() - 1; // ID of the first question
        $pdo->exec("INSERT INTO `forum_replies` (`question_id`, `author`, `role`, `content`) VALUES (
            $qId,
            'Dr. Salim Rahal',
            'Expert Hydraulique',
            'Bonjour Amina. En Algérie, la vitesse économique standard est comprise entre 0.8 m/s et 1.2 m/s. En dessous de 0.5 m/s, il y a risque de sédimentation. Au-delà de 1.5 m/s, les pertes de charge et les risques de coup de bélier augmentent fortement. Pour la classe PN, il faut calculer la pression statique max, ajouter les pertes de charge et la surpression due au coup de bélier (souvent estimée à 25-50% en pré-dimensionnement), puis prendre une marge de sécurité de 20%.'
        )");
    }

} catch (PDOException $e) {
    // Return friendly error page or handle it globally
    die("<div style='padding: 20px; font-family: sans-serif; background: #fff5f5; border: 1px solid #ffc9c9; color: #cc0000; border-radius: 5px; margin: 20px;'>
            <h3>Erreur de connexion à la base de données</h3>
            <p>Impossible de se connecter au serveur MySQL (localhost). Veuillez vérifier que <strong>XAMPP Control Panel</strong> est ouvert et que le service <strong>MySQL</strong> est actif.</p>
            <p>Détail de l'erreur : <code>" . htmlspecialchars($e->getMessage()) . "</code></p>
         </div>");
}
?>
