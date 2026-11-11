
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


// MENYIMPAN DATA KOPERASI

if ($_SERVER["REQUEST_METHOD"] == "POST") {

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

    $sql = "INSERT INTO koperasi_merahputih
            (namobj, remark, wadmkd, wadmkc, x, y)
            VALUES (?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);

    $stmt->bind_param(
        "ssssdd",
        $namobj,
        $remark,
        $wadmkd,
        $wadmkc,
        $x,
        $y
    );

    if ($stmt->execute()) {
        header("Location: ../index.php?status=tambah");
        exit;
    } else {
        echo "Data gagal disimpan: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>
