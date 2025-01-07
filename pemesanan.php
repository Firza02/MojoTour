<?php
require 'koneksi.php';

$sql = "SELECT * FROM tb_destinasi";
$result = $conn->query($sql);
$total_bayar = 0;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama = $_POST['nama'];
    $email = $_POST['email'];
    $tanggal = $_POST['tanggal'];
    $tipe_hari = $_POST['tipe_hari'];
    $destinasi = $_POST['destinasi'];
    $jumlah = $_POST['jumlah'];
    $metode_pembayaran = $_POST['metode_pembayaran'];
}

     // Handle file upload
     $target_dir = "uploads/";
     $target_file = $target_dir . basename($_FILES["bukti_pembayaran"]["name"]);
     $uploadOk = 1;
     $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
 
     // Check if image file is a actual image or fake image
     $check = getimagesize($_FILES["bukti_pembayaran"]["tmp_name"]);
     if($check !== false) {
         $uploadOk = 1;
     } else {
         echo "File is not an image.";
         $uploadOk = 0;
     }

     // Check if file already exists
    if (file_exists($target_file)) {
        echo "Sorry, file already exists.";
        $uploadOk = 0;
    }

    // Check file size
    if ($_FILES["bukti_pembayaran"]["size"] > 500000) {
        echo "Sorry, your file is too large.";
        $uploadOk = 0;
    }

    // Allow certain file formats
    if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
    && $imageFileType != "gif" ) {
        echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
        $uploadOk = 0;
    }

    // Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 0) {
        echo "Sorry, your file was not uploaded.";
    // if everything is ok, try to upload file
    } else {
        if (move_uploaded_file($_FILES["bukti_pembayaran"]["tmp_name"], $target_file)) {
            // File successfully uploaded
            $bukti_pembayaran = basename($_FILES["bukti_pembayaran"]["name"]);
        } else {
            echo "Sorry, there was an error uploading your file.";
            exit;
        }
    }

    // Mengambil harga destinasi dari database berdasarkan destinasi yang dipilih
    $sql_harga = "SELECT harga_destinasi FROM tb_destinasi WHERE id_destinasi = $destinasi";
    $result_harga = $conn->query($sql_harga);

    if ($result_harga->num_rows > 0) {
        $row_harga = $result_harga->fetch_assoc();
        $harga_destinasi = $row_harga["harga_destinasi"];
        $total_bayar = $jumlah * $harga_destinasi;

    $sql = "INSERT INTO pemesanan (nama, email, tanggal, tipe_hari, destinasi, jumlah, metode_pembayaran, total_bayar, bukti_pembayaran)
            VALUES ('$nama', '$email', '$tanggal', '$tipe_hari', '$destinasi', $jumlah, '$metode_pembayaran', $total_bayar, '$bukti_pembayaran')";

    if ($conn->query($sql) === TRUE) {
        $last_id = $conn->insert_id;
        $sql = "SELECT * FROM pemesanan WHERE id_pemesanan   = $last_id";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
        } else {
            echo "No booking found.";
            exit;
        }
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
        exit;
    }
}


