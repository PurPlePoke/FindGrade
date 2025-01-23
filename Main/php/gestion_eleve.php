<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user']) || $_SESSION['type'] !== 'admin') {
    header("Location: index.php");
    exit();
}

try {
    $stmt = $conn->query("SELECT 
                            Étudiant.ID_Étudiant, 
                            Utilisateur.Nom, 
                            Utilisateur.Prénom, 
                            Utilisateur.Email, 
                            Formation.Sigle,
                            AVG(Ressource_Avg.Average_Note) as Moyenne
                          FROM Étudiant
                          JOIN Utilisateur ON Étudiant.ID_Utilisateur = Utilisateur.ID_Utilisateur
                          JOIN Promotion ON Étudiant.ID_Promotion = Promotion.ID_Promotion
                          JOIN Formation ON Promotion.ID_Formation = Formation.ID_Formation
                          LEFT JOIN (
                              SELECT Note.ID_Étudiant, UE.ID_UE, AVG(Note.Note) as Average_Note
                              FROM Note
                              JOIN Ressource ON Note.ID_Ressource = Ressource.ID_Ressource
                              JOIN UE ON Ressource.ID_UE = UE.ID_UE
                              GROUP BY Note.ID_Étudiant, UE.ID_UE
                          ) as Ressource_Avg ON Étudiant.ID_Étudiant = Ressource_Avg.ID_Étudiant
                          GROUP BY Étudiant.ID_Étudiant
                          ORDER BY Moyenne DESC");
    $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Erreur: " . $e->getMessage();
    die();
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
        <h1>Gestion des Élèves</h1>
<table>
    <thead>
        <tr>
            <th>Classement</th>
            <th>ID Élève</th>
            <th>Nom</th>
            <th>Prénom</th>
            <th>Email</th>
            <th>Formation</th>
            <th>Moyenne</th>
            <th class="table-action-column"><a onclick="location.href='add_eleve.php'" type="button" style="display: inline-block;"><span class="material-icons">add_box</span></a></th>
        </tr>
    </thead>
    <tbody>
        <?php $rank = 1; ?>
        <?php foreach ($students as $student): ?>
            <tr>
                <td><?php echo $rank++; ?></td>
                <td><?php echo htmlspecialchars($student['ID_Étudiant']); ?></td>
                <td><?php echo htmlspecialchars($student['Nom']); ?></td>
                <td><?php echo htmlspecialchars($student['Prénom']); ?></td>
                <td><?php echo htmlspecialchars($student['Email']); ?></td>
                <td><?php echo htmlspecialchars($student['Sigle']); ?></td>
                <td><?php echo htmlspecialchars(number_format($student['Moyenne'], 2)); ?></td>
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
