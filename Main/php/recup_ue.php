<?php
include 'db.php';

$id_formation = $_GET['id_formation'];

$stmt = $conn->prepare("SELECT ID_UE, Nom_UE FROM UE WHERE ID_Formation = ?");
$stmt->execute([$id_formation]);
$ues = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($ues);
?>
