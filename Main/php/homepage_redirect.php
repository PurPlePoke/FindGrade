<?php
session_start();

if (!isset($_SESSION['user']) || !isset($_SESSION['type'])) {
    header("Location: index.php");
    exit();
}

switch ($_SESSION['type']) {
    case 'admin':
        header("Location: homepage_admin.php");
        break;
    case 'professeur':
        header("Location: homepage_prof.php");
        break;
    case 'eleve':
        header("Location: homepage_eleve.php");
        break;
    default:
        header("Location: index.php");
        break;
}

exit();
?>
