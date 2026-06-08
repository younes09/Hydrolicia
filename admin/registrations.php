<?php
// admin/registrations.php
require_once '../config/db.php';
require_once 'includes/header.php';

$message = '';
$err_message = '';

// Fetch all trainings for mapping and filtering
$trainings_list = [];
$trainings_map = [];
try {
    $stmt_tr = $pdo->query("SELECT `code`, `title` FROM `trainings` ORDER BY `title` ASC");
    $trainings_list = $stmt_tr->fetchAll();
    foreach ($trainings_list as $tr) {
        $trainings_map[$tr['code']] = $tr['title'];
    }
} catch (Exception $e) {
    // Fail silently
}

// Check for delete action
if (isset($_GET['delete'])) {
    $delete_id = intval($_GET['delete']);
    try {
        $stmt = $pdo->prepare("DELETE FROM `registrations` WHERE `id` = :id");
        $stmt->execute(['id' => $delete_id]);
        $message = "L'inscription a été supprimée avec succès.";
    } catch (Exception $e) {
        $err_message = "Impossible de supprimer l'inscription : " . $e->getMessage();
    }
}

// Filtering
$course_filter = isset($_GET['course_filter']) ? trim($_GET['course_filter']) : '';

// Fetch all registrations
try {
    $sql = "SELECT * FROM `registrations`";
    $params = [];
    
    if (!empty($course_filter)) {
        $sql .= " WHERE `course_id` = :course";
        $params['course'] = $course_filter;
    }
    
    $sql .= " ORDER BY `id` DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $registrations = $stmt->fetchAll();
} catch (Exception $e) {
    $err_message = "Erreur SQL : " . $e->getMessage();
    $registrations = [];
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-1">Inscriptions aux Formations</h2>
        <p class="text-muted mb-0">Consultez et gérez la liste des candidats inscrits à vos modules pratiques.</p>
    </div>
</div>

<?php if (!empty($message)): ?>
    <div class="alert alert-success alert-dismissible fade show rounded-4" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i> <?php echo htmlspecialchars($message); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?php if (!empty($err_message)): ?>
    <div class="alert alert-danger alert-dismissible fade show rounded-4" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i> <?php echo htmlspecialchars($err_message); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<!-- Filter bar -->
<div class="admin-card mb-4 p-3">
    <form method="GET" action="registrations.php" class="row g-3 align-items-center">
        <div class="col-md-4">
            <label class="form-label text-muted small fw-bold mb-1">Filtrer par module :</label>
            <select name="course_filter" class="form-select" onchange="this.form.submit()">
                <option value="">Tous les modules</option>
                <?php foreach ($trainings_list as $tr): ?>
                    <option value="<?php echo htmlspecialchars($tr['code']); ?>" <?php echo ($course_filter == $tr['code']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($tr['title']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-2 mt-4">
            <?php if (!empty($course_filter)): ?>
                <a href="registrations.php" class="btn btn-outline-secondary btn-sm rounded-pill"><i class="bi bi-x-circle me-1"></i>Réinitialiser</a>
            <?php endif; ?>
        </div>
    </form>
</div>

<!-- List Card -->
<div class="admin-card">
    <?php if (empty($registrations)): ?>
        <div class="text-center py-5">
            <i class="bi bi-person-x fs-1 text-muted"></i>
            <h5 class="mt-3">Aucune inscription trouvée</h5>
            <p class="text-muted small">Aucun enregistrement ne correspond aux critères sélectionnés.</p>
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>Candidat</th>
                        <th>Adresse E-mail</th>
                        <th>Formation sélectionnée</th>
                        <th>Date d'inscription</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($registrations as $reg): ?>
                        <tr>
                            <td>
                                <div class="fw-bold d-flex align-items-center">
                                    <div class="avatar-circle me-2 bg-light text-primary d-flex align-items-center justify-content-center fw-bold rounded-circle" style="width:32px; height:32px; font-size:0.85rem;">
                                        <?php echo strtoupper(substr($reg['name'], 0, 1)); ?>
                                    </div>
                                    <?php echo htmlspecialchars($reg['name']); ?>
                                </div>
                            </td>
                            <td>
                                <a href="mailto:<?php echo htmlspecialchars($reg['email']); ?>" class="text-decoration-none text-dark">
                                    <i class="bi bi-envelope me-1 text-muted"></i><?php echo htmlspecialchars($reg['email']); ?>
                                </a>
                            </td>
                            <td>
                                <span class="badge bg-primary-subtle text-primary-emphasis rounded-pill px-3 py-2">
                                    <?php 
                                        if (isset($trainings_map[$reg['course_id']])) {
                                            echo htmlspecialchars($trainings_map[$reg['course_id']]);
                                        } else {
                                            echo htmlspecialchars($reg['course_id']);
                                        }
                                    ?>
                                </span>
                            </td>
                            <td>
                                <span class="text-muted"><i class="bi bi-calendar3 me-1"></i><?php echo date('d-m-Y H:i', strtotime($reg['created_at'])); ?></span>
                            </td>
                            <td class="text-end">
                                <a href="registrations.php?delete=<?php echo $reg['id']; ?>&course_filter=<?php echo urlencode($course_filter); ?>" 
                                   class="action-btn action-btn-danger" 
                                   onclick="return confirm('Êtes-vous sûr de vouloir supprimer définitivement cette inscription ?')"
                                   data-bs-toggle="tooltip" 
                                   data-bs-placement="top" 
                                   title="Supprimer l'inscription">
                                    <i class="bi bi-trash-fill"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?php
require_once 'includes/footer.php';
?>
