<?php
// Include db file to auto-initialize DB and tables upon first load
require_once 'config/db.php';
include_once 'includes/header.php';

// Fetch quick stats to make homepage look alive
$stats = [
    'registrations' => 0,
    'consultations' => 0,
    'questions' => 0,
    'studies' => 0
];

try {
    $stmt1 = $pdo->query("SELECT COUNT(*) FROM `registrations`");
    $stats['registrations'] = $stmt1->fetchColumn();

    $stmt2 = $pdo->query("SELECT COUNT(*) FROM `consultations`");
    $stats['consultations'] = $stmt2->fetchColumn();

    $stmt3 = $pdo->query("SELECT COUNT(*) FROM `forum_questions`");
    $stats['questions'] = $stmt3->fetchColumn();

    $stmt4 = $pdo->query("SELECT COUNT(*) FROM `studies`");
    $stats['studies'] = $stmt4->fetchColumn();
} catch (Exception $e) {
    // If table not loaded yet, keep 0
}
?>

<!-- Hero Section -->
<section class="hero-section">
    <div class="container position-relative">
        <div class="row align-items-center g-5">
            <div class="col-lg-7">
                <span class="hero-badge"><i class="bi bi-patch-check-fill me-1"></i> Plateforme Digitale Intelligente</span>
                <h1 class="display-4 fw-bold text-white mb-4">
                    Propulsez Vos Compétences en <span class="text-info">Ingénierie Hydraulique</span>
                </h1>
                <p class="lead text-white-50 mb-5">
                    Hydrolicia accompagne les étudiants, jeunes ingénieurs et bureaux d'études en Algérie grâce à des formations appliquées, des consultations d'experts en ligne et un assistant intelligent d'IA.
                </p>
                <div class="d-flex flex-wrap gap-3">
                    <a href="trainings.php" class="btn btn-info btn-lg rounded-pill px-4 py-3 text-dark font-weight-bold shadow-lg">
                        <i class="bi bi-mortarboard-fill me-2"></i>Découvrir nos formations
                    </a>
                    <a href="chatbot.php" class="btn btn-outline-light btn-lg rounded-pill px-4 py-3">
                        <i class="bi bi-robot me-2"></i>Essayer HydroBot AI
                    </a>
                </div>
            </div>
            <div class="col-lg-5 text-center d-none d-lg-block">
                <div class="floating-element">
                    <!-- Glassmorphism card displaying premium info -->
                    <div class="card bg-white bg-opacity-10 border border-white border-opacity-20 rounded-4 text-start p-4 backdrop-blur shadow-2xl">
                        <div class="d-flex align-items-center mb-4">
                            <div class="bg-info bg-opacity-20 p-3 rounded-3 me-3 text-info">
                                <i class="bi bi-activity fs-3 text-white"></i>
                            </div>
                            <div>
                                <h5 class="text-white mb-0">Indicateurs Hydriques</h5>
                                <small class="text-info">Algérie & Gestion des ressources</small>
                            </div>
                        </div>
                        <p class="text-white-50 small mb-3">
                            <i class="bi bi-info-circle text-info me-2"></i>Plus de 80 STEPs (Stations d'Épuration) en activité, poussant vers une politique active de réutilisation des eaux épurées.
                        </p>
                        <div class="border-top border-white border-opacity-10 pt-3 mt-3">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-white-50 small">Rendement réseau ciblé</span>
                                <span class="badge bg-success rounded-pill">> 80%</span>
                            </div>
                            <div class="progress bg-white bg-opacity-10" style="height: 6px;">
                                <div class="progress-bar bg-success" role="progressbar" style="width: 80%" aria-valuenow="80" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Stats Counters Section -->
<section class="py-5 bg-white border-bottom border-light shadow-sm">
    <div class="container">
        <div class="row g-4 text-center">
            <div class="col-6 col-md-3">
                <div class="p-3 border-end border-light">
                    <h2 class="display-6 fw-bold text-primary mb-1"><?php echo (12 + $stats['registrations']); ?></h2>
                    <p class="text-muted mb-0 small uppercase font-weight-bold">Inscriptions Formations</p>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="p-3 border-end border-light">
                    <h2 class="display-6 fw-bold text-primary mb-1"><?php echo (8 + $stats['consultations']); ?></h2>
                    <p class="text-muted mb-0 small uppercase font-weight-bold">Consultations Planifiées</p>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="p-3 border-end border-light">
                    <h2 class="display-6 fw-bold text-primary mb-1"><?php echo (42 + $stats['questions']); ?></h2>
                    <p class="text-muted mb-0 small uppercase font-weight-bold">Questions Forum</p>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="p-3">
                    <h2 class="display-6 fw-bold text-primary mb-1"><?php echo (5 + $stats['studies']); ?></h2>
                    <p class="text-muted mb-0 small uppercase font-weight-bold">Projets d'Études Déposés</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Core Solutions Section -->
<section class="py-5">
    <div class="container">
        <div class="text-center max-w-xl mx-auto mb-5">
            <h2 class="fw-bold text-primary">Nos Solutions Innovantes</h2>
            <p class="text-muted">Digitaliser l'ingénierie hydraulique pour surmonter le déficit d'accompagnement technique</p>
        </div>
        
        <div class="row g-4">
            <!-- Card 1 -->
            <div class="col-md-4">
                <div class="card hydro-card h-100 p-4">
                    <div class="card-icon-wrapper icon-blue">
                        <i class="bi bi-mortarboard"></i>
                    </div>
                    <h4 class="fw-bold mb-3">Formations Pratiques</h4>
                    <p class="text-muted mb-4">
                        Maîtrisez les logiciels phares du secteur (EPANET, SewerGEMS, HEC-RAS, CropWat) à travers des projets réels et des guides de dimensionnement appliqués aux normes algériennes.
                    </p>
                    <a href="trainings.php" class="text-info fw-bold text-decoration-none mt-auto">Voir les modules <i class="bi bi-arrow-right"></i></a>
                </div>
            </div>
            <!-- Card 2 -->
            <div class="col-md-4">
                <div class="card hydro-card h-100 p-4">
                    <div class="card-icon-wrapper icon-orange">
                        <i class="bi bi-chat-left-quote"></i>
                    </div>
                    <h4 class="fw-bold mb-3">Consultations Experts</h4>
                    <p class="text-muted mb-4">
                        Réservez un créneau en ligne avec des experts seniors (modélisateurs de réseaux, hydrologues, ingénieurs en assainissement) pour valider vos notes de calcul et modélisations.
                    </p>
                    <a href="consultations.php" class="text-orange fw-bold text-decoration-none mt-auto">Réserver un expert <i class="bi bi-arrow-right"></i></a>
                </div>
            </div>
            <!-- Card 3 -->
            <div class="col-md-4">
                <div class="card hydro-card h-100 p-4">
                    <div class="card-icon-wrapper icon-teal">
                        <i class="bi bi-robot"></i>
                    </div>
                    <h4 class="fw-bold mb-3">Intelligence Artificielle</h4>
                    <p class="text-muted mb-4">
                        Interrogez notre <strong>HydroBot AI</strong> pour vos calculs rapides (formule de Manning-Strickler, diamètres économiques) et vos questions sur les règlements techniques locaux.
                    </p>
                    <a href="chatbot.php" class="text-teal fw-bold text-decoration-none mt-auto">Lancer l'assistant <i class="bi bi-arrow-right"></i></a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Environmental Impact Section -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-6">
                <div class="p-3 bg-white rounded-4 shadow-sm border border-light">
                    <div class="d-flex align-items-center mb-4">
                        <div class="bg-success bg-opacity-10 text-success p-3 rounded-3 me-3">
                            <i class="bi bi-recycle fs-3"></i>
                        </div>
                        <h3 class="fw-bold text-dark mb-0">Impact Écologique & Durabilité</h3>
                    </div>
                    
                    <ul class="list-unstyled">
                        <li class="d-flex mb-4">
                            <i class="bi bi-droplet-fill text-success fs-5 me-3"></i>
                            <div>
                                <h6 class="fw-bold mb-1">Optimisation des Réseaux AEP</h6>
                                <p class="text-muted small mb-0">Nos cours de modélisation enseignent le calage et la détection de fuites sous EPANET pour réduire les pertes physiques dans les réseaux de distribution d'eau potable.</p>
                            </div>
                        </li>
                        <li class="d-flex mb-4">
                            <i class="bi bi-arrow-repeat text-success fs-5 me-3"></i>
                            <div>
                                <h6 class="fw-bold mb-1">Réutilisation des Eaux Usées Épurées (REUSE)</h6>
                                <p class="text-muted small mb-0">Nous mettons l'accent sur les modules de traitement secondaire/tertiaire (STEP) pour encourager l'irrigation agricole sécurisée des cultures selon la norme NA 17099.</p>
                            </div>
                        </li>
                        <li class="d-flex">
                            <i class="bi bi-shield-check text-success fs-5 me-3"></i>
                            <div>
                                <h6 class="fw-bold mb-1">Gestion Résiliente des Crues</h6>
                                <p class="text-muted small mb-0">Nous formons à l'utilisation d'outils comme HEC-RAS pour concevoir des ouvrages de protection respectant l'écoulement naturel et l'équilibre environnemental des oueds.</p>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
            
            <div class="col-lg-6">
                <span class="text-success uppercase small font-weight-bold d-block mb-2"><i class="bi bi-tree-fill me-1"></i> Économie Verte & Savoir</span>
                <h2 class="fw-bold text-primary mb-4">Comment Hydrolicia contribue au Plan National de l'Eau</h2>
                <p class="text-muted mb-4">
                    L'Algérie fait face à un stress hydrique permanent. Former des ingénieurs compétents et capables de concevoir des réseaux d'eau sans fuite et des systèmes d'irrigation optimisés n'est pas seulement un besoin professionnel, c'est un impératif écologique national.
                </p>
                <div class="card border-0 bg-success bg-opacity-10 text-success p-4 rounded-4">
                    <h5 class="fw-bold mb-2"><i class="bi bi-globe-europe-africa me-2"></i>Objectif Zéro Gaspillage</h5>
                    <p class="mb-0 small text-dark-50">
                        Chaque projet d'étude hydraulique soumis sur notre plateforme est conçu en intégrant des critères d'éco-conception pour minimiser l'empreinte carbone des stations de pompage et optimiser les pertes de charge.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Target Markets / Target Audience -->
<section class="py-5">
    <div class="container">
        <div class="text-center max-w-xl mx-auto mb-5">
            <h2 class="fw-bold text-primary">Qui Accompagnons-nous ?</h2>
            <p class="text-muted">Des profils ciblés pour un impact sectoriel structuré</p>
        </div>
        
        <div class="row g-4">
            <div class="col-md-3">
                <div class="p-4 bg-white rounded-3 shadow-sm border border-light text-center h-100">
                    <div class="fs-1 text-info mb-3"><i class="bi bi-mortarboard-fill"></i></div>
                    <h5 class="fw-bold">Étudiants</h5>
                    <p class="text-muted small">Complément pratique aux cours théoriques universitaires.</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="p-4 bg-white rounded-3 shadow-sm border border-light text-center h-100">
                    <div class="fs-1 text-primary mb-3"><i class="bi bi-award-fill"></i></div>
                    <h5 class="fw-bold">Diplômés</h5>
                    <p class="text-muted small">Insertion professionnelle rapide grâce aux compétences logicielles.</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="p-4 bg-white rounded-3 shadow-sm border border-light text-center h-100">
                    <div class="fs-1 text-orange mb-3"><i class="bi bi-building-fill-gear"></i></div>
                    <h5 class="fw-bold">Bureaux d'Études</h5>
                    <p class="text-muted small">Validation technique de projets et formation de leurs ingénieurs.</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="p-4 bg-white rounded-3 shadow-sm border border-light text-center h-100">
                    <div class="fs-1 text-success mb-3"><i class="bi bi-people-fill"></i></div>
                    <h5 class="fw-bold">Collectivités</h5>
                    <p class="text-muted small">Aide à la décision pour la modernisation des infrastructures.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Call to Action Banner -->
<section class="py-5 bg-primary text-white text-center rounded-4 mx-3 my-5 position-relative overflow-hidden shadow-lg">
    <div class="position-absolute top-0 start-0 w-100 h-100 bg-gradient opacity-25" style="background: radial-gradient(circle, rgba(255,255,255,0.15) 0%, rgba(0,0,0,0) 80%);"></div>
    <div class="container position-relative py-4">
        <h2 class="fw-bold mb-3">Besoin d'une étude technique ou d'un conseil ?</h2>
        <p class="lead text-white-50 mb-4 max-w-lg mx-auto">
            Déposez votre cahier des charges et nos experts hydrauliciens s'occuperont du dimensionnement de vos réseaux ou de la modélisation de vos ouvrages.
        </p>
        <div class="d-flex justify-content-center gap-3">
            <a href="studies.php" class="btn btn-info rounded-pill px-4 py-3 font-weight-bold text-dark"><i class="bi bi-file-earmark-pdf me-2"></i>Soumettre une étude</a>
            <a href="consultations.php" class="btn btn-outline-light rounded-pill px-4 py-3"><i class="bi bi-calendar-check me-2"></i>Contacter un expert</a>
        </div>
    </div>
</section>

<?php include_once 'includes/footer.php'; ?>
