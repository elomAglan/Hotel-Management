<?php
session_start();

if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $client_id = $_POST['client_id'];
    $reservation_id = $_POST['reservation_id'];
    $montant = $_POST['montant'];
    $mode_paiement = $_POST['mode_paiement'];
    $commentaire = isset($_POST['commentaire']) ? $_POST['commentaire'] : '';

    include_once("db.php");

    // Insertion du paiement dans la base de données
    $insert_query = "INSERT INTO paiements (reservation_id, client_id, montant, mode_paiement, commentaire) VALUES (?, ?, ?, ?, ?)";
    $stmt = $con->prepare($insert_query);
    $stmt->bind_param("iisss", $reservation_id, $client_id, $montant, $mode_paiement, $commentaire);

    if ($stmt->execute()) {
        // Paiement enregistré avec succès

        // Inclure FPDF
        require('fpdf/fpdf.php');

        // Récupérer les détails de la réservation
        $query = "SELECT r.id, r.date_debut, r.date_fin, r.heure_arrivee, c.id AS client_id, c.nom, c.prenom, ch.numero_chambre, ch.type_chambre
                  FROM reservations r
                  JOIN clients c ON r.client_id = c.id
                  JOIN chambres ch ON r.id_chambre = ch.id
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
        $pdf = new FPDF('P', 'mm', array(58, 150)); // Taille du reçu similaire à celui des supermarchés
        $pdf->AddPage();

        // Définir la police pour simuler le style d'impression des reçus
        $pdf->SetFont('Courier', '', 8);

        // Supprimer les caractères spéciaux
        $mode_paiement = str_replace(['é', 'è', 'à', 'ç'], ['e', 'e', 'a', 'c'], $mode_paiement);
        $commentaire = str_replace(['é', 'è', 'à', 'ç'], ['e', 'e', 'a', 'c'], $commentaire);

        // Ajouter un titre centré
        $pdf->Cell(0, 4, '*** Recu de Paiement ***', 0, 1, 'C');
        $pdf->Ln(2);

        // Détails du paiement avec MultiCell pour éviter le débordement
        $pdf->MultiCell(0, 4, 'ID Reservation: ' . $reservation['id']);
        $pdf->MultiCell(0, 4, 'Client: ' . $reservation['prenom'] . ' ' . $reservation['nom']);
        $pdf->MultiCell(0, 4, 'Chambre: ' . $reservation['numero_chambre'] . ' - ' . $reservation['type_chambre']);
        $pdf->MultiCell(0, 4, 'Date de Debut: ' . $reservation['date_debut']);
        $pdf->MultiCell(0, 4, 'Date de Fin: ' . $reservation['date_fin']);
        $pdf->MultiCell(0, 4, 'Heure d\'Arrivee: ' . $reservation['heure_arrivee']);
        $pdf->Ln(2);
        $pdf->MultiCell(0, 4, 'Montant: ' . number_format($montant, 2) . ' CFA');
        $pdf->MultiCell(0, 4, 'Mode de Paiement: ' . $mode_paiement);

        if (!empty($commentaire)) {
            $pdf->Ln(2);
            $pdf->MultiCell(0, 4, 'Commentaire: ' . $commentaire);
        }

        // Remerciements
        $pdf->Ln(6);
        $pdf->Cell(0, 4, 'Merci pour votre visite!', 0, 1, 'C');
        $pdf->Ln(2);
        $pdf->Cell(0, 4, '********************', 0, 1, 'C');

        // Nommer le fichier PDF
        $pdf_filename = 'recu_paiement_' . $reservation['id'] . '.pdf';

        // Télécharger le fichier PDF
        $pdf->Output('D', $pdf_filename);

        exit();
    } else {
        echo "Erreur lors de l'enregistrement du paiement.";
    }
}
?>
