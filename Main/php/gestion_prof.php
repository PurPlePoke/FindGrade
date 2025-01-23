<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user']) || $_SESSION['type'] !== 'admin') {
    header("Location: index.php");
    exit();
}

$stmt = $conn->query("
    SELECT Enseignant.ID_Enseignant, Utilisateur.Nom, Utilisateur.Prénom, Utilisateur.Email, Enseignant.Statut
    FROM Enseignant
    JOIN Utilisateur ON Enseignant.ID_Utilisateur = Utilisateur.ID_Utilisateur
");
$professors = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
        <h1>Gestion des Professeurs</h1>
<table>
    <thead>
        <tr>
            <th>Nom</th>
            <th>Prénom</th>
            <th>Email</th>
            <th>Statut</th>
            <th>Ressources</th>
            <th class="table-action-column"><a onclick="location.href='add_prof.php'" type="button" style="display: inline-block;"><span class="material-icons">add_box</span></a></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($professors as $professor): ?>
            <tr>
                <td><?= htmlspecialchars($professor['Nom']) ?></td>
                <td><?= htmlspecialchars($professor['Prénom']) ?></td>
                <td><?= htmlspecialchars($professor['Email']) ?></td>
                <td><?= htmlspecialchars($professor['Statut']) ?></td>
                <td>
                    <?php
                    $ressources_stmt = $conn->prepare("SELECT Nom_Ressource FROM Ressource WHERE ID_Enseignant = ?");
                    $ressources_stmt->execute([$professor['ID_Enseignant']]);
                    $ressources = $ressources_stmt->fetchAll(PDO::FETCH_ASSOC);
                    foreach ($ressources as $ressource) {
                        echo htmlspecialchars($ressource['Nom_Ressource']) . "<br>";
                    }
                    ?>
                </td>
                <td class="action-icons">
                    <a href="edit_eleve.php?id=<?= htmlspecialchars($student['ID_Étudiant']) ?>"><span class="material-icons">edit</span></a>
                    <a href="delete_eleve.php?id=<?= htmlspecialchars($student['ID_Étudiant']) ?>" onclick="return confirm('Are you sure you want to delete this student?')"><span class="material-icons">delete</span></a>
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
