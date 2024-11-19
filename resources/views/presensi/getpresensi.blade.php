<?php
function selisih($jam_masuk, $jam_keluar)
{
    // Mengambil jam, menit, dan detik dari waktu masuk
    [$h, $m, $s] = explode(':', $jam_masuk);
    $dtAwal = mktime($h, $m, $s, '1', '1', '1');

    // Mengambil jam, menit, dan detik dari waktu keluar
    [$h, $m, $s] = explode(':', $jam_keluar);
    $dtAkhir = mktime($h, $m, $s, '1', '1', '1');

    // Menghitung selisih dalam detik
    $dtSelisih = $dtAkhir - $dtAwal;

    // Menghitung total menit
    $totalmenit = $dtSelisih / 60;

    // Menghitung jam dan menit
    $jml_jam = floor($totalmenit / 60); // Menggunakan floor untuk mendapatkan jam
    $sisamenit = $totalmenit % 60; // Menggunakan modulus untuk mendapatkan sisa menit

    // Format output jam:menit
    return sprintf('%d:%02d', $jml_jam, $sisamenit);
}
?>
@foreach ($presensi as $d)
    @php
        $foto_in = Storage::url('uploads/absensi/' . $d->foto_in);
        $foto_out = Storage::url('uploads/absensi/' . $d->foto_out);
    @endphp
    @if ($d->status == 'h')
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $d->nik }}</td>
            <td>{{ $d->nama_lengkap }}</td>
            <td>{{ $d->nama_cabang }}</td>
            <td>{{ $d->kode_dept }}</td>
            <td>{{ $d->nama_jam_kerja }} ({{ $d->jam_masuk }} s/d {{ $d->jam_pulang }})</td>
            <td>{{ $d->jam_in }}</td>
            <td>
                <img src="{{ url($foto_in) }}" class="avatar" alt="">
            </td>
            <td>{!! $d->jam_out != null ? $d->jam_out : '<span class="badge bg-danger text-white">Belum Absen</span>' !!}</td>
            <td>
                @if ($d->jam_out != null)
                    <img src="{{ url($foto_out) }}" class="avatar" alt="">
                @else
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        class="icon icon-tabler icons-tabler-outline icon-tabler-hourglass-high">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M6.5 7h11" />
                        <path d="M6 20v-2a6 6 0 1 1 12 0v2a1 1 0 0 1 -1 1h-10a1 1 0 0 1 -1 -1z" />
                        <path d="M6 4v2a6 6 0 1 0 12 0v-2a1 1 0 0 0 -1 -1h-10a1 1 0 0 0 -1 1z" />
                    </svg>
                @endif

            </td>
            <td>
                @if ($d->status == 'h')
                    <span class="badge" style="background-color: #FFFF">H</span>
                @endif
            </td>
            <td>
                @if ($d->jam_in >= $d->jam_masuk)
                    @php
                        $jamterlambat = selisih($d->jam_masuk, $d->jam_in);
                    @endphp
                    <span class="badge bg-danger text-white">Terlambat {{ $jamterlambat }}</span>
                @else
                    <span class="badge bg-success text-white">Tepat Waktu</span>
                @endif
            </td>
            <td>
                <a href="#" class="btn btn-primary tampilkanpeta" id="{{ $d->id }}">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-map-2">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M12 18.5l-3 -1.5l-6 3v-13l6 -3l6 3l6 -3v7.5" />
                        <path d="M9 4v13" />
                        <path d="M15 7v5.5" />
                        <path
                            d="M21.121 20.121a3 3 0 1 0 -4.242 0c.418 .419 1.125 1.045 2.121 1.879c1.051 -.89 1.759 -1.516 2.121 -1.879z" />
                        <path d="M19 18v.01" />
                    </svg>
                </a>
            </td>
        </tr>
    @else
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $d->nik }}</td>
            <td>{{ $d->nama_lengkap }}</td>
            <td>{{ $d->nama_cabang }}</td>
            <td>{{ $d->kode_dept }}</td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td>
                @if ($d->status == 'i')
                    <span class="badge" style="background-color: #fff171">I</span>
                @elseif ($d->status == 's')
                    <span class="badge" style="background-color: #ff8800">S</span>
                @elseif ($d->status == 'c')
                    <span class="badge" style="background-color: #C7F9B3">C</span>
                @endif
            </td>
            <td>{{ $d->keterangan }}</td>
            <td></td>
        </tr>
    @endif
@endforeach

<script>
    $(function() {
        $(".tampilkanpeta").click(function(e) {
            var id = $(this).attr("id");
            $.ajax({
                type: 'POST',
                url: '/tampilkanpeta',
                data: {
                    _token: "{{ csrf_token() }}",
                    id: id
                },
                cache: false,
                success: function(respond) {
                    $("#loadmap").html(respond);
                }
            });
            $("#modal-tampilkanpeta").modal("show");
        });
    });
</script>
