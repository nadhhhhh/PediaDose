<?php
// kalkulator.php
require_once 'config/db.php';

$result = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dosis_dewasa = (float)$_POST['dosis_dewasa'];
    $rumus = htmlspecialchars($_POST['rumus']);
    $umur_tahun = (float)($_POST['umur_tahun'] ?? 0);
    $umur_bulan = (float)($_POST['umur_bulan'] ?? 0);
    $berat_badan = (float)($_POST['berat_badan'] ?? 0);
    $sediaan = htmlspecialchars($_POST['sediaan']);
    $kekuatan_mg = (float)($_POST['kekuatan_mg'] ?? 1);
    $kekuatan_ml = (float)($_POST['kekuatan_ml'] ?? 1);

    if ($kekuatan_mg <= 0) $kekuatan_mg = 1;
    if ($kekuatan_ml <= 0) $kekuatan_ml = 1;

    $dosis_anak_mg = 0;
    $rumus_label = "";
    $error = "";

    // 1. Hitung dosis anak dalam mg
    if ($rumus === 'young') {
        if ($umur_tahun <= 0) $error = "Umur tahun harus diisi untuk Rumus Young.";
        $dosis_anak_mg = ($umur_tahun / ($umur_tahun + 12)) * $dosis_dewasa;
        $rumus_label = "Rumus Young (Anak < 12 tahun)";
    } elseif ($rumus === 'dilling') {
        if ($umur_tahun <= 0) $error = "Umur tahun harus diisi untuk Rumus Dilling.";
        $dosis_anak_mg = ($umur_tahun / 20) * $dosis_dewasa;
        $rumus_label = "Rumus Dilling (Anak > 8 tahun)";
    } elseif ($rumus === 'fried') {
        if ($umur_bulan <= 0) $error = "Umur bulan harus diisi untuk Rumus Fried.";
        $dosis_anak_mg = ($umur_bulan / 150) * $dosis_dewasa;
        $rumus_label = "Rumus Fried (Bayi < 1 tahun)";
    } elseif ($rumus === 'clark') {
        if ($berat_badan <= 0) $error = "Berat badan harus diisi untuk Rumus Clark.";
        $dosis_anak_mg = ($berat_badan / 70) * $dosis_dewasa;
        $rumus_label = "Rumus Clark (Berdasarkan Berat Badan)";
    }

    if (empty($error)) {
        // 2. Konversi ke sediaan praktis
        $konversi_label = "";
        $dosis_anak_mg_format = round($dosis_anak_mg, 1);

        if ($sediaan === 'tablet') {
            $jumlah_tablet = $dosis_anak_mg / $kekuatan_mg;
            $jumlah_tablet_bulat = round($jumlah_tablet, 1);
            $konversi_label = "<strong>{$jumlah_tablet_bulat} Tablet/Puyer</strong> per pemberian (Sediaan {$kekuatan_mg} mg/tablet).";
        } elseif ($sediaan === 'sirup') {
            // Sirup dibulatkan ke bilangan bulat sesuai request user
            $volume_ml = $dosis_anak_mg / ($kekuatan_mg / $kekuatan_ml);
            $volume_ml_bulat = round($volume_ml, 0);
            
            // Konversi takaran rumah tangga
            if ($volume_ml_bulat >= 15 && $volume_ml_bulat % 15 == 0) {
                $sendok = $volume_ml_bulat / 15;
                $takar = " ({$sendok} Sendok Makan)";
            } elseif ($volume_ml_bulat >= 5 && $volume_ml_bulat % 5 == 0) {
                $sendok = $volume_ml_bulat / 5;
                $takar = " ({$sendok} Sendok Teh)";
            } else {
                $takar = "";
            }

            $konversi_label = "<strong>{$volume_ml_bulat} ml{$takar}</strong> per pemberian (Sediaan {$kekuatan_mg} mg / {$kekuatan_ml} ml).";
        } elseif ($sediaan === 'drop') {
            // Drop bisa 1 angka di belakang koma atau bulat
            $volume_ml = $dosis_anak_mg / ($kekuatan_mg / $kekuatan_ml);
            $volume_ml_format = round($volume_ml, 1);
            $jumlah_tetes = round($volume_ml * 20, 0); // 1 ml = 20 tetes
            $konversi_label = "<strong>{$volume_ml_format} ml (sekitar {$jumlah_tetes} tetes)</strong> per pemberian (Sediaan Drop {$kekuatan_mg} mg / {$kekuatan_ml} ml).";
        }

        $result = [
            'dosis_anak_mg' => $dosis_anak_mg_format,
            'rumus_label' => $rumus_label,
            'konversi_label' => $konversi_label
        ];
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kalkulator Konversi Dosis - PediaDose</title>
    <link rel="stylesheet" href="assets/css/style.css?v=<?= time() ?>">
</head>
<body>

    <div class="sidebar">
        <div class="sidebar-header">
            <h2>PediaDose</h2>
        </div>
        <ul class="sidebar-menu">
            <li><a href="index.php">Dashboard</a></li>
            <li><a href="check.php">Cek Dosis</a></li>
            <li><a href="kalkulator.php" class="active">Kalkulator Dosis</a></li>
            <li><a href="drugs.php">Data Obat</a></li>
            <li><a href="history.php">Riwayat Cek</a></li>
            <li><a href="about.php">Tentang</a></li>
        </ul>
        <div style="padding: 15px 20px;">
            <a href="mobile/kalkulator.php" style="display: block; background: var(--secondary-blue); color: white; text-align: center; padding: 10px; border-radius: 8px; text-decoration: none; font-weight: bold;">📱 Versi Mobile</a>
        </div>
        <div class="sidebar-footer">
            <p>&copy; 2026 PediaDose</p>
        </div>
    </div>

    <div class="main-content">
        <h1 class="page-title">Kalkulator Konversi Dosis Pediatrik</h1>
        <p style="color: var(--text-muted); margin-bottom: 20px;">Hitung takaran dosis anak berdasarkan dosis dewasa yang diketahui menggunakan standar rumus medis.</p>

        <?php if (isset($error) && $error): ?>
            <div class="alert alert-danger" style="margin-bottom: 20px;">
                <?= $error ?>
            </div>
        <?php endif; ?>

        <?php if ($result): ?>
            <div class="card" style="border-top: 5px solid var(--primary-blue);">
                <h2>Hasil Kalkulasi Konversi</h2>
                <div class="alert alert-info" style="margin-top: 15px;">
                    <p style="font-size: 1.1rem; margin-bottom: 10px;">Metode: <strong><?= $result['rumus_label'] ?></strong></p>
                    <p style="font-size: 1.2rem; margin-bottom: 10px;">Dosis Anak: <strong><?= $result['dosis_anak_mg'] ?> mg</strong></p>
                    <p style="font-size: 1.1rem;">Takaran Pemberian: <?= $result['konversi_label'] ?></p>
                </div>
                <div style="margin-top: 20px; text-align: center;">
                    <a href="kalkulator.php" class="btn btn-primary">Hitung Ulang</a>
                </div>
            </div>
        <?php endif; ?>

        <div class="card" <?= $result ? 'style="display:none;"' : '' ?>>
            <form action="kalkulator.php" method="POST" id="kalkulatorForm">
                
                <div class="form-group">
                    <label for="dosis_dewasa">Dosis Lazim Dewasa (mg)</label>
                    <input type="number" step="0.1" id="dosis_dewasa" name="dosis_dewasa" class="form-control" required placeholder="Cth: 500">
                </div>

                <div class="form-group">
                    <label for="rumus">Pilihan Rumus Pediatrik</label>
                    <select id="rumus" name="rumus" class="form-control" required onchange="updateParameterInput()">
                        <option value="">-- Pilih Rumus --</option>
                        <option value="young">Rumus Young (Berdasarkan Umur, < 12 Tahun)</option>
                        <option value="dilling">Rumus Dilling (Berdasarkan Umur, > 8 Tahun)</option>
                        <option value="fried">Rumus Fried (Berdasarkan Umur, Bayi < 1 Tahun)</option>
                        <option value="clark">Rumus Clark (Berdasarkan Berat Badan)</option>
                    </select>
                </div>

                <!-- Parameter Input Dinamis -->
                <div class="grid-2" id="param-container" style="display: none; background: #f8f9fa; padding: 15px; border-radius: 8px; margin-bottom: 15px; border: 1px solid #e9ecef;">
                    <div class="form-group" id="param-umur-tahun" style="display: none;">
                        <label for="umur_tahun">Umur (Tahun)</label>
                        <input type="number" id="umur_tahun" name="umur_tahun" class="form-control" min="1" placeholder="Cth: 6">
                    </div>
                    <div class="form-group" id="param-umur-bulan" style="display: none;">
                        <label for="umur_bulan">Umur (Bulan)</label>
                        <input type="number" id="umur_bulan" name="umur_bulan" class="form-control" min="1" placeholder="Cth: 8">
                    </div>
                    <div class="form-group" id="param-berat-badan" style="display: none;">
                        <label for="berat_badan">Berat Badan (kg)</label>
                        <input type="number" step="0.1" id="berat_badan" name="berat_badan" class="form-control" min="1" placeholder="Cth: 15">
                    </div>
                </div>

                <hr style="border:0; border-top:1px solid var(--border-color); margin: 20px 0;">

                <div class="form-group">
                    <label for="sediaan">Bentuk Sediaan Obat</label>
                    <select id="sediaan" name="sediaan" class="form-control" required onchange="updateSediaanInput()">
                        <option value="">-- Pilih Bentuk Sediaan --</option>
                        <option value="tablet">Tablet / Puyer / Kapsul</option>
                        <option value="sirup">Cair / Sirup (Sendok Takar)</option>
                        <option value="drop">Cair / Drop (Tetes)</option>
                    </select>
                </div>

                <!-- Kekuatan Sediaan Dinamis -->
                <div class="grid-2" id="kekuatan-container" style="display: none; background: #f8f9fa; padding: 15px; border-radius: 8px; margin-bottom: 15px; border: 1px solid #e9ecef;">
                    <div class="form-group" id="kekuatan-tablet" style="display: none;">
                        <label for="kekuatan_tablet_mg">Kandungan per Tablet/Kapsul (mg)</label>
                        <input type="number" step="0.1" id="kekuatan_tablet_mg" name="kekuatan_mg" class="form-control" placeholder="Cth: 500">
                    </div>
                    
                    <div class="form-group" id="kekuatan-sirup" style="display: none; grid-column: span 2;">
                        <label>Kekuatan Sediaan Cair</label>
                        <div style="display: flex; gap: 10px; align-items: center;">
                            <input type="number" step="0.1" id="kekuatan_sirup_mg" name="kekuatan_mg" class="form-control" placeholder="mg (Cth: 120)" style="flex: 1;">
                            <span>mg /</span>
                            <input type="number" step="0.1" id="kekuatan_sirup_ml" name="kekuatan_ml" class="form-control" placeholder="ml (Cth: 5)" style="flex: 1;">
                            <span>ml</span>
                        </div>
                    </div>
                </div>

                <div class="form-group" style="margin-top: 20px;">
                    <button type="submit" class="btn btn-primary" style="width: 100%; padding: 15px; font-size: 1.1rem;">Hitung Konversi</button>
                </div>
            </form>
        </div>
    </div>

    <script src="assets/js/script.js"></script>
    <script>
        function updateParameterInput() {
            const rumus = document.getElementById('rumus').value;
            const container = document.getElementById('param-container');
            const umurTahun = document.getElementById('param-umur-tahun');
            const umurBulan = document.getElementById('param-umur-bulan');
            const beratBadan = document.getElementById('param-berat-badan');

            umurTahun.style.display = 'none';
            umurBulan.style.display = 'none';
            beratBadan.style.display = 'none';

            if (rumus === 'young' || rumus === 'dilling') {
                container.style.display = 'grid';
                umurTahun.style.display = 'block';
            } else if (rumus === 'fried') {
                container.style.display = 'grid';
                umurBulan.style.display = 'block';
            } else if (rumus === 'clark') {
                container.style.display = 'grid';
                beratBadan.style.display = 'block';
            } else {
                container.style.display = 'none';
            }
        }

        function updateSediaanInput() {
            const sediaan = document.getElementById('sediaan').value;
            const container = document.getElementById('kekuatan-container');
            const tablet = document.getElementById('kekuatan-tablet');
            const sirup = document.getElementById('kekuatan-sirup');
            
            const mgInputTablet = document.getElementById('kekuatan_tablet_mg');
            const mgInputSirup = document.getElementById('kekuatan_sirup_mg');
            const mlInputSirup = document.getElementById('kekuatan_sirup_ml');

            tablet.style.display = 'none';
            sirup.style.display = 'none';
            
            // Non-aktifkan semua name agar tidak saling timpa
            mgInputTablet.removeAttribute('name');
            mgInputSirup.removeAttribute('name');
            mlInputSirup.removeAttribute('name');

            if (sediaan === 'tablet') {
                container.style.display = 'grid';
                tablet.style.display = 'block';
                mgInputTablet.setAttribute('name', 'kekuatan_mg');
            } else if (sediaan === 'sirup' || sediaan === 'drop') {
                container.style.display = 'grid';
                sirup.style.display = 'block';
                mgInputSirup.setAttribute('name', 'kekuatan_mg');
                mlInputSirup.setAttribute('name', 'kekuatan_ml');
            } else {
                container.style.display = 'none';
            }
        }

        // Initialize display on back button
        window.onload = function() {
            updateParameterInput();
            updateSediaanInput();
        };
    </script>
</body>
</html>
