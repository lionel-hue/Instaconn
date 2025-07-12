<?php
session_start();
header('Content-Type: application/json');

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Non autorisé']);
    exit;
}

$user_id = $_SESSION['user_id'];

try {
    // Connexion à la base de données
    $pdo = new PDO("mysql:host=localhost;dbname=Instaconn", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Récupérer les conversations de l'utilisateur avec le dernier message
    $sql = "
        SELECT DISTINCT 
            u.id,
            u.nom,
            u.prenom,
            u.image,
            (SELECT m.texte 
             FROM Message m 
             WHERE (m.id_uti_1 = ? AND m.id_uti_2 = u.id) 
                OR (m.id_uti_1 = u.id AND m.id_uti_2 = ?) 
             ORDER BY m.date DESC 
             LIMIT 1) as dernierMessage,
            (SELECT m.date 
             FROM Message m 
             WHERE (m.id_uti_1 = ? AND m.id_uti_2 = u.id) 
                OR (m.id_uti_1 = u.id AND m.id_uti_2 = ?) 
             ORDER BY m.date DESC 
             LIMIT 1) as derniereDate
        FROM Utilisateur u
        WHERE u.id IN (
            SELECT DISTINCT 
                CASE 
                    WHEN m.id_uti_1 = ? THEN m.id_uti_2 
                    ELSE m.id_uti_1 
                END as contact_id
            FROM Message m 
            WHERE m.id_uti_1 = ? OR m.id_uti_2 = ?
        )
        ORDER BY derniereDate DESC
    ";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$user_id, $user_id, $user_id, $user_id, $user_id, $user_id, $user_id]);
    
    $conversations = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($conversations);
    
} catch (PDOException $e) {
    echo json_encode(['error' => 'Erreur de base de données: ' . $e->getMessage()]);
}
?>