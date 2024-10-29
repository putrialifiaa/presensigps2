<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>A4</title>

    <!-- Normalize or reset CSS with your favorite library -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/7.0.0/normalize.min.css">

    <!-- Load paper.css for happy printing -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/paper-css/0.4.1/paper.css">

    <!-- Set page size here: A5, A4 or A3 -->
    <style>
        @page {
            size: A4
        }

        h3 {
            font-family: 'Times New Roman', Times, serif;
            font-size: 14px;
            font-weight: 600;
        }

        .thin-font {
            font-size: 14px;
            font-weight: 300;
        }

        .tabeldatakaryawan {
            margin-top: 40px;
        }

        .tabelpresensi {
            width: 100%;
            max-width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
            overflow-x: auto;
            /* Tambahkan scroll jika tabel terlalu lebar */
        }

        .tabelpresensi tr th {
            border: 1px solid #000000;
            padding: 4px;
            /* Kurangi padding */
            background-color: #c9c9c9;
            font-size: 10px;
        }

        .tabelpresensi tr td {
            border: 1px solid #000000;
            padding: 4px;
            /* Kurangi padding */
            font-size: 12px;
            /* Sesuaikan ukuran font */
            word-wrap: break-word;
            /* Membungkus teks panjang */
        }

        .foto {
            width: 40px;
            height: 30px;
        }

        @media print {

            .tabelpresensi tr th,
            .tabelpresensi tr td {
                font-size: 8px;
                /* Sesuaikan ukuran font untuk cetak */
            }
        }
    </style>
</head>

<body class="A4 landscape">
    <?php
    function selisih($jam_masuk, $jam_keluar)
    {
        [$h, $m, $s] = explode(':', $jam_masuk);
        $dtAwal = mktime($h, $m, $s, '1', '1', '1');
    
        [$h, $m, $s] = explode(':', $jam_keluar);
        $dtAkhir = mktime($h, $m, $s, '1', '1', '1');
    
        $dtSelisih = $dtAkhir - $dtAwal;
        $totalmenit = $dtSelisih / 60;
    
        $jml_jam = floor($totalmenit / 60);
        $sisamenit = $totalmenit % 60;
    
        return sprintf('%d:%02d', $jml_jam, $sisamenit);
    }
    ?>
    <section class="sheet padding-10mm">

        <table style="width: 100%">
            <tr>
                <td style="width: 30px">
                    <img src="{{ asset('assets/img/logopresensi.png') }}" width="90" height="70">
                </td>
                <td>
                    <h3>REKAP PRESENSI KARYAWAN BANK MITRA SYARIAH<br>
                        <span class="thin-font">PERIODE {{ strtoupper($namabulan[$bulan]) }}
                            {{ $tahun }}</span><br>
                    </h3>
                </td>
            </tr>
        </table>

        <table class="tabelpresensi">
            <tr>
                <th rowspan="2">NIK</th>
                <th rowspan="2">Nama Karyawan</th>
                <th colspan="{{ $jmlhari }}">Bulan {{ $namabulan[$bulan] }} {{ $tahun }}</th>
                <th rowspan="2">TH</th>
                <th rowspan="2">TT</th>
            </tr>
            <tr>
                @foreach ($rangetanggal as $d)
                    @if ($d != null)
                        <th>{{ date('d', strtotime($d)) }}</th>
                    @endif
                @endforeach

            </tr>
            @foreach ($rekap as $r)
                <tr>
                    <td>{{ $r->nik }}</td>
                    <td>{{ $r->nama_lengkap }}</td>

                    <?php
                        for ($i = 1; $i <= $jmlhari - 1; $i++) {
                            $tgl = 'tgl_' . $i;
                            $datapresensi = explode("|",$r->$tgl);
                        ?>
                    <td>
                        @if ($r->$tgl != null)
                            {{ $datapresensi[2] }}
                        @endif

                    </td>
                    <?php
                       }
                        ?>
                    </td>
                </tr>
            @endforeach
        </table>

        <table width="100%" style="margin-top: 50px; margin-bottom: 50px;">
            <tr>
                <td></td>
                <td style="text-align: right; padding-right: 30px; padding-bottom: 50px; padding-left: 20px;">Gresik,
                    {{ date('d-m-Y') }}</td>
            </tr>
        </table>

    </section>
</body>

</html>
