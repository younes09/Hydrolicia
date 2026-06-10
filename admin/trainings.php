<?php
// admin/trainings.php
require_once '../config/db.php';
require_once 'includes/auth.php';

$message = '';
$err_message = '';

// Handle CRUD operations
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $action = $_POST['action'];
        
        if ($action === 'add') {
            $code = trim($_POST['code']);
            $title = trim($_POST['title']);
            $duration = trim($_POST['duration']);
            $description = trim($_POST['description']);
            $price = trim($_POST['price']);
            $badge = trim($_POST['badge']);
            $badge_style = trim($_POST['badge_style']);
            $program = trim($_POST['program']);
            
            if (empty($code) || empty($title) || empty($duration) || empty($description) || empty($price) || empty($program)) {
                $err_message = "Veuillez remplir tous les champs obligatoires.";
            } else {
                try {
                    // Check if code is unique
                    $check = $pdo->prepare("SELECT COUNT(*) FROM `trainings` WHERE `code` = :code");
                    $check->execute(['code' => $code]);
                    if ($check->fetchColumn() > 0) {
                        $err_message = "Ce code de formation existe déjà. Veuillez en choisir un autre.";
                    } else {
                        $stmt = $pdo->prepare("INSERT INTO `trainings` (`code`, `title`, `duration`, `description`, `price`, `badge`, `badge_style`, `program`) VALUES (:code, :title, :duration, :description, :price, :badge, :badge_style, :program)");
                        $stmt->execute([
                            'code' => $code,
                            'title' => $title,
                            'duration' => $duration,
                            'description' => $description,
                            'price' => $price,
                            'badge' => $badge,
                            'badge_style' => $badge_style,
                            'program' => $program
                        ]);
                        $message = "La formation a été ajoutée avec succès.";
                    }
                } catch (Exception $e) {
                    $err_message = "Erreur lors de l'ajout : " . $e->getMessage();
                }
            }
        } elseif ($action === 'edit') {
            $id = intval($_POST['id']);
            $code = trim($_POST['code']);
            $title = trim($_POST['title']);
            $duration = trim($_POST['duration']);
            $description = trim($_POST['description']);
            $price = trim($_POST['price']);
            $badge = trim($_POST['badge']);
            $badge_style = trim($_POST['badge_style']);
            $program = trim($_POST['program']);
            
            if (empty($code) || empty($title) || empty($duration) || empty($description) || empty($price) || empty($program)) {
                $err_message = "Veuillez remplir tous les champs obligatoires.";
            } else {
                try {
                    // Check if code is unique to other rows
                    $check = $pdo->prepare("SELECT COUNT(*) FROM `trainings` WHERE `code` = :code AND `id` != :id");
                    $check->execute(['code' => $code, 'id' => $id]);
                    if ($check->fetchColumn() > 0) {
                        $err_message = "Ce code de formation est déjà utilisé par une autre formation.";
                    } else {
                        $stmt = $pdo->prepare("UPDATE `trainings` SET `code` = :code, `title` = :title, `duration` = :duration, `description` = :description, `price` = :price, `badge` = :badge, `badge_style` = :badge_style, `program` = :program WHERE `id` = :id");
                        $stmt->execute([
                            'code' => $code,
                            'title' => $title,
                            'duration' => $duration,
                            'description' => $description,
                            'price' => $price,
                            'badge' => $badge,
                            'badge_style' => $badge_style,
                            'program' => $program,
                            'id' => $id
                        ]);
                        $message = "La formation a été mise à jour avec succès.";
                    }
                } catch (Exception $e) {
                    $err_message = "Erreur lors de la mise à jour : " . $e->getMessage();
                }
            }
        }
    }
}

if (isset($_GET['delete'])) {
    $delete_id = intval($_GET['delete']);
    try {
        $stmt = $pdo->prepare("DELETE FROM `trainings` WHERE `id` = :id");
        $stmt->execute(['id' => $delete_id]);
        $message = "La formation a été supprimée avec succès.";
    } catch (Exception $e) {
        $err_message = "Erreur lors de la suppression : " . $e->getMessage();
    }
}

// Fetch all trainings
try {
    $trainings = $pdo->query("SELECT * FROM `trainings` ORDER BY `id` DESC")->fetchAll();
} catch (Exception $e) {
    $err_message = "Erreur de base de données : " . $e->getMessage();
    $trainings = [];
}

require_once 'includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-1">Catalogue de Formations</h2>
        <p class="text-muted mb-0">Gérez le catalogue des formations professionnelles et pratiques proposées sur le site.</p>
    </div>
    <button class="btn btn-primary rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#addTrainingModal">
        <i class="bi bi-plus-lg me-2"></i>Ajouter une formation
    </button>
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

