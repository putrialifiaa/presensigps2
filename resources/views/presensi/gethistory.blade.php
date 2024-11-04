<style>
    .historicontent {
        display: flex;
    }

    .datapresensi {
        margin-left: 10px;
    }
</style>

@if ($history->isEmpty())
    <div class="alert alert-outline-warning">
        <p>Data Tidak Ada</p>
    </div>
@endif

@foreach ($history as $d)
    @if ($d->status == 'h')
        <div class="card" style="margin-bottom: 10px; padding: 10px;">
            <div class="card-body" style="padding: 5px;">
                <div class="historicontent" style="display: flex; align-items: center;">
                    <div class="iconpresensi" style="flex-shrink: 0;">
                        <ion-icon name="finger-print-outline" style="font-size: 30px;" class="text-primary"></ion-icon>
                    </div>
                    <div class="datapresensi" style="line-height: 1.2; margin-left: 10px;">
                        <h3 style="margin: 0; font-size: 14px;">{{ $d->nama_jam_kerja }}</h3>
                        <h4 style="margin: 0; font-size: 14px;">
                            {{ date('d-m-Y', strtotime($d->tgl_presensi)) }}</h4>
                        <span style="margin: 0; font-size: 14px;">
                            {!! $d->jam_in != null ? date('H:i', strtotime($d->jam_in)) : '<span class="text-danger">Belum Absen</span>' !!}
                        </span>
                        <span style="margin: 0; font-size: 14px;">
                            {!! $d->jam_out != null
                                ? '=' . date('H:i', strtotime($d->jam_out))
                                : '<span class="text-danger">- Belum Absen</span>' !!}
                        </span>
                        <div id="keterangan" class="mt-2">
                            @php
                                //Jam Ketika Absen
                                $jam_in = date('H:i', strtotime($d->jam_in));

                                //Jam Jadwal Masuk
                                $jam_masuk = date('H:i', strtotime($d->jam_masuk));

                                $jadwal_jam_masuk = $d->tgl_presensi . ' ' . $jam_masuk;
                                $jam_presensi = $d->tgl_presensi . ' ' . $jam_in;
                            @endphp
                            @if ($jam_in > $jam_masuk)
                                @php
                                    $jmlterlambat = hitungjamterlambat($jadwal_jam_masuk, $jam_presensi);
                                    $jmlterlambatdesimal = hitungjamterlambatdesimal($jadwal_jam_masuk, $jam_presensi);
                                @endphp
                                <span class="danger">Terlambat {{ $jmlterlambat }}
                                    ({{ $jmlterlambatdesimal }} Jam)
                                </span>
                            @else
                                <span style="color:green">Tepat Waktu</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @elseif ($d->status == 'i')
        <div class="card" style="margin-bottom: 10px; padding: 10px;">
            <div class="card-body" style="padding: 5px;">
                <div class="historicontent" style="display: flex; align-items: center;">
                    <div class="iconpresensi" style="flex-shrink: 0;">
                        <ion-icon name="document-text-outline" style="font-size: 30px;" class="text-success"></ion-icon>
                    </div>
                    <div class="datapresensi" style="line-height: 1.2; margin-left: 10px;">
                        <h3 style="margin: 0; font-size: 14px;">IZIN - {{ $d->kode_izin }}</h3>
                        <h4 style="margin: 0; font-size: 14px;">
                            {{ date('d-m-Y', strtotime($d->tgl_presensi)) }}</h4>
                        <span>
                            {{ $d->keterangan }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    @elseif ($d->status == 's')
        <div class="card" style="margin-bottom: 10px; padding: 10px;">
            <div class="card-body" style="padding: 5px;">
                <div class="historicontent" style="display: flex; align-items: center;">
                    <div class="iconpresensi" style="flex-shrink: 0;">
                        <ion-icon name="medkit-outline" style="font-size: 30px;" class="text-warning"></ion-icon>
                    </div>
                    <div class="datapresensi" style="line-height: 1.2; margin-left: 10px;">
                        <h3 style="margin: 0; font-size: 14px;">SAKIT - {{ $d->kode_izin }}</h3>
                        <h4 style="margin: 0; font-size: 14px;">
                            {{ date('d-m-Y', strtotime($d->tgl_presensi)) }}</h4>
                        <span>
                            {{ $d->keterangan }}
                        </span>
                        <br>
                        @if (!empty($d->doc_sid))
                            <span style="display: block; margin-top: 5px; color: rgba(128, 128, 128, 0.7);">
                                <ion-icon name="attach-outline" style="vertical-align: middle;"></ion-icon>
                                Surat Dokter
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @elseif ($d->status == 'c')
        <div class="card" style="margin-bottom: 10px; padding: 10px;">
            <div class="card-body" style="padding: 5px;">
                <div class="historicontent" style="display: flex; align-items: center;">
                    <div class="iconpresensi" style="flex-shrink: 0;">
                        <ion-icon name="calendar-number-outline" style="font-size: 30px;"
                            class="text-danger"></ion-icon>
                    </div>
                    <div class="datapresensi" style="line-height: 1.2; margin-left: 10px;">
                        <h3 style="margin: 0; font-size: 14px;">CUTI - {{ $d->kode_izin }}</h3>
                        <h4 style="margin: 0; font-size: 14px;">
                            {{ date('d-m-Y', strtotime($d->tgl_presensi)) }}</h4>
                        <span class="text-secondary">
                            {{ $d->nama_cuti }}
                        </span>
                        <br>
                        <span>
                            {{ $d->keterangan }}
                        </span>
                        <br>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endforeach
