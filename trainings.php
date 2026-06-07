<?php
require_once 'config/db.php';
include_once 'includes/header.php';
?>

<div class="container my-5">
    <div class="text-center mb-5">
        <span class="badge bg-info bg-opacity-10 text-info px-3 py-2 rounded-pill mb-2">Formations Pratiques Appliquées</span>
        <h1 class="fw-bold text-primary display-5">Catalogue de Formations</h1>
        <p class="text-muted max-w-lg mx-auto">
            Développez des compétences pratiques directement exploitables sur le marché du travail grâce à nos formations orientées projets réels et logiciels professionnels.
        </p>
    </div>

    <!-- Course Cards Grid -->
    <div class="row g-4">
        <!-- Course 1 -->
        <div class="col-lg-6">
            <div class="card hydro-card h-100 p-4">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <span class="badge bg-primary rounded-pill px-3 py-2">Modélisation AEP</span>
                    <span class="text-muted"><i class="bi bi-clock me-1"></i> 24 Heures</span>
                </div>
                <h3 class="fw-bold mb-2">Alimentation en Eau Potable (EPANET)</h3>
                <p class="text-secondary small mb-3">
                    Conception, calage et dimensionnement des réseaux de distribution sous pression.
                </p>
                <div class="bg-light p-3 rounded-3 mb-4">
                    <h6 class="fw-bold text-primary mb-2"><i class="bi bi-journal-text me-2"></i>Programme clé :</h6>
                    <ul class="list-unstyled mb-0 small text-muted">
                        <li class="mb-1"><i class="bi bi-check2 text-success me-2"></i>Calcul des débits de pointe & dimensionnement des conduites</li>
                        <li class="mb-1"><i class="bi bi-check2 text-success me-2"></i>Calcul des réservoirs de stockage & stations de pompage</li>
                        <li class="mb-1"><i class="bi bi-check2 text-success me-2"></i>Modélisation dynamique et calage des pressions sous EPANET</li>
                        <li><i class="bi bi-check2 text-success me-2"></i>Analyse des transitoires hydrauliques (coup de bélier)</li>
                    </ul>
                </div>
                <div class="d-flex justify-content-between align-items-center mt-auto">
                    <div>
                        <span class="text-muted small d-block">Tarif Étudiant / Pro</span>
                        <strong class="fs-4 text-primary">15 000 DA</strong>
                    </div>
                    <button class="btn btn-outline-primary rounded-pill px-4" onclick="openRegisterModal('aep', 'Alimentation en Eau Potable (EPANET)')">
                        S'inscrire <i class="bi bi-arrow-right-short ms-1"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Course 2 -->
        <div class="col-lg-6">
            <div class="card hydro-card h-100 p-4">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <span class="badge bg-teal bg-opacity-10 text-teal rounded-pill px-3 py-2" style="background-color: #f0fdfa; color: #0d9488;">Assainissement & Drainage</span>
                    <span class="text-muted"><i class="bi bi-clock me-1"></i> 30 Heures</span>
                </div>
                <h3 class="fw-bold mb-2">Réseaux d'Assainissement (SewerGEMS)</h3>
                <p class="text-secondary small mb-3">
                    Dimensionnement des réseaux de collecte des eaux usées et pluviales urbaines.
                </p>
                <div class="bg-light p-3 rounded-3 mb-4">
                    <h6 class="fw-bold text-teal mb-2" style="color: #0d9488;"><i class="bi bi-journal-text me-2"></i>Programme clé :</h6>
                    <ul class="list-unstyled mb-0 small text-muted">
                        <li class="mb-1"><i class="bi bi-check2 text-success me-2"></i>Calcul des débits d'eaux usées domestiques (normes algériennes)</li>
                        <li class="mb-1"><i class="bi bi-check2 text-success me-2"></i>Évaluation des débits d'orage par la méthode rationnelle</li>
                        <li class="mb-1"><i class="bi bi-check2 text-success me-2"></i>Tracé en plan et profil en long (Covadis / Mensura)</li>
                        <li><i class="bi bi-check2 text-success me-2"></i>Modélisation hydraulique gravitaire sous SewerGEMS</li>
                    </ul>
                </div>
                <div class="d-flex justify-content-between align-items-center mt-auto">
                    <div>
                        <span class="text-muted small d-block">Tarif Étudiant / Pro</span>
                        <strong class="fs-4 text-primary">18 000 DA</strong>
                    </div>
                    <button class="btn btn-outline-primary rounded-pill px-4" onclick="openRegisterModal('assainissement', 'Réseaux d\'Assainissement (SewerGEMS)')">
                        S'inscrire <i class="bi bi-arrow-right-short ms-1"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Course 3 -->
        <div class="col-lg-6">
            <div class="card hydro-card h-100 p-4">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <span class="badge bg-success rounded-pill px-3 py-2">Irrigation & Écologie</span>
                    <span class="text-muted"><i class="bi bi-clock me-1"></i> 20 Heures</span>
                </div>
                <h3 class="fw-bold mb-2">Irrigation & Économie d'Eau (CROPWAT)</h3>
                <p class="text-secondary small mb-3">
                    Dimensionnement des réseaux d'irrigation et gestion optimale des ressources en eau.
                </p>
                <div class="bg-light p-3 rounded-3 mb-4">
                    <h6 class="fw-bold text-success mb-2"><i class="bi bi-journal-text me-2"></i>Programme clé :</h6>
                    <ul class="list-unstyled mb-0 small text-muted">
                        <li class="mb-1"><i class="bi bi-check2 text-success me-2"></i>Calcul des besoins en eau des cultures sous CROPWAT</li>
                        <li class="mb-1"><i class="bi bi-check2 text-success me-2"></i>Conception de systèmes goutte-à-goutte et aspersion</li>
                        <li class="mb-1"><i class="bi bi-check2 text-success me-2"></i>Calcul hydraulique des conduites de distribution</li>
                        <li><i class="bi bi-check2 text-success me-2"></i>Réutilisation des eaux épurées pour l'agriculture (REUSE)</li>
                    </ul>
                </div>
                <div class="d-flex justify-content-between align-items-center mt-auto">
                    <div>
                        <span class="text-muted small d-block">Tarif Étudiant / Pro</span>
                        <strong class="fs-4 text-primary">12 000 DA</strong>
                    </div>
                    <button class="btn btn-outline-primary rounded-pill px-4" onclick="openRegisterModal('irrigation', 'Irrigation & Économie d\'Eau (CROPWAT)')">
                        S'inscrire <i class="bi bi-arrow-right-short ms-1"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Course 4 -->
        <div class="col-lg-6">
            <div class="card hydro-card h-100 p-4">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <span class="badge bg-warning text-dark rounded-pill px-3 py-2">Ouvrages & Inondations</span>
                    <span class="text-muted"><i class="bi bi-clock me-1"></i> 28 Heures</span>
                </div>
                <h3 class="fw-bold mb-2">Hydraulique des Cours d'Eau (HEC-RAS)</h3>
                <p class="text-secondary small mb-3">
                    Modélisation des écoulements à surface libre et délimitation des zones inondables.
                </p>
                <div class="bg-light p-3 rounded-3 mb-4">
                    <h6 class="fw-bold text-warning mb-2" style="color: #d97706;"><i class="bi bi-journal-text me-2"></i>Programme clé :</h6>
                    <ul class="list-unstyled mb-0 small text-muted">
                        <li class="mb-1"><i class="bi bi-check2 text-success me-2"></i>Modélisation hydraulique 1D et 2D permanente/non permanente</li>
                        <li class="mb-1"><i class="bi bi-check2 text-success me-2"></i>Études d'inondation & protection des agglomérations</li>
                        <li class="mb-1"><i class="bi bi-check2 text-success me-2"></i>Dimensionnement des digues, seuils et canaux d'évacuation</li>
                        <li><i class="bi bi-check2 text-success me-2"></i>Étude de rupture de barrages (scénarios de sécurité)</li>
                    </ul>
                </div>
                <div class="d-flex justify-content-between align-items-center mt-auto">
                    <div>
                        <span class="text-muted small d-block">Tarif Étudiant / Pro</span>
                        <strong class="fs-4 text-primary">20 000 DA</strong>
                    </div>
                    <button class="btn btn-outline-primary rounded-pill px-4" onclick="openRegisterModal('hecras', 'Hydraulique des Cours d\'Eau (HEC-RAS)')">
                        S'inscrire <i class="bi bi-arrow-right-short ms-1"></i>
                    </button>
                </div>
            </div>
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
// JS Logic for Registration AJAX
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
                
                // Clear fields
                document.getElementById('registrationForm').reset();
                
                // Close modal after 2 seconds
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
    
    // Clear status
    const formAlert = document.getElementById('formAlert');
    formAlert.classList.add('d-none');
    
    registerModal.show();
}
</script>

<?php include_once 'includes/footer.php'; ?>