<!-- List Card -->
<div class="admin-card">
    <?php if (empty($trainings)): ?>
        <div class="text-center py-5">
            <i class="bi bi-journal-x fs-1 text-muted"></i>
            <h5 class="mt-3">Aucune formation enregistrée</h5>
            <p class="text-muted small">Commencez par ajouter un nouveau module pratique.</p>
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover align-middle text-nowrap">
                <thead>
                    <tr>
                        <th style="width: 100px;">Code / ID</th>
                        <th>Badge & Formation</th>
                        <th>Description</th>
                        <th>Durée & Tarif</th>
                        <th class="text-end" style="width: 150px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($trainings as $t): ?>
                        <tr>
                            <td data-label="Code">
                                <code class="bg-light text-primary px-2 py-1 rounded small fw-bold"><?php echo htmlspecialchars($t['code']); ?></code>
                            </td>
                            <td data-label="Formation">
                                <div class="mb-1">
                                    <?php if (!empty($t['badge'])): ?>
                                        <span class="badge <?php echo htmlspecialchars($t['badge_style']); ?> rounded-pill px-2 py-1 small me-1">
                                            <?php echo htmlspecialchars($t['badge']); ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                                <h6 class="fw-bold mb-0 text-dark"><?php echo htmlspecialchars($t['title']); ?></h6>
                            </td>
                            <td data-label="Description">
                                <small class="text-muted d-inline-block text-truncate" style="max-width: 320px;" title="<?php echo htmlspecialchars($t['description']); ?>">
                                    <?php echo htmlspecialchars($t['description']); ?>
                                </small>
                            </td>
                            <td data-label="Durée & Tarif">
                                <div class="small fw-semibold text-primary"><i class="bi bi-clock me-1 text-muted"></i><?php echo htmlspecialchars($t['duration']); ?></div>
                                <div class="small fw-bold text-success"><i class="bi bi-tag me-1 text-muted"></i><?php echo htmlspecialchars($t['price']); ?></div>
                            </td>
                            <td data-label="Actions" class="text-end">
                                <div class="btn-group gap-1">
                                    <button class="action-btn btn-edit" 
                                            data-id="<?php echo $t['id']; ?>"
                                            data-code="<?php echo htmlspecialchars($t['code']); ?>"
                                            data-title="<?php echo htmlspecialchars($t['title']); ?>"
                                            data-duration="<?php echo htmlspecialchars($t['duration']); ?>"
                                            data-description="<?php echo htmlspecialchars($t['description']); ?>"
                                            data-price="<?php echo htmlspecialchars($t['price']); ?>"
                                            data-badge="<?php echo htmlspecialchars($t['badge']); ?>"
                                            data-badge_style="<?php echo htmlspecialchars($t['badge_style']); ?>"
                                            data-program="<?php echo htmlspecialchars($t['program']); ?>"
                                            data-bs-toggle="modal" 
                                            data-bs-target="#editTrainingModal"
                                            title="Modifier la formation">
                                        <i class="bi bi-pencil-fill"></i>
                                    </button>
                                    <a href="trainings.php?delete=<?php echo $t['id']; ?>" 
                                       class="action-btn action-btn-danger" 
                                       onclick="return confirm('Voulez-vous vraiment supprimer définitivement cette formation ?\nAttention, les inscriptions liées à ce code ne seront pas supprimées mais ne correspondront plus à un module actif.')"
                                       title="Supprimer la formation">
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

