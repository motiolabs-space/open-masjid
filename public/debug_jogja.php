<?php
// Database credentials
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'masjid2';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$database", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "Connected to database: $database\n";
    
    // 1. Find Jogja Province ID
    echo "1. Searching for DI Yogyakarta Province...\n";
    $stmt = $pdo->query("SELECT * FROM provinces WHERE name LIKE '%Yogyakarta%' OR name LIKE '%Jogja%'");
    $provs = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($provs)) {
        echo "❌ Province NOT FOUND!\n";
    } else {
        foreach ($provs as $p) {
            echo "✅ Found Province: [{$p['id']}] {$p['name']}\n";
            $provId = $p['id'];
            
            // 2. Check Regencies for this Province
            echo "   Checking regencies with province_id = $provId ...\n";
            $stmt2 = $pdo->prepare("SELECT * FROM regencies WHERE province_id = ?");
            $stmt2->execute([$provId]);
            $cities = $stmt2->fetchAll(PDO::FETCH_ASSOC);
            
            echo "   Found " . count($cities) . " cities linked to this province.\n";
            foreach ($cities as $c) {
                echo "   - [{$c['id']}] {$c['name']}\n";
            }
        }
    }
    
    // 3. Search for orphaned Jogja cities
    echo "\n2. Searching for any cities with 'Yogyakarta' or 'Bantul' in name (orphaned check)...\n";
    $stmt3 = $pdo->query("SELECT * FROM regencies WHERE name LIKE '%Yogyakarta%' OR name LIKE '%Bantul%' OR name LIKE '%Sleman%'");
    $orphans = $stmt3->fetchAll(PDO::FETCH_ASSOC);
    foreach ($orphans as $o) {
        echo "   Found city: [{$o['id']}] {$o['name']} (Prov ID: {$o['province_id']})\n";
    }

} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
