<?php
// Simple script to check database without loading full CI4 framework
$envFile = __DIR__ . '/../app-core/.env';
if (!file_exists($envFile)) {
    die("Error: .env file not found at $envFile\n");
}

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

    echo "Connected to database: $dbname\n";

    // Check provinces
    echo "Checking 'provinces' table...\n";
    $stmt = $pdo->query("SELECT * FROM provinces ORDER BY id");
    $provinces = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "Total Provinces: " . count($provinces) . "\n";
    foreach ($provinces as $p) {
        echo "[{$p['id']}] {$p['name']}\n";
    }
    
    // Check total count
    $count = $pdo->query("SELECT count(*) FROM provinces")->fetchColumn();
    echo "Total provinces count: $count\n";

    // Check regencies schema
    echo "\nChecking 'regencies' table schema...\n";
    try {
        $stmt = $pdo->query("DESCRIBE regencies");
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($columns as $col) {
            echo "Column: " . $col['Field'] . " (" . $col['Type'] . ")\n";
        }
        $regCount = $pdo->query("SELECT count(*) FROM regencies")->fetchColumn();
        echo "Total regencies count: $regCount\n";
    } catch (PDOException $e) {
        echo "Table 'regencies' likely does not exist yet.\n";
    }

} catch (PDOException $e) {
    echo "Database Error: " . $e->getMessage() . "\n";
}
