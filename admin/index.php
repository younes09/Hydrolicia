<?php
// admin/index.php
require_once '../config/db.php';
require_once 'includes/header.php';

// Fetch stats
$count_registrations = 0;
$count_consultations_total = 0;
$count_consultations_pending = 0;
$count_studies_total = 0;
$count_studies_pending = 0;
$count_forum_questions = 0;

try {
    $count_registrations = $pdo->query("SELECT COUNT(*) FROM `registrations`")->fetchColumn();
    $count_consultations_total = $pdo->query("SELECT COUNT(*) FROM `consultations`")->fetchColumn();
    $count_consultations_pending = $pdo->query("SELECT COUNT(*) FROM `consultations` WHERE `status` = 'En attente'")->fetchColumn();
    $count_studies_total = $pdo->query("SELECT COUNT(*) FROM `studies`")->fetchColumn();
    $count_studies_pending = $pdo->query("SELECT COUNT(*) FROM `studies` WHERE `status` = 'Reçu'")->fetchColumn();
    $count_forum_questions = $pdo->query("SELECT COUNT(*) FROM `forum_questions`")->fetchColumn();
    
    // Fetch recent lists
    $recent_registrations = $pdo->query("SELECT * FROM `registrations` ORDER BY `id` DESC LIMIT 5")->fetchAll();
    $recent_consultations = $pdo->query("SELECT * FROM `consultations` ORDER BY `id` DESC LIMIT 5")->fetchAll();
    $recent_studies = $pdo->query("SELECT * FROM `studies` ORDER BY `id` DESC LIMIT 5")->fetchAll();
} catch (Exception $e) {
    echo "<div class='alert alert-danger'>Erreur de base de données : " . htmlspecialchars($e->getMessage()) . "</div>";
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-1">Tableau de Bord</h2>
        <p class="text-muted mb-0">Bienvenue dans l'espace de gestion de la plateforme Hydrolicia.</p>
    </div>
    <div class="text-muted small">
        <i class="bi bi-clock me-1"></i> Dernière mise à jour : <?php echo date('d-m-Y H:i'); ?>
    </div>
</div>

<!-- Stats row -->
<div class="row">
    <!-- Stat 1: Inscriptions -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="admin-card h-100 py-2">
            <div class="stat-card">
                <div>
                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1" style="font-size: 0.8rem; letter-spacing: 0.5px;">Inscriptions</div>
                    <div class="h3 mb-0 font-weight-bold"><?php echo $count_registrations; ?></div>
                    <small class="text-muted">Étudiants & pros inscrits</small>
                </div>
                <div class="stat-icon" style="background-color: rgba(13, 110, 253, 0.1); color: #0d6efd;">
                    <i class="bi bi-mortarboard-fill"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Stat 2: Consultations -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="admin-card h-100 py-2">
            <div class="stat-card">
                <div>
                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1" style="font-size: 0.8rem; letter-spacing: 0.5px;">Consultations</div>
                    <div class="h3 mb-0 font-weight-bold"><?php echo $count_consultations_total; ?></div>
                    <?php if ($count_consultations_pending > 0): ?>
                        <span class="badge bg-warning text-dark"><?php echo $count_consultations_pending; ?> en attente</span>
                    <?php else: ?>
                        <small class="text-muted">Toutes traitées</small>
                    <?php endif; ?>
                </div>
                <div class="stat-icon" style="background-color: rgba(255, 193, 7, 0.1); color: #ffc107;">
                    <i class="bi bi-calendar2-check-fill"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Stat 3: Dossiers d'études -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="admin-card h-100 py-2">
            <div class="stat-card">
                <div>
                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1" style="font-size: 0.8rem; letter-spacing: 0.5px;">Dossiers d'Études</div>
                    <div class="h3 mb-0 font-weight-bold"><?php echo $count_studies_total; ?></div>
                    <?php if ($count_studies_pending > 0): ?>
                        <span class="badge bg-success"><?php echo $count_studies_pending; ?> nouveaux</span>
                    <?php else: ?>
                        <small class="text-muted">Aucun nouveau dossier</small>
                    <?php endif; ?>
                </div>
                <div class="stat-icon" style="background-color: rgba(25, 135, 84, 0.1); color: #198754;">
                    <i class="bi bi-folder-fill"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Stat 4: Forum questions -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="admin-card h-100 py-2">
            <div class="stat-card">
                <div>
                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1" style="font-size: 0.8rem; letter-spacing: 0.5px;">Communauté</div>
                    <div class="h3 mb-0 font-weight-bold"><?php echo $count_forum_questions; ?></div>
                    <small class="text-muted">Sujets de discussion</small>
                </div>
                <div class="stat-icon" style="background-color: rgba(13, 202, 240, 0.1); color: #0dcaf0;">
                    <i class="bi bi-chat-left-quote-fill"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Main dashboard list grids -->
<div class="row">
    <!-- Left panel: recent registrations and consultations -->
    <div class="col-lg-7 mb-4">
        <!-- Recent registrations card -->
        <div class="admin-card">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold mb-0 text-primary"><i class="bi bi-people me-2"></i>Inscriptions Récentes</h5>
                <a href="registrations.php" class="btn btn-sm btn-link text-decoration-none">Tout voir <i class="bi bi-arrow-right"></i></a>
            </div>
            
            <?php if (empty($recent_registrations)): ?>
                <p class="text-muted small my-3 text-center">Aucune inscription enregistrée.</p>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle" style="font-size: 0.9rem;">
                        <thead class="table-light">
                            <tr>
                                <th>Nom</th>
                                <th>Formation</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recent_registrations as $reg): ?>
                                <tr>
                                    <td>
                                        <div class="fw-bold"><?php echo htmlspecialchars($reg['name']); ?></div>
                                        <small class="text-muted"><?php echo htmlspecialchars($reg['email']); ?></small>
                                    </td>
                                    <td>
                                        <span class="badge bg-primary-subtle text-primary-emphasis rounded-pill">
                                            <?php 
                                                if ($reg['course_id'] == 'aep') echo 'AEP (EPANET)';
                                                elseif ($reg['course_id'] == 'assainissement') echo 'Assainissement (SewerGEMS)';
                                                elseif ($reg['course_id'] == 'irrigation') echo 'Irrigation (CROPWAT)';
                                                elseif ($reg['course_id'] == 'hecras') echo 'Hydraulique (HEC-RAS)';
                                                else echo htmlspecialchars($reg['course_id']);
                                            ?>
                                        </span>
                                    </td>
                                    <td>
                                        <small class="text-muted"><?php echo date('d-m-Y', strtotime($reg['created_at'])); ?></small>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>

        <!-- Recent consultations card -->
        <div class="admin-card">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold mb-0 text-warning"><i class="bi bi-calendar-event me-2"></i>Consultations Récentes</h5>
                <a href="consultations.php" class="btn btn-sm btn-link text-decoration-none text-warning">Tout voir <i class="bi bi-arrow-right"></i></a>
            </div>
            
            <?php if (empty($recent_consultations)): ?>
                <p class="text-muted small my-3 text-center">Aucune consultation réservée.</p>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle" style="font-size: 0.9rem;">
                        <thead class="table-light">
                            <tr>
                                <th>Nom & Thème</th>
                                <th>Expert</th>
                                <th>Date & Heure</th>
                                <th>Statut</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recent_consultations as $consult): ?>
                                <tr>
                                    <td>
                                        <div class="fw-bold"><?php echo htmlspecialchars($consult['name']); ?></div>
                                        <small class="text-muted d-inline-block text-truncate" style="max-width: 150px;"><?php echo htmlspecialchars($consult['topic']); ?></small>
                                    </td>
                                    <td>
                                        <small><?php echo htmlspecialchars($consult['expert_name']); ?></small>
                                    </td>
                                    <td>
                                        <small class="text-muted"><?php echo date('d-m-Y', strtotime($consult['date'])); ?> <?php echo substr($consult['time'], 0, 5); ?></small>
                                    </td>
                                    <td>
                                        <?php if ($consult['status'] == 'Confirmé'): ?>
                                            <span class="pill-status bg-success bg-opacity-10 text-success"><i class="bi bi-check-circle-fill"></i> Confirmé</span>
                                        <?php else: ?>
                                            <span class="pill-status bg-warning bg-opacity-10 text-warning"><i class="bi bi-clock-fill"></i> Attente</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Right panel: recent studies and quick guides -->
    <div class="col-lg-5 mb-4">
        <!-- Recent studies card -->
        <div class="admin-card">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold mb-0 text-success"><i class="bi bi-folder-symlink me-2"></i>Dossiers d'Études Récents</h5>
                <a href="studies.php" class="btn btn-sm btn-link text-decoration-none text-success">Tout voir <i class="bi bi-arrow-right"></i></a>
            </div>
            
            <?php if (empty($recent_studies)): ?>
                <p class="text-muted small my-3 text-center">Aucune demande déposée.</p>
            <?php else: ?>
                <div class="list-group list-group-flush">
                    <?php foreach ($recent_studies as $study): ?>
                        <div class="list-group-item px-0 py-3">
                            <div class="d-flex w-100 justify-content-between mb-1">
                                <h6 class="mb-0 fw-bold"><?php echo htmlspecialchars($study['name']); ?></h6>
                                <small class="text-muted"><?php echo date('d-m-Y', strtotime($study['created_at'])); ?></small>
                            </div>
                            <p class="mb-1 text-muted small text-truncate" style="max-width: 100%;"><?php echo htmlspecialchars($study['description']); ?></p>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="small text-info"><?php echo htmlspecialchars($study['organization']); ?></span>
                                <?php if ($study['status'] == 'Approuvé'): ?>
                                    <span class="pill-status bg-success bg-opacity-10 text-success" style="font-size:0.75rem;"><i class="bi bi-check-circle-fill"></i> Approuvé</span>
                                <?php elseif ($study['status'] == 'Devis envoyé'): ?>
                                    <span class="pill-status bg-info bg-opacity-10 text-info" style="font-size:0.75rem;"><i class="bi bi-file-earmark-medical-fill"></i> Devis</span>
                                <?php else: ?>
                                    <span class="pill-status bg-warning bg-opacity-10 text-warning" style="font-size:0.75rem;"><i class="bi bi-hourglass-split"></i> Reçu</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Quick actions / System Information -->
        <div class="admin-card">
            <h5 class="fw-bold mb-3"><i class="bi bi-shield-lock me-2 text-info"></i>Gestion Système</h5>
            <p class="small text-muted mb-3">Raccourcis rapides et statut système de la plateforme Hydrolicia :</p>
            
            <div class="d-grid gap-2">
                <a href="../chatbot.php" target="_blank" class="btn btn-outline-primary text-start rounded-3 btn-sm py-2">
                    <i class="bi bi-robot me-2 text-info"></i> Tester l'assistant HydroBot AI
                </a>
                <a href="forum.php" class="btn btn-outline-secondary text-start rounded-3 btn-sm py-2">
                    <i class="bi bi-chat-dots-fill me-2 text-warning"></i> Modérer les derniers messages
                </a>
            </div>
            
            <div class="border-top border-light mt-4 pt-3">
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted small">Version PHP</span>
                    <span class="badge bg-secondary-subtle text-secondary-emphasis rounded-pill"><?php echo phpversion(); ?></span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted small">Serveur MySQL</span>
                    <span class="badge bg-secondary-subtle text-secondary-emphasis rounded-pill">Actif (PDO)</span>
                </div>
                <div class="d-flex justify-content-between">
                    <span class="text-muted small">Base de données</span>
                    <span class="badge bg-secondary-subtle text-secondary-emphasis rounded-pill">hydrolicia</span>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
require_once 'includes/footer.php';
?>
