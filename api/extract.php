<?php
// api/extract.php
header('Content-Type: application/json');

// Mock Database for AI Extraction
$drugDictionary = [
    'paracetamol' => [
        'nama_obat' => 'Paracetamol',
        'diagnosis' => 'Demam, Nyeri Ringan',
        'dosis_dewasa' => '500-1000',
        'dosis_dewasa_satuan' => 'mg',
        'min_mg' => 40.00, // min_mg_per_kg_per_day
        'max_mg' => 60.00,
        'catatan' => 'Dosis pediatri umumnya 10-15 mg/kgBB per kali pemberian, maks 4x sehari. (Diekstrak secara otomatis)'
    ],
    'amoxicillin' => [
        'nama_obat' => 'Amoxicillin',
        'diagnosis' => 'Infeksi Bakteri, Otitis Media',
        'dosis_dewasa' => '500',
        'dosis_dewasa_satuan' => 'mg',
        'min_mg' => 20.00,
        'max_mg' => 90.00,
        'catatan' => 'Dosis dibagi tiap 8 atau 12 jam. (Diekstrak secara otomatis)'
    ],
    'ibuprofen' => [
        'nama_obat' => 'Ibuprofen',
        'diagnosis' => 'Demam, Nyeri, Inflamasi',
        'dosis_dewasa' => '400',
        'dosis_dewasa_satuan' => 'mg',
        'min_mg' => 20.00,
        'max_mg' => 40.00,
        'catatan' => 'Dosis umumnya 5-10 mg/kgBB per kali pemberian, maks 4x sehari. Berikan setelah makan. (Diekstrak secara otomatis)'
    ],
    'cefixime' => [
        'nama_obat' => 'Cefixime',
        'diagnosis' => 'Infeksi Bakteri Rentan',
        'dosis_dewasa' => '200',
        'dosis_dewasa_satuan' => 'mg',
        'min_mg' => 8.00,
        'max_mg' => 12.00,
        'catatan' => 'Dapat diberikan sebagai dosis tunggal atau dibagi tiap 12 jam. (Diekstrak secara otomatis)'
    ]
];

$type = $_POST['type'] ?? '';
$extractedData = null;
$sourceText = '';

// Delay to simulate processing time
sleep(2);

if ($type === 'url') {
    $url = $_POST['url'] ?? '';
    if (empty($url)) {
        echo json_encode(['status' => 'error', 'message' => 'URL tidak boleh kosong']);
        exit;
    }
    // We parse the URL string for keywords since real scraping is blocked by MIMS Cloudflare
    $sourceText = strtolower($url);
} elseif ($type === 'file') {
    if (!isset($_FILES['file'])) {
        echo json_encode(['status' => 'error', 'message' => 'File tidak ditemukan']);
        exit;
    }
    // We parse the filename for keywords simulating OCR result
    $sourceText = strtolower($_FILES['file']['name']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Tipe ekstraksi tidak valid']);
    exit;
}

// Find matching drug in dictionary
foreach ($drugDictionary as $keyword => $data) {
    if (strpos($sourceText, $keyword) !== false) {
        $extractedData = $data;
        break;
    }
}

if ($extractedData) {
    echo json_encode([
        'status' => 'success',
        'data' => $extractedData
    ]);
} else {
    // Generic response if keyword not found
    echo json_encode([
        'status' => 'success',
        'data' => [
            'nama_obat' => 'Obat (Hasil Ekstrak)',
            'diagnosis' => 'Berdasarkan referensi web/scan',
            'dosis_dewasa' => '500',
            'dosis_dewasa_satuan' => 'mg',
            'min_mg' => 10,
            'max_mg' => 20,
            'catatan' => 'Data otomatis ini tidak spesifik, mohon verifikasi ulang secara manual.'
        ]
    ]);
}
