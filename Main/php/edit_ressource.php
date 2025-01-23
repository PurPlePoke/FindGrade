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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nom_ressource = $_POST['nom_ressource'];
    $id_ue = $_POST['id_ue'];
    $semestre = $_POST['semestre'];
    $id_enseignant = $_POST['id_enseignant'];

    try {
        $stmt = $conn->prepare("UPDATE Ressource SET Nom_Ressource = ?, Semestre = ?, ID_UE = ?, ID_Enseignant = ? WHERE ID_Ressource = ?");
        $stmt->execute([$nom_ressource, $semestre, $id_ue, $id_enseignant, $id_ressource]);

        header("Location: gestion_ressource.php");
        exit();
    } catch (PDOException $e) {
        echo "Erreur: " . $e->getMessage();
    }
}

$stmt = $conn->prepare("SELECT Nom_Ressource, Semestre, ID_UE, ID_Enseignant FROM Ressource WHERE ID_Ressource = ?");
$stmt->execute([$id_ressource]);
$ressource = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$ressource) {
    echo "Ressource non trouvée.";
    exit();
}

$stmt = $conn->prepare("SELECT ID_Formation FROM UE WHERE ID_UE = ?");
$stmt->execute([$ressource['ID_UE']]);
$formation_id = $stmt->fetch(PDO::FETCH_ASSOC)['ID_Formation'];

$stmt = $conn->query("SELECT ID_Formation, Nom_Formation FROM Formation");
$formations = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $conn->prepare("SELECT ID_UE, Nom_UE FROM UE WHERE ID_Formation = ?");
$stmt->execute([$formation_id]);
$ues = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
            <h1>Modifier une Ressource</h1>
            <form method="post" action="edit_ressource.php?id=<?= htmlspecialchars($id_ressource) ?>">
                <label for="nom_ressource">Nom de la Ressource :</label>
                <input type="text" id="nom_ressource" name="nom_ressource" value="<?= htmlspecialchars($ressource['Nom_Ressource']) ?>" required><br>

                <label for="semestre">Semestre :</label>
                <input type="number" id="semestre" name="semestre" value="<?= htmlspecialchars($ressource['Semestre']) ?>" required><br>

                <label for="id_formation">Formation :</label>
                <select id="id_formation" name="id_formation" required>
                    <option value="">Sélectionner une formation</option>
                    <?php foreach ($formations as $formation): ?>
                        <option value="<?= htmlspecialchars($formation['ID_Formation']) ?>" <?= ($formation['ID_Formation'] == $formation_id) ? 'selected' : '' ?>><?= htmlspecialchars($formation['Nom_Formation']) ?></option>
                    <?php endforeach; ?>
                </select><br>

                <label for="id_ue">UE :</label>
                <select id="id_ue" name="id_ue" required>
                    <option value="">Sélectionner une UE</option>
                    <?php foreach ($ues as $ue): ?>
                        <option value="<?= htmlspecialchars($ue['ID_UE']) ?>" <?= ($ue['ID_UE'] == $ressource['ID_UE']) ? 'selected' : '' ?>><?= htmlspecialchars($ue['Nom_UE']) ?></option>
                    <?php endforeach; ?>
                </select><br>

                <label for="id_enseignant">Enseignant :</label>
                <select id="id_enseignant" name="id_enseignant" required>
                    <?php
                    $stmt = $conn->query("SELECT Enseignant.ID_Enseignant, CONCAT(Utilisateur.Nom, ' ', Utilisateur.Prénom) AS NomComplet FROM Enseignant JOIN Utilisateur ON Enseignant.ID_Utilisateur = Utilisateur.ID_Utilisateur");
                    $enseignants = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    foreach ($enseignants as $enseignant) {
                        echo "<option value=\"" . htmlspecialchars($enseignant['ID_Enseignant']) . "\" " . ($enseignant['ID_Enseignant'] == $ressource['ID_Enseignant'] ? 'selected' : '') . ">" . htmlspecialchars($enseignant['NomComplet']) . "</option>";
                    }
                    ?>
                </select><br>

                <button type="submit">Enregistrer</button>
            </form>

            <button onclick="location.href='gestion_ressource.php'">Retour à la gestion des ressources</button>

            <script>
            document.getElementById('id_formation').addEventListener('change', function() {
                var id_formation = this.value;
                fetch('recup_ue.php?id_formation=' + id_formation)
                    .then(response => response.json())
                    .then(data => {
                        var ueSelect = document.getElementById('id_ue');
                        ueSelect.innerHTML = '<option value="">Sélectionner une UE</option>';
                        data.forEach(function(ue) {
                            var option = document.createElement('option');
                            option.value = ue.ID_UE;
                            option.text = ue.Nom_UE;
                            ueSelect.appendChild(option);
                        });
                    });
            });
            </script>
        </div>
    </main>
    <div class="footer"> &copy; FindGrade </div>
    <script src="../javaScript/script.js"></script>
</body>
</html>
