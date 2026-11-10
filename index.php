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
    // Latitude berada pada rentang -90 sampai 90
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

    <!-- Leaflet CSS -->
    <link
        rel="stylesheet"
        href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
    >

    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
        }

        
        .header {
    background: linear-gradient(90deg, #F23C9C, #FFA3DA);
    color: white;
    padding: 15px 20px;
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
            height: calc(100vh - 75px);
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
            width: 20pxpx;
            height: 20px;
            object-fit: contain;
            vertical-align: middle;
            margin-right: 4px;
        }
    </style>
</head>

<body>

    
    <!--  HEADER -->
    

    <div class="header">
        <h2>WebGIS Persebaran Koperasi Merah Putih</h2>
        <p>Kabupaten Sukoharjo</p>
    </div>

    
    <!-- AREA PETA -->
    

    <div id="map"></div>

    <!-- Leaflet JavaScript -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <script>


        // MEMBUAT PETA

        var map = L.map('map').setView([-7.668450620300294, 110.81471114170897], 11);


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


        // LAYER MARKER

        
        var markerLayer = L.featureGroup();

        dataKoperasi.forEach(function(data) {

            var latitude = parseFloat(data.latitude);
            var longitude = parseFloat(data.longitude);

            var koperasiIcon = L.icon({
                iconUrl: 'image/icon.png',
                iconSize: [35, 35],
                iconAnchor: [17, 35],
                popupAnchor: [0, -35]
            });

        var marker = L.marker(
            [latitude, longitude],
            { icon: koperasiIcon }
        );

            // Informasi pada popup
            var informasi =
                '<b>Nama Koperasi:</b> ' + (data.namobj || '-') +
                '<br><b>Desa:</b> ' + (data.wadmkd || '-') +
                '<br><b>Kecamatan:</b> ' + (data.wadmkc || '-');

            marker.bindPopup(informasi);

            // Memasukkan marker ke dalam layer
            markerLayer.addLayer(marker);

        });


        // MENAMPILKAN MARKER PADA PETA

        markerLayer.addTo(map);


        
        // MENGATUR ZOOM SELURUH MARKER

        if (dataKoperasi.length > 0) {

            var bounds = markerLayer.getBounds();

            if (bounds.isValid()) {
                map.fitBounds(bounds, {
                    padding: [30, 30]
                });
            }

        }


        // MEMBUAT LAYER CONTROL

        var baseMaps = {
            "Esri Imagery": basemapEsri,
            "OpenStreetMap": basemapOSM
        };

        var overlayMaps = {
            "Persebaran Koperasi Merah Putih": markerLayer
        };

        var overlayMaps = {
            "Persebaran Koperasi Merah Putih": markerLayer
        };

        // Membuat Layer Control
    var layerControl = L.control.layers(
        baseMaps,
        overlayMaps,
        {
            collapsed: false,
            position: 'topright'
        }
    ).addTo(map);


// Menambahkan legenda ke dalam Layer Control
var legend = L.DomUtil.create('div', 'legend-control');

legend.innerHTML = `
    <b>LEGENDA</b><br>

    <img src="image/icon.png">
    Koperasi Merah Putih<br>

`;

// Memasukkan legenda ke panel Layer Control
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
