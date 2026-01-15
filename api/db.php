<?php
declare(strict_types=1);

/**
 * Erstellt eine PDO-Verbindung zur Hostinger-MySQL-Datenbank.
 */

// Lade Umgebungsvariablen aus .env Datei
function loadEnv($path) {
    if (!file_exists($path)) {
        throw new Exception('.env file not found');
    }
    
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        
        list($name, $value) = explode('=', $line, 2);
        $name = trim($name);
        $value = trim($value);
        
        if (!array_key_exists($name, $_ENV)) {
            $_ENV[$name] = $value;
        }
    }
}

// .env Datei laden (ein Verzeichnis höher)
loadEnv(__DIR__ . '/../.env');

$DB_HOST = $_ENV['DB_HOST'] ?? 'localhost';
$DB_NAME = $_ENV['DB_NAME'] ?? '';
$DB_USER = $_ENV['DB_USER'] ?? '';
$DB_PASS = $_ENV['DB_PASS'] ?? '';

$dsn = "mysql:host={$DB_HOST};dbname={$DB_NAME};charset=utf8mb4";

$options = [
  PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
  PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
  PDO::ATTR_EMULATE_PREPARES => false,
];

try {
  $pdo = new PDO($dsn, $DB_USER, $DB_PASS, $options);

} catch (PDOException $e) {
  http_response_code(500);
  header('Content-Type: application/json; charset=utf-8');
  echo json_encode([
    'error' => 'DB connection failed',
    'details' => $e->getMessage()
  ]);
  exit;
}
