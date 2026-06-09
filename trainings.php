<?php
require_once 'config/db.php';
include_once 'includes/header.php';

// Fetch trainings from database
$trainings = [];
try {
    $stmt = $pdo->query("SELECT * FROM `trainings` ORDER BY `id` ASC");
    $trainings = $stmt->fetchAll();
} catch (Exception $e) {
    // Fail silently
}

// Build unique categories list from badges
$categories = [];
foreach ($trainings as $t) {
    if (!empty($t['badge']) && !in_array($t['badge'], $categories)) {
        $categories[] = $t['badge'];
    }
}
?>

<div class="container my-5">
    <div class="text-center mb-5">
        <span class="badge bg-info bg-opacity-10 text-info px-3 py-2 rounded-pill mb-2">Formations Pratiques Appliquées</span>
        <h1 class="fw-bold text-primary display-5">Catalogue de Formations</h1>
        <p class="text-muted max-w-lg mx-auto">
            Développez des compétences pratiques directement exploitables sur le marché du travail grâce à nos formations orientées projets réels et logiciels professionnels.
        </p>
    </div>

    <!-- ===== FILTER BAR ===== -->
    <div class="filter-bar-wrapper mb-5">
        <!-- Search Input -->
        <div class="filter-search-box mb-3">
            <span class="filter-search-icon"><i class="bi bi-search"></i></span>
            <input
                type="text"
                id="filterSearch"
                class="filter-search-input"
                placeholder="Rechercher une formation..."
                autocomplete="off"
            >
            <button class="filter-search-clear d-none" id="btnClearSearch" title="Effacer">
                <i class="bi bi-x-circle-fill"></i>
            </button>
        </div>

        <!-- Filter Controls Row -->
        <div class="d-flex flex-wrap align-items-center gap-3">

            <!-- Category Pills -->
            <div class="filter-pill-group" id="categoryFilters">
                <button class="filter-pill active" data-category="all">
                    <i class="bi bi-grid-3x3-gap me-1"></i> Toutes
                </button>
                <?php foreach ($categories as $cat): ?>
                    <button class="filter-pill" data-category="<?php echo htmlspecialchars($cat); ?>">
                        <?php echo htmlspecialchars($cat); ?>
                    </button>
                <?php endforeach; ?>
            </div>

            <!-- Separator -->
            <div class="filter-separator d-none d-md-block"></div>

            <!-- Duration Filter -->
            <div class="filter-select-wrapper">
                <i class="bi bi-clock filter-select-icon"></i>
                <select id="filterDuration" class="filter-select">
                    <option value="all">Toutes durées</option>
                    <option value="short">Courte (≤ 20h)</option>
                    <option value="medium">Moyenne (21–27h)</option>
                    <option value="long">Longue (≥ 28h)</option>
                </select>
            </div>

            <!-- Results count badge -->
            <div class="ms-auto">
                <span class="filter-count-badge" id="resultsCount">
                    <?php echo count($trainings); ?> formation<?php echo count($trainings) > 1 ? 's' : ''; ?>
                </span>
            </div>
        </div>
    </div>

    <!-- Course Cards Grid -->
    <div class="row g-4" id="trainingsGrid">
        <?php if (empty($trainings)): ?>
            <div class="col-12 text-center py-5">
                <div class="card border-0 shadow-sm p-5 rounded-4">
                    <i class="bi bi-journal-x fs-1 text-muted mb-3"></i>
                    <h5 class="fw-bold">Aucune formation disponible</h5>
                    <p class="text-muted mb-0">Revenez plus tard pour découvrir nos nouveaux programmes.</p>
                </div>
            </div>
        <?php else: ?>
            <?php foreach ($trainings as $course): ?>
                <?php
                    // Get program points from newline string
                    $program_points = array_filter(array_map('trim', explode("\n", $course['program'])));

                    // Map badge style
                    $badge_style = $course['badge_style'];
                    $text_color_inline = '';
                    if ($course['badge_style'] == 'bg-teal') {
                        $badge_style = '';
                        $text_color_inline = 'style="background-color: #f0fdfa; color: #0d9488;"';
                    }

                    // Parse hours for filtering
                    preg_match('/(\d+)/', $course['duration'], $durationMatch);
                    $hours = isset($durationMatch[1]) ? (int)$durationMatch[1] : 0;
                    $durationCategory = $hours <= 20 ? 'short' : ($hours <= 27 ? 'medium' : 'long');
                ?>
                <div class="col-lg-6 training-card-col"
                     data-title="<?php echo strtolower(htmlspecialchars($course['title'] . ' ' . $course['description'])); ?>"
                     data-category="<?php echo htmlspecialchars($course['badge']); ?>"
                     data-duration="<?php echo $durationCategory; ?>">
                    <div class="card hydro-card h-100 p-4">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <?php if (!empty($course['badge'])): ?>
                                <span class="badge rounded-pill px-3 py-2 <?php echo htmlspecialchars($badge_style); ?>" <?php echo $text_color_inline; ?>>
                                    <?php echo htmlspecialchars($course['badge']); ?>
                                </span>
                            <?php endif; ?>
                            <span class="text-muted"><i class="bi bi-clock me-1"></i> <?php echo htmlspecialchars($course['duration']); ?></span>
                        </div>
                        <h3 class="fw-bold mb-2"><?php echo htmlspecialchars($course['title']); ?></h3>
                        <p class="text-secondary small mb-3">
                            <?php echo htmlspecialchars($course['description']); ?>
                        </p>
                        <div class="bg-light p-3 rounded-3 mb-4">
                            <h6 class="fw-bold text-primary mb-2"><i class="bi bi-journal-text me-2"></i>Programme clé :</h6>
                            <ul class="list-unstyled mb-0 small text-muted">
                                <?php foreach ($program_points as $point): ?>
                                    <li class="mb-1"><i class="bi bi-check2 text-success me-2"></i><?php echo htmlspecialchars($point); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mt-auto">
                            <div>
                                <span class="text-muted small d-block">Tarif Étudiant / Pro</span>
                                <strong class="fs-4 text-primary"><?php echo htmlspecialchars($course['price']); ?></strong>
                            </div>
                            <button class="btn btn-outline-primary rounded-pill px-4" onclick="openRegisterModal('<?php echo htmlspecialchars($course['code']); ?>', '<?php echo htmlspecialchars(addslashes($course['title'])); ?>')">
                                S'inscrire <i class="bi bi-arrow-right-short ms-1"></i>
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- Empty Filter State -->
    <div class="text-center py-5 d-none" id="noResultsMessage">
        <div class="card border-0 shadow-sm p-5 rounded-4 mx-auto" style="max-width: 480px;">
            <div class="mb-3">
                <span class="no-results-icon"><i class="bi bi-funnel-x"></i></span>
            </div>
            <h5 class="fw-bold mb-2">Aucune formation trouvée</h5>
            <p class="text-muted small mb-4">Essayez de modifier vos critères de recherche ou réinitialisez les filtres.</p>
            <button class="btn btn-outline-primary rounded-pill px-4" id="btnResetFilters">
                <i class="bi bi-arrow-counterclockwise me-2"></i>Réinitialiser les filtres
            </button>
        </div>
    </div>

