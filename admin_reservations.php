<?php
require 'vendor/autoload.php';
session_start();

if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

include_once("db.php");

// Initialiser le message à vide
if (!isset($_SESSION['message'])) {
    $_SESSION['message'] = "";
}

// Ajouter une nouvelle réservation
if (isset($_POST['submit'])) {
    $client_id = $_POST['client_id'];
    $chambre_id = $_POST['chambre_id'];
    $date_debut = $_POST['date_debut'];
    $date_fin = $_POST['date_fin'];
    $heure_arrivee = $_POST['heure_arrivee'];

    // Vérifier si la chambre est déjà réservée pour la période spécifiée
    $check_query_reservations = "
        SELECT * FROM reservations 
        WHERE id_chambre = ? 
        AND ((date_debut <= ? AND date_fin >= ?) 
        OR (date_debut <= ? AND date_fin >= ?))
    ";
    $stmt_reservations = $con->prepare($check_query_reservations);
    $stmt_reservations->bind_param("issss", $chambre_id, $date_fin, $date_debut, $date_debut, $date_fin);
    $stmt_reservations->execute();
    $result_reservations = $stmt_reservations->get_result();

    // Si la chambre est réservée, afficher un message d'erreur
    if ($result_reservations->num_rows > 0) {
        $_SESSION['message'] = "La chambre est déjà réservée pour cette période.";
    } else {
        // Insérer la nouvelle réservation
        $insert_query = "INSERT INTO reservations (client_id, id_chambre, date_debut, date_fin, heure_arrivee) VALUES (?, ?, ?, ?, ?)";
        $stmt = $con->prepare($insert_query);
        $stmt->bind_param("iisss", $client_id, $chambre_id, $date_debut, $date_fin, $heure_arrivee);

        if ($stmt->execute()) {
            $_SESSION['message'] = "Réservation ajoutée avec succès.";
        } else {
            $_SESSION['message'] = "Erreur lors de l'ajout de la réservation.";
        }
    }
    header("Location: admin_reservations.php");
    exit();
}

// Supprimer une réservation
if (isset($_GET['delete'])) {
    $reservation_id = $_GET['delete'];
    
    $delete_query = "DELETE FROM reservations WHERE id = ?";
    $stmt = $con->prepare($delete_query);
    $stmt->bind_param("i", $reservation_id);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Réservation supprimée avec succès.";
    } else {
        $_SESSION['message'] = "Erreur lors de la suppression de la réservation.";
    }
    header("Location: admin_reservations.php");
    exit();
}

// Récupérer les clients et les chambres
$clients = $con->query("SELECT id FROM clients");
$chambres = $con->query("SELECT id, numero_chambre, type_chambre, prix FROM chambres");

// Récupérer la liste des réservations
$query = "
    SELECT reservations.id AS reservation_id, reservations.heure_arrivee, clients.id AS client_id, chambres.numero_chambre, chambres.type_chambre, reservations.date_debut, reservations.date_fin
    FROM reservations
    INNER JOIN clients ON reservations.client_id = clients.id
    INNER JOIN chambres ON reservations.id_chambre = chambres.id
";
$req = $con->query($query);

// Récupérer les réservations des utilisateurs
$query_utilisateurs = "
    SELECT reservation_utilisateur.id, reservation_utilisateur.nom, reservation_utilisateur.prenom, reservation_utilisateur.carte_identite, reservation_utilisateur.date_arrivee, reservation_utilisateur.date_depart, reservation_utilisateur.chambre_id
    FROM reservation_utilisateur
";
$req_utilisateurs = $con->query($query_utilisateurs);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Réservations - Hôtel Luxe</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<style>

body {
            font-family: 'Roboto', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f0f2f5;
            color: #333;
        }

        .navbar {
            width: 250px;
            background-color: #004d99;
            color: #fff;
            height: 100vh;
            display: flex;
            flex-direction: column;
            padding: 20px;
            position: fixed;
            top: 0;
            left: 0;
        }

        .navbar .logo {
            font-size: 28px;
            color: #fff;
            text-decoration: none;
            margin-bottom: 30px;
        }

        .navbar nav ul {
            list-style: none;
            margin: 0;
            padding: 0;
            width: 100%;
        }

        .navbar nav ul li {
            margin-bottom: 15px;
        }

        .navbar nav ul li a {
            color: #fff;
            text-decoration: none;
            font-size: 18px;
            padding: 10px;
            display: block;
            transition: background-color 0.3s;
            border-radius: 5px;
        }

        .navbar nav ul li a:hover {
            background-color: #003366;
        }

        /* Contenu principal */
