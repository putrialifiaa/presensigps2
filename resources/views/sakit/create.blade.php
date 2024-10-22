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
        <div class="pageTitle">Formulir Sakit</div>
        <div class="right"></div>
    </div>
    <!--- App Header --->
@endsection

@section('content')
    <div class="row" style="margin-top:70px">
        <div class="col">
            <form method="POST" action="/izinsakit/store" id="frmizin" enctype="multipart/form-data">
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
                    <input type="text" readonly id="jml_hari" name="jml_hari" class="form-control" autocomplete="off"
                        placeholder="Jumlah Hari">
                </div>
                <div class="custom-file-upload" id="fileUpload1" style="height: 100px !important">
                    <input type="file" name="sid" id="fileuploadInput" accept=".png, .jpg, .jpeg">
                    <label for="fileuploadInput">
                        <span>
                            <strong>
                                <ion-icon name="cloud-upload-outline" role="img" class="md hydrated"
                                    aria-label="cloud upload outline"></ion-icon>
                                <i>Upload Surat Dokter</i>
                            </strong>
                        </span>
                    </label>
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
                    $("#jml_hari").val("0 Hari");
                    return; // Keluar dari fungsi jika tanggal "sampai" kosong
                }

                // Pastikan tanggal valid sebelum menghitung
                if (!isNaN(date1.getTime()) && !isNaN(date2.getTime())) {
                    // Untuk menghitung perbedaan waktu antara dua tanggal
                    var Difference_In_Time = date2.getTime() - date1.getTime();

                    // Untuk menghitung jumlah hari antara dua tanggal
                    var Difference_In_Days = Difference_In_Time / (1000 * 3600 * 24);

                    // Menampilkan jumlah hari dengan keterangan " Hari"
                    $("#jml_hari").val((Difference_In_Days + 1) + " Hari");
                } else {
                    // Jika tanggal tidak valid
                    $("#jml_hari").val("Invalid Date");
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
                var keterangan = $("#keterangan").val();

                if (tgl_izin_dari == "" || tgl_izin_sampai == "") {
                    Swal.fire({
                        title: 'Oops!',
                        text: 'Tanggal Harus Diisi',
                        icon: 'warning',
                    });
                    event.preventDefault(); // Mencegah submit form jika tidak valid
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
