<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user']) || $_SESSION['type'] !== 'admin') {
    header("Location: index.php");
    exit();
}

$stmt = $conn->query("SELECT Ressource.ID_Ressource, Ressource.Nom_Ressource, UE.Nom_UE, Formation.Nom_Formation
                      FROM Ressource
                      JOIN UE ON Ressource.ID_UE = UE.ID_UE
                      JOIN Formation ON UE.ID_Formation = Formation.ID_Formation");
$ressources = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
                        <a href="./homepage_redirect.php">Accueil</a>
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
        <h1>Gestion des Ressources</h1>
<table>
    <thead>
        <tr>
            <th>Nom de la Ressource</th>
            <th>UE</th>
            <th>Formation</th>
            <th><a onclick="location.href='add_ressource.php'" type="button"><span class="material-icons">add_box</span></a></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($ressources as $ressource): ?>
            <tr>
                <td><?= htmlspecialchars($ressource['Nom_Ressource']) ?></td>
                <td><?= htmlspecialchars($ressource['Nom_UE']) ?></td>
                <td><?= htmlspecialchars($ressource['Nom_Formation']) ?></td>
                <td>
                    <a href="edit_ressource.php?id=<?= htmlspecialchars($ressource['ID_Ressource']) ?>"><span class="material-icons">edit</span></a>
                    <a href="delete_ressource.php?id=<?= htmlspecialchars($ressource['ID_Ressource']) ?>" onclick="return confirm('Are you sure you want to delete this resource?')"><span class="material-icons">delete</span></a>
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
