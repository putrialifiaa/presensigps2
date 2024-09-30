@extends('layouts.presensi')
@section('header')
    <!--- App Header --->
    <div class="appHeader bg-primary text-light">
        <div class="left">
            <a href="javascript:;" class="headerButton goBack">
                <ion-icon name="chevron-back-outline"></ion-icon>
            </a>
        </div>
        <div class="pageTitle">Riwayat Presensi</div>
        <div class="right"></div>
    </div>
    <!--- App Header --->
@endsection

@section('content')
    <div class="row" style="margin-top:70px">
        <div class="col">
            <div class="row">
                <div class="col-12">
                    <div class="form-group">
                        <select name="bulan" id="bulan" class="form-control">
                            <option value="">Pilih Bulan</option>
                            @for ($i = 1; $i <= 12; $i++)
                                <option value="{{ $i }}" {{ date('m') == $i ? 'selected' : '' }}>
                                    {{ $namabulan[$i] }}
                                </option>
                            @endfor
                        </select>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="form-group">
                        <select name="tahun" id="tahun" class="form-control">
                            <option value="">Pilih Tahun</option>
                            @php
                                $tahunmulai = 2024;
                                $tahunsekarang = date('Y');
                            @endphp
                            @for ($tahun = $tahunmulai; $tahun <= $tahunsekarang; $tahun++)
                                <option value="{{ $tahun }}" {{ date('Y') == $tahun ? 'selected' : '' }}>
                                    {{ $tahun }}
                                </option>
                            @endfor
                        </select>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="form-group">
                        <button class="btn btn-primary btn-block" id="getdata">Search</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col" id="showhistory"></div>
    </div>
@endsection

@push('myscript')
    <script>
        $(function() {
            $("#getdata").click(function(e) {
                e.preventDefault(); // Mencegah submit form yang tidak diinginkan
                var bulan = $("#bulan").val();
                var tahun = $("#tahun").val();

                if (bulan == "" || tahun == "") {
                    alert("Bulan dan Tahun harus dipilih");
                    return;
                }

                $.ajax({
                    type: 'POST',
                    url: '/gethistory',
                    data: {
                        _token: "{{ csrf_token() }}",
                        bulan: bulan,
                        tahun: tahun,
                    },
                    cache: false,
                    success: function(respond) {
                        $("#showhistory").html(respond);
                        // Tampilkan data yang dikembalikan oleh server
                        alert("Data presensi: Bulan " + respond.bulan + " dan Tahun " + respond
                            .tahun);
                        console.log(respond);
                    },
                    error: function(xhr, status, error) {
                        // Tampilkan pesan error jika ada kesalahan dalam AJAX
                        alert("Terjadi kesalahan: " + error);
                    }
                });
            });
        });
    </script>
@endpush
