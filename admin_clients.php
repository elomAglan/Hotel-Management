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
    if (isset($_POST['add']) && empty($_POST['id'])) {
        // Ajouter un nouveau client
        $nom = $_POST['nom'];
        $prenom = $_POST['prenom'];
        $num_carte = !empty($_POST['num_carte']) ? $_POST['num_carte'] : null;
        $telephone = $_POST['telephone'];
        $adresse = $_POST['adresse'];
        
        $query = "INSERT INTO clients (nom, prenom, num_carte, telephone, adresse) VALUES ('$nom', '$prenom', '$num_carte', '$telephone', '$adresse')";
        mysqli_query($con, $query);
    } elseif (isset($_POST['edit']) && !empty($_POST['id'])) {
        // Modifier un client existant
        $id = $_POST['id'];
        $nom = $_POST['nom'];
        $prenom = $_POST['prenom'];
        $num_carte = !empty($_POST['num_carte']) ? $_POST['num_carte'] : null;
        $telephone = $_POST['telephone'];
        $adresse = $_POST['adresse'];
        
        $query = "UPDATE clients SET nom = '$nom', prenom = '$prenom', num_carte = '$num_carte', telephone = '$telephone', adresse = '$adresse' WHERE id = $id";
        mysqli_query($con, $query);
    } elseif (isset($_POST['delete'])) {
        // Supprimer un client
        $id = $_POST['id'];
        
        $query = "DELETE FROM clients WHERE id = $id";
        mysqli_query($con, $query);
    }
}

// Récupérer les clients de la base de données
$query = "SELECT * FROM clients";
$result = mysqli_query($con, $query);
$clients = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gérer les Clients - Hôtel Luxe</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

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

        .form-container input, .form-container select {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .icon-edit {
    color: #007bff; /* Couleur bleue pour l'édition */
}

.icon-delete {
    color: #e74c3c; /* Couleur rouge pour la suppression */
}

.icon-receipt {
    color: #28a745; /* Couleur verte pour la génération de reçu */
}

/* Pour changer la couleur des icônes au survol */
.icon-edit:hover,
.icon-delete:hover,
.icon-receipt:hover {
    opacity: 0.7; /* Légère transparence pour l'effet de survol */
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
        <h1>Gestion des Clients</h1>

        <div class="table-container">
            <h2>Liste des Clients</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nom</th>
                        <th>Prénom</th>
                        <th>Numéro de Carte</th>
                        <th>Téléphone</th>
                        <th>Adresse</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($clients as $client): ?>
                    <tr>
                        <td><?= $client['id'] ?></td>
                        <td><?= $client['nom'] ?></td>
                        <td><?= $client['prenom'] ?></td>
                        <td><?= $client['num_carte'] ?></td>
                        <td><?= $client['telephone'] ?></td>
                        <td><?= $client['adresse'] ?></td>
                        <td>
    <form action="admin_clients.php" method="post" style="display:inline;">
        <input type="hidden" name="id" value="<?= $client['id'] ?>">
        <!-- Icône de suppression -->
        <button type="submit" name="delete" style="background: none; border: none; cursor: pointer;" title="Supprimer">
            <i class="fas fa-trash" style="color: #ff4d4d;"></i>
        </button>
        <!-- Icône de modification -->
        <button type="button" onclick="editClient('<?= $client['id'] ?>', '<?= $client['nom'] ?>', '<?= $client['prenom'] ?>', '<?= $client['num_carte'] ?>', '<?= $client['telephone'] ?>', '<?= $client['adresse'] ?>')" style="background: none; border: none; cursor: pointer;" title="Modifier">
            <i class="fas fa-edit" style="color: #004d99;"></i>
        </button>
    </form>
</td>

                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="form-container">
            <h2>Ajouter ou Modifier un Client</h2>
            <form action="admin_clients.php" method="post">
                <input type="hidden" name="id" id="client-id">
                <label for="nom">Nom</label>
                <input type="text" name="nom" id="nom" required>

                <label for="prenom">Prénom</label>
                <input type="text" name="prenom" id="prenom" required>

                <label for="num_carte">Numéro de Carte</label>
                <input type="text" name="num_carte" id="num_carte">

                <label for="telephone">Téléphone</label>
                <input type="text" name="telephone" id="telephone" required>

                <label for="adresse">Adresse</label>
                <input type="text" name="adresse" id="adresse" required>

                <button type="submit" name="add" id="submit-btn" class="btn">Ajouter</button>
            </form>
        </div>
    </main>
    <script>
        function editClient(id, nom, prenom, num_carte, telephone, adresse) {
            document.getElementById('client-id').value = id;
            document.getElementById('nom').value = nom;
            document.getElementById('prenom').value = prenom;
            document.getElementById('num_carte').value = num_carte;
            document.getElementById('telephone').value = telephone;
            document.getElementById('adresse').value = adresse;
            
            // Change the button text to "Modifier"
            const submitBtn = document.getElementById('submit-btn');
            submitBtn.textContent = 'Modifier';
            submitBtn.name = 'edit';
        }

        // Reset the form when the page is loaded or when the form is submitted
        document.getElementById('client-id').value = '';
    </script>
</body>
</html>