
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Input Data Koperasi</title>

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
            background: #F23C9C;
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
        <h2>Input Data Koperasi Merah Putih</h2>
        <p>Kabupaten Sukoharjo</p>
    </div>

    <div class="container">

        <h3>Form Input Data</h3>

        <form action="input.php" method="POST">

            <div class="form-grid">

                <div class="form-group">
                    <label>Nama Koperasi</label>
                    <input type="text" name="namobj" required>
                </div>

                <div class="form-group">
                    <label>Remark</label>
                    <input type="text" name="remark">
                </div>

                <div class="form-group">
                    <label>Desa</label>
                    <input type="text" name="wadmkd" required>
                </div>

                <div class="form-group">
                    <label>Kecamatan</label>
                    <input type="text" name="wadmkc" required>
                </div>

                <div class="form-group">
                    <label>Longitude (X)</label>
                    <input
                        type="number"
                        step="any"
                        name="x"
                        placeholder="Contoh: 110.8147"
                        required
                    >
                </div>

                <div class="form-group">
                    <label>Latitude (Y)</label>
                    <input
                        type="number"
                        step="any"
                        name="y"
                        placeholder="Contoh: -7.6684"
                        required
                    >
                </div>

            </div>

            <div class="buttons">

                <button type="submit" class="btn btn-simpan">
                    Simpan Data
                </button>

                <a href="../index.php" class="btn btn-kembali">
                    Kembali
                </a>

            </div>

        </form>

    </div>

</body>
</html>
