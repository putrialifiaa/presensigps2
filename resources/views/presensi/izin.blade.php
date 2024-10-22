@extends('layouts.presensi')
@section('header')
    <!--- App Header --->
    <div class="appHeader bg-primary text-light">
        <div class="left">
            <a href="javascript:;" class="headerButton goBack">
                <ion-icon name="chevron-back-outline"></ion-icon>
            </a>
        </div>
        <div class="pageTitle">Data Izin atau Sakit</div>
        <div class="right"></div>
    </div>
    <!--- App Header --->
@endsection

@section('content')
    <div class="row" style="margin-top:70px">
        <div class="col">
            @php
                $messagesuccess = Session::get('success');
                $messageerror = Session::get('error');
            @endphp
            @if ($messagesuccess)
                <div class="alert alert-success">
                    {{ $messagesuccess }}
                </div>
            @endif
            @if ($messageerror)
                <div class="alert alert-danger">
                    {{ $messageerror }}
                </div>
            @endif
        </div>
    </div>
    <div class="row">
        <div class="col">
            @foreach ($dataizin as $d)
                @php
                    if ($d->status == 'i') {
                        $status = 'Izin';
                    } elseif ($d->status == 's') {
                        $status = 'Sakit';
                    } elseif ($d->status == 'c') {
                        $status = 'Cuti';
                    } else {
                        $status = 'Not Found';
                    }
                @endphp
                <div class="card" style="margin-bottom: 10px; padding: 10px;">
                    <div class="card-body" style="padding: 5px;">
                        <div class="historicontent" style="display: flex; align-items: center;">
                            <div class="iconpresensi" style="flex-shrink: 0;">
                                @if ($d->status == 'i')
                                    <ion-icon name="document-text-outline"
                                        style="font-size: 40px; color:rgb(92, 132, 206)"></ion-icon>
                                @elseif ($d->status == 's')
                                    <ion-icon name="medkit-outline"
                                        style="font-size: 35px; color:rgb(187, 27, 67)"></ion-icon>
                                @endif
                            </div>
                            <div class="datapresensi" style="line-height: 1.2; margin-left: 10px;">
                                <h3 style="margin: 0 0 5px 0; font-size: 16px;">
                                    {{ date('d-m-Y', strtotime($d->tgl_izin_dari)) }} ({{ $status }})</h3>

                                <small style="font-size: 15px;">
                                    {{ date('d-m-Y', strtotime($d->tgl_izin_dari)) }} s/d
                                    {{ date('d-m-Y', strtotime($d->tgl_izin_sampai)) }}
                                </small>
                                <p style="font-size: 15px; margin-bottom: 5px;">{{ $d->keterangan }}
                                    <br>
                                    @if (!empty($d->doc_sid))
                                        <span style="display: block; margin-top: 5px; color: rgba(128, 128, 128, 0.7);">
                                            <ion-icon name="attach-outline" style="vertical-align: middle;"></ion-icon>
                                            Surat Dokter
                                        </span>
                                    @endif
                                </p>
                            </div>
                            <div class="status" style="margin-left: auto; align-self: flex-start;">
                                @if ($d->status_approved == 0)
                                    <span class="badge bg-warning">Pending</span>
                                @elseif ($d->status_approved == '1')
                                    <span class="badge bg-success">Disetujui</span>
                                @elseif ($d->status_approved == '2')
                                    <span class="badge bg-danger">Ditolak</span>
                                @endif
                                <p style="margin-top:5px; font-weight:bold">
                                    {{ hitunghari($d->tgl_izin_dari, $d->tgl_izin_sampai) }} Hari</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!--
                                                                                                                                                                                    <ul class="listview image-listview">
                                                                                                                                                                                        <li>
                                                                                                                                                                                            <div class="item">
                                                                                                                                                                                                <div class="in">
                                                                                                                                                                                                    <div>
                                                                                                                                                                                                        <b>{{ date('d-m-Y', strtotime($d->tgl_izin_dari)) }}
                                                                                                                                                                                                            ({{ $d->status == 's' ? 'Sakit' : 'Izin' }})
    </b><br>
                                                                                                                                                                                                        <small class="text-muted">{{ $d->keterangan }}</small>
                                                                                                                                                                                                    </div>
                                                                                                                                                                                                    @if ($d->status_approved == 0)
    <span class="badge bg-warning">Waiting</span>
@elseif ($d->status_approved == 1)
    <span class="badge bg-success">Approved</span>
@elseif ($d->status_approved == 2)
    <span class="badge bg-danger">Dismissed</span>
    @endif
                                                                                                                                                                                                </div>
                                                                                                                                                                                            </div>
                                                                                                                                                                                        </li>
                                                                                                                                                                                    </ul>
                                                                                                                                                                                -->
            @endforeach
        </div>
    </div>
    <div class="fab-button bottom-right" style="margin-bottom: 70px">
        <a href="/presensi/buatizin" class="fab">
            <ion-icon name="add-outline"></ion-icon>
        </a>
    </div>
    <div class="fab-button animate bottom-right dropdown" style="margin-bottom:70px">
        <a href="#" class="fab bg-primary" data-toggle="dropdown">
            <ion-icon name="add-outline" role="img" class="md hydrated" aria-label="add outline"></ion-icon>
        </a>
        <div class="dropdown-menu">
            <a class="dropdown-item bg-primary" href="/izinabsen">
                <ion-icon name="document-outline" role="img" class="md hydrated" aria-label="image outline"></ion-icon>
                <p>Izin</p>
            </a>

            <a class="dropdown-item bg-primary" href="/izinsakit">
                <ion-icon name="document-outline" role="img" class="md hydrated"
                    aria-label="videocam outline"></ion-icon>
                <p>Sakit</p>
            </a>
            <a class="dropdown-item bg-primary" href="/izincuti">
                <ion-icon name="document-outline" role="img" class="md hydrated"
                    aria-label="videocam outline"></ion-icon>
                <p>Cuti</p>
            </a>
        </div>
    </div>
@endsection
