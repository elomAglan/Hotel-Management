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

// Récupération des données du formulaire
$nom = $_POST['nom'];
$prenom = $_POST['prenom'];
$carte_identite = $_POST['carte_identite'];
$date_arrivee = $_POST['date_arrivee'];
$date_depart = $_POST['date_depart'];
$chambre_id = $_POST['chambre_id'];

// Vérifier la disponibilité de la chambre dans la table `reservations`
$sql_check_reservations = "SELECT * FROM reservations WHERE id_chambre = ? AND ((date_debut <= ? AND date_fin >= ?) OR (date_debut <= ? AND date_fin >= ?))";
$stmt_check_reservations = $conn->prepare($sql_check_reservations);
$stmt_check_reservations->bind_param('issss', $chambre_id, $date_depart, $date_arrivee, $date_arrivee, $date_depart);
$stmt_check_reservations->execute();
$result_check_reservations = $stmt_check_reservations->get_result();

// Vérifier la disponibilité de la chambre dans la table `reservation_utilisateur`
$sql_check_reservation_utilisateur = "SELECT * FROM reservation_utilisateur WHERE chambre_id = ? AND ((date_arrivee <= ? AND date_depart >= ?) OR (date_arrivee <= ? AND date_depart >= ?))";
$stmt_check_reservation_utilisateur = $conn->prepare($sql_check_reservation_utilisateur);
$stmt_check_reservation_utilisateur->bind_param('issss', $chambre_id, $date_depart, $date_arrivee, $date_arrivee, $date_depart);
$stmt_check_reservation_utilisateur->execute();
$result_check_reservation_utilisateur = $stmt_check_reservation_utilisateur->get_result();

// Si la chambre est trouvée dans l'une des deux requêtes, elle est occupée
if ($result_check_reservations->num_rows > 0 || $result_check_reservation_utilisateur->num_rows > 0) {
    // Chambre non disponible
    header("Location: reservations.php?message=chambre_occupee&type=error");
    exit();
}

// Chambre disponible, rediriger vers la page de paiement
$_SESSION['reservation'] = [
    'nom' => $nom,
    'prenom' => $prenom,
    'carte_identite' => $carte_identite,
    'date_arrivee' => $date_arrivee,
    'date_depart' => $date_depart,
    'chambre_id' => $chambre_id
];

header("Location: payment.php");
exit();
?>
