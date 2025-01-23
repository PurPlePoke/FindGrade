<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user']) || $_SESSION['type'] !== 'professeur') {
    header("Location: index.php");
    exit();
}

if (!isset($_GET['note_id']) || !isset($_GET['ressource_id'])) {
    echo "ID de la note manquant.";
    exit();
}

$note_id = $_GET['note_id'];
$ressource_id = $_GET['ressource_id'];

try {
    $stmt = $conn->prepare("DELETE FROM Note WHERE ID_Note = ?");
    $stmt->execute([$note_id]);

    header("Location: gestion_note.php?ressource_id=" . htmlspecialchars($ressource_id));
    exit();
} catch (PDOException $e) {
    echo "Erreur: " . $e->getMessage();
    exit();
}
?>

