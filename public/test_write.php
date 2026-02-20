<?php
// Script to test file writing permissions
// Upload this to public_html/test_write.php

define('FCPATH', __DIR__ . '/');
$targetDir = FCPATH . 'images/masjid';

echo "<h1>File Write Test</h1>";
echo "<b>FCPATH:</b> " . FCPATH . "<br>";
echo "<b>Target Dir:</b> " . $targetDir . "<br>";

// 1. Check if images folder exists
if (!is_dir(FCPATH . 'images')) {
    echo "❌ Folder 'images' not found.<br>";
} else {
    echo "✅ Folder 'images' exists.<br>";
    echo "Permissions: " . substr(sprintf('%o', fileperms(FCPATH . 'images')), -4) . "<br>";
    echo "Owner: " . fileowner(FCPATH . 'images') . "<br>";
}

// 2. Try to create 'masjid' folder if not exists
if (!is_dir($targetDir)) {
    echo "⚠️ Folder 'images/masjid' not found. Keep trying to create...<br>";
    if (mkdir($targetDir, 0755, true)) {
        echo "✅ Successfully created 'images/masjid'.<br>";
    } else {
        echo "❌ Failed to create 'images/masjid'. Check permissions of 'images' folder.<br>";
        $error = error_get_last();
        echo "Error: " . $error['message'] . "<br>";
    }
} else {
    echo "✅ Folder 'images/masjid' already exists.<br>";
    echo "Permissions: " . substr(sprintf('%o', fileperms($targetDir)), -4) . "<br>";
}

// 3. Try to write a test file
$testFile = $targetDir . '/test_upload.txt';
if (file_put_contents($testFile, 'Test write ' . date('Y-m-d H:i:s'))) {
    echo "✅ Successfully wrote test file to: $testFile<br>";
    echo "Check URL: <a href='/images/masjid/test_upload.txt' target='_blank'>/images/masjid/test_upload.txt</a><br>";
} else {
    echo "❌ Failed to write file to 'images/masjid'.<br>";
    $error = error_get_last();
    echo "Error: " . $error['message'] . "<br>";
}
