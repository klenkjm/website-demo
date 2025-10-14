<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// PHPMailer laden
require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';

// === KONFIGURATION ===
$adminEmail = 'kontakt@familie-beraten.de';
$adminName  = 'Familienberatung Katharina Klenk';

$mail = new PHPMailer(true);

try {
    // SMTP-Einstellungen
    $mail->isSMTP();
    $mail->Host       = 'smtp.netcup.net';
    $mail->SMTPAuth   = true;
    $mail->Username   = $adminEmail;
    $mail->Password   = 'DEIN_PASSWORT'; // besser: App-Passwort verwenden
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;

    // Eingabedaten aus dem Formular
    $name    = htmlspecialchars($_POST['name']);
    $email   = htmlspecialchars($_POST['email']);
    $subject = htmlspecialchars($_POST['subject']);
    $message = nl2br(htmlspecialchars($_POST['message']));

    // ===============================
    // 1️⃣ E-Mail an dich (Admin)
    // ===============================
    $mail->setFrom($adminEmail, 'Kontaktformular Familie-beraten.de');
    $mail->addAddress($adminEmail, $adminName);
    $mail->addReplyTo($email, $name);

    $mail->isHTML(true);
    $mail->Subject = 'Neue Nachricht über das Kontaktformular';
    $mail->Body    = "
        <h3>Neue Nachricht von deiner Website</h3>
        <p><strong>Name:</strong> {$name}</p>
        <p><strong>E-Mail:</strong> {$email}</p>
        <p><strong>Betreff:</strong> {$subject}</p>
        <p><strong>Nachricht:</strong><br>{$message}</p>
    ";
    $mail->AltBody = "Name: $name\nE-Mail: $email\nBetreff: $subject\nNachricht:\n" . strip_tags($message);
    
    $mail->send();

    // ===============================
    // 2️⃣ Automatische Bestätigung an den Absender
    // ===============================
    $confirm = new PHPMailer(true);
    $confirm->isSMTP();
    $confirm->Host       = 'smtp.netcup.net';
    $confirm->SMTPAuth   = true;
    $confirm->Username   = $adminEmail;
    $confirm->Password   = 'DEIN_PASSWORT'; // gleiches Passwort wie oben
    $confirm->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $confirm->Port       = 587;

    $confirm->setFrom($adminEmail, 'Familienberatung Katharina Klenk');
    $confirm->addAddress($email, $name);
    $confirm->isHTML(true);
    $confirm->Subject = 'Ihre Nachricht an Familienberatung Katharina Klenk';
    $confirm->Body = "
        <p>Liebe/r {$name},</p>
        <p>vielen Dank für Ihre Nachricht. Ich habe Ihre Anfrage erhalten und melde mich so bald wie möglich bei Ihnen.</p>
        <p>Herzliche Grüße<br>
        <strong>Katharina Klenk</strong><br>
        Familienberatung nach Jesper Juul<br>
        <a href='https://familie-beraten.de'>familie-beraten.de</a></p>
    ";
    $confirm->AltBody = "Liebe/r {$name},\n\nvielen Dank für Ihre Nachricht. Ich melde mich bald bei Ihnen.\n\nHerzliche Grüße\nKatharina Klenk";

    $confirm->send();

    echo '<script>alert("Vielen Dank! Ihre Nachricht wurde erfolgreich gesendet."); window.location.href="index.html";</script>';
} catch (Exception $e) {
    echo '<script>alert("Leider konnte die Nachricht nicht gesendet werden. Bitte versuchen Sie es später erneut."); window.location.href="index.html";</script>';
}
?>
