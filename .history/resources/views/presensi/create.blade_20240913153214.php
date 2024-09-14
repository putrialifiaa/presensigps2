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
            <!-- Perbaikan class untuk webcam -->
            <div class="webcam-capture"></div>
        </div>
    </div>
@endsection

@push('myscript')
    <!-- Mengimpor pustaka webcam.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/webcamjs/1.0.26/webcam.min.js"></script>

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
    </script>
@endpush