<!-- Add Training Modal -->
<div class="modal fade" id="addTrainingModal" tabindex="-1" aria-labelledby="addTrainingModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header bg-primary text-white border-0 py-3 rounded-top-4">
                <h5 class="modal-title fw-bold" id="addTrainingModalLabel"><i class="bi bi-journal-plus me-2"></i>Ajouter une formation</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="trainings.php" method="POST">
                <input type="hidden" name="action" value="add">
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label text-muted small fw-bold">Code unique (Slug)</label>
                            <input type="text" name="code" class="form-control" placeholder="Ex: aep, assainissement, ciment" required pattern="^[a-zA-Z0-9_-]+$">
                            <div class="form-text">Ce code identifie la formation et lie les inscriptions (lettres, chiffres, tirets).</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small fw-bold">Titre de la formation</label>
                            <input type="text" name="title" class="form-control" placeholder="Ex: Alimentation en Eau Potable (EPANET)" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small fw-bold">Durée</label>
                            <input type="text" name="duration" class="form-control" placeholder="Ex: 24 Heures, 3 Semaines" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small fw-bold">Tarif</label>
                            <input type="text" name="price" class="form-control" placeholder="Ex: 15 000 DA" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small fw-bold">Texte du badge</label>
                            <input type="text" name="badge" class="form-control" placeholder="Ex: Modélisation AEP, Nouveau">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small fw-bold">Style visuel du badge</label>
                            <select name="badge_style" class="form-select">
                                <option value="bg-primary text-white">Bleu (Primary)</option>
                                <option value="bg-success text-white">Vert (Success)</option>
                                <option value="bg-warning text-dark">Jaune (Warning)</option>
                                <option value="bg-danger text-white">Rouge (Danger)</option>
                                <option value="bg-info text-dark">Bleu clair (Info)</option>
                                <option value="bg-secondary text-white">Gris (Secondary)</option>
                                <option value="bg-dark text-white">Noir (Dark)</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label text-muted small fw-bold">Description courte</label>
                            <textarea name="description" class="form-control" rows="2" placeholder="Décrivez succinctement les objectifs de la formation..." required></textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label text-muted small fw-bold">Programme clé (Un point par ligne)</label>
                            <textarea name="program" class="form-control" rows="5" placeholder="Ex:&#10;Calcul des débits de pointe & dimensionnement&#10;Calcul des réservoirs de stockage&#10;Modélisation sous EPANET" required></textarea>
                            <div class="form-text">Entrez les étapes majeures du programme. Séparez chaque étape par un retour à la ligne.</div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-3 bg-light rounded-bottom-4">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-3" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4">Créer la formation <i class="bi bi-check-lg ms-1"></i></button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Training Modal -->
<div class="modal fade" id="editTrainingModal" tabindex="-1" aria-labelledby="editTrainingModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header bg-primary text-white border-0 py-3 rounded-top-4">
                <h5 class="modal-title fw-bold" id="editTrainingModalLabel"><i class="bi bi-pencil-square me-2"></i>Modifier la formation</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="trainings.php" method="POST">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" name="id" id="edit_id">
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label text-muted small fw-bold">Code unique (Slug)</label>
                            <input type="text" name="code" id="edit_code" class="form-control" required pattern="^[a-zA-Z0-9_-]+$">
                            <div class="form-text">Ce code identifie la formation et lie les inscriptions.</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small fw-bold">Titre de la formation</label>
                            <input type="text" name="title" id="edit_title" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small fw-bold">Durée</label>
                            <input type="text" name="duration" id="edit_duration" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small fw-bold">Tarif</label>
                            <input type="text" name="price" id="edit_price" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small fw-bold">Texte du badge</label>
                            <input type="text" name="badge" id="edit_badge" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small fw-bold">Style visuel du badge</label>
                            <select name="badge_style" id="edit_badge_style" class="form-select">
                                <option value="bg-primary text-white">Bleu (Primary)</option>
                                <option value="bg-success text-white">Vert (Success)</option>
                                <option value="bg-warning text-dark">Jaune (Warning)</option>
                                <option value="bg-danger text-white">Rouge (Danger)</option>
                                <option value="bg-info text-dark">Bleu clair (Info)</option>
                                <option value="bg-secondary text-white">Gris (Secondary)</option>
                                <option value="bg-dark text-white">Noir (Dark)</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label text-muted small fw-bold">Description courte</label>
                            <textarea name="description" id="edit_description" class="form-control" rows="2" required></textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label text-muted small fw-bold">Programme clé (Un point par ligne)</label>
                            <textarea name="program" id="edit_program" class="form-control" rows="5" required></textarea>
                            <div class="form-text">Chaque ligne correspond à un point clé à puces dans l'affichage public.</div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-3 bg-light rounded-bottom-4">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-3" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4">Sauvegarder les modifications <i class="bi bi-save ms-1"></i></button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const editButtons = document.querySelectorAll('.btn-edit');
    editButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            document.getElementById('edit_id').value = this.getAttribute('data-id');
            document.getElementById('edit_code').value = this.getAttribute('data-code');
            document.getElementById('edit_title').value = this.getAttribute('data-title');
            document.getElementById('edit_duration').value = this.getAttribute('data-duration');
            document.getElementById('edit_description').value = this.getAttribute('data-description');
            document.getElementById('edit_price').value = this.getAttribute('data-price');
            document.getElementById('edit_badge').value = this.getAttribute('data-badge');
            document.getElementById('edit_badge_style').value = this.getAttribute('data-badge_style');
            document.getElementById('edit_program').value = this.getAttribute('data-program');
        });
    });
});
</script>

<?php
require_once 'includes/footer.php';
?>