$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<meta charset="utf-8">
    <title>MojoTour</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="" name="keywords">
    <meta content="" name="description">

    <!-- Favicon -->
    <link href="img/about1.png" rel="icon">

    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Heebo:wght@400;500;600&family=Nunito:wght@600;700;800&display=swap" rel="stylesheet">

    <!-- Icon Font Stylesheet -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Libraries Stylesheet -->
    <link href="lib/animate/animate.min.css" rel="stylesheet">
    <link href="lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">
    <link href="lib/tempusdominus/css/tempusdominus-bootstrap-4.min.css" rel="stylesheet" />

    <!-- Customized Bootstrap Stylesheet -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- Template Stylesheet -->
    <link href="css/style.css" rel="stylesheet">
    <style>
        /* Navbar styles */
        .navbar {
            transition: all 0.3s;
            border: none; /* Menghapus border */
        }

        .navbar .nav-link,
        .navbar .btn {
            color: #ffffff !important;
            transition: color 0.3s;
        }

        .navbar .nav-link:hover,
        .navbar .nav-link.active {
            color: #FFA500 !important;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.5);
        }

        .navbar .nav-link {
            position: relative;
        }

        .navbar .nav-link::after {
            content: '';
            position: absolute;
            left: 50%;
            bottom: -5px;
            width: 0;
            height: 2px;
            background: #FFA500;
            transition: width 0.3s, left 0.3s;
        }

        .navbar .nav-link:hover::after {
            width: 100%;
            left: 0;
        }

        .navbar .dropdown-menu {
            background: #125993;
            border: none; /* Menghapus border */
            border-radius: 0;
        }

        .navbar .dropdown-item {
            color: #ffffff;
            transition: 0.3s;
            background: #FFA500
        }

        .navbar .dropdown-item:hover {
            background: #FFA500;
            color: #000000;
        }

        .btn-primary {
            background-color: #FFA500;
            border: none;
            color: #ffffff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .btn-primary:hover {
            background-color: #125993;
        }

        /* Navbar link spacing */
        .navbar-nav .nav-item {
            margin-left: 1rem;
            margin-right: 1rem;
        }

        /* Hero section */
        .hero-header {
            background-image: url(img/home-slide-1.jpg);
            background-size: cover;
            background-position: center;
            position: relative;
            padding: 150px 0;
            color: #ffffff;
            text-align: center;
        }

        .hero-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
        }

        .hero-header .container {
            position: relative;
            z-index: 1;
        }

        .hero-header h1 {
            font-size: 4rem;
            font-weight: bold;
            color: #125993;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
        }

        .hero-header p {
            font-size: 1.5rem;
            color: #ffffff;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.5);
        }

        .hero-header .btn {
            border: 2px solid #ffffff;
            color: #ffffff;
            transition: background-color 0.3s, border-color 0.3s;
        }

        .hero-header .btn:hover {
            background-color: #FFA500;
            border-color: #FFA500;
        }

        .section-title {
            display: inline-block;
            background-color: #ffffff;
            color: #125993; /* Warna biru navy */
            padding: 5px 10px;
            border-radius: 5px;
            margin-bottom: 10px;
        }

        .section-title.text-start {
            text-align: start;
        }

        .section-title.text-primary {
            color: #125993; /* Warna biru navy */
        }

        .section-title.bg-white {
            background-color: #ffffff;
        }

        .section-title.text-white {
            color: #ffffff;
        }

        .btn-primary {
            background-color: #125993; /* Warna biru navy */
            border-color: #125993; /* Warna biru navy */
            border-radius:40px;
        }

        .btn-primary:hover {
            background-color: #FFA500; /* Warna orange */
            border-color: #FFA500; /* Warna orange */
        }

        .text-primaryy {
            color: #125993; /* Warna biru navy */
        }

        .text-orange {
            color: #FFA500; /* Warna orange */
        }
        .bg-primaryy {
            color: #125993; /* Warna orange */
        }
        .bg-primary-globe {
        background-color: #FFA500; /* Warna orange */
    }

    .bg-primary-dollar {
        background-color: #FFA500; /* Warna orange */
    }

    .bg-primary-plane {
        background-color: #FFA500; /* Warna orange */
    }

    /* pdf */
    body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        .content {
            margin-bottom: 20px;
        }
        .print-button {
            padding: 10px 20px;
            font-size: 16px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .print-button:hover {
            background-color: #0056b3;
        }
    
    </style>
    </head>

<body>
    <div class="container-xxl py-5">
        <div class="container">
            <div class="row g-5 justify-content-center">
                <div class="col-lg-6 wow fadeInUp" data-wow-delay="0.1s" style="min-height: 0px;">
                    <div class="position-relative h-95">
                        <img src="img/about2.png" width="250px" alt="MojoTour Image" class="img-fluid">
                    </div>
                </div>
                <div class="col-lg-6 form-container">
                    <?php if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($row)): ?>
                        <h2>DETAIL TIKET</h2>
                        <p>Tanggal Kunjungan: <?php echo $row['tanggal']; ?></p>
                        <p>Destinasi / Packages: <?php echo $row['destinasi']; ?></p>
                        <p>Tipe Hari: <?php echo $row['tipe_hari']; ?></p>
                        <p>Total Wisatawan: <?php echo $row['jumlah']; ?></p>
                        <p>Metode Pembayaran: <?php echo $row['metode_pembayaran']; ?></p>
                        <p>Total Bayar: Rp <?php echo number_format($row['total_bayar'], 0, ',', '.'); ?></p>
                        <p><img src="img/QR.jpg" width="150px"></p>
                        <p><button class="print-button" onclick="printPage()">Cetak PDF</button><script>
                        function printPage() {
                         window.print();
                        }
                        </script><p>
                    <?php else: ?>
                        <form action="booking.php" method="POST">
                            <div class="form-group mt-3">
                                <label for="nama">Nama</label>
                                <input type="text" id="nama" name="nama" class="form-control" required>
                            </div>
                            <div class="form-group mt-3">
                                <label for="email">Email</label>
                                <input type="email" id="email" name="email" class="form-control" required>
                            </div>
                            <div class="form-group mt-3">
                                <label for="tanggal">Tanggal Kunjungan</label>
                                <input type="date" id="tanggal" name="tanggal" class="form-control" required>
                            </div>
                            <div class="form-group mt-3">
                                <label for="tipe_hari">Tipe Hari</label>
                                <select id="tipe_hari" name="tipe_hari" class="form-control" required>
                                    <option value="">Pilih Tipe Hari</option>
                                    <option value="Weekday">Weekday</option>
                                    <option value="Weekend">Weekend</option>
                                </select>
                            </div>
                            <div class="form-group mt-3">
                                <label for="destinasi">Pilih Destinasi / Packages</label>
                                <select id="destinasi" name="destinasi" class="form-control" required>
                                    <option value="">Pilih Destinasi</option>
                                    <option value="Museum Gubug Wayang">Museum Gubug Wayang</option>
                                    <option value="Alas Venus">Alas Venus</option>
                                    <option value="Gunung Bekel">Gunung Bekel</option>
                                    <option value="TOS Rafting">TOS Rafting</option>
                                    <option value="Wisata Klurak">Wisata Klurak</option>
                                    <option value="Wisata Kembangbelor">Wisata Kembangbelor</option>
                                    <option value="Putuk Pulosari">Putuk Pulosari</option>
                                </select>
                            </div>
                            <div class="form-group mt-3">
                                <label for="total_wisatawan">Total Wisatawan</label>
                                <input type="number" id="total_wisatawan" name="total_wisatawan" min="1" value="1" class="form-control" required>
                            </div>
                            <div class="form-group mt-3">
                                <label for="metode_pembayaran">Metode Pembayaran</label>
                                <select id="metode_pembayaran" name="metode_pembayaran" class="form-control" required>
                                    <option value="QRIS">QRIS</option>
                                    <option value="Transfer Bank">Transfer Bank</option>
                                    <option value="E-Wallet">E-Wallet</option>
                                </select>
                            </div>
                            <div class="form-group mt-3">
                                <label for="payment_proof">Bukti Pembayaran:</label><br>
                                <input type="file" id="payment_proof" name="payment_proof" required><br><br>
                            </div>
                            <div class="form-check mt-3">
                                <input type="checkbox" id="agreement" name="agreement" class="form-check-input" required>
                                <label for="agreement" class="form-check-label">Saya menyetujui bahwa tiket yang sudah dibeli tidak bisa dilakukan pembatalan dengan alasan apapun. Baik itu kesalahan dari calon pengunjung maupun pengelola.</label>
                            </div>
                            <div class="form-group mt-3">
                                <input type="submit" value="Pesan Sekarang" name="pemesanan" class="btn btn-primary rounded-pill py-2 px-4 ms-3">
                            </div>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
