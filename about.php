<?php
// about.php
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tentang - PediaDose</title>
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
            <li><a href="history.php">Riwayat Cek</a></li>
            <li><a href="about.php" class="active">Tentang</a></li>
        </ul>
        <div style="padding: 15px 20px;">
            <a href="mobile/about.php" style="display: block; background: var(--secondary-blue); color: white; text-align: center; padding: 10px; border-radius: 8px; text-decoration: none; font-weight: bold;">📱 Versi Mobile</a>
        </div>
        <div class="sidebar-footer">
            <p>&copy; 2026 PediaDose</p>
        </div>
    </div>

    <div class="main-content">
        <h1 class="page-title">Tentang Aplikasi</h1>

        <div class="grid-2">
            <div class="card">
                <h2>PediaDose</h2>
                <p style="margin-top: 15px; line-height: 1.6;">
                    PediaDose adalah aplikasi prototipe simulasi yang dirancang untuk membantu memvisualisasikan bagaimana teknologi *rule-based* dan integrasi kecerdasan buatan dapat digunakan untuk memverifikasi kewajaran dosis obat pediatrik.
                </p>
                <p style="margin-top: 10px; line-height: 1.6;">
                    Aplikasi ini menghitung total dosis harian berdasarkan parameter berat badan anak dan membandingkannya dengan batas minimum dan maksimum harian yang diizinkan untuk obat tertentu.
                </p>
            </div>

            <div class="card" style="border-top: 5px solid var(--alert-red-text);">
                <h2 style="color: var(--alert-red-text);">Disclaimer Medis!</h2>
                <div class="alert alert-danger" style="margin-top: 15px;">
                    <strong>PERHATIAN: APLIKASI INI HANYA UNTUK TUJUAN EDUKASI DAN DEMONSTRASI.</strong>
                </div>
                <ul style="margin-left: 20px; line-height: 1.6;">
                    <li>Aplikasi ini <strong>bukan</strong> perangkat medis.</li>
                    <li>Sistem ini <strong>tidak</strong> boleh digunakan sebagai pengganti nasihat medis, diagnosis, atau pengambilan keputusan klinis oleh tenaga medis berlisensi.</li>
                    <li>Label "AI Confidence Score" hanyalah simulasi (angka acak) dan tidak merepresentasikan model Machine Learning sungguhan.</li>
                    <li>Pembuat aplikasi tidak bertanggung jawab atas kerugian atau cedera yang timbul dari penggunaan informasi di dalam aplikasi ini.</li>
                </ul>
            </div>
        </div>
    </div>

</body>
</html>
