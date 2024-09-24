<?php
require 'vendor/autoload.php';
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Afficher les erreurs PHP
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Connexion à la base de données
$host = 'localhost';
$user = 'root';
$password = '';
$dbname = 'hotel_management';

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die("La connexion a échoué : " . $conn->connect_error);
}

// Requête pour récupérer les chambres
$sql = "SELECT * FROM chambres";
$result = $conn->query($sql);

$chambres = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $chambres[] = $row;
    }
}

$conn->close();

// Initialiser les variables de message et de type
$message = '';
$type = '';

if (isset($_GET['message'])) {
    if ($_GET['message'] == 'success') {
        $message = 'Réservation réussie!';
        $type = 'success';
    } elseif ($_GET['message'] == 'error') {
        $message = 'Une erreur est survenue. Veuillez réessayer.';
        $type = 'error';
    } elseif ($_GET['message'] == 'chambre_occupee') {
        $message = 'Désolé, cette chambre est déjà occupée pour les dates sélectionnées. Veuillez choisir une autre date ou une autre chambre.';
        $type = 'error';
    }
}


// Afficher le message s'il existe
 if (!empty($message)): ?>
    <div class="alert alert-<?php echo $type; ?>" style="margin: 20px 0; padding: 15px; border: 1px solid <?php echo $type == 'success' ? 'green' : 'red'; ?>; background-color: <?php echo $type == 'success' ? '#d4edda' : '#f8d7da'; ?>; color: <?php echo $type == 'success' ? '#155724' : '#721c24'; ?>;">
        <?php echo $message; ?>
    </div>
