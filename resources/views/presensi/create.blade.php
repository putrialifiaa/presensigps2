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
            @if ($cek > 0)
                <button id="takeabsen" class="btn btn-danger btn-block" style="margin-top: 20px;">
                    <ion-icon name="camera-outline"></ion-icon>
                    Absen Pulang
                </button>
            @else
                <button id="takeabsen" class="btn btn-primary btn-block" style="margin-top: 20px;">
                    <ion-icon name="camera-outline"></ion-icon>
                    Absen Masuk
                </button>
            @endif
        </div>
    </div>
    <div class="row mt-2">
        <div class="col">
            <div id="map"></div>
        </div>
    </div>

    <!-- Audio notifikasi -->
    <audio id="notif_in" src="{{ asset('assets/sound/notif_in.mp3') }}" type="audio/mpeg">
    </audio>
    <audio id="notif_out" src="{{ asset('assets/sound/notif_out.mp3') }}" type="audio/mpeg">
    </audio>
    <audio id="notif_radius" src="{{ asset('assets/sound/notif_radius.mp3') }}" type="audio/mpeg">
    </audio>
@endsection

@push('myscript')
    <!-- Mengimpor pustaka webcam.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/webcamjs/1.0.26/webcam.min.js"></script>

    <style>
        .webcam-capture,
        .webcam-capture video {
            display: block;
            width: 100% !important;
            height: auto !important;
            margin: auto;
            border-radius: 15px;
        }
    </style>

    <script>
        var notif_in = document.getElementById('notif_in');
        var notif_out = document.getElementById('notif_out');
        var notif_radius = document.getElementById('notif_radius');

        // Setel properti webcam
        Webcam.set({
            width: 540,
            height: 430,
            image_format: 'jpeg',
            jpeg_quality: 80
        });

        Webcam.attach('.webcam-capture');

        var lokasi = document.getElementById('lokasi');

        // Inisialisasi Map dengan Leaflet
        var map = L.map('map').setView([-7.170690135108098, 112.65269280809838], 15);

        // Mengambil tile map dari OpenStreetMap
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        // Menandai lokasi kantor
        var kantorIcon = L.icon({
            iconUrl: "{{ asset('assets/img/office-building.png') }}",
            iconSize: [38, 95],
        });

        var kantorMarker = L.marker([-7.170690135108098, 112.65269280809838], {
            icon: kantorIcon
        }).addTo(map);

        // Mengambil lokasi user
        navigator.geolocation.getCurrentPosition(function(position) {
            var lat = position.coords.latitude;
            var long = position.coords.longitude;

            lokasi.value = lat + "," + long;

            var userMarker = L.marker([lat, long]).addTo(map)
                .bindPopup('Lokasi Anda Sekarang')
                .openPopup();

            map.setView([lat, long], 15);
        });

        $("#takeabsen").click(function(e) {
            e.preventDefault();

            // Mengambil gambar dari webcam
            Webcam.snap(function(uri) {
                image = uri;
            });

            // Mengirim data melalui AJAX
            $.ajax({
                type: 'POST',
                url: '/presensi/store',
                data: {
                    _token: "{{ csrf_token() }}",
                    image: image,
                    lokasi: $("#lokasi").val()
                },
                success: function(response) {
                    if (response.status === "success") {
                        if (response.type === "in") {
                            notif_in.play();
                        } else {
                            notif_out.play();
                        }
                        Swal.fire({
                            title: 'Berhasil!',
                            text: response.message,
                            icon: 'success',
                            confirmButtonText: 'OK'
                        });
                        setTimeout(function() {
                            window.location.href = '/dashboard';
                        }, 3000);
                    } else {
                        if (response.type === "radius") {
                            notif_radius.play();
                        }
                        Swal.fire({
                            title: 'Error!',
                            text: response.message,
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    }
                },
                error: function(xhr) {
                    var response = JSON.parse(xhr.responseText);
                    Swal.fire({
                        title: 'Error!',
                        text: response.message,
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            });
        });
    </script>
@endpush
