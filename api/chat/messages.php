<?php
session_start();
header('Content-Type: application/json');

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Non autorisé']);
    exit;
}

$user_id = $_SESSION['user_id'];
$conversation_id = $_GET['conversation_id'] ?? null;

if (!$conversation_id) {
    echo json_encode(['error' => 'ID de conversation manquant']);
    exit;
}


try {
    // Connexion à la base de données
    $pdo = new PDO("mysql:host=localhost;dbname=Instaconn", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Récupérer les messages entre l'utilisateur actuel et l'utilisateur sélectionné
    $sql = "
        SELECT m.*, u.nom, u.prenom, u.image
        FROM Message m
        JOIN Utilisateur u ON m.id_uti_1 = u.id
        WHERE (m.id_uti_1 = ? AND m.id_uti_2 = ?) 
           OR (m.id_uti_1 = ? AND m.id_uti_2 = ?)
        ORDER BY m.date ASC
    ";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$user_id, $conversation_id, $conversation_id, $user_id]);
    
    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($messages);
    
} catch (PDOException $e) {
    echo json_encode(['error' => 'Erreur de base de données: ' . $e->getMessage()]);
}
?>