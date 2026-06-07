<?php
// api/forum.php
header('Content-Type: application/json');

require_once '../config/db.php';

$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($action === 'ask') {
        $title = isset($_POST['title']) ? trim($_POST['title']) : '';
        $author = isset($_POST['author']) ? trim($_POST['author']) : '';
        $role = isset($_POST['role']) ? trim($_POST['role']) : '';
        $category = isset($_POST['category']) ? trim($_POST['category']) : '';
        $content = isset($_POST['content']) ? trim($_POST['content']) : '';

        if (empty($title) || empty($author) || empty($role) || empty($category) || empty($content)) {
            echo json_encode(['success' => false, 'message' => 'Veuillez remplir tous les champs obligatoires.']);
            exit;
        }

        try {
            $stmt = $pdo->prepare("INSERT INTO `forum_questions` (`title`, `author`, `role`, `category`, `content`) VALUES (:title, :author, :role, :category, :content)");
            $stmt->execute([
                'title' => $title,
                'author' => $author,
                'role' => $role,
                'category' => $category,
                'content' => $content
            ]);
            
            $qId = $pdo->lastInsertId();

            echo json_encode([
                'success' => true,
                'message' => 'Votre question a été publiée !',
                'question' => [
                    'id' => $qId,
                    'title' => $title,
                    'author' => $author,
                    'role' => $role,
                    'category' => $category,
                    'content' => $content,
                    'created_at' => date('Y-m-d H:i:s')
                ]
            ]);
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => 'Erreur de base de données : ' . $e->getMessage()]);
        }
        exit;
    }
    
    if ($action === 'reply') {
        $question_id = isset($_POST['question_id']) ? intval($_POST['question_id']) : 0;
        $author = isset($_POST['author']) ? trim($_POST['author']) : '';
        $role = isset($_POST['role']) ? trim($_POST['role']) : '';
        $content = isset($_POST['content']) ? trim($_POST['content']) : '';

        if (empty($question_id) || empty($author) || empty($role) || empty($content)) {
            echo json_encode(['success' => false, 'message' => 'Veuillez remplir tous les champs obligatoires.']);
            exit;
        }

        try {
            $stmt = $pdo->prepare("INSERT INTO `forum_replies` (`question_id`, `author`, `role`, `content`) VALUES (:question_id, :author, :role, :content)");
            $stmt->execute([
                'question_id' => $question_id,
                'author' => $author,
                'role' => $role,
                'content' => $content
            ]);

            $rId = $pdo->lastInsertId();

            echo json_encode([
                'success' => true,
                'message' => 'Votre réponse a été ajoutée !',
                'reply' => [
                    'id' => $rId,
                    'question_id' => $question_id,
                    'author' => $author,
                    'role' => $role,
                    'content' => $content,
                    'created_at' => date('Y-m-d H:i:s')
                ]
            ]);
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => 'Erreur de base de données : ' . $e->getMessage()]);
        }
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if ($action === 'get_replies') {
        $question_id = isset($_GET['question_id']) ? intval($_GET['question_id']) : 0;

        if (empty($question_id)) {
            echo json_encode(['success' => false, 'message' => 'ID de question manquant.']);
            exit;
        }

        try {
            $stmt = $pdo->prepare("SELECT * FROM `forum_replies` WHERE `question_id` = :question_id ORDER BY `id` ASC");
            $stmt->execute(['question_id' => $question_id]);
            $replies = $stmt->fetchAll();

            echo json_encode([
                'success' => true,
                'replies' => $replies
            ]);
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => 'Erreur lors de la récupération : ' . $e->getMessage()]);
        }
        exit;
    }
}

echo json_encode(['success' => false, 'message' => 'Action non supportée.']);
?>
