
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


// MENGAMBIL DATA KOPERASI

$ogc_fid = (int) $_GET["id"];

$sql = "SELECT * FROM koperasi_merahputih
        WHERE ogc_fid = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $ogc_fid);
$stmt->execute();

$result = $stmt->get_result();
$data = $result->fetch_assoc();

if (!$data) {
    die("Data koperasi tidak ditemukan.");
}

$x = (float) $data["x"];
$y = (float) $data["y"];

// Mendeteksi urutan koordinat secara otomatis
if (abs($x) <= 90 && abs($y) > 90) {
    $temp = $x;
    $x = $y;
    $y = $temp;
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Edit Data Koperasi</title>

    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #fff7fb;
        }

        .header {
            background: linear-gradient(90deg, #F23C9C, #FFA3DA);
            color: white;
            padding: 20px;
        }

        .header h2 {
            margin: 0;
        }

        .container {
            max-width: 750px;
            margin: 30px auto;
            padding: 25px;
            background: white;
            border: 1px solid #ffcce8;
            border-radius: 8px;
        }

        h3 {
            color: #F23C9C;
            margin-top: 0;
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 7px;
        }

        label {
            font-size: 13px;
            font-weight: bold;
        }

        input {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
            box-sizing: border-box;
            width: 100%;
        }

        input:focus {
            outline: none;
            border-color: #F23C9C;
        }

        .buttons {
            margin-top: 20px;
            display: flex;
            gap: 10px;
        }

        .btn {
            padding: 10px 16px;
            border: none;
            border-radius: 5px;
            color: white;
            text-decoration: none;
            cursor: pointer;
            font-size: 13px;
        }

        .btn-simpan {
            background: #3498db;
        }

        .btn-kembali {
            background: #777;
        }

        @media (max-width: 600px) {
            .container {
                margin: 15px;
                padding: 18px;
            }

            .form-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>

</head>

<body>

    <div class="header">
        <h2>Edit Data Koperasi Merah Putih</h2>
        <p>Kabupaten Sukoharjo</p>
    </div>

    <div class="container">

        <h3>Form Edit Data</h3>

        <form action="edit.php" method="POST">

            <input
                type="hidden"
                name="ogc_fid"
                value="<?php echo (int)$data['ogc_fid']; ?>"
            >

            <div class="form-grid">

                <div class="form-group">
                    <label>Nama Koperasi</label>
                    <input
                        type="text"
                        name="namobj"
                        value="<?php echo htmlspecialchars($data['namobj'] ?? ''); ?>"
                        required
                    >
                </div>

                <div class="form-group">
                    <label>Remark</label>
                    <input
                        type="text"
                        name="remark"
                        value="<?php echo htmlspecialchars($data['remark'] ?? ''); ?>"
                    >
                </div>

                <div class="form-group">
                    <label>Desa</label>
                    <input
                        type="text"
                        name="wadmkd"
                        value="<?php echo htmlspecialchars($data['wadmkd'] ?? ''); ?>"
                        required
                    >
                </div>

                <div class="form-group">
                    <label>Kecamatan</label>
                    <input
                        type="text"
                        name="wadmkc"
                        value="<?php echo htmlspecialchars($data['wadmkc'] ?? ''); ?>"
                        required
                    >
                </div>

                <div class="form-group">
                    <label>Longitude (X)</label>
                    <input
                        type="number"
                        step="any"
                        name="x"
                        value="<?php echo htmlspecialchars((string)$x); ?>"
                        required
                    >
                </div>

                <div class="form-group">
                    <label>Latitude (Y)</label>
                    <input
                        type="number"
                        step="any"
                        name="y"
                        value="<?php echo htmlspecialchars((string)$y); ?>"
                        required
                    >
                </div>

            </div>

            <div class="buttons">

                <button type="submit" class="btn btn-simpan">
                    Simpan Perubahan
                </button>

                <a href="../index.php" class="btn btn-kembali">
                    Kembali
                </a>

            </div>

        </form>

    </div>

</body>
</html>
