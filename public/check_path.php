<?php
$path = __DIR__;
echo "<h1>Path Finder</h1>";
echo "<b>Lokasi folder ini (public_html):</b><br>";
echo $path . "<br><br>";

echo "<b>Copy line ini ke file .env Anda:</b><br>";
echo "<code style='background:#eee; padding:5px; border:1px solid #ccc; display:inline-block;'>STORAGE_PATH=\"{$path}\"</code>";
