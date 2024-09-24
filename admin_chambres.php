<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Afficher les erreurs PHP
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Connexion à la base de données
include_once "db.php";

// Traitement des actions POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['edit'])) {
        $id = $_POST['id'];

        // Récupérer le statut actuel de la chambre
        $query = "SELECT statut FROM chambres WHERE id = ?";
        $stmt = $con->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $current_status = $row['statut'];

            // Déterminer le nouveau statut
            $new_status = ($current_status === 'Libre') ? 'Occupé' : 'Libre';

            // Mettre à jour le statut de la chambre
            $query = "UPDATE chambres SET statut = ? WHERE id = ?";
            $stmt = $con->prepare($query);
            $stmt->bind_param("si", $new_status, $id);
            $stmt->execute();
        } else {
            // Gestion du cas où la chambre n'existe pas
            echo "Chambre non trouvée.";
        }
    }
}

// Récupérer les chambres de la base de données
$query = "SELECT * FROM chambres";
$result = $con->query($query);
$chambres = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gérer les Chambres - Hôtel Luxe</title>
    <style>
        /* Styles de base */
        body {
            font-family: 'Roboto', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f0f2f5;
            color: #333;
        }

        /* Navbar verticale à gauche */
        .navbar {
            width: 250px;
            background-color: #004d99;
            color: #fff;
            height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            padding: 30px 20px;
            position: fixed;
        }

        .navbar .logo {
            font-size: 28px;
            color: #fff;
            text-decoration: none;
            margin-bottom: 40px;
        }

        .navbar nav ul {
            list-style: none;
            margin: 0;
            padding: 0;
            width: 100%;
        }

        .navbar nav ul li {
            margin-bottom: 20px;
        }

        .navbar nav ul li a {
            color: #fff;
            text-decoration: none;
            font-size: 18px;
            padding: 12px;
            display: block;
            transition: background-color 0.3s;
            border-radius: 5px;
        }

        .navbar nav ul li a:hover {
            background-color: #003366;
        }

        /* Contenu principal décalé à droite */
        main {
            margin-left: 270px;
            padding: 40px;
            background-color: #fff;
        }

        h1 {
            font-size: 28px;
            margin-bottom: 20px;
        }

        .table-container {
            margin-bottom: 40px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th, td {
            padding: 12px;
            text-align: left;
        }

        th {
            background-color: #f4f4f4;
        }

        .btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #004d99;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            font-size: 16px;
            transition: background-color 0.3s;
        }

        .btn:hover {
            background-color: #003366;
        }

        .form-container {
            margin-top: 20px;
        }

        .form-container form {
            background-color: #f7f9fb;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .form-container label {
            display: block;
            margin-bottom: 10px;
            font-weight: bold;
        }

        .form-container input, .form-container select, .form-container textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <header>
        <div class="navbar">
            <a href="admin_dashboard.php" class="logo">Hôtel Luxe</a>
            <nav>
                <ul>
                <li><a href="admin_dashboard.php">Dashboard</a></li>
                <li><a href="admin_clients.php">Clients</a></li>
                <li><a href="admin_chambres.php">Chambres</a></li>
                <li><a href="admin_reservations.php">Réservations</a></li>
                <li><a href="logout.php">Déconnexion</a></li>
                </ul>
            </nav>
        </div>
    </header>
    
    <main>
        <h1>Gestion des Chambres</h1>

        <div class="table-container">
            <h2>Liste des Chambres</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Numéro</th>
                        <th>Type</th>
                        <th>Prix</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($chambres as $chambre): ?>
                    <tr>
                        <td><?= $chambre['id'] ?></td>
                        <td><?= $chambre['numero_chambre'] ?></td>
                        <td><?= $chambre['type_chambre'] ?></td>
                        <td><?= number_format($chambre['prix'], 2, ',', ' ') ?> cfa</td>
                        <td><?= $chambre['statut'] ?></td>
                        <td>
                            <form action="admin_chambres.php" method="post" style="display:inline;">
                                <input type="hidden" name="id" value="<?= $chambre['id'] ?>">
                                <button type="submit" name="edit" class="btn">Modifier</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>
