
<?php
// KONEKSI DATABASE

$servername = "localhost";
$username   = "root";
$password   = "";
$dbname     = "kec_sukoharjo";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Koneksi database gagal: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");


// MENGHAPUS DATA KOPERASI

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $ogc_fid = (int) $_POST["ogc_fid"];

    $sql = "DELETE FROM koperasi_merahputih
            WHERE ogc_fid = ?";

    $stmt = $conn->prepare($sql);

    $stmt->bind_param("i", $ogc_fid);

    if ($stmt->execute()) {
        header("Location: index.php?status=hapus");
        exit;
    } else {
        echo "Data gagal dihapus: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>
