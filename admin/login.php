<?php
// admin/login.php
session_start();

// If already logged in, redirect to dashboard
if (isset($_SESSION['admin_logged']) && $_SESSION['admin_logged'] === true) {
    header('Location: index.php');
    exit;
}

require_once '../config/db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    if (!empty($username) && !empty($password)) {
        try {
            $stmt = $pdo->prepare("SELECT * FROM `admins` WHERE `username` = :username LIMIT 1");
            $stmt->execute(['username' => $username]);
            $admin = $stmt->fetch();

            if ($admin && password_verify($password, $admin['password_hash'])) {
                $_SESSION['admin_logged'] = true;
                $_SESSION['admin_username'] = $admin['username'];
                $_SESSION['admin_id'] = $admin['id'];
                
                header('Location: index.php');
                exit;
            } else {
                $error = 'Nom d\'utilisateur ou mot de passe incorrect.';
            }
        } catch (Exception $e) {
            $error = 'Erreur serveur lors de la connexion : ' . $e->getMessage();
        }
    } else {
        $error = 'Veuillez remplir tous les champs.';
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administration | Connexion</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- Custom styling with glassmorphism and premium aesthetics -->
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@400;500;600;700;800&display=swap');
        
        :root {
            --bg-gradient: linear-gradient(135deg, #0f172a 0%, #0284c7 100%);
            --glass-bg: rgba(15, 23, 42, 0.45);
            --glass-border: rgba(255, 255, 255, 0.1);
            --text-color: #f8fafc;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg-gradient);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-color);
            margin: 0;
            overflow: hidden;
            position: relative;
        }

        /* Abstract glowing blobs for premium feel */
        .glow-blob {
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            z-index: 0;
            opacity: 0.5;
        }
        .blob-1 {
            width: 300px;
            height: 300px;
            background: #0284c7;
            top: -10%;
            left: 10%;
            animation: float-blob-1 12s infinite alternate;
        }
        .blob-2 {
            width: 400px;
            height: 400px;
            background: #0d9488;
            bottom: -10%;
            right: 10%;
            animation: float-blob-2 15s infinite alternate;
        }

        @keyframes float-blob-1 {
            0% { transform: translateY(0) scale(1); }
            100% { transform: translateY(50px) scale(1.2); }
        }
        @keyframes float-blob-2 {
            0% { transform: translateY(0) scale(1.1); }
            100% { transform: translateY(-60px) scale(0.9); }
        }

        .login-container {
            z-index: 10;
            width: 100%;
            max-width: 440px;
            padding: 20px;
        }

        .login-card {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: 24px;
            padding: 40px;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.3);
            text-align: center;
        }

        .brand-logo {
            font-family: 'Outfit', sans-serif;
            font-weight: 800;
            font-size: 1.8rem;
            color: #ffffff;
            margin-bottom: 30px;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
        }

        .brand-logo i {
            color: #38bdf8;
            filter: drop-shadow(0 0 8px rgba(56, 189, 248, 0.6));
        }

        .form-label {
            font-size: 0.85rem;
            font-weight: 600;
            color: #94a3b8;
            text-align: left;
            display: block;
            margin-bottom: 8px;
        }

        .input-group-text {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--glass-border);
            color: #64748b;
            border-radius: 12px 0 0 12px;
        }

        .form-control {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--glass-border);
            color: #ffffff;
            border-radius: 0 12px 12px 0;
            padding: 12px;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            background: rgba(255, 255, 255, 0.1);
            border-color: #38bdf8;
            box-shadow: 0 0 0 3px rgba(56, 189, 248, 0.25);
            color: #ffffff;
        }

        .btn-login {
            background: linear-gradient(90deg, #0284c7 0%, #0d9488 100%);
            border: none;
            color: white;
            padding: 14px;
            font-weight: 600;
            border-radius: 12px;
            margin-top: 15px;
            box-shadow: 0 4px 15px rgba(2, 132, 199, 0.4);
            transition: all 0.3s ease;
        }

        .btn-login:hover {
            background: linear-gradient(90deg, #0369a1 0%, #0f766e 100%);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(2, 132, 199, 0.5);
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .back-to-site {
            margin-top: 25px;
            display: inline-block;
            color: #94a3b8;
            text-decoration: none;
            font-size: 0.85rem;
            transition: color 0.3s ease;
        }

        .back-to-site:hover {
            color: #38bdf8;
        }

        .alert-custom {
            background: rgba(239, 68, 68, 0.15);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: #fca5a5;
            border-radius: 12px;
            padding: 12px;
            font-size: 0.85rem;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
    </style>
</head>
<body>

    <!-- Background glowing graphics -->
    <div class="glow-blob blob-1"></div>
    <div class="glow-blob blob-2"></div>

    <div class="login-container">
        <div class="login-card">
            <a href="../index.php" class="brand-logo">
                <i class="bi bi-droplet-half"></i>
                <span>HYDRO<span style="color: #38bdf8;">LICIA</span></span>
            </a>
            
            <h5 class="text-white-50 mb-4 fw-normal">Espace d'Administration</h5>

            <?php if (!empty($error)): ?>
                <div class="alert-custom text-start">
                    <i class="bi bi-exclamation-triangle-fill fs-5"></i>
                    <div><?php echo htmlspecialchars($error); ?></div>
                </div>
            <?php endif; ?>

            <form action="login.php" method="POST">
                <div class="mb-3 text-start">
                    <label for="username" class="form-label">Nom d'utilisateur</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-person-fill"></i></span>
                        <input type="text" name="username" id="username" class="form-control" placeholder="Entrez votre identifiant" required autocomplete="username">
                    </div>
                </div>

                <div class="mb-4 text-start">
                    <label for="password" class="form-label">Mot de passe</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                        <input type="password" name="password" id="password" class="form-control" placeholder="Entrez votre mot de passe" required autocomplete="current-password">
                    </div>
                </div>

                <button type="submit" class="btn btn-login w-100">
                    Se connecter <i class="bi bi-box-arrow-in-right ms-1"></i>
                </button>
            </form>

            <a href="../index.php" class="back-to-site">
                <i class="bi bi-arrow-left me-1"></i> Retour au site public
            </a>
        </div>
    </div>

</body>
</html>
