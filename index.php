<?php
session_start();
require_once 'config/db.php';

$database = new Database();
$db = $database->getConnection();

// Requête SQL avancée pour récupérer les chambres disponibles
$query = "SELECT r.*, 
          (SELECT COUNT(*) FROM reservations res 
           WHERE res.room_id = r.id 
           AND res.status = 'confirmée') as reservation_count
          FROM rooms r 
          WHERE r.status = 'disponible'
          ORDER BY r.price ASC";
$stmt = $db->prepare($query);
$stmt->execute();
$rooms = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Tableau associatif pour les images des chambres
$room_images = [
    'Simple' => 'https://images.unsplash.com/photo-1566665797739-1674de7a421a?auto=format&fit=crop&w=800&q=80',
    'Double' => 'https://images.unsplash.com/photo-1551107696-a4b0c5a0d9a2?auto=format&fit=crop&w=800&q=80',
    'Suite' => 'https://images.unsplash.com/photo-1590490360182-c33d57733427?auto=format&fit=crop&w=800&q=80'
];

// Images spécifiques par numéro de chambre
$specific_rooms = [
    '101' => 'https://images.unsplash.com/photo-1578683010236-d716f9a3f461?auto=format&fit=crop&w=800&q=80',
    '102' => 'https://images.unsplash.com/photo-1578683010236-d716f9a3f461?auto=format&fit=crop&w=800&q=80',
    '201' => 'https://images.unsplash.com/photo-1582719508461-905c673771fd?auto=format&fit=crop&w=800&q=80'
];

