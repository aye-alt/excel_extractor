<?php
$updated_json_path = 'updated_merged_products.json';
$updated_products = json_decode(file_get_contents($updated_json_path), true);


$csv_file_path = 'woocommerce_products.csv';
$csv_file = fopen($csv_file_path, 'w');


foreach ($updated_products as $product) {
    echo $product['title'];
}