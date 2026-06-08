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

// Check for actions (status updates)
if (isset($_GET['action']) && isset($_GET['id'])) {
    $action = $_GET['action'];
    $id = intval($_GET['id']);
    try {
        if ($action === 'confirm') {
            $stmt = $pdo->prepare("UPDATE `registrations` SET `status` = 'Confirmé' WHERE `id` = :id");
            $stmt->execute(['id' => $id]);
            $message = "L'inscription a été marquée comme confirmée.";
        } elseif ($action === 'pay') {
            $stmt = $pdo->prepare("UPDATE `registrations` SET `status` = 'Payé' WHERE `id` = :id");
            $stmt->execute(['id' => $id]);
            $message = "Le paiement a été enregistré avec succès.";
        } elseif ($action === 'cancel') {
            $stmt = $pdo->prepare("UPDATE `registrations` SET `status` = 'Annulé' WHERE `id` = :id");
            $stmt->execute(['id' => $id]);
            $message = "L'inscription a été annulée.";
        } elseif ($action === 'pending') {
            $stmt = $pdo->prepare("UPDATE `registrations` SET `status` = 'En attente' WHERE `id` = :id");
            $stmt->execute(['id' => $id]);
            $message = "L'inscription a été remise en attente.";
        }
    } catch (Exception $e) {
        $err_message = "Erreur lors du changement de statut : " . $e->getMessage();
    }
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
$status_filter = isset($_GET['status_filter']) ? trim($_GET['status_filter']) : '';

// Fetch all registrations
try {
    $sql = "SELECT * FROM `registrations`";
    $where_clauses = [];
    $params = [];
    
    if (!empty($course_filter)) {
        $where_clauses[] = "`course_id` = :course";
        $params['course'] = $course_filter;
    }
    
    if (!empty($status_filter)) {
        $where_clauses[] = "`status` = :status";
        $params['status'] = $status_filter;
    }
    
    if (!empty($where_clauses)) {
        $sql .= " WHERE " . implode(" AND ", $where_clauses);
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
        <div class="col-md-4">
            <label class="form-label text-muted small fw-bold mb-1">Filtrer par statut :</label>
            <select name="status_filter" class="form-select" onchange="this.form.submit()">
                <option value="">Tous les statuts</option>
                <option value="En attente" <?php echo ($status_filter == 'En attente') ? 'selected' : ''; ?>>En attente</option>
                <option value="Confirmé" <?php echo ($status_filter == 'Confirmé') ? 'selected' : ''; ?>>Confirmé</option>
                <option value="Payé" <?php echo ($status_filter == 'Payé') ? 'selected' : ''; ?>>Payé</option>
                <option value="Annulé" <?php echo ($status_filter == 'Annulé') ? 'selected' : ''; ?>>Annulé</option>
            </select>
        </div>
        <div class="col-md-4 d-flex align-items-end mt-4">
            <?php if (!empty($course_filter) || !empty($status_filter)): ?>
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
                        <th>Statut</th>
                        <th class="text-end" style="min-width: 180px;">Actions</th>
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
                                <div class="mb-1">
                                    <a href="mailto:<?php echo htmlspecialchars($reg['email']); ?>" class="text-decoration-none text-dark">
                                        <i class="bi bi-envelope me-1 text-muted small"></i><?php echo htmlspecialchars($reg['email']); ?>
                                    </a>
                                </div>
                                <?php if (!empty($reg['phone'])): ?>
                                    <div>
                                        <a href="tel:<?php echo htmlspecialchars($reg['phone']); ?>" class="text-decoration-none text-muted small">
                                            <i class="bi bi-telephone me-1 small"></i><?php echo htmlspecialchars($reg['phone']); ?>
                                        </a>
                                    </div>
                                <?php endif; ?>
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
                            <td>
                                <?php if ($reg['status'] == 'Payé'): ?>
                                    <span class="pill-status bg-success bg-opacity-10 text-success"><i class="bi bi-credit-card-2-back-fill me-1"></i>Payé</span>
                                <?php elseif ($reg['status'] == 'Confirmé'): ?>
                                    <span class="pill-status bg-primary bg-opacity-10 text-primary"><i class="bi bi-check-circle-fill me-1"></i>Confirmé</span>
                                <?php elseif ($reg['status'] == 'Annulé'): ?>
                                    <span class="pill-status bg-danger bg-opacity-10 text-danger"><i class="bi bi-x-circle-fill me-1"></i>Annulé</span>
                                <?php else: ?>
                                    <span class="pill-status bg-warning bg-opacity-10 text-warning"><i class="bi bi-clock-fill me-1"></i>En attente</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end">
                                <div class="btn-group gap-1">
                                    <?php if ($reg['status'] !== 'Confirmé' && $reg['status'] !== 'Payé'): ?>
                                        <a href="registrations.php?action=confirm&id=<?php echo $reg['id']; ?>&course_filter=<?php echo urlencode($course_filter); ?>&status_filter=<?php echo urlencode($status_filter); ?>" 
                                           class="action-btn action-btn-success" 
                                           data-bs-toggle="tooltip" 
                                           data-bs-placement="top" 
                                           title="Confirmer l'inscription">
                                            <i class="bi bi-check-lg text-success"></i>
                                        </a>
                                    <?php endif; ?>
                                    
                                    <?php if ($reg['status'] === 'Confirmé'): ?>
                                        <a href="registrations.php?action=pay&id=<?php echo $reg['id']; ?>&course_filter=<?php echo urlencode($course_filter); ?>&status_filter=<?php echo urlencode($status_filter); ?>" 
                                           class="action-btn action-btn-success" 
                                           data-bs-toggle="tooltip" 
                                           data-bs-placement="top" 
                                           title="Enregistrer le paiement">
                                            <i class="bi bi-credit-card text-success"></i>
                                        </a>
                                    <?php endif; ?>

                                    <?php if ($reg['status'] !== 'Annulé'): ?>
                                        <a href="registrations.php?action=cancel&id=<?php echo $reg['id']; ?>&course_filter=<?php echo urlencode($course_filter); ?>&status_filter=<?php echo urlencode($status_filter); ?>" 
                                           class="action-btn" 
                                           data-bs-toggle="tooltip" 
                                           data-bs-placement="top" 
                                           title="Annuler l'inscription">
                                            <i class="bi bi-x-circle text-danger"></i>
                                        </a>
                                    <?php endif; ?>

                                    <?php if ($reg['status'] === 'Annulé' || $reg['status'] === 'Payé'): ?>
                                        <a href="registrations.php?action=pending&id=<?php echo $reg['id']; ?>&course_filter=<?php echo urlencode($course_filter); ?>&status_filter=<?php echo urlencode($status_filter); ?>" 
                                           class="action-btn" 
                                           data-bs-toggle="tooltip" 
                                           data-bs-placement="top" 
                                           title="Remettre en attente">
                                            <i class="bi bi-arrow-counterclockwise text-warning"></i>
                                        </a>
                                    <?php endif; ?>
                                    
                                    <a href="registrations.php?delete=<?php echo $reg['id']; ?>&course_filter=<?php echo urlencode($course_filter); ?>&status_filter=<?php echo urlencode($status_filter); ?>" 
                                       class="action-btn action-btn-danger" 
                                       onclick="return confirm('Êtes-vous sûr de vouloir supprimer définitivement cette inscription ?')"
                                       data-bs-toggle="tooltip" 
                                       data-bs-placement="top" 
                                       title="Supprimer l'inscription">
                                        <i class="bi bi-trash-fill"></i>
                                    </a>
                                </div>
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
