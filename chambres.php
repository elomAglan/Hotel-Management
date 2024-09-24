<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nos Chambres - Hôtel Luxe</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* CSS existant */
        body {
            font-family: 'Roboto', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f0f2f5;
            color: #333;
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

        h1 {
            text-align: center;
            margin: 50px 0;
            font-size: 36px;
            color: #004d99;
        }

        .room-list {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .room-item {
            display: flex;
            align-items: center;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
            overflow: hidden;
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .room-item:hover {
            transform: translateY(-10px);
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.2);
        }

        .room-item img {
            width: 40%;
            height: auto;
        }

        .room-details {
            padding: 20px;
            width: 60%;
        }

        .room-details h2 {
            margin-top: 0;
            font-size: 24px;
            color: #004d99;
        }

        .room-details p {
            margin: 10px 0;
            color: #666;
            font-size: 16px;
        }

        .room-details .price {
            font-size: 18px;
            color: #333;
            font-weight: bold;
            margin-bottom: 15px;
        }

        .btn-reserve {
            display: inline-block;
            padding: 10px 20px;
            background-color: #004d99;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            font-size: 16px;
            transition: background-color 0.3s;
        }

        .btn-reserve:hover {
            background-color: #003366;
        }

        .footer {
            background-color: #004d99;
            color: #fff;
            text-align: center;
            padding: 15px 0;
            font-size: 14px;
            margin-top: 50px;
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
        <h1>Nos Chambres</h1>

        <section class="room-list">
            <?php
            // Connexion à la base de données
            $conn = new mysqli('localhost', 'root', '', 'hotel_management');

            // Vérifier la connexion
            if ($conn->connect_error) {
                die("Échec de la connexion : " . $conn->connect_error);
            }

            // Requête pour récupérer les chambres
            $sql = "SELECT numero_chambre, type_chambre, prix, image_url, description FROM chambres";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                // Afficher les chambres
                while($row = $result->fetch_assoc()) {
                    echo '<div class="room-item">';
                    echo '<img src="'.$row['image_url'].'" alt="Chambre '.$row['type_chambre'].'">';
                    echo '<div class="room-details">';
                    echo '<h2>Chambre '.$row['type_chambre'].'</h2>';
                    echo '<p>'.$row['description'].'</p>';
                    echo '<p class="price">Prix : '.$row['prix'].' $ /nuit</p>';
                    echo '</div>';
                    echo '</div>';
                }
            } else {
                echo "Aucune chambre disponible.";
            }

            // Fermer la connexion
            $conn->close();
            ?>
        </section>
    </main>

    <footer class="footer">
        <p>&copy; 2024 Hôtel Luxe. Tous droits réservés.</p>
    </footer>
</body>
</html>
