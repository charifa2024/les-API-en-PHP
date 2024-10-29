<?php
header('Content-Type: application/json');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Connexion à la base de données MySQL
$dsn = 'mysql:host=localhost;dbname=gestion_taches';
$username = 'root'; // Remplacez par votre utilisateur
$password = ''; // Remplacez par votre mot de passe

try {
    $pdo = new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Erreur de connexion : ' . $e->getMessage()]);
    exit;
}
///Endpoint 1 : POST /api/tasks (Créer une nouvelle tâche)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);

    if (isset($data['title']) && !empty($data['title'])) {
        $title = $data['title'];
        $description = $data['description'] ?? '';

        $stmt = $pdo->prepare("INSERT INTO tasks (title, description) VALUES (:title, :description)");
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':description', $description);

        if ($stmt->execute()) {
            echo json_encode(['status' => 'Tâche créée avec succès']);
        } else {
            echo json_encode(['error' => 'Erreur lors de la création de la tâche']);
        }
    } else {
        echo json_encode(['error' => 'Le title de la tâche est requis']);
    }
}

///Endpoint 2 : GET /api/tasks/{id} (Récupérer une tâche spécifique)
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];

    $stmt = $pdo->prepare("SELECT * FROM tasks WHERE id = :id");
    $stmt->bindParam(':id', $id);
    $stmt->execute();

    $tache = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($tache) {
        echo json_encode($tache);
    } else {
        echo json_encode(['error' => 'Tâche non trouvée']);
    }
}
///Endpoint 3 : PUT /api/tasks/{id} (Modifier une tâche existante)
if ($_SERVER['REQUEST_METHOD'] === 'PUT' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $data = json_decode(file_get_contents("php://input"), true);

    if (isset($data['title']) && !empty($data['title'])) {
        $title = $data['title'];
        $description = $data['description'] ?? '';

        $stmt = $pdo->prepare("UPDATE tasks SET title = :title, description = :description WHERE id = :id");
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':id', $id);

        if ($stmt->execute()) {
            echo json_encode(['status' => 'Tâche mise à jour avec succès']);
        } else {
            echo json_encode(['error' => 'Erreur lors de la mise à jour de la tâche']);
        }
    } else {
        echo json_encode(['error' => 'Le title de la tâche est requis']);
    }
}
///Endpoint 4 : DELETE /api/tasks/{id} (Supprimer une tâche)
if ($_SERVER['REQUEST_METHOD'] === 'DELETE' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];

    $stmt = $pdo->prepare("DELETE FROM tasks WHERE id = :id");
    $stmt->bindParam(':id', $id);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'Tâche supprimée avec succès']);
    } else {
        echo json_encode(['error' => 'Erreur lors de la suppression de la tâche']);
    }
}


?>