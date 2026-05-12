<?php
// mobile/drugs.php
require_once '../config/db.php';

$message = '';
$alertClass = '';

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM drugs WHERE id = ?");
    if ($stmt->execute([$id])) {
        $message = "Obat dihapus.";
        $alertClass = "alert-success";
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? '';
    $nama_obat = htmlspecialchars($_POST['nama_obat']);
    $diagnosis = htmlspecialchars($_POST['diagnosis']);
    $dosis_dewasa = htmlspecialchars($_POST['dosis_dewasa'] ?? '');
    $dosis_dewasa_satuan = htmlspecialchars($_POST['dosis_dewasa_satuan'] ?? 'mg');
    $min_mg = (float)$_POST['min_mg_per_kg_per_day'];
    $max_mg = (float)$_POST['max_mg_per_kg_per_day'];

    if (empty($id)) {
        $stmt = $pdo->prepare("INSERT INTO drugs (nama_obat, diagnosis, dosis_dewasa, dosis_dewasa_satuan, min_mg_per_kg_per_day, max_mg_per_kg_per_day) VALUES (?, ?, ?, ?, ?, ?)");
        if ($stmt->execute([$nama_obat, $diagnosis, $dosis_dewasa, $dosis_dewasa_satuan, $min_mg, $max_mg])) {
            $message = "Obat ditambah.";
            $alertClass = "alert-success";
        }
    } else {
        $stmt = $pdo->prepare("UPDATE drugs SET nama_obat=?, diagnosis=?, dosis_dewasa=?, dosis_dewasa_satuan=?, min_mg_per_kg_per_day=?, max_mg_per_kg_per_day=? WHERE id=?");
        if ($stmt->execute([$nama_obat, $diagnosis, $dosis_dewasa, $dosis_dewasa_satuan, $min_mg, $max_mg, $id])) {
            $message = "Obat diupdate.";
            $alertClass = "alert-success";
        }
    }
}

$editDrug = null;
if (isset($_GET['edit'])) {
    $id = (int)$_GET['edit'];
    $stmt = $pdo->prepare("SELECT * FROM drugs WHERE id = ?");
    $stmt->execute([$id]);
    $editDrug = $stmt->fetch();
}

$stmt = $pdo->query("SELECT * FROM drugs ORDER BY nama_obat ASC");
$drugs = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Obat - Mobile</title>
    <link rel="stylesheet" href="../assets/css/style.css?v=<?= time() ?>">
    <link rel="stylesheet" href="../assets/css/mobile-style.css?v=<?= time() ?>">
