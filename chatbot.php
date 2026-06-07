<?php
require_once 'config/db.php';
include_once 'includes/header.php';
?>

<div class="container my-5">
    <div class="text-center mb-5">
        <span class="badge bg-info bg-opacity-10 text-info px-3 py-2 rounded-pill mb-2"><i class="bi bi-cpu me-1"></i>Intelligence Artificielle</span>
        <h1 class="fw-bold text-primary display-5">HydroBot AI</h1>
        <p class="text-muted max-w-lg mx-auto">
            Posez des questions d'ingénierie, effectuez des pré-dimensionnements instantanés ou consultez les normes réglementaires algériennes.
        </p>
    </div>

    <div class="row g-4">
        <!-- Templates & Quick Guide Sidebar -->
        <div class="col-lg-4">
            <div class="card border-0 bg-white shadow-sm rounded-4 p-4 mb-4">
                <h5 class="fw-bold mb-3 text-primary"><i class="bi bi-calculator me-2"></i>Modèles de Calculs</h5>
                <p class="small text-muted mb-3">Cliquez sur un modèle ci-dessous pour le charger dans la console de chat et exécuter un pré-dimensionnement :</p>
                
                <div class="d-flex flex-column gap-2">
                    <button class="btn btn-outline-primary text-start rounded-3 p-2.5 small" onclick="loadTemplateQuery('Calculer Manning Ks=90 D=0.4 I=0.005')">
                        <div class="fw-bold mb-1 small text-dark"><i class="bi bi-water me-2 text-info"></i>Manning-Strickler</div>
                        <span class="text-muted small" style="font-size: 0.75rem;">Exemple : Conduite PVC D=400mm, pente 0.5%</span>
                    </button>
                    
                    <button class="btn btn-outline-primary text-start rounded-3 p-2.5 small" onclick="loadTemplateQuery('Calculer Refoulement Q=30')">
                        <div class="fw-bold mb-1 small text-dark"><i class="bi bi-activity me-2 text-info"></i>Diamètre Économique (Refoulement)</div>
                        <span class="text-muted small" style="font-size: 0.75rem;">Exemple : Station de pompage, débit Q=30 L/s</span>
                    </button>
                    
                    <button class="btn btn-outline-primary text-start rounded-3 p-2.5 small" onclick="loadTemplateQuery('Normes de consommation AEP en Algérie')">
                        <div class="fw-bold mb-1 small text-dark"><i class="bi bi-journal-check me-2 text-info"></i>Dotation AEP (ADE)</div>
                        <span class="text-muted small" style="font-size: 0.75rem;">Normes de dotation d'eau par habitant</span>
                    </button>
                    
                    <button class="btn btn-outline-primary text-start rounded-3 p-2.5 small" onclick="loadTemplateQuery('Normes de réutilisation des eaux usées STEP REUSE')">
                        <div class="fw-bold mb-1 small text-dark"><i class="bi bi-recycle me-2 text-info"></i>Normes REUSE (STEP)</div>
                        <span class="text-muted small" style="font-size: 0.75rem;">Norme algérienne NA 17099 (Irrigation agricole)</span>
                    </button>
                </div>
            </div>

            <!-- Software Guide Card -->
            <div class="card border-0 bg-light rounded-4 p-4">
                <h5 class="fw-bold text-dark mb-2"><i class="bi bi-info-circle-fill me-2 text-info"></i>Conseils Pratiques</h5>
                <p class="small text-muted mb-0">
                    HydroBot supporte également des explications générales sur les logiciels phares. Essayez d'écrire : <strong>"Modéliser sous EPANET"</strong> ou <strong>"Modélisation HEC-RAS"</strong> pour obtenir des consignes de calage ou de paramétrage géométrique.
                </p>
            </div>
        </div>

        <!-- Chat Area -->
        <div class="col-lg-8">
            <div class="chat-container">
                <!-- Chat Header -->
                <div class="chat-header">
                    <div class="chat-avatar">
                        <i class="bi bi-robot"></i>
                    </div>
                    <div>
                        <h5 class="mb-0 text-white fw-bold">HydroBot AI</h5>
                        <small class="text-info d-flex align-items-center">
                            <span class="bg-success rounded-circle d-inline-block me-1.5" style="width: 8px; height: 8px;"></span>
                            Conseiller Technique Connecté
                        </small>
                    </div>
                </div>

                <!-- Message Log -->
                <div class="chat-messages d-flex flex-column" id="chatMessages">
                    <!-- Default greeting -->
                    <div class="d-flex justify-content-start mb-3">
                        <div class="chat-bubble bubble-received">
                            Bonjour ! Je suis <strong>HydroBot</strong>, l'assistant d'Intelligence Artificielle d'Hydrolicia.<br><br>
                            Je peux vous aider à effectuer des calculs de dimensionnement rapides ou à consulter les spécifications réglementaires nationales.<br><br>
                            Comment puis-je vous accompagner dans vos études hydrauliques aujourd'hui ?
                        </div>
                    </div>
                </div>

                <!-- Input Area -->
                <div class="chat-input-area">
                    <form id="chatForm" class="d-flex gap-2">
                        <input type="text" id="chatInput" class="form-control rounded-pill border-light bg-light px-4 py-2.5 text-dark" placeholder="Posez une question technique ou lancez un calcul..." autocomplete="off">
                        <button type="submit" class="btn btn-info rounded-circle d-flex align-items-center justify-content-center shadow-md text-dark" style="width: 48px; height: 48px;">
                            <i class="bi bi-send-fill"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chat JS logic -->
<script src="assets/js/chat.js"></script>

<?php include_once 'includes/footer.php'; ?>
