<?php
$servername = "localhost";
$username = "root"; // replace with your database username
$password = ""; // replace with your database password
$dbname = "pemesanan";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama = $_POST['nama'];
    $email = $_POST['email'];
    $tanggal = $_POST['tanggal'];
    $tipe_hari = $_POST['tipe_hari'];
    $destinasi = $_POST['destinasi'];
    $jumlah = $_POST['jumlah'];
    $metode_pembayaran = $_POST['metode_pembayaran'];

    // Mengambil harga destinasi dari database berdasarkan destinasi yang dipilih
    $sql_harga = "SELECT harga_destinasi FROM tb_destinasi WHERE id_destinasi = $destinasi";
    $result_harga = $conn->query($sql_harga);

    if ($result_harga->num_rows > 0) {
        $row_harga = $result_harga->fetch_assoc();
        $harga_destinasi = $row_harga["harga_destinasi"];
        $total_bayar = $jumlah * $harga_destinasi;

    $sql = "INSERT INTO pemesanan (nama, email, tanggal, tipe_hari, destinasi, jumlah, metode_pembayaran, total_bayar)
            VALUES ('$nama', '$email', '$tanggal', '$tipe_hari', '$destinasi', $jumlah, '$metode_pembayaran', $total_bayar)";

    if ($conn->query($sql) === TRUE) {
        $last_id = $conn->insert_id;
        $sql = "SELECT * FROM pemesanan WHERE id = $last_id";
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
    } else {
        echo "Destinasi tidak ditemukan.";
        exit;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Details</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container-xxl py-5">
        <div class="container">
            <div class="row g-5 justify-content-center">
                <div class="col-lg-6 wow fadeInUp" data-wow-delay="0.1s" style="min-height: 0px;">
                    <div class="position-relative h-95">
                        <img src="img/about2.png" alt="MojoTour Image" class="img-fluid">
                    </div>
                </div>
                <div class="col-lg-6 form-container">
                    <?php if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($row)): ?>
                        <h2>DETAIL TIKET</h2>
                        <p>Tanggal Pemesanan: <?php echo date('l, d F Y', strtotime($row['tanggal_pemesanan'])); ?></p>
                        <p>Tanggal Kunjungan: <?php echo $row['tanggal']; ?></p>
                        <p>Destinasi / Packages: <?php echo $row['destinasi']; ?></p>
                        <p>Tipe Hari: <?php echo $row['tipe_hari']; ?></p>
                        <p>Total Wisatawan: <?php echo $row['jumlah']; ?></p>
                        <p>Metode Pembayaran: <?php echo $row['metode_pembayaran']; ?></p>
                        <p>Total Bayar: Rp <?php echo number_format($row['total_bayar'], 0, ',', '.'); ?></p>
                    <?php else: ?>
                        <p>Terjadi kesalahan. Silakan coba lagi.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
