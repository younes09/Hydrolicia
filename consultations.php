<?php
require_once 'config/db.php';
include_once 'includes/header.php';

// Fetch active consultations and experts from database
$booked_consultations = [];
$experts = [];
try {
    $stmt = $pdo->query("SELECT * FROM `consultations` ORDER BY `id` DESC LIMIT 10");
    $booked_consultations = $stmt->fetchAll();
    
    $stmt_exp = $pdo->query("SELECT * FROM `experts` ORDER BY `id` ASC");
    $experts = $stmt_exp->fetchAll();
} catch (Exception $e) {
    // Silently ignore or show error
}

// Build unique specialty keywords for filter
$specialties = [];
foreach ($experts as $exp) {
    // Extract first keyword before comma or &
    $parts = preg_split('/[,&]/', $exp['specialty']);
    $key   = trim($parts[0]);
    if ($key && !in_array($key, $specialties)) {
        $specialties[] = $key;
    }
}
?>

<div class="container my-5">
    <div class="text-center mb-5">
        <span class="badge bg-warning bg-opacity-10 text-warning px-3 py-2 rounded-pill mb-2" style="background-color: #fff7ed; color: #ea580c;">Conseils & Expertises</span>
        <h1 class="fw-bold text-primary display-5">Consultation avec nos Experts</h1>
        <p class="text-muted max-w-lg mx-auto">
            Bénéficiez d'un accompagnement personnalisé en réservant un entretien technique en ligne avec nos ingénieurs hydrauliciens confirmés.
        </p>
    </div>

    <div class="row g-4">
        <!-- Expert profiles listing -->
        <div class="col-lg-8">
            <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
                <h3 class="fw-bold text-primary mb-0"><i class="bi bi-people me-2"></i>Nos Experts Disponibles</h3>
                <span class="filter-count-badge" id="expertsCount">
                    <?php echo count($experts); ?> expert<?php echo count($experts) > 1 ? 's' : ''; ?>
                </span>
            </div>

            <!-- ===== EXPERT FILTER BAR ===== -->
            <?php if (!empty($experts)): ?>
            <div class="expert-filter-bar mb-4">
                <!-- Search -->
                <div class="filter-search-box mb-3">
                    <span class="filter-search-icon"><i class="bi bi-search"></i></span>
                    <input
                        type="text"
                        id="expertSearch"
                        class="filter-search-input"
                        placeholder="Rechercher un expert ou une spécialité..."
                        autocomplete="off"
                    >
                    <button class="filter-search-clear d-none" id="btnClearExpertSearch" title="Effacer">
                        <i class="bi bi-x-circle-fill"></i>
                    </button>
                </div>

                <!-- Pills Row -->
                <div class="d-flex flex-wrap align-items-center gap-2">
                    <!-- Status filter -->
                    <div class="filter-pill-group">
                        <button class="filter-pill active" data-status="all">
                            <i class="bi bi-grid me-1"></i> Tous
                        </button>
                        <button class="filter-pill" data-status="available">
                            <i class="bi bi-circle-fill text-success me-1" style="font-size:.55rem;"></i> Disponibles
                        </button>
                        <button class="filter-pill" data-status="unavailable">
                            <i class="bi bi-circle-fill text-danger me-1" style="font-size:.55rem;"></i> Indisponibles
                        </button>
                    </div>

                    <!-- Separator -->
                    <div class="filter-separator d-none d-sm-block"></div>

                    <!-- Specialty select -->
                    <div class="filter-select-wrapper">
                        <i class="bi bi-funnel filter-select-icon"></i>
                        <select id="expertSpecialty" class="filter-select">
                            <option value="all">Toutes spécialités</option>
                            <?php foreach ($specialties as $sp): ?>
                                <option value="<?php echo htmlspecialchars(strtolower($sp)); ?>">
                                    <?php echo htmlspecialchars($sp); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Experts Grid -->
            <div class="row g-4" id="expertsGrid">
                <?php if (empty($experts)): ?>
                    <div class="col-12 text-center py-4 text-muted">
                        <p>Aucun expert disponible pour le moment.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($experts as $exp): ?>
                        <?php
                            $bg_color_style = '';
                            if ($exp['avatar_color_class'] == 'primary') {
                                $bg_color_style = 'background-color: rgba(13, 110, 253, 0.1); color: #0d6efd;';
                            } elseif ($exp['avatar_color_class'] == 'warning') {
                                $bg_color_style = 'background-color: #fff7ed; color: #ea580c;';
                            } elseif ($exp['avatar_color_class'] == 'success') {
                                $bg_color_style = 'background-color: rgba(25, 135, 84, 0.1); color: #198754;';
                            } elseif ($exp['avatar_color_class'] == 'danger') {
                                $bg_color_style = 'background-color: rgba(220, 53, 69, 0.1); color: #dc3545;';
                            } elseif ($exp['avatar_color_class'] == 'info') {
                                $bg_color_style = 'background-color: rgba(13, 202, 240, 0.1); color: #0dcaf0;';
                            } else {
                                $bg_color_style = 'background-color: rgba(108, 117, 125, 0.1); color: #6c757d;';
                            }
                            $is_available = ($exp['status'] === 'Disponible');

                            // specialty keyword for filtering
                            $specParts   = preg_split('/[,&]/', $exp['specialty']);
                            $specKeyword = strtolower(trim($specParts[0]));
                        ?>
                        <div class="col-md-12 expert-card-col"
                             data-name="<?php echo strtolower(htmlspecialchars($exp['name'] . ' ' . $exp['specialty'] . ' ' . $exp['bio'])); ?>"
                             data-status="<?php echo $is_available ? 'available' : 'unavailable'; ?>"
                             data-specialty="<?php echo htmlspecialchars($specKeyword); ?>">
                            <div class="card bg-white border border-light shadow-sm rounded-4 p-4 expert-card-item">
                                <div class="row align-items-center">
                                    <div class="col-md-2 text-center text-md-start">
                                        <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto" style="width: 70px; height: 70px; <?php echo $bg_color_style; ?>">
                                            <i class="bi <?php echo htmlspecialchars($exp['avatar_class']); ?> fs-2"></i>
                                        </div>
                                    </div>
                                    <div class="col-md-7 mt-3 mt-md-0">
                                        <h5 class="fw-bold mb-1"><?php echo htmlspecialchars($exp['name']); ?></h5>
                                        <span class="text-accent small d-block mb-2 fw-semibold text-info"><?php echo htmlspecialchars($exp['specialty']); ?></span>
                                        <p class="text-muted small mb-0">
                                            <?php echo htmlspecialchars($exp['bio']); ?>
                                        </p>
                                    </div>
                                    <div class="col-md-3 text-center text-md-end mt-3 mt-md-0">
                                        <?php if ($is_available): ?>
                                            <span class="badge bg-success bg-opacity-10 text-success rounded-pill mb-2"><i class="bi bi-circle-fill me-1 small"></i>Disponible</span>
                                            <button class="btn btn-sm btn-outline-primary d-block w-100 rounded-pill" onclick="selectExpert('<?php echo htmlspecialchars(addslashes($exp['name'])); ?>')">Choisir</button>
                                        <?php else: ?>
                                            <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill mb-2"><i class="bi bi-circle-fill me-1 small"></i>Indisponible</span>
                                            <button class="btn btn-sm btn-outline-secondary d-block w-100 rounded-pill" disabled>Choisir</button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <!-- Empty filter state -->
            <div class="text-center py-5 d-none" id="noExpertResults">
                <div class="card border-0 shadow-sm p-5 rounded-4 mx-auto" style="max-width: 420px;">
                    <div class="mb-3">
                        <span class="no-results-icon"><i class="bi bi-person-x"></i></span>
                    </div>
                    <h5 class="fw-bold mb-2">Aucun expert trouvé</h5>
                    <p class="text-muted small mb-4">Modifiez vos critères de recherche ou réinitialisez les filtres.</p>
                    <button class="btn btn-outline-primary rounded-pill px-4" id="btnResetExpertFilters">
                        <i class="bi bi-arrow-counterclockwise me-2"></i>Réinitialiser
                    </button>
                </div>
            </div>
        </div>

        <!-- Booking Form Sidebar -->
        <div class="col-lg-4">
            <div class="card border-0 bg-primary text-white rounded-4 p-4 shadow-lg">
                <h4 class="fw-bold mb-3"><i class="bi bi-calendar-event me-2"></i>Réserver une session</h4>
                <p class="small text-white-50 mb-4">Planifiez une séance de consultation technique d'une heure en visioconférence avec l'expert de votre choix.</p>
                
                <div id="bookingAlert" class="alert d-none mb-3" role="alert"></div>

                <form id="bookingForm">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Expert</label>
                        <select name="expert_name" id="expert_name" class="form-select bg-white text-dark" required>
                            <option value="">Sélectionnez un expert...</option>
                            <?php foreach ($experts as $exp): ?>
                                <?php if ($exp['status'] === 'Disponible'): ?>
                                    <option value="<?php echo htmlspecialchars($exp['name']); ?>"><?php echo htmlspecialchars($exp['name']); ?></option>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Votre nom complet</label>
                        <input type="text" name="name" class="form-control" placeholder="Ex: Slimane Hocine" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Adresse E-mail</label>
                        <input type="email" name="email" class="form-control" placeholder="Ex: slimane@example.com" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Numéro de mobile</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white text-primary border-end-0" style="border-color: rgba(255,255,255,0.3);">
                                <i class="bi bi-phone"></i>
                            </span>
                            <input type="tel" name="phone" class="form-control border-start-0" placeholder="Ex: 0550 123 456" pattern="[0-9+\s\-]{8,15}" required>
                        </div>
                        <div class="form-text text-white-50" style="font-size: 0.75rem;"><i class="bi bi-info-circle me-1"></i>Pour vous contacter en cas de modification du créneau.</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Thématique / Projet</label>
                        <select name="topic" class="form-select" required>
                            <option value="AEP &amp; Modélisation EPANET">AEP &amp; Modélisation EPANET</option>
                            <option value="Assainissement &amp; SewerGEMS">Assainissement &amp; SewerGEMS</option>
                            <option value="Irrigation Goutte-à-Goutte / REUSE">Irrigation Goutte-à-Goutte / REUSE</option>
                            <option value="Étude de rupture de barrage / Crues">Étude de rupture de barrage / Crues</option>
                            <option value="Autre étude de dimensionnement">Autre étude de dimensionnement</option>
                        </select>
                    </div>

                    <div class="row g-2 mb-4">
                        <div class="col-6">
                            <label class="form-label small fw-bold">Date</label>
                            <input type="date" name="date" class="form-control text-dark bg-white" required min="<?php echo date('Y-m-d'); ?>">
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-bold">Heure</label>
                            <select name="time" class="form-select text-dark bg-white" required>
                                <option value="09:00">09:00</option>
                                <option value="10:30">10:30</option>
                                <option value="13:30">13:30</option>
                                <option value="15:00">15:00</option>
                            </select>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-info w-100 rounded-pill py-2.5 font-weight-bold text-dark shadow-sm" id="btnBook">
                        Confirmer mon créneau <i class="bi bi-check-lg ms-1"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Active bookings Dashboard -->
    <div class="row mt-5">
        <div class="col-md-12">
            <div class="p-4 bg-white rounded-4 shadow-sm border border-light">
                <h3 class="fw-bold mb-4 text-primary"><i class="bi bi-display me-2"></i>Suivi des réservations récentes</h3>
                
                <?php if (empty($booked_consultations)): ?>
                    <div class="text-center py-4 text-muted">
                        <i class="bi bi-calendar-x fs-1 mb-2"></i>
                        <p class="mb-0">Aucune consultation réservée pour le moment. Soyez le premier !</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light text-secondary">
                                <tr>
                                    <th>Client / Étudiant</th>
                                    <th>Expert assigné</th>
                                    <th>Projet / Thématique</th>
                                    <th>Date &amp; Heure</th>
                                    <th>Statut</th>
                                </tr>
                            </thead>
                            <tbody id="bookingsTableBody">
                                <?php foreach ($booked_consultations as $consult): ?>
                                    <tr>
                                        <td>
                                            <div class="fw-bold"><?php echo htmlspecialchars($consult['name']); ?></div>
                                            <small class="text-muted"><?php echo htmlspecialchars($consult['email']); ?></small>
                                        </td>
                                        <td>
                                            <i class="bi bi-person text-info me-1"></i><?php echo htmlspecialchars($consult['expert_name']); ?>
                                        </td>
                                        <td>
                                            <span class="text-truncate d-inline-block" style="max-width: 200px;"><?php echo htmlspecialchars($consult['topic']); ?></span>
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary-subtle text-secondary-emphasis"><i class="bi bi-calendar-event me-1"></i><?php echo htmlspecialchars($consult['date']); ?> à <?php echo htmlspecialchars($consult['time']); ?></span>
                                        </td>
                                        <td>
                                            <?php if ($consult['status'] == 'Confirmé'): ?>
                                                <span class="pill-status bg-success bg-opacity-10 text-success"><i class="bi bi-check-circle-fill me-1"></i>Confirmé</span>
                                            <?php else: ?>
                                                <span class="pill-status bg-warning bg-opacity-10 text-warning"><i class="bi bi-clock-fill me-1"></i>En attente</span>
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
    </div>
