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
    $stmt = $conn->prepare("SELECT Utilisateur.Prénom, Utilisateur.Nom, Enseignant.ID_Enseignant 
                            FROM Utilisateur 
                            JOIN Enseignant ON Utilisateur.ID_Utilisateur = Enseignant.ID_Utilisateur 
                            WHERE Utilisateur.ID_Utilisateur = ?");
    $stmt->execute([$user_id]);
    $professor = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$professor) {
        throw new Exception("Erreur: Professeur non trouvé.");
    }

    $professor_id = $professor['ID_Enseignant'];

    $stmt = $conn->prepare("SELECT Nom_Ressource FROM Ressource WHERE ID_Ressource = ?");
    $stmt->execute([$ressource_id]);
    $resource = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$resource) {
        throw new Exception("Erreur: Ressource non trouvée.");
    }

    $stmt = $conn->prepare("SELECT Promotion.ID_Promotion 
                            FROM Ressource 
                            JOIN UE ON Ressource.ID_UE = UE.ID_UE 
                            JOIN Formation ON UE.ID_Formation = Formation.ID_Formation
                            JOIN Promotion ON Formation.ID_Formation = Promotion.ID_Formation
                            WHERE Ressource.ID_Ressource = ?");
    $stmt->execute([$ressource_id]);
    $promotion = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$promotion) {
        throw new Exception("Erreur: Promotion non trouvée.");
    }

    $promotion_id = $promotion['ID_Promotion'];

    $students_stmt = $conn->prepare("SELECT Étudiant.ID_Étudiant, Utilisateur.Nom, Utilisateur.Prénom 
                                     FROM Étudiant 
                                     JOIN Utilisateur ON Étudiant.ID_Utilisateur = Utilisateur.ID_Utilisateur 
                                     WHERE Étudiant.ID_Promotion = ?");
    $students_stmt->execute([$promotion_id]);
    $students = $students_stmt->fetchAll(PDO::FETCH_ASSOC);

    $eval_stmt = $conn->prepare("SELECT ID_Évaluation FROM Évaluation WHERE ID_Promotion = ? AND ID_Ressource = ?");
    $eval_stmt->execute([$promotion_id, $ressource_id]);
    $evaluation = $eval_stmt->fetch(PDO::FETCH_ASSOC);

    if (!$evaluation) {
        $eval_insert_stmt = $conn->prepare("INSERT INTO Évaluation (ID_Promotion, ID_Enseignant, ID_Ressource, Horaire) VALUES (?, ?, ?, ?)");
        $eval_insert_stmt->execute([$promotion_id, $professor_id, $ressource_id, date('Y-m-d H:i:s')]);
        $evaluation_id = $conn->lastInsertId();
    } else {
        $evaluation_id = $evaluation['ID_Évaluation'];
    }

} catch (PDOException $e) {
    echo "Erreur: " . $e->getMessage();
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $coefficient = $_POST['coefficient'];
    $notes = $_POST['notes'];
    $date = date('Y-m-d');

    try {
        $conn->beginTransaction();

        foreach ($notes as $etudiant_id => $note) {
            $stmt = $conn->prepare("INSERT INTO Note (ID_Étudiant, ID_Ressource, ID_Enseignant, Coefficient, Note, Date, ID_Évaluation) 
                                    VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$etudiant_id, $ressource_id, $professor_id, $coefficient, $note, $date, $evaluation_id]);
        }

        $conn->commit();

        header("Location: gestion_note.php?ressource_id=" . $ressource_id);
        exit();
    } catch (Exception $e) {
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
    <link rel="stylesheet" href="../css/style_professeur.css">
    <link rel="stylesheet" href="../css/style_navbar_prof&etudiant.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <title>Ajouter des Notes</title>
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
                        <a href="./account.php"><?= htmlspecialchars($professor['Prénom'] . ' ' . $professor['Nom']) ?><i><span class="material-icons">account_circle</span></a></i>
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
            <h1>Ajouter des Notes pour la Ressource: <?= htmlspecialchars($resource['Nom_Ressource']) ?></h1>
            <form method="post" action="add_note.php?ressource_id=<?= htmlspecialchars($ressource_id) ?>">
                <label for="coefficient">Coefficient :</label>
                <input type="number" id="coefficient" name="coefficient" step="0.1" required><br><br>

                <table>
                    <thead>
                        <tr>
                            <th>Nom</th>
                            <th>Prénom</th>
                            <th>Note</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($students as $student): ?>
                            <tr>
                                <td><?= htmlspecialchars($student['Nom']) ?></td>
                                <td><?= htmlspecialchars($student['Prénom']) ?></td>
                                <td>
                                    <input type="number" name="notes[<?= htmlspecialchars($student['ID_Étudiant']) ?>]" step="0.1" required>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <button type="submit">Enregistrer</button>
            </form>
        </div>
    </main>
    <div class="footer"> &copy; FindGrade </div>
    <script src="../javaScript/script.js"></script>
</body>
</html>
