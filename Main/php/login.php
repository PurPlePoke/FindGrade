<?php
session_start();
include 'db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    try {
        $stmt = $conn->prepare("SELECT ID_Utilisateur, Mot_de_passe, Type FROM Utilisateur WHERE CONCAT(Prénom, '.', Nom) = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['Mot_de_passe'])) {
            $_SESSION['user'] = $user['ID_Utilisateur'];
            $_SESSION['user_id'] = $user['ID_Utilisateur'];
            $_SESSION['type'] = $user['Type'];

            if ($user['Type'] == 'professeur') {
                $ressources_stmt = $conn->prepare("
                    SELECT ID_Ressource, Nom_Ressource 
                    FROM Ressource 
                    JOIN Enseignant ON Ressource.ID_Enseignant = Enseignant.ID_Enseignant 
                    WHERE Enseignant.ID_Utilisateur = ?
                ");
                $ressources_stmt->execute([$user['ID_Utilisateur']]);
                $_SESSION['ressources'] = $ressources_stmt->fetchAll(PDO::FETCH_ASSOC);
                header("Location: homepage_prof.php");
            } elseif ($user['Type'] == 'admin') {
                header("Location: homepage_admin.php");
            } elseif ($user['Type'] == 'eleve') {
                header("Location: homepage_eleve.php");
            }
            exit();
        } else {
            $error = 'Identifiant ou mot de passe incorrect';
        }
    } catch (PDOException $e) {
        $error = "Erreur: " . $e->getMessage();
    }
}

if (isset($error)) {
    $_SESSION['login_error'] = $error;
    header("Location: index.php");
    exit();
}
?>