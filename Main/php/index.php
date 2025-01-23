<?php
session_start();
include 'db.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Se Connecter à FindGrade</title>
    <link rel="stylesheet" href="../css/style_connexion.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>
<body>
    <div class="wrapper">
            <div class="logo">
                <img src="https://upload.wikimedia.org/wikipedia/fr/3/30/Logo_Université_Gustave_Eiffel_2020.svg"  alt="Logo Université Gustave Eiffel">
                <img src="../images/Design_sans_titre_2.svg" class="FindGrade_logo" alt="Logo FindGrade">
            </div>
            <h1>Se Connecter à FindGrade</h1>
        <form id="loginForm" method="post" action="login.php">
                <div class="input-box">
                    <input type="text" name="username" placeholder="Identifiant" required>
                    <i class='bx bxs-user'><span class="material-icons">person</span></i>
                </div>
                <div class="input-box">
                    <input type="password" name="password" placeholder="Mot de passe" required>
                    <i class='bx bxs-lock-alt' ><span class="material-icons">lock</span></i>                
                </div>

                <?php if (isset($_SESSION['login_error'])): ?>
                <p style="color: red;"><?= $_SESSION['login_error'] ?></p>
                <?php unset($_SESSION['login_error']); ?>
                <?php endif; ?>

                <div class="remember-forgot">
                    <label><input type="checkbox" name="remember">Se souvenir de moi</label>
                    <a href="#">Mot de Passe Oublié ?</a>
                </div>

                <button type="submit" class="btn">Se connecter</button>

                <div class="footer"> &copy; FindGrade </div>
        </form>
    </div>

    <script src="../javaScript/script.js"></script>
</body>
</html>