</div>

<script>
// ===== EXPERT FILTER LOGIC =====
document.addEventListener('DOMContentLoaded', function () {

    const cards        = document.querySelectorAll('.expert-card-col');
    const searchInput  = document.getElementById('expertSearch');
    const clearBtn     = document.getElementById('btnClearExpertSearch');
    const specialtySel = document.getElementById('expertSpecialty');
    const countBadge   = document.getElementById('expertsCount');
    const noResults    = document.getElementById('noExpertResults');
    const resetBtn     = document.getElementById('btnResetExpertFilters');

    if (!searchInput) return; // no experts, nothing to filter

    let activeStatus = 'all';

    // ---- Status pill buttons ----
    document.querySelectorAll('.filter-pill[data-status]').forEach(pill => {
        pill.addEventListener('click', function () {
            document.querySelectorAll('.filter-pill[data-status]').forEach(p => p.classList.remove('active'));
            this.classList.add('active');
            activeStatus = this.dataset.status;
            applyFilters();
        });
    });

    // ---- Search ----
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

    // ---- Specialty select ----
    specialtySel.addEventListener('change', applyFilters);

    // ---- Reset ----
    resetBtn.addEventListener('click', resetFilters);

    function resetFilters() {
        searchInput.value = '';
        clearBtn.classList.add('d-none');
        specialtySel.value = 'all';
        activeStatus = 'all';
        document.querySelectorAll('.filter-pill[data-status]').forEach(p => p.classList.remove('active'));
        document.querySelector('.filter-pill[data-status="all"]').classList.add('active');
        applyFilters();
    }

    function applyFilters() {
        const query     = searchInput.value.toLowerCase().trim();
        const specialty = specialtySel.value;
        let visible = 0;

        cards.forEach(card => {
            const matchSearch    = query === '' || card.dataset.name.includes(query);
            const matchStatus    = activeStatus === 'all' || card.dataset.status === activeStatus;
            const matchSpecialty = specialty === 'all' || card.dataset.specialty.includes(specialty);

            const show = matchSearch && matchStatus && matchSpecialty;

            if (show) {
                card.classList.remove('filter-hidden');
                card.classList.add('filter-visible');
                visible++;
            } else {
                card.classList.remove('filter-visible');
                card.classList.add('filter-hidden');
            }
        });

        // Update count
        countBadge.textContent = visible + ' expert' + (visible > 1 ? 's' : '');

        // Toggle empty state
        noResults.classList.toggle('d-none', visible > 0);
    }
});

