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

function GetCategoriesSelectedProduct($product){
    $cats = "";
    foreach ($product as $key) {
        $cats .= $key['name'] . " > ";
    }
    return $cats;
}

$latest_prods = [];
// Ürün verilerini eşleştir ve güncelle
foreach ($data as &$row) {      
    foreach ($products as $product) {
        if(str_contains($row['title'], $product['title'])){
            $prod = [
                'title' => $product['title'] . ' - ' . $row['packaking'] . $row['unit'] . ' X ' . $row['in_the_package'] . ' ' . $row['in_unit'],
                'price' => $row['amount'],
                'categories' => GetCategoriesSelectedProduct($product['categories'])
            ];

            array_push($latest_prods, $prod);
        }
    }
}

print_r(json_encode($latest_prods));

exit;
// Güncellenmiş JSON verisini dosyaya yaz
file_put_contents($outputJsonFile, json_encode($products, JSON_PRETTY_PRINT));

echo "Güncelleme tamamlandı!";
?>
