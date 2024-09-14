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
@endsection

@section('content')
    <div class="row" style="margin-top: 70px">
        <div class="col">
            <input type="hidden" id="lokasi">
            <!-- Elemen untuk webcam -->
            <div class="webcam-capture"></div>
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
        }

        function errorCallback(error) {
            console.error("Error mendapatkan lokasi: ", error);
            lokasi.value = "Tidak dapat mengambil lokasi";
        }
    </script>
@endpush
