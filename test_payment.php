<?php

require 'vendor/autoload.php';

// Charger la configuration
$config = include('config.php');

// Vérifier la clé API
if (empty($config['stripe_key'])) {
    die("La clé API Stripe n'est pas configurée. Veuillez mettre à jour votre fichier config.php.");
}

// Initialiser Stripe
\Stripe\Stripe::setApiKey($config['stripe_key']);

try {
    // Exemple de création d'un paiement
    $paymentIntent = \Stripe\PaymentIntent::create([
        'amount' => 1099, // Montant en cents (par exemple, 10.99 USD)
        'currency' => 'usd',
        'payment_method' => 'pm_card_visa', // Remplacez avec un identifiant de méthode de paiement valide pour les tests
        'confirmation_method' => 'manual',
        'confirm' => true,
    ]);

    echo "Paiement créé avec succès. ID du paiement : " . $paymentIntent->id;
} catch (\Stripe\Exception\ApiErrorException $e) {
    echo 'Erreur : ' . $e->getMessage();
}
