<?php
function get_product_image($title, $wordpress_api_url) {
    $search_url = "{$wordpress_api_url}/wp-json/wp/v2/posts?search=" . urlencode($title);
    
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $search_url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    $result = curl_exec($curl);
    curl_close($curl);

    $products = json_decode($result, true);
    $image_url = null;

    if (!empty($products)) {
        foreach ($products as $product) {
            if (isset($product['content']['rendered'])) {
                preg_match('/<img[^>]+src="([^">]+)"/i', $product['content']['rendered'], $matches);
                if (!empty($matches[1])) {
                    $image_url = $matches[1];
                    break;
                }
            }
        }
    }

    return $image_url;
}

// WordPress API URL ve kimlik bilgileri
$wordpress_api_url = "https://www.dervis.com.tr";

// merged_products.json dosyasını oku
$merged_json_path = 'merged_products.json';
$merged_products = json_decode(file_get_contents($merged_json_path), true);

foreach ($merged_products as &$product) {
    if (empty($product['image'])) {
        $image_url = get_product_image($product['title'], $wordpress_api_url);
        if ($image_url) {
            $product['image'] = $image_url;
        }else{
            echo 'eşleşmedi <br>';
        }
    }
}

// Güncellenmiş JSON verisini dosyaya yaz
file_put_contents('updated_merged_products.json', json_encode($merged_products, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

echo "Ürünlerin resimleri başarıyla güncellendi ve updated_merged_products.json dosyasına kaydedildi.\n";