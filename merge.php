<?php
// JSON dosyalarını oku
$excel_json_data = file_get_contents('excel_to_json.json');
$excel_products = json_decode($excel_json_data, true);

$test_json_data = file_get_contents('test.json');
$test_products = json_decode($test_json_data, true);

// Ürünleri eşleştir ve birleştir
$merged_products = [];
$similarity_threshold = 50; // %50 benzerlik eşiği

foreach ($excel_products as $excel_product) {
    $best_match = null;
    $highest_similarity = 0;

    foreach ($test_products as $test_product) {
        similar_text($excel_product['title'], $test_product['title'], $similarity_percentage);

        if ($similarity_percentage > $highest_similarity) {
            $highest_similarity = $similarity_percentage;
            $best_match = $test_product;
        }
    }

    if ($highest_similarity >= $similarity_threshold) {
        $merged_product = array_merge($excel_product, $best_match);
        $merged_products[] = $merged_product;
    }
}

// Birleştirilmiş JSON verisini dosyaya yaz
$merged_json_data = json_encode($merged_products, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
file_put_contents('merged_products.json', $merged_json_data);

echo "Veriler başarıyla birleştirildi ve merged_products.json dosyasına kaydedildi.\n";
?>
