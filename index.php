
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

$sql = "SELECT ogc_fid, namobj, remark, wadmkd, wadmkc, x, y
        FROM koperasi_merahputih";

$result = $conn->query($sql);

if (!$result) {
    die("Query gagal: " . $conn->error);
}

$dataKoperasi = [];

while ($row = $result->fetch_assoc()) {

    $x = (float) $row['x'];
    $y = (float) $row['y'];

    // Mendeteksi urutan koordinat secara otomatis
    if (abs($x) <= 90 && abs($y) > 90) {
        $latitude  = $x;
        $longitude = $y;
    } else {
        $latitude  = $y;
        $longitude = $x;
    }

    // Memastikan koordinat valid
    if (
        $latitude >= -90 && $latitude <= 90 &&
        $longitude >= -180 && $longitude <= 180
    ) {
        $dataKoperasi[] = [
            "ogc_fid"   => $row['ogc_fid'],
            "namobj"    => $row['namobj'],
            "wadmkd"    => $row['wadmkd'],
            "wadmkc"    => $row['wadmkc'],
            "latitude"  => $latitude,
            "longitude" => $longitude
        ];
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>WebGIS Koperasi Merah Putih</title>

    <link
        rel="stylesheet"
        href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
    >

    <style>

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: white;
        }

        .header {
            background: linear-gradient(90deg, #F23C9C, #FFA3DA);
            color: white;
            padding: 15px 20px;
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .header h2 {
            margin: 0;
        }

        .header p {
            margin: 5px 0 0;
            font-size: 14px;
        }

        #map {
            width: 100%;
            height: 55vh;
            min-height: 360px;
            position: sticky;
            top: 77px;
            z-index: 500;
        }

        .leaflet-popup-content {
            line-height: 1.6;
        }

        .legend-control {
            padding: 4px 7px;
            background: white;
            line-height: 18px;
            color: #333;
            font-size: 12px;
        }

        .legend-control img {
            width: 20px;
            height: 20px;
            object-fit: contain;
            vertical-align: middle;
            margin-right: 4px;
        }

        .table-container {
            padding: 25px;
            background: white;
        }

        .table-container h3 {
            color: #F23C9C;
            margin-top: 0;
            margin-bottom: 18px;
        }

        .tombol-tambah {
            display: inline-block;
            padding: 10px 15px;
            background: #F23C9C;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-size: 13px;
            margin-bottom: 15px;
        }

        .table-wrapper {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
        }

        th {
            background: #F23C9C;
            color: white;
            padding: 12px;
            text-align: left;
            white-space: nowrap;
        }

        td {
            padding: 11px 12px;
            border-bottom: 1px solid #eee;
        }

        tbody tr {
            cursor: pointer;
            transition: background 0.2s;
        }

        tbody tr:hover {
            background: #fff0f7;
        }

        tbody tr.selected {
            background: #ffcce8;
            outline: 2px solid #F23C9C;
            font-weight: bold;
        }

        .table-info {
            font-size: 12px;
            color: #777;
            margin-top: 12px;
        }

        .aksi {
            display: flex;
            gap: 6px;
            white-space: nowrap;
        }

        .aksi form {
            margin: 0;
        }

        .btn {
            display: inline-block;
            padding: 7px 10px;
            border: none;
            border-radius: 5px;
            color: white;
            text-decoration: none;
            cursor: pointer;
            font-size: 12px;
        }

        .btn-edit {
            background: #3498db;
        }

        .btn-hapus {
            background: #e74c3c;
        }

        .pesan {
            margin: 15px 25px;
            padding: 12px;
            background: #fff0f7;
            border: 1px solid #ffcce8;
            border-radius: 5px;
            color: #a51d68;
            font-size: 13px;
        }

        @media (max-width: 600px) {
            .table-container {
                padding: 15px;
            }
        }

    </style>

</head>

<body>

    <div class="header">
        <h2>WebGIS Persebaran Koperasi Merah Putih</h2>
        <p>Kabupaten Sukoharjo</p>
    </div>

    <div id="map"></div>

    <?php if (isset($_GET['status'])): ?>

        <div class="pesan">
            <?php
                if ($_GET['status'] == 'tambah') {
                    echo "Data koperasi berhasil ditambahkan.";
                } elseif ($_GET['status'] == 'edit') {
                    echo "Data koperasi berhasil diperbarui.";
                } elseif ($_GET['status'] == 'hapus') {
                    echo "Data koperasi berhasil dihapus.";
                }
            ?>
        </div>

    <?php endif; ?>


    <!-- TABEL DATA KOPERASI -->

    <div class="table-container">

        <h3>Data Persebaran Koperasi Merah Putih</h3>

        <a href="input/index.php" class="tombol-tambah">
            + Tambah Data Koperasi
        </a>

        <div class="table-wrapper">

            <table>

                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Nama Koperasi</th>
                        <th>Desa</th>
                        <th>Kecamatan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>

                <tbody>

                    <?php foreach ($dataKoperasi as $index => $data): ?>

                    <tr
                        id="row-<?php echo $index; ?>"
                        onclick="pilihKoperasi(<?php echo $index; ?>)"
                    >

                        <td><?php echo $index + 1; ?></td>

                        <td>
                            <?php echo htmlspecialchars($data['namobj'] ?? '-'); ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($data['wadmkd'] ?? '-'); ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($data['wadmkc'] ?? '-'); ?>
                        </td>

                        <td onclick="event.stopPropagation();">

                            <div class="aksi">

                                <a
                                    href="edit/index.php?id=<?php echo (int)$data['ogc_fid']; ?>"
                                    class="btn btn-edit"
                                >
                                    Edit
                                </a>

                                <form
                                    action="delete.php"
                                    method="POST"
                                    onsubmit="return confirm('Yakin ingin menghapus data ini?')"
                                >

                                    <input
                                        type="hidden"
                                        name="ogc_fid"
                                        value="<?php echo (int)$data['ogc_fid']; ?>"
                                    >

                                    <button type="submit" class="btn btn-hapus">
                                        Hapus
                                    </button>

                                </form>

                            </div>

                        </td>

                    </tr>

                    <?php endforeach; ?>

                </tbody>

            </table>

        </div>

        <p class="table-info">
            Klik salah satu baris untuk melihat lokasi koperasi pada peta.
        </p>

    </div>


    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <script>

        // MEMBUAT PETA

        var map = L.map('map').setView(
            [-7.668450620300294, 110.81471114170897],
            11
        );


        // PILIHAN BASEMAP

        // Basemap Esri Imagery

        var basemapEsri = L.tileLayer(
            'https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}',
            {
                attribution: 'Tiles &copy; Esri',
                maxZoom: 19
            }
        );


        // Basemap OpenStreetMap

        var basemapOSM = L.tileLayer(
            'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
            {
                attribution: '&copy; OpenStreetMap contributors',
                maxZoom: 19
            }
        );


        // Basemap awal

        basemapEsri.addTo(map);


        // MENAMPILKAN DATA DARI PHP

        var dataKoperasi = <?php
            echo json_encode(
                $dataKoperasi,
                JSON_HEX_TAG |
                JSON_HEX_APOS |
                JSON_HEX_QUOT |
                JSON_HEX_AMP
            );
        ?>;


        // IKON MARKER

        var koperasiIcon = L.icon({
            iconUrl: 'image/icon.png',
            iconSize: [35, 35],
            iconAnchor: [17, 35],
            popupAnchor: [0, -35]
        });


        // IKON MARKER TERPILIH

        var selectedIcon = L.icon({
            iconUrl: 'image/icon.png',
            iconSize: [45, 45],
            iconAnchor: [22, 45],
            popupAnchor: [0, -45]
        });


        // LAYER MARKER

        var markerLayer = L.featureGroup();

        // Menyimpan marker berdasarkan index data

        var daftarMarker = [];

        var markerTerpilih = null;
        var indexTerpilih = null;


        dataKoperasi.forEach(function(data, index) {

            var latitude = parseFloat(data.latitude);
            var longitude = parseFloat(data.longitude);

            var marker = L.marker(
                [latitude, longitude],
                { icon: koperasiIcon }
            );


            // INFORMASI PADA POPUP

            var informasi =
                '<b>Nama Koperasi:</b> ' + (data.namobj || '-') +
                '<br><b>Desa:</b> ' + (data.wadmkd || '-') +
                '<br><b>Kecamatan:</b> ' + (data.wadmkc || '-');

            marker.bindPopup(informasi, {
                autoPan: true,
                autoPanPaddingTopLeft: [20, 90],
                autoPanPaddingBottomRight: [20, 20]
            });


            // MENYIMPAN MARKER SESUAI INDEX

            daftarMarker.push(marker);


            // KETIKA MARKER DIKLIK

            marker.on('click', function() {
                highlightBaris(index);
            });


            // MEMASUKKAN MARKER KE DALAM LAYER

            markerLayer.addLayer(marker);

        });


        // MENAMPILKAN MARKER PADA PETA

        markerLayer.addTo(map);


        // MENGATUR ZOOM SELURUH MARKER

        if (dataKoperasi.length > 0) {

            var bounds = markerLayer.getBounds();

            if (bounds.isValid()) {
                map.fitBounds(bounds, {
                    padding: [30, 30],
                    maxZoom: 15
                });
            }

        }


        // FUNGSI HIGHLIGHT BARIS TABEL

        function highlightBaris(index) {

            // Menghapus highlight sebelumnya

            if (indexTerpilih !== null) {

                var barisSebelumnya = document.getElementById(
                    'row-' + indexTerpilih
                );

                if (barisSebelumnya) {
                    barisSebelumnya.classList.remove('selected');
                }

            }


            // mengembalikan ikon marker sebelumnya

            if (markerTerpilih !== null) {
                markerTerpilih.setIcon(koperasiIcon);
                markerTerpilih.setZIndexOffset(0);
            }


            // memilih baris baru

            var baris = document.getElementById('row-' + index);

            if (baris) {
                baris.classList.add('selected');
            }


            // memilih marker baru

            markerTerpilih = daftarMarker[index];

            markerTerpilih.setIcon(selectedIcon);
            markerTerpilih.setZIndexOffset(1000);

            indexTerpilih = index;

        }


        // KETIKA BARIS TABEL DIKLIK

        function pilihKoperasi(index) {

            var data = dataKoperasi[index];

            // HIGHLIGHT BARIS DAN MARKER

            highlightBaris(index);


            // MENGGESER PETA KE LOKASI KOPERASI

            map.flyTo(
                [data.latitude, data.longitude],
                16,
                {
                    duration: 1
                }
            );


            // MEMBUKA POPUP MARKER

            markerTerpilih.openPopup();

        }


        // MEMBUAT LAYER CONTROL

        var baseMaps = {
            "Esri Imagery": basemapEsri,
            "OpenStreetMap": basemapOSM
        };

        var overlayMaps = {
            "Persebaran Koperasi Merah Putih": markerLayer
        };

        var layerControl = L.control.layers(
            baseMaps,
            overlayMaps,
            {
                collapsed: false,
                position: 'topright'
            }
        ).addTo(map);


        // MENAMBAHKAN LEGENDA

        var legend = L.DomUtil.create('div', 'legend-control');

        legend.innerHTML = `
            <b>LEGENDA</b><br>
            <img src="image/icon.png">
            Koperasi Merah Putih<br>
        `;


        // MEMASUKKAN LEGENDA KE PANEL LAYER CONTROL

        layerControl.getContainer().appendChild(legend);


        // MENAMBAHKAN SKALA PETA

        L.control.scale({
            position: 'bottomright',
            metric: true,
            imperial: false
        }).addTo(map);


        // INFORMASI JUMLAH DATA

        console.log("Jumlah data koperasi:", dataKoperasi.length);

    </script>

</body>
</html>
