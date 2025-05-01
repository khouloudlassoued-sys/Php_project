<?php
session_start();
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $database = new Database();
    $db = $database->getConnection();

    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    try {
        $query = "INSERT INTO users (username, email, password) VALUES (:username, :email, :password)";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $password);
        
        if ($stmt->execute()) {
            header("Location: login.php");
            exit();
        }
    } catch(PDOException $e) {
        $error = "Erreur lors de l'inscription: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription - Gestion Hôtel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body>
    <!-- Header Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="../index.php">
                <i class="fas fa-hotel me-2"></i>Gestion Hôtel
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="../index.php">
                            <i class="fas fa-home me-1"></i>Accueil
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="login.php">
                            <i class="fas fa-sign-in-alt me-1"></i>Connexion
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="register.php">
                            <i class="fas fa-user-plus me-1"></i>Inscription
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="auth-card card fade-in-up">
            <div class="card-header">
                <div class="auth-logo text-center">
                    <i class="fas fa-user-plus"></i>
                </div>
                <h3 class="text-center">Créez un compte</h3>
            </div>
            <div class="card-body">
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        <?php echo $error; ?>
                    </div>
                <?php endif; ?>
                
                <form method="POST" action="">
                    <div class="mb-4">
                        <label for="username" class="form-label">
                            <i class="fas fa-user me-2"></i>Nom d'utilisateur
                        </label>
                        <input type="text" class="form-control" id="username" name="username" required>
                    </div>
                    <div class="mb-4">
                        <label for="email" class="form-label">
                            <i class="fas fa-envelope me-2"></i>Email
                        </label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="mb-4">
                        <label for="password" class="form-label">
                            <i class="fas fa-lock me-2"></i>Mot de passe
                        </label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <div class="mb-4 form-check">
                        <input type="checkbox" class="form-check-input" id="terms" required>
                        <label class="form-check-label" for="terms">J'accepte les conditions d'utilisation</label>
                    </div>
                    <div class="d-grid mb-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-user-plus me-2"></i>S'inscrire
                        </button>
                    </div>
                </form>
                <div class="text-center">
                    <p>Déjà inscrit ? <a href="login.php">Connectez-vous</a></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-4 mb-md-0">
                    <h5><i class="fas fa-hotel me-2"></i>Gestion Hôtel</h5>
                    <p class="mb-0">Votre confort est notre priorité</p>
                </div>
                <div class="col-md-4 mb-4 mb-md-0">
                    <h5>Liens rapides</h5>
                    <ul class="list-unstyled">
                        <li><a href="../index.php" class="footer-link">Accueil</a></li>
                        <li><a href="login.php" class="footer-link">Connexion</a></li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <h5>Contact</h5>
                    <p class="mb-0">
                        <i class="fas fa-phone me-2"></i>+33 1 23 45 67 89
                    </p>
                    <p class="mb-0">
                        <i class="fas fa-envelope me-2"></i>contact@hotel.com
                    </p>
                </div>
            </div>
            <hr class="my-4 bg-light">
            <div class="row">
                <div class="col-md-6">
                    <p class="mb-0">&copy; 2024 Gestion Hôtel. Tous droits réservés.</p>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 