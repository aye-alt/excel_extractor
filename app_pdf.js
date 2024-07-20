const fs = require('fs');
const pdf = require('pdf-parse');

// PDF dosyasını oku
let dataBuffer = fs.readFileSync('sources/dervis_fiyat.pdf');

pdf(dataBuffer).then(function(data) {
    // PDF içeriği metin olarak burada
    let text = data.text;

    fs.writeFileSync('output.log', text);


    // Ürün bilgilerini düzenlemek için düzenli ifadeler kullan
    // Ürün kodu, ağırlık, fiyat ve toplam değeri yakalamak için daha esnek bir regex
    let productRegex = /P\d{4}\s+\d+(\.\d+)?\s+₺\s+\d+(\.\d+)?\s+₺\s+\d+\s+\w+\.\s+\d+(\.\d+)?\s+₺/g;
    let matches = text.match(productRegex);

    if (!matches) {
        console.log('No matches found');
        return;
    }

    // JSON formatında sonuçları saklamak için boş bir dizi oluştur
    let products = [];

    // Eşleşmeleri işle
    matches.forEach(match => {
        let parts = match.split(/\s+/);
        let product = {
            code: parts[0],
            weight: parts[1] + ' kg',
            price: parts[3] + ' ' + parts[2],
            total: parts[5] + ' ' + parts[4]
        };
        products.push(product);
    });

    // JSON çıktısını yazdır
    console.log(JSON.stringify(products, null, 2));

    // JSON çıktısını bir dosyaya kaydet
    fs.writeFileSync('output.json', JSON.stringify(products, null, 2));
});
