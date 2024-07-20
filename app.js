const ExcelJS = require('exceljs');
const fs = require('fs');

async function convertExcelToJson() {
    const workbook = new ExcelJS.Workbook();
    await workbook.xlsx.readFile('sources/dervis_fiyat.xlsx');
    const worksheet = workbook.getWorksheet(1);

    const jsonData = [];

    worksheet.eachRow((row, rowNumber) => {
        if (rowNumber > 1) { // Skip header row
            const productId = row.getCell(1).value; // Column A
            const productName = row.getCell(2).value; // Column A
            const packaging = row.getCell(3).value; // Column B
            const productCode = row.getCell(4).value; // Column C
            const productBarcode = row.getCell(5).value; // Column D
            const vatRate = row.getCell(6).value; // Column E

            // Check if all cells in the row have values
            if (productName && packaging && productCode && productBarcode && vatRate) {
                const rowData = {
                    "product_id": productId, // We can use the rowNumber - 1 as product ID
                    "product_name": productName,
                    "packaging": packaging,
                    "product_code": productCode,
                    "product_barcode": productBarcode,
                    "product_vat_rate": vatRate
                };
                jsonData.push(rowData);
            }
        }
    });

    fs.writeFileSync('output.json', JSON.stringify(jsonData, null, 2));
}

convertExcelToJson().then(() => {
    console.log('Excel verileri JSON formatına dönüştürüldü.');
}).catch(err => {
    console.error('Bir hata oluştu:', err);
});
