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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $email = $_POST['email'];
    $statut = $_POST['statut'];

    try {
        $conn->beginTransaction();

        $stmt = $conn->prepare("UPDATE Utilisateur 
                                JOIN Enseignant ON Utilisateur.ID_Utilisateur = Enseignant.ID_Utilisateur 
                                SET Utilisateur.Nom = :nom, Utilisateur.Prénom = :prenom, Utilisateur.Email = :email, Enseignant.Statut = :statut 
                                WHERE Enseignant.ID_Enseignant = :id_enseignant");
        $stmt->bindParam(':nom', $nom);
        $stmt->bindParam(':prenom', $prenom);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':statut', $statut);
        $stmt->bindParam(':id_enseignant', $id_enseignant);

        if ($stmt->execute()) {
            $conn->commit();
            header("Location: gestion_prof.php");
            exit();
        } else {
            $conn->rollBack();
            echo "Erreur: " . $stmt->errorInfo()[2];
        }
    } catch (PDOException $e) {
        $conn->rollBack();
        echo "Erreur: " . $e->getMessage();
    }
}

try {
    $stmt = $conn->prepare("SELECT Utilisateur.Nom, Utilisateur.Prénom, Utilisateur.Email, Enseignant.Statut
                            FROM Enseignant 
                            JOIN Utilisateur ON Enseignant.ID_Utilisateur = Utilisateur.ID_Utilisateur 
                            WHERE Enseignant.ID_Enseignant = :id_enseignant");
    $stmt->bindParam(':id_enseignant', $id_enseignant);
    $stmt->execute();
    $professor = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$professor) {
        echo "Professeur non trouvé.";
        exit();
    }
} catch (PDOException $e) {
    echo "Erreur: " . $e->getMessage();
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
        <h1>Modifier un Professeur</h1>
<form method="post" action="edit_prof.php?id=<?= htmlspecialchars($id_enseignant) ?>">
    <label for="nom">Nom :</label>
    <input type="text" id="nom" name="nom" value="<?= htmlspecialchars($professor['Nom']) ?>" required><br>

    <label for="prenom">Prénom :</label>
    <input type="text" id="prenom" name="prenom" value="<?= htmlspecialchars($professor['Prénom']) ?>" required><br>

    <label for="email">Email :</label>
    <input type="email" id="email" name="email" value="<?= htmlspecialchars($professor['Email']) ?>" required><br>

    <label for="statut">Statut :</label>
    <select id="statut" name="statut" required>
        <option value="Professeur" <?= $professor['Statut'] == 'Professeur' ? 'selected' : '' ?>>Professeur</option>
        <option value="Intervenant" <?= $professor['Statut'] == 'Intervenant' ? 'selected' : '' ?>>Intervenant</option>
    </select><br>

    <button type="submit">Enregistrer</button>
</form>

<button onclick="location.href='gestion_prof.php'">Retour à la gestion des professeurs</button>
        </div>
    </main>
    <div class="footer"> &copy; FindGrade </div>
    <script src="../javaScript/script.js"></script>
</body>
</html>
