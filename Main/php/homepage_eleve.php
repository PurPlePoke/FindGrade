<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user']) || $_SESSION['type'] !== 'eleve') {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user'];
$promotion_id = null;

try {
    $stmt = $conn->prepare("SELECT ID_Promotion FROM étudiant WHERE ID_Utilisateur = ?");
    $stmt->execute([$user_id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        $promotion_id = $result['ID_Promotion'];
    } else {
        throw new Exception("Promotion ID non trouvée pour l'utilisateur.");
    }

    $stmt = $conn->prepare("SELECT DISTINCT Année FROM promotion WHERE ID_Promotion = ?");
    $stmt->execute([$promotion_id]);
    $years = $stmt->fetchAll(PDO::FETCH_COLUMN);

    $stmt = $conn->prepare("SELECT ID_UE, Nom_UE FROM ue WHERE ID_Formation IN (SELECT ID_Formation FROM promotion WHERE ID_Promotion = ?)");
    $stmt->execute([$promotion_id]);
    $ues = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $stmt = $conn->prepare("SELECT ID_Ressource, Nom_Ressource FROM ressource WHERE ID_UE IN (SELECT ID_UE FROM ue WHERE ID_Formation IN (SELECT ID_Formation FROM promotion WHERE ID_Promotion = ?))");
    $stmt->execute([$promotion_id]);
    $resources = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $stmt = $conn->prepare("SELECT Prénom, Nom FROM utilisateur WHERE ID_Utilisateur = ?");
    $stmt->execute([$user_id]);
    $profile = $stmt->fetch(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    echo "Erreur: " . $e->getMessage();
    exit();
} catch (Exception $e) {
    echo "Erreur: " . $e->getMessage();
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $selected_year = $_POST['year'];
    $selected_semester = $_POST['semester'];
    $selected_ue = $_POST['ue'];
    $selected_resource = $_POST['resource'];

    try {
        $stmt = $conn->prepare("
            SELECT AVG(Note) as moyenne
            FROM note
            JOIN ressource ON note.ID_Ressource = ressource.ID_Ressource
            WHERE ressource.ID_UE = ? AND ressource.Semestre = ?
        ");
        $stmt->execute([$selected_ue, $selected_semester]);
        $average = $stmt->fetch(PDO::FETCH_ASSOC)['moyenne'];

        $stmt = $conn->prepare("
            SELECT Utilisateur.Nom, Utilisateur.Prénom, AVG(Note) as moyenne
            FROM note
            JOIN étudiant ON note.ID_Étudiant = étudiant.ID_Étudiant
            JOIN utilisateur ON étudiant.ID_Utilisateur = utilisateur.ID_Utilisateur
            WHERE étudiant.ID_Promotion = ? AND note.ID_Ressource IN (SELECT ID_Ressource FROM ressource WHERE ID_UE = ? AND Semestre = ?)
            GROUP BY étudiant.ID_Étudiant
            ORDER BY moyenne DESC
        ");
        $stmt->execute([$promotion_id, $selected_ue, $selected_semester]);
        $ranking = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Erreur: " . $e->getMessage();
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/style_eleve.css">
    <link rel="stylesheet" href="../css/style_navbar_prof&etudiant.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <title>Accueil Étudiant</title>
</head>
<body>
<header>
    <div class="container">
        <nav class="navbar">
            <ul class="nav-links">
                <li class="nav-link"><a href="./homepage_eleve.php">Accueil</a></li>
                <li class="nav-link"><a href="https://elearning.univ-eiffel.fr/login/index.php">Cours Moodle</a></li>
                <li class="nav-link"><a href="https://edt.univ-eiffel.fr/direct/index.jsp?login=visuedt&password=visuedt">Emploi du temps</a></li>
                <li class="nav-link">
                    <a href="./account.html">
                        <span class="material-icons">account_circle</span> <?= htmlspecialchars($profile['Prénom'] . ' ' . $profile['Nom']) ?>
                    </a>
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
        <h1>Choisir une Année, un Semestre, une UE, et une Ressource</h1>
        <form method="post" action="homepage_eleve.php">
            <label for="year">Année :</label>
            <select id="year" name="year">
                <option value="">Sélectionner une année</option>
                <?php foreach ($years as $year): ?>
                    <option value="<?= htmlspecialchars($year) ?>"><?= htmlspecialchars($year) ?></option>
                <?php endforeach; ?>
            </select><br>

            <label for="semester">Semestre :</label>
            <select id="semester" name="semester">
                <option value="1">Semestre 1</option>
                <option value="2">Semestre 2</option>
            </select><br>

            <label for="ue">UE :</label>
            <select id="ue" name="ue">
                <option value="">Sélectionner une UE</option>
                <?php foreach ($ues as $ue): ?>
                    <option value="<?= htmlspecialchars($ue['ID_UE']) ?>"><?= htmlspecialchars($ue['Nom_UE']) ?></option>
                <?php endforeach; ?>
            </select><br>

            <label for="resource">Ressource :</label>
            <select id="resource" name="resource">
                <option value="">Sélectionner une ressource</option>
                <?php foreach ($resources as $resource): ?>
                    <option value="<?= htmlspecialchars($resource['ID_Ressource']) ?>"><?= htmlspecialchars($resource['Nom_Ressource']) ?></option>
                <?php endforeach; ?>
            </select><br>

            <button type="submit">Valider</button>
        </form>

        <?php if ($_SERVER['REQUEST_METHOD'] == 'POST'): ?>
            <h2>Résultats pour l'année, le semestre, l'UE et la ressource :</h2>
            <p>Moyenne de l'élève: <?= htmlspecialchars($average) ?></p>
            <h3>Classement :</h3>
            <table>
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Prénom</th>
                        <th>Moyenne</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($ranking as $rank): ?>
                        <tr>
                            <td><?= htmlspecialchars($rank['Nom']) ?></td>
                            <td><?= htmlspecialchars($rank['Prénom']) ?></td>
                            <td><?= htmlspecialchars($rank['moyenne']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</main>
<div class="footer"> &copy; FindGrade </div>
<script src="../javaScript/script.js"></script>
</body>
</html>