</head>
<body>

    <div class="mobile-topbar">
        <h1>Data Obat</h1>
        <a href="../drugs.php" style="color: white; text-decoration: none; font-size: 0.8rem; background: rgba(255,255,255,0.2); padding: 5px 10px; border-radius: 5px;">💻 Desktop</a>
    </div>

    <div class="main-content">
        <?php if ($message): ?>
            <div class="alert <?= $alertClass ?>" style="padding: 10px; font-size: 0.9rem;">
                <?= $message ?>
            </div>
        <?php endif; ?>

        <div class="card">
            <h2 style="font-size: 1.1rem; margin-bottom: 10px;"><?= $editDrug ? 'Edit Obat' : 'Tambah Obat Baru' ?></h2>
            
            <?php if (!$editDrug): ?>
            <div class="tabs" style="margin-bottom: 15px;">
                <button type="button" class="tab-btn active" id="btn-smart-m" onclick="switchTabMobile('smart-m')" style="padding: 8px 10px; font-size: 0.9rem;">Smart Input</button>
                <button type="button" class="tab-btn" id="btn-manual-m" onclick="switchTabMobile('manual-m')" style="padding: 8px 10px; font-size: 0.9rem;">Manual</button>
            </div>

            <div id="tab-smart-m" class="tab-content active">
                <div class="file-upload-area" onclick="document.getElementById('leaflet_upload_m').click()" style="padding: 20px;">
                    <i style="font-size: 2rem; margin-bottom: 5px;">📄</i>
                    <p style="font-size: 0.9rem;">Tap Upload Foto Leaflet</p>
                    <input type="file" id="leaflet_upload_m" accept="image/*,.pdf" style="display: none;" onchange="extractDataMobile('file')">
                </div>
                
                <div style="text-align: center; margin: 10px 0; color: var(--text-muted); font-weight: bold; font-size: 0.9rem;">ATAU</div>
                
                <div class="form-group">
                    <label style="font-size: 0.9rem;">Paste Link MIMS</label>
                    <div style="display: flex; gap: 5px;">
                        <input type="text" id="mims_url_m" class="form-control" placeholder="https://mims.com/..." style="flex: 2;">
                        <button type="button" class="btn" style="background: var(--secondary-blue); color: white; padding: 10px; font-size: 0.85rem;" onclick="extractDataMobile('url')">Ekstrak</button>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <div id="tab-manual-m" class="tab-content <?= $editDrug ? 'active' : '' ?>">
                <form action="drugs.php" method="POST">
                    <input type="hidden" name="id" value="<?= $editDrug['id'] ?? '' ?>">
                    
                    <div class="form-group">
                        <input type="text" id="input_nama_obat_m" name="nama_obat" class="form-control" required placeholder="Nama Obat" value="<?= $editDrug['nama_obat'] ?? '' ?>">
                    </div>
                    <div class="form-group">
                        <input type="text" id="input_diagnosis_m" name="diagnosis" class="form-control" placeholder="Indikasi" value="<?= $editDrug['diagnosis'] ?? '' ?>">
                    </div>
                    <div class="form-group" style="display: flex; gap: 5px;">
                        <input type="text" id="input_dosis_dewasa_m" name="dosis_dewasa" class="form-control" placeholder="Dosis Dewasa (Nilai)" value="<?= $editDrug['dosis_dewasa'] ?? '' ?>" style="flex: 2;">
                        <select id="input_dosis_dewasa_satuan_m" name="dosis_dewasa_satuan" class="form-control" style="flex: 1;">
                            <?php $satuan_dewasa = $editDrug['dosis_dewasa_satuan'] ?? 'mg'; ?>
                            <option value="mg" <?= $satuan_dewasa == 'mg' ? 'selected' : '' ?>>mg</option>
                            <option value="gr" <?= $satuan_dewasa == 'gr' ? 'selected' : '' ?>>gr</option>
                            <option value="mcg" <?= $satuan_dewasa == 'mcg' ? 'selected' : '' ?>>mcg</option>
                            <option value="ml" <?= $satuan_dewasa == 'ml' ? 'selected' : '' ?>>ml</option>
                            <option value="tablet" <?= $satuan_dewasa == 'tablet' ? 'selected' : '' ?>>tablet</option>
                            <option value="kapsul" <?= $satuan_dewasa == 'kapsul' ? 'selected' : '' ?>>kapsul</option>
                            <option value="tetes" <?= $satuan_dewasa == 'tetes' ? 'selected' : '' ?>>tetes</option>
                        </select>
                    </div>

                    <div style="display: flex; gap: 10px; margin-bottom: 15px;">
                        <div style="flex: 1;">
                            <small style="color: var(--text-muted);">Min Pediatri (mg/kg)</small>
                            <input type="number" step="0.01" id="input_min_mg_m" name="min_mg_per_kg_per_day" class="form-control" required value="<?= $editDrug['min_mg_per_kg_per_day'] ?? '' ?>">
                        </div>
                        <div style="flex: 1;">
                            <small style="color: var(--text-muted);">Max Pediatri (mg/kg)</small>
                            <input type="number" step="0.01" id="input_max_mg_m" name="max_mg_per_kg_per_day" class="form-control" required value="<?= $editDrug['max_mg_per_kg_per_day'] ?? '' ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <textarea id="input_catatan_m" name="catatan" class="form-control" placeholder="Catatan/Panduan" rows="2"><?= $editDrug['catatan'] ?? '' ?></textarea>
                    </div>

                    <div style="display: flex; gap: 10px;">
                        <button type="submit" class="btn btn-primary" style="flex: 2; padding: 10px;"><?= $editDrug ? 'Simpan' : 'Tambah' ?></button>
                        <?php if ($editDrug): ?>
                            <a href="drugs.php" class="btn" style="flex: 1; padding: 10px; background-color: #e0e0e0; color: #333; text-align: center;">Batal</a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>

        <h3 style="margin-bottom: 10px; font-size: 1rem;">Daftar Obat</h3>
        <?php foreach($drugs as $drug): ?>
        <div class="card" style="padding: 15px; margin-bottom: 10px;">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <strong style="font-size: 1.1rem; color: var(--primary-blue);"><?= $drug['nama_obat'] ?></strong>
                    <div style="font-size: 0.8rem; color: var(--text-muted); margin-top: 5px;">
                        Dewasa: <?= $drug['dosis_dewasa'] ? $drug['dosis_dewasa'] . ' ' . $drug['dosis_dewasa_satuan'] : '-' ?>
                    </div>
                    <div style="font-size: 0.8rem; color: var(--text-muted);">
                        Pediatri: <?= $drug['min_mg_per_kg_per_day'] ?> - <?= $drug['max_mg_per_kg_per_day'] ?> mg/kg/hr
                    </div>
                </div>
                <div style="display: flex; gap: 5px;">
                    <a href="drugs.php?edit=<?= $drug['id'] ?>" style="background: var(--alert-yellow); padding: 5px 10px; border-radius: 5px; text-decoration: none; font-size: 0.8rem;">✏️</a>
                    <a href="drugs.php?delete=<?= $drug['id'] ?>" class="btn-delete" style="background: var(--alert-red); padding: 5px 10px; border-radius: 5px; text-decoration: none; font-size: 0.8rem;">🗑️</a>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
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
        <a href="kalkulator.php" class="nav-item">
            <div class="nav-icon">🧮</div>
            Kalkulator
        </a>
        <a href="drugs.php" class="nav-item active">
            <div class="nav-icon">💊</div>
            Obat
        </a>
        <a href="history.php" class="nav-item">
            <div class="nav-icon">📋</div>
            Riwayat
        </a>
    </div>

    <div class="ai-loading-overlay" id="aiOverlayM">
        <div class="spinner"></div>
        <div class="ai-loading-text" id="aiLoadingTextM" style="font-size: 1rem; text-align: center; padding: 0 20px;">AI Sedang Mengekstrak...</div>
    </div>

    <script src="../assets/js/script.js"></script>
    <script>
        function switchTabMobile(tab) {
            document.getElementById('btn-smart-m').classList.remove('active');
            document.getElementById('btn-manual-m').classList.remove('active');
            document.getElementById('tab-smart-m').classList.remove('active');
            document.getElementById('tab-manual-m').classList.remove('active');

            document.getElementById('btn-' + tab).classList.add('active');
            document.getElementById('tab-' + tab).classList.add('active');
        }

        function extractDataMobile(type) {
            const urlInput = document.getElementById('mims_url_m').value;
            const fileInput = document.getElementById('leaflet_upload_m').files[0];
            
            if (type === 'url' && !urlInput) {
                alert('Silakan paste link terlebih dahulu!');
                return;
            }

            const overlay = document.getElementById('aiOverlayM');
            const loadingText = document.getElementById('aiLoadingTextM');
            overlay.style.display = 'flex';

            if (type === 'file') {
                loadingText.innerText = 'Sistem Sedang Membaca Leaflet...';
            } else {
                loadingText.innerText = 'Sistem Sedang Mengekstrak Web...';
            }

            let formData = new FormData();
            formData.append('type', type);
            if (type === 'url') formData.append('url', urlInput);
            if (type === 'file' && fileInput) formData.append('file', fileInput);

            fetch('../api/extract.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                overlay.style.display = 'none';
                if (data.status === 'success') {
                    switchTabMobile('manual-m');
                    document.getElementById('input_nama_obat_m').value = data.data.nama_obat || '';
                    document.getElementById('input_diagnosis_m').value = data.data.diagnosis || '';
                    document.getElementById('input_dosis_dewasa_m').value = data.data.dosis_dewasa || '';
                    document.getElementById('input_dosis_dewasa_satuan_m').value = data.data.dosis_dewasa_satuan || 'mg';
                    document.getElementById('input_min_mg_m').value = data.data.min_mg || '';
                    document.getElementById('input_max_mg_m').value = data.data.max_mg || '';
                    document.getElementById('input_catatan_m').value = data.data.catatan || '';
                    
                    document.getElementById('mims_url_m').value = '';
                    document.getElementById('leaflet_upload_m').value = '';
                } else {
                    alert('Gagal mengekstrak: ' + data.message);
                }
            })
            .catch(error => {
                overlay.style.display = 'none';
                alert('Terjadi kesalahan jaringan.');
                console.error(error);
            });
        }
    </script>
</body>
</html>
