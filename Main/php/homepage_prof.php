<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user']) || $_SESSION['type'] !== 'professeur') {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user'];

try {
    $stmt = $conn->prepare("SELECT Utilisateur.Nom, Utilisateur.Prénom, Utilisateur.Genre, Enseignant.Statut 
                            FROM Utilisateur 
                            JOIN Enseignant ON Utilisateur.ID_Utilisateur = Enseignant.ID_Utilisateur 
                            WHERE Utilisateur.ID_Utilisateur = ?");
    $stmt->execute([$user_id]);
    $professor = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$professor) {
        echo "Erreur: Professeur non trouvé.";
        exit();
    }

    $ressources_stmt = $conn->prepare("SELECT ID_Ressource, Nom_Ressource 
                                       FROM Ressource 
                                       WHERE ID_Enseignant = (SELECT ID_Enseignant FROM Enseignant WHERE ID_Utilisateur = ?)");
    $ressources_stmt->execute([$user_id]);
    $resources = $ressources_stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Erreur: " . $e->getMessage();
    exit();
}

$genre = $professor['Genre'];
$statut = $professor['Statut'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/style_professeur.css">
    <link rel="stylesheet" href="../css/style_navbar_prof&etudiant.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <title>Accueil Professeur</title>
</head>
<body>
    <header>
        <div class="container">
            <nav class="navbar">
                <ul class="nav-links">
                    <li class="nav-link">
                        <a href="./homepage_redirect.php">Accueil</a>
                    </li>
                    <li class="nav-link services">
                        <a href="#">
                            Evaluations<span class="material-icons dropdown-icon">arrow_drop_down</span>
                        </a>
                        <ul class="drop-down">
                    <?php foreach ($resources as $resource): ?>
                        <li><a href="gestion_note.php?ressource_id=<?= htmlspecialchars($resource['ID_Ressource']) ?>"><?= htmlspecialchars($resource['Nom_Ressource']) ?></a></li>
                    <?php endforeach; ?>
                </ul>
                    </li>
                    <li class="nav-link">
                        <a href="https://elearning.univ-eiffel.fr/login/index.php">Cours Moodle</a>
                    </li>
                    <li class="nav-link">
                        <a href="https://edt.univ-eiffel.fr/direct/index.jsp?login=visuedt&password=visuedt">Emploi du temps </a>
                    </li>

                    <li class=" nav-link">
                        <a href="./account.html"><?= htmlspecialchars($professor['Prénom'] . ' ' . $professor['Nom']) ?><i><span class="material-icons iconeCompte">account_circle</span></a></i>
                        <ul class="drop-down-account">
                            <li><a href="">Mes informations</a></li>
                            <li><a href="">Préférences</a></li>
                            <li><a href="logout.php">Se déconnecter</a></li>
                        </ul>
                    </li>
                </ul>
            </nav>
        </div>
    </header>
    <main>
        <div class="presentation">
            <h1>Bonjour <?= htmlspecialchars($genre . ' ' . $professor['Nom']) ?></h1>
            <h2><?= htmlspecialchars($statut) ?> aux ressources : 
                <ul>
                    <?php foreach ($resources as $resource): ?>
                        <li><a href="gestion_note.php?ressource_id=<?= htmlspecialchars($resource['ID_Ressource']) ?>"><?= htmlspecialchars($resource['Nom_Ressource']) ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </h2>
        </div>
    </main>
    <div class="footer"> &copy; FindGrade </div>
    <script src="../javaScript/script.js"></script>
</body>
</html>
