<?php
require_once 'config/db.php';
include_once 'includes/header.php';

// Fetch submitted studies from database
$studies_list = [];
try {
    $stmt = $pdo->query("SELECT * FROM `studies` ORDER BY `id` DESC LIMIT 10");
    $studies_list = $stmt->fetchAll();
} catch (Exception $e) {
    // Keep empty if error
}
?>

<div class="container my-5">
    <div class="text-center mb-5">
        <span class="badge bg-success bg-opacity-10 text-success px-3 py-2 rounded-pill mb-2">Prestations d'Ingénierie</span>
        <h1 class="fw-bold text-primary display-5">Études Techniques Hydrauliques</h1>
        <p class="text-muted max-w-lg mx-auto">
            Vous représentez un bureau d'études, une collectivité locale ou une entreprise ? Soumettez votre cahier des charges et faites réaliser vos dimensionnements par nos experts chevronnés.
        </p>
    </div>

    <div class="row g-4">
        <!-- Core engineering services overview -->
        <div class="col-lg-7">
            <h3 class="fw-bold mb-4 text-primary"><i class="bi bi-gear-wide-connected me-2"></i>Nos Domaines d'Intervention</h3>
            
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="p-3 bg-white rounded-3 shadow-sm border border-light h-100">
                        <div class="fs-3 text-info mb-2"><i class="bi bi-droplet"></i></div>
                        <h6 class="fw-bold text-dark">Modélisation de Réseaux AEP</h6>
                        <p class="text-muted small mb-0">Simulation dynamique sous EPANET, équilibrage des pressions, calage des pertes de charge et stratégies anti-coup de bélier.</p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="p-3 bg-white rounded-3 shadow-sm border border-light h-100">
                        <div class="fs-3 text-warning mb-2"><i class="bi bi-filter-square"></i></div>
                        <h6 class="fw-bold text-dark">Diagnostic d'Assainissement</h6>
                        <p class="text-muted small mb-0">Plan de drainage des eaux usées urbaines, calcul de bassins de rétention et régulateurs de débit d'eaux pluviales.</p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="p-3 bg-white rounded-3 shadow-sm border border-light h-100">
                        <div class="fs-3 text-success mb-2"><i class="bi bi-flower1"></i></div>
                        <h6 class="fw-bold text-dark">Périmètres d'Irrigation</h6>
                        <p class="text-muted small mb-0">Conception de réseaux d'irrigation sous pression et goutte-à-goutte, études de besoins hydriques agricoles.</p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="p-3 bg-white rounded-3 shadow-sm border border-light h-100">
                        <div class="fs-3 text-danger mb-2"><i class="bi bi-bank"></i></div>
                        <h6 class="fw-bold text-dark">Ouvrages & Stations de Pompage</h6>
                        <p class="text-muted small mb-0">Calcul hydraulique de conduites de refoulement, dimensionnement de stations de relevage et châteaux d'eau.</p>
                    </div>
                </div>
                <div class="col-12">
                    <div class="p-3 bg-success bg-opacity-10 text-success rounded-3 border border-success border-opacity-20 d-flex align-items-center">
                        <i class="bi bi-recycle fs-3 me-3"></i>
                        <div>
                            <h6 class="fw-bold mb-1">Études d'Éco-conception & Réutilisation (REUSE)</h6>
                            <p class="text-dark-50 small mb-0">Nous concevons des scénarios d'utilisation des effluents épurés conformes à la réglementation agricole et environnementale algérienne.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Submission Form -->
        <div class="col-lg-5">
            <div class="card border-0 bg-white shadow-lg rounded-4 p-4">
                <h4 class="fw-bold text-primary mb-3"><i class="bi bi-file-earmark-arrow-up me-2"></i>Soumettre une Demande</h4>
                <p class="small text-muted mb-4">Décrivez brièvement les caractéristiques de votre projet d'étude pour recevoir une proposition méthodologique et financière.</p>
                
                <div id="studyAlert" class="alert d-none mb-3" role="alert"></div>

                <form id="studyForm" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label class="form-label text-muted small fw-bold">Nom Complet du contact</label>
                        <input type="text" name="name" class="form-control bg-light border-0" placeholder="Ex: Mohamed Khemisti" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-muted small fw-bold">Organisme / Bureau d'études / Société</label>
                        <input type="text" name="organization" class="form-control bg-light border-0" placeholder="Ex: HydrAlger BE / Porteur projet" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-muted small fw-bold">Adresse E-mail</label>
                        <input type="email" name="email" class="form-control bg-light border-0" placeholder="Ex: contact@mydomain.com" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-muted small fw-bold">Numéro de téléphone</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-0 text-primary">
                                <i class="bi bi-telephone-fill"></i>
                            </span>
                            <input type="tel" name="phone" class="form-control bg-light border-0" placeholder="Ex: 0550 123 456" pattern="[0-9+\s\-]{8,15}" required>
                        </div>
                        <div class="form-text text-muted" style="font-size: 0.75rem;"><i class="bi bi-info-circle me-1"></i>Un ingénieur vous contactera sous 48h pour échanger sur votre projet.</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-muted small fw-bold">Type d'étude hydraulique</label>
                        <select name="study_type" class="form-select bg-light border-0" required>
                            <option value="Modélisation & Calage de Réseau AEP">Modélisation & Calage de Réseau AEP</option>
                            <option value="Diagnostic & Dimensionnement Assainissement">Diagnostic & Dimensionnement Assainissement</option>
                            <option value="Conception Périmètre d'Irrigation Agricole">Conception Périmètre d'Irrigation Agricole</option>
                            <option value="Dimensionnement Ouvrages & Stations de Pompage">Dimensionnement Ouvrages & Stations de Pompage</option>
                            <option value="Étude REUSE (Réutilisation eaux STEP)">Étude REUSE (Réutilisation eaux STEP)</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-muted small fw-bold">Description de votre besoin (débits, linéaire, objectifs...)</label>
                        <textarea name="description" class="form-control bg-light border-0" rows="3" placeholder="Indiquez brièvement le type de réseau, le linéaire approximatif, la localisation et le délai souhaité..." required></textarea>
                    </div>

                    <div class="mb-4">
                        <label class="form-label text-muted small fw-bold">Document joint (Cahier des charges, plans, EPANET... Optionnel)</label>
                        <input type="file" name="project_file" class="form-control bg-light border-0 text-secondary" id="projectFile" accept=".pdf,.docx,.doc,.zip,.rar,.dwg,.inp,.net" style="font-size: 0.9rem;">
                        <div class="form-text text-muted" style="font-size: 0.75rem;">Formats acceptés : PDF, Word, ZIP/RAR, CAD (dwg), EPANET (inp, net). Max : 10 Mo.</div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 rounded-pill py-2.5 shadow-md" id="btnSubmitStudy">
                        Envoyer ma demande <i class="bi bi-send-fill ms-2"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Active studies dashboard tracking list -->
    <div class="row mt-5">
        <div class="col-md-12">
            <div class="p-4 bg-white rounded-4 shadow-sm border border-light">
                <h3 class="fw-bold mb-4 text-primary"><i class="bi bi-folder-symlink me-2"></i>Suivi des Demandes d'Études en cours</h3>
                
                <?php if (empty($studies_list)): ?>
                    <div class="text-center py-4 text-muted">
                        <i class="bi bi-folder-x fs-1 mb-2"></i>
                        <p class="mb-0">Aucun dossier d'étude déposé récemment.</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light text-secondary">
                                <tr>
                                    <th>Demandeur / Organisme</th>
                                    <th>Type d'Étude</th>
                                    <th>Date de Dépôt</th>
                                    <th>Description</th>
                                    <th>Statut de l'Analyse</th>
                                </tr>
                            </thead>
                            <tbody id="studiesTableBody">
                                <?php foreach ($studies_list as $study): ?>
                                    <tr>
                                        <td>
                                            <div class="fw-bold"><?php echo htmlspecialchars($study['name']); ?></div>
                                            <small class="text-info"><?php echo htmlspecialchars($study['organization']); ?></small>
                                        </td>
                                        <td>
                                            <span class="badge bg-primary-subtle text-primary-emphasis"><?php echo htmlspecialchars($study['study_type']); ?></span>
                                        </td>
                                        <td>
                                            <small class="text-muted"><i class="bi bi-calendar3 me-1"></i><?php echo date('d-m-Y', strtotime($study['created_at'])); ?></small>
                                        </td>
                                        <td>
                                            <span class="text-muted d-inline-block text-truncate" style="max-width: 280px;" title="<?php echo htmlspecialchars($study['description']); ?>">
                                                <?php echo htmlspecialchars($study['description']); ?>
                                            </span>
                                            <?php if (!empty($study['file_path'])): ?>
                                                <div class="small text-info mt-1"><i class="bi bi-paperclip me-1"></i>Fichier joint</div>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($study['status'] == 'Approuvé'): ?>
                                                <span class="pill-status bg-success bg-opacity-10 text-success"><i class="bi bi-check-circle-fill me-1"></i>Approuvé</span>
                                            <?php elseif ($study['status'] == 'Devis envoyé'): ?>
                                                <span class="pill-status bg-info bg-opacity-10 text-info"><i class="bi bi-file-earmark-medical-fill me-1"></i>Devis envoyé</span>
                                            <?php else: ?>
                                                <span class="pill-status bg-warning bg-opacity-10 text-warning"><i class="bi bi-hourglass-split me-1"></i>Reçu / En analyse</span>
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
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('studyForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const btnSubmit = document.getElementById('btnSubmitStudy');
        const studyAlert = document.getElementById('studyAlert');
        
        btnSubmit.disabled = true;
        btnSubmit.innerHTML = 'Envoi en cours... <span class="spinner-border spinner-border-sm ms-2" role="status" aria-hidden="true"></span>';
        
        const formData = new FormData(this);
        
        fetch('api/submit_study.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            btnSubmit.disabled = false;
            btnSubmit.innerHTML = 'Envoyer ma demande <i class="bi bi-send-fill ms-2"></i>';
            
            studyAlert.classList.remove('d-none', 'alert-success', 'alert-danger');
            
            if(data.success) {
                studyAlert.classList.add('alert-success');
                studyAlert.innerHTML = '<i class="bi bi-check-circle-fill me-2"></i>' + data.message;
                
                // Reset form
                document.getElementById('studyForm').reset();
                
                // Append row
                if (data.study) {
                    const tableBody = document.getElementById('studiesTableBody');
                    const newRow = `
                        <tr>
                            <td>
                                <div class="fw-bold">${escapeHtml(data.study.name)}</div>
                                <small class="text-info">${escapeHtml(data.study.organization)}</small>
                            </td>
                            <td>
                                <span class="badge bg-primary-subtle text-primary-emphasis">${escapeHtml(data.study.study_type)}</span>
                            </td>
                            <td>
                                <small class="text-muted"><i class="bi bi-calendar3 me-1"></i>${getFormattedToday()}</small>
                            </td>
                            <td>
                                <span class="text-muted d-inline-block text-truncate" style="max-width: 280px;" title="${escapeHtml(data.study.description)}">
                                    ${escapeHtml(data.study.description)}
                                </span>
                                ${data.study.file_path ? `<div class="small text-info mt-1"><i class="bi bi-paperclip me-1"></i>Fichier joint</div>` : ''}
                            </td>
                            <td>
                                <span class="pill-status bg-warning bg-opacity-10 text-warning"><i class="bi bi-hourglass-split me-1"></i>Reçu / En analyse</span>
                            </td>
                        </tr>
                    `;
                    
                    if (tableBody) {
                        tableBody.insertAdjacentHTML('afterbegin', newRow);
                    } else {
                        // Reload if previously empty
                        setTimeout(() => {
                            window.location.reload();
                        }, 1000);
                    }
                }
                
                setTimeout(() => {
                    studyAlert.classList.add('d-none');
                }, 3000);
            } else {
                studyAlert.classList.add('alert-danger');
                studyAlert.innerHTML = '<i class="bi bi-exclamation-triangle-fill me-2"></i>' + data.message;
            }
        })
        .catch(err => {
            btnSubmit.disabled = false;
            btnSubmit.innerHTML = 'Envoyer ma demande <i class="bi bi-send-fill ms-2"></i>';
            studyAlert.classList.remove('d-none');
            studyAlert.classList.add('alert-danger');
            studyAlert.innerHTML = '<i class="bi bi-exclamation-triangle-fill me-2"></i> Une erreur réseau s\'est produite.';
        });
    });
});

function escapeHtml(str) {
    return str.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;").replace(/'/g, "&#039;");
}

function getFormattedToday() {
    const today = new Date();
    const dd = String(today.getDate()).padStart(2, '0');
    const mm = String(today.getMonth() + 1).padStart(2, '0'); // January is 0
    const yyyy = today.getFullYear();
    return dd + '-' + mm + '-' + yyyy;
}
</script>

<?php include_once 'includes/footer.php'; ?>
