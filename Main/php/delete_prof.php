<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user']) || $_SESSION['type'] !== 'admin') {
    header("Location: index.php");
    exit();
}

if (!isset($_GET['id'])) {
    echo "ID de professeur manquant.";
    exit();
}

$id_enseignant = $_GET['id'];

try {
    $conn->beginTransaction();

    $stmt = $conn->prepare("SELECT ID_Utilisateur FROM Enseignant WHERE ID_Enseignant = :id_enseignant");
    $stmt->bindParam(':id_enseignant', $id_enseignant);
    $stmt->execute();
    $enseignant = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($enseignant) {
        $id_utilisateur = $enseignant['ID_Utilisateur'];

        $stmt = $conn->prepare("DELETE FROM Enseignant WHERE ID_Enseignant = :id_enseignant");
        $stmt->bindParam(':id_enseignant', $id_enseignant);
        $stmt->execute();

        $stmt = $conn->prepare("DELETE FROM Utilisateur WHERE ID_Utilisateur = :id_utilisateur");
        $stmt->bindParam(':id_utilisateur', $id_utilisateur);
        $stmt->execute();

        $conn->commit();

        header("Location: gestion_prof.php");
        exit();
    } else {
        echo "Professeur non trouvé.";
        exit();
    }
} catch (PDOException $e) {
    $conn->rollBack();
    echo "Erreur: " . $e->getMessage();
    exit();
}
?>
