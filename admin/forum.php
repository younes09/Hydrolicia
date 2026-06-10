<?php
// admin/forum.php
require_once '../config/db.php';
require_once 'includes/header.php';

$message = '';
$err_message = '';

// Handle deletion of question
if (isset($_GET['delete_q'])) {
    $q_id = intval($_GET['delete_q']);
    try {
        $stmt = $pdo->prepare("DELETE FROM `forum_questions` WHERE `id` = :id");
        $stmt->execute(['id' => $q_id]);
        $message = "Le sujet du forum et toutes ses réponses associées ont été supprimés.";
    } catch (Exception $e) {
        $err_message = "Impossible de supprimer le sujet : " . $e->getMessage();
    }
}

// Handle deletion of reply
if (isset($_GET['delete_r'])) {
    $r_id = intval($_GET['delete_r']);
    try {
        $stmt = $pdo->prepare("DELETE FROM `forum_replies` WHERE `id` = :id");
        $stmt->execute(['id' => $r_id]);
        $message = "La réponse a été supprimée avec succès.";
    } catch (Exception $e) {
        $err_message = "Impossible de supprimer la réponse : " . $e->getMessage();
    }
}

// Fetch tab choice
$tab = isset($_GET['tab']) ? $_GET['tab'] : 'questions';

// Fetch all questions
try {
    $questions = $pdo->query("SELECT * FROM `forum_questions` ORDER BY `id` DESC")->fetchAll();
} catch (Exception $e) {
    $questions = [];
}

