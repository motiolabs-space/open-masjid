<?php
function fetchUrl($url) {
    echo "Fetching $url ... ";
    $json = @file_get_contents($url);
    if ($json) {
        echo "OK! Length: " . strlen($json) . "\n";
        $data = json_decode($json, true);
        if ($data) {
            print_r(array_slice($data, 0, 3)); // Show first 3 items or keys
            return $data;
        }
    } else {
        echo "FAILED (404/Error)\n";
    }
    return null;
}

// Test various potential endpoints
fetchUrl('https://api.myquran.com/v3/sholat/provinsi');
fetchUrl('https://api.myquran.com/v3/sholat/provinsi/semua');
fetchUrl('https://api.myquran.com/v3/daerah/provinsi');
fetchUrl('https://api.myquran.com/v3/daerah/provinsi/semua');
fetchUrl('https://api.myquran.com/v3/sholat/kota/cari/jakarta');
