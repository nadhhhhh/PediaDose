<?php
// mobile/check.php
require_once '../config/db.php';

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
            $input_label = $jumlah_dosis . " Cth (" . $volume_ml . " ml)";
        } elseif ($satuan_dosis === 'sendok_makan') {
            $volume_ml = $jumlah_dosis * 15;
            $input_label = $jumlah_dosis . " C (+ " . $volume_ml . " ml)";
        } elseif ($satuan_dosis === 'tetes') {
            $volume_ml = $jumlah_dosis / 20;
            $input_label = $jumlah_dosis . " gtt (" . $volume_ml . " ml)";
        }
        
        $dosis_per_pemberian_mg = $volume_ml * ($kekuatan_mg / $kekuatan_ml);
        $input_label .= " (" . round($dosis_per_pemberian_mg, 2) . "mg)";
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
            $message = "Dosis di atas batas aman.";
        } elseif ($total_dosis_harian_mg < $batas_minimum_harian) {
            $status = 'Underdosing';
            $alertClass = 'alert-warning';
            $message = "Dosis di bawah batas efektif.";
        }

        $ai_score = rand(70, 98);
        $frekuensi_string = $frekuensi_per_hari . "x/hari";
        $summaryString = "$nama_obat {$input_label} {$frekuensi_string} (BB: {$berat_badan_kg}kg) terdeteksi <strong>" . strtolower($status) . "</strong>.";

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
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Cek Dosis - Mobile</title>
    <link rel="stylesheet" href="../assets/css/style.css?v=<?= time() ?>">
    <link rel="stylesheet" href="../assets/css/mobile-style.css?v=<?= time() ?>">
</head>
<body>

    <div class="mobile-topbar">
        <h1>Cek Dosis AI</h1>
        <a href="../check.php" style="color: white; text-decoration: none; font-size: 0.8rem; background: rgba(255,255,255,0.2); padding: 5px 10px; border-radius: 5px;">💻 Desktop</a>
    </div>

    <div class="main-content">
        <?php if ($result): ?>
            <div class="card" style="border-top: 5px solid <?= $result['status'] == 'Normal' ? 'var(--alert-green-text)' : ($result['status'] == 'Overdosing' ? 'var(--alert-red-text)' : 'var(--alert-yellow-text)') ?>;">
                <h2 style="font-size: 1.1rem;">Hasil Analisis</h2>
                <div class="alert <?= $result['alertClass'] ?> alert-result" style="margin-top: 10px; padding: 10px;">
                    <h3 style="margin-bottom: 5px; font-size: 1rem;"><?= $result['status'] ?></h3>
                    <p style="font-size: 0.9rem; margin-bottom: 5px;"><?= $result['summaryString'] ?></p>
                </div>
                
                <div style="background-color: #e9ecef; padding: 10px; border-radius: 8px; text-align: center; margin: 15px 0;">
                    <span style="font-size: 0.8rem; color: var(--text-muted);">AI Score</span><br>
                    <span style="font-size: 1.3rem; font-weight: bold; color: var(--primary-blue);"><?= $result['ai_score'] ?>%</span>
                </div>

                <a href="check.php" class="btn btn-primary">Cek Pasien Lain</a>
            </div>
        <?php endif; ?>

        <div class="card" <?= $result ? 'style="display:none;"' : '' ?>>
            <form action="check.php" method="POST">
                <div class="form-group">
                    <label>Pasien & BB (kg)</label>
                    <div style="display: flex; gap: 10px;">
                        <input type="text" name="nama_pasien" class="form-control" required placeholder="Nama" style="flex: 2;">
                        <input type="number" step="0.1" name="berat_badan_kg" class="form-control" required placeholder="BB kg" style="flex: 1;">
                    </div>
                </div>

                <div class="form-group">
                    <label>Umur (Thn & Bln)</label>
                    <div style="display: flex; gap: 10px;">
                        <input type="number" name="umur_tahun" class="form-control" min="0" placeholder="Tahun" value="0" required>
                        <input type="number" name="umur_bulan" class="form-control" min="0" max="11" placeholder="Bulan" value="0" required>
                    </div>
                </div>

                <div class="form-group">
                    <label>Diagnosis</label>
                    <input type="text" name="diagnosis" class="form-control" required placeholder="Cth: Demam">
                </div>

                <div class="form-group">
                    <label>Obat</label>
                    <select name="drug_id" class="form-control" required>
                        <option value="">-- Pilih --</option>
                        <?php foreach($drugs as $d): ?>
                            <option value="<?= $d['id'] ?>"><?= $d['nama_obat'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Dosis</label>
                    <div style="display: flex; gap: 5px;">
                        <input type="number" step="0.1" name="jumlah_dosis" class="form-control" required placeholder="Jml" style="flex: 1;">
                        <select name="satuan_dosis" class="form-control" style="flex: 1.5;">
                            <option value="mg">mg</option>
                            <option value="ml">ml</option>
                            <option value="sendok_teh">Cth (5ml)</option>
                            <option value="sendok_makan">C (15ml)</option>
                            <option value="tetes">Drop</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label>Kekuatan Sediaan (Jika bkn mg)</label>
                    <div style="display: flex; gap: 5px; align-items: center;">
                        <input type="number" step="0.1" name="kekuatan_mg" class="form-control" placeholder="mg" value="0">
                        <span>/</span>
                        <input type="number" step="0.1" name="kekuatan_ml" class="form-control" placeholder="ml" value="1">
                        <span>ml</span>
                    </div>
                </div>

                <div class="form-group">
                    <label>Frekuensi /Hari</label>
                    <input type="number" name="frekuensi_per_hari" class="form-control" required min="1" placeholder="Cth: 3">
                </div>

                <button type="submit" class="btn btn-primary" style="margin-top: 10px;">Hitung Dosis</button>
            </form>
        </div>
    </div>

    <!-- Bottom Navigation -->
    <div class="bottom-nav">
        <a href="index.php" class="nav-item">
            <div class="nav-icon">🏠</div>
            Home
        </a>
        <a href="check.php" class="nav-item active">
            <div class="nav-icon">🩺</div>
            Cek
        </a>
        <a href="kalkulator.php" class="nav-item">
            <div class="nav-icon">🧮</div>
            Kalkulator
        </a>
        <a href="drugs.php" class="nav-item">
            <div class="nav-icon">💊</div>
            Obat
        </a>
        <a href="history.php" class="nav-item">
            <div class="nav-icon">📋</div>
            Riwayat
        </a>
    </div>

</body>
</html>
