<?php
// admin/includes/header.php
require_once 'auth.php';
$active_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hydrolicia Admin Panel</title>
    <link rel="icon" href="../assets/img/Logo.png" type="image/png">
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Custom Admin Stylesheet -->
    <style>
        :root {
            --admin-primary: #0f172a;       /* Slate 900 */
            --admin-secondary: #1e293b;     /* Slate 800 */
            --admin-accent: #0284c7;        /* Sky 600 */
            --admin-accent-hover: #0369a1;  /* Sky 700 */
            --admin-bg: #f8fafc;            /* Slate 50 */
            --admin-card-bg: #ffffff;
            --admin-text: #0f172a;
            --admin-text-muted: #64748b;
            --transition-smooth: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--admin-bg);
            color: var(--admin-text);
            margin: 0;
            overflow-x: hidden;
        }

        h1, h2, h3, h4, h5, h6 {
            font-family: 'Outfit', sans-serif;
            font-weight: 600;
        }

        /* Sidebar styling */
        .admin-sidebar {
            width: 260px;
            height: 100vh;
            background-color: var(--admin-primary);
            color: #ffffff;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 100;
            transition: var(--transition-smooth);
            border-right: 1px solid rgba(255, 255, 255, 0.05);
            display: flex;
            flex-direction: column;
        }

        .sidebar-brand {
            padding: 24px;
            font-family: 'Outfit', sans-serif;
            font-weight: 800;
            font-size: 1.4rem;
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
            color: #ffffff;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }

        .sidebar-brand i {
            color: #38bdf8;
            filter: drop-shadow(0 0 5px rgba(56, 189, 248, 0.4));
        }

        .sidebar-menu {
            list-style: none;
            padding: 20px 10px;
            margin: 0;
            flex: 1;
            overflow-y: auto;
        }

        .menu-item {
            margin-bottom: 5px;
        }

        .menu-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 16px;
            color: #94a3b8;
            text-decoration: none;
            border-radius: 12px;
            font-weight: 500;
            font-size: 0.95rem;
            transition: var(--transition-smooth);
        }

        .menu-link:hover {
            color: #ffffff;
            background-color: rgba(255, 255, 255, 0.03);
        }

        .menu-link.active {
            color: #ffffff;
            background-color: var(--admin-accent);
            box-shadow: 0 4px 12px rgba(2, 132, 199, 0.3);
        }

        .sidebar-footer {
            padding: 16px;
            border-top: 1px solid rgba(255, 255, 255, 0.05);
            background-color: rgba(0, 0, 0, 0.1);
        }

        /* Main Content wrapper */
        .admin-main {
            margin-left: 260px;
            min-height: 100vh;
            transition: var(--transition-smooth);
            display: flex;
            flex-direction: column;
        }

        /* Top Navbar */
        .admin-navbar {
            background-color: var(--admin-card-bg);
            border-bottom: 1px solid #e2e8f0;
            padding: 16px 30px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 90;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.02);
        }

        .navbar-toggle {
            display: none;
            background: none;
            border: none;
            font-size: 1.5rem;
            color: var(--admin-primary);
            cursor: pointer;
        }

        .admin-profile {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .profile-avatar {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            background-color: rgba(2, 132, 199, 0.1);
            color: var(--admin-accent);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.95rem;
            border: 1px solid rgba(2, 132, 199, 0.2);
        }

        .admin-content-body {
            padding: 30px;
            flex: 1;
        }

        /* Premium Dashboard UI Elements */
        .admin-card {
            background-color: var(--admin-card-bg);
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.02);
            padding: 24px;
            margin-bottom: 24px;
            transition: var(--transition-smooth);
        }

        .admin-card:hover {
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.04);
        }

        .stat-card {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .stat-icon {
            width: 54px;
            height: 54px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }

        .pill-status {
            font-size: 0.8rem;
            padding: 4px 12px;
            border-radius: 50px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .table-responsive {
            border-radius: 12px;
            overflow: hidden;
            border: 1px solid #e2e8f0;
        }

        .table {
            margin-bottom: 0;
        }

        .table th {
            background-color: #f8fafc;
            color: var(--admin-text-muted);
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 600;
            padding: 16px;
        }

        .table td {
            padding: 16px;
            color: #334155;
            vertical-align: middle;
        }

        .action-btn {
            width: 34px;
            height: 34px;
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 1px solid #e2e8f0;
            background-color: #ffffff;
            color: var(--admin-text-muted);
            transition: var(--transition-smooth);
            text-decoration: none;
        }

        .action-btn:hover {
            background-color: var(--admin-bg);
            color: var(--admin-text);
        }

        .action-btn-danger:hover {
            background-color: #fef2f2;
            color: #ef4444;
            border-color: #fca5a5;
        }

        .action-btn-success:hover {
            background-color: #f0fdf4;
            color: #22c55e;
            border-color: #bbf7d0;
        }

        /* Responsive sidebar adaptations */
        @media (max-width: 991.98px) {
            .admin-sidebar {
                left: -260px;
            }
            .admin-sidebar.show {
                left: 0;
            }
            .admin-main {
                margin-left: 0;
            }
            .navbar-toggle {
                display: block;
            }
        }

        /* ===== MOBILE TABLE RESPONSIVENESS ===== */
        @media (max-width: 767.98px) {

            /* Reduce content padding on small screens */
            .admin-content-body {
                padding: 16px 12px;
            }

            .admin-navbar {
                padding: 12px 16px;
            }

            /* Transform table into card-style list on mobile */
            .table-responsive {
                border: none;
                border-radius: 0;
                overflow: visible;
            }

            .table-responsive table {
                display: block;
            }

            .table-responsive thead {
                display: none; /* Hide the header row */
            }

            .table-responsive tbody {
                display: flex;
                flex-direction: column;
                gap: 12px;
            }

            .table-responsive tbody tr {
                display: flex;
                flex-direction: column;
                background: #fff;
                border: 1px solid #e2e8f0;
                border-radius: 14px;
                padding: 14px 16px;
                box-shadow: 0 2px 8px rgba(0,0,0,0.04);
                gap: 6px;
            }

            /* Each cell becomes a labeled row */
            .table-responsive tbody td {
                display: flex;
                align-items: flex-start;
                gap: 10px;
                padding: 6px 0;
                border: none;
                border-bottom: 1px dashed #f1f5f9;
                font-size: 0.88rem;
            }

            .table-responsive tbody td:last-child {
                border-bottom: none;
                padding-top: 10px;
                justify-content: flex-start !important;
                text-align: left !important;
            }

            /* Show data-label as a prefix */
            .table-responsive tbody td::before {
                content: attr(data-label);
                font-weight: 700;
                font-size: 0.75rem;
                color: var(--admin-text-muted);
                text-transform: uppercase;
                letter-spacing: 0.4px;
                min-width: 95px;
                flex-shrink: 0;
                margin-top: 2px;
            }

            /* Action buttons: bigger touch target on mobile */
            .action-btn {
                width: 38px;
                height: 38px;
                border-radius: 10px;
            }

            .btn-group.gap-1 {
                flex-wrap: wrap;
            }

            /* Badge & status pills */
            .pill-status {
                font-size: 0.78rem;
            }

            /* Admin cards */
            .admin-card {
                padding: 16px;
                border-radius: 12px;
            }

            /* Filters: stack vertically */
            .admin-card .row.g-3 > div {
                padding: 0;
            }
        }

        /* Extra small phones */
        @media (max-width: 479.98px) {
            .admin-content-body {
                padding: 12px 8px;
            }
            .table-responsive tbody td::before {
                min-width: 80px;
                font-size: 0.7rem;
            }
        }
    </style>
</head>
<body>

    <!-- Sidebar -->
    <div class="admin-sidebar" id="sidebar">
        <a href="index.php" class="sidebar-brand">
            <i class="bi bi-droplet-half"></i>
            <span>HYDRO<span style="color: #38bdf8;">LICIA</span></span>
        </a>
        <ul class="sidebar-menu">
            <li class="menu-item">
                <a href="index.php" class="menu-link <?php echo ($active_page == 'index.php') ? 'active' : ''; ?>">
                    <i class="bi bi-speedometer2"></i>
                    <span>Tableau de Bord</span>
                </a>
            </li>
            <li class="menu-item">
                <a href="registrations.php" class="menu-link <?php echo ($active_page == 'registrations.php') ? 'active' : ''; ?>">
                    <i class="bi bi-mortarboard"></i>
                    <span>Inscriptions</span>
                </a>
            </li>
            <li class="menu-item">
                <a href="consultations.php" class="menu-link <?php echo ($active_page == 'consultations.php') ? 'active' : ''; ?>">
                    <i class="bi bi-calendar2-check"></i>
                    <span>Consultations</span>
                </a>
            </li>
            <li class="menu-item">
                <a href="studies.php" class="menu-link <?php echo ($active_page == 'studies.php') ? 'active' : ''; ?>">
                    <i class="bi bi-folder2-open"></i>
                    <span>Dossiers d'Études</span>
                </a>
            </li>
            <li class="menu-item">
                <a href="trainings.php" class="menu-link <?php echo ($active_page == 'trainings.php') ? 'active' : ''; ?>">
                    <i class="bi bi-book"></i>
                    <span>Catalogue Formations</span>
                </a>
            </li>
            <li class="menu-item">
                <a href="experts.php" class="menu-link <?php echo ($active_page == 'experts.php') ? 'active' : ''; ?>">
                    <i class="bi bi-people"></i>
                    <span>Gestion Experts</span>
                </a>
            </li>
            <li class="menu-item">
                <a href="forum.php" class="menu-link <?php echo ($active_page == 'forum.php') ? 'active' : ''; ?>">
                    <i class="bi bi-chat-square-text"></i>
                    <span>Modération Forum</span>
                </a>
            </li>
        </ul>
        <div class="sidebar-footer">
            <a href="../index.php" class="menu-link text-white-50">
                <i class="bi bi-box-arrow-up-right"></i>
                <span>Retour au site public</span>
            </a>
        </div>
    </div>

    <!-- Main Content Area -->
    <div class="admin-main">
        <!-- Top Navbar -->
        <nav class="admin-navbar">
            <button class="navbar-toggle" id="sidebarToggle">
                <i class="bi bi-list"></i>
            </button>
            <h5 class="mb-0 d-none d-md-block text-secondary">Espace de Gestion</h5>
            <div class="admin-profile">
                <div class="profile-avatar">
                    <?php echo strtoupper(substr($_SESSION['admin_username'], 0, 2)); ?>
                </div>
                <div class="d-none d-sm-block text-start me-3">
                    <div class="small fw-semibold"><?php echo htmlspecialchars($_SESSION['admin_username']); ?></div>
                    <div class="small text-muted" style="font-size: 0.75rem;">Administrateur</div>
                </div>
                <a href="logout.php" class="btn btn-sm btn-outline-danger rounded-pill px-3">
                    <i class="bi bi-box-arrow-right me-1"></i>Déconnexion
                </a>
            </div>
        </nav>

        <div class="admin-content-body">
