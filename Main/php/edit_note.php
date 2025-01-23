<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user']) || $_SESSION['type'] !== 'professeur') {
    header("Location: index.php");
    exit();
}

if (!isset($_GET['note_id'])) {
    echo "ID de la note manquant.";
    exit();
}

$note_id = $_GET['note_id'];

try {
    $stmt = $conn->prepare("SELECT Note, Coefficient, ID_Étudiant FROM Note WHERE ID_Note = ?");
    $stmt->execute([$note_id]);
    $note = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$note) {
        throw new Exception("Erreur: Note non trouvée.");
    }

    $stmt = $conn->prepare("SELECT Utilisateur.Nom, Utilisateur.Prénom 
                            FROM Étudiant 
                            JOIN Utilisateur ON Étudiant.ID_Utilisateur = Utilisateur.ID_Utilisateur 
                            WHERE Étudiant.ID_Étudiant = ?");
    $stmt->execute([$note['ID_Étudiant']]);
    $student = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$student) {
        throw new Exception("Erreur: Étudiant non trouvé.");
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $new_note = $_POST['note'];
        $new_coefficient = $_POST['coefficient'];

        $stmt = $conn->prepare("UPDATE Note SET Note = ?, Coefficient = ? WHERE ID_Note = ?");
        $stmt->execute([$new_note, $new_coefficient, $note_id]);

        header("Location: gestion_note.php?ressource_id=" . $_GET['ressource_id']);
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
    <link rel="stylesheet" href="../css/style_professeur.css">
    <link rel="stylesheet" href="../css/style_navbar_prof&etudiant.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <title>Modifier une Note</title>
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
                            <li><a href="./ajout_evaluation.php">Ajouter une évaluation</a></li>
                            <li><a href="./voir_controle.php">Voir mes évaluations</a></li>
                        </ul>
                    </li>
                    <li class="nav-link">
                        <a href="https://elearning.univ-eiffel.fr/login/index.php">Cours Moodle</a>
                    </li>
                    <li class="nav-link">
                        <a href="https://edt.univ-eiffel.fr/direct/index.jsp?login=visuedt&password=visuedt">Emploi du temps </a>
                    </li>

                    <li class=" nav-link">
                        <a href="./account.php"><?= htmlspecialchars($_SESSION['user']['Prénom'] . ' ' . $_SESSION['user']['Nom']) ?><i><span class="material-icons">account_circle</span></a></i>
                        <ul class="drop-down-account">
                            <li><a href="./informations.php">Mes informations</a></li>
                            <li><a href="./preference.php">Préférences</a></li>
                            <li><a href="./logout.php">Se déconnecter</a></li>
                        </ul>
                    </li>
                </ul>
            </nav>
        </div>
    </header>
    <main>
        <div class="presentation">
            <h1>Modifier la Note de: <?= htmlspecialchars($student['Prénom'] . ' ' . $student['Nom']) ?></h1>
            <form method="post" action="edit_note.php?note_id=<?= htmlspecialchars($note_id) ?>&ressource_id=<?= htmlspecialchars($_GET['ressource_id']) ?>">
                <label for="note">Note :</label>
                <input type="number" id="note" name="note" step="0.1" value="<?= htmlspecialchars($note['Note']) ?>" required><br><br>

                <label for="coefficient">Coefficient :</label>
                <input type="number" id="coefficient" name="coefficient" step="0.1" value="<?= htmlspecialchars($note['Coefficient']) ?>" required><br><br>

                <button type="submit">Enregistrer</button>
            </form>
        </div>
    </main>
    <div class="footer"> &copy; FindGrade </div>
    <script src="../javaScript/script.js"></script>
</body>
</html>
