<?php
session_start();
require_once 'config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: auth/login.php");
    exit();
}

$database = new Database();
$db = $database->getConnection();

// Traitement de la suppression de réservation
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $reservation_id = $_GET['id'];
    
    // Vérifier que la réservation appartient à l'utilisateur
    $query = "SELECT * FROM reservations WHERE id = :id AND user_id = :user_id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $reservation_id);
    $stmt->bindParam(':user_id', $_SESSION['user_id']);
    $stmt->execute();
    
    if ($stmt->rowCount() > 0) {
        // Supprimer la réservation
        $query = "DELETE FROM reservations WHERE id = :id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':id', $reservation_id);
        
        if ($stmt->execute()) {
            $success_message = "La réservation a été supprimée avec succès.";
        } else {
            $error_message = "Erreur lors de la suppression de la réservation.";
        }
    } else {
        $error_message = "Vous n'êtes pas autorisé à supprimer cette réservation.";
    }
}

// Récupérer les réservations de l'utilisateur avec une requête SQL avancée
$query = "SELECT r.*, 
          rm.room_number, rm.type as room_type, rm.price as room_price,
          DATEDIFF(r.check_out, r.check_in) as nights
          FROM reservations r
          JOIN rooms rm ON r.room_id = rm.id
          WHERE r.user_id = :user_id
          ORDER BY r.check_in DESC";
$stmt = $db->prepare($query);
$stmt->bindParam(':user_id', $_SESSION['user_id']);
$stmt->execute();
$reservations = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Tableau associatif pour les images des chambres
$room_images = [
    'Simple' => 'https://images.unsplash.com/photo-1566665797739-1674de7a421a?auto=format&fit=crop&w=800&q=80',
    'Double' => 'https://images.unsplash.com/photo-1551107696-a4b0c5a0d9a2?auto=format&fit=crop&w=800&q=80',
    'Suite' => 'https://images.unsplash.com/photo-1590490360182-c33d57733427?auto=format&fit=crop&w=800&q=80'
];

// Images spécifiques par numéro de chambre
$specific_rooms = [
    '102' => 'https://images.unsplash.com/photo-1578683010236-d716f9a3f461?auto=format&fit=crop&w=800&q=80'
];
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes Réservations - Gestion Hôtel</title>
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
                        <a class="nav-link active" href="reservations.php">
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
            <h1 class="hero-title">Mes Réservations</h1>
            <p class="hero-subtitle">Gérez vos séjours et consultez l'historique de vos réservations</p>
        </div>
    </section>

    <!-- Main Content -->
    <div class="container mt-4">
        <?php if (isset($success_message)): ?>
            <div class="alert alert-success fade-in">
                <i class="fas fa-check-circle me-2"></i>
                <?php echo $success_message; ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger fade-in">
                <i class="fas fa-exclamation-circle me-2"></i>
                <?php echo $error_message; ?>
            </div>
        <?php endif; ?>
        
        <?php if (empty($reservations)): ?>
            <div class="alert alert-info text-center fade-in-up">
                <i class="fas fa-info-circle me-2"></i>
                Vous n'avez aucune réservation pour le moment.
                <a href="index.php" class="alert-link">Consultez nos chambres disponibles</a>
            </div>
        <?php else: ?>
            <div class="row">
                <?php foreach ($reservations as $index => $reservation): ?>
                    <div class="col-md-6 mb-4 <?php echo $index % 2 == 0 ? 'slide-in-left' : 'slide-in-right'; ?>">
                        <div class="card reservation-card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="card-title mb-0">
                                        <i class="fas fa-calendar-check me-2"></i>
                                        Réservation #<?php echo $reservation['id']; ?>
                                    </h5>
                                    <span class="badge bg-<?php 
                                        echo $reservation['status'] == 'confirmée' ? 'success' : 
                                            ($reservation['status'] == 'en attente' ? 'warning' : 'danger'); 
                                    ?>">
                                        <?php echo ucfirst($reservation['status']); ?>
                                    </span>
                                </div>
                                
                                <div class="room-image-container mb-3" style="height: 150px;">
                                    <?php 
                                        // Déterminer quelle image utiliser
                                        $image_url = isset($specific_rooms[$reservation['room_number']]) 
                                            ? $specific_rooms[$reservation['room_number']] 
                                            : $room_images[$reservation['room_type']];
                                    ?>
                                    <img src="<?php echo $image_url; ?>" 
                                         alt="<?php echo htmlspecialchars($reservation['room_type']); ?>" 
                                         class="room-image">
                                    <div class="room-overlay">
                                        <div class="room-type">
                                            <i class="fas fa-door-open me-2"></i>
                                            Chambre <?php echo htmlspecialchars($reservation['room_number']); ?>
                                            (<?php echo htmlspecialchars($reservation['room_type']); ?>)
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="reservation-dates">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <i class="fas fa-sign-in-alt me-2"></i>
                                            <strong>Départ:</strong>
                                            <?php 
                                                $check_in = new DateTime($reservation['check_in']);
                                                echo $check_in->format('d/m/Y');
                                            ?>
                                        </div>
                                        <div>
                                            <i class="fas fa-sign-out-alt me-2"></i>
                                            <strong>Arrivée:</strong>
                                            <?php 
                                                $check_out = new DateTime($reservation['check_out']);
                                                echo $check_out->format('d/m/Y');
                                            ?>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="d-flex justify-content-between align-items-center mt-3">
                                    <div>
                                        <p class="mb-1">
                                            <i class="fas fa-moon me-2"></i>
                                            <strong>Nuits:</strong> <?php echo $reservation['nights']; ?>
                                        </p>
                                        <p class="mb-0">
                                            <i class="fas fa-euro-sign me-2"></i>
                                            <strong>Total:</strong> <?php echo number_format($reservation['total_price'], 2); ?> €
                                        </p>
                                    </div>
                                    <div class="text-end">
                                        <small class="text-muted">
                                            <i class="fas fa-clock me-2"></i>
                                            Réservé le <?php 
                                                $created_at = new DateTime($reservation['created_at']);
                                                echo $created_at->format('d/m/Y à H:i');
                                            ?>
                                        </small>
                                    </div>
                                </div>
                                
                                <div class="mt-3 pt-3 border-top d-flex justify-content-between">
                                    <a href="edit_reservation.php?id=<?php echo $reservation['id']; ?>" class="btn btn-outline-primary">
                                        <i class="fas fa-edit me-2"></i>Modifier
                                    </a>
                                    <a href="#" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal<?php echo $reservation['id']; ?>">
                                        <i class="fas fa-trash-alt me-2"></i>Annuler
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Modal de confirmation de suppression -->
                    <div class="modal fade" id="deleteModal<?php echo $reservation['id']; ?>" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="deleteModalLabel">Confirmer l'annulation</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <p>Êtes-vous sûr de vouloir annuler votre réservation pour la chambre <?php echo htmlspecialchars($reservation['room_number']); ?> 
                                    du <?php echo $check_in->format('d/m/Y'); ?> au <?php echo $check_out->format('d/m/Y'); ?> ?</p>
                                    <p class="text-danger"><strong>Attention :</strong> Cette action est irréversible.</p>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                    <a href="reservations.php?action=delete&id=<?php echo $reservation['id']; ?>" class="btn btn-danger">
                                        <i class="fas fa-trash-alt me-2"></i>Confirmer l'annulation
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
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
                        <li><a href="#" class="footer-link">Chambres</a></li>
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