@extends('layouts.presensi')
@section('content')
    <style>
        .logout {
            position: absolute;
            color: white;
            font-size: 30px;
            text-decoration: none;
            right: 5px;
        }

        .logout:hover {
            color: white;

        }
    </style>
    <div class="section" id="user-section">
        <a href="/proseslogout" class="logout">
            <ion-icon name="log-out-outline"></ion-icon>
        </a>
        <div id="user-detail">
            <div class="avatar">
                @if (!empty(Auth::guard('karyawan')->user()->foto))
                    @php
                        $path = Storage::url('uploads/karyawan/' . Auth::guard('karyawan')->user()->foto);
                    @endphp
                    <img src="{{ url($path) }}" alt="avatar" class="imaged w64" style="height:60px">
                @else
                    <img src="assets/img/sample/avatar/avatar1.jpg" alt="avatar" class="imaged w64 rounded">
                @endif
            </div>
            <div id="user-info">
                <h3 id="user-name">{{ Auth::guard('karyawan')->user()->nama_lengkap }}</h3>
                <span id="user-role">{{ Auth::guard('karyawan')->user()->jabatan }}</span>
                <span id="user-role">({{ Auth::guard('karyawan')->user()->kode_cabang }})</span>
            </div>
        </div>
    </div>

    <div class="section" id="menu-section">
        <div class="card">
            <div class="card-body text-center">
                <div class="list-menu">
                    <div class="item-menu text-center">
                        <div class="menu-icon">
                            <a href="/editprofile" class="green" style="font-size: 40px;">
                                <ion-icon name="person-sharp"></ion-icon>
                            </a>
                        </div>
                        <div class="menu-name">
                            <span class="text-center">Profil</span>
                        </div>
                    </div>
                    <div class="item-menu text-center">
                        <div class="menu-icon">
                            <a href="/presensi/izin" class="danger" style="font-size: 40px;">
                                <ion-icon name="calendar-number"></ion-icon>
                            </a>
                        </div>
                        <div class="menu-name">
                            <span class="text-center">Cuti</span>
                        </div>
                    </div>
                    <div class="item-menu text-center">
                        <div class="menu-icon">
                            <a href="/presensi/history" class="warning" style="font-size: 40px;">
                                <ion-icon name="document-text"></ion-icon>
                            </a>
                        </div>
                        <div class="menu-name">
                            <span class="text-center">Histori</span>
                        </div>
                    </div>
                    <div class="item-menu text-center">
                        <div class="menu-icon">
                            <a href="" class="orange" style="font-size: 40px;">
                                <ion-icon name="location"></ion-icon>
                            </a>
                        </div>
                        <div class="menu-name">
                            Lokasi
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="section mt-2" id="presence-section">
        <div class="todaypresence">
            <div class="row">
                <div class="col-6">
                    <div class="card gradasigreen">
                        <div class="card-body">
                            <div class="presencecontent">
                                <div class="iconpresence">
                                    @if ($presensihariini !== null)
                                        @php
                                            $path = Storage::url('uploads/absensi/' . $presensihariini->foto_in);
                                        @endphp
                                        <img src="{{ url($path) }}" alt="" class="imaged w48">
                                    @else
                                        <ion-icon name="camera"></ion-icon>
                                    @endif
                                </div>
                                <div class="presencedetail">
                                    <h4 class="presencetitle">Masuk</h4>
                                    <span>{{ $presensihariini !== null ? $presensihariini->jam_in : 'Belum Absen' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="card gradasired">
                        <div class="card-body">
                            <div class="presencecontent">
                                <div class="iconpresence">
                                    @if ($presensihariini !== null && $presensihariini->jam_out !== null)
                                        @php
                                            $path = Storage::url('uploads/absensi/' . $presensihariini->foto_out);
                                        @endphp
                                        <img src="{{ url($path) }}" alt="" class="imaged w48">
                                    @else
                                        <ion-icon name="camera"></ion-icon>
                                    @endif
                                </div>
                                <div class="presencedetail">
                                    <h4 class="presencetitle">Pulang</h4>
                                    <span>{{ $presensihariini !== null && $presensihariini->jam_out !== null ? $presensihariini->jam_out : 'Belum Absen' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div id="rekappresensi">
            <h3>Rekap Presensi Bulan {{ $namabulan[$bulanini] }} Tahun {{ $tahunini }}</h3>
            <div class="row">
                <div class="col-3">
                    <div class="card">
                        <div class="card-body text-center" style="padding:8px !important; line-height:0.8rem">
                            <div style="position: relative; display: inline-block; width: 40px; height: 40px;">
                                <span class="badge bg-danger rounded-circle"
                                    style="position: absolute; top: -8px; right: -8px; width: 20px; height: 20px; display: flex;
                                    justify-content: center; align-items: center; font-size: 0.7rem;">{{ $rekappresensi->jumlah_hadir }}
                                </span>
                                <ion-icon name="man-outline" style="font-size: 1.8rem;" class="text-primary"></ion-icon>
                            </div>
                            <br><span
                                style="font-size: 0.7rem; font-weight: 500; margin-top: -8px; display: block;">Hadir</span>
                        </div>
                    </div>
                </div>
                <div class="col-3">
                    <div class="card">
                        <div class="card-body text-center" style="padding:8px !important; line-height:0.8rem">
                            <div style="position: relative; display: inline-block; width: 40px; height: 40px;">
                                <span class="badge bg-danger rounded-circle"
                                    style="position: absolute; top: -8px; right: -8px; width: 20px; height: 20px; display: flex;
                                    justify-content: center; align-items: center; font-size: 0.7rem;">{{ $rekapizin->jmlizin }}</span>
                                <ion-icon name="clipboard-outline" style="font-size: 1.8rem;"
                                    class="text-success"></ion-icon>
                            </div>
                            <br><span
                                style="font-size: 0.7rem; font-weight: 500; margin-top: -8px; display: block;">Izin</span>
                        </div>
                    </div>
                </div>
                <div class="col-3">
                    <div class="card">
                        <div class="card-body text-center" style="padding:8px !important; line-height:0.8rem">
                            <div style="position: relative; display: inline-block; width: 40px; height: 40px;">
                                <span class="badge bg-danger rounded-circle"
                                    style="position: absolute; top: -8px; right: -8px; width: 20px; height: 20px; display: flex;
                                    justify-content: center; align-items: center; font-size: 0.7rem;">{{ $rekapizin->jmlsakit }}</span>
                                <ion-icon name="medkit-outline" style="font-size: 1.8rem;"
                                    class="text-warning"></ion-icon>
                            </div>
                            <br><span
                                style="font-size: 0.7rem; font-weight: 500; margin-top: -8px; display: block;">Sakit</span>
                        </div>
                    </div>
                </div>
                <div class="col-3">
                    <div class="card">
                        <div class="card-body text-center" style="padding:8px !important; line-height:0.8rem">
                            <div style="position: relative; display: inline-block; width: 40px; height: 40px;">
                                <span class="badge bg-danger rounded-circle"
                                    style="position: absolute; top: -8px; right: -8px; width: 20px; height: 20px; display: flex;
                                    justify-content: center; align-items: center; font-size: 0.7rem;">{{ $rekappresensi->jumlah_terlambat }}
                                </span>
                                <ion-icon name="alarm-outline" style="font-size: 1.8rem;" class="text-danger"></ion-icon>
                            </div>
                            <br><span
                                style="font-size: 0.7rem; font-weight: 500; margin-top: -8px; display: block;">Terlambat</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="presencetab mt-2">
            <div class="tab-pane fade show active" id="pilled" role="tabpanel">
                <ul class="nav nav-tabs style1" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" data-toggle="tab" href="#home" role="tab">
                            Bulan Ini
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#profile" role="tab">
                            Leaderboard
                        </a>
                    </li>
                </ul>
            </div>
            <div class="tab-content mt-2" style="margin-bottom:100px;">
                <div class="tab-pane fade show active" id="home" role="tabpanel">
                    <!-------
                                                                                                                                                                                                                                                                <ul class="listview image-listview">
                                                                                                                                                                                                                                                                    @foreach ($historibulanini as $d)
    @php
        $path = Storage::url('uploads/absensi/' . $d->foto_in);
    @endphp
                                                                                                                                                                                                                                                                        <li>
                                                                                                                                                                                                                                                                            <div class="item">
                                                                                                                                                                                                                                                                                <div class="icon-box bg-primary">
                                                                                                                                                                                                                                                                                    <ion-icon name="image-outline" role="img" class="md hydrated"
                                                                                                                                                                                                                                                                                        aria-label="image outline"></ion-icon>
                                                                                                                                                                                                                                                                                </div>
                                                                                                                                                                                                                                                                                <div class="in">
                                                                                                                                                                                                                                                                                    <div>{{ date('d-m-y', strtotime($d->tgl_presensi)) }}</div>
                                                                                                                                                                                                                                                                                    <span class="badge badge-success">{{ $d->jam_in }}</span>
                                                                                                                                                                                                                                                                                    <span
                                                                                                                                                                                                                                                                                        class="badge badge-danger">{{ $presensihariini !== null && $d->jam_out !== null ? $d->jam_out : 'Belum Absen' }}</span>
                                                                                                                                                                                                                                                                                </div>
                                                                                                                                                                                                                                                                            </div>
                                                                                                                                                                                                                                                                        </li>
    @endforeach
                                                                                                                                                                                                                                                                </ul>
                                                                                                                                                                                                                                                            ga dihapus sp tau nnti dipake lgi------>
                    <style>
                        .historicontent {
                            display: flex;
                        }

                        .datapresensi {
                            margin-left: 10px;
                        }
                    </style>
                    @foreach ($historibulanini as $d)
                        @if ($d->status == 'h')
                            <div class="card" style="margin-bottom: 10px; padding: 10px;">
                                <div class="card-body" style="padding: 5px;">
                                    <div class="historicontent" style="display: flex; align-items: center;">
                                        <div class="iconpresensi" style="flex-shrink: 0;">
                                            <ion-icon name="finger-print-outline" style="font-size: 30px;"
                                                class="text-primary"></ion-icon>
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
                                                        $jmlterlambat = hitungjamterlambat(
                                                            $jadwal_jam_masuk,
                                                            $jam_presensi,
                                                        );
                                                        $jmlterlambatdesimal = hitungjamterlambatdesimal(
                                                            $jadwal_jam_masuk,
                                                            $jam_presensi,
                                                        );
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
                                            <ion-icon name="document-text-outline" style="font-size: 30px;"
                                                class="text-success"></ion-icon>
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
                                            <ion-icon name="medkit-outline" style="font-size: 30px;"
                                                class="text-warning"></ion-icon>
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
                                                <span
                                                    style="display: block; margin-top: 5px; color: rgba(128, 128, 128, 0.7);">
                                                    <ion-icon name="attach-outline"
                                                        style="vertical-align: middle;"></ion-icon>
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
                </div>
                <div class="tab-pane fade" id="profile" role="tabpanel">
                    <ul class="listview image-listview">
                        @foreach ($leaderboard as $d)
                            <li>
                                <div class="item">
                                    <img src="assets/img/sample/avatar/avatar1.jpg" alt="image" class="image">
                                    <div class="in">
                                        <div>
                                            <b>{{ $d->nama_lengkap }}</b><br>
                                            <small class="text-muted">{{ $d->jabatan }}</small>
                                        </div>
                                        <span class="badge {{ $d->jam_in < '07:30' ? 'bg-success' : 'bg-danger' }}">
                                            {{ $d->jam_in }}
                                        </span>
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection
