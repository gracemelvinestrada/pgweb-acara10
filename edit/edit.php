
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


// MEMPERBARUI DATA KOPERASI

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $ogc_fid = (int) $_POST["ogc_fid"];

    $namobj = trim($_POST["namobj"]);
    $remark = trim($_POST["remark"]);
    $wadmkd = trim($_POST["wadmkd"]);
    $wadmkc = trim($_POST["wadmkc"]);

    $x = $_POST["x"];
    $y = $_POST["y"];

    if (
        $namobj == "" ||
        $wadmkd == "" ||
        $wadmkc == "" ||
        !is_numeric($x) ||
        !is_numeric($y)
    ) {
        die("Data tidak lengkap atau koordinat tidak valid.");
    }

    $x = (float) $x;
    $y = (float) $y;

    if (
        $x < -180 || $x > 180 ||
        $y < -90 || $y > 90
    ) {
        die("Koordinat tidak valid.");
    }

    $sql = "UPDATE koperasi_merahputih
            SET namobj = ?, remark = ?, wadmkd = ?,
                wadmkc = ?, x = ?, y = ?
            WHERE ogc_fid = ?";

    $stmt = $conn->prepare($sql);

    $stmt->bind_param(
        "ssssddi",
        $namobj,
        $remark,
        $wadmkd,
        $wadmkc,
        $x,
        $y,
        $ogc_fid
    );

    if ($stmt->execute()) {
        header("Location: ../index.php?status=edit");
        exit;
    } else {
        echo "Data gagal diperbarui: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>
