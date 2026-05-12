<?php
// history.php
require_once 'config/db.php';

// Fetch All History
$stmt = $pdo->query("SELECT * FROM checks ORDER BY waktu_cek DESC");
$histories = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Cek - PediaDose</title>
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
            <li><a href="drugs.php">Data Obat</a></li>
            <li><a href="history.php" class="active">Riwayat Cek</a></li>
            <li><a href="about.php">Tentang</a></li>
        </ul>
        <div style="padding: 15px 20px;">
            <a href="mobile/history.php" style="display: block; background: var(--secondary-blue); color: white; text-align: center; padding: 10px; border-radius: 8px; text-decoration: none; font-weight: bold;">📱 Versi Mobile</a>
        </div>
        <div class="sidebar-footer">
            <p>&copy; 2026 PediaDose</p>
        </div>
    </div>

    <div class="main-content">
        <h1 class="page-title">Riwayat Pengecekan Dosis</h1>

        <div class="card">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Waktu Cek</th>
                            <th>Pasien</th>
                            <th>BB (kg)</th>
                            <th>Obat</th>
                            <th>Dosis Harian</th>
                            <th>Status Simulasi</th>
                            <th>AI Score</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($histories as $history): ?>
                            <?php
                                $badgeClass = 'badge-success';
                                if ($history['status'] == 'Overdosing') $badgeClass = 'badge-danger';
                                if ($history['status'] == 'Underdosing') $badgeClass = 'badge-warning';
                            ?>
                        <tr>
                            <td><?= date('d M Y, H:i', strtotime($history['waktu_cek'])) ?></td>
                            <?php
                                $years = floor($history['umur_bulan'] / 12);
                                $months = $history['umur_bulan'] % 12;
                                $ageStr = '';
                                if ($years > 0) $ageStr .= $years . ' thn ';
                                if ($months > 0 || $years == 0) $ageStr .= $months . ' bln';
                            ?>
                            <td><?= $history['nama_pasien'] ?> <br><small class="text-muted">(<?= trim($ageStr) ?>)</small></td>
                            <td><?= $history['berat_badan_kg'] ?></td>
                            <td><?= $history['nama_obat'] ?></td>
                            <td><?= $history['total_dosis_harian_mg'] ?> mg</td>
                            <td><span class="badge <?= $badgeClass ?>"><?= $history['status'] ?></span></td>
                            <td><strong><?= $history['ai_confidence_score'] ?>%</strong></td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if(empty($histories)): ?>
                        <tr><td colspan="7" style="text-align: center;">Belum ada riwayat pengecekan.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</body>
</html>
