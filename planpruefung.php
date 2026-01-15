<?php
// Datenbankverbindung
require_once 'api/db.php';

// URL-Parameter auslesen
$plan_parameter = isset($_GET['Plan']) ? $_GET['Plan'] : null;

// Parameter splitten
$plan_parts = null;
if ($plan_parameter) {
    $parts = explode('/', $plan_parameter);
    if (count($parts) === 4) {
        // Datum formatieren (von YYYYMMDD zu YYYY.MM.DD für Anzeige und YYYY-MM-DD für DB)
        $datum_raw = $parts[3];
        $datum_formatted = $datum_raw;
        $datum_db = $datum_raw;
        
        if (strlen($datum_raw) === 8 && ctype_digit($datum_raw)) {
            $datum_formatted = substr($datum_raw, 0, 4) . '.' . substr($datum_raw, 4, 2) . '.' . substr($datum_raw, 6, 2);
            $datum_db = substr($datum_raw, 0, 4) . '-' . substr($datum_raw, 4, 2) . '-' . substr($datum_raw, 6, 2);
        }
        
        $plan_parts = [
            'PrNr' => $parts[0],
            'plan_nummer' => $parts[1],
            'plan_version' => $parts[2],
            'plan_datum' => $datum_formatted,
            'plan_datum_db' => $datum_db  // Für DB-Abfrage
        ];
    }
}

// Erster Datensatz aus der Tabelle "Pläne" abrufen
$plan = null;
$error_message = null;
$version_info = null;

