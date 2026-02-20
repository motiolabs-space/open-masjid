<?php
// Simple script to check regencies data
$envFile = __DIR__ . '/../app-core/.env';
$lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
$env = [];
foreach ($lines as $line) {
    if (strpos($line, '#') === 0) continue;
    $parts = explode('=', $line, 2);
    if (count($parts) === 2) {
        $env[trim($parts[0])] = trim($parts[1]);
    }
}

$host = $env['database.default.hostname'] ?? 'localhost';
$user = $env['database.default.username'] ?? 'root';
$pass = $env['database.default.password'] ?? '';
$dbname = $env['database.default.database'] ?? 'masjid_db';

try {
    $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";
    $pdo = new PDO($dsn, $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "Checking 'regencies' table data...\n";
    $stmt = $pdo->query("SELECT * FROM regencies");
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "Total: " . count($rows) . "\n";
    foreach ($rows as $r) {
        print_r($r);
    }

} catch (PDOException $e) {
    echo "Database Error: " . $e->getMessage() . "\n";
}
