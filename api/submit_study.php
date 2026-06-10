<?php
// api/submit_study.php
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Méthode de requête non autorisée.']);
    exit;
}

require_once '../config/db.php';

$name         = isset($_POST['name'])         ? trim($_POST['name'])         : '';
$organization = isset($_POST['organization']) ? trim($_POST['organization']) : '';
$email        = isset($_POST['email'])        ? trim($_POST['email'])        : '';
$phone        = isset($_POST['phone'])        ? trim($_POST['phone'])        : '';
$study_type   = isset($_POST['study_type'])   ? trim($_POST['study_type'])   : '';
$description  = isset($_POST['description'])  ? trim($_POST['description'])  : '';

if (empty($name) || empty($organization) || empty($email) || empty($phone) || empty($study_type) || empty($description)) {
    echo json_encode(['success' => false, 'message' => 'Veuillez remplir tous les champs obligatoires.']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'L\'adresse e-mail saisie n\'est pas valide.']);
    exit;
}

if (!preg_match('/^[0-9+\s\-]{8,15}$/', $phone)) {
    echo json_encode(['success' => false, 'message' => 'Le numéro de téléphone saisi n\'est pas valide (8 à 15 chiffres).']);
    exit;
}

$file_path = null;

if (isset($_FILES['project_file']) && $_FILES['project_file']['error'] !== UPLOAD_ERR_NO_FILE) {
    $file = $_FILES['project_file'];
    
    // Check errors
    if ($file['error'] !== UPLOAD_ERR_OK) {
        echo json_encode(['success' => false, 'message' => 'Erreur lors du transfert du fichier.']);
        exit;
    }
    
    // Check file size (10 MB limit)
    $max_size = 10 * 1024 * 1024;
    if ($file['size'] > $max_size) {
        echo json_encode(['success' => false, 'message' => 'Le fichier est trop volumineux (maximum 10 Mo).']);
        exit;
    }
    
    // Check allowed extensions
    $allowed_exts = ['pdf', 'doc', 'docx', 'zip', 'rar', 'dwg', 'inp', 'net'];
    $filename = $file['name'];
    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    
    if (!in_array($ext, $allowed_exts)) {
        echo json_encode(['success' => false, 'message' => 'Format de fichier non autorisé.']);
        exit;
    }
    
    // Create destination directory if not exists
    $upload_dir = '../uploads/studies/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }
    
    // Clean filename and make unique
    $clean_name = preg_replace('/[^a-zA-Z0-9_-]/', '_', pathinfo($filename, PATHINFO_FILENAME));
    $new_filename = uniqid('study_', true) . '_' . $clean_name . '.' . $ext;
    $dest_path = $upload_dir . $new_filename;
    
    if (!move_uploaded_file($file['tmp_name'], $dest_path)) {
        echo json_encode(['success' => false, 'message' => 'Échec de la sauvegarde du fichier sur le serveur.']);
        exit;
    }
    
    $file_path = $new_filename;
}

try {
    $insert = $pdo->prepare("INSERT INTO `studies` (`name`, `organization`, `email`, `phone`, `study_type`, `description`, `file_path`, `status`) VALUES (:name, :organization, :email, :phone, :study_type, :description, :file_path, 'Reçu')");
    $insert->execute([
        'name'         => $name,
        'organization' => $organization,
        'email'        => $email,
        'phone'        => $phone,
        'study_type'   => $study_type,
        'description'  => $description,
        'file_path'    => $file_path
    ]);

    $lastId = $pdo->lastInsertId();

    echo json_encode([
        'success' => true,
        'message' => 'Votre demande d\'étude technique a été soumise avec succès ! Un ingénieur chef de projet va analyser votre besoin et vous recontactera sous 48 heures.',
        'study' => [
            'id'           => $lastId,
            'name'         => $name,
            'organization' => $organization,
            'email'        => $email,
            'phone'        => $phone,
            'study_type'   => $study_type,
            'description'  => $description,
            'file_path'    => $file_path
        ]
    ]);

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Erreur de base de données : ' . $e->getMessage()]);
}
?>
