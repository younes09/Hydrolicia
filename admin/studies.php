<?php
// admin/studies.php
require_once '../config/db.php';
require_once 'includes/header.php';

$message = '';
$err_message = '';

// Handle actions
if (isset($_GET['action']) && isset($_GET['id'])) {
    $action = $_GET['action'];
    $id = intval($_GET['id']);
    
    try {
        if ($action === 'analyse') {
            $stmt = $pdo->prepare("UPDATE `studies` SET `status` = 'Reçu / En analyse' WHERE `id` = :id");
            $stmt->execute(['id' => $id]);
            $message = "Le dossier est maintenant en cours d'analyse.";
        } elseif ($action === 'devis') {
            $stmt = $pdo->prepare("UPDATE `studies` SET `status` = 'Devis envoyé' WHERE `id` = :id");
            $stmt->execute(['id' => $id]);
            $message = "Le statut a été mis à jour : Devis envoyé.";
        } elseif ($action === 'approve') {
            $stmt = $pdo->prepare("UPDATE `studies` SET `status` = 'Approuvé' WHERE `id` = :id");
            $stmt->execute(['id' => $id]);
            $message = "La demande d'étude a été approuvée.";
        } elseif ($action === 'reject') {
            $stmt = $pdo->prepare("UPDATE `studies` SET `status` = 'Rejeté' WHERE `id` = :id");
            $stmt->execute(['id' => $id]);
            $message = "Le dossier a été marqué comme rejeté.";
        } elseif ($action === 'delete') {
            // Get file path before deleting and delete the file from the server
            $stmt = $pdo->prepare("SELECT file_path FROM studies WHERE id = :id");
            $stmt->execute(['id' => $id]);
            $file_path = $stmt->fetchColumn();
            if ($file_path && file_exists('../uploads/studies/' . $file_path)) {
                unlink('../uploads/studies/' . $file_path);
            }
            // Delete from database
            $stmt = $pdo->prepare("DELETE FROM studies WHERE id = :id");
            $stmt->execute(['id' => $id]);
            $message = "Le dossier d'étude a été supprimé définitivement.";
        }
    } catch (Exception $e) {
        $err_message = "Une erreur est survenue lors du traitement : " . $e->getMessage();
    }
}

// Filtering
$status_filter = isset($_GET['status_filter']) ? trim($_GET['status_filter']) : '';
$type_filter = isset($_GET['type_filter']) ? trim($_GET['type_filter']) : '';

