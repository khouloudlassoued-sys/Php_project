<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

$database = new Database();
$db = $database->getConnection();

// Gestion des actions CRUD
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'create':
                $query = "INSERT INTO rooms (room_number, type, price, capacity, description) 
                         VALUES (:room_number, :type, :price, :capacity, :description)";
                $stmt = $db->prepare($query);
                $stmt->bindParam(':room_number', $_POST['room_number']);
                $stmt->bindParam(':type', $_POST['type']);
                $stmt->bindParam(':price', $_POST['price']);
                $stmt->bindParam(':capacity', $_POST['capacity']);
                $stmt->bindParam(':description', $_POST['description']);
                $stmt->execute();
                break;

            case 'update':
                $query = "UPDATE rooms SET 
                         room_number = :room_number,
                         type = :type,
                         price = :price,
                         capacity = :capacity,
                         description = :description,
                         status = :status
                         WHERE id = :id";
                $stmt = $db->prepare($query);
                $stmt->bindParam(':id', $_POST['id']);
                $stmt->bindParam(':room_number', $_POST['room_number']);
                $stmt->bindParam(':type', $_POST['type']);
                $stmt->bindParam(':price', $_POST['price']);
                $stmt->bindParam(':capacity', $_POST['capacity']);
                $stmt->bindParam(':description', $_POST['description']);
                $stmt->bindParam(':status', $_POST['status']);
                $stmt->execute();
                break;

            case 'delete':
                $query = "DELETE FROM rooms WHERE id = :id";
                $stmt = $db->prepare($query);
                $stmt->bindParam(':id', $_POST['id']);
                $stmt->execute();
                break;
        }
        header("Location: rooms.php");
        exit();
    }
}

// Récupérer toutes les chambres
$query = "SELECT * FROM rooms ORDER BY room_number";
$stmt = $db->prepare($query);
$stmt->execute();
$rooms = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Chambres - Administration</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="#">Gestion Hôtel - Administration</a>
            <div class="collapse navbar-collapse">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="../index.php">Accueil</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="rooms.php">Gestion des Chambres</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../auth/logout.php">Déconnexion</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Gestion des Chambres</h1>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addRoomModal">
                Ajouter une chambre
            </button>
        </div>

        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Numéro</th>
                        <th>Type</th>
                        <th>Prix</th>
                        <th>Capacité</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($rooms as $room): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($room['room_number']); ?></td>
                            <td><?php echo htmlspecialchars($room['type']); ?></td>
                            <td><?php echo number_format($room['price'], 2); ?> €</td>
                            <td><?php echo $room['capacity']; ?> personnes</td>
                            <td>
                                <span class="badge bg-<?php 
                                    echo $room['status'] == 'disponible' ? 'success' : 
                                        ($room['status'] == 'occupée' ? 'danger' : 'warning'); 
                                ?>">
                                    <?php echo ucfirst($room['status']); ?>
                                </span>
                            </td>
                            <td>
                                <button type="button" class="btn btn-sm btn-primary" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#editRoomModal"
                                        data-room='<?php echo json_encode($room); ?>'>
                                    Modifier
                                </button>
                                <form method="POST" action="" class="d-inline">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?php echo $room['id']; ?>">
                                    <button type="submit" class="btn btn-sm btn-danger" 
                                            onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette chambre ?')">
                                        Supprimer
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Ajout -->
    <div class="modal fade" id="addRoomModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Ajouter une chambre</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="create">
                        <div class="mb-3">
                            <label class="form-label">Numéro de chambre</label>
                            <input type="text" class="form-control" name="room_number" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Type</label>
                            <input type="text" class="form-control" name="type" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Prix par nuit</label>
                            <input type="number" step="0.01" class="form-control" name="price" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Capacité</label>
                            <input type="number" class="form-control" name="capacity" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" name="description" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-primary">Ajouter</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Modification -->
    <div class="modal fade" id="editRoomModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Modifier la chambre</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="update">
                        <input type="hidden" name="id" id="edit_id">
                        <div class="mb-3">
                            <label class="form-label">Numéro de chambre</label>
                            <input type="text" class="form-control" name="room_number" id="edit_room_number" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Type</label>
                            <input type="text" class="form-control" name="type" id="edit_type" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Prix par nuit</label>
                            <input type="number" step="0.01" class="form-control" name="price" id="edit_price" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Capacité</label>
                            <input type="number" class="form-control" name="capacity" id="edit_capacity" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" name="description" id="edit_description" rows="3"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Statut</label>
                            <select class="form-control" name="status" id="edit_status">
                                <option value="disponible">Disponible</option>
                                <option value="occupée">Occupée</option>
                                <option value="maintenance">En maintenance</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-primary">Enregistrer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Remplir le formulaire de modification avec les données de la chambre
        document.getElementById('editRoomModal').addEventListener('show.bs.modal', function(event) {
            var button = event.relatedTarget;
            var room = JSON.parse(button.getAttribute('data-room'));
            
            document.getElementById('edit_id').value = room.id;
            document.getElementById('edit_room_number').value = room.room_number;
            document.getElementById('edit_type').value = room.type;
            document.getElementById('edit_price').value = room.price;
            document.getElementById('edit_capacity').value = room.capacity;
            document.getElementById('edit_description').value = room.description;
            document.getElementById('edit_status').value = room.status;
        });
    </script>
</body>
</html> 