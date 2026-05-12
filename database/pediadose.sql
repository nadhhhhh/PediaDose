-- Database: `pediadose`
--

CREATE DATABASE IF NOT EXISTS `pediadose` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `pediadose`;

-- --------------------------------------------------------

--
-- Table structure for table `drugs`
--

CREATE TABLE `drugs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama_obat` varchar(255) NOT NULL,
  `diagnosis` varchar(255) DEFAULT NULL,
  `dosis_dewasa` varchar(255) DEFAULT NULL,
  `dosis_dewasa_satuan` varchar(50) DEFAULT 'mg',
  `min_mg_per_kg_per_day` decimal(10,2) NOT NULL,
  `max_mg_per_kg_per_day` decimal(10,2) NOT NULL,
  `catatan` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `drugs`
--

INSERT INTO `drugs` (`id`, `nama_obat`, `diagnosis`, `min_mg_per_kg_per_day`, `max_mg_per_kg_per_day`, `catatan`) VALUES
(1, 'Amoxicillin', 'Infeksi Saluran Napas, Otitis Media', 20.00, 90.00, 'Dosis dibagi tiap 8 atau 12 jam'),
(2, 'Paracetamol', 'Demam, Nyeri Ringan', 40.00, 60.00, 'Dosis umumnya 10-15 mg/kgBB per kali pemberian, maks 4x sehari'),
(3, 'Ibuprofen', 'Demam, Nyeri, Inflamasi', 20.00, 40.00, 'Dosis umumnya 5-10 mg/kgBB per kali pemberian, maks 4x sehari'),
(4, 'Cefixime', 'Infeksi bakteri rentan', 8.00, 12.00, 'Dosis dapat diberikan dosis tunggal atau dibagi tiap 12 jam'),
(5, 'Azithromycin', 'Infeksi bakteri (respirasi, kulit)', 10.00, 12.00, 'Hari ke-1: 10mg/kg, Hari ke 2-5: 5mg/kg. Atau 10mg/kg selama 3 hari');

-- --------------------------------------------------------

--
-- Table structure for table `checks`
--

CREATE TABLE `checks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama_pasien` varchar(255) NOT NULL,
  `umur_bulan` int(11) NOT NULL,
  `berat_badan_kg` decimal(5,2) NOT NULL,
  `diagnosis` varchar(255) DEFAULT NULL,
  `nama_obat` varchar(255) NOT NULL,
  `dosis_per_pemberian_mg` decimal(10,2) NOT NULL,
  `frekuensi_per_hari` int(11) NOT NULL,
  `total_dosis_harian_mg` decimal(10,2) NOT NULL,
  `status` enum('Normal','Underdosing','Overdosing') NOT NULL,
  `ai_confidence_score` int(11) DEFAULT NULL,
  `waktu_cek` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
