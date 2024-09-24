<?php
// Vérifiez si le formulaire a été soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupérez les données du formulaire et échappez les caractères spéciaux pour éviter les injections
    $nom = htmlspecialchars(trim($_POST['nom']));
    $email = htmlspecialchars(trim($_POST['email']));
    $message = htmlspecialchars(trim($_POST['message']));

    // Valider les champs
    if (empty($nom) || empty($email) || empty($message)) {
        echo "Tous les champs sont obligatoires.";
        exit();
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "L'adresse email n'est pas valide.";
        exit();
    }

    // Définissez les paramètres de l'email
    $to = 'contact@hotelluxe.com'; // L'adresse email de destination
    $subject = 'Nouveau message de contact';
    $headers = "From: $email\r\n";
    $headers .= "Reply-To: $email\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";

    // Contenu de l'email
    $email_content = "<html><body>";
    $email_content .= "<h2>Message de Contact</h2>";
    $email_content .= "<p><strong>Nom:</strong> $nom</p>";
    $email_content .= "<p><strong>Email:</strong> $email</p>";
    $email_content .= "<p><strong>Message:</strong></p>";
    $email_content .= "<p>$message</p>";
    $email_content .= "</body></html>";

    // Essayez d'envoyer l'email
    if (mail($to, $subject, $email_content, $headers)) {
        // Redirige vers une page de confirmation si l'email est envoyé avec succès
        header('Location: contact_success.php');
        exit();
    } else {
        // Affiche un message d'erreur si l'email n'est pas envoyé
        echo "Une erreur s'est produite lors de l'envoi du message. Veuillez réessayer.";
    }
} else {
    // Redirige vers la page de contact si la méthode de requête n'est pas POST
    header('Location: contact.php');
    exit();
}
