<?php
require_once 'config/db.php';
include_once 'includes/header.php';

// Fetch all questions with reply counts
$questions = [];
try {
    $stmt = $pdo->query("
        SELECT q.*, COUNT(r.id) as reply_count 
        FROM `forum_questions` q 
        LEFT JOIN `forum_replies` r ON q.id = r.question_id 
        GROUP BY q.id 
        ORDER BY q.id DESC
    ");
    $questions = $stmt->fetchAll();
} catch (Exception $e) {
    // Keep empty
}
?>

<div class="container my-5">
    <!-- Header -->
    <div class="text-center mb-5">
        <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill mb-2">Espace d'Échange</span>
        <h1 class="fw-bold text-primary display-5">Communauté Hydraulique</h1>
        <p class="text-muted max-w-lg mx-auto">
            Étudiants, jeunes diplômés et ingénieurs séniors partagent leurs connaissances et s'entraident sur les problématiques d'ingénierie et de recherche en Algérie.
        </p>
    </div>

    <div class="row g-4">
        <!-- Sidebar filters & Ask trigger -->
        <div class="col-lg-3">
            <!-- Ask Question Card CTA -->
            <button class="btn btn-info w-100 rounded-pill py-3 text-dark font-weight-bold shadow-sm mb-4" data-bs-toggle="collapse" data-bs-target="#askQuestionCollapse" aria-expanded="false" aria-controls="askQuestionCollapse">
                <i class="bi bi-patch-question me-2"></i>Poser une Question
            </button>

            <!-- Categories Card -->
            <div class="card bg-white border border-light shadow-sm rounded-4 p-4 sticky-lg-top" style="top: 100px; z-index: 10;">
                <h5 class="fw-bold mb-3 text-primary"><i class="bi bi-funnel-fill me-2"></i>Catégories</h5>
                <div class="d-flex flex-column gap-2">
                    <a href="#" class="category-filter active btn btn-sm bg-primary text-white text-start rounded-pill py-2 px-3 fw-semibold" data-category="all">
                        <i class="bi bi-grid-fill me-2"></i>Toutes les thématiques
                    </a>
                    <a href="#" class="category-filter btn btn-sm bg-light text-dark text-start rounded-pill py-2 px-3 fw-semibold" data-category="AEP (Alimentation en Eau Potable)">
                        <i class="bi bi-droplet me-2 text-info"></i>Alimentation Eau Potable
                    </a>
                    <a href="#" class="category-filter btn btn-sm bg-light text-dark text-start rounded-pill py-2 px-3 fw-semibold" data-category="Assainissement & Environnement">
                        <i class="bi bi-filter-square me-2 text-warning" style="color:#d97706 !important;"></i>Assainissement & STEP
                    </a>
                    <a href="#" class="category-filter btn btn-sm bg-light text-dark text-start rounded-pill py-2 px-3 fw-semibold" data-category="Irrigation & Économie d'eau">
                        <i class="bi bi-flower1 me-2 text-success"></i>Irrigation & REUSE
                    </a>
                    <a href="#" class="category-filter btn btn-sm bg-light text-dark text-start rounded-pill py-2 px-3 fw-semibold" data-category="Ouvrages Hydrauliques & Crues">
                        <i class="bi bi-bank me-2 text-danger"></i>Ouvrages & Barrages
                    </a>
                </div>
            </div>
        </div>

        <!-- Forum Feed -->
        <div class="col-lg-9">
            
            <!-- Ask Question Collapse Form -->
            <div class="collapse mb-4" id="askQuestionCollapse">
                <div class="card border-0 shadow-lg rounded-4 p-4 bg-light">
                    <h4 class="fw-bold text-primary mb-3"><i class="bi bi-pencil-square me-2"></i>Publier une question sur le forum</h4>
                    
                    <div id="askAlert" class="alert d-none mb-3" role="alert"></div>

                    <form id="askForm">
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label text-muted small fw-bold">Votre Nom / Pseudo</label>
                                <input type="text" name="author" class="form-control bg-white border-0" placeholder="Ex: Ryad B." required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted small fw-bold">Votre Profil</label>
                                <select name="role" class="form-select bg-white border-0" required>
                                    <option value="Étudiant">Étudiant</option>
                                    <option value="Professionnel">Professionnel (Bureau d'études / Travaux)</option>
                                    <option value="Expert">Expert Hydraulique</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-muted small fw-bold">Titre synthétique de la question</label>
                            <input type="text" name="title" class="form-control bg-white border-0" placeholder="Ex: Dimensionnement de trop-plein de réservoir 500m3" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-muted small fw-bold">Thématique principale</label>
                            <select name="category" class="form-select bg-white border-0" required>
                                <option value="AEP (Alimentation en Eau Potable)">AEP (Alimentation en Eau Potable)</option>
                                <option value="Assainissement & Environnement">Assainissement & Environnement</option>
                                <option value="Irrigation & Économie d'eau">Irrigation & Économie d'eau</option>
                                <option value="Ouvrages Hydrauliques & Crues">Ouvrages Hydrauliques & Crues</option>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label class="form-label text-muted small fw-bold">Détaillez vos données de calcul ou de problème technique</label>
                            <textarea name="content" class="form-control bg-white border-0" rows="5" placeholder="Décrivez votre calcul, le logiciel utilisé (EPANET, HEC-RAS etc.) et joignez les paramètres de débit/pente..." required></textarea>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-toggle="collapse" data-bs-target="#askQuestionCollapse">Annuler</button>
                            <button type="submit" class="btn btn-primary rounded-pill px-4" id="btnAskSubmit">
                                Poser ma question <i class="bi bi-send-fill ms-1"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- List of Questions -->
            <div id="questionsContainer" class="d-flex flex-column gap-4">
                <?php if (empty($questions)): ?>
                    <div class="card p-5 text-center bg-white border border-light shadow-sm rounded-4 text-muted">
                        <i class="bi bi-chat-dots fs-1 mb-2"></i>
                        <p class="mb-0">Aucun sujet publié sur le forum. Soyez le premier à poser une question !</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($questions as $q): ?>
                        <div class="card hydro-card forum-question-card bg-white p-4" data-category="<?php echo htmlspecialchars($q['category']); ?>">
                            <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-3">
                                <span class="badge bg-secondary-subtle text-secondary-emphasis rounded-pill px-3 py-2 small">
                                    <i class="bi bi-tag-fill me-1 small"></i><?php echo htmlspecialchars($q['category']); ?>
                                </span>
                                <small class="text-muted"><i class="bi bi-clock me-1"></i><?php echo date('d/m/Y à H:i', strtotime($q['created_at'])); ?></small>
                            </div>
                            
                            <h4 class="fw-bold mb-2 text-dark"><?php echo htmlspecialchars($q['title']); ?></h4>
                            
                            <div class="d-flex align-items-center mb-3 text-muted">
                                <small>
                                    Par <strong><?php echo htmlspecialchars($q['author']); ?></strong> 
                                    <span class="badge badge-role <?php 
                                        if($q['role'] == 'Étudiant' || $q['role'] == 'Etudiant') echo 'badge-student';
                                        elseif($q['role'] == 'Professionnel' || $q['role'] == 'Professionel') echo 'badge-professional';
                                        else echo 'badge-expert';
                                    ?> ms-1"><?php echo htmlspecialchars($q['role']); ?></span>
                                </small>
                            </div>

                            <p class="text-secondary small mb-4" style="white-space: pre-line;"><?php echo htmlspecialchars($q['content']); ?></p>

                            <!-- Accordion Collapse Trigger -->
                            <div class="d-flex justify-content-between align-items-center border-top border-light pt-3">
                                <button class="btn btn-sm btn-outline-info rounded-pill px-3 py-1.5" 
                                        data-bs-toggle="collapse" 
                                        data-bs-target="#replies-collapse-<?php echo $q['id']; ?>" 
                                        aria-expanded="false" 
                                        aria-controls="replies-collapse-<?php echo $q['id']; ?>">
                                    <i class="bi bi-chat-left-text me-2"></i>
                                    <span id="reply-count-<?php echo $q['id']; ?>"><?php echo $q['reply_count']; ?> réponses</span>
                                </button>
                                <span class="text-muted small">Cliquer pour répondre ou voir les discussions</span>
                            </div>

                            <!-- Replies collapse panel -->
                            <div class="collapse question-collapse mt-4" id="replies-collapse-<?php echo $q['id']; ?>" data-question-id="<?php echo $q['id']; ?>">
                                <div class="bg-light p-3 rounded-4 border border-light">
                                    <h6 class="fw-bold text-primary mb-3"><i class="bi bi-chat-dots me-2"></i>Discussions</h6>
                                    
                                    <!-- Dynamic replies list container loaded via JS -->
                                    <div id="replies-container-<?php echo $q['id']; ?>" class="mb-4"></div>

                                    <!-- Submit reply form -->
                                    <form onsubmit="submitReply(event, <?php echo $q['id']; ?>)" class="border-top border-secondary-subtle pt-3">
                                        <div class="row g-2 mb-2">
                                            <div class="col-md-6">
                                                <input type="text" name="author" class="form-control form-control-sm bg-white" placeholder="Votre nom/pseudo" required>
                                            </div>
                                            <div class="col-md-6">
                                                <select name="role" class="form-select form-select-sm bg-white" required>
                                                    <option value="Étudiant">Étudiant</option>
                                                    <option value="Professionnel">Professionnel</option>
                                                    <option value="Expert">Expert</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="mb-2">
                                            <textarea name="content" class="form-control form-control-sm bg-white" rows="2" placeholder="Saisissez votre réponse technique ou conseil pratique..." required></textarea>
                                        </div>
                                        <div class="text-end">
                                            <button type="submit" class="btn btn-sm btn-primary rounded-pill px-3">
                                                Répondre <i class="bi bi-reply-fill ms-1"></i>
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Forum JS -->
<script src="assets/js/community.js"></script>

<?php include_once 'includes/footer.php'; ?>
