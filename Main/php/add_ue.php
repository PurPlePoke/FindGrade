<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user']) || $_SESSION['type'] !== 'admin') {
    header("Location: index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nom_ue = $_POST['nom_ue'];
    $id_formation = $_POST['id_formation'];

    try {
        $stmt = $conn->prepare("INSERT INTO UE (Nom_UE, ID_Formation) VALUES (?, ?)");
        $stmt->execute([$nom_ue, $id_formation]);

        header("Location: gestion_ue.php");
        exit();
    } catch (PDOException $e) {
        echo "Erreur: " . $e->getMessage();
    }
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
                        <a href="./homepage_admin.php">Dashboard</a>
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
        <h1>Ajouter une UE</h1>
<form method="post" action="add_ue.php">
    <label for="nom_ue">Nom de l'UE :</label>
    <input type="text" id="nom_ue" name="nom_ue" required><br>

    <label for="id_formation">Formation :</label>
    <select id="id_formation" name="id_formation" required>
        <option value="">Sélectionner une formation</option>
        <?php foreach ($formations as $formation): ?>
            <option value="<?= htmlspecialchars($formation['ID_Formation']) ?>"><?= htmlspecialchars($formation['Nom_Formation']) ?></option>
        <?php endforeach; ?>
    </select><br>

    <button type="submit">Enregistrer</button>
</form>

<button onclick="location.href='gestion_ue.php'">Retour à la gestion des UEs</button>
        </div>
    </main>
    <div class="footer"> &copy; FindGrade </div>
    <script src="../javaScript/script.js"></script>
</body>
</html>
