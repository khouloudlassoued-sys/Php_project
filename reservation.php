<?php
session_start();
require_once 'config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: auth/login.php");
    exit();
}

$database = new Database();
$db = $database->getConnection();

if (!isset($_GET['room_id'])) {
    header("Location: index.php");
    exit();
}

$room_id = $_GET['room_id'];

// Récupérer les détails de la chambre avec une requête SQL avancée
$query = "SELECT r.*, 
          (SELECT COUNT(*) FROM reservations res 
           WHERE res.room_id = r.id 
           AND res.status = 'confirmée') as reservation_count
          FROM rooms r 
          WHERE r.id = :room_id";
$stmt = $db->prepare($query);
$stmt->bindParam(':room_id', $room_id);
$stmt->execute();
$room = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$room) {
    header("Location: index.php");
    exit();
}

// Tableau associatif pour les images des chambres
$room_images = [
    'Simple' => 'https://images.unsplash.com/photo-1566665797739-1674de7a421a?auto=format&fit=crop&w=800&q=80',
    'Double' => 'https://images.unsplash.com/photo-1551107696-a4b0c5a0d9a2?auto=format&fit=crop&w=800&q=80',
    'Suite' => 'https://images.unsplash.com/photo-1590490360182-c33d57733427?auto=format&fit=crop&w=800&q=80'
];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $check_in = $_POST['check_in'];
    $check_out = $_POST['check_out'];
    
    // Vérifier la disponibilité de la chambre pour la période
    $query = "SELECT COUNT(*) FROM reservations 
              WHERE room_id = :room_id 
              AND status = 'confirmée'
              AND ((check_in <= :check_out AND check_out >= :check_in))";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':room_id', $room_id);
    $stmt->bindParam(':check_in', $check_in);
    $stmt->bindParam(':check_out', $check_out);
    $stmt->execute();
    
    if ($stmt->fetchColumn() == 0) {
        // Calculer le nombre de nuits
        $date1 = new DateTime($check_in);
        $date2 = new DateTime($check_out);
        $interval = $date1->diff($date2);
        $nights = $interval->days;
        
        // Calculer le prix total
        $total_price = $nights * $room['price'];
        
        try {
            $query = "INSERT INTO reservations (user_id, room_id, check_in, check_out, total_price) 
                     VALUES (:user_id, :room_id, :check_in, :check_out, :total_price)";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':user_id', $_SESSION['user_id']);
            $stmt->bindParam(':room_id', $room_id);
            $stmt->bindParam(':check_in', $check_in);
            $stmt->bindParam(':check_out', $check_out);
            $stmt->bindParam(':total_price', $total_price);
            
            if ($stmt->execute()) {
                header("Location: reservations.php");
                exit();
            }
        } catch(PDOException $e) {
            $error = "Erreur lors de la réservation: " . $e->getMessage();
        }
    } else {
        $error = "La chambre n'est pas disponible pour cette période";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réservation - Gestion Hôtel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
    <!-- Header Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="fas fa-hotel me-2"></i>Gestion Hôtel
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">
                            <i class="fas fa-home me-1"></i>Accueil
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="reservations.php">
                            <i class="fas fa-calendar-alt me-1"></i>Mes Réservations
                        </a>
                    </li>
                    <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin'): ?>
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
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section" style="padding: 50px 0;">
        <div class="container">
            <h1 class="hero-title">Réserver une Chambre</h1>
            <p class="hero-subtitle">Chambre <?php echo htmlspecialchars($room['room_number']); ?> - <?php echo htmlspecialchars($room['type']); ?></p>
        </div>
    </section>

    <div class="container mt-4">
        <?php if (isset($error)): ?>
            <div class="alert alert-danger fade-in">
                <i class="fas fa-exclamation-circle me-2"></i>
                <?php echo $error; ?>
            </div>
        <?php endif; ?>
        
        <div class="row">
            <div class="col-md-6 mb-4 slide-in-left">
                <div class="card room-card">
                    <div class="room-price">
                        <?php echo number_format($room['price'], 2); ?> DNT/nuit
                    </div>
                    <div class="room-image-container">
                        <img src="<?php echo $room_images[$room['type']]; ?>" 
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
                            Détails de la chambre
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
                            <span class="room-feature">
                                <i class="fas fa-shower me-1"></i>
                                Salle de bain
                            </span>
                        </div>
                        
                        <div class="mt-3 text-muted">
                            <i class="fas fa-calendar-check me-2"></i>
                            <?php echo $room['reservation_count']; ?> réservations confirmées
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6 mb-4 slide-in-right">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">
                            <i class="fas fa-calendar-plus me-2"></i>
                            Formulaire de réservation
                        </h5>
                        
                        <form method="POST" action="" class="mt-4">
                            <div class="mb-4">
                                <label for="check_in" class="form-label">
                                    <i class="fas fa-calendar me-2"></i>
                                    Check-in
                                </label>
                                <input type="date" class="form-control" id="check_in" name="check_in" required>
                            </div>
                            <div class="mb-4">
                                <label for="check_out" class="form-label">
                                    <i class="fas fa-calendar-check me-2"></i>
                                Check-out
                                </label>
                                <input type="date" class="form-control" id="check_out" name="check_out" required>
                            </div>
                            
                            <div class="alert alert-info mb-4">
                                <div id="summary">
                                    <p class="mb-1">
                                        <i class="fas fa-moon me-2"></i>
                                        <strong>Nombre de nuits:</strong> <span id="nightCount">0</span>
                                    </p>
                                    <p class="mb-0">
                                        <i class="fas fa-money-bill-wave me-2"></i>
                                        <strong>Prix total estimé:</strong> <span id="totalPrice">0.00</span> DNT
                                    </p>
                                </div>
                            </div>
                            
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-check-circle me-2"></i>
                                    Confirmer la réservation
                                </button>
                            </div>
                        </form>
                    </div>
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
                        <li><a href="index.php" class="footer-link">Accueil</a></li>
                        <li><a href="reservations.php" class="footer-link">Mes réservations</a></li>
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
    <script>
        // Définir la date minimale à aujourd'hui
        const today = new Date().toISOString().split('T')[0];
        document.getElementById('check_in').min = today;
        document.getElementById('check_out').min = today;
        
        // Mettre à jour la date minimale de départ quand la date d'arrivée change
        document.getElementById('check_in').addEventListener('change', function() {
            const checkInDate = new Date(this.value);
            // La date de départ doit être au moins un jour après la date d'arrivée
            checkInDate.setDate(checkInDate.getDate() + 1);
            const minCheckOut = checkInDate.toISOString().split('T')[0];
            
            // Mettre à jour la date minimale de départ
            document.getElementById('check_out').min = minCheckOut;
            
            // Si la date de départ est avant la nouvelle date minimale, on la met à jour
            const checkOut = document.getElementById('check_out');
            if (checkOut.value && new Date(checkOut.value) <= new Date(this.value)) {
                checkOut.value = minCheckOut;
            }
            
            calculateTotal();
        });

        document.getElementById('check_out').addEventListener('change', function() {
            const checkInDate = new Date(document.getElementById('check_in').value);
            const checkOutDate = new Date(this.value);
            
            // Vérifier que la date de départ est bien après la date d'arrivée
            if (checkOutDate <= checkInDate) {
                alert("La date de départ doit être postérieure à la date d'arrivée.");
                // Fixer la date de départ au lendemain de la date d'arrivée
                checkInDate.setDate(checkInDate.getDate() + 1);
                this.value = checkInDate.toISOString().split('T')[0];
            }
            
            calculateTotal();
        });

        // Calculer le nombre de nuits et le prix total
        function calculateTotal() {
            const checkIn = document.getElementById('check_in').value;
            const checkOut = document.getElementById('check_out').value;
            
            if (checkIn && checkOut) {
                const date1 = new Date(checkIn);
                const date2 = new Date(checkOut);
                
                // Vérifier que la date de départ est bien après la date d'arrivée
                if (date2 <= date1) {
                    console.error("La date de départ doit être postérieure à la date d'arrivée");
                    return;
                }
                
                const diffTime = date2.getTime() - date1.getTime();
                const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
                
                if (diffDays > 0) {
                    const pricePerNight = <?php echo $room['price']; ?>;
                    const totalPrice = diffDays * pricePerNight;
                    
                    document.getElementById('nightCount').textContent = diffDays;
                    document.getElementById('totalPrice').textContent = totalPrice.toFixed(2);
                }
            }
        }
        
        // Initialiser la validation
        const checkIn = document.getElementById('check_in');
        if (checkIn.value) {
            const checkInDate = new Date(checkIn.value);
            checkInDate.setDate(checkInDate.getDate() + 1);
            document.getElementById('check_out').min = checkInDate.toISOString().split('T')[0];
        }
        
        calculateTotal();
    </script>
</body>
</html> 