@extends('layouts.presensi')
@section('header')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0-beta/css/materialize.min.css">
    <style>
        .datepicker-modal {
            max-height: 430px !important;
        }

        .datepicker-date-display {
            background-color: #0f3a7e !important;
        }
    </style>
    <!--- App Header --->
    <div class="appHeader bg-primary text-light">
        <div class="left">
            <a href="javascript:;" class="headerButton goBack">
                <ion-icon name="chevron-back-outline"></ion-icon>
            </a>
        </div>
        <div class="pageTitle">Formulir Izin</div>
        <div class="right"></div>
    </div>
    <!--- App Header --->
@endsection

@section('content')
    <div class="row" style="margin-top:70px">
        <div class="col">
            <form method="POST" action="/presensi/storeizin" id="frmizin">
                @csrf
                <div class="form-group">
                    <input type="text" id="tgl_izin" name="tgl_izin" class="form-control datepicker"
                        placeholder="Tanggal">
                </div>
                <div class="form-group">
                    <select name="status" id="status" class="form-control">
                        <option value="">Pilih Status</option>
                        <option value="i">Izin</option>
                        <option value="s">Sakit</option>
                    </select>
                </div>
                <div class="form-group">
                    <textarea name="keterangan" id="keterangan" cols="30" rows="5" class="form-control" placeholder="Keterangan"></textarea>
                </div>
                <div class="form-group">
                    <button class="btn btn-primary w-100" type="submit">Kirim</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('myscript')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0-beta/js/materialize.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var currYear = (new Date()).getFullYear();
            var elems = document.querySelectorAll('.datepicker');
            var instances = M.Datepicker.init(elems, {
                format: 'yyyy-mm-dd', // Format tanggal
                autoClose: true // Menutup otomatis setelah memilih
            });

            // Pastikan form menggunakan # untuk ID form
            $("#frmizin").submit(function(event) {
                var tgl_izin = $("#tgl_izin").val();
                var status = $("#status").val();
                var keterangan = $("#keterangan").val();

                if (tgl_izin == "") {
                    Swal.fire({
                        title: 'Oops!',
                        text: 'Tanggal Harus Diisi',
                        icon: 'warning',
                    });
                    event.preventDefault(); // Mencegah submit form jika tidak valid
                } else if (status == "") {
                    Swal.fire({
                        title: 'Oops!',
                        text: 'Status Harus Diisi',
                        icon: 'warning',
                    });
                    event.preventDefault();
                } else if (keterangan == "") {
                    Swal.fire({
                        title: 'Oops!',
                        text: 'Keterangan Harus Diisi',
                        icon: 'warning',
                    });
                    event.preventDefault();
                }
            });
        });
    </script>
@endpush