// Fetch all replies
try {
    $replies = $pdo->query("
        SELECT r.*, q.title AS question_title 
        FROM `forum_replies` r 
        LEFT JOIN `forum_questions` q ON r.question_id = q.id 
        ORDER BY r.id DESC
    ")->fetchAll();
} catch (Exception $e) {
    $replies = [];
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-1">Modération du Forum</h2>
        <p class="text-muted mb-0">Consultez et modérez les messages de la communauté pour préserver un espace d'échange technique constructif.</p>
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

<!-- Tabs navigation -->
<ul class="nav nav-pills mb-4" id="forumTabs" role="tablist">
    <li class="nav-item" role="presentation">
        <a href="forum.php?tab=questions" class="nav-link rounded-pill px-4 <?php echo ($tab == 'questions') ? 'active' : ''; ?>">
            <i class="bi bi-chat-left-dots me-2"></i> Sujets de discussion (<?php echo count($questions); ?>)
        </a>
    </li>
    <li class="nav-item" role="presentation">
        <a href="forum.php?tab=replies" class="nav-link rounded-pill px-4 <?php echo ($tab == 'replies') ? 'active' : ''; ?>">
            <i class="bi bi-chat-left-quote me-2"></i> Réponses postées (<?php echo count($replies); ?>)
        </a>
    </li>
</ul>

<div class="tab-content" id="forumTabsContent">
    <!-- Tab 1: Questions -->
    <?php if ($tab == 'questions'): ?>
        <div class="admin-card">
            <?php if (empty($questions)): ?>
                <div class="text-center py-5">
                    <i class="bi bi-chat-square-x fs-1 text-muted"></i>
                    <h5 class="mt-3">Aucun sujet de discussion</h5>
                    <p class="text-muted small">Aucun sujet n'a encore été créé sur le forum.</p>
                </div>
            <?php else: ?>
                <!-- Search & Filter Area -->
                <div class="row g-3 mb-4 align-items-center">
                    <div class="col-md-7">
                        <div class="position-relative">
                            <input type="text" id="adminQuestionSearch" class="form-control rounded-pill bg-light border-0 px-4 py-2 text-dark" placeholder="Rechercher par auteur, sujet, contenu..." style="font-size: 0.9rem;">
                            <i class="bi bi-search text-muted position-absolute end-0 top-50 translate-middle-y me-3"></i>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <select id="adminQuestionCategory" class="form-select rounded-pill bg-light border-0 px-3 py-2 text-secondary" style="font-size: 0.9rem;">
                            <option value="all">Toutes les thématiques</option>
                            <option value="AEP (Alimentation en Eau Potable)">AEP (Alimentation en Eau Potable)</option>
                            <option value="Assainissement & Environnement">Assainissement & Environnement</option>
                            <option value="Irrigation & Économie d'eau">Irrigation & Économie d'eau</option>
                            <option value="Ouvrages Hydrauliques & Crues">Ouvrages Hydrauliques & Crues</option>
                        </select>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Auteur / Rôle</th>
                                <th>Sujet & Catégorie</th>
                                <th>Message</th>
                                <th>Date</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- No Results message row -->
                            <tr id="adminQuestionsNoResults" class="d-none">
                                <td colspan="5" class="text-center py-4 text-muted">
                                    <i class="bi bi-search fs-4 d-block mb-2 text-info"></i>
                                    Aucun sujet ne correspond à votre recherche.
                                </td>
                            </tr>
                            <?php foreach ($questions as $q): ?>
                                <tr class="question-row" 
                                    data-category="<?php echo htmlspecialchars($q['category'], ENT_QUOTES); ?>"
                                    data-author="<?php echo htmlspecialchars($q['author'], ENT_QUOTES); ?>"
                                    data-title="<?php echo htmlspecialchars($q['title'], ENT_QUOTES); ?>"
                                    data-content="<?php echo htmlspecialchars($q['content'], ENT_QUOTES); ?>">
                                    <td data-label="Auteur">
                                        <div class="fw-bold"><?php echo htmlspecialchars($q['author']); ?></div>
                                        <span class="badge-role <?php 
                                            if ($q['role'] == 'Étudiant' || $q['role'] == 'Etudiant') echo 'badge-student';
                                            elseif ($q['role'] == 'Expert' || $q['role'] == 'Expert Hydraulique') echo 'badge-expert';
                                            else echo 'badge-professional';
                                        ?>">
                                            <?php echo htmlspecialchars($q['role']); ?>
                                        </span>
                                    </td>
                                    <td data-label="Sujet & Catégorie">
                                        <div class="fw-bold text-dark"><?php echo htmlspecialchars($q['title']); ?></div>
                                        <small class="text-info"><?php echo htmlspecialchars($q['category']); ?></small>
                                    </td>
                                    <td data-label="Message">
                                        <span class="d-inline-block text-truncate text-muted" style="max-width: 250px;" title="<?php echo htmlspecialchars($q['content']); ?>">
                                            <?php echo htmlspecialchars($q['content']); ?>
                                        </span>
                                        <button type="button" class="btn btn-sm btn-link p-0 d-block text-start text-decoration-none small" 
                                                onclick="showDetailModal(this)"
                                                data-author="<?php echo htmlspecialchars($q['author'], ENT_QUOTES); ?>"
                                                data-content="<?php echo htmlspecialchars($q['content'], ENT_QUOTES); ?>">
                                            Afficher tout
                                        </button>
                                    </td>
                                    <td data-label="Date">
                                        <small class="text-muted"><i class="bi bi-calendar3 me-1"></i><?php echo date('d-m-Y', strtotime($q['created_at'])); ?></small>
                                    </td>
                                    <td data-label="Actions" class="text-end">
                                        <a href="forum.php?tab=questions&delete_q=<?php echo $q['id']; ?>" 
                                           class="action-btn action-btn-danger" 
                                           onclick="return confirm('Êtes-vous certain de vouloir supprimer ce sujet du forum ? Cela supprimera également TOUTES les réponses associées.')"
                                           data-bs-toggle="tooltip" 
                                           data-bs-placement="top" 
                                           title="Supprimer le sujet">
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
    <?php endif; ?>

    <!-- Tab 2: Replies -->
    <?php if ($tab == 'replies'): ?>
        <div class="admin-card">
            <?php if (empty($replies)): ?>
                <div class="text-center py-5">
                    <i class="bi bi-chat-square-quote fs-1 text-muted"></i>
                    <h5 class="mt-3">Aucune réponse rédigée</h5>
                    <p class="text-muted small">Aucune contribution n'a été rédigée sur le forum.</p>
                </div>
            <?php else: ?>
                <!-- Search Area -->
                <div class="row g-3 mb-4">
                    <div class="col-12">
                        <div class="position-relative">
                            <input type="text" id="adminReplySearch" class="form-control rounded-pill bg-light border-0 px-4 py-2 text-dark" placeholder="Rechercher par auteur, sujet original ou contenu de la réponse..." style="font-size: 0.9rem;">
                            <i class="bi bi-search text-muted position-absolute end-0 top-50 translate-middle-y me-3"></i>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Auteur / Rôle</th>
                                <th>Sujet original</th>
                                <th>Réponse rédigée</th>
                                <th>Date de publication</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- No Results message row -->
                            <tr id="adminRepliesNoResults" class="d-none">
                                <td colspan="5" class="text-center py-4 text-muted">
                                    <i class="bi bi-search fs-4 d-block mb-2 text-info"></i>
                                    Aucune réponse ne correspond à votre recherche.
                                </td>
                            </tr>
                            <?php foreach ($replies as $r): ?>
                                <tr class="reply-row" 
                                    data-author="<?php echo htmlspecialchars($r['author'], ENT_QUOTES); ?>"
                                    data-subject="<?php echo htmlspecialchars($r['question_title'] ?? '', ENT_QUOTES); ?>"
                                    data-content="<?php echo htmlspecialchars($r['content'], ENT_QUOTES); ?>">
                                    <td data-label="Auteur">
                                        <div class="fw-bold"><?php echo htmlspecialchars($r['author']); ?></div>
                                        <span class="badge-role <?php 
                                            if ($r['role'] == 'Étudiant' || $r['role'] == 'Etudiant') echo 'badge-student';
                                            elseif ($r['role'] == 'Expert' || $r['role'] == 'Expert Hydraulique') echo 'badge-expert';
                                            else echo 'badge-professional';
                                        ?>">
                                            <?php echo htmlspecialchars($r['role']); ?>
                                        </span>
                                    </td>
                                    <td data-label="Sujet original">
                                        <div class="text-muted small text-truncate" style="max-width: 180px;">
                                            <?php if ($r['question_title']): ?>
                                                <i class="bi bi-link-45deg me-1"></i><?php echo htmlspecialchars($r['question_title']); ?>
                                            <?php else: ?>
                                                <span class="text-danger"><i class="bi bi-exclamation-circle me-1"></i>Sujet supprimé</span>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td data-label="Réponse">
                                        <span class="d-inline-block text-truncate text-muted" style="max-width: 280px;" title="<?php echo htmlspecialchars($r['content']); ?>">
                                            <?php echo htmlspecialchars($r['content']); ?>
                                        </span>
                                        <button type="button" class="btn btn-sm btn-link p-0 d-block text-start text-decoration-none small" 
                                                onclick="showDetailModal(this)"
                                                data-author="<?php echo htmlspecialchars($r['author'], ENT_QUOTES); ?>"
                                                data-content="<?php echo htmlspecialchars($r['content'], ENT_QUOTES); ?>">
                                            Afficher tout
                                        </button>
                                    </td>
                                    <td data-label="Date">
                                        <small class="text-muted"><i class="bi bi-calendar3 me-1"></i><?php echo date('d-m-Y H:i', strtotime($r['created_at'])); ?></small>
                                    </td>
                                    <td data-label="Actions" class="text-end">
                                        <a href="forum.php?tab=replies&delete_r=<?php echo $r['id']; ?>" 
                                           class="action-btn action-btn-danger" 
                                           onclick="return confirm('Souhaitez-vous vraiment supprimer définitivement cette réponse ?')"
                                           data-bs-toggle="tooltip" 
                                           data-bs-placement="top" 
                                           title="Supprimer la réponse">
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
    <?php endif; ?>
</div>

<!-- Modal for viewing full question/reply contents -->
<div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header bg-primary text-white border-0 py-3 rounded-top-4">
                <h5 class="modal-title fw-bold" id="detailModalLabel">Contenu complet du message</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-3">
                    <label class="form-label text-muted small fw-bold mb-1">Auteur :</label>
                    <div id="modalAuthor" class="fw-bold text-dark fs-6"></div>
                </div>
                <div class="mb-2">
                    <label class="form-label text-muted small fw-bold mb-1">Message :</label>
                    <div id="modalTextContent" class="bg-light p-3 rounded-3 text-secondary" style="white-space: pre-wrap; font-size: 0.95rem; line-height: 1.6;"></div>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Fermer</button>
            </div>
        </div>
    </div>
</div>

<script>
let detailModal;

document.addEventListener('DOMContentLoaded', function() {
    detailModal = new bootstrap.Modal(document.getElementById('detailModal'));

    // Admin filtering for questions
    const qSearch = document.getElementById('adminQuestionSearch');
    const qCategory = document.getElementById('adminQuestionCategory');
    const qNoResults = document.getElementById('adminQuestionsNoResults');
    const qRows = document.querySelectorAll('.question-row');

    function filterAdminQuestions() {
        if (!qRows.length) return;
        const query = qSearch ? qSearch.value.toLowerCase().trim() : '';
        const cat = qCategory ? qCategory.value : 'all';
        let visibleCount = 0;

        qRows.forEach(row => {
            const author = row.getAttribute('data-author').toLowerCase();
            const title = row.getAttribute('data-title').toLowerCase();
            const content = row.getAttribute('data-content').toLowerCase();
            const category = row.getAttribute('data-category');

            const matchesCategory = (cat === 'all' || category === cat);
            const matchesQuery = (query === '' || 
                                  author.includes(query) || 
                                  title.includes(query) || 
                                  content.includes(query));

            if (matchesCategory && matchesQuery) {
                row.classList.remove('d-none');
                visibleCount++;
            } else {
                row.classList.add('d-none');
            }
        });

        if (qNoResults) {
            if (visibleCount === 0) {
                qNoResults.classList.remove('d-none');
            } else {
                qNoResults.classList.add('d-none');
            }
        }
    }

    if (qSearch) qSearch.addEventListener('input', filterAdminQuestions);
    if (qCategory) qCategory.addEventListener('change', filterAdminQuestions);

    // Admin filtering for replies
    const rSearch = document.getElementById('adminReplySearch');
    const rNoResults = document.getElementById('adminRepliesNoResults');
    const rRows = document.querySelectorAll('.reply-row');

    function filterAdminReplies() {
        if (!rRows.length) return;
        const query = rSearch ? rSearch.value.toLowerCase().trim() : '';
        let visibleCount = 0;

        rRows.forEach(row => {
            const author = row.getAttribute('data-author').toLowerCase();
            const subject = row.getAttribute('data-subject').toLowerCase();
            const content = row.getAttribute('data-content').toLowerCase();

            const matchesQuery = (query === '' || 
                                  author.includes(query) || 
                                  subject.includes(query) || 
                                  content.includes(query));

            if (matchesQuery) {
                row.classList.remove('d-none');
                visibleCount++;
            } else {
                row.classList.add('d-none');
            }
        });

        if (rNoResults) {
            if (visibleCount === 0) {
                rNoResults.classList.remove('d-none');
            } else {
                rNoResults.classList.add('d-none');
            }
        }
    }

    if (rSearch) rSearch.addEventListener('input', filterAdminReplies);
});

function showDetailModal(btn) {
    document.getElementById('modalAuthor').innerText = btn.dataset.author;
    document.getElementById('modalTextContent').innerText = btn.dataset.content;
    detailModal.show();
}
</script>

<?php
require_once 'includes/footer.php';
?>
