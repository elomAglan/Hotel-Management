<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contactez-nous - Hôtel Luxe</title>
    <link rel="stylesheet" href="style.css">
    <style>
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

        .contact-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .contact-info {
            margin-bottom: 40px;
            text-align: center;
        }

        .contact-info h2 {
            font-size: 28px;
            color: #004d99;
            margin-bottom: 15px;
        }

        .contact-info p {
            font-size: 16px;
            color: #666;
            margin: 5px 0;
        }

        .social-links {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-top: 30px;
        }

        .social-links a {
            text-decoration: none;
            color: #fff;
            padding: 15px;
            border-radius: 50%;
            font-size: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            transition: background-color 0.3s ease;
        }

        .social-links a.whatsapp {
            background-color: #25D366;
        }

        .social-links a.facebook {
            background-color: #4267B2;
        }

        .social-links a.instagram {
            background-color: #E4405F;
        }

        .social-links a:hover {
            opacity: 0.8;
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
    <h1>Contactez-nous</h1>

    <div class="contact-container">
        <div class="contact-info">
            <h2>Informations de Contact</h2>
            <p><strong>Adresse :</strong> 123 Rue de l'Hôtel, Ville, Pays</p>
            <p><strong>Téléphone :</strong> +228 123 456 789</p>
            <p><strong>Email :</strong> contact@hotelluxe.com</p>
            <p><strong>Maps :</strong> <a href="https://www.google.com/maps?q=569H+VC Lomé" target="_blank">lien de notre position</a></p>
        </div>

        <div class="social-links">
            <a href="https://wa.me/1234567890" class="whatsapp" target="_blank">WhatsApp</a>
            <a href="https://www.facebook.com/hotelluxe" class="facebook" target="_blank">Facebook</a>
            <a href="https://www.instagram.com/hotelluxe" class="instagram" target="_blank">Instagram</a>
        </div>
    </div>
</main>

<footer class="footer">
    <p>&copy; 2024 Hôtel Luxe. Tous droits réservés.</p>
</footer>
</body>
</html>
