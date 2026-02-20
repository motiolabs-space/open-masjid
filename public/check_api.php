<?php
// Search for Yogyakarta
$urlSearch = "https://api.myquran.com/v3/sholat/kota/cari/yogyakarta";
echo "Searching City: $urlSearch ...\n";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $urlSearch);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
$outputSearch = curl_exec($ch);
curl_close($ch);
print_r(json_decode($outputSearch, true));

// Try with correct ID
$id = '577ef1154f3240ad5b9b413aa7346a1e';
$y = '2025';
$m = '02';
$d = '19';
$url = "https://api.myquran.com/v3/sholat/jadwal/$id/$y/$m/$d";

echo "Fetching with correct ID: $url ...\n";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
$output = curl_exec($ch);
curl_close($ch);

print_r(json_decode($output, true));
