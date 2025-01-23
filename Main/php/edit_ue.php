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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nom_ue = $_POST['nom_ue'];
    $id_formation = $_POST['id_formation'];

    try {
        $stmt = $conn->prepare("UPDATE UE SET Nom_UE = ?, ID_Formation = ? WHERE ID_UE = ?");
        $stmt->execute([$nom_ue, $id_formation, $id_ue]);

        header("Location: gestion_ue.php");
        exit();
    } catch (PDOException $e) {
        echo "Erreur: " . $e->getMessage();
    }
}

$stmt = $conn->prepare("SELECT Nom_UE, ID_Formation FROM UE WHERE ID_UE = ?");
$stmt->execute([$id_ue]);
$ue = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$ue) {
    echo "UE non trouvée.";
    exit();
}

$stmt = $conn->query("SELECT ID_Formation, Nom_Formation FROM Formation");
$formations = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/style_admin.css">
    <link rel="stylesheet" href="../css/style_navbar_prof&etudiant.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <title>Admin Dashboard</title>
</head>
<body>
    <header>
        <div class="container">
            <nav class="navbar">
                <ul class="nav-links">
                    <li class="nav-link">
                        <a href="./homepage_redirect.php">Dashboard</a> 
                    </li>
                    <li class="nav-link">
                        <a href='gestion_ue.php'>Gestion des UE</a>
                    </li>
                    <li class="nav-link">
                        <a href='gestion_ressource.php'>Gestion des Ressources</a>
                    </li>
                    <li class="nav-link">
                        <a href='gestion_formation.php'>Gestion des Formations</a>
                    </li>
                    <li class="nav-link">
                        <a href='gestion_eleve.php'>Gestion des Élèves</a>
                    </li>
                    <li class="nav-link">
                        <a href='gestion_prof.php'>Gestion des Professeurs</a>
                    </li>
                    <li class="nav-link">
                        <a href="./preference.html">Preferences</a>
                    </li>
                    <li class="nav-link">
                        <a href="./logout.php">Déconnexion</a>
                    </li>
                </ul>
            </nav>
        </div>
    </header>
    <main>
        <div class="presentation">
            <h1>Modifier une UE</h1>
            <form method="post" action="edit_ue.php?id=<?= htmlspecialchars($id_ue) ?>">
            <label for="nom_ue">Nom de l'UE :</label>
            <input type="text" id="nom_ue" name="nom_ue" value="<?= htmlspecialchars($ue['Nom_UE']) ?>" required><br>
            <label for="id_formation">Formation :</label>
            <select id="id_formation" name="id_formation" required>
                <option value="">Sélectionner une formation</option>
                <?php foreach ($formations as $formation): ?>
                    <option value="<?= htmlspecialchars($formation['ID_Formation']) ?>" <?= $ue['ID_Formation'] == $formation['ID_Formation'] ? 'selected' : '' ?>><?= htmlspecialchars($formation['Nom_Formation']) ?></option>
        <?php endforeach; ?>
    </select><br>

    <button type="submit">Enregistrer</button>
</form>

<button onclick="location.href='gestion_ue.php'">Retour à la gestion des UE</button>
        </div>
    </main>
    <div class="footer"> &copy; FindGrade </div>
    <script src="../javaScript/script.js"></script>
</body>
</html>
