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
    <div class="row">
        <div class="col">
            <div class="row" style="margin-top:70px">
                <div class="col-7 pr-0">
                    <div class="form-group">
                        <select name="bulan" id="bulan" class="form-control">
                            <option value="">Bulan</option>
                            @for ($i = 1; $i <= 12; $i++)
                                <option {{ Request('bulan') == $i ? 'selected' : '' }} value="{{ $i }}">
                                    {{ $namabulan[$i] }}</option>
                            @endfor
                        </select>
                    </div>
                </div>
                <div class="col-5 pl-1">
                    <div class="form-group">
                        <select name="tahun" id="tahun" class="form-control">
                            <option value="">Tahun</option>
                            @php
                                $tahun_awal = 2022;
                                $tahun_sekarang = date('Y');
                            @endphp
                            @for ($t = $tahun_awal; $t <= $tahun_sekarang; $t++)
                                <option value="{{ $t }}" {{ Request('tahun') == $t ? 'selected' : '' }}>
                                    {{ $t }}</option>
                            @endfor
                        </select>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <button class="btn btn-primary mt-0 mb-2" id="getdata">Cari Data</button>
                </div>
            </div>
        </div>
    </div>
    <div class="row" style="position:fixed; width:100%; margin:auto; overflow-y:scroll; height:430px">
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
                        // Menampilkan data di elemen #showhistory
                        $("#showhistory").html(respond);
                    },
                    error: function(xhr, status, error) {
                        alert("Terjadi kesalahan: " + error);
                    }
                });
            });
        });
    </script>
@endpush
