<?php
// admin/consultations.php
require_once '../config/db.php';
require_once 'includes/header.php';

$message = '';
$err_message = '';

// Handle actions
if (isset($_GET['action']) && isset($_GET['id'])) {
    $action = $_GET['action'];
    $id = intval($_GET['id']);
    
    try {
        if ($action === 'confirm') {
            $stmt = $pdo->prepare("UPDATE `consultations` SET `status` = 'Confirmé' WHERE `id` = :id");
            $stmt->execute(['id' => $id]);
            $message = "La consultation a été confirmée avec succès.";
        } elseif ($action === 'pending') {
            $stmt = $pdo->prepare("UPDATE `consultations` SET `status` = 'En attente' WHERE `id` = :id");
            $stmt->execute(['id' => $id]);
            $message = "La consultation a été remise en attente.";
        } elseif ($action === 'cancel') {
            $stmt = $pdo->prepare("UPDATE `consultations` SET `status` = 'Annulé' WHERE `id` = :id");
            $stmt->execute(['id' => $id]);
            $message = "La consultation a été annulée.";
        } elseif ($action === 'delete') {
            $stmt = $pdo->prepare("DELETE FROM `consultations` WHERE `id` = :id");
            $stmt->execute(['id' => $id]);
            $message = "La consultation a été supprimée définitivement.";
        }
    } catch (Exception $e) {
        $err_message = "Une erreur est survenue lors de l'opération : " . $e->getMessage();
    }
}

// Filtering parameters
$expert_filter = isset($_GET['expert_filter']) ? trim($_GET['expert_filter']) : '';
$status_filter = isset($_GET['status_filter']) ? trim($_GET['status_filter']) : '';

$experts_list = [];
try {
    $experts_list = $pdo->query("SELECT `name` FROM `experts` ORDER BY `name` ASC")->fetchAll();
} catch (Exception $e) {
    // Fail silently
}

