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
    $statut = $_POST['statut'];

    $login = strtolower("$prenom.$nom");

    try {
        $conn->beginTransaction();

        $stmt = $conn->prepare("INSERT INTO Utilisateur (Nom, Prénom, Genre, Email, Mot_de_passe, Type, Login) VALUES (?, ?, ?, ?, ?, 'professeur', ?)");
        $stmt->execute([$nom, $prenom, $genre, $email, $password, $login]);

        $user_id = $conn->lastInsertId();

        $stmt = $conn->prepare("INSERT INTO Enseignant (ID_Utilisateur, Statut) VALUES (?, ?)");
        $stmt->execute([$user_id, $statut]);

        $conn->commit();

        header("Location: gestion_prof.php");
        exit();
    } catch (PDOException $e) {
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
            <h1>Ajouter un Professeur</h1>
            <form method="post" action="add_prof.php">
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

                <label for="statut">Statut :</label>
                <select id="statut" name="statut" required>
                    <option value="professeur">Professeur</option>
                    <option value="intervenant">Intervenant</option>
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
