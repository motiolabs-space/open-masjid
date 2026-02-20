<?php
$url = 'https://api.myquran.com/v3/sholat/kota/semua';
$json = file_get_contents($url);
if ($json === false) {
    die("Error fetching URL");
}
$data = json_decode($json, true);
if (json_last_error() !== JSON_ERROR_NONE) {
    die("JSON Decode Error: " . json_last_error_msg());
}
echo "Data Count: " . count($data) . "\n";


$prefixes = [];

// Access the 'data' key if it exists
$cities = $data['data'] ?? []; // MyQuran V3 structure

foreach ($cities as $city) {
    if (isset($city['id'])) {
        $id = $city['id'];
        // Only look at numeric IDs for now, or take first 2 chars
        $prefix = substr($id, 0, 2);
        
        if (!isset($prefixes[$prefix])) {
            $prefixes[$prefix] = $city['lokasi'];
        }
    }
}

ksort($prefixes);

$output = "";
foreach ($prefixes as $p => $name) {
    $output .= "$p : $name\n";
}

file_put_contents('prefixes.txt', $output);
echo "Written to prefixes.txt";
