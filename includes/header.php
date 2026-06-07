<?php
// includes/header.php
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hydrolicia | Plateforme Digitale d'Ingénierie Hydraulique</title>
    <link rel="icon" href="assets/img/Logo.png" type="image/png">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="assets/css/style.css" rel="stylesheet">
</head>

<body>

    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-glass sticky-top py-3">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="index.php">
                <i class="bi bi-droplet-half text-info me-2 fs-3"></i>
                <span>HYDRO<span class="text-info">LICIA</span></span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item">
                        <a class="nav-link <?php echo ($current_page == 'index.php') ? 'active' : ''; ?>"
                            href="index.php">Accueil</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo ($current_page == 'trainings.php') ? 'active' : ''; ?>"
                            href="trainings.php">Formations</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo ($current_page == 'consultations.php') ? 'active' : ''; ?>"
                            href="consultations.php">Experts</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo ($current_page == 'studies.php') ? 'active' : ''; ?>"
                            href="studies.php">Études</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo ($current_page == 'community.php') ? 'active' : ''; ?>"
                            href="community.php">Communauté</a>
                    </li>
                    <li class="nav-item ms-lg-3 mt-3 mt-lg-0">
                        <a class="btn btn-outline-info rounded-pill d-flex align-items-center px-4 py-2 text-dark font-weight-bold <?php echo ($current_page == 'chatbot.php') ? 'active bg-info-subtle' : ''; ?>"
                            href="chatbot.php">
                            <i class="bi bi-robot text-info me-2"></i> HydroBot AI
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>