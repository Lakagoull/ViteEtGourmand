<?php
require_once 'phpmailer/Exception.php';
require_once 'phpmailer/PHPMailer.php';
require_once 'phpmailer/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

/**
 * MOTEUR D'ENVOI (Fonction de base utilisée par toutes les autres)
 */
function envoyerEmail(string $destinataire, string $sujet, string $contenuHtml) {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = SMTP_HOST;
        $mail->SMTPAuth   = true;
        $mail->Username   = SMTP_USER;
        $mail->Password   = SMTP_PASS;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; 
        $mail->Port       = SMTP_PORT;
        $mail->CharSet    = 'UTF-8'; 

        $mail->setFrom(SMTP_USER, 'Vite & Gourmand');
        $mail->addAddress($destinataire);
        $mail->isHTML(true);
        $mail->Subject = $sujet;
        $mail->Body    = $contenuHtml;

        return $mail->send();
    } catch (Exception $e) {
        error_log("Erreur PHPMailer : " . $mail->ErrorInfo);
        return false;
    }
}

/**
 * 1. MAIL DE BIENVENUE
 */
function mailBienvenue($email, $prenom) {
    $sujet = "Bienvenue chez Vite & Gourmand !";
    $contenu = "<div style='font-family:Arial;'><h2>Bonjour " . htmlspecialchars($prenom) . ",</h2><p>Merci de rejoindre la communauté Vite & Gourmand !</p></div>";
    return envoyerEmail($email, $sujet, $contenu);
}

/**
 * 2. MAIL DE CONFIRMATION COMMANDE
 */
function mailConfirmationCommande($email, $contenuHtml) {
    return envoyerEmail($email, "Confirmation de votre réservation - Vite & Gourmand", $contenuHtml);
}

/**
 * 3. MAIL NOTIFICATION MATÉRIEL (LOGISTIQUE)
 */
function mailNotificationMateriel($reservation_id, $titre_menu, $date_evenement, $heure_livraison) {
    $sujet = "NOUVELLE COMMANDE - Préparation Matériel (Réf: $reservation_id)";
    $contenu = "<h2>Nouvelle commande (Réf: $reservation_id)</h2><p>Menu : $titre_menu<br>Date : $date_evenement à $heure_livraison</p>";
    return envoyerEmail(SMTP_USER, $sujet, $contenu);
}

/**
 * 4. MAIL MOT DE PASSE OUBLIÉ
 */
function mailMotDePasseOublie($email, $token) {
    $sujet = "Réinitialisation de votre mot de passe";
    $lien = "http://viteetgourmand-traiteur.infinityfreeapp.com/reinitialiser-mdp.php?token=" . $token;
    $contenu = "
    <div style='font-family:Arial;'>
        <h2>Réinitialisation demandée</h2>
        <p>Cliquez sur le lien ci-dessous pour créer un nouveau mot de passe :</p>
        <p><a href='$lien' style='padding:10px; background:#d9534f; color:#fff; text-decoration:none;'>Réinitialiser mon mot de passe</a></p>
        <p>Ce lien est valide pour une durée limitée.</p>
    </div>";
    return envoyerEmail($email, $sujet, $contenu);
}