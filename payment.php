<?php

require 'vendor/autoload.php';

// Connexion à la base de données
$host = 'localhost';
$user = 'root';
$password = '';
$dbname = 'hotel_management';

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die("La connexion a échoué : " . $conn->connect_error);
}

// Charger la configuration
$config = include('config.php');

// Vérification de la configuration
if (!is_array($config)) {
    die('La configuration n\'a pas été chargée correctement.');
}

// Vérification de la clé API Stripe
if (empty($config['stripe_key'])) {
    die("La clé API Stripe n'est pas configurée. Veuillez mettre à jour votre fichier config.php.");
}

// Initialisation de Stripe
\Stripe\Stripe::setApiKey($config['stripe_key']);

use Stripe\Checkout\Session;

session_start();

// Vérification de la session de réservation
if (!isset($_SESSION['reservation'])) {
    header("Location: reservations.php");
    exit();
}

// Récupérer les détails de la réservation
$reservation = $_SESSION['reservation'];
$chambre_id = $reservation['chambre_id'];
$date_arrivee = $reservation['date_arrivee'];
$date_depart = $reservation['date_depart'];

// Vérification de la disponibilité de la chambre dans la table reservations
$sql_check_reservations = "SELECT * FROM reservations WHERE id_chambre = ? AND ((date_debut <= ? AND date_fin >= ?) OR (date_debut <= ? AND date_fin >= ?))";
$stmt_check_reservations = $conn->prepare($sql_check_reservations);
$stmt_check_reservations->bind_param('issss', $chambre_id, $date_depart, $date_arrivee, $date_arrivee, $date_depart);
$stmt_check_reservations->execute();
$result_check_reservations = $stmt_check_reservations->get_result();

if ($result_check_reservations->num_rows > 0) {
    // Chambre non disponible
    header("Location: reservations.php?message=" . urlencode("Désolé, cette chambre est déjà réservée pour les dates sélectionnées.") . "&type=error");
    exit();
}

// Vérifier la disponibilité dans reservation_utilisateur
$sql_check_reservation_utilisateur = "SELECT * FROM reservation_utilisateur WHERE chambre_id = ? AND ((date_arrivee <= ? AND date_depart >= ?) OR (date_arrivee <= ? AND date_depart >= ?))";
$stmt_check_reservation_utilisateur = $conn->prepare($sql_check_reservation_utilisateur);
$stmt_check_reservation_utilisateur->bind_param('issss', $chambre_id, $date_depart, $date_arrivee, $date_arrivee, $date_depart);
$stmt_check_reservation_utilisateur->execute();
$result_check_reservation_utilisateur = $stmt_check_reservation_utilisateur->get_result();

if ($result_check_reservation_utilisateur->num_rows > 0) {
    // Chambre non disponible
    header("Location: reservations.php?message=" . urlencode("Désolé, cette chambre est déjà réservée pour les dates sélectionnées.") . "&type=error");
    exit();
}

// Récupérer le prix de la chambre
$sql_get_price = "SELECT prix FROM chambres WHERE id = ?";
$stmt_get_price = $conn->prepare($sql_get_price);
$stmt_get_price->bind_param('i', $chambre_id);
$stmt_get_price->execute();
$result_get_price = $stmt_get_price->get_result();

if ($result_get_price->num_rows === 0) {
    die("La chambre demandée n'existe pas.");
}

$chambre = $result_get_price->fetch_assoc();
$prix_par_nuit = $chambre['prix'];

// Calcul du montant total
$date1 = new DateTime($date_arrivee);
$date2 = new DateTime($date_depart);
$interval = $date1->diff($date2);
$nombre_jours = $interval->days;
$montant_total = $prix_par_nuit * $nombre_jours;

// Créer une session de paiement
try {
    $session = Session::create([
        'payment_method_types' => ['card'],
        'line_items' => [
            [
                'price_data' => [
                    'currency' => 'usd',
                    'product_data' => [
                        'name' => 'Réservation Chambre',
                    ],
                    'unit_amount' => $montant_total * 100, // Montant en cents (par exemple, 10.00 USD)
                ],
                'quantity' => 1,
            ],
        ],
        'mode' => 'payment',
        'success_url' => 'http://yourdomain.com/success.php',
        'cancel_url' => 'http://yourdomain.com/cancel.php',
    ]);

    // Redirection vers la page de paiement Stripe
    header('Location: ' . $session->url);
    exit();
} catch (Exception $e) {
    // Gérer les erreurs
    die('Erreur lors de la création de la session de paiement : ' . $e->getMessage());
}

?>
