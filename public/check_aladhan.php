<?php
// Test AlAdhan API with Coordinates (Yogyakarta approx: -7.7956, 110.3695)
$lat = -7.7956;
$long = 110.3695;
$date = date('d-m-Y'); // Format DD-MM-YYYY
// Method 20 = Kemenag seems to be missing from some docs, let's try standard or look for ID.
// Actually Method 20 is "Kemenag - Indonesia" in some listings. Let's try.
$url = "http://api.aladhan.com/v1/timings/$date?latitude=$lat&longitude=$long&method=20";

echo "Fetching AlAdhan: $url ...\n";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$output = curl_exec($ch);
curl_close($ch);

$data = json_decode($output, true);
print_r($data);
