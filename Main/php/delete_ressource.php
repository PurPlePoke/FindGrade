<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user']) || $_SESSION['type'] !== 'admin') {
    header("Location: index.php");
    exit();
}

if (!isset($_GET['id'])) {
    echo "ID de la ressource manquant.";
    exit();
}

$id_ressource = $_GET['id'];

try {
    $stmt = $conn->prepare("DELETE FROM Ressource WHERE ID_Ressource = ?");
    $stmt->execute([$id_ressource]);

    header("Location: gestion_ressource.php");
    exit();
} catch (PDOException $e) {
    echo "Erreur: " . $e->getMessage();
    exit();
}
?>
