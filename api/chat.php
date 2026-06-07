<?php
// api/chat.php
header('Content-Type: application/json');

$message = isset($_POST['message']) ? trim($_POST['message']) : '';

if (empty($message)) {
    echo json_encode(['success' => false, 'message' => 'Message vide.']);
    exit;
}

$response = '';
$calc_details = '';

// Conversion to lowercase for matching
$msg_lower = mb_strtolower($message, 'UTF-8');

// Match 1: Manning-Strickler calculation
if (preg_match('/manning|strickler/i', $msg_lower)) {
    // Check if user provided variables like D=0.4, I=0.01, K=90
    // Try to extract values: D or d (diameter in m), I or i (slope), K or Ks (Strickler coefficient)
    $d = 0; $slope = 0; $k = 0;
    
    // Regex matches e.g. D=0.4 or d=0.4 or diameter=0.4
    if (preg_match('/[dD](?:iametre)?\s*=\s*([0-9.]+)/', $message, $match_d)) {
        $d = floatval($match_d[1]);
    }
    if (preg_match('/[iI](?:pente)?\s*=\s*([0-9.]+)/', $message, $match_i)) {
        $slope = floatval($match_i[1]);
    }
    if (preg_match('/[kK](?:s)?\s*=\s*([0-9.]+)/', $message, $match_k)) {
        $k = floatval($match_k[1]);
    }

    if ($d > 0 && $slope > 0 && $k > 0) {
        // Run full-pipe Manning-Strickler calculation
        // Area S = pi * D^2 / 4
        // Wet perimeter P = pi * D
        // Hydraulic radius R = D / 4
        // Flow rate Q = Ks * S * R^(2/3) * I^(1/2)
        // Velocity V = Ks * R^(2/3) * I^(1/2)
        
        $area = M_PI * pow($d, 2) / 4;
        $r_h = $d / 4;
        $velocity = $k * pow($r_h, 2/3) * sqrt($slope);
        $flow = $velocity * $area; // m3/s
        
        $flow_lps = $flow * 1000; // L/s
        
        $response = "🤖 <strong>Calculateur Manning-Strickler (Section Pleine) :</strong><br><br>";
        $response .= "Données reçues :<br>";
        $response .= "• Diamètre (D) = " . $d . " m (DN " . ($d*1000) . ")<br>";
        $response .= "• Pente (I) = " . $slope . " m/m (" . ($slope*100) . " %)<br>";
        $response .= "• Coefficient de Strickler (Ks) = " . $k . " (PVC/PEHD)<br><br>";
        $response .= "<strong>Résultats :</strong><br>";
        $response .= "• Rayon hydraulique (Rh) = D/4 = " . round($r_h, 4) . " m<br>";
        $response .= "• Vitesse d'écoulement (V) = Ks * Rh^(2/3) * I^(1/2) = <strong>" . round($velocity, 2) . " m/s</strong><br>";
        $response .= "• Débit pleine section (Qp) = V * Section = <strong>" . round($flow, 4) . " m³/s</strong> (soit <strong>" . round($flow_lps, 1) . " L/s</strong>).<br><br>";
        $response .= "<span class='text-muted small'><i class='bi bi-info-circle'></i> Remarque : Pour l'assainissement gravitaire en Algérie, la vitesse d'autocurage à section pleine doit idéalement être supérieure à 0.6 m/s pour éviter les dépôts.</span>";
    } else {
        $response = "🤖 <strong>Formule de Manning-Strickler (Assainissement Gravitaire) :</strong><br><br>";
        $response .= "La formule fondamentale pour les conduites circulaires à écoulement libre est :<br>";
        $response .= "<code>V = Ks * Rh^(2/3) * I^(1/2)</code> et <code>Q = V * S</code><br><br>";
        $response .= "Où :<br>";
        $response .= "• <strong>V</strong> : Vitesse moyenne (m/s)<br>";
        $response .= "• <strong>Ks</strong> : Rugosité de Strickler (PVC/PEHD ≈ 90, Béton lisse ≈ 80, Béton brut ≈ 70)<br>";
        $response .= "• <strong>Rh</strong> : Rayon hydraulique = Section mouillée / Périmètre mouillé (Pour section pleine, Rh = D / 4)<br>";
        $response .= "• <strong>I</strong> : Pente de la canalisation (m/m)<br>";
        $response .= "• <strong>Q</strong> : Débit d'écoulement (m³/s)<br><br>";
        $response .= "💡 <strong>Essayez de me faire calculer en tapant exactement :</strong><br>";
        $response .= "<code>Calculer Manning Ks=90 D=0.3 I=0.01</code>";
    }
}
// Match 2: Economic diameter (Bresse formula)
elseif (preg_match('/economique|bresse|pompage|refoulement/i', $msg_lower)) {
    $q_val = 0;
    
    // Extract Q in L/s or m3/s
    if (preg_match('/[qQ]\s*=\s*([0-9.]+)\s*(lps|l\/s|L\/s)/i', $message, $match_q_lps)) {
        $q_val = floatval($match_q_lps[1]) / 1000.0;
    } elseif (preg_match('/[qQ]\s*=\s*([0-9.]+)/', $message, $match_q)) {
        $q_val = floatval($match_q[1]);
        // If user typed e.g. 50 and meant L/s, let's assume it's m3/s if < 5, otherwise L/s
        if ($q_val > 2) {
            $q_val = $q_val / 1000.0; // Assume L/s
        }
    }
    
    if ($q_val > 0) {
        // Bresse Formula: D = 1.5 * sqrt(Q) (under 24h pumping) or D = 1.2 * sqrt(Q) (standard)
        // Standard Refoulement formula in Algeria (often based on V economic = 1 m/s):
        // D = sqrt(4Q / (pi * V)) = 1.13 * sqrt(Q)
        $d_bresse_12 = 1.2 * sqrt($q_val);
        $d_eco_1 = sqrt((4 * $q_val) / (M_PI * 1.0));
        
        $response = "🤖 <strong>Dimensionnement Conduite de Refoulement AEP :</strong><br><br>";
        $response .= "Pour un débit de refoulement Q = " . round($q_val * 1000, 1) . " L/s (soit " . round($q_val, 4) . " m³/s) :<br><br>";
        $response .= "<strong>1. Formule de Bresse standard (D = 1.2 * √Q) :</strong><br>";
        $response .= "• Diamètre théorique = " . round($d_bresse_12, 3) . " m (soit DN " . round($d_bresse_12 * 1000) . " mm).<br><br>";
        $response .= "<strong>2. Diamètre pour vitesse économique (V ≈ 1.0 m/s) :</strong><br>";
        $response .= "• Diamètre théorique = " . round($d_eco_1, 3) . " m (soit DN " . round($d_eco_1 * 1000) . " mm).<br><br>";
        $response .= "<strong>Recommandation commerciale (Classements PN en Algérie) :</strong><br>";
        $d_mm = $d_eco_1 * 1000;
        if ($d_mm <= 110) {
            $response .= "👉 Sélectionnez un tube PEHD de diamètre extérieur 110 mm (DN 90 intérieur).";
        } elseif ($d_mm <= 160) {
            $response .= "👉 Sélectionnez un tube PEHD ou Fonte de DN 150 (Diamètre Extérieur 160 mm).";
        } elseif ($d_mm <= 225) {
            $response .= "👉 Sélectionnez un tube PEHD de 225 mm ou Fonte DN 200.";
        } else {
            $suggested_dn = ceil($d_mm / 50) * 50;
            $response .= "👉 Sélectionnez le diamètre nominal normalisé supérieur le plus proche : <strong>DN " . $suggested_dn . "</strong>.";
        }
    } else {
        $response = "🤖 <strong>Formule du Diamètre Économique (Pumping Conduite) :</strong><br><br>";
        $response .= "Pour calculer le diamètre optimal d'une conduite de refoulement (AEP) reliée à une station de pompage, on utilise :<br>";
        $response .= "• La formule simplifiée de Bresse : <code>D = 1.2 * √Q</code> (pour un fonctionnement continu de 24h)<br>";
        $response .= "• La relation débit-section pour une vitesse économique $V = 1$ m/s : <code>D = √(4*Q / (π * V))</code><br><br>";
        $response .= "💡 <strong>Essayez de me faire calculer en tapant exactement :</strong><br>";
        $response .= "<code>Calculer Refoulement Q=50</code> (pour 50 L/s)";
    }
}
// Match 3: Water consumption norms in Algeria
elseif (preg_match('/consommation|norme|besoin|ratio/i', $msg_lower)) {
    $response = "🤖 <strong>Normes de dotation en eau potable (AEP) en Algérie :</strong><br><br>";
    $response .= "Les directives de l'Algérienne Des Eaux (ADE) et du Ministère de l'Hydraulique prévoient les dotations suivantes par habitant et par jour :<br>";
    $response .= "• <strong>Zones rurales / petites communes :</strong> 100 à 120 Litres/hab/jour<br>";
    $response .= "• <strong>Villes moyennes :</strong> 130 à 180 Litres/hab/jour<br>";
    $response .= "• <strong>Grandes agglomérations (Alger, Oran, Constantine) :</strong> 200 à 250 Litres/hab/jour<br><br>";
    $response .= "<strong>Coefficients de pointe réglementaires :</strong><br>";
    $response .= "• Coefficient de pointe journalier (Kp) : <code>1.2 à 1.5</code> (souvent fixé à 1.3)<br>";
    $response .= "• Coefficient de pointe horaire (Kh) : <code>1.5 à 2.0</code> (dépend de la taille de la population)<br><br>";
    $response .= "💡 <em>Exemple de calcul : Pour une ville de 10 000 habitants avec dotation de 150 L/j, le débit moyen est de 17.36 L/s. Avec Kp = 1.3, le débit de pointe journalier à distribuer est de 22.56 L/s.</em>";
}
// Match 4: Wastewater Reuse (REUSE) regulation
elseif (preg_match('/reuse|reutilisation|step|eaux usées|eaux usees/i', $msg_lower)) {
    $response = "🤖 <strong>Normes algériennes de réutilisation des eaux usées épurées (REUSE) :</strong><br><br>";
    $response .= "L'arrêté interministériel fixe les critères physico-chimiques et biologiques stricts (Norme NA 17099) pour la réutilisation des eaux issues des STEPs en agriculture :<br><br>";
    $response .= "<strong>1. Paramètres Biologiques (Irrigation restreinte) :</strong><br>";
    $response .= "• Coliformes thermotolérants (E. coli) : < 1000 ufc / 100 mL<br>";
    $response .= "• Œufs d'helminthes parasites : < 1 œuf / Litre<br><br>";
    $response .= "<strong>2. Paramètres Physico-chimiques clés :</strong><br>";
    $response .= "• DBO5 (Demande Biochimique en Oxygène) : ≤ 30 mg/L<br>";
    $response .= "• DCO (Demande Chimique en Oxygène) : ≤ 120 mg/L<br>";
    $response .= "• MES (Matières En Suspension) : ≤ 30 mg/L<br>";
    $response .= "• Conductivité électrique (Salinité) : < 3 dS/m pour éviter la salinisation des sols.<br><br>";
    $response .= "⚠️ <em>Note : L'irrigation des légumes consommés crus avec des eaux usées épurées reste interdite. La réutilisation cible principalement l'arboriculture, le fourrage et les céréales.</em>";
}
// Match 5: EPANET details
elseif (preg_match('/epanet/i', $msg_lower)) {
    $response = "🤖 <strong>Modélisation sous EPANET :</strong><br><br>";
    $response .= "EPANET est l'outil standard (gratuit et open-source) utilisé par l'ADE et les bureaux d'études en Algérie pour l'analyse des réseaux sous pression.<br>";
    $response .= "<strong>Conseils pour démarrer :</strong><br>";
    $response .= "1. Importez le plan AutoCAD nettoyé ou dessinez les nœuds de demande et réservoirs.<br>";
    $response .= "2. Renseignez l'altitude de chaque nœud (Z) et le débit de demande au nœud.<br>";
    $response .= "3. Saisissez les diamètres intérieurs réels et sélectionnez la formule de perte de charge (Hazen-Williams est la plus commune en AEP, rugosité ≈ 130 pour PVC neuf).<br>";
    $response .= "4. Définissez la courbe de pompe si vous modélisez un refoulement.<br><br>";
    $response .= "📩 <em>Si vous butez sur le calage des pressions ou les analyses de période prolongée, vous pouvez soumettre une demande d'assistance sur notre onglet <strong>Études</strong> !</em>";
}
// Match 6: HEC-RAS details
elseif (preg_match('/hec-ras|hecras/i', $msg_lower)) {
    $response = "🤖 <strong>Simulation d'écoulements sous HEC-RAS :</strong><br><br>";
    $response .= "HEC-RAS est privilégié en Algérie pour délimiter les zones inondables des oueds et dimensionner les gabions de protection hydraulique.<br>";
    $response .= "<strong>Méthodologie standard :</strong><br>";
    $response .= "1. Importez la géométrie (sections transversales) extraites de levés topographiques ou de MNT (Modèle Numérique de Terrain).<br>";
    $response .= "2. Fixez les coefficients de Manning : Lit mineur propre (0.035), avec végétation dense (0.05 - 0.07), lit majeur agricole (0.045).<br>";
    $response .= "3. Entrez les débits de crue de période de retour centennale (Q100) déterminés par l'étude hydrologique.<br>";
    $response .= "4. Renseignez la hauteur normale en aval comme condition limite d'écoulement.<br><br>";
    $response .= "👉 <em>Nous proposons une formation pratique dédiée à HEC-RAS de 28h sur l'onglet <strong>Formations</strong> !</em>";
}
// Default greeting / fail-safe
else {
    $response = "🤖 Bonjour ! Je suis <strong>HydroBot</strong>, le conseiller intelligent d'Hydroconsult.<br><br>";
    $response .= "Je suis spécialisé en ingénierie hydraulique algérienne. Je peux vous aider sur :<br>";
    $response .= "• Les formules de calcul hydraulique (Manning-Strickler, Bresse, Hazen-Williams)<br>";
    $response .= "• La réglementation nationale (normes AEP de l'ADE, décret REUSE NA 17099)<br>";
    $response .= "• Les logiciels d'ingénierie (EPANET, HEC-RAS, SewerGEMS)<br><br>";
    $response .= "💡 <strong>Posez-moi des questions pratiques ou cliquez sur l'un des modèles de calcul à gauche de votre écran !</strong>";
}

echo json_encode([
    'success' => true,
    'reply' => $response
]);
?>
