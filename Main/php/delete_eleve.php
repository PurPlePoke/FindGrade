<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user']) || $_SESSION['type'] !== 'admin') {
    header("Location: index.php");
    exit();
}

if (isset($_GET['id'])) {
    $etudiant_id = $_GET['id'];

    try {
        $conn->beginTransaction();

        $stmt = $conn->prepare("DELETE FROM Note WHERE ID_Étudiant = ?");
        $stmt->execute([$etudiant_id]);

        $stmt = $conn->prepare("DELETE FROM inscription WHERE ID_Étudiant = ?");
        $stmt->execute([$etudiant_id]);

        $stmt = $conn->prepare("DELETE FROM Étudiant WHERE ID_Étudiant = ?");
        $stmt->execute([$etudiant_id]);

        $stmt = $conn->prepare("SELECT ID_Utilisateur FROM Étudiant WHERE ID_Étudiant = ?");
        $stmt->execute([$etudiant_id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($user) {
            $user_id = $user['ID_Utilisateur'];

            $stmt = $conn->prepare("DELETE FROM Utilisateur WHERE ID_Utilisateur = ?");
            $stmt->execute([$user_id]);
        }

        $conn->commit();

        header("Location: gestion_eleve.php");
        exit();
    } catch (Exception $e) {
        $conn->rollBack();
        echo "Erreur: " . $e->getMessage();
    }
} else {
    echo "ID de l'étudiant manquant.";
}
?>
