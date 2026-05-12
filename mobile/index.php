<?php
// mobile/index.php
require_once '../config/db.php';

// Get statistics
$stmt = $pdo->query("SELECT COUNT(*) FROM drugs");
$totalObat = $stmt->fetchColumn();

$stmt = $pdo->query("SELECT COUNT(*) FROM checks");
$totalCek = $stmt->fetchColumn();

$stmt = $pdo->query("SELECT COUNT(*) FROM checks WHERE status = 'Overdosing'");
$totalOverdosis = $stmt->fetchColumn();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>PediaDose Mobile</title>
    <link rel="stylesheet" href="../assets/css/style.css?v=<?= time() ?>">
    <link rel="stylesheet" href="../assets/css/mobile-style.css?v=<?= time() ?>">
</head>
<body>

    <div class="mobile-topbar">
        <h1>PediaDose</h1>
        <a href="../index.php" style="color: white; text-decoration: none; font-size: 0.8rem; background: rgba(255,255,255,0.2); padding: 5px 10px; border-radius: 5px;">💻 Desktop</a>
    </div>

    <div class="main-content">
        <div class="card">
            <div style="display: flex; gap: 15px; align-items: center; flex-direction: column; text-align: center;">
                <div>
                    <h2 style="font-size: 1.2rem;">Selamat Datang!</h2>
                    <p style="margin-top: 5px; color: var(--text-muted); font-size: 0.9rem;">Sistem pengecekan dosis pediatrik.</p>
                </div>
                <div style="display: flex; gap: 10px;">
                    <img src="../assets/img/pill.png" alt="Pill" style="width: 60px; height: 60px; border-radius: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                    <img src="../assets/img/syrup.png" alt="Syrup" style="width: 60px; height: 60px; border-radius: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                    <img src="../assets/img/blister.png" alt="Blister" style="width: 60px; height: 60px; border-radius: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                </div>
            </div>
        </div>

        <div class="grid-2">
            <div class="stat-card">
                <h3>Total Database Obat</h3>
                <div class="value"><?= $totalObat ?></div>
            </div>
            <div class="stat-card">
                <h3>Total Pengecekan</h3>
                <div class="value"><?= $totalCek ?></div>
            </div>
            <div class="stat-card" style="border-left-color: var(--alert-red-text);">
                <h3>Kasus Overdosing</h3>
                <div class="value" style="color: var(--alert-red-text);"><?= $totalOverdosis ?></div>
            </div>
        </div>
    </div>

    <!-- Bottom Navigation -->
    <div class="bottom-nav">
        <a href="index.php" class="nav-item active">
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
        <a href="history.php" class="nav-item">
            <div class="nav-icon">📋</div>
            Riwayat
        </a>
    </div>

    <script src="../assets/js/script.js"></script>
</body>
</html>
