<?php
require_once 'config/database.php';
$database = new Database();
$db = $database->getConnection();

// Détection dynamique de l'hôte
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
$host = $_SERVER['HTTP_HOST'];
$uri_path = dirname($_SERVER['PHP_SELF']);
$base_url = $protocol . "://" . $host . ($uri_path == '/' || $uri_path == '\\' ? '' : $uri_path);

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    
    $stmt = $db->prepare("SELECT * FROM adherents WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $adherent = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($adherent && !empty($adherent['email'])) {
        $to = $adherent['email'];
        $sujet = "Votre QR Code d'accès - Dabakh Fitness";
        $numero_licence = $adherent['numero_licence'];
        
        // URL complète et dynamique intégrée au QR code
        $url_avis = $base_url . "/donner_avis.php?licence=" . $numero_licence;
        $qr_url = "https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=" . urlencode($url_avis);
        
        $message_html = '
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; color: #333; }
                .container { padding: 20px; border: 1px solid #ddd; border-radius: 5px; max-width: 600px; margin: auto; }
                .header { background: #111; color: #fff; padding: 10px; text-align: center; border-radius: 5px 5px 0 0; }
                .content { padding: 20px; text-align: center; }
                .qr { margin: 20px 0; }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h2>Dabakh Fitness - Bienvenue !</h2>
                </div>
                <div class="content">
                    <p>Bonjour <strong>' . htmlspecialchars($adherent['prenom'] . ' ' . $adherent['nom']) . '</strong>,</p>
                    <p>Votre inscription a bien été validée.</p>
                    <p>Votre numéro de licence est : <strong>' . $numero_licence . '</strong></p>
                    <p>Scannez ce QR code pour donner directement votre avis sur le coaching ou le fonctionnement de la salle :</p>
                    <div class="qr">
                        <img src="' . $qr_url . '" alt="QR Code Licence">
                    </div>
                    <p><small>À très bientôt dans notre salle !</small></p>
                </div>
            </div>
        </body>
        </html>
        ';

        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= "From: contact@dabakhfitness.com" . "\r\n";

        if (mail($to, $sujet, $message_html, $headers)) {
            header("Location: adherents.php?msg=email_sent");
            exit();
        } else {
            header("Location: adherents.php?msg=email_error");
            exit();
        }
    }
}

header("Location: adherents.php");
exit();
?>
