<?php
// Inclure la connexion à la base de données
include('db.php');

// Récupérer l'ID du reçu
$recu_id = $_GET['recu_id'];

// Récupérer les informations du reçu depuis la base de données
$sql = "SELECT rdp.*, c.nom AS client_nom, c.prenom AS client_prenom, u.username AS caissier_nom 
        FROM reçu_de_paiement rdp 
        JOIN clients c ON rdp.client_id = c.id 
        JOIN utilisateurs u ON rdp.caissier_id = u.id 
        WHERE rdp.id = '$recu_id'";
$result = mysqli_query($conn, $sql);
$recu = mysqli_fetch_assoc($result);

// Afficher les informations du reçu (ou utiliser une bibliothèque comme FPDF pour générer un PDF)
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Reçu de paiement</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .recu { width: 500px; margin: 0 auto; border: 1px solid #000; padding: 20px; }
        .recu h1 { text-align: center; }
        .recu p { margin: 10px 0; }
    </style>
</head>
<body>
    <div class="recu">
        <h1>Reçu de paiement</h1>
        <p><strong>Client:</strong> <?php echo $recu['client_nom'] . ' ' . $recu['client_prenom']; ?></p>
        <p><strong>Date de paiement:</strong> <?php echo $recu['date_paiement']; ?></p>
        <p><strong>Montant:</strong> <?php echo number_format($recu['montant'], 2); ?> €</p>
        <p><strong>Mode de paiement:</strong> <?php echo $recu['mode_paiement']; ?></p>
        <p><strong>Caissier:</strong> <?php echo $recu['caissier_nom']; ?></p>
        <?php if ($recu['commentaire']) { ?>
            <p><strong>Commentaire:</strong> <?php echo $recu['commentaire']; ?></p>
        <?php } ?>
    </div>
</body>
</html>

<?php
// Fermer la connexion
mysqli_close($conn);
?>
