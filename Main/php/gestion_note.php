<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user']) || $_SESSION['type'] !== 'professeur') {
    header("Location: index.php");
    exit();
}

if (!isset($_GET['ressource_id'])) {
    echo "Ressource ID manquant.";
    exit();
}

$ressource_id = $_GET['ressource_id'];
$user_id = $_SESSION['user'];

try {
    $stmt = $conn->prepare("SELECT Note.ID_Note, Note.Note, Note.Coefficient, Note.Date, Utilisateur.Nom, Utilisateur.Prénom 
                            FROM Note 
                            JOIN Étudiant ON Note.ID_Étudiant = Étudiant.ID_Étudiant
                            JOIN Utilisateur ON Étudiant.ID_Utilisateur = Utilisateur.ID_Utilisateur
                            WHERE Note.ID_Ressource = ?");
    $stmt->execute([$ressource_id]);
    $notes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $stmt = $conn->prepare("SELECT Nom_Ressource FROM Ressource WHERE ID_Ressource = ?");
    $stmt->execute([$ressource_id]);
    $resource = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$resource) {
        echo "Erreur: Ressource non trouvée.";
        exit();
    }

    $stmt = $conn->prepare("SELECT Utilisateur.Prénom, Utilisateur.Nom 
                            FROM Utilisateur 
                            JOIN Enseignant ON Utilisateur.ID_Utilisateur = Enseignant.ID_Utilisateur 
                            WHERE Utilisateur.ID_Utilisateur = ?");
    $stmt->execute([$user_id]);
    $professor = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$professor) {
        throw new Exception("Erreur: Professeur non trouvé.");
    }

    $stmt = $conn->prepare("SELECT Ressource.ID_Ressource, Ressource.Nom_Ressource 
                            FROM Ressource 
                            JOIN Enseignant ON Ressource.ID_Enseignant = Enseignant.ID_Enseignant 
                            WHERE Enseignant.ID_Utilisateur = ?");
    $stmt->execute([$user_id]);
    $resources = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!$resources) {
        throw new Exception("Erreur: Aucune ressource trouvée pour ce professeur.");
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
    <link rel="stylesheet" href="../css/style_professeur.css">
    <link rel="stylesheet" href="../css/style_navbar_prof&etudiant.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <title>Gestion des Notes</title>
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
                    <?php foreach ($resources as $resource_item): ?>
                        <li><a href="gestion_note.php?ressource_id=<?= htmlspecialchars($resource_item['ID_Ressource']) ?>"><?= htmlspecialchars($resource_item['Nom_Ressource']) ?></a></li>
                    <?php endforeach; ?>
                </ul>
                    <li class="nav-link">
                        <a href="https://elearning.univ-eiffel.fr/login/index.php">Cours Moodle</a>
                    </li>
                    <li class="nav-link">
                        <a href="https://edt.univ-eiffel.fr/direct/index.jsp?login=visuedt&password=visuedt">Emploi du temps </a>
                    </li>

                    <li class=" nav-link">
                        <a href="./account.html"><?= htmlspecialchars($professor['Prénom'] . ' ' . $professor['Nom']) ?><i><span class="material-icons">account_circle</span></a></i>
                        <ul class="drop-down-account">
                            <li><a href="./informations.html">Mes informations</a></li>
                            <li><a href="./preference.html">Préférences</a></li>
                            <li><a href="./logout.php">Se déconnecter</a></li>
                        </ul>
                    </li>
                </ul>
            </nav>
        </div>
    </header>
    <main>
        <div class="presentation">
            <h1>Notes pour la Ressource: <?= htmlspecialchars($resource['Nom_Ressource']) ?></h1>
            <table>
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Prénom</th>
                        <th>Note</th>
                        <th>Coefficient</th>
                        <th>Date</th>
                        <th><a onclick="location.href='add_note.php?ressource_id=<?= htmlspecialchars($ressource_id) ?>'" type="button"><span class="material-icons">add_box</span></a></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($notes as $note): ?>
                        <tr>
                            <td><?= htmlspecialchars($note['Nom']) ?></td>
                            <td><?= htmlspecialchars($note['Prénom']) ?></td>
                            <td><?= htmlspecialchars($note['Note']) ?></td>
                            <td><?= htmlspecialchars($note['Coefficient']) ?></td>
                            <td><?= htmlspecialchars($note['Date']) ?></td>
                            <td>
                            <a href="edit_note.php?id=<?= htmlspecialchars($note['ID_Note']) ?>&ressource_id=<?= htmlspecialchars($ressource_id) ?>"><span class="material-icons">edit</span></a>
                            <a href="delete_note.php?id=<?= htmlspecialchars($note['ID_Note']) ?>&ressource_id=<?= htmlspecialchars($ressource_id) ?>" onclick="return confirm('Voulez-vous vraiment supprimer cette note ?')"><span class="material-icons">delete</span></a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>
    <div class="footer"> &copy; FindGrade </div>
    <script src="../javaScript/script.js"></script>
</body>
</html>




