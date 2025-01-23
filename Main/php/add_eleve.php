<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user']) || $_SESSION['type'] !== 'admin') {
    header("Location: index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $genre = $_POST['genre'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $sigle = $_POST['formation'];

    try {
        $conn->beginTransaction();

        $stmt = $conn->prepare("INSERT INTO Utilisateur (Nom, Prénom, Genre, Email, Mot_de_passe, Type, Login) VALUES (?, ?, ?, ?, ?, 'eleve', CONCAT(?, '.', ?))");
        $stmt->execute([$nom, $prenom, $genre, $email, $password, $prenom, $nom]);
        $user_id = $conn->lastInsertId();

        $stmt = $conn->prepare("SELECT ID_Formation FROM Formation WHERE Sigle = ?");
        $stmt->execute([$sigle]);
        $formation = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$formation) {
            throw new Exception("Erreur: Formation non trouvée.");
        }

        $formation_id = $formation['ID_Formation'];

        $stmt = $conn->prepare("SELECT ID_Promotion FROM Promotion WHERE ID_Formation = ?");
        $stmt->execute([$formation_id]);
        $promotion = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$promotion) {
            throw new Exception("Erreur: Promotion non trouvée pour la formation.");
        }

        $promotion_id = $promotion['ID_Promotion'];

        $stmt = $conn->prepare("INSERT INTO Étudiant (ID_Utilisateur, ID_Promotion) VALUES (?, ?)");
        $stmt->execute([$user_id, $promotion_id]);
        $etudiant_id = $conn->lastInsertId();

        $stmt = $conn->prepare("SELECT ID_Ressource FROM Ressource JOIN UE ON Ressource.ID_UE = UE.ID_UE WHERE UE.ID_Formation = ?");
        $stmt->execute([$formation_id]);
        $resources = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($resources as $resource) {
            $stmt = $conn->prepare("INSERT INTO inscription (ID_Étudiant, ID_Ressource) VALUES (?, ?)");
            $stmt->execute([$etudiant_id, $resource['ID_Ressource']]);
        }

        $conn->commit();

        header("Location: gestion_eleve.php");
        exit();
    } catch (Exception $e) {
        $conn->rollBack();
        echo "Erreur: " . $e->getMessage();
    }
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
    <title>Admin Dashboard</title>
</head>
<body>
    <header>
        <div class="container">
            <nav class="navbar">
                <ul class="nav-links">
                    <li class="nav-link"><a href="./homepage_admin.php">Dashboard</a></li>
                    <li class="nav-link"><a href='gestion_ue.php'>Gestion des UE</a></li>
                    <li class="nav-link"><a href='gestion_ressource.php'>Gestion des Ressources</a></li>
                    <li class="nav-link"><a href='gestion_formation.php'>Gestion des Formations</a></li>
                    <li class="nav-link"><a href='gestion_eleve.php'>Gestion des Élèves</a></li>
                    <li class="nav-link"><a href='gestion_prof.php'>Gestion des Professeurs</a></li>
                    <li class="nav-link"><a href="./preference.html">Preferences</a></li>
                    <li class="nav-link"><a href="./logout.php">Déconnexion</a></li>
                </ul>
            </nav>
        </div>
    </header>
    <main>
        <div class="presentation">
            <h1>Ajouter un Élève</h1>
            <form method="post" action="add_eleve.php">
                <label for="nom">Nom :</label>
                <input type="text" id="nom" name="nom" required><br>

                <label for="prenom">Prénom :</label>
                <input type="text" id="prenom" name="prenom" required><br>

                <label for="genre">Genre :</label>
                <select id="genre" name="genre" required>
                    <option value="Monsieur">Monsieur</option>
                    <option value="Madame">Madame</option>
                </select><br>

                <label for="email">Email :</label>
                <input type="email" id="email" name="email" required><br>

                <label for="password">Mot de passe :</label>
                <input type="password" id="password" name="password" required><br>

                <label for="formation">Formation :</label>
                <select id="formation" name="formation" required>
                    <?php
                    $stmt = $conn->query("SELECT Sigle FROM Formation");
                    $formations = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    foreach ($formations as $formation) {
                        echo "<option value=\"" . htmlspecialchars($formation['Sigle']) . "\">" . htmlspecialchars($formation['Sigle']) . "</option>";
                    }
                    ?>
                </select><br>

                <button type="submit">Enregistrer</button>
            </form>

            <button onclick="location.href='gestion_eleve.php'">Retour à la gestion des élèves</button>
        </div>
    </main>
    <div class="footer"> &copy; FindGrade </div>
    <script src="../javaScript/script.js"></script>
</body>
</html>
