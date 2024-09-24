<?php

require 'vendor/autoload.php';
use TCPDF;
use Endroid\QrCode\QrCode;

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
if (!is_array($config)) {
    die('La configuration n\'a pas été chargée correctement.');
}

// Vérification de la clé API Stripe
if (empty($config['stripe_key'])) {
    die("La clé API Stripe n'est pas configurée. Veuillez mettre à jour votre fichier config.php.");
}

\Stripe\Stripe::setApiKey($config['stripe_key']);
use Stripe\Checkout\Session;
session_start();

if (!isset($_SESSION['reservation'])) {
    header("Location: reservations.php?message=La session de réservation est invalide. Veuillez refaire votre réservation.&type=error");
    exit();
}

$reservation = $_SESSION['reservation'];
$chambre_id = $reservation['chambre_id'];
$date_arrivee = $reservation['date_arrivee'];
$date_depart = $reservation['date_depart'];
$nom = $reservation['nom'];
$prenom = $reservation['prenom'];
$carte_identite = $reservation['carte_identite'];
$montant_total = $reservation['montant_total'];

// Récupérer le paiement Stripe
$session_id = $_GET['session_id'];
$session = Session::retrieve($session_id);

if ($session->payment_status === 'paid') {
    // Générer un identifiant unique pour la réservation
    $reservation_id = uniqid();

    // Insérer la réservation dans la base de données
    $sql_insert_reservation = "INSERT INTO reservation_utilisateur (id, nom, prenom, carte_identite, date_arrivee, date_depart, chambre_id) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt_insert_reservation = $conn->prepare($sql_insert_reservation);
    $stmt_insert_reservation->bind_param('ssssssi', $reservation_id, $nom, $prenom, $carte_identite, $date_arrivee, $date_depart, $chambre_id);

    if ($stmt_insert_reservation->execute()) {
        // Générer le reçu sécurisé
        generateReceipt($conn, $reservation, $montant_total, $reservation_id);

        // Réinitialiser la session de réservation
        unset($_SESSION['reservation']);

        // Redirection avec message de succès
        header("Location: reservations.php?message=Votre réservation a été confirmée avec succès.&type=success");
    } else {
        die('Erreur lors de l\'enregistrement de la réservation.');
    }
} else {
    die('Le paiement n\'a pas été confirmé.');
}

function generateReceipt($conn, $reservation, $montant_total, $reservation_id) {
    $chambre_id = $reservation['chambre_id'];
    $date_arrivee = $reservation['date_arrivee'];
    $date_depart = $reservation['date_depart'];
    $nom = $reservation['nom'];
    $prenom = $reservation['prenom'];

    // Créer un code QR contenant les informations de la réservation
    $qrCodeData = "ID Réservation: $reservation_id\nNom: $nom $prenom\nChambre ID: $chambre_id\nDate d'arrivée: $date_arrivee\nDate de départ: $date_depart";
    $qrCode = new QrCode($qrCodeData);
    $qrCodeImage = $qrCode->writeString();

    // Créer le PDF
    $pdf = new TCPDF();
    $pdf->AddPage();
    $pdf->SetFont('Helvetica', '', 12);

    $pdf->Cell(0, 10, 'Reçu de Réservation', 0, 1, 'C');
    $pdf->Ln();
    $pdf->Cell(0, 10, 'ID de Réservation: ' . $reservation_id);
    $pdf->Ln();
    $pdf->Cell(0, 10, 'Nom: ' . $nom . ' ' . $prenom);
    $pdf->Ln();
    $pdf->Cell(0, 10, 'Chambre ID: ' . $chambre_id);
    $pdf->Ln();
    $pdf->Cell(0, 10, 'Date d\'arrivée: ' . $date_arrivee);
    $pdf->Ln();
    $pdf->Cell(0, 10, 'Date de départ: ' . $date_depart);
    $pdf->Ln();
    $pdf->Cell(0, 10, 'Montant Total: $' . number_format($montant_total / 100, 2));
    $pdf->Ln();

    // Ajouter le code QR au PDF
    $pdf->Image('@' . $qrCodeImage, 15, 140, 50, 50, 'PNG');

    // Sauvegarder le PDF
    $pdf->Output('recu_reservation_' . $reservation_id . '.pdf', 'D');
}
?>