<?php endif; ?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réservations - Hôtel Luxe</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* CSS pour la page de réservation */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f9f9f9;
        }
        header {
            background-color: #004d99;
            color: #fff;
            padding: 20px 0;
        }

        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 50px;
        }

        .navbar .logo {
            font-size: 30px;
            color: #fff;
            text-decoration: none;
        }

        .navbar nav ul {
            list-style: none;
            margin: 0;
            padding: 0;
            display: flex;
        }

        .navbar nav ul li {
            margin-left: 25px;
        }

        .navbar nav ul li a {
            color: #fff;
            text-decoration: none;
            font-size: 18px;
            transition: color 0.3s;
        }

        .navbar nav ul li a:hover {
            color: #ffcc00;
        }

        .entête {
            color: #0056b3;
            text-align: center;
            margin-top: 30px;
        }

        .entête h1 {
            color: #0056b3;
            margin: 0;
        }
        
        /* Hero section */
        .hero {
            text-align: center;
            padding: 100px 20px;
            background: url('hotel.jfif') no-repeat center center/cover;
            color: #fff;
        }

        .hero h1 {
            font-size: 48px;
            margin-bottom: 20px;
            text-shadow: 2px 2px 5px rgba(0, 0, 0, 0.7);
        }

        .hero p {
            font-size: 22px;
            margin-bottom: 40px;
        }

        .btn-primary {
            display: inline-block;
            padding: 12px 25px;
            background-color: #ffcc00;
            color: #004d99;
            text-decoration: none;
            border-radius: 5px;
            font-size: 18px;
            font-weight: bold;
            transition: background-color 0.3s;
        }

        .btn-primary:hover {
            background-color: #e6b800;
        }

        /* Features section */
        .features {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            padding: 50px;
            background-color: #fff;
        }

        .feature {
            background-color: #f7f9fb;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin: 15px;
            text-align: center;
            width: 300px;
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

        /* Style de la modale */
.modal {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    overflow: auto;
}

.modal-content {
    background-color: #fff;
    margin: 15% auto;
    padding: 20px;
    border-radius: 10px;
    width: 50%;
    max-width: 500px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
}

.close {
    color: #aaa;
    float: right;
    font-size: 28px;
    font-weight: bold;
}

.close:hover,
.close:focus {
    color: #000;
    text-decoration: none;
    cursor: pointer;
}

.modal h2 {
    margin-top: 0;
}

.modal form {
    display: flex;
    flex-direction: column;
}

.modal form label {
    margin-bottom: 8px;
}

.modal form input {
    margin-bottom: 15px;
    padding: 10px;
    font-size: 16px;
}

.modal form button {
    background-color: #004d99;
    color: #fff;
    padding: 10px;
    border: none;
    border-radius: 5px;
    font-size: 16px;
    cursor: pointer;
    transition: background-color 0.3s;
}

.modal form button:hover {
    background-color: #003366;
}

.message-panel {
    display: none;
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background-color: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
    padding: 20px;
    border-radius: 5px;
    z-index: 1000;
    width: 400px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    text-align: center;
}

.message-panel.success {
    background-color: #d4edda;
    color: #155724;
    border-color: #c3e6cb;
}

.message-panel button {
    background: transparent;
    border: none;
    font-size: 16px;
    float: right;
    cursor: pointer;
}


        /* Footer */
        footer {
            background-color: #004d99;
            color: #fff;
            text-align: center;
            padding: 15px 0;
            margin-top: 50px;
            font-size: 14px;
        }

        /* Media Queries pour la réactivité */
@media (max-width: 768px) {
    .nav-links {
        display: none; /* Masquer le menu par défaut sur les petits écrans */
        position: absolute;
        top: 60px; /* Ajuster en fonction de la hauteur de votre barre de navigation */
        left: 0;
        width: 100%;
        background-color: #004d99;
        flex-direction: column;
        align-items: center;
    }

    .nav-links.active {
        display: flex; /* Afficher le menu lorsque la classe 'active' est ajoutée */
    }

    .hamburger {
        display: block; /* Afficher l'icône hamburger sur les petits écrans */
    }

    .close-icon {
        display: none; /* Masquer l'icône de fermeture par défaut */
    }

    .nav-links.active ~ .close-icon {
        display: block; /* Afficher l'icône de fermeture lorsque le menu est ouvert */
    }

    .nav-links.active ~ .hamburger {
        display: none; /* Masquer l'icône hamburger lorsque le menu est ouvert */
    }

    .nav-links ul {
        flex-direction: column;
        width: 100%;
        text-align: center;
    }

    .nav-links li {
        margin: 10px 0;
    }
}

/* Masquer les icônes de menu sur les grands écrans */
@media (min-width: 769px) {
    .hamburger, .close-icon {
        display: none;
    }
}

/* Afficher les icônes de menu sur les petits écrans */
@media (max-width: 768px) {
    .hamburger {
        display: block;
    }

    .close-icon {
        display: none; /* Par défaut, le X est masqué jusqu'à ce que le menu soit ouvert */
    }

    .nav-links.active ~ .close-icon {
        display: block; /* Afficher l'icône de fermeture (X) lorsque le menu est ouvert */
    }

    .nav-links.active ~ .hamburger {
        display: none; /* Masquer l'icône hamburger lorsque le menu est ouvert */
    }
}
    </style>
</head>
<body>

 
<header>
    <div class="navbar">
        <a href="acceuil.php" class="logo">Hôtel Luxe</a>
        <div class="hamburger" onclick="toggleMenu()">
            &#9776; <!-- Icône des trois barres -->
        </div>
        <div class="close-icon" onclick="toggleMenu()">
            &times; <!-- Icône de fermeture "X" -->
        </div>
        <nav class="nav-links">
            <ul>
                <li><a href="acceuil.php">Accueil</a></li>
                <li><a href="reservations.php">Réservations</a></li>
                <li><a href="chambres.php">Chambres</a></li>
                <li><a href="contact.php">Contact</a></li>
                <li><a href="logout.php">Déconnexion</a></li>
            </ul>
        </nav>
    </div>
</header>

<script>
    
    function toggleMenu() {
    var navLinks = document.querySelector('.nav-links');
    var hamburgerIcon = document.querySelector('.hamburger');
    var closeIcon = document.querySelector('.close-icon');

    navLinks.classList.toggle('active');

    if (navLinks.classList.contains('active')) {
        hamburgerIcon.style.display = 'none';
        closeIcon.style.display = 'block';
    } else {
        hamburgerIcon.style.display = 'block';
        closeIcon.style.display = 'none';
    }
}


</script>
    
    <main>
        <section class="hero">
            <h1>Réservez Votre Séjour</h1>
            <p>Choisissez parmi nos chambres confortables pour un séjour mémorable.</p>
        </section>

<!-- Modale de réservation -->
<div id="reservationModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2>Réservation</h2>
        <form action="process_reservation.php" method="POST">
            <label for="nom">Nom:</label>
            <input type="text" id="nom" name="nom" required>

            <label for="prenom">Prénom:</label>
            <input type="text" id="prenom" name="prenom" required>

            <label for="carte_identite">N° de carte d'identité:</label>
            <input type="text" id="carte_identite" name="carte_identite" required>

            <label for="date_arrivee">Date d'arrivée:</label>
            <input type="date" id="date_arrivee" name="date_arrivee" required>

            <label for="date_depart">Date de départ:</label>
            <input type="date" id="date_depart" name="date_depart" required>

            <input type="hidden" id="chambre_id" name="chambre_id">
            
            <button type="submit" class="btn-primary">Confirmer la réservation</button>
        </form>
    </div>
</div>




<script>
// Récupérer les éléments de la modale
var modal = document.getElementById("reservationModal");
var closeModal = document.getElementsByClassName("close")[0];

// Fonction pour ouvrir la modale
function openModal(roomId) {
    document.getElementById('chambre_id').value = roomId; // Affecter l'ID de la chambre au formulaire
    modal.style.display = "block";
}

// Fermer la modale lorsque l'utilisateur clique sur "x"
closeModal.onclick = function() {
    modal.style.display = "none";
}

// Fermer la modale lorsqu'on clique en dehors de celle-ci
window.onclick = function(event) {
    if (event.target == modal) {
        modal.style.display = "none";
    }
}

</script>


<!-- Panneau de message -->
<div id="messagePanel" class="message-panel">
        <button onclick="closeMessagePanel()">x</button>
        <span id="messageContent"></span>
    </div>


    <script>
        // Fonction pour afficher le panneau de message
        function showMessage(type, message) {
            var panel = document.getElementById('messagePanel');
            var content = document.getElementById('messageContent');

            content.textContent = message;
            panel.classList.remove('success', 'error'); // Retirer les classes existantes
            panel.classList.add(type); // Ajouter la classe de type
            panel.style.display = 'block'; // Afficher le panneau
        }

        // Fonction pour fermer le panneau de message
        function closeMessagePanel() {
            document.getElementById('messagePanel').style.display = 'none';
        }

        // Fonction pour extraire les paramètres GET de l'URL
        function getQueryParams() {
            var params = {};
            window.location.search.substring(1).split("&").forEach(function(param) {
                var pair = param.split("=");
                params[pair[0]] = decodeURIComponent(pair[1]);
            });
            return params;
        }

        // Vérifier si un message doit être affiché
        window.onload = function() {
            var params = getQueryParams();
            if (params.message && params.type) {
                showMessage(params.type, params.message);
            }
        }
    </script>


        <?php if (!empty($chambres)) : ?>
            <?php
            // Organiser les chambres par type
            $types_chambres = [
                'Standard' => [],
                'Deluxe' => [],
                'Suite' => []
            ];

            foreach ($chambres as $chambre) {
                $types_chambres[$chambre['type_chambre']][] = $chambre;
            }
            ?>

            <?php foreach ($types_chambres as $type => $liste_chambres) : ?>
                <?php if (!empty($liste_chambres)) : ?>
                    <section class="entête">
                        <h1>Les chambres <?php echo $type; ?></h1>
                    </section>
                    <section class="features" id="<?php echo $type; ?>">
                        <?php foreach ($liste_chambres as $chambre) : ?>
                            <div class="feature">
                                <img src="<?php echo $chambre['image_url']; ?>" alt="Chambre <?php echo $chambre['type_chambre']; ?>">
                                <h2><?php echo $chambre['type_chambre']; ?></h2>
                                <p><?php echo $chambre['description']; ?></p>
                                <a href="javascript:void(0);" class="btn-primary" onclick="openModal(<?php echo $chambre['id']; ?>)">Réserver Maintenant</a>
                                </div>
                        <?php endforeach; ?>
                    </section>
                <?php endif; ?>
            <?php endforeach; ?>
        <?php else : ?>
            <p>Aucune chambre disponible pour le moment.</p>
        <?php endif; ?>
    </main>
    
    <footer>
        <p>&copy; 2024 Hôtel Luxe. Tous droits réservés.</p>
    </footer>
</body>
</html>
