<?php

// Curl kullanarak HTTP isteği yapmak için fonksiyon
function fetch_url($url) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpcode >= 200 && $httpcode < 300) {
        return $response;
    }
    return false;
}

// WordPress API'den gönderileri almak için fonksiyon
function get_wordpress_posts($page = 1) {
    $url = "https://www.dervis.com.tr/wp-json/wp/v2/posts?page=$page";
    return fetch_url($url);
}

// Kategori detaylarını almak için fonksiyon
function get_category_details($category_id) {
    $url = "https://www.dervis.com.tr/wp-json/wp/v2/categories/$category_id";
    return fetch_url($url);
}

// Gönderi verilerini işlemek için fonksiyon
function parse_post_data($post) {
    $title = html_entity_decode($post['title']['rendered'], ENT_QUOTES, 'UTF-8');
    
    // HTML içeriğini temizleme
    $description = html_entity_decode($post['content']['rendered'], ENT_QUOTES, 'UTF-8');
    $description = strip_tags($description);
    $description = preg_replace('/\s+/', ' ', $description); // Tüm fazla beyaz alanları tek boşluk ile değiştir
    
    // Kategorileri işleme
    $post_categories = [];
    foreach ($post['categories'] as $category_id) {
        $category_response = get_category_details($category_id);
        if ($category_response) {
            $category_details = json_decode($category_response, true);
            $post_categories[] = [
                'id' => $category_details['id'],
                'name' => $category_details['name'],
                'slug' => $category_details['slug']
            ];
        }
    }
    
    $image = get_featured_image($post['id']);
    
    return [
        'title' => $title,
        'description' => $description,
        'categories' => $post_categories,
        'image' => $image
    ];
}

// Öne çıkan resmi almak için fonksiyon
function get_featured_image($post_id) {
    $url = "https://www.dervis.com.tr/wp-json/wp/v2/media?parent=$post_id";
    $response = fetch_url($url);
    if ($response) {
        $media = json_decode($response, true);
        if (!empty($media)) {
            return $media[0]['source_url'];
        }
    }
    return null;
}

$all_posts = [];
$page = 1;

// Tüm gönderileri al ve işle
do {
    $posts_response = get_wordpress_posts($page);
    if ($posts_response) {
        $posts = json_decode($posts_response, true);
        foreach ($posts as $post) {
            $parsed_data = parse_post_data($post);
            array_push($all_posts, $parsed_data);
        }
        $page++;
    } else {
        break;
    }
} while (!empty($posts));

header('Content-Type: application/json; charset=UTF-8');
echo json_encode($all_posts, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
?>
