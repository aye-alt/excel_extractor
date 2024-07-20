const xlsx = require('xlsx');
const fs = require('fs');

// Excel dosyasını oku
const workbook = xlsx.readFile('sources/converted_dervis_fiyat.xlsx');

// Sayfayı seç
const sheetName = workbook.SheetNames[0];
const sheet = workbook.Sheets[sheetName];

// Sayfa verilerini JSON formatına çevir
const data = xlsx.utils.sheet_to_json(sheet, {header: 1});

// JSON yapısını oluşturmak için bir nesne oluştur
let categories = [];
let currentCategory = '';
const headers = ['Ürün\nGörseli', 'Malın Cinsi\nAçıklama', 'Ürün\nKodu', 'Ambalaj', 'KG BR.\nFiyat', 'Bidon BR. Fiyat', 'Koli İçi', 'Tutar', 'KDV Oranı'];

// Verileri işleyerek JSON yapısını oluştur
data.forEach(row => {
    // Eğer kategori adı varsa, bunu currentCategory olarak ayarla
    if (row[0] && row[0].includes('Hijyen Ürünleri')) {
        currentCategory = row[0].trim();
        categories.push({
            category_name: currentCategory,
            products: [],
            product_count: 0
        });
    } else if (row[1] && !headers.includes(row[1]) && row[2] !== 'Ürün\nKodu' && row[2] !== undefined) {
        // Eğer ürün bilgileri varsa, currentCategory altına ekle
        let category = categories.find(c => c.category_name === currentCategory);
        if (category) {
            let product = {
                image: row[0] || "",
                title: row[1] || "",
                product_code: row[2] || "",
                amount: row[3] || 0,
                unit: row[4] || "",
                unit_price: row[5] || "",
                in_the_package: row[6] || 0,
                in_the_package_unit: row[7] || "",
                index: category.products.length + 1 // index ekle
            };
            category.products.push(product);
            category.product_count = category.products.length; // product_count güncelle
        }
    }
});

// JSON çıktısını yazdır
console.log(JSON.stringify({categories: categories}, null, 2));

// JSON çıktısını bir dosyaya kaydet
fs.writeFileSync('output_xlsx.json', JSON.stringify({categories: categories}, null, 2));