.main-content {
    margin-left: 270px; /* Ajuster la marge gauche pour laisser de l'espace pour la barre de navigation */
    padding: 40px;
    width: calc(100% - 270px); /* Ajuster la largeur du contenu principal */
    box-sizing: border-box; /* Inclure le padding dans la largeur totale */
}


        h1 {
            font-size: 28px;
            margin-bottom: 20px;
        }

        table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 20px;
}

table th, table td {
    padding: 12px 15px;
    border: 1px solid #ddd;
    text-align: left;
}

table th {
    background-color: #004d99;
    color: #fff;
}

table tr:nth-child(even) {
    background-color: #f7f9fb;
}
        .btn-primary {
    display: inline-block;
    padding: 10px 20px;
    background-color: #004d99;
    color: #fff;
    text-decoration: none;
    border-radius: 5px;
    transition: background-color 0.3s;
}

        .btn-primary:hover {
    background-color: #003366;
}

.btn-danger {
    background-color: #e74c3c;
    color: #fff;
    padding: 8px 12px;
    text-decoration: none;
    border-radius: 5px;
    transition: background-color 0.3s;
}

.btn-danger:hover {
    background-color: #c0392b;
}

.form-group {
    margin-bottom: 15px;
}


.form-group label {
    display: block;
    margin-bottom: 5px;
    font-weight: bold;
}

.form-group input {
    width: 100%;
    padding: 10px;
    box-sizing: border-box;
}

.form-group select {
    width: 100%;
    padding: 10px;
    font-size: 16px;
    color: #333;
    background-color: #f9f9f9;
    border: 1px solid #ccc;
    border-radius: 5px;
    box-sizing: border-box;
    transition: border-color 0.3s ease, box-shadow 0.3s ease;
}

.form-group select:focus {
    border-color: #004d99;
    box-shadow: 0 0 5px rgba(0, 77, 153, 0.3);
    outline: none;
}

.form-group option {
    padding: 10px;
    font-size: 16px;
    color: #333;
}

.form-group select option:disabled {
    color: #999;
    background-color: #f0f0f0;
}

        .message {
    margin-bottom: 20px;
    color: #e74c3c;
    font-weight: bold;
}

/* Style général pour le formulaire */
form {
    width: 100%;
    max-width: 500px;
    margin: 0 auto;
    padding: 20px;
    border: 1px solid #ddd;
    border-radius: 8px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    background-color: #f9f9f9;
}

/* Style pour les titres */
h2 {
    font-family: Arial, sans-serif;
    color: #333;
    margin-bottom: 20px;
}

/* Style pour les labels */
label {
    display: block;
    font-weight: bold;
    margin: 10px 0 5px;
    color: #333;
}

/* Style pour les champs de texte et les sélecteurs */
input[type="number"],
select,
textarea {
    width: 100%;
    padding: 8px;
    margin: 5px 0 15px;
    border: 1px solid #ccc;
    border-radius: 4px;
    font-size: 16px;
}

/* Style pour les zones de texte */
textarea {
    resize: vertical;
    min-height: 100px;
}

