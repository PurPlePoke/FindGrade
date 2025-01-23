<?php
include 'db.php';

if (!isset($_GET['ue_id'])) {
    echo json_encode([]);
    exit();
}

$ue_id = $_GET['ue_id'];

try {
    $stmt = $conn->prepare("SELECT ID_Ressource, Nom_Ressource FROM Ressource WHERE ID_UE = ?");
    $stmt->execute([$ue_id]);
    $resources = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($resources);
} catch (PDOException $e) {
    echo json_encode([]);
}
?>
