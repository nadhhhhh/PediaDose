<?php
// drugs.php
require_once 'config/db.php';

$message = '';
$alertClass = '';

// Handle Delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM drugs WHERE id = ?");
    if ($stmt->execute([$id])) {
        $message = "Data obat berhasil dihapus.";
        $alertClass = "alert-success";
    }
}

// Handle Add/Edit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? '';
    $nama_obat = htmlspecialchars($_POST['nama_obat']);
    $diagnosis = htmlspecialchars($_POST['diagnosis']);
    $dosis_dewasa = htmlspecialchars($_POST['dosis_dewasa'] ?? '');
    $dosis_dewasa_satuan = htmlspecialchars($_POST['dosis_dewasa_satuan'] ?? 'mg');
    $min_mg = (float)$_POST['min_mg_per_kg_per_day'];
    $max_mg = (float)$_POST['max_mg_per_kg_per_day'];
    $catatan = htmlspecialchars($_POST['catatan']);

    if (empty($id)) {
        // Create
        $stmt = $pdo->prepare("INSERT INTO drugs (nama_obat, diagnosis, dosis_dewasa, dosis_dewasa_satuan, min_mg_per_kg_per_day, max_mg_per_kg_per_day, catatan) VALUES (?, ?, ?, ?, ?, ?, ?)");
        if ($stmt->execute([$nama_obat, $diagnosis, $dosis_dewasa, $dosis_dewasa_satuan, $min_mg, $max_mg, $catatan])) {
            $message = "Data obat berhasil ditambahkan.";
            $alertClass = "alert-success";
        }
    } else {
        // Update
        $stmt = $pdo->prepare("UPDATE drugs SET nama_obat=?, diagnosis=?, dosis_dewasa=?, dosis_dewasa_satuan=?, min_mg_per_kg_per_day=?, max_mg_per_kg_per_day=?, catatan=? WHERE id=?");
        if ($stmt->execute([$nama_obat, $diagnosis, $dosis_dewasa, $dosis_dewasa_satuan, $min_mg, $max_mg, $catatan, $id])) {
            $message = "Data obat berhasil diperbarui.";
            $alertClass = "alert-success";
        }
    }
}

// Fetch Data for Edit Form
$editDrug = null;
if (isset($_GET['edit'])) {
    $id = (int)$_GET['edit'];
    $stmt = $pdo->prepare("SELECT * FROM drugs WHERE id = ?");
    $stmt->execute([$id]);
    $editDrug = $stmt->fetch();
}

