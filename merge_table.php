<?php
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

// Excel ve JSON dosya yolları
$excelFile = 'sources/converted_dervis_fiyat.xlsx';
$jsonFile = 'test.json';
$outputJsonFile = 'latest_prods.json';

// Excel dosyasını oku
$spreadsheet = IOFactory::load($excelFile);
$worksheet = $spreadsheet->getActiveSheet();
$rows = $worksheet->toArray();

// JSON dosyasını oku
$jsonData = file_get_contents($jsonFile);
$products = json_decode($jsonData, true);

// Excel başlıklarını ve verilerini işle
$header = array_shift($rows); // İlk satır başlıklar
$data = [];

// Excel verilerini diziye dönüştür
foreach ($rows as $row) {
    $data[] = array_combine($header, $row);
}

// Ürün verilerini eşleştir ve güncelle
foreach ($products as &$product) {
    $title = $product['title'];
    
    foreach ($data as $item) {
        if ($item['title'] === $title) {
            $product['product_code'] = $item['product_code'] ?? '';
            $product['packaking'] = $item['packaking'] ?? '';
            $product['kg_unit_price'] = $item['kg_unit_price'] ?? '';
            $product['canister_unit_price'] = $item['canister_unit_price'] ?? '';
            $product['in_the_package'] = $item['in_the_package'] ?? '';
            $product['amount'] = $item['amount'] ?? '';
            $product['vat_rate'] = $item['vat_rate'] ?? '';
        }
    }
}

// Güncellenmiş JSON verisini dosyaya yaz
file_put_contents($outputJsonFile, json_encode($products, JSON_PRETTY_PRINT));

echo "Güncelleme tamamlandı!";
?>
