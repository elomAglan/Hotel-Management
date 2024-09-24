<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription</title>
    <link rel="stylesheet" href="style.css">
</head>
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
        .form input[type="email"],
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
<body>
<?php
// Inclure le fichier de connexion à la base de données
include_once "db.php";

if (isset($_POST['register'])) {
    $username = mysqli_real_escape_string($con, $_POST['username']);
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $password = mysqli_real_escape_string($con, $_POST['password']);
    $confirm_password = mysqli_real_escape_string($con, $_POST['confirm_password']);
    
    // Vérifier si les mots de passe correspondent
    if ($password != $confirm_password) {
        $error = "Les mots de passe ne correspondent pas";
    } else {
        // Vérifier si le nom d'utilisateur ou l'email existe déjà
        $query = "SELECT * FROM utilisateurs WHERE username='$username' OR email='$email'";
        $result = mysqli_query($con, $query);
        
        if (mysqli_num_rows($result) > 0) {
            $error = "Le nom d'utilisateur ou l'email est déjà pris";
        } else {
            // Hacher le mot de passe
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            // Insérer le nouvel utilisateur dans la base de données
            $query = "INSERT INTO utilisateurs (username, email, password) VALUES ('$username', '$email', '$hashed_password')";
            
            if (mysqli_query($con, $query)) {
                // Rediriger l'utilisateur vers la page de connexion après une inscription réussie
                header("Location: login.php");
                exit();
            } else {
                $error = "Erreur lors de la création du compte. Veuillez réessayer.";
            }
        }
    }
}
?>


<div class="form">
    <a href="login.php" class="back_btn">J'ai déjà un compte !</a>
    <h2>Créer un compte</h2>
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
        <label>Email</label>
        <input type="email" name="email" required>
        <label>Mot de passe</label>
        <input type="password" name="password" required>
        <label>Confirmer le mot de passe</label>
        <input type="password" name="confirm_password" required>
        <input type="submit" value="S'inscrire" name="register">
    </form>
</div>
</body>
</html>