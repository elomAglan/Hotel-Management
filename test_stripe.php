<?php
require 'vendor/autoload.php'; // Inclure l'autoload de Composer

if (class_exists('Stripe\Stripe')) {
    echo "Classe Stripe chargée avec succès.";
} else {
    echo "Classe Stripe non trouvée.";
}