</div>

<!-- Registration Modal -->
<div class="modal fade" id="registerModal" tabindex="-1" aria-labelledby="registerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header bg-primary text-white border-0 py-3 rounded-top-4">
                <h5 class="modal-title fw-bold" id="registerModalLabel">S'inscrire à une formation</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <!-- Form Alert Status -->
                <div id="formAlert" class="alert d-none mb-3" role="alert"></div>

                <form id="registrationForm">
                    <input type="hidden" name="course_id" id="modal_course_id">
                    
                    <div class="mb-3">
                        <label class="form-label text-muted small fw-bold">Nom complet</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="bi bi-person text-secondary"></i></span>
                            <input type="text" name="name" class="form-control bg-light border-start-0" placeholder="Ex: Larbi Ben M'hidi" required>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label text-muted small fw-bold">Adresse E-mail</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="bi bi-envelope text-secondary"></i></span>
                            <input type="email" name="email" class="form-control bg-light border-start-0" placeholder="Ex: larbi@example.com" required>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label text-muted small fw-bold">Numéro de téléphone</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="bi bi-telephone text-secondary"></i></span>
                            <input type="tel" name="phone" class="form-control bg-light border-start-0" placeholder="Ex: 0550123456" required>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label text-muted small fw-bold">Formation sélectionnée</label>
                        <input type="text" id="modal_course_title" class="form-control bg-light" readonly style="font-weight:600; color:var(--primary-color);">
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary rounded-pill py-2.5 shadow-sm" id="btnSubmit">
                            Valider l'inscription <i class="bi bi-send-fill ms-2"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// ===== FILTER LOGIC =====
