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

        #max_cuti,
        #keterangan {
            background-color: #ffffff;
            font-size: 14px;
        }

        #info_max_cuti,
        #info_jml_hari {
            font-size: 0.9em;
            color: #555;
        }
    </style>

    <!--- App Header --->
    <div class="appHeader bg-primary text-light">
        <div class="left">
            <a href="javascript:;" class="headerButton goBack">
                <ion-icon name="chevron-back-outline"></ion-icon>
            </a>
        </div>
        <div class="pageTitle">Formulir Cuti</div>
        <div class="right"></div>
    </div>
    <!--- App Header --->
@endsection

@section('content')
    <div class="row" style="margin-top:70px">
        <div class="col">
            <form method="POST" action="/izincuti/store" id="frmizin">
                @csrf
                <div class="form-group">
                    <input type="text" id="tgl_izin_dari" autocomplete="off" name="tgl_izin_dari"
                        class="form-control datepicker" placeholder="Dari">
                </div>
                <div class="form-group">
                    <input type="text" id="tgl_izin_sampai" autocomplete="off" name="tgl_izin_sampai"
                        class="form-control datepicker" placeholder="Sampai">
                </div>
                <div class="form-group">
                    <input type="hidden" readonly id="jml_hari" name="jml_hari" class="form-control" autocomplete="off"
                        placeholder="Jumlah Hari">
                    <p id="info_jml_hari"></p>
                </div>
                <div class="form-group">
                    <select name="kode_cuti" id="kode_cuti" class="form-control">
                        <option value="">Pilih Cuti</option>
                        @foreach ($mastercuti as $c)
                            <option value="{{ $c->kode_cuti }}">{{ $c->nama_cuti }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <textarea name="max_cuti" id="max_cuti" cols="30" rows="1" class="form-control" placeholder="Maksimal Cuti"
                        readonly style="display:none;"></textarea>
                    <p id="info_max_cuti"></p>
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

            function loadjumlahhari() {
                var dari = $("#tgl_izin_dari").val();
                var sampai = $("#tgl_izin_sampai").val();
                var date1 = new Date(dari);
                var date2 = new Date(sampai);

                // Cek apakah tanggal "sampai" belum diisi
                if (!sampai) {
                    $("#jml_hari").val("0");
                    $("#info_jml_hari").html("<b style='font-size: 0.9em;'>Jumlah Cuti yang Diambil: 0 Hari</b>");
                    return;
                }

                // Pastikan tanggal valid sebelum menghitung
                if (!isNaN(date1.getTime()) && !isNaN(date2.getTime())) {
                    var Difference_In_Time = date2.getTime() - date1.getTime();
                    var Difference_In_Days = Difference_In_Time / (1000 * 3600 * 24);

                    var jmlHari = Difference_In_Days + 1;
                    $("#jml_hari").val(jmlHari);
                    $("#info_jml_hari").html("<b style='font-size: 0.9em;'>Jumlah Cuti yang Diambil: " + jmlHari +
                        " Hari</b>");
                } else {
                    $("#jml_hari").val("Invalid Date");
                    $("#info_jml_hari").html("<b style='font-size: 0.9em;'>Tanggal tidak valid</b>");
                }
            }

            $("#tgl_izin_dari, #tgl_izin_sampai").change(function(e) {
                loadjumlahhari();
            });

            //$("#tgl_izin").change(function(e) {
            //    $.ajax({
            //        var tgl_izin = $(this).val();
            //        type: 'POST',
            //        url: '/presensi/cekpengajuanizin',
            //        data: {
            //            _token: "{{ csrf_token() }}",
            //            tgl_izin: tgl_izin
            //        },
            //        cache: false,
            //        success: function(respond) {
            //            if (respond == 1) {
            //                Swal.fire({
            //                    title: 'Oops !',
            //                    text: 'Anda Sudah Melakukan Izin',
            //                    icon: 'warning'
            //                }).then((result) => {
            //                    $("#tgl_izin").val("");
            //                });
            //            }
            //        }
            //    });
            //});

            // Pastikan form menggunakan # untuk ID form
            $("#frmizin").submit(function(event) {
                var tgl_izin_dari = $("#tgl_izin_dari").val();
                var tgl_izin_sampai = $("#tgl_izin_sampai").val();
                var jml_hari = $("#jml_hari").val();
                var max_cuti = $("#max_cuti").val();
                var keterangan = $("#keterangan").val();
                var kode_cuti = $("#kode_cuti").val();
                if (tgl_izin_dari == "" || tgl_izin_sampai == "") {
                    Swal.fire({
                        title: 'Oops!',
                        text: 'Tanggal Harus Diisi',
                        icon: 'warning',
                    });
                    event.preventDefault(); // Mencegah submit form jika tidak valid
                } else if (kode_cuti == "") {
                    Swal.fire({
                        title: 'Oops!',
                        text: 'Kode Cuti Harus Diisi',
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
                } else if (parseInt(jml_hari) > parseInt(max_cuti)) {
                    Swal.fire({
                        title: 'Oops!',
                        text: 'Jumlah Cuti Melebihi Batas Maksimal',
                        icon: 'warning',
                    });
                    event.preventDefault();
                }
            });

            $("#kode_cuti").change(function(e) {
                var kode_cuti = $(this).val();
                var tgl_izin_dari = $("#tgl_izin_dari").val();
                if (tgl_izin_dari == "") {
                    Swal.fire({
                        title: 'Oops!',
                        text: 'Tanggal Belum Diisi',
                        icon: 'warning',
                    });
                    event.preventDefault();
                    $("#kode_cuti").val("");
                } else {
                    $.ajax({
                        url: '/izincuti/getmaxcuti',
                        type: 'POST',
                        data: {
                            _token: "{{ csrf_token() }}",
                            kode_cuti: kode_cuti,
                            tgl_izin_dari: tgl_izin_dari
                        },
                        cache: false,
                        success: function(respond) {
                            $("#max_cuti").val(respond);
                            $("#info_max_cuti").html(
                                "<b style='font-size: 0.9em;'>Maks. Cuti yang Bisa Diambil Adalah :" +
                                respond + " Hari</b>");
                        }
                    });
                }
            });
        });
    </script>
@endpush
