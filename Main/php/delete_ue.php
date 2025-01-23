<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user']) || $_SESSION['type'] !== 'admin') {
    header("Location: index.php");
    exit();
}

if (!isset($_GET['id'])) {
    echo "ID de l'UE manquant.";
    exit();
}

$id_ue = $_GET['id'];

try {
    $stmt = $conn->prepare("DELETE FROM UE WHERE ID_UE = ?");
    $stmt->execute([$id_ue]);

    header("Location: gestion_ue.php");
    exit();
} catch (PDOException $e) {
    echo "Erreur: " . $e->getMessage();
    exit();
}
?>
