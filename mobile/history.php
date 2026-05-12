<?php
// mobile/history.php
require_once '../config/db.php';

$stmt = $pdo->query("SELECT * FROM checks ORDER BY waktu_cek DESC");
$histories = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Riwayat - Mobile</title>
    <link rel="stylesheet" href="../assets/css/style.css?v=<?= time() ?>">
    <link rel="stylesheet" href="../assets/css/mobile-style.css?v=<?= time() ?>">
</head>
<body>

    <div class="mobile-topbar">
        <h1>Riwayat Cek</h1>
        <a href="../history.php" style="color: white; text-decoration: none; font-size: 0.8rem; background: rgba(255,255,255,0.2); padding: 5px 10px; border-radius: 5px;">💻 Desktop</a>
    </div>

    <div class="main-content">
        <?php foreach($histories as $history): ?>
            <?php
                $badgeClass = 'var(--alert-green)';
                $textColor = 'var(--alert-green-text)';
                if ($history['status'] == 'Overdosing') { $badgeClass = 'var(--alert-red)'; $textColor = 'var(--alert-red-text)'; }
                if ($history['status'] == 'Underdosing') { $badgeClass = 'var(--alert-yellow)'; $textColor = 'var(--alert-yellow-text)'; }

                $years = floor($history['umur_bulan'] / 12);
                $months = $history['umur_bulan'] % 12;
                $ageStr = '';
                if ($years > 0) $ageStr .= $years . 'thn ';
                if ($months > 0 || $years == 0) $ageStr .= $months . 'bln';
            ?>
        <div class="card" style="padding: 15px; margin-bottom: 10px; border-left: 5px solid <?= $textColor ?>;">
            <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
                <strong style="font-size: 1.1rem;"><?= $history['nama_pasien'] ?></strong>
                <span style="font-size: 0.8rem; color: var(--text-muted);"><?= date('d M Y', strtotime($history['waktu_cek'])) ?></span>
            </div>
            <div style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 10px;">
                <?= trim($ageStr) ?> | <?= $history['berat_badan_kg'] ?> kg
            </div>
            <div style="display: flex; justify-content: space-between; align-items: center; background: var(--light-bg); padding: 10px; border-radius: 8px;">
                <div>
                    <div style="font-weight: bold; color: var(--primary-blue);"><?= $history['nama_obat'] ?></div>
                    <div style="font-size: 0.8rem;"><?= $history['total_dosis_harian_mg'] ?> mg/hari</div>
                </div>
                <div style="text-align: right;">
                    <div style="background: <?= $badgeClass ?>; color: <?= $textColor ?>; padding: 3px 8px; border-radius: 12px; font-size: 0.75rem; font-weight: bold; display: inline-block;">
                        <?= $history['status'] ?>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
        <?php if(empty($histories)): ?>
            <div style="text-align: center; padding: 20px; color: var(--text-muted);">Belum ada riwayat.</div>
        <?php endif; ?>
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
        <a href="drugs.php" class="nav-item">
            <div class="nav-icon">💊</div>
            Obat
        </a>
        <a href="history.php" class="nav-item active">
            <div class="nav-icon">📋</div>
            Riwayat
        </a>
    </div>

</body>
</html>