// Featured rooms pour le carousel
$featured_rooms = array_slice($rooms, 0, min(3, count($rooms)));
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion Hôtel - Accueil</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
    <!-- Header Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="#">
                <i class="fas fa-hotel me-2"></i>Gestion Hôtel
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="index.php">
                            <i class="fas fa-home me-1"></i>Accueil
                        </a>
                    </li>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="reservations.php">
                                <i class="fas fa-calendar-alt me-1"></i>Mes Réservations
                            </a>
                        </li>
                        <?php if ($_SESSION['role'] == 'admin'): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="admin/rooms.php">
                                    <i class="fas fa-cog me-1"></i>Administration
                                </a>
                            </li>
                        <?php endif; ?>
                        <li class="nav-item">
                            <a class="nav-link" href="auth/logout.php">
                                <i class="fas fa-sign-out-alt me-1"></i>Déconnexion
                            </a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="auth/login.php">
                                <i class="fas fa-sign-in-alt me-1"></i>Connexion
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="auth/register.php">
                                <i class="fas fa-user-plus me-1"></i>Inscription
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <h1 class="hero-title">Découvrez le confort et l'élégance</h1>
            <p class="hero-subtitle">Réservez votre séjour idéal parmi notre sélection de chambres luxueuses</p>
            <a href="#rooms" class="btn btn-primary btn-lg">
                <i class="fas fa-search me-2"></i>Explorer nos chambres
            </a>
        </div>
    </section>

    <!-- Featured Rooms Carousel -->
    <div class="container">
        <div id="roomCarousel" class="carousel slide room-carousel" data-bs-ride="carousel">
            <div class="carousel-inner">
                <?php foreach ($featured_rooms as $index => $room): ?>
                    <div class="carousel-item <?php echo $index === 0 ? 'active' : ''; ?>">
                        <?php 
                            // Déterminer quelle image utiliser
                            $image_url = isset($specific_rooms[$room['room_number']]) 
                                ? $specific_rooms[$room['room_number']] 
                                : $room_images[$room['type']];
                        ?>
                        <img src="<?php echo $image_url; ?>" class="d-block w-100" alt="<?php echo htmlspecialchars($room['type']); ?>">
                        <div class="carousel-caption d-none d-md-block">
                            <h5><?php echo htmlspecialchars($room['type']); ?> - Chambre <?php echo htmlspecialchars($room['room_number']); ?></h5>
                            <p><?php echo htmlspecialchars($room['description']); ?></p>
                            <?php if (isset($_SESSION['user_id'])): ?>
                                <a href="reservation.php?room_id=<?php echo $room['id']; ?>" class="btn btn-primary btn-sm">
                                    <i class="fas fa-calendar-plus me-1"></i>Réserver
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <button class="carousel-control-prev" type="button" data-bs-target="#roomCarousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Précédent</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#roomCarousel" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Suivant</span>
            </button>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container" id="rooms">
        <div class="row mb-4">
            <div class="col-12 fade-in-up">
                <h2 class="text-center mb-4">Nos Chambres Disponibles</h2>
                <p class="text-center text-muted">Découvrez notre sélection de chambres confortables pour votre séjour</p>
            </div>
        </div>
        
        <div class="row">
            <?php foreach ($rooms as $index => $room): ?>
                <div class="col-md-4 mb-4 <?php echo $index % 2 == 0 ? 'slide-in-left' : 'slide-in-right'; ?>">
                    <div class="card room-card">
                        <div class="room-price">
                            <?php echo number_format($room['price'], 2); ?> DNT/nuit
                        </div>
                        <div class="room-image-container">
                            <?php 
                                // Déterminer quelle image utiliser
                                $image_url = isset($specific_rooms[$room['room_number']]) 
                                    ? $specific_rooms[$room['room_number']] 
                                    : $room_images[$room['type']];
                            ?>
                            <img src="<?php echo $image_url; ?>" 
                                 alt="Chambre <?php echo htmlspecialchars($room['room_number']); ?>" 
                                 class="room-image">
                            <div class="room-overlay">
                                <div class="room-type">
                                    <i class="fas fa-bed"></i>
                                    <?php echo htmlspecialchars($room['type']); ?>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <h5 class="card-title">
                                <i class="fas fa-door-open me-2"></i>
                                Chambre <?php echo htmlspecialchars($room['room_number']); ?>
                            </h5>
                            <p class="card-text">
                                <i class="fas fa-info-circle me-2"></i>
                                <?php echo htmlspecialchars($room['description']); ?>
                            </p>
                            <div class="room-features">
                                <span class="room-feature">
                                    <i class="fas fa-users me-1"></i>
                                    <?php echo $room['capacity']; ?> pers.
                                </span>
                                <span class="room-feature">
                                    <i class="fas fa-wifi me-1"></i>
                                    WiFi
                                </span>
                                <span class="room-feature">
                                    <i class="fas fa-tv me-1"></i>
                                    TV
                                </span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mt-3">
                                <div class="text-muted">
                                    <i class="fas fa-calendar-check me-2"></i>
                                    <?php echo $room['reservation_count']; ?> réservations
                                </div>
                                <?php if (isset($_SESSION['user_id'])): ?>
                                    <a href="reservation.php?room_id=<?php echo $room['id']; ?>" class="btn btn-primary">
                                        <i class="fas fa-calendar-plus me-2"></i>Réserver
                                    </a>
                                <?php else: ?>
                                    <a href="auth/login.php" class="btn btn-primary">
                                        <i class="fas fa-sign-in-alt me-2"></i>Se connecter
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Why Choose Us Section -->
    <section class="container mt-5 fade-in-up">
        <div class="row mb-4">
            <div class="col-12">
                <h2 class="text-center mb-4">Pourquoi nous choisir</h2>
                <p class="text-center text-muted">Nous offrons une expérience exceptionnelle pour votre séjour</p>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-4 mb-4">
                <div class="card h-100 text-center">
                    <div class="card-body">
                        <i class="fas fa-star text-warning fa-3x mb-3"></i>
                        <h5 class="card-title">Service de qualité</h5>
                        <p class="card-text">Notre personnel dévoué est là pour répondre à tous vos besoins.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card h-100 text-center">
                    <div class="card-body">
                        <i class="fas fa-map-marker-alt text-danger fa-3x mb-3"></i>
                        <h5 class="card-title">Emplacement idéal</h5>
                        <p class="card-text">Situé au cœur de la ville, à proximité des attractions principales.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card h-100 text-center">
                    <div class="card-body">
                        <i class="fas fa-coffee text-success fa-3x mb-3"></i>
                        <h5 class="card-title">Équipements modernes</h5>
                        <p class="card-text">Profitez d'équipements modernes et d'un confort exceptionnel.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

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
                        <li><a href="index.php" class="footer-link">Accueil</a></li>
                        <li><a href="#rooms" class="footer-link">Chambres</a></li>
                        <li><a href="auth/login.php" class="footer-link">Connexion</a></li>
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
                    <p class="mb-0">
                        <i class="fas fa-map-marker-alt me-2"></i>123 Avenue des Champs, Paris
                    </p>
                </div>
            </div>
            <hr class="my-4 bg-light">
            <div class="row">
                <div class="col-md-6">
                    <p class="mb-0">&copy; 2024 Gestion Hôtel. Tous droits réservés.</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <a href="#" class="footer-link me-3"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="footer-link me-3"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="footer-link me-3"><i class="fab fa-instagram"></i></a>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 