<?php
require 'vendor/autoload.php';

session_start();

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

// Récupérer les données du formulaire
$chambre_id = $_POST['chambre_id'];
$date_arrivee = $_POST['date_arrivee'];
$date_depart = $_POST['date_depart'];
$nom = $_POST['nom'];
$prenom = $_POST['prenom'];
$carte_identite = $_POST['carte_identite'];
$stripeToken = $_POST['stripeToken'];
$paymentStatus = $_POST['payment_status'];

// Vérifiez le statut du paiement
if ($paymentStatus !== 'completed') {
    header("Location: reservations.php?message=Le paiement a échoué&type=error");
    exit();
}

// Préparer et exécuter la requête d'insertion
$sql = "INSERT INTO reservation_utilisateur (nom, prenom, carte_identite, date_arrivee, date_depart, chambre_id) VALUES (?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
if ($stmt === false) {
    die("Erreur de préparation de la requête : " . $conn->error);
}

$stmt->bind_param("sssssi", $nom, $prenom, $carte_identite, $date_arrivee, $date_depart, $chambre_id);

if ($stmt->execute()) {
    // Redirection avec un message de succès si la réservation a réussi
    header("Location: reservations.php?message=Chambre réservée avec succès&type=success");
} else {
    // Redirection avec un message d'erreur en cas de problème d'insertion
    header("Location: reservations.php?message=Erreur lors de la réservation&type=error");
}

$stmt->close();
$conn->close();
exit();
?>
