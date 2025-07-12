<?php
session_start();
include(__DIR__."/../database.php");

// à remplacer plus tard par $_SESSION['user_id']
$current_user_id = 5;

$type = $_GET['type'] ?? '';

if ($type === 'home') {
    // Récupère les amis avec qui je peux écrire
    $stmt = $pdo->prepare("
        SELECT u.id, u.nom, u.prenom
        FROM amitie a
        JOIN utilisateur u ON (u.id = CASE 
            WHEN a.id_1 = :id THEN a.id_2 
            ELSE a.id_1 
        END)
        WHERE (a.id_1 = :id OR a.id_2 = :id) AND a.statut = 'ami'
    ");
    $stmt->execute(['id' => $current_user_id]);

} elseif ($type === 'requests') {
    // Récupère les utilisateurs qui m'ont envoyé une demande
    $stmt = $pdo->prepare("
        SELECT u.id, u.nom, u.prenom
        FROM amitie a
        JOIN utilisateur u ON u.id = a.id_1
        WHERE a.id_2 = :id AND a.statut = 'en attente'
    ");
    $stmt->execute(['id' => $current_user_id]);

} else {
    echo "<li class='list-group-item'>Invalid request.</li>";
    exit;
}

$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($rows) {
    foreach ($rows as $row) {
        $photoProfil = "https://via.placeholder.com/40"; // tu pourras remplacer ça par $row['photo'] si tu ajoutes un champ photo

        if ($type === 'home') {
            echo "
            <li class='list-group-item d-flex align-items-center'>
                <img src='$photoProfil' class='rounded-circle me-3' width='40' height='40' alt='photo de profil'>
                <div>
                    <strong>{$row['prenom']} {$row['nom']}</strong>
                </div>
                <div class='ms-auto'>
                    <button class='btn btn-sm btn-outline-primary'>Message</button>
                </div>
            </li>";
        } elseif ($type === 'requests') {
            echo "
            <li class='list-group-item d-flex align-items-center'>
                <img src='$photoProfil' class='rounded-circle me-3' width='40' height='40' alt='photo de profil'>
                <div>
                    <strong>{$row['prenom']} {$row['nom']}</strong>
                </div>
                <div class='ms-auto'>
                    <button class='btn btn-sm btn-success me-2'>Accepter</button>
                    <button class='btn btn-sm btn-danger'>Refuser</button>
                </div>
            </li>";
        }
    }
} else {
    echo "<li class='list-group-item text-center text-muted'>Aucun résultat trouvé.</li>";
}

