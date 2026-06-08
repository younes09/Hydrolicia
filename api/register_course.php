<?php
// api/register_course.php
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Méthode de requête non autorisée.']);
    exit;
}

require_once '../config/db.php';

$name = isset($_POST['name']) ? trim($_POST['name']) : '';
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$course_id = isset($_POST['course_id']) ? trim($_POST['course_id']) : '';
$phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';

if (empty($name) || empty($email) || empty($course_id) || empty($phone)) {
    echo json_encode(['success' => false, 'message' => 'Veuillez remplir tous les champs obligatoires.']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'L\'adresse e-mail saisie n\'est pas valide.']);
    exit;
}

try {
    // Check if the user is already registered for this course
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM `registrations` WHERE `email` = :email AND `course_id` = :course_id");
    $stmt->execute(['email' => $email, 'course_id' => $course_id]);
    
    if ($stmt->fetchColumn() > 0) {
        echo json_encode(['success' => false, 'message' => 'Vous êtes déjà inscrit à cette formation avec cette adresse e-mail.']);
        exit;
    }

    // Insert registration
    $insert = $pdo->prepare("INSERT INTO `registrations` (`name`, `email`, `course_id`, `phone`) VALUES (:name, :email, :course_id, :phone)");
    $insert->execute([
        'name' => $name,
        'email' => $email,
        'course_id' => $course_id,
        'phone' => $phone
    ]);

    echo json_encode([
        'success' => true, 
        'message' => 'Félicitations ' . htmlspecialchars($name) . '! Votre inscription a été enregistrée avec succès. Un conseiller vous contactera sous peu.'
    ]);

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Erreur de base de données lors de l\'inscription: ' . $e->getMessage()]);
}
?>