// ===== BOOKING LOGIC =====
function selectExpert(expertName) {
    document.getElementById('expert_name').value = expertName;
    document.getElementById('expert_name').scrollIntoView({ behavior: 'smooth', block: 'center' });
}

document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('bookingForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const btnBook     = document.getElementById('btnBook');
        const bookingAlert = document.getElementById('bookingAlert');
        
        btnBook.disabled = true;
        btnBook.innerHTML = 'Planification... <span class="spinner-border spinner-border-sm ms-2" role="status" aria-hidden="true"></span>';
        
        const formData = new FormData(this);
        
        fetch('api/book_consultation.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            btnBook.disabled = false;
            btnBook.innerHTML = 'Confirmer mon créneau <i class="bi bi-check-lg ms-1"></i>';
            
            bookingAlert.classList.remove('d-none', 'alert-success', 'alert-danger');
            
            if(data.success) {
                bookingAlert.classList.add('alert-success');
                bookingAlert.innerHTML = '<i class="bi bi-check-circle-fill me-2"></i>' + data.message;
                
                document.getElementById('bookingForm').reset();
                
                if (data.consultation) {
                    const tableBody = document.getElementById('bookingsTableBody');
                    
                    const newRow = `
                        <tr>
                            <td>
                                <div class="fw-bold">${escapeHtml(data.consultation.name)}</div>
                                <small class="text-muted">${escapeHtml(data.consultation.email)}</small>
                            </td>
                            <td>
                                <i class="bi bi-person text-info me-1"></i>${escapeHtml(data.consultation.expert_name)}
                            </td>
                            <td>
                                <span class="text-truncate d-inline-block" style="max-width: 200px;">${escapeHtml(data.consultation.topic)}</span>
                            </td>
                            <td>
                                <span class="badge bg-secondary-subtle text-secondary-emphasis"><i class="bi bi-calendar-event me-1"></i>${escapeHtml(data.consultation.date)} à ${escapeHtml(data.consultation.time.substring(0,5))}</span>
                            </td>
                            <td>
                                <span class="pill-status bg-warning bg-opacity-10 text-warning"><i class="bi bi-clock-fill me-1"></i>En attente</span>
                            </td>
                        </tr>
                    `;
                    
                    if (tableBody) {
                        tableBody.insertAdjacentHTML('afterbegin', newRow);
                    } else {
                        setTimeout(() => { window.location.reload(); }, 1000);
                    }
                }
                
                setTimeout(() => { bookingAlert.classList.add('d-none'); }, 3000);
            } else {
                bookingAlert.classList.add('alert-danger');
                bookingAlert.innerHTML = '<i class="bi bi-exclamation-triangle-fill me-2"></i>' + data.message;
            }
        })
        .catch(err => {
            btnBook.disabled = false;
            btnBook.innerHTML = 'Confirmer mon créneau <i class="bi bi-check-lg ms-1"></i>';
            bookingAlert.classList.remove('d-none');
            bookingAlert.classList.add('alert-danger');
            bookingAlert.innerHTML = '<i class="bi bi-exclamation-triangle-fill me-2"></i> Une erreur réseau s\'est produite.';
        });
    });
});

function escapeHtml(str) {
    return str.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;").replace(/'/g, "&#039;");
}
</script>

<?php include_once 'includes/footer.php'; ?>