try {
    $sql = "SELECT * FROM `studies`";
    $where_clauses = [];
    $params = [];
    
    if (!empty($status_filter)) {
        $where_clauses[] = "`status` = :status";
        $params['status'] = $status_filter;
    }
    
    if (!empty($type_filter)) {
        $where_clauses[] = "`study_type` = :type";
        $params['type'] = $type_filter;
    }
    
    if (!empty($where_clauses)) {
        $sql .= " WHERE " . implode(" AND ", $where_clauses);
    }
    
    $sql .= " ORDER BY `id` DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $studies = $stmt->fetchAll();
} catch (Exception $e) {
    $err_message = "Erreur SQL : " . $e->getMessage();
    $studies = [];
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-1">Dossiers d'Études</h2>
        <p class="text-muted mb-0">Analysez les demandes de dimensionnement soumises par des tiers et proposez des devis ou approbations.</p>
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

<!-- Filters panel -->
<div class="admin-card mb-4 p-3">
    <form method="GET" action="studies.php" class="row g-3">
        <div class="col-md-4">
            <label class="form-label text-muted small fw-bold mb-1">Filtrer par type d'étude :</label>
            <select name="type_filter" class="form-select" onchange="this.form.submit()">
                <option value="">Tous les types</option>
                <option value="Modélisation & Calage de Réseau AEP" <?php echo ($type_filter == "Modélisation & Calage de Réseau AEP") ? 'selected' : ''; ?>>Modélisation AEP</option>
                <option value="Diagnostic & Dimensionnement Assainissement" <?php echo ($type_filter == "Diagnostic & Dimensionnement Assainissement") ? 'selected' : ''; ?>>Assainissement / Drainage</option>
                <option value="Conception Périmètre d'Irrigation Agricole" <?php echo ($type_filter == "Conception Périmètre d'Irrigation Agricole") ? 'selected' : ''; ?>>Irrigation Agricole</option>
                <option value="Dimensionnement Ouvrages & Stations de Pompage" <?php echo ($type_filter == "Dimensionnement Ouvrages & Stations de Pompage") ? 'selected' : ''; ?>>Pompage & Refoulement</option>
                <option value="Étude REUSE (Réutilisation eaux STEP)" <?php echo ($type_filter == "Étude REUSE (Réutilisation eaux STEP)") ? 'selected' : ''; ?>>REUSE (STEP)</option>
            </select>
        </div>
        <div class="col-md-4">
            <label class="form-label text-muted small fw-bold mb-1">Filtrer par statut :</label>
            <select name="status_filter" class="form-select" onchange="this.form.submit()">
                <option value="">Tous les statuts</option>
                <option value="Reçu" <?php echo ($status_filter == 'Reçu') ? 'selected' : ''; ?>>Reçu</option>
                <option value="Reçu / En analyse" <?php echo ($status_filter == 'Reçu / En analyse') ? 'selected' : ''; ?>>En analyse</option>
                <option value="Devis envoyé" <?php echo ($status_filter == 'Devis envoyé') ? 'selected' : ''; ?>>Devis envoyé</option>
                <option value="Approuvé" <?php echo ($status_filter == 'Approuvé') ? 'selected' : ''; ?>>Approuvé</option>
                <option value="Rejeté" <?php echo ($status_filter == 'Rejeté') ? 'selected' : ''; ?>>Rejeté</option>
            </select>
        </div>
        <div class="col-md-4 d-flex align-items-end">
            <?php if (!empty($type_filter) || !empty($status_filter)): ?>
                <a href="studies.php" class="btn btn-outline-secondary btn-sm rounded-pill mb-1"><i class="bi bi-x-circle me-1"></i>Réinitialiser</a>
            <?php endif; ?>
        </div>
    </form>
</div>

<!-- Grid / Table of dossiers -->
<div class="admin-card">
    <?php if (empty($studies)): ?>
        <div class="text-center py-5">
            <i class="bi bi-folder-x fs-1 text-muted"></i>
            <h5 class="mt-3">Aucun dossier d'étude</h5>
            <p class="text-muted small">Aucune soumission ne correspond aux filtres appliqués.</p>
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>Demandeur / BE</th>
                        <th>Type d'Étude</th>
                        <th>Date de Dépôt</th>
                        <th>Description du projet</th>
                        <th>Statut</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($studies as $study): ?>
                        <tr>
                            <td data-label="Demandeur">
                                <div class="fw-bold"><?php echo htmlspecialchars($study['name']); ?></div>
                                <div class="small text-info mb-1"><?php echo htmlspecialchars($study['organization']); ?></div>
                                <a href="mailto:<?php echo htmlspecialchars($study['email']); ?>" class="small text-muted text-decoration-none d-block">
                                    <i class="bi bi-envelope me-1"></i><?php echo htmlspecialchars($study['email']); ?>
                                </a>
                                <?php if (!empty($study['phone'])): ?>
                                    <a href="tel:<?php echo htmlspecialchars($study['phone']); ?>" class="small text-muted text-decoration-none d-block">
                                        <i class="bi bi-telephone-fill me-1 text-primary"></i><?php echo htmlspecialchars($study['phone']); ?>
                                    </a>
                                <?php endif; ?>
                            </td>
                            <td data-label="Type d'étude">
                                <span class="badge bg-secondary-subtle text-secondary-emphasis"><?php echo htmlspecialchars($study['study_type']); ?></span>
                            </td>
                            <td data-label="Date de dépôt">
                                <small class="text-muted"><i class="bi bi-calendar-event me-1"></i><?php echo date('d-m-Y', strtotime($study['created_at'])); ?></small>
                            </td>
                            <td data-label="Description">
                                <span class="d-inline-block text-truncate text-muted" style="max-width: 200px;">
                                    <?php echo htmlspecialchars($study['description']); ?>
                                </span>
                                <button type="button" class="btn btn-sm btn-link p-0 d-block text-start text-decoration-none small text-accent" 
                                        onclick="showDescriptionModal(this)"
                                        data-owner="<?php echo htmlspecialchars($study['name'], ENT_QUOTES); ?>"
                                        data-description="<?php echo htmlspecialchars($study['description'], ENT_QUOTES); ?>"
                                        data-filepath="<?php echo !empty($study['file_path']) ? htmlspecialchars($study['file_path'], ENT_QUOTES) : ''; ?>">
                                    Lire la suite...
                                </button>
                                <?php if (!empty($study['file_path'])): ?>
                                    <a href="../uploads/studies/<?php echo htmlspecialchars($study['file_path']); ?>" target="_blank" class="badge bg-light text-primary border border-primary text-decoration-none mt-1 d-inline-flex align-items-center gap-1 small" style="font-size: 0.75rem;">
                                        <i class="bi bi-file-earmark-arrow-down-fill"></i> Doc joint
                                    </a>
                                <?php endif; ?>
                            </td>
                            <td data-label="Statut">
                                <?php if ($study['status'] == 'Approuvé'): ?>
                                    <span class="pill-status bg-success bg-opacity-10 text-success"><i class="bi bi-check-circle-fill"></i> Approuvé</span>
                                <?php elseif ($study['status'] == 'Devis envoyé'): ?>
                                    <span class="pill-status bg-info bg-opacity-10 text-info"><i class="bi bi-file-earmark-medical-fill"></i> Devis envoyé</span>
                                <?php elseif ($study['status'] == 'Rejeté'): ?>
                                    <span class="pill-status bg-danger bg-opacity-10 text-danger"><i class="bi bi-x-circle-fill"></i> Rejeté</span>
                                <?php elseif ($study['status'] == 'Reçu / En analyse'): ?>
                                    <span class="pill-status bg-primary bg-opacity-10 text-primary"><i class="bi bi-gear-fill"></i> En analyse</span>
                                <?php else: ?>
                                    <span class="pill-status bg-warning bg-opacity-10 text-warning"><i class="bi bi-hourglass-split"></i> Reçu</span>
                                <?php endif; ?>
                            </td>
                            <td data-label="Actions" class="text-end">
                                <div class="btn-group gap-1">
                                    <?php if ($study['status'] === 'Reçu'): ?>
                                        <a href="studies.php?action=analyse&id=<?php echo $study['id']; ?>&type_filter=<?php echo urlencode($type_filter); ?>&status_filter=<?php echo urlencode($status_filter); ?>" 
                                           class="action-btn" 
                                           data-bs-toggle="tooltip" 
                                           data-bs-placement="top" 
                                           title="Marquer en cours d'analyse">
                                            <i class="bi bi-gear-fill text-primary"></i>
                                        </a>
                                    <?php endif; ?>
                                    
                                    <?php if ($study['status'] === 'Reçu' || $study['status'] === 'Reçu / En analyse'): ?>
                                        <a href="studies.php?action=devis&id=<?php echo $study['id']; ?>&type_filter=<?php echo urlencode($type_filter); ?>&status_filter=<?php echo urlencode($status_filter); ?>" 
                                           class="action-btn" 
                                           data-bs-toggle="tooltip" 
                                           data-bs-placement="top" 
                                           title="Déclarer devis envoyé">
                                            <i class="bi bi-file-earmark-medical-fill text-info"></i>
                                        </a>
                                    <?php endif; ?>

                                    <?php if ($study['status'] !== 'Approuvé'): ?>
                                        <a href="studies.php?action=approve&id=<?php echo $study['id']; ?>&type_filter=<?php echo urlencode($type_filter); ?>&status_filter=<?php echo urlencode($status_filter); ?>" 
                                           class="action-btn action-btn-success" 
                                           data-bs-toggle="tooltip" 
                                           data-bs-placement="top" 
                                           title="Approuver le dossier">
                                            <i class="bi bi-check-lg text-success"></i>
                                        </a>
                                    <?php endif; ?>
                                    
                                    <?php if ($study['status'] !== 'Rejeté'): ?>
                                        <a href="studies.php?action=reject&id=<?php echo $study['id']; ?>&type_filter=<?php echo urlencode($type_filter); ?>&status_filter=<?php echo urlencode($status_filter); ?>" 
                                           class="action-btn" 
                                           data-bs-toggle="tooltip" 
                                           data-bs-placement="top" 
                                           title="Rejeter le dossier">
                                            <i class="bi bi-x-circle text-danger"></i>
                                        </a>
                                    <?php endif; ?>

                                    <a href="studies.php?action=delete&id=<?php echo $study['id']; ?>&type_filter=<?php echo urlencode($type_filter); ?>&status_filter=<?php echo urlencode($status_filter); ?>" 
                                       class="action-btn action-btn-danger" 
                                       onclick="return confirm('Êtes-vous certain de vouloir supprimer ce dossier d\'étude ? Cette action est irréversible.')"
                                       data-bs-toggle="tooltip" 
                                       data-bs-placement="top" 
                                       title="Supprimer définitivement">
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

<!-- Modal for full project details -->
<div class="modal fade" id="descriptionModal" tabindex="-1" aria-labelledby="descriptionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header bg-primary text-white border-0 py-3 rounded-top-4">
                <h5 class="modal-title fw-bold" id="descriptionModalLabel">Description détaillée du projet</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-3">
                    <label class="form-label text-muted small fw-bold mb-1">Porteur du Projet :</label>
                    <div id="modalProjectOwner" class="fw-bold text-dark fs-6"></div>
                </div>
                <div class="mb-3">
                    <label class="form-label text-muted small fw-bold mb-1">Cahier des charges / Descriptif :</label>
                    <div id="modalDescriptionContent" class="bg-light p-3 rounded-3 text-secondary" style="white-space: pre-wrap; font-size: 0.95rem; line-height: 1.6;"></div>
                </div>
                <div class="mb-2 d-none" id="modalFileContainer">
                    <label class="form-label text-muted small fw-bold mb-1">Document joint :</label>
                    <div>
                        <a href="#" id="modalFileLink" target="_blank" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                            <i class="bi bi-file-earmark-arrow-down-fill me-1"></i> Télécharger le document joint
                        </a>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Fermer</button>
            </div>
        </div>
    </div>
</div>

<script>
let descModal;

document.addEventListener('DOMContentLoaded', function() {
    descModal = new bootstrap.Modal(document.getElementById('descriptionModal'));
});

function showDescriptionModal(btn) {
    document.getElementById('modalProjectOwner').innerText = btn.dataset.owner;
    document.getElementById('modalDescriptionContent').innerText = btn.dataset.description;
    
    const fileContainer = document.getElementById('modalFileContainer');
    const fileLink = document.getElementById('modalFileLink');
    if (btn.dataset.filepath) {
        fileLink.href = '../uploads/studies/' + btn.dataset.filepath;
        fileContainer.classList.remove('d-none');
    } else {
        fileLink.href = '#';
        fileContainer.classList.add('d-none');
    }
    descModal.show();
}
</script>

<?php
require_once 'includes/footer.php';
?>