document.addEventListener('DOMContentLoaded', function () {

    const cards       = document.querySelectorAll('.training-card-col');
    const searchInput = document.getElementById('filterSearch');
    const clearBtn    = document.getElementById('btnClearSearch');
    const durationSel = document.getElementById('filterDuration');
    const countBadge  = document.getElementById('resultsCount');
    const noResults   = document.getElementById('noResultsMessage');
    const resetBtn    = document.getElementById('btnResetFilters');

    let activeCategory = 'all';

    // ---- Category pills ----
    document.querySelectorAll('.filter-pill').forEach(pill => {
        pill.addEventListener('click', function () {
            document.querySelectorAll('.filter-pill').forEach(p => p.classList.remove('active'));
            this.classList.add('active');
            activeCategory = this.dataset.category;
            applyFilters();
        });
    });

    // ---- Search input ----
    searchInput.addEventListener('input', function () {
        clearBtn.classList.toggle('d-none', this.value.length === 0);
        applyFilters();
    });

    clearBtn.addEventListener('click', function () {
        searchInput.value = '';
        clearBtn.classList.add('d-none');
        searchInput.focus();
        applyFilters();
    });

    // ---- Duration select ----
    durationSel.addEventListener('change', applyFilters);

    // ---- Reset button (empty state) ----
    resetBtn.addEventListener('click', resetFilters);

    function resetFilters() {
        searchInput.value = '';
        clearBtn.classList.add('d-none');
        durationSel.value = 'all';
        activeCategory = 'all';
        document.querySelectorAll('.filter-pill').forEach(p => p.classList.remove('active'));
        document.querySelector('.filter-pill[data-category="all"]').classList.add('active');
        applyFilters();
    }

    function applyFilters() {
        const query    = searchInput.value.toLowerCase().trim();
        const duration = durationSel.value;

        let visible = 0;

        cards.forEach(card => {
            const matchSearch   = query === '' || card.dataset.title.includes(query);
            const matchCategory = activeCategory === 'all' || card.dataset.category === activeCategory;
            const matchDuration = duration === 'all' || card.dataset.duration === duration;

            const show = matchSearch && matchCategory && matchDuration;

            if (show) {
                card.classList.remove('filter-hidden');
                card.classList.add('filter-visible');
                visible++;
            } else {
                card.classList.remove('filter-visible');
                card.classList.add('filter-hidden');
            }
        });

        // Update count badge
        countBadge.textContent = visible + ' formation' + (visible > 1 ? 's' : '');

        // Toggle empty state
        noResults.classList.toggle('d-none', visible > 0);
    }
});

// ===== REGISTRATION MODAL =====
let registerModal;

document.addEventListener('DOMContentLoaded', function() {
    registerModal = new bootstrap.Modal(document.getElementById('registerModal'));
    
    document.getElementById('registrationForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const btnSubmit = document.getElementById('btnSubmit');
        const formAlert = document.getElementById('formAlert');
        
        btnSubmit.disabled = true;
        btnSubmit.innerHTML = 'Traitement en cours... <span class="spinner-border spinner-border-sm ms-2" role="status" aria-hidden="true"></span>';
        
        const formData = new FormData(this);
        
        fetch('api/register_course.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            btnSubmit.disabled = false;
            btnSubmit.innerHTML = 'Valider l\'inscription <i class="bi bi-send-fill ms-2"></i>';
            
            formAlert.classList.remove('d-none', 'alert-success', 'alert-danger');
            
            if(data.success) {
                formAlert.classList.add('alert-success');
                formAlert.innerHTML = '<i class="bi bi-check-circle-fill me-2"></i>' + data.message;
                document.getElementById('registrationForm').reset();
                setTimeout(() => {
                    registerModal.hide();
                    formAlert.classList.add('d-none');
                }, 2000);
            } else {
                formAlert.classList.add('alert-danger');
                formAlert.innerHTML = '<i class="bi bi-exclamation-triangle-fill me-2"></i>' + data.message;
            }
        })
        .catch(err => {
            btnSubmit.disabled = false;
            btnSubmit.innerHTML = 'Valider l\'inscription <i class="bi bi-send-fill ms-2"></i>';
            formAlert.classList.remove('d-none');
            formAlert.classList.add('alert-danger');
            formAlert.innerHTML = '<i class="bi bi-exclamation-triangle-fill me-2"></i> Une erreur s\'est produite. Veuillez réessayer.';
        });
    });
});

function openRegisterModal(id, title) {
    document.getElementById('modal_course_id').value = id;
    document.getElementById('modal_course_title').value = title;
    const formAlert = document.getElementById('formAlert');
    formAlert.classList.add('d-none');
    registerModal.show();
}
</script>

<?php include_once 'includes/footer.php'; ?>
