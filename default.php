<?php
// Datenbankverbindung prüfen
require_once 'db.php';

// Verbindung testen
$db_status = "connected";
$db_message = "Datenbankverbindung erfolgreich";

try {
    // Einfache Abfrage zum Test der Verbindung
    $stmt = $pdo->query('SELECT 1');
    $stmt->fetch();
} catch (PDOException $e) {
    $db_status = "error";
    $db_message = "Datenbankfehler: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="de">
    <head>
        <title>Default page</title>
        <link rel="icon" type="image/x-icon" href="/assets/favicon.ico">
        <meta charset="utf-8">
        <meta content="IE=edge,chrome=1" http-equiv="X-UA-Compatible">
        <meta content="Default page" name="description">
        <meta content="width=device-width, initial-scale=1" name="viewport">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link href="https://fonts.googleapis.com/css2?family=DM+Sans&display=swap" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;700&display=swap" rel="stylesheet">
        <style>
            body {
                margin: 0px;
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                width: 100vw;
                height: 100vh;
                min-height: 675px;
                background-color: #F4F5FF;
            }
            .logo {
                max-width: 250px;
                height: auto;
                margin-bottom: 20px;
            }
            p {
                width: 100%;
                left: 0px;
                font-size: 16px;
                font-family: 'DM Sans', sans-serif;
                font-weight: 400;
                letter-spacing: 0px;
                text-align: center;
                vertical-align: top;
                max-width: 550px;
                color: #727586;
                margin: 0px;
            }
            a:hover {
                cursor: pointer;
                color: #673DE6;
                text-decoration: underline;
            }
            h1 {
                font-family: 'DM Sans', sans-serif;
                font-size: 24px;
                font-weight: 700;
                letter-spacing: 0px;
                text-align: center;
                margin: 8px;
            }
            .content {
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                width: 100%;
                height: 100%;
            }
            .ic-launch  {
                margin-left: 10.5px;
                width: 21px !important;
                height: 20px !important;
            }
            .link-container {
                margin-top: 32px;
                margin-bottom: 32px;
            }
            .link {
                display: flex;
                flex-direction: row;
                align-items: center;
                justify-content: center;
                font-family: 'DM Sans', sans-serif;
                font-style: normal;
                font-weight: 700;
                font-size: 14px;
                color: #673DE6;
                margin-top: 8px;
                text-decoration: none;
            }
            .main-image {
                width: 100%;
                max-width: 650px;
                max-height: 406px;
                height: auto;
            }
            .navigation {
                width: 100%;
                height: 72px;
                display: flex;
                margin: 0;
                padding: 0;
                flex-direction: row;
                align-items: center;
                justify-content: center;
                background-color: #36344D;
            }
            @media screen and (max-width: 580px) and (min-width: 0px) {
                h1, p, .link-container {
                    width: 80%;
                }
            }
            @media screen and (min-width: 650px) and (min-height: 0px) and (max-height: 750px) {
                .link-container {
                    margin-top: 12px;
                }
                h1 {
                    margin-top: 0px;
                    margin-bottom: 0px;
                }
            }
        </style>
    </head>
    <body>
        <div class="content">
            <img src="/assets/HH-Anlagenbau-Gelb-transparent.png" alt="HH Anlagenbau" class="logo">
            <h1>Planprüfung für Baustellen</h1>
            <p>mit QR Code:</p>
            
            <!-- Datenbank-Status -->
            <div style="margin-top: 20px; padding: 10px; border-radius: 5px; background-color: <?php echo $db_status === 'connected' ? '#d4edda' : '#f8d7da'; ?>; border: 1px solid <?php echo $db_status === 'connected' ? '#c3e6cb' : '#f5c6cb'; ?>; max-width: 550px;">
                <p style="color: <?php echo $db_status === 'connected' ? '#155724' : '#721c24'; ?>; margin: 0;">
                    <strong>DB-Status:</strong> <?php echo htmlspecialchars($db_message); ?>
                </p>
            </div>
          </div>
    </body>
</html>