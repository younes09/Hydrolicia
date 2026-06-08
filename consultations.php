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
            <h3 class="fw-bold mb-4 text-primary"><i class="bi bi-people me-2"></i>Nos Experts Disponibles</h3>
            
            <div class="row g-4">
                <?php if (empty($experts)): ?>
                    <div class="col-12 text-center py-4 text-muted">
                        <p>Aucun expert disponible pour le moment.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($experts as $exp): ?>
                        <?php
                            // Class mapping for avatar background color
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
                        ?>
                        <div class="col-md-12">
                            <div class="card bg-white border border-light shadow-sm rounded-4 p-4 mb-3">
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
                        <label class="form-label small fw-bold">Thématique / Projet</label>
                        <select name="topic" class="form-select" required>
                            <option value="AEP & Modélisation EPANET">AEP & Modélisation EPANET</option>
                            <option value="Assainissement & SewerGEMS">Assainissement & SewerGEMS</option>
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
                                    <th>Date & Heure</th>
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
function selectExpert(expertName) {
    document.getElementById('expert_name').value = expertName;
    document.getElementById('expert_name').scrollIntoView({ behavior: 'smooth', block: 'center' });
}

document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('bookingForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const btnBook = document.getElementById('btnBook');
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
                
                // Clear form
                document.getElementById('bookingForm').reset();
                
                // Refresh bookings table
                if (data.consultation) {
                    const tableBody = document.getElementById('bookingsTableBody');
                    
                    // If table was empty, remove the empty message block
                    const emptyContainer = tableBody ? null : document.querySelector('.table-responsive');
                    
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
                        // Reload page to reflect table structures if it was completely empty
                        setTimeout(() => {
                            window.location.reload();
                        }, 1000);
                    }
                }
                
                setTimeout(() => {
                    bookingAlert.classList.add('d-none');
                }, 3000);
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
