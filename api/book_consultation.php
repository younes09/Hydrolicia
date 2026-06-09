<?php
// api/book_consultation.php
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Méthode de requête non autorisée.']);
    exit;
}

require_once '../config/db.php';

$expert_name = isset($_POST['expert_name']) ? trim($_POST['expert_name']) : '';
$name        = isset($_POST['name'])        ? trim($_POST['name'])        : '';
$email       = isset($_POST['email'])       ? trim($_POST['email'])       : '';
$phone       = isset($_POST['phone'])       ? trim($_POST['phone'])       : '';
$topic       = isset($_POST['topic'])       ? trim($_POST['topic'])       : '';
$date        = isset($_POST['date'])        ? trim($_POST['date'])        : '';
$time        = isset($_POST['time'])        ? trim($_POST['time'])        : '';

if (empty($expert_name) || empty($name) || empty($email) || empty($phone) || empty($topic) || empty($date) || empty($time)) {
    echo json_encode(['success' => false, 'message' => 'Veuillez remplir tous les champs obligatoires.']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'L\'adresse e-mail saisie n\'est pas valide.']);
    exit;
}

// Basic phone validation: digits, spaces, +, -, at least 8 chars
if (!preg_match('/^[0-9+\s\-]{8,15}$/', $phone)) {
    echo json_encode(['success' => false, 'message' => 'Le numéro de mobile saisi n\'est pas valide (8 à 15 chiffres).']);
    exit;
}

try {
    // Check if slot is already taken for the expert at that date and time
    $check = $pdo->prepare("SELECT COUNT(*) FROM `consultations` WHERE `expert_name` = :expert_name AND `date` = :date AND `time` = :time");
    $check->execute([
        'expert_name' => $expert_name,
        'date'        => $date,
        'time'        => $time
    ]);
    
    if ($check->fetchColumn() > 0) {
        echo json_encode(['success' => false, 'message' => 'Ce créneau horaire est déjà réservé pour cet expert. Veuillez choisir un autre créneau ou un autre expert.']);
        exit;
    }

    // Insert booking
    $insert = $pdo->prepare("INSERT INTO `consultations` (`expert_name`, `name`, `email`, `phone`, `topic`, `date`, `time`, `status`) VALUES (:expert_name, :name, :email, :phone, :topic, :date, :time, 'En attente')");
    $insert->execute([
        'expert_name' => $expert_name,
        'name'        => $name,
        'email'       => $email,
        'phone'       => $phone,
        'topic'       => $topic,
        'date'        => $date,
        'time'        => $time
    ]);

    $lastId = $pdo->lastInsertId();

    echo json_encode([
        'success' => true,
        'message' => 'Votre consultation a été planifiée avec succès ! Un e-mail de confirmation contenant le lien de visioconférence vous a été envoyé.',
        'consultation' => [
            'id'          => $lastId,
            'name'        => $name,
            'email'       => $email,
            'phone'       => $phone,
            'expert_name' => $expert_name,
            'topic'       => $topic,
            'date'        => $date,
            'time'        => $time
        ]
    ]);

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Erreur de base de données : ' . $e->getMessage()]);
}
?>
