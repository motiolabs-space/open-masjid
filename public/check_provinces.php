<?php
// Load CodeIgniter framework
require '../app-core/app/Config/Paths.php';
$paths = new Config\Paths();
require rtrim($paths->systemDirectory, '\\/ ') . DIRECTORY_SEPARATOR . 'bootstrap.php';

use App\Models\ProvinceModel;

$model = new ProvinceModel();
$provinces = $model->findAll();

echo "Total Provinces: " . count($provinces) . "\n";
foreach ($provinces as $p) {
    echo "ID: " . $p['id'] . " - Name: " . $p['name'] . "\n";
}
