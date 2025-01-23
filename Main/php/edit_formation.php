<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user']) || $_SESSION['type'] !== 'admin') {
    header("Location: index.php");
    exit();
}

if (!isset($_GET['id'])) {
    echo "ID de la formation manquant.";
    exit();
}

$id_formation = $_GET['id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nom_formation = $_POST['nom_formation'];
    $sigle = $_POST['sigle'];

    try {
        $stmt = $conn->prepare("UPDATE Formation SET Nom_Formation = ?, Sigle = ? WHERE ID_Formation = ?");
        $stmt->execute([$nom_formation, $sigle, $id_formation]);

        $stmt = $conn->prepare("UPDATE Promotion SET Sigle = ? WHERE ID_Formation = ?");
        $stmt->execute([$sigle, $id_formation]);

        header("Location: gestion_formation.php");
        exit();
    } catch (PDOException $e) {
        echo "Erreur: " . $e->getMessage();
    }
}

$stmt = $conn->prepare("SELECT Nom_Formation, Sigle FROM Formation WHERE ID_Formation = ?");
$stmt->execute([$id_formation]);
$formation = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$formation) {
    echo "Formation non trouvée.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/style_admin.css">
    <link rel="stylesheet" href="../css/style_navbar_prof&etudiant.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <title>Modifier une Formation</title>
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
            <h1>Modifier une Formation</h1>
            <form method="post" action="edit_formation.php?id=<?= htmlspecialchars($id_formation) ?>">
                <label for="nom_formation">Nom de la Formation :</label>
                <input type="text" id="nom_formation" name="nom_formation" value="<?= htmlspecialchars($formation['Nom_Formation']) ?>" required><br>

                <label for="sigle">Sigle :</label>
                <input type="text" id="sigle" name="sigle" value="<?= htmlspecialchars($formation['Sigle']) ?>" required><br>

                <button type="submit">Enregistrer</button>
            </form>

            <button onclick="location.href='gestion_formation.php'">Retour aux formations</button>
        </div>
    </main>
    <div class="footer"> &copy; FindGrade </div>
    <script src="../javaScript/script.js"></script>
</body>
</html>
