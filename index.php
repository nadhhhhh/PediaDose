<?php
// index.php
require_once 'config/db.php';

// Get statistics
$stmt = $pdo->query("SELECT COUNT(*) FROM drugs");
$totalObat = $stmt->fetchColumn();

$stmt = $pdo->query("SELECT COUNT(*) FROM checks");
$totalCek = $stmt->fetchColumn();

$stmt = $pdo->query("SELECT COUNT(*) FROM checks WHERE status = 'Overdosing'");
$totalOverdosis = $stmt->fetchColumn();

$stmt = $pdo->query("SELECT COUNT(*) FROM checks WHERE status = 'Underdosing'");
$totalUnderdosis = $stmt->fetchColumn();

$stmt = $pdo->query("SELECT COUNT(*) FROM checks WHERE status = 'Normal'");
$totalNormal = $stmt->fetchColumn();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - PediaDose</title>
    <link rel="stylesheet" href="assets/css/style.css?v=<?= time() ?>">
</head>
<body>

    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-header">
            <h2>PediaDose</h2>
        </div>
        <ul class="sidebar-menu">
            <li><a href="index.php" class="active">Dashboard</a></li>
            <li><a href="check.php">Cek Dosis</a></li>
            <li><a href="kalkulator.php">Kalkulator Dosis</a></li>
            <li><a href="drugs.php">Data Obat</a></li>
            <li><a href="history.php">Riwayat Cek</a></li>
            <li><a href="about.php">Tentang</a></li>
        </ul>
        <div style="padding: 15px 20px;">
            <a href="mobile/index.php" style="display: block; background: var(--secondary-blue); color: white; text-align: center; padding: 10px; border-radius: 8px; text-decoration: none; font-weight: bold;">📱 Versi Mobile</a>
        </div>
        <div class="sidebar-footer">
            <p>&copy; 2026 PediaDose</p>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <h1 class="page-title">Dashboard</h1>
        
        <div class="card">
            <div style="display: flex; gap: 20px; align-items: center; flex-wrap: wrap;">
                <div style="flex: 1; min-width: 200px;">
                    <h2>Selamat Datang di PediaDose</h2>
                    <p style="margin-top: 10px; color: var(--text-muted);">Sistem simulasi pengecekan kewajaran dosis obat pediatrik berdasarkan parameter berat badan dan panduan klinis dasar.</p>
                </div>
                <div style="display: flex; gap: 15px;">
                    <img src="assets/img/pill.png" alt="Pill" style="width: 80px; height: 80px; border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1);">
                    <img src="assets/img/syrup.png" alt="Syrup" style="width: 80px; height: 80px; border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1);">
                    <img src="assets/img/blister.png" alt="Blister" style="width: 80px; height: 80px; border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1);">
                </div>
            </div>
        </div>

        <div class="dashboard-grid">
            <div class="stat-card">
                <h3>Total Database Obat</h3>
                <div class="value"><?= $totalObat ?></div>
            </div>
            <div class="stat-card">
                <h3>Total Pengecekan (Simulasi)</h3>
                <div class="value"><?= $totalCek ?></div>
            </div>
            <div class="stat-card" style="border-left-color: var(--alert-red-text);">
                <h3>Kasus Overdosing</h3>
                <div class="value" style="color: var(--alert-red-text);"><?= $totalOverdosis ?></div>
            </div>
            <div class="stat-card" style="border-left-color: var(--alert-yellow-text);">
                <h3>Kasus Underdosing</h3>
                <div class="value" style="color: var(--alert-yellow-text);"><?= $totalUnderdosis ?></div>
            </div>
        </div>

        <div class="card">
            <h3>Peringatan Medis</h3>
            <p style="margin-top: 10px; line-height: 1.6;">
                Aplikasi ini hanyalah <strong>simulasi edukasi</strong>. Segala hasil perhitungan dan label "AI Confidence Score" dibuat berdasarkan <em>rule-based</em> sederhana dan algoritma simulasi (angka acak) untuk keperluan demonstrasi. Aplikasi ini <strong>tidak boleh</strong> digunakan sebagai pengganti keputusan klinis dari tenaga medis profesional (Dokter/Apoteker).
            </p>
        </div>
    </div>

    <script src="assets/js/script.js"></script>
</body>
</html>
