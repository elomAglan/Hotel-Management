<?php
require('fpdf/fpdf.php'); // Inclure FPDF

// Connexion à la base de données
$con = new mysqli("localhost", "username", "password", "hotel_management");
if ($con->connect_error) {
    die("Échec de la connexion : " . $con->connect_error);
}

// Récupérer l'ID de réservation à partir de l'URL
if (!isset($_GET['reservation_id']) || empty($_GET['reservation_id'])) {
    die("ID de réservation manquant.");
}

$reservation_id = intval($_GET['reservation_id']);

// Récupérer les détails de la réservation
$query = "SELECT r.id, r.date_debut, r.date_fin, r.heure_arrivee, c.id AS client_id, c.nom, c.prenom, ch.numero_chambre, ch.type_chambre
          FROM reservations r
          JOIN clients c ON r.client_id = c.id
          JOIN chambres ch ON r.chambre_id = ch.id
          WHERE r.id = ?";
$stmt = $con->prepare($query);
$stmt->bind_param("i", $reservation_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Réservation non trouvée.");
}

$reservation = $result->fetch_assoc();

// Créer une instance de FPDF
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 16);

// Ajouter un titre
$pdf->Cell(0, 10, 'Reçu de Réservation', 0, 1, 'C');

// Ajouter les détails de la réservation
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(0, 10, 'ID Réservation: ' . $reservation['id'], 0, 1);
$pdf->Cell(0, 10, 'Client: ' . $reservation['prenom'] . ' ' . $reservation['nom'], 0, 1);
$pdf->Cell(0, 10, 'Chambre: ' . $reservation['numero_chambre'] . ' - ' . $reservation['type_chambre'], 0, 1);
$pdf->Cell(0, 10, 'Date de Début: ' . $reservation['date_debut'], 0, 1);
$pdf->Cell(0, 10, 'Date de Fin: ' . $reservation['date_fin'], 0, 1);
$pdf->Cell(0, 10, 'Heure d\'Arrivée: ' . $reservation['heure_arrivee'], 0, 1);

// Nommer le fichier PDF
$pdf_filename = 'recu_reservation_' . $reservation['id'] . '.pdf';

// Télécharger le fichier PDF
$pdf->Output($pdf_filename, 'D');

// Fermer la connexion
$con->close();
?>
