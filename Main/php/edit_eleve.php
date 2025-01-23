<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user']) || $_SESSION['type'] !== 'admin') {
    header("Location: index.php");
    exit();
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    try {
        $stmt = $conn->prepare("SELECT Utilisateur.Nom, Utilisateur.Prénom, Utilisateur.Email, Formation.Sigle
                                FROM Étudiant
                                JOIN Utilisateur ON Étudiant.ID_Utilisateur = Utilisateur.ID_Utilisateur
                                JOIN Promotion ON Étudiant.ID_Promotion = Promotion.ID_Promotion
                                JOIN Formation ON Promotion.ID_Formation = Formation.ID_Formation
                                WHERE Étudiant.ID_Étudiant = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $student = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$student) {
            echo "Aucun élève trouvé avec cet ID.";
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $nom = $_POST['nom'];
            $prenom = $_POST['prenom'];
            $email = $_POST['email'];
            $sigle = $_POST['sigle'];

            $stmt = $conn->prepare("SELECT Promotion.ID_Promotion 
                                    FROM Promotion
                                    JOIN Formation ON Promotion.ID_Formation = Formation.ID_Formation
                                    WHERE Formation.Sigle = :sigle");
            $stmt->bindParam(':sigle', $sigle);
            $stmt->execute();
            $promotion = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($promotion) {
                $stmt = $conn->prepare("UPDATE Utilisateur SET Nom = :nom, Prénom = :prenom, Email = :email WHERE ID_Utilisateur = (SELECT ID_Utilisateur FROM Étudiant WHERE ID_Étudiant = :id)");
                $stmt->bindParam(':nom', $nom);
                $stmt->bindParam(':prenom', $prenom);
                $stmt->bindParam(':email', $email);
                $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                $stmt->execute();

                $stmt = $conn->prepare("UPDATE Étudiant SET ID_Promotion = :promotion WHERE ID_Étudiant = :id");
                $stmt->bindParam(':promotion', $promotion['ID_Promotion']);
                $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                $stmt->execute();

                header("Location: gestion_eleve.php");
                exit();
            } else {
                echo "Erreur: Formation non trouvée.";
            }
        }
    } catch (PDOException $e) {
        echo "Erreur: " . $e->getMessage();
        exit();
    }
} else {
    echo "ID de l'élève non spécifié.";
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
        <h1>Modifier un Élève</h1>
<form method="post" action="edit_eleve.php?id=<?= htmlspecialchars($id) ?>">
    <label for="nom">Nom :</label>
    <input type="text" id="nom" name="nom" value="<?= htmlspecialchars($student['Nom']) ?>" required><br>

    <label for="prenom">Prénom :</label>
    <input type="text" id="prenom" name="prenom" value="<?= htmlspecialchars($student['Prénom']) ?>" required><br>

    <label for="email">Email :</label>
    <input type="email" id="email" name="email" value="<?= htmlspecialchars($student['Email']) ?>" required><br>

    <label for="sigle">Sigle de la Formation :</label>
    <input type="text" id="sigle" name="sigle" value="<?= htmlspecialchars($student['Sigle']) ?>" required><br>

    <button type="submit">Enregistrer</button>
</form>

<button onclick="location.href='gestion_eleve.php'">Retour à la gestion des élèves</button>
        </div>
    </main>
    <div class="footer"> &copy; FindGrade </div>
    <script src="../javaScript/script.js"></script>
</body>
</html>
