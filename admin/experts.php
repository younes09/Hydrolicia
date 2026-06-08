<?php
// admin/experts.php
require_once '../config/db.php';
require_once 'includes/auth.php';

$message = '';
$err_message = '';

// Handle CRUD operations
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $action = $_POST['action'];
        
        if ($action === 'add') {
            $name = trim($_POST['name']);
            $specialty = trim($_POST['specialty']);
            $bio = trim($_POST['bio']);
            $avatar_class = trim($_POST['avatar_class']);
            $avatar_color_class = trim($_POST['avatar_color_class']);
            $status = trim($_POST['status']);
            
            if (empty($name) || empty($specialty) || empty($bio)) {
                $err_message = "Veuillez remplir tous les champs obligatoires.";
            } else {
                try {
                    // Check if name is unique
                    $check = $pdo->prepare("SELECT COUNT(*) FROM `experts` WHERE `name` = :name");
                    $check->execute(['name' => $name]);
                    if ($check->fetchColumn() > 0) {
                        $err_message = "Un expert avec ce nom existe déjà.";
                    } else {
                        $stmt = $pdo->prepare("INSERT INTO `experts` (`name`, `specialty`, `bio`, `avatar_class`, `avatar_color_class`, `status`) VALUES (:name, :specialty, :bio, :avatar_class, :avatar_color_class, :status)");
                        $stmt->execute([
                            'name' => $name,
                            'specialty' => $specialty,
                            'bio' => $bio,
                            'avatar_class' => $avatar_class,
                            'avatar_color_class' => $avatar_color_class,
                            'status' => $status
                        ]);
                        $message = "L'expert a été ajouté avec succès.";
                    }
                } catch (Exception $e) {
                    $err_message = "Erreur lors de l'ajout : " . $e->getMessage();
                }
            }
        } elseif ($action === 'edit') {
            $id = intval($_POST['id']);
            $name = trim($_POST['name']);
            $specialty = trim($_POST['specialty']);
            $bio = trim($_POST['bio']);
            $avatar_class = trim($_POST['avatar_class']);
            $avatar_color_class = trim($_POST['avatar_color_class']);
            $status = trim($_POST['status']);
            
            if (empty($name) || empty($specialty) || empty($bio)) {
                $err_message = "Veuillez remplir tous les champs obligatoires.";
            } else {
                try {
                    // Check if name is unique to other rows
                    $check = $pdo->prepare("SELECT COUNT(*) FROM `experts` WHERE `name` = :name AND `id` != :id");
                    $check->execute(['name' => $name, 'id' => $id]);
                    if ($check->fetchColumn() > 0) {
                        $err_message = "Un autre expert porte déjà ce nom.";
                    } else {
                        $stmt = $pdo->prepare("UPDATE `experts` SET `name` = :name, `specialty` = :specialty, `bio` = :bio, `avatar_class` = :avatar_class, `avatar_color_class` = :avatar_color_class, `status` = :status WHERE `id` = :id");
                        $stmt->execute([
                            'name' => $name,
                            'specialty' => $specialty,
                            'bio' => $bio,
                            'avatar_class' => $avatar_class,
                            'avatar_color_class' => $avatar_color_class,
                            'status' => $status,
                            'id' => $id
                        ]);
                        $message = "L'expert a été mis à jour avec succès.";
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
        $stmt = $pdo->prepare("DELETE FROM `experts` WHERE `id` = :id");
        $stmt->execute(['id' => $delete_id]);
        $message = "L'expert a été supprimé avec succès.";
    } catch (Exception $e) {
        $err_message = "Erreur lors de la suppression : " . $e->getMessage();
    }
}

// Fetch all experts
try {
    $experts = $pdo->query("SELECT * FROM `experts` ORDER BY `id` DESC")->fetchAll();
} catch (Exception $e) {
    $err_message = "Erreur de base de données : " . $e->getMessage();
    $experts = [];
}

require_once 'includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-1">Gestion des Experts</h2>
        <p class="text-muted mb-0">Administrez la liste des experts hydrauliciens qualifiés disponibles pour les consultations.</p>
    </div>
    <button class="btn btn-primary rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#addExpertModal">
        <i class="bi bi-plus-lg me-2"></i>Ajouter un expert
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
    <?php if (empty($experts)): ?>
        <div class="text-center py-5">
            <i class="bi bi-people fs-1 text-muted"></i>
            <h5 class="mt-3">Aucun expert enregistré</h5>
            <p class="text-muted small">Commencez par ajouter un expert qualifié.</p>
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th style="width: 80px;">Avatar</th>
                        <th>Nom & Spécialité</th>
                        <th>Biographie</th>
                        <th>Statut</th>
                        <th class="text-end" style="width: 150px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($experts as $e): ?>
                        <tr>
                            <td>
                                <?php
                                    // Class mapping for avatar background color
                                    $bg_color_style = '';
                                    if ($e['avatar_color_class'] == 'primary') {
                                        $bg_color_style = 'background-color: rgba(13, 110, 253, 0.1); color: #0d6efd;';
                                    } elseif ($e['avatar_color_class'] == 'warning') {
                                        $bg_color_style = 'background-color: #fff7ed; color: #ea580c;';
                                    } elseif ($e['avatar_color_class'] == 'success') {
                                        $bg_color_style = 'background-color: rgba(25, 135, 84, 0.1); color: #198754;';
                                    } elseif ($e['avatar_color_class'] == 'danger') {
                                        $bg_color_style = 'background-color: rgba(220, 53, 69, 0.1); color: #dc3545;';
                                    } elseif ($e['avatar_color_class'] == 'info') {
                                        $bg_color_style = 'background-color: rgba(13, 202, 240, 0.1); color: #0dcaf0;';
                                    } else {
                                        $bg_color_style = 'background-color: rgba(108, 117, 125, 0.1); color: #6c757d;';
                                    }
                                ?>
                                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; <?php echo $bg_color_style; ?>">
                                    <i class="bi <?php echo htmlspecialchars($e['avatar_class']); ?> fs-4"></i>
                                </div>
                            </td>
                            <td>
                                <h6 class="fw-bold mb-1 text-dark"><?php echo htmlspecialchars($e['name']); ?></h6>
                                <span class="small text-muted fw-semibold"><?php echo htmlspecialchars($e['specialty']); ?></span>
                            </td>
                            <td>
                                <small class="text-muted d-inline-block text-truncate" style="max-width: 350px;" title="<?php echo htmlspecialchars($e['bio']); ?>">
                                    <?php echo htmlspecialchars($e['bio']); ?>
                                </small>
                            </td>
                            <td>
                                <?php if ($e['status'] === 'Disponible'): ?>
                                    <span class="pill-status bg-success bg-opacity-10 text-success"><i class="bi bi-circle-fill me-1 small"></i>Disponible</span>
                                <?php else: ?>
                                    <span class="pill-status bg-danger bg-opacity-10 text-danger"><i class="bi bi-circle-fill me-1 small"></i>Indisponible</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end">
                                <div class="btn-group gap-1">
                                    <button class="action-btn btn-edit" 
                                            data-id="<?php echo $e['id']; ?>"
                                            data-name="<?php echo htmlspecialchars($e['name']); ?>"
                                            data-specialty="<?php echo htmlspecialchars($e['specialty']); ?>"
                                            data-bio="<?php echo htmlspecialchars($e['bio']); ?>"
                                            data-avatar_class="<?php echo htmlspecialchars($e['avatar_class']); ?>"
                                            data-avatar_color_class="<?php echo htmlspecialchars($e['avatar_color_class']); ?>"
                                            data-status="<?php echo htmlspecialchars($e['status']); ?>"
                                            data-bs-toggle="modal" 
                                            data-bs-target="#editExpertModal"
                                            title="Modifier l'expert">
                                        <i class="bi bi-pencil-fill"></i>
                                    </button>
                                    <a href="experts.php?delete=<?php echo $e['id']; ?>" 
                                       class="action-btn action-btn-danger" 
                                       onclick="return confirm('Voulez-vous vraiment supprimer définitivement cet expert ?')"
                                       title="Supprimer l'expert">
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

<!-- Add Expert Modal -->
<div class="modal fade" id="addExpertModal" tabindex="-1" aria-labelledby="addExpertModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header bg-primary text-white border-0 py-3 rounded-top-4">
                <h5 class="modal-title fw-bold" id="addExpertModalLabel"><i class="bi bi-person-plus me-2"></i>Ajouter un expert</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="experts.php" method="POST">
                <input type="hidden" name="action" value="add">
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label text-muted small fw-bold">Nom complet (Titre inclus)</label>
                            <input type="text" name="name" class="form-control" placeholder="Ex: Dr. Salim Rahal, Ing. Karima Ould-Kadi" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small fw-bold">Spécialité / Sous-titre</label>
                            <input type="text" name="specialty" class="form-control" placeholder="Ex: Modélisation AEP, Réseaux sous pression" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small fw-bold">Icône d'avatar (Bootstrap Icon class)</label>
                            <select name="avatar_class" class="form-select">
                                <option value="bi-person-fill-gear">Engrenage (bi-person-fill-gear)</option>
                                <option value="bi-person-fill-lock">Cadenas (bi-person-fill-lock)</option>
                                <option value="bi-person-fill-check">Coche (bi-person-fill-check)</option>
                                <option value="bi-person-fill">Standard (bi-person-fill)</option>
                                <option value="bi-award-fill">Badge/Prix (bi-award-fill)</option>
                                <option value="bi-mortarboard-fill">Diplômé (bi-mortarboard-fill)</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small fw-bold">Couleur visuelle de l'avatar</label>
                            <select name="avatar_color_class" class="form-select">
                                <option value="primary">Bleu (Primary)</option>
                                <option value="warning">Orange (Warning)</option>
                                <option value="success">Vert (Success)</option>
                                <option value="danger">Rouge (Danger)</option>
                                <option value="info">Cyan (Info)</option>
                                <option value="secondary">Gris (Secondary)</option>
                            </select>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label text-muted small fw-bold">Statut de disponibilité</label>
                            <select name="status" class="form-select">
                                <option value="Disponible">Disponible</option>
                                <option value="Indisponible">Indisponible</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label text-muted small fw-bold">Biographie / Expérience détaillée</label>
                            <textarea name="bio" class="form-control" rows="4" placeholder="Décrivez le parcours de l'expert, son nombre d'années d'expérience et ses projets majeurs..." required></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-3 bg-light rounded-bottom-4">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-3" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4">Créer le profil expert <i class="bi bi-check-lg ms-1"></i></button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Expert Modal -->
<div class="modal fade" id="editExpertModal" tabindex="-1" aria-labelledby="editExpertModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header bg-primary text-white border-0 py-3 rounded-top-4">
                <h5 class="modal-title fw-bold" id="editExpertModalLabel"><i class="bi bi-pencil-square me-2"></i>Modifier l'expert</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="experts.php" method="POST">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" name="id" id="edit_id">
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label text-muted small fw-bold">Nom complet (Titre inclus)</label>
                            <input type="text" name="name" id="edit_name" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small fw-bold">Spécialité / Sous-titre</label>
                            <input type="text" name="specialty" id="edit_specialty" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small fw-bold">Icône d'avatar (Bootstrap Icon class)</label>
                            <select name="avatar_class" id="edit_avatar_class" class="form-select">
                                <option value="bi-person-fill-gear">Engrenage (bi-person-fill-gear)</option>
                                <option value="bi-person-fill-lock">Cadenas (bi-person-fill-lock)</option>
                                <option value="bi-person-fill-check">Coche (bi-person-fill-check)</option>
                                <option value="bi-person-fill">Standard (bi-person-fill)</option>
                                <option value="bi-award-fill">Badge/Prix (bi-award-fill)</option>
                                <option value="bi-mortarboard-fill">Diplômé (bi-mortarboard-fill)</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small fw-bold">Couleur visuelle de l'avatar</label>
                            <select name="avatar_color_class" id="edit_avatar_color_class" class="form-select">
                                <option value="primary">Bleu (Primary)</option>
                                <option value="warning">Orange (Warning)</option>
                                <option value="success">Vert (Success)</option>
                                <option value="danger">Rouge (Danger)</option>
                                <option value="info">Cyan (Info)</option>
                                <option value="secondary">Gris (Secondary)</option>
                            </select>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label text-muted small fw-bold">Statut de disponibilité</label>
                            <select name="status" id="edit_status" class="form-select">
                                <option value="Disponible">Disponible</option>
                                <option value="Indisponible">Indisponible</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label text-muted small fw-bold">Biographie / Expérience détaillée</label>
                            <textarea name="bio" id="edit_bio" class="form-control" rows="4" required></textarea>
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
            document.getElementById('edit_name').value = this.getAttribute('data-name');
            document.getElementById('edit_specialty').value = this.getAttribute('data-specialty');
            document.getElementById('edit_bio').value = this.getAttribute('data-bio');
            document.getElementById('edit_avatar_class').value = this.getAttribute('data-avatar_class');
            document.getElementById('edit_avatar_color_class').value = this.getAttribute('data-avatar_color_class');
            document.getElementById('edit_status').value = this.getAttribute('data-status');
        });
    });
});
</script>

<?php
require_once 'includes/footer.php';
?>
