@extends('layouts.presensi')

@section('header')
    <!-- App Header -->
    <div class="appHeader bg-primary text-light">
        <div class="left">
            <a href="javascript:;" class="headerButton goBack">
                <ion-icon name="chevron-back-outline"></ion-icon>
            </a>
        </div>
        <div class="pageTitle">E-Presensi</div>
        <div class="right"></div>
    </div>
    <!-- * App Header -->

    <style>
        #map {
            height: 200px;
        }
    </style>

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
@endsection

@section('content')
    <div class="row" style="margin-top: 70px">
        <div class="col">
            <input type="hidden" id="lokasi">
            <!-- Elemen untuk webcam -->
            <div class="webcam-capture"></div>
        </div>
    </div>
    <div class="row" style="margin-top: 20px;">
        <div class="col">
            <button id="takeabsen" class="btn btn-primary btn-block" style="margin-top: 20px;">
                <ion-icon name="camera-outline"></ion-icon>
                Absen Masuk
            </button>
        </div>
    </div>
    <div class="row mt-2">
        <div class="col">
            <div id="map"></div>
        </div>
    </div>
@endsection

@push('myscript')
    <!-- Mengimpor pustaka webcam.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/webcamjs/1.0.26/webcam.min.js"></script>

    <!-- Tambahkan style untuk elemen webcam -->
    <style>
        .webcam-capture,
        .webcam-capture video {
            display: block;
            width: 100% !important;
            height: auto !important;
            margin: auto;
            border-radius: 15px;
            /* Radius untuk melengkungkan tepi */
        }
    </style>

    <script>
        // Setel properti webcam
        Webcam.set({
            height: 480,
            width: 640,
            image_format: 'jpeg',
            jpeg_quality: 80
        });

        // Lampirkan webcam ke elemen dengan class .webcam-capture
        Webcam.attach('.webcam-capture');

        var lokasi = document.getElementById('lokasi');
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(successCallback, errorCallback);
        }

        function successCallback(position) {
            lokasi.value = position.coords.latitude + ', ' + position.coords.longitude;

            // Inisialisasi peta Leaflet
            var map = L.map('map').setView([position.coords.latitude, position.coords.longitude], 15);

            // Menambahkan tile layer OpenStreetMap
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
            }).addTo(map);
            var marker = L.marker([position.coords.latitude, position.coords.longitude]).addTo(map);

            // Menambahkan marker pada posisi pengguna
            L.marker([position.coords.latitude, position.coords.longitude]).addTo(map)
                .bindPopup('Lokasi Anda')
                .openPopup();
        }

        function errorCallback(error) {
            console.error("Error mendapatkan lokasi: ", error);
            lokasi.value = "Tidak dapat mengambil lokasi";
        }
    </script>
@endpush