/* Style pour les boutons */
button {
    width: 100%;
    padding: 10px;
    font-size: 16px;
    color: #fff;
    background-color: #007bff;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

button:hover {
    background-color: #0056b3;
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

.table-container {
    margin-top: 60px; /* Ajuste la valeur selon la hauteur de ta barre de navigation */
}
</style>
<body>
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

    <div class="main-content">
        <h1>Gestion des Réservations</h1>

        <!-- Formulaire pour ajouter une réservation -->
        <form method="POST" action="">
            <div class="form-group">
                <label for="client_id">ID Client:</label>
                <select id="client_id" name="client_id" required>
                    <option value="">Sélectionnez un ID de client</option>
                    <?php while ($client = $clients->fetch_assoc()) : ?>
                        <option value="<?php echo $client['id']; ?>"><?php echo $client['id']; ?></option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="chambre_id">Chambre:</label>
                <select id="chambre_id" name="chambre_id" required>
                    <option value="">Sélectionnez une chambre</option>
                    <?php while ($chambre = $chambres->fetch_assoc()) : ?>
                        <option value="<?php echo $chambre['id']; ?>">
                            <?php echo "Chambre " . $chambre['numero_chambre'] . " - " . $chambre['type_chambre']; ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="date_debut">Date de Début:</label>
                <input type="date" id="date_debut" name="date_debut" required>
            </div>

            <div class="form-group">
                <label for="date_fin">Date de Fin:</label>
                <input type="date" id="date_fin" name="date_fin" required>
            </div>

            <div class="form-group">
                <label for="heure_arrivee">Heure d'Arrivée:</label>
                <input type="time" id="heure_arrivee" name="heure_arrivee" required>
            </div>

            <button type="submit" name="submit" class="btn-primary">Ajouter la réservation</button>
        </form>

        <?php if (!empty($_SESSION['message'])) : ?>
        <p class="message"><?php echo $_SESSION['message']; ?></p>
        <?php $_SESSION['message'] = ""; // Effacer le message après l'affichage ?>
        <?php endif; ?>


        <?php
        if (isset($_GET['generate_receipt'])) {
            $reservation_id = $_GET['generate_receipt'];
            
            // Récupérer les détails de la réservation et le prix de la chambre
            $query = "SELECT reservations.date_debut, reservations.date_fin, chambres.prix
                      FROM reservations
                      INNER JOIN chambres ON reservations.id_chambre = chambres.id
                      WHERE reservations.id = ?";
            $stmt = $con->prepare($query);
            $stmt->bind_param("i", $reservation_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $reservation = $result->fetch_assoc();

            if ($reservation) {
                $date_debut = new DateTime($reservation['date_debut']);
                $date_fin = new DateTime($reservation['date_fin']);
                $interval = $date_debut->diff($date_fin);
                $nombre_jours = $interval->days;
                $prix_chambre = $reservation['prix'];
                $montant = $nombre_jours * $prix_chambre;
            }
        ?>
        <h2>Enregistrer un paiement pour la réservation ID: <?php echo $reservation_id; ?></h2>
        <form action="enregistrer_paiement.php" method="POST">
            <input type="hidden" name="client_id" value="<?php echo $_GET['client_id']; ?>" />
            <input type="hidden" name="reservation_id" value="<?php echo $reservation_id; ?>" />
            
            <label for="montant">Montant:</label>
            <input type="number" id="montant" name="montant" step="0.01" value="<?php echo $montant; ?>" required>

            <label for="mode_paiement">Mode de paiement:</label>
            <select id="mode_paiement" name="mode_paiement" required>
                <option value="carte_credit">Carte de Crédit</option>
                <option value="cash">cash</option>
                <option value="cheque">cheque</option>
                <option value="virement_bancaire">Virement Bancaire</option>
            </select>

            <button type="submit">Enregistrer le paiement et générer un reçu</button>
        </form>
        <?php } ?>

        <!-- Liste des réservations -->
        <h2>Réservations</h2>
        <table>
            <thead>
                <tr>
                    <th>ID Réservation</th>
                    <th>ID Client</th>
                    <th>Numéro de Chambre</th>
                    <th>Type de Chambre</th>
                    <th>Date de Début</th>
                    <th>Date de Fin</th>
                    <th>Heure d'Arrivée</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $req->fetch_assoc()) : ?>
                <tr>
                    <td><?php echo $row['reservation_id']; ?></td>
                    <td><?php echo $row['client_id']; ?></td>
                    <td><?php echo $row['numero_chambre']; ?></td>
                    <td><?php echo $row['type_chambre']; ?></td>
                    <td><?php echo $row['date_debut']; ?></td>
                    <td><?php echo $row['date_fin']; ?></td>
                    <td><?php echo $row['heure_arrivee']; ?></td>
                    <td>
                        <a href="?delete=<?php echo $row['reservation_id']; ?>" class="btn-delete">Supprimer</a>
                        <a href="?generate_receipt=<?php echo $row['reservation_id']; ?>&client_id=<?php echo $row['client_id']; ?>" class="btn-receipt">Générer le reçu</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <!-- Liste des réservations des utilisateurs -->
        <h2>Réservations des Utilisateurs</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nom</th>
                    <th>Prénom</th>
                    <th>Carte d'Identité</th>
                    <th>Date d'Arrivée</th>
                    <th>Date de Départ</th>
                    <th>ID Chambre</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row_utilisateur = $req_utilisateurs->fetch_assoc()) : ?>
                <tr>
                    <td><?php echo $row_utilisateur['id']; ?></td>
                    <td><?php echo $row_utilisateur['nom']; ?></td>
                    <td><?php echo $row_utilisateur['prenom']; ?></td>
                    <td><?php echo $row_utilisateur['carte_identite']; ?></td>
                    <td><?php echo $row_utilisateur['date_arrivee']; ?></td>
                    <td><?php echo $row_utilisateur['date_depart']; ?></td>
                    <td><?php echo $row_utilisateur['chambre_id']; ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
