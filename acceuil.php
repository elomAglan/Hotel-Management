<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Afficher les erreurs PHP
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>


<style>
/* Styles de base pour les grands écrans */
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

/* Hero section */
.hero {
    text-align: center;
    padding: 100px 20px;
    background: url('hotel.jfif') no-repeat center center/cover;
    color: #fff;
}

.hero h1 {
    font-size: 48px;
    margin-bottom: 20px;
    text-shadow: 2px 2px 5px rgba(0, 0, 0, 0.7);
}

.hero p {
    font-size: 22px;
    margin-bottom: 40px;
}

.btn-primary {
    display: inline-block;
    padding: 12px 25px;
    background-color: #ffcc00;
    color: #004d99;
    text-decoration: none;
    border-radius: 5px;
    font-size: 18px;
    font-weight: bold;
    transition: background-color 0.3s;
}

.btn-primary:hover {
    background-color: #e6b800;
}

/* Features section */
.features {
    display: flex;
    justify-content: space-between;
    flex-wrap: wrap;
    padding: 50px;
    background-color: #fff;
}

.features a {
    text-decoration: none;
}

.feature {
    background-color: #f7f9fb;
    border-radius: 10px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    padding: 20px;
    margin: 15px;
    text-align: center;
    flex: 1;
    min-width: 250px;
    max-width: 300px;
    transition: transform 0.3s, box-shadow 0.3s;
}

.feature:hover {
    transform: translateY(-10px);
    box-shadow: 0 6px 15px rgba(0, 0, 0, 0.2);
}

.feature img {
    width: 100%;
    height: auto;
    border-radius: 10px;
}

.feature h2 {
    margin-top: 15px;
    font-size: 24px;
    color: #004d99;
}

.feature p {
    margin: 15px 0;
    color: #666;
    font-size: 16px;
}

.btn-secondary {
    display: inline-block;
    padding: 10px 20px;
    background-color: #004d99;
    color: #fff;
    text-decoration: none;
    border-radius: 5px;
    font-size: 16px;
    transition: background-color 0.3s;
}

.btn-secondary:hover {
    background-color: #003366;
}

/* Footer */
footer {
    background-color: #004d99;
    color: #fff;
    text-align: center;
    padding: 15px 0;
    margin-top: 50px;
    font-size: 14px;
}

/* Styles des icônes du menu */
.menu-icons {
    display: none; /* Masquer par défaut */
}

/* Styles de base pour les grands écrans */
.nav-links {
    display: flex; /* Afficher le menu par défaut sur les grands écrans */
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


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accueil - Hôtel Luxe</title>
    <link rel="stylesheet" href="style.css">
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
    <section class="hero">
        <h1>Bienvenue à Hôtel Luxe</h1>
        <p>Nous offrons une expérience inoubliable avec un service impeccable et des chambres confortables.</p>
        <a href="reservations.php" class="btn-primary">Réserver Maintenant</a>
    </section>
    
    <section class="features">
        <a href="reservations.php#standard">
            <div class="feature">
                <img src="h1.jfif" alt="Chambre Standard">
                <h2>Chambres Standards</h2>
                <p>Chambre confortable avec toutes les commodités nécessaires.</p>
            </div>
        </a>

        <a href="reservations.php#Deluxe">
            <div class="feature">
                <img src="H2.jfif" alt="Chambre Deluxe">
                <h2>Chambres Deluxes</h2>
                <p>Chambre spacieuse avec vue imprenable et luxe supplémentaire.</p>
            </div>
        </a>
        
        <a href="reservations.php#suite">
            <div class="feature">
                <img src="H3.jpg" alt="Suite">
                <h2>Suites</h2>
                <p>Suite élégante avec salon séparé et vue panoramique.</p>
            </div>
        </a>
    </section>
</main>

<footer>
    <p>&copy; 2024 Hôtel Luxe. Tous droits réservés.</p>
</footer>

</body>
</html>