// Fetch All Drugs
$stmt = $pdo->query("SELECT * FROM drugs ORDER BY nama_obat ASC");
$drugs = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Obat - PediaDose</title>
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
            <li><a href="kalkulator.php">Kalkulator Dosis</a></li>
            <li><a href="drugs.php" class="active">Data Obat</a></li>
            <li><a href="history.php">Riwayat Cek</a></li>
            <li><a href="about.php">Tentang</a></li>
        </ul>
        <div style="padding: 15px 20px;">
            <a href="mobile/drugs.php" style="display: block; background: var(--secondary-blue); color: white; text-align: center; padding: 10px; border-radius: 8px; text-decoration: none; font-weight: bold;">📱 Versi Mobile</a>
        </div>
        <div class="sidebar-footer">
            <p>&copy; 2026 PediaDose</p>
        </div>
    </div>

    <div class="main-content">
        <h1 class="page-title">Manajemen Data Obat</h1>

        <?php if ($message): ?>
            <div class="alert <?= $alertClass ?>">
                <?= $message ?>
            </div>
        <?php endif; ?>

        <div class="card">
            <h2><?= $editDrug ? 'Edit Data Obat' : 'Tambah Obat Baru' ?></h2>
            
            <?php if (!$editDrug): ?>
            <div class="tabs" style="margin-top: 15px;">
                <button type="button" class="tab-btn active" id="btn-smart" onclick="switchTab('smart')">Smart Input</button>
                <button type="button" class="tab-btn" id="btn-manual" onclick="switchTab('manual')">Input Manual</button>
            </div>
            
            <div id="tab-smart" class="tab-content active">
                <div class="file-upload-area" onclick="document.getElementById('leaflet_upload').click()">
                    <i style="font-size: 2.5rem; display: block; margin-bottom: 10px;">📄</i>
                    <p>Klik untuk Mengunggah Foto/Scan Leaflet Obat</p>
                    <p style="font-size: 0.85rem; color: var(--text-muted); margin-top: 5px;">Mendukung gambar (JPG, PNG) & PDF</p>
                    <input type="file" id="leaflet_upload" accept="image/*,.pdf" style="display: none;" onchange="extractData('file')">
                </div>
                
                <div style="text-align: center; margin: 15px 0; color: var(--text-muted); font-weight: bold;">ATAU</div>
                
                <div class="form-group">
                    <label>Paste Link Referensi Obat (misal: MIMS)</label>
                    <div style="display: flex; gap: 10px;">
                        <input type="text" id="mims_url" class="form-control" placeholder="https://www.mims.com/indonesia/drug/info/...">
                        <button type="button" class="btn" style="background: var(--secondary-blue); color: white;" onclick="extractData('url')">Ekstrak</button>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <div id="tab-manual" class="tab-content <?= $editDrug ? 'active' : '' ?>">
                <form action="drugs.php" method="POST" <?= $editDrug ? 'style="margin-top: 15px;"' : '' ?>>
                    <input type="hidden" name="id" value="<?= $editDrug['id'] ?? '' ?>">
                    
                    <div class="grid-2">
                        <div class="form-group">
                            <label>Nama Obat</label>
                            <input type="text" id="input_nama_obat" name="nama_obat" class="form-control" required value="<?= $editDrug['nama_obat'] ?? '' ?>">
                        </div>
                        <div class="form-group">
                            <label>Indikasi / Diagnosis</label>
                            <input type="text" id="input_diagnosis" name="diagnosis" class="form-control" value="<?= $editDrug['diagnosis'] ?? '' ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Dosis Dewasa</label>
                        <div style="display: flex; gap: 10px;">
                            <input type="text" id="input_dosis_dewasa" name="dosis_dewasa" class="form-control" placeholder="Nilai Dosis (misal: 500 atau 500-1000)" style="flex: 2;" value="<?= $editDrug['dosis_dewasa'] ?? '' ?>">
                            <select id="input_dosis_dewasa_satuan" name="dosis_dewasa_satuan" class="form-control" style="flex: 1;">
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
                    </div>

                    <div class="grid-2">
                        <div class="form-group">
                            <label>Min Dosis Pediatri (mg/kg/hari)</label>
                            <input type="number" step="0.01" id="input_min_mg" name="min_mg_per_kg_per_day" class="form-control" required value="<?= $editDrug['min_mg_per_kg_per_day'] ?? '' ?>">
                        </div>
                        <div class="form-group">
                            <label>Max Dosis Pediatri (mg/kg/hari)</label>
                            <input type="number" step="0.01" id="input_max_mg" name="max_mg_per_kg_per_day" class="form-control" required value="<?= $editDrug['max_mg_per_kg_per_day'] ?? '' ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Catatan / Panduan Klinis</label>
                        <textarea id="input_catatan" name="catatan" class="form-control" rows="3"><?= $editDrug['catatan'] ?? '' ?></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary"><?= $editDrug ? 'Simpan Perubahan' : 'Simpan Obat' ?></button>
                    <?php if ($editDrug): ?>
                        <a href="drugs.php" class="btn" style="background-color: var(--border-color); color: var(--text-dark);">Batal</a>
                    <?php endif; ?>
                </form>
            </div>
        </div>

        <div class="card">
            <h2>Daftar Obat Tersedia</h2>
            <div class="table-responsive" style="margin-top: 15px;">
                <table class="table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Obat</th>
                            <th>Dosis Dewasa</th>
                            <th>Dosis Pediatri (mg/kg/hari)</th>
                            <th>Indikasi</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($drugs as $index => $drug): ?>
                        <tr>
                            <td><?= $index + 1 ?></td>
                            <td><strong><?= $drug['nama_obat'] ?></strong></td>
                            <td><?= $drug['dosis_dewasa'] ? $drug['dosis_dewasa'] . ' ' . $drug['dosis_dewasa_satuan'] : '-' ?></td>
                            <td><?= $drug['min_mg_per_kg_per_day'] ?> - <?= $drug['max_mg_per_kg_per_day'] ?></td>
                            <td><?= $drug['diagnosis'] ?></td>
                            <td>
                                <a href="drugs.php?edit=<?= $drug['id'] ?>" class="btn btn-sm btn-primary">Edit</a>
                                <a href="drugs.php?delete=<?= $drug['id'] ?>" class="btn btn-sm btn-danger btn-delete">Hapus</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if(empty($drugs)): ?>
                        <tr><td colspan="5" style="text-align: center;">Belum ada data obat.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="ai-loading-overlay" id="aiOverlay">
        <div class="spinner"></div>
        <div class="ai-loading-text" id="aiLoadingText">AI Sedang Mengekstrak...</div>
    </div>

    <script src="assets/js/script.js"></script>
    <script>
        function switchTab(tab) {
            document.getElementById('btn-smart').classList.remove('active');
            document.getElementById('btn-manual').classList.remove('active');
            document.getElementById('tab-smart').classList.remove('active');
            document.getElementById('tab-manual').classList.remove('active');

            document.getElementById('btn-' + tab).classList.add('active');
            document.getElementById('tab-' + tab).classList.add('active');
        }

        function extractData(type) {
            const urlInput = document.getElementById('mims_url').value;
            const fileInput = document.getElementById('leaflet_upload').files[0];
            
            if (type === 'url' && !urlInput) {
                alert('Silakan paste link terlebih dahulu!');
                return;
            }

            const overlay = document.getElementById('aiOverlay');
            const loadingText = document.getElementById('aiLoadingText');
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

            fetch('api/extract.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                overlay.style.display = 'none';
                if (data.status === 'success') {
                    switchTab('manual');
                    document.getElementById('input_nama_obat').value = data.data.nama_obat || '';
                    document.getElementById('input_diagnosis').value = data.data.diagnosis || '';
                    document.getElementById('input_dosis_dewasa').value = data.data.dosis_dewasa || '';
                    document.getElementById('input_dosis_dewasa_satuan').value = data.data.dosis_dewasa_satuan || 'mg';
                    document.getElementById('input_min_mg').value = data.data.min_mg || '';
                    document.getElementById('input_max_mg').value = data.data.max_mg || '';
                    document.getElementById('input_catatan').value = data.data.catatan || '';
                    
                    document.getElementById('mims_url').value = '';
                    document.getElementById('leaflet_upload').value = '';
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
