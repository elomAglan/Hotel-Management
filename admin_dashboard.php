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

// Date actuelle
$date_actuelle = date('Y-m-d');
$jours_avertissement = 1; // Nombre de jours avant la sortie
$date_limite = date('Y-m-d', strtotime($date_actuelle . ' + ' . $jours_avertissement . ' days'));

// Requête SQL pour récupérer les réservations dont la date de sortie est proche ou qui sont d'une seule journée
$query = "SELECT r.id, r.date_fin, c.nom, c.prenom, c.telephone
          FROM reservations r
          JOIN clients c ON r.client_id = c.id
          WHERE r.date_fin = ? OR r.date_fin = ? OR (r.date_debut = ? AND r.date_fin = ?)";
$stmt = $con->prepare($query);
$stmt->bind_param('ssss', $date_limite, $date_actuelle, $date_actuelle, $date_actuelle);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de Bord Admin - Hôtel Luxe</title>
    <style>
        /* Styles de base */
        body {
            font-family: 'Roboto', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f0f2f5;
            color: #333;
            display: flex;
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
            padding: 40px 30px;
            flex: 1;
            background-color: #fff;
            min-height: 100vh;
        }

/* Animation de clignotement avec rouge adouci */
@keyframes clignote {
    0% { background-color: #ff6666; }   /* Rouge adouci */
    50% { background-color: #ff9999; }  /* Rouge encore plus adouci */
    100% { background-color: #ff6666; } /* Rouge adouci */
}

.clignotant {
    animation: clignote 1s infinite;
    color: #fff;
    font-weight: bold;
    padding: 10px;
    border-radius: 5px;
}


        /* Section Hero avec image en arrière-plan */
        .hero {
            text-align: center;
            padding: 100px 20px;
            background: url('hotel.jfif') no-repeat center center;
            background-size: cover;
            color: #fff;
            border-radius: 10px;
            margin-bottom: 30px;
        }

        .hero h1 {
            font-size: 48px;
            margin-bottom: 20px;
            text-shadow: 2px 2px 5px rgba(0, 0, 0, 0.7);
        }

        .hero p {
            font-size: 24px;
            text-shadow: 2px 2px 5px rgba(0, 0, 0, 0.7);
        }

        /* Section des fonctionnalités */
        .features {
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
            padding: 50px;
            background-color: #fff;
        }

        .features a {
            text-decoration: none;
        }

        .feature {
            background-color: #f7f9fb;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin: 15px;
            text-align: center;
            flex: 1;
            min-width: 250px;
            max-width: 300px;
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .feature:hover {
            transform: translateY(-10px);
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.2);
        }

        .feature img {
            width: 100%;
            height: auto;
            border-radius: 10px;
        }

        .feature h2 {
            margin-top: 15px;
            font-size: 24px;
            color: #004d99;
        }

        .feature p {
            margin: 15px 0;
            color: #666;
            font-size: 16px;
        }

        .btn-secondary {
            display: inline-block;
            padding: 10px 20px;
            background-color: #004d99;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            font-size: 16px;
            transition: background-color 0.3s;
        }

        .btn-secondary:hover {
            background-color: #003366;
        }

        /* Style pour les alertes */
        .alert {
            background-color: #ffdddd;
            color: #000000;
            padding: 20px;
            margin: 20px 0;
            border: 1px solid #d8000c;
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
      
           <!-- Section des Alertes -->
           <section class="alerts">
            <h2>Alerte de Sortie Prochaine</h2>
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <?php 
                        // Vérifier si la date de sortie est aujourd'hui
                        $is_today = ($row['date_fin'] === $date_actuelle); 
                        // Classe clignotante si la date de sortie est aujourd'hui
                        $alert_class = $is_today ? 'clignotant' : ''; 
                    ?>
                    <div class="alert <?= $alert_class ?>">
                        <strong>Réservation ID : <?= $row['id'] ?></strong><br>
                        Client : <?= $row['nom'] ?> <?= $row['prenom'] ?><br>
                        Date de Sortie : <?= $row['date_fin'] ?><br>
                        Téléphone : <?= $row['telephone'] ?>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>Aucune alerte de sortie prochaine pour aujourd'hui.</p>
            <?php endif; ?>
        </section>

    </main>
</body>
</html>
<?php
$stmt->close();
$con->close();
?>