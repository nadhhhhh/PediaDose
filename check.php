<?php
// check.php
require_once 'config/db.php';

// Fetch all drugs for dropdown
$stmt = $pdo->query("SELECT id, nama_obat, min_mg_per_kg_per_day, max_mg_per_kg_per_day FROM drugs ORDER BY nama_obat ASC");
$drugs = $stmt->fetchAll();

$result = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_pasien = htmlspecialchars($_POST['nama_pasien']);
    $umur_tahun = (int)($_POST['umur_tahun'] ?? 0);
    $umur_bulan_input = (int)($_POST['umur_bulan'] ?? 0);
    $umur_bulan = ($umur_tahun * 12) + $umur_bulan_input;
    if ($umur_bulan <= 0) $umur_bulan = 1;
    $berat_badan_kg = (float)$_POST['berat_badan_kg'];
    $diagnosis = htmlspecialchars($_POST['diagnosis']);
    $drug_id = (int)$_POST['drug_id'];
    
    $jumlah_dosis = (float)$_POST['jumlah_dosis'];
    $satuan_dosis = htmlspecialchars($_POST['satuan_dosis']);
    $kekuatan_mg = (float)$_POST['kekuatan_mg'];
    $kekuatan_ml = (float)$_POST['kekuatan_ml'];
    if ($kekuatan_ml <= 0) $kekuatan_ml = 1;

    $dosis_per_pemberian_mg = 0;
    $input_label = $jumlah_dosis . " " . $satuan_dosis;

    if ($satuan_dosis === 'mg') {
        $dosis_per_pemberian_mg = $jumlah_dosis;
        $input_label = $jumlah_dosis . " mg";
    } else {
        $volume_ml = 0;
        if ($satuan_dosis === 'ml') {
            $volume_ml = $jumlah_dosis;
            $input_label = $jumlah_dosis . " ml";
        } elseif ($satuan_dosis === 'sendok_teh') {
            $volume_ml = $jumlah_dosis * 5;
            $input_label = $jumlah_dosis . " Sendok Teh (" . $volume_ml . " ml)";
        } elseif ($satuan_dosis === 'sendok_makan') {
            $volume_ml = $jumlah_dosis * 15;
            $input_label = $jumlah_dosis . " Sendok Makan (" . $volume_ml . " ml)";
        } elseif ($satuan_dosis === 'tetes') {
            $volume_ml = $jumlah_dosis / 20;
            $input_label = $jumlah_dosis . " Tetes (" . $volume_ml . " ml)";
        }
        
        $dosis_per_pemberian_mg = $volume_ml * ($kekuatan_mg / $kekuatan_ml);
        $input_label .= " (setara " . round($dosis_per_pemberian_mg, 2) . " mg)";
    }

    $frekuensi_per_hari = (int)$_POST['frekuensi_per_hari'];

    // Get drug info
    $stmt = $pdo->prepare("SELECT * FROM drugs WHERE id = ?");
    $stmt->execute([$drug_id]);
    $drug = $stmt->fetch();

    if ($drug) {
        $nama_obat = $drug['nama_obat'];
        $min_mg_per_kg_per_day = (float)$drug['min_mg_per_kg_per_day'];
        $max_mg_per_kg_per_day = (float)$drug['max_mg_per_kg_per_day'];

        // Calculations
        $total_dosis_harian_mg = $dosis_per_pemberian_mg * $frekuensi_per_hari;
        $batas_minimum_harian = $min_mg_per_kg_per_day * $berat_badan_kg;
        $batas_maksimum_harian = $max_mg_per_kg_per_day * $berat_badan_kg;

        // Rule-based checking
        $status = 'Normal';
        $alertClass = 'alert-success';
        $message = "Dosis berada dalam rentang lazim.";

        if ($total_dosis_harian_mg > $batas_maksimum_harian) {
            $status = 'Overdosing';
            $alertClass = 'alert-danger';
            $message = "Dosis <strong>di atas</strong> batas maksimum aman. Risiko toksisitas!";
        } elseif ($total_dosis_harian_mg < $batas_minimum_harian) {
            $status = 'Underdosing';
            $alertClass = 'alert-warning';
            $message = "Dosis <strong>di bawah</strong> batas minimum efektif. Risiko terapi gagal.";
        }

        // Simulate AI Confidence Score (70 - 98)
        $ai_score = rand(70, 98);

        // Output Result String
        $frekuensi_string = $frekuensi_per_hari . "x sehari";
        $summaryString = "$nama_obat {$input_label} {$frekuensi_string} pada anak {$berat_badan_kg} kg terdeteksi <strong>" . strtolower($status) . "</strong>.";

        $result = [
            'status' => $status,
            'alertClass' => $alertClass,
            'message' => $message,
            'summaryString' => $summaryString,
            'ai_score' => $ai_score,
            'total_harian' => $total_dosis_harian_mg,
            'batas_min' => $batas_minimum_harian,
            'batas_max' => $batas_maksimum_harian
        ];

        // Save to Database
        $stmt = $pdo->prepare("INSERT INTO checks (nama_pasien, umur_bulan, berat_badan_kg, diagnosis, nama_obat, dosis_per_pemberian_mg, frekuensi_per_hari, total_dosis_harian_mg, status, ai_confidence_score) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $nama_pasien, $umur_bulan, $berat_badan_kg, $diagnosis, $nama_obat, 
            $dosis_per_pemberian_mg, $frekuensi_per_hari, $total_dosis_harian_mg, 
            $status, $ai_score
        ]);
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cek Dosis - PediaDose</title>
    <link rel="stylesheet" href="assets/css/style.css?v=<?= time() ?>">
</head>
<body>

    <div class="sidebar">
        <div class="sidebar-header">
            <h2>PediaDose</h2>
        </div>
        <ul class="sidebar-menu">
            <li><a href="index.php">Dashboard</a></li>
            <li><a href="check.php" class="active">Cek Dosis</a></li>
            <li><a href="kalkulator.php">Kalkulator Dosis</a></li>
            <li><a href="drugs.php">Data Obat</a></li>
            <li><a href="history.php">Riwayat Cek</a></li>
            <li><a href="about.php">Tentang</a></li>
        </ul>
        <div style="padding: 15px 20px;">
            <a href="mobile/check.php" style="display: block; background: var(--secondary-blue); color: white; text-align: center; padding: 10px; border-radius: 8px; text-decoration: none; font-weight: bold;">📱 Versi Mobile</a>
        </div>
        <div class="sidebar-footer">
            <p>&copy; 2026 PediaDose</p>
        </div>
    </div>

    <div class="main-content">
        <h1 class="page-title">Form Cek Dosis Pediatrik</h1>

        <?php if ($result): ?>
            <div class="card" style="border-top: 5px solid <?= $result['status'] == 'Normal' ? 'var(--alert-green-text)' : ($result['status'] == 'Overdosing' ? 'var(--alert-red-text)' : 'var(--alert-yellow-text)') ?>;">
                <h2>Hasil Analisis AI (Simulasi)</h2>
                <div class="alert <?= $result['alertClass'] ?> alert-result" style="margin-top: 15px;">
                    <h3 style="margin-bottom: 10px;">Status: <?= $result['status'] ?></h3>
                    <p style="font-size: 1.1rem; margin-bottom: 10px;"><?= $result['summaryString'] ?></p>
                    <p><?= $result['message'] ?></p>
                </div>
                
                <div class="grid-2" style="margin-top: 20px;">
                    <div>
                        <p><strong>Total Dosis Harian:</strong> <?= $result['total_harian'] ?> mg/hari</p>
                        <p><strong>Range Aman (Berdasarkan BB):</strong> <?= $result['batas_min'] ?> mg - <?= $result['batas_max'] ?> mg /hari</p>
                    </div>
                    <div style="text-align: right;">
                        <div style="display: inline-block; background-color: #e9ecef; padding: 10px 20px; border-radius: 8px;">
                            <span style="font-size: 0.9rem; color: var(--text-muted);">AI Confidence Score</span><br>
                            <span style="font-size: 1.5rem; font-weight: bold; color: var(--primary-blue);"><?= $result['ai_score'] ?>%</span>
                        </div>
                    </div>
                </div>
                <div style="margin-top: 20px; text-align: center;">
                    <a href="check.php" class="btn btn-primary">Cek Pasien Lain</a>
                </div>
            </div>
        <?php endif; ?>

        <div class="card" <?= $result ? 'style="display:none;"' : '' ?>>
            <form action="check.php" method="POST">
                <div class="grid-2">
                    <div class="form-group">
                        <label for="nama_pasien">Nama Pasien</label>
                        <input type="text" id="nama_pasien" name="nama_pasien" class="form-control" required placeholder="Cth: An. Budi">
                    </div>
                    <div class="form-group">
                        <label>Umur</label>
                        <div style="display: flex; gap: 10px;">
                            <div style="flex: 1;">
                                <small style="display: block; margin-bottom: 5px; color: var(--text-muted); font-weight: 500;">Tahun</small>
                                <input type="number" id="umur_tahun" name="umur_tahun" class="form-control" min="0" placeholder="0" value="0" required>
                            </div>
                            <div style="flex: 1;">
                                <small style="display: block; margin-bottom: 5px; color: var(--text-muted); font-weight: 500;">Bulan</small>
                                <input type="number" id="umur_bulan" name="umur_bulan" class="form-control" min="0" max="11" placeholder="0" value="0" required>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid-2">
                    <div class="form-group">
                        <label for="berat_badan_kg">Berat Badan (kg)</label>
                        <input type="number" step="0.1" id="berat_badan_kg" name="berat_badan_kg" class="form-control" required min="1" placeholder="Cth: 12.5">
                    </div>
                    <div class="form-group">
                        <label for="diagnosis">Diagnosis</label>
                        <input type="text" id="diagnosis" name="diagnosis" class="form-control" required placeholder="Cth: Faringitis">
                    </div>
                </div>

                <hr style="border:0; border-top:1px solid var(--border-color); margin: 20px 0;">

                <div class="form-group">
                    <label for="drug_id">Nama Obat</label>
                    <select id="drug_id" name="drug_id" class="form-control" required>
                        <option value="">-- Pilih Obat --</option>
                        <?php foreach($drugs as $d): ?>
                            <option value="<?= $d['id'] ?>"><?= $d['nama_obat'] ?> (Range: <?= $d['min_mg_per_kg_per_day'] ?> - <?= $d['max_mg_per_kg_per_day'] ?> mg/kg/hari)</option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="grid-2">
                    <div class="form-group">
                        <label>Jumlah Dosis & Satuan</label>
                        <div style="display: flex; gap: 10px;">
                            <input type="number" step="0.1" name="jumlah_dosis" class="form-control" required placeholder="Cth: 1" style="flex: 1;">
                            <select name="satuan_dosis" class="form-control" style="flex: 2;">
                                <option value="mg">mg (Zat Aktif)</option>
                                <option value="ml">Mililiter (ml)</option>
                                <option value="sendok_teh">Sendok Teh (5 ml)</option>
                                <option value="sendok_makan">Sendok Makan (15 ml)</option>
                                <option value="tetes">Tetes (drop)</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Kekuatan Sediaan (Jika bukan mg)</label>
                        <div style="display: flex; gap: 5px; align-items: center;">
                            <input type="number" step="0.1" name="kekuatan_mg" class="form-control" placeholder="mg" value="0">
                            <span>mg /</span>
                            <input type="number" step="0.1" name="kekuatan_ml" class="form-control" placeholder="ml" value="1">
                            <span>ml</span>
                        </div>
                    </div>
                </div>

                <div class="grid-2" style="margin-top: 5px;">
                    <div class="form-group">
                        <label for="frekuensi_per_hari">Frekuensi per Hari (kali)</label>
                        <input type="number" id="frekuensi_per_hari" name="frekuensi_per_hari" class="form-control" required min="1" placeholder="Cth: 3">
                    </div>
                </div>

                <div class="form-group" style="margin-top: 20px;">
                    <button type="submit" class="btn btn-primary" style="width: 100%; padding: 15px; font-size: 1.1rem;">Hitung & Cek Dosis</button>
                </div>
            </form>
        </div>
    </div>

    <script src="assets/js/script.js"></script>
</body>
</html>
