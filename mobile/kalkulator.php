<?php
// mobile/kalkulator.php
require_once '../config/db.php';

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

    if ($rumus === 'young') {
        if ($umur_tahun <= 0) $error = "Isi umur tahun (Rumus Young).";
        $dosis_anak_mg = ($umur_tahun / ($umur_tahun + 12)) * $dosis_dewasa;
        $rumus_label = "Young (< 12 thn)";
    } elseif ($rumus === 'dilling') {
        if ($umur_tahun <= 0) $error = "Isi umur tahun (Rumus Dilling).";
        $dosis_anak_mg = ($umur_tahun / 20) * $dosis_dewasa;
        $rumus_label = "Dilling (> 8 thn)";
    } elseif ($rumus === 'fried') {
        if ($umur_bulan <= 0) $error = "Isi umur bulan (Rumus Fried).";
        $dosis_anak_mg = ($umur_bulan / 150) * $dosis_dewasa;
        $rumus_label = "Fried (< 1 thn)";
    } elseif ($rumus === 'clark') {
        if ($berat_badan <= 0) $error = "Isi berat badan (Rumus Clark).";
        $dosis_anak_mg = ($berat_badan / 70) * $dosis_dewasa;
        $rumus_label = "Clark (BB)";
    }

    if (empty($error)) {
        $konversi_label = "";
        $dosis_anak_mg_format = round($dosis_anak_mg, 1);

        if ($sediaan === 'tablet') {
            $jumlah_tablet = $dosis_anak_mg / $kekuatan_mg;
            $jumlah_tablet_bulat = round($jumlah_tablet, 1);
            $konversi_label = "<strong>{$jumlah_tablet_bulat} Tablet/Puyer</strong>";
        } elseif ($sediaan === 'sirup') {
            $volume_ml = $dosis_anak_mg / ($kekuatan_mg / $kekuatan_ml);
            $volume_ml_bulat = round($volume_ml, 0);
            
            if ($volume_ml_bulat >= 15 && $volume_ml_bulat % 15 == 0) {
                $sendok = $volume_ml_bulat / 15;
                $takar = " ({$sendok} C)";
            } elseif ($volume_ml_bulat >= 5 && $volume_ml_bulat % 5 == 0) {
                $sendok = $volume_ml_bulat / 5;
                $takar = " ({$sendok} Cth)";
            } else {
                $takar = "";
            }

            $konversi_label = "<strong>{$volume_ml_bulat} ml{$takar}</strong>";
        } elseif ($sediaan === 'drop') {
            $volume_ml = $dosis_anak_mg / ($kekuatan_mg / $kekuatan_ml);
            $volume_ml_format = round($volume_ml, 1);
            $jumlah_tetes = round($volume_ml * 20, 0);
            $konversi_label = "<strong>{$volume_ml_format} ml (~{$jumlah_tetes} tetes)</strong>";
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Kalkulator - Mobile</title>
    <link rel="stylesheet" href="../assets/css/style.css?v=<?= time() ?>">
    <link rel="stylesheet" href="../assets/css/mobile-style.css?v=<?= time() ?>">
    <style>
        .bottom-nav {
            justify-content: space-between;
        }
        .nav-item {
            flex: 1;
            font-size: 0.7rem;
            padding: 8px 2px;
        }
        .nav-icon {
            font-size: 1.2rem;
            margin-bottom: 3px;
        }
    </style>
</head>
<body>

    <div class="mobile-topbar">
        <h1>Kalkulator Konversi</h1>
        <a href="../kalkulator.php" style="color: white; text-decoration: none; font-size: 0.8rem; background: rgba(255,255,255,0.2); padding: 5px 10px; border-radius: 5px;">💻 Desktop</a>
    </div>

    <div class="main-content" style="padding-bottom: 80px;">
        <?php if (isset($error) && $error): ?>
            <div class="alert alert-danger" style="margin-bottom: 15px; padding: 10px;">
                <?= $error ?>
            </div>
        <?php endif; ?>

        <?php if ($result): ?>
            <div class="card" style="border-top: 5px solid var(--primary-blue);">
                <h2 style="font-size: 1.1rem;">Hasil Kalkulasi</h2>
                <div class="alert alert-info" style="margin-top: 10px; padding: 10px;">
                    <p style="font-size: 0.9rem; margin-bottom: 5px;">Metode: <strong><?= $result['rumus_label'] ?></strong></p>
                    <p style="font-size: 1.1rem; margin-bottom: 5px;">Dosis Anak: <strong><?= $result['dosis_anak_mg'] ?> mg</strong></p>
                    <p style="font-size: 1.1rem;">Takaran: <?= $result['konversi_label'] ?></p>
                </div>
                
                <a href="kalkulator.php" class="btn btn-primary" style="margin-top: 15px;">Hitung Ulang</a>
            </div>
        <?php endif; ?>

        <div class="card" <?= $result ? 'style="display:none;"' : '' ?>>
            <form action="kalkulator.php" method="POST" id="kalkulatorForm">
                
                <div class="form-group">
                    <label>Dosis Dewasa (mg)</label>
                    <input type="number" step="0.1" name="dosis_dewasa" class="form-control" required placeholder="Cth: 500">
                </div>

                <div class="form-group">
                    <label>Rumus Pediatrik</label>
                    <select id="rumus" name="rumus" class="form-control" required onchange="updateParameterInput()">
                        <option value="">-- Pilih --</option>
                        <option value="young">Young (< 12 Thn)</option>
                        <option value="dilling">Dilling (> 8 Thn)</option>
                        <option value="fried">Fried (< 1 Thn)</option>
                        <option value="clark">Clark (Berat Badan)</option>
                    </select>
                </div>

                <div id="param-container" style="display: none; background: #f8f9fa; padding: 10px; border-radius: 8px; margin-bottom: 15px;">
                    <div class="form-group" id="param-umur-tahun" style="display: none; margin-bottom: 0;">
                        <label>Umur (Tahun)</label>
                        <input type="number" id="umur_tahun" name="umur_tahun" class="form-control" min="1" placeholder="Cth: 6">
                    </div>
                    <div class="form-group" id="param-umur-bulan" style="display: none; margin-bottom: 0;">
                        <label>Umur (Bulan)</label>
                        <input type="number" id="umur_bulan" name="umur_bulan" class="form-control" min="1" placeholder="Cth: 8">
                    </div>
                    <div class="form-group" id="param-berat-badan" style="display: none; margin-bottom: 0;">
                        <label>Berat Badan (kg)</label>
                        <input type="number" step="0.1" id="berat_badan" name="berat_badan" class="form-control" min="1" placeholder="Cth: 15">
                    </div>
                </div>

                <div class="form-group">
                    <label>Bentuk Sediaan</label>
                    <select id="sediaan" name="sediaan" class="form-control" required onchange="updateSediaanInput()">
                        <option value="">-- Pilih --</option>
                        <option value="tablet">Tablet / Puyer</option>
                        <option value="sirup">Sirup</option>
                        <option value="drop">Drop</option>
                    </select>
                </div>

                <div id="kekuatan-container" style="display: none; background: #f8f9fa; padding: 10px; border-radius: 8px; margin-bottom: 15px;">
                    <div class="form-group" id="kekuatan-tablet" style="display: none; margin-bottom: 0;">
                        <label>Kekuatan per Tablet (mg)</label>
                        <input type="number" step="0.1" id="kekuatan_tablet_mg" name="kekuatan_mg" class="form-control" placeholder="Cth: 500">
                    </div>
                    
                    <div class="form-group" id="kekuatan-sirup" style="display: none; margin-bottom: 0;">
                        <label>Kekuatan Cairan</label>
                        <div style="display: flex; gap: 5px; align-items: center;">
                            <input type="number" step="0.1" id="kekuatan_sirup_mg" name="kekuatan_mg" class="form-control" placeholder="mg" style="flex: 1;">
                            <span>/</span>
                            <input type="number" step="0.1" id="kekuatan_sirup_ml" name="kekuatan_ml" class="form-control" placeholder="ml" style="flex: 1;">
                            <span>ml</span>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary" style="margin-top: 5px;">Hitung Konversi</button>
            </form>
        </div>
    </div>

    <!-- Bottom Navigation -->
    <div class="bottom-nav">
        <a href="index.php" class="nav-item">
            <div class="nav-icon">🏠</div>
            Home
        </a>
        <a href="check.php" class="nav-item">
            <div class="nav-icon">🩺</div>
            Cek
        </a>
        <a href="kalkulator.php" class="nav-item active">
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
                container.style.display = 'block';
                umurTahun.style.display = 'block';
            } else if (rumus === 'fried') {
                container.style.display = 'block';
                umurBulan.style.display = 'block';
            } else if (rumus === 'clark') {
                container.style.display = 'block';
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
            
            mgInputTablet.removeAttribute('name');
            mgInputSirup.removeAttribute('name');
            mlInputSirup.removeAttribute('name');

            if (sediaan === 'tablet') {
                container.style.display = 'block';
                tablet.style.display = 'block';
                mgInputTablet.setAttribute('name', 'kekuatan_mg');
            } else if (sediaan === 'sirup' || sediaan === 'drop') {
                container.style.display = 'block';
                sirup.style.display = 'block';
                mgInputSirup.setAttribute('name', 'kekuatan_mg');
                mlInputSirup.setAttribute('name', 'kekuatan_ml');
            } else {
                container.style.display = 'none';
            }
        }

        window.onload = function() {
            updateParameterInput();
            updateSediaanInput();
        };
    </script>
</body>
</html>