try {
    $sql = "SELECT * FROM `consultations`";
    $where_clauses = [];
    $params = [];
    
    if (!empty($expert_filter)) {
        $where_clauses[] = "`expert_name` = :expert";
        $params['expert'] = $expert_filter;
    }
    
    if (!empty($status_filter)) {
        $where_clauses[] = "`status` = :status";
        $params['status'] = $status_filter;
    }
    
    if (!empty($where_clauses)) {
        $sql .= " WHERE " . implode(" AND ", $where_clauses);
    }
    
    $sql .= " ORDER BY `date` DESC, `time` DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $consultations = $stmt->fetchAll();
} catch (Exception $e) {
    $err_message = "Erreur SQL : " . $e->getMessage();
    $consultations = [];
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-1">Rendez-vous Consultations</h2>
        <p class="text-muted mb-0">Planifiez et suivez les créneaux d'entretien réservés par les utilisateurs avec vos experts hydrauliciens.</p>
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
    <form method="GET" action="consultations.php" class="row g-3 align-items-end">
        <div class="col-md-4">
            <label class="form-label text-muted small fw-bold mb-1">Recherche :</label>
            <div class="position-relative">
                <input type="text" id="adminConsultSearch" class="form-control" placeholder="Client, e-mail, téléphone, sujet..." style="font-size: 0.9rem;">
                <i class="bi bi-search text-muted position-absolute end-0 top-50 translate-middle-y me-3"></i>
            </div>
        </div>
        <div class="col-md-3">
            <label class="form-label text-muted small fw-bold mb-1">Filtrer par expert :</label>
            <select name="expert_filter" class="form-select" onchange="this.form.submit()" style="font-size: 0.9rem;">
                <option value="">Tous les experts</option>
                <?php foreach ($experts_list as $exp): ?>
                    <option value="<?php echo htmlspecialchars($exp['name']); ?>" <?php echo ($expert_filter == $exp['name']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($exp['name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label text-muted small fw-bold mb-1">Filtrer par statut :</label>
            <select name="status_filter" class="form-select" onchange="this.form.submit()" style="font-size: 0.9rem;">
                <option value="">Tous les statuts</option>
                <option value="En attente" <?php echo ($status_filter == 'En attente') ? 'selected' : ''; ?>>En attente</option>
                <option value="Confirmé" <?php echo ($status_filter == 'Confirmé') ? 'selected' : ''; ?>>Confirmé</option>
                <option value="Annulé" <?php echo ($status_filter == 'Annulé') ? 'selected' : ''; ?>>Annulé</option>
            </select>
        </div>
        <div class="col-md-2 d-grid">
            <?php if (!empty($expert_filter) || !empty($status_filter)): ?>
                <a href="consultations.php" class="btn btn-outline-secondary rounded-pill btn-sm mb-1 py-2 text-center"><i class="bi bi-x-circle me-1"></i>Réinitialiser</a>
            <?php else: ?>
                <div style="height: 38px;"></div>
            <?php endif; ?>
        </div>
    </form>
</div>

<!-- List of bookings -->
<div class="admin-card">
    <?php if (empty($consultations)): ?>
        <div class="text-center py-5">
            <i class="bi bi-calendar-x fs-1 text-muted"></i>
            <h5 class="mt-3">Aucune consultation enregistrée</h5>
            <p class="text-muted small">Aucun créneau ne correspond à vos filtres actuels.</p>
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>Client / Demandeur</th>
                        <th>Expert affecté</th>
                        <th>Projet / Sujet d'Étude</th>
                        <th>Date & Heure</th>
                        <th>Statut</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- No Results message row -->
                    <tr id="adminConsultNoResults" class="d-none">
                        <td colspan="6" class="text-center py-4 text-muted">
                            <i class="bi bi-search fs-4 d-block mb-2 text-info"></i>
                            Aucune consultation ne correspond à votre recherche.
                        </td>
                    </tr>
                    <?php foreach ($consultations as $consult): ?>
                        <tr class="consult-row" 
                            data-name="<?php echo htmlspecialchars($consult['name'], ENT_QUOTES); ?>"
                            data-email="<?php echo htmlspecialchars($consult['email'], ENT_QUOTES); ?>"
                            data-phone="<?php echo htmlspecialchars($consult['phone'] ?? '', ENT_QUOTES); ?>"
                            data-topic="<?php echo htmlspecialchars($consult['topic'] ?? '', ENT_QUOTES); ?>">
                            <td data-label="Client">
                                <div class="fw-bold"><?php echo htmlspecialchars($consult['name']); ?></div>
                                <a href="mailto:<?php echo htmlspecialchars($consult['email']); ?>" class="small text-muted text-decoration-none d-block">
                                    <i class="bi bi-envelope me-1"></i><?php echo htmlspecialchars($consult['email']); ?>
                                </a>
                                <?php if (!empty($consult['phone'])): ?>
                                    <a href="tel:<?php echo htmlspecialchars($consult['phone']); ?>" class="small text-muted text-decoration-none d-block">
                                        <i class="bi bi-phone me-1 text-primary"></i><?php echo htmlspecialchars($consult['phone']); ?>
                                    </a>
                                <?php endif; ?>
                            </td>
                            <td data-label="Expert">
                                <div class="fw-semibold"><i class="bi bi-person-badge text-info me-1"></i><?php echo htmlspecialchars($consult['expert_name']); ?></div>
                            </td>
                            <td data-label="Sujet">
                                <span class="d-inline-block text-truncate" style="max-width: 200px;" title="<?php echo htmlspecialchars($consult['topic']); ?>">
                                    <?php echo htmlspecialchars($consult['topic']); ?>
                                </span>
                            </td>
                            <td data-label="Date & Heure">
                                <span class="badge bg-secondary-subtle text-secondary-emphasis py-2 px-3">
                                    <i class="bi bi-calendar-event me-1"></i><?php echo date('d-m-Y', strtotime($consult['date'])); ?> à <?php echo substr($consult['time'], 0, 5); ?>
                                </span>
                            </td>
                            <td data-label="Statut">
                                <?php if ($consult['status'] == 'Confirmé'): ?>
                                    <span class="pill-status bg-success bg-opacity-10 text-success"><i class="bi bi-check-circle-fill"></i> Confirmé</span>
                                <?php elseif ($consult['status'] == 'Annulé'): ?>
                                    <span class="pill-status bg-danger bg-opacity-10 text-danger"><i class="bi bi-x-circle-fill"></i> Annulé</span>
                                <?php else: ?>
                                    <span class="pill-status bg-warning bg-opacity-10 text-warning"><i class="bi bi-clock-fill"></i> En attente</span>
                                <?php endif; ?>
                            </td>
                            <td data-label="Actions" class="text-end">
                                <div class="btn-group gap-1">
                                    <?php if ($consult['status'] !== 'Confirmé'): ?>
                                        <a href="consultations.php?action=confirm&id=<?php echo $consult['id']; ?>&expert_filter=<?php echo urlencode($expert_filter); ?>&status_filter=<?php echo urlencode($status_filter); ?>" 
                                           class="action-btn action-btn-success" 
                                           data-bs-toggle="tooltip" 
                                           data-bs-placement="top" 
                                           title="Confirmer la session">
                                            <i class="bi bi-check-lg text-success"></i>
                                        </a>
                                    <?php endif; ?>
                                    
                                    <?php if ($consult['status'] === 'Confirmé'): ?>
                                        <a href="consultations.php?action=pending&id=<?php echo $consult['id']; ?>&expert_filter=<?php echo urlencode($expert_filter); ?>&status_filter=<?php echo urlencode($status_filter); ?>" 
                                           class="action-btn" 
                                           data-bs-toggle="tooltip" 
                                           data-bs-placement="top" 
                                           title="Remettre en attente">
                                            <i class="bi bi-arrow-counterclockwise text-warning"></i>
                                        </a>
                                    <?php endif; ?>

                                    <?php if ($consult['status'] !== 'Annulé'): ?>
                                        <a href="consultations.php?action=cancel&id=<?php echo $consult['id']; ?>&expert_filter=<?php echo urlencode($expert_filter); ?>&status_filter=<?php echo urlencode($status_filter); ?>" 
                                           class="action-btn" 
                                           data-bs-toggle="tooltip" 
                                           data-bs-placement="top" 
                                           title="Annuler la session">
                                            <i class="bi bi-x-circle text-danger"></i>
                                        </a>
                                    <?php endif; ?>
                                    
                                    <a href="consultations.php?action=delete&id=<?php echo $consult['id']; ?>&expert_filter=<?php echo urlencode($expert_filter); ?>&status_filter=<?php echo urlencode($status_filter); ?>" 
                                       class="action-btn action-btn-danger" 
                                       onclick="return confirm('Voulez-vous vraiment supprimer définitivement cette réservation de créneau ?')"
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    const consultSearch = document.getElementById('adminConsultSearch');
    const consultNoResults = document.getElementById('adminConsultNoResults');
    const consultRows = document.querySelectorAll('.consult-row');

    if (consultSearch) {
        consultSearch.addEventListener('input', function() {
            const query = this.value.toLowerCase().trim();
            let visibleCount = 0;

            consultRows.forEach(row => {
                const name = row.getAttribute('data-name').toLowerCase();
                const email = row.getAttribute('data-email').toLowerCase();
                const phone = row.getAttribute('data-phone').toLowerCase();
                const topic = row.getAttribute('data-topic').toLowerCase();

                const matchesQuery = (query === '' || 
                                      name.includes(query) || 
                                      email.includes(query) || 
                                      phone.includes(query) ||
                                      topic.includes(query));

                if (matchesQuery) {
                    row.classList.remove('d-none');
                    visibleCount++;
                } else {
                    row.classList.add('d-none');
                }
            });

            if (consultNoResults) {
                if (visibleCount === 0) {
                    consultNoResults.classList.remove('d-none');
                } else {
                    consultNoResults.classList.add('d-none');
                }
            }
        });
    }
});
</script>

<?php
require_once 'includes/footer.php';
?>
