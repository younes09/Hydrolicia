<?php
// api/submit_study.php
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Méthode de requête non autorisée.']);
    exit;
}

require_once '../config/db.php';

$name = isset($_POST['name']) ? trim($_POST['name']) : '';
$organization = isset($_POST['organization']) ? trim($_POST['organization']) : '';
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$study_type = isset($_POST['study_type']) ? trim($_POST['study_type']) : '';
$description = isset($_POST['description']) ? trim($_POST['description']) : '';

if (empty($name) || empty($organization) || empty($email) || empty($study_type) || empty($description)) {
    echo json_encode(['success' => false, 'message' => 'Veuillez remplir tous les champs obligatoires.']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'L\'adresse e-mail saisie n\'est pas valide.']);
    exit;
}

try {
    $insert = $pdo->prepare("INSERT INTO `studies` (`name`, `organization`, `email`, `study_type`, `description`, `status`) VALUES (:name, :organization, :email, :study_type, :description, 'Reçu')");
    $insert->execute([
        'name' => $name,
        'organization' => $organization,
        'email' => $email,
        'study_type' => $study_type,
        'description' => $description
    ]);

    $lastId = $pdo->lastInsertId();

    echo json_encode([
        'success' => true,
        'message' => 'Votre demande d\'étude technique a été soumise avec succès ! Un ingénieur chef de projet va analyser votre besoin et vous recontactera sous 48 heures.',
        'study' => [
            'id' => $lastId,
            'name' => $name,
            'organization' => $organization,
            'email' => $email,
            'study_type' => $study_type,
            'description' => $description
        ]
    ]);

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Erreur de base de données : ' . $e->getMessage()]);
}
?>
