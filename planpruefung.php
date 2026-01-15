<?php
// Datenbankverbindung
require_once 'api/db.php';

// Erster Datensatz aus der Tabelle "Pläne" abrufen
$plan = null;
$error_message = null;

try {
    // $stmt = $pdo->query("SELECT * FROM Pläne ORDER BY id ASC LIMIT 1");
    $stmt = $pdo->query("SELECT * FROM Pläne LIMIT 1");
    $plan = $stmt->fetch();
} catch (PDOException $e) {
    $error_message = "Fehler beim Laden der Daten: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="de">
    <head>
        <title>Planprüfung</title>
        <link rel="icon" type="image/x-icon" href="/assets/favicon.ico">
        <meta charset="utf-8">
        <meta content="IE=edge,chrome=1" http-equiv="X-UA-Compatible">
        <meta content="Planprüfung" name="description">
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
                justify-content: flex-start;
                width: 100vw;
                min-height: 100vh;
                background-color: #F4F5FF;
                padding: 20px;
                box-sizing: border-box;
            }
            .container {
                max-width: 900px;
                width: 100%;
                background: white;
                border-radius: 8px;
                padding: 30px;
                box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            }
            .logo {
                display: block;
                max-width: 250px;
                height: auto;
                margin: 0 auto 20px auto;
            }
            h1 {
                font-family: 'DM Sans', sans-serif;
                font-size: 32px;
                font-weight: 700;
                letter-spacing: 0px;
                text-align: center;
                margin: 0 0 10px 0;
                color: #36344D;
            }
            h2 {
                font-family: 'DM Sans', sans-serif;
                font-size: 24px;
                font-weight: 700;
                color: #36344D;
                margin-top: 25px;
                margin-bottom: 15px;
            }
            p {
                font-size: 16px;
                font-family: 'DM Sans', sans-serif;
                font-weight: 400;
                letter-spacing: 0px;
                color: #727586;
                line-height: 1.6;
                margin: 8px 0;
            }
            .back-link {
                display: inline-block;
                margin-bottom: 20px;
                color: #673DE6;
                text-decoration: none;
                font-family: 'DM Sans', sans-serif;
                font-weight: 700;
                font-size: 14px;
            }
            .back-link:hover {
                text-decoration: underline;
            }
            .plan-card {
                background-color: #f9f9f9;
                border: 1px solid #ddd;
                border-radius: 8px;
                padding: 20px;
                margin-top: 20px;
            }
            .plan-row {
                display: grid;
                grid-template-columns: 200px 1fr;
                gap: 10px;
                padding: 10px 0;
                border-bottom: 1px solid #e0e0e0;
            }
            .plan-row:last-child {
                border-bottom: none;
            }
            .plan-label {
                font-weight: 700;
                color: #36344D;
                font-family: 'DM Sans', sans-serif;
            }
            .plan-value {
                color: #727586;
                font-family: 'DM Sans', sans-serif;
            }
            .error-box {
                background-color: #f8d7da;
                border: 1px solid #f5c6cb;
                color: #721c24;
                padding: 15px;
                border-radius: 5px;
                margin-top: 20px;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <img src="/assets/HH-Anlagenbau-Gelb-transparent.png" alt="HH Anlagenbau" class="logo">
            <h1>Planprüfung</h1>
            <p style="text-align: center;">Prüfen Sie hier Ihre Baupläne mit QR-Code</p>
            
            <?php if ($error_message): ?>
                <div class="error-box">
                    <strong>Fehler:</strong> <?php echo htmlspecialchars($error_message); ?>
                </div>
            <?php elseif ($plan): ?>
                <h2>Plan Details</h2>
                <div class="plan-card">
                    <?php foreach ($plan as $key => $value): ?>
                        <?php if (!is_numeric($key)): ?>
                            <div class="plan-row">
                                <div class="plan-label"><?php echo htmlspecialchars($key); ?>:</div>
                                <div class="plan-value"><?php echo htmlspecialchars($value ?? '-'); ?></div>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p style="margin-top: 20px; text-align: center;">Keine Pläne in der Datenbank gefunden.</p>
            <?php endif; ?>
        </div>
    </body>
</html>
