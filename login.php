<?php
// Afficher les erreurs PHP
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Inclure le fichier de connexion à la base de données
include_once "db.php";

// Vérifier si le formulaire de connexion est soumis
if (isset($_POST['login'])) {
    $username = mysqli_real_escape_string($con, $_POST['username']);
    $password = mysqli_real_escape_string($con, $_POST['password']);
    
    // Requête pour vérifier les informations de connexion
    $query = "SELECT * FROM utilisateurs WHERE username='$username'";
    $result = mysqli_query($con, $query);

    if (!$result) {
        die("Erreur de requête SQL : " . mysqli_error($con));
    } 

    if (mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
        // Vérifier le mot de passe
        if (password_verify($password, $user['password'])) {
            // Démarrer la session et rediriger vers la page d'accueil
            session_start();
            $_SESSION['username'] = $username;
            $_SESSION['role'] = $user['role']; // Stocker le rôle dans la session
            if ($user['role'] == 'admin') {
                header("Location: admin_dashboard.php");
            } else {
                header("Location: acceuil.php");
            }
            exit();
        } else {
            $error = "Mot de passe incorrect";
        }
    } else {
        $error = "Nom d'utilisateur incorrect";
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion</title>
    
    <style>
        /* Styles de base */
        body {
            font-family: 'Roboto', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background: linear-gradient(135deg, #71b7e6, #9b59b6);
            color: #fff;
        }
        .form {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }
        .form h2 {
            margin-bottom: 20px;
            color: #333;
        }
        .form label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #333;
        }
        .form input[type="text"],
        .form input[type="password"] {
            width: calc(100% - 22px);
            padding: 10px;
            margin-bottom: 20px;
            border: 2px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
        }
        .form input[type="submit"] {
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 5px;
            background-color: #9b59b6;
            color: #fff;
            font-size: 18px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .form input[type="submit"]:hover {
            background-color: #8e44ad;
        }
        .form .erreur_message {
            color: red;
            margin-bottom: 15px;
        }
        .form a.back_btn {
            display: inline-block;
            margin-bottom: 20px;
            text-decoration: none;
            color: #71b7e6;
        }
        .form a.back_btn img {
    vertical-align: middle;
    margin-right: 8px;
    width: 25px; /* Ajustez la largeur selon vos besoins */
    height: auto; /* Maintient le ratio d'aspect */
}


        /* Styles réactifs */
        @media (max-width: 600px) {
            .form {
                padding: 20px;
                width: 90%;
            }
            .form input[type="submit"] {
                font-size: 16px;
            }
        }
    </style>
</head>
<body>
<div class="form">
    <a href="register.php" class="back_btn">J'ai pas de compte ! </a>
    <br>
    <a href="acceuil.php" class="back_btn">Connecter sans compte ! </a>
    <h2>Connexion</h2>
    <p class="erreur_message">
        <?php 
        if (isset($error)) {
            echo $error;
        }
        ?>
    </p>
    <form action="" method="POST">
        <label>Nom d'utilisateur</label>
        <input type="text" name="username" required>
        <label>Mot de passe</label>
        <input type="password" name="password" required>
        <input type="submit" value="Se connecter" name="login">
    </form>
</div>
</body>
</html>