// Wenn Plan-Parameter vorhanden sind, in der Datenbank suchen
if ($plan_parts) {
    try {
        // Suche nach dem spezifischen Plan
        $stmt = $pdo->prepare("SELECT * FROM Pläne WHERE PrNr = :prnr AND plan_nummer = :nummer AND plan_version = :version AND plan_datum = :datum LIMIT 1");
        $stmt->execute([
            ':prnr' => $plan_parts['PrNr'],
            ':nummer' => $plan_parts['plan_nummer'],
            ':version' => $plan_parts['plan_version'],
            ':datum' => $plan_parts['plan_datum_db']
        ]);
        $plan = $stmt->fetch();
        
        // Prüfe auf mehrere Versionen mit gleicher PrNr und plan_nummer
        $stmt_versions = $pdo->prepare("
            SELECT plan_version, id, plan_datum 
            FROM Pläne 
            WHERE PrNr = :prnr AND plan_nummer = :nummer 
            ORDER BY id DESC
        ");
        $stmt_versions->execute([
            ':prnr' => $plan_parts['PrNr'],
            ':nummer' => $plan_parts['plan_nummer']
        ]);
        $all_versions = $stmt_versions->fetchAll();
        
        if (count($all_versions) > 1) {
            $latest_version = $all_versions[0]; // Höchste ID (neueste)
            $version_info = [
                'count' => count($all_versions),
                'latest_version' => $latest_version['plan_version'],
                'latest_id' => $latest_version['id'],
                'is_latest' => ($plan && $plan['id'] == $latest_version['id']),
                'all_versions' => $all_versions
            ];
        }
        
    } catch (PDOException $e) {
        $error_message = "Fehler beim Laden der Daten: " . $e->getMessage();
    }
} else {
    // Ohne Parameter: ersten Datensatz laden
    try {
        $stmt = $pdo->query("SELECT * FROM Pläne LIMIT 1");
        $plan = $stmt->fetch();
    } catch (PDOException $e) {
        $error_message = "Fehler beim Laden der Daten: " . $e->getMessage();
    }
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
            
            <?php if ($plan_parts): ?>
                <?php 
                    // Hintergrundfarbe und Status basierend auf Version
                    if (!$plan) {
                        // Plan existiert nicht in der Datenbank
                        $bg_color = '#ffcccc'; // Standard grau
                        $border_color = '#dc3545';
                        $status_text = 'Plan existiert NICHT';
                        $status_color = '#dc3545';
                    } elseif ($version_info && !$version_info['is_latest']) {
                        // Plan existiert, aber nicht die neueste Version
                        $bg_color = '#ffcccc'; // Hellrot
                        $border_color = '#dc3545';
                        $status_text = 'KEINE aktuelle Version';
                        $status_color = '#dc3545';
                    } else {
                        // Plan existiert und ist die aktuelle Version
                        $bg_color = '#d4edda'; // Grün
                        $border_color = '#673DE6';
                        $status_text = 'AKTUELLE Version';
                        $status_color = '#28a745';
                    }
                ?>
                
                <div style="text-align: center; margin: 20px 0 10px 0;">
                    <span style="font-family: 'DM Sans', sans-serif; font-size: 32px; font-weight: 700; color: <?php echo $status_color; ?>;">
                        <?php echo $status_text; ?>
                    </span>
                </div>
                
                <div style="margin: 10px 0 20px 0; padding: 20px; background-color: <?php echo $bg_color; ?>; border-radius: 8px; border-left: 4px solid <?php echo $border_color; ?>;">
                    <table style="font-family: 'DM Sans', sans-serif; border-collapse: collapse;">
                        <tr>
                            <td style="font-weight: 700; color: #36344D; padding: 8px 15px 8px 0; white-space: nowrap;">PrNr:</td>
                            <td style="color: #673DE6; font-size: 18px; font-weight: 700; padding: 8px 0;"><?php echo htmlspecialchars($plan_parts['PrNr']); ?></td>
                        </tr>
                        <tr>
                            <td style="font-weight: 700; color: #36344D; padding: 8px 15px 8px 0; white-space: nowrap;">Plan-Nummer:</td>
                            <td style="color: #673DE6; font-size: 18px; font-weight: 700; padding: 8px 0;"><?php echo htmlspecialchars($plan_parts['plan_nummer']); ?></td>
                        </tr>
                        <tr>
                            <td style="font-weight: 700; color: #36344D; padding: 8px 15px 8px 0; white-space: nowrap;">Version:</td>
                            <td style="color: #673DE6; font-size: 18px; font-weight: 700; padding: 8px 0;"><?php echo htmlspecialchars($plan_parts['plan_version']); ?></td>
                        </tr>
                        <tr>
                            <td style="font-weight: 700; color: #36344D; padding: 8px 15px 8px 0; white-space: nowrap;">Datum:</td>
                            <td style="color: #673DE6; font-size: 18px; font-weight: 700; padding: 8px 0;"><?php echo htmlspecialchars($plan_parts['plan_datum']); ?></td>
                        </tr>
                    </table>
                </div>
            <?php elseif ($plan_parameter): ?>
                <div style="text-align: center; margin: 20px 0; padding: 15px; background-color: #f8d7da; border-radius: 5px; border-left: 4px solid #dc3545;">
                    <strong style="color: #721c24; font-family: 'DM Sans', sans-serif;">Fehler:</strong> 
                    <span style="color: #721c24; font-family: 'DM Sans', sans-serif;">Ungültiges Plan-Format. Erwartet: PrNr/Nummer/Version/Datum</span>
                </div>
            <?php endif; ?>
            
            <?php if ($version_info): ?>
                <div style="margin: 20px 0; padding: 15px; background-color: <?php echo $version_info['is_latest'] ? '#d1ecf1' : '#fff3cd'; ?>; border-radius: 5px; border-left: 4px solid <?php echo $version_info['is_latest'] ? '#17a2b8' : '#ffc107'; ?>;">
                    <strong style="color: #004085; font-family: 'DM Sans', sans-serif;">Version-Info:</strong>
                    
                    <details style="margin-top: 10px;">
                        <summary style="cursor: pointer; font-weight: 700; color: #004085;">Alle Versionen anzeigen</summary>
                        <table style="margin-top: 10px; width: 100%; font-family: 'DM Sans', sans-serif; font-size: 14px;">
                            <tr style="background-color: rgba(0,0,0,0.05);">
                                <th style="padding: 5px; text-align: left; width: 1%; white-space: nowrap;">Version</th>
                                <th style="padding: 5px; text-align: left;">Datum</th>
                            </tr>
                            <?php foreach ($version_info['all_versions'] as $v): ?>
                                <tr style="<?php echo ($plan && $v['id'] == $plan['id']) ? 'background-color: #ffffcc;' : ''; ?>">
                                    <td style="padding: 5px; white-space: nowrap;"><?php echo htmlspecialchars($v['plan_version']); ?></td>
                                    <td style="padding: 5px;"><?php echo htmlspecialchars($v['plan_datum']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </table>
                    </details>
                </div>
            <?php endif; ?>

            <?php if ($error_message): ?>
                <div class="error-box">
                    <strong>Fehler:</strong> <?php echo htmlspecialchars($error_message); ?>
                </div>
            <?php endif; ?>
        </div>
    </body>
</html>
