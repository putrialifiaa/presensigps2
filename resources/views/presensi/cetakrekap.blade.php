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
            background-color: #a4c2b2;
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
                <th colspan="31">Tanggal</th>
                <th rowspan="2">TH</th>
                <th rowspan="2">TT</th>
            </tr>
            <tr>
                <?php for ($i = 1; $i <= 31; $i++) { ?>
                <th>{{ $i }}</th>
                <?php } ?>
            </tr>
            @foreach ($rekap as $d)
                <tr>
                    <td>{{ $d->nik }}</td>
                    <td>{{ $d->nama_lengkap }}</td>

                    <?php
                    $totalhadir = 0;
                    $totalterlambat = 0;
                    for ($i = 1; $i <= 31; $i++) {
                        $tgl = "tgl_" . $i;
                        if (empty($d->$tgl)) {
                            $hadir = ['', ''];
                            $totalhadir += 0;
                        } else {
                            $hadir = explode("-", $d->$tgl);
                            $totalhadir += 1;
                            if ($hadir[0] > $d->jam_masuk) {
                                $totalterlambat +=1;
                            }
                        }
                    ?>
                    <td>
                        <span
                            style="color:{{ $hadir[0] > $d->jam_masuk ? 'red' : '' }}">{{ !empty($hadir[1]) ? $hadir[0] : '-' }}</span><br>
                        <span
                            style="color:{{ $hadir[1] < $d->jam_pulang ? 'red' : '' }}">{{ !empty($hadir[1]) ? $hadir[1] : '-' }}</span>
                    </td>

                    <?php
                    }
                    ?>
                    <td>{{ $totalhadir }}</td>
                    <td>{{ $totalterlambat }}</td>
                </tr>
            @endforeach
        </table>

        <table width="100%" style="margin-top: 50px; margin-bottom: 50px;">
            <tr>
                <td></td>
                <td style="text-align: center; padding-right: 30px; padding-bottom: 50px;">Gresik,
                    {{ date('d-m-Y') }}</td>
            </tr>
            <tr>
                <td style="text-align: center; vertical-align: bottom; padding-top: 80px;">
                    <u>Nama HRD</u><br>
                    <b>HRD Manager</b>
                </td>
                <td style="text-align: center; vertical-align: bottom; padding-top: 80px;">
                    <u>Nama Direktur</u><br>
                    <b>Direktur</b>
                </td>
            </tr>
        </table>

    </section>
</body>

</html>
