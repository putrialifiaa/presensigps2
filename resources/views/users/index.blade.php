@extends('layouts.admin.tabler')
@section('content')
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <!--- Page pre-title --->

                    <h2 class="page-title">
                        Data User's
                    </h2>
                </div>
            </div>
        </div>
    </div>
    <div class="page-body">
        <div class="container-xl">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">
                                    <div class="row">
                                        <div class="col-12">
                                            @if (session('success'))
                                                <div class="alert alert-success">
                                                    {{ session('success') }}
                                                </div>
                                            @endif

                                            @if (session('warning'))
                                                <div class="alert alert-warning">
                                                    {{ session('warning') }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                </div>
                            </div>

                            <div class="row">
                                <div class="col-12">
                                    <a href="#" class="btn btn-primary" id="btnTambahUser">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round"
                                            class="icon icon-tabler icons-tabler-outline icon-tabler-plus">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                            <path d="M12 5l0 14" />
                                            <path d="M5 12l14 0" />
                                        </svg>
                                        Tambah Data
                                    </a>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-12">
                                    <form action="{{ URL::current() }}" method="GET">
                                        <div class="row">
                                            <div class="col-10">
                                                <div class="form-group">
                                                    <input type="text" name="name" id="name" class="form-control"
                                                        placeholder="Nama User" value="{{ Request('name') }}">
                                                </div>
                                            </div>
                                            <div class="col-2">
                                                <div class="form-group">
                                                    <button type="submit" class="btn btn-primary">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="24"
                                                            height="24" viewBox="0 0 24 24" fill="none"
                                                            stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                            stroke-linejoin="round"
                                                            class="icon icon-tabler icons-tabler-outline icon-tabler-search">
                                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                            <path d="M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0" />
                                                            <path d="M21 21l-6 -6" />
                                                        </svg>
                                                        Cari
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-12">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Nama</th>
                                                <th>E-mail</th>
                                                <th>Cabang</th>
                                                <th>Role</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($users as $d)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>{{ $d->name }}</td>
                                                    <td>{{ $d->email }}</td>
                                                    <td>{{ $d->nama_cabang }}</td>
                                                    <td>{{ ucwords($d->role) }}</td>
                                                    <td>
                                                        <div class="btn-group">
                                                            <a href="#" class="edit" id_user="{{ $d->id }}">
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="24"
                                                                    height="24" viewBox="0 0 24 24" fill="none"
                                                                    stroke="currentColor" stroke-width="2"
                                                                    stroke-linecap="round" stroke-linejoin="round"
                                                                    class="icon icon-tabler icons-tabler-outline icon-tabler-edit">
                                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                                    <path
                                                                        d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" />
                                                                    <path
                                                                        d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z" />
                                                                    <path d="M16 5l3 3" />
                                                                </svg>
                                                            </a>
                                                            <form action="/konfigurasi/users/{{ $d->id }}/delete"
                                                                method="POST" style="margin-left:5px">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button class="btn btn-danger btn-sm delete-confirm">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24"
                                                                        height="24" viewBox="0 0 24 24" fill="none"
                                                                        stroke="currentColor" stroke-width="2"
                                                                        stroke-linecap="round" stroke-linejoin="round"
                                                                        class="icon icon-tabler icons-tabler-outline icon-tabler-trash">
                                                                        <path stroke="none" d="M0 0h24v24H0z"
                                                                            fill="none" />
                                                                        <path d="M4 7l16 0" />
                                                                        <path d="M10 11l0 6" />
                                                                        <path d="M14 11l0 6" />
                                                                        <path
                                                                            d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" />
                                                                        <path
                                                                            d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" />
                                                                    </svg>
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                    {{ $users->links('vendor.pagination.bootstrap-5') }}
                                </div>
                            </div>
                        </div>

                    </div>

                </div>
            </div>
        </div>

    </div>
    <div class="modal modal-blur fade" id="modal-inputuser" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Data User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="/konfigurasi/users/store" method="POST" id="frmUser">
                        @csrf

                        <div class="row">
                            <div class="col-12">
                                <div class="input-icon">
                                    <span class="input-icon-addon">
                                        <!-- Download SVG icon from http://tabler-icons.io/i/user -->
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round"
                                            class="icon icon-tabler icons-tabler-outline icon-tabler-user">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                            <path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" />
                                            <path d="M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" />
                                        </svg>
                                    </span>
                                    <input type="text" id="nama_user" class="form-control" name="nama_user"
                                        placeholder="Nama User">
                                </div>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-12">
                                <div class="input-icon">
                                    <span class="input-icon-addon">
                                        <!-- Download SVG icon from http://tabler-icons.io/i/user -->
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round"
                                            class="icon icon-tabler icons-tabler-outline icon-tabler-mail">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                            <path
                                                d="M3 7a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v10a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2v-10z" />
                                            <path d="M3 7l9 6l9 -6" />
                                        </svg>
                                    </span>
                                    <input type="text" id="email" class="form-control" name="email"
                                        placeholder="Email">
                                </div>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-12">
                                <div class="form-group">
                                    <select name="kode_cabang" id="kode_cabang" class="form-select">
                                        <option value="">Cabang</option>
                                        @foreach ($cabang as $d)
                                            <option value="{{ $d->kode_cabang }}">{{ $d->nama_cabang }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-12">
                                <div class="form-group">
                                    <select name="role" id="role" class="form-select">
                                        <option value="">Role</option>
                                        @foreach ($role as $d)
                                            <option value="{{ $d->name }}">{{ ucwords($d->name) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-12">
                                <div class="input-icon">
                                    <span class="input-icon-addon">
                                        <!-- Download SVG icon from http://tabler-icons.io/i/user -->
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round"
                                            class="icon icon-tabler icons-tabler-outline icon-tabler-password-user">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                            <path d="M12 17v4" />
                                            <path d="M10 20l4 -2" />
                                            <path d="M10 18l4 2" />
                                            <path d="M5 17v4" />
                                            <path d="M3 20l4 -2" />
                                            <path d="M3 18l4 2" />
                                            <path d="M19 17v4" />
                                            <path d="M17 20l4 -2" />
                                            <path d="M17 18l4 2" />
                                            <path d="M9 6a3 3 0 1 0 6 0a3 3 0 0 0 -6 0" />
                                            <path d="M7 14a2 2 0 0 1 2 -2h6a2 2 0 0 1 2 2" />
                                        </svg>
                                    </span>
                                    <input type="password" id="password" class="form-control" name="password"
                                        placeholder="Password">
                                </div>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-12">
                                <div class="form-group">
                                    <button class="btn  btn-primary w-100">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round"
                                            class="icon icon-tabler icons-tabler-outline icon-tabler-send">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                            <path d="M10 14l11 -11" />
                                            <path
                                                d="M21 3l-6.5 18a.55 .55 0 0 1 -1 0l-3.5 -7l-7 -3.5a.55 .55 0 0 1 0 -1l18 -6.5" />
                                        </svg>
                                        Simpan
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>
    <div class="modal modal-blur fade" id="modal-edituser" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Data User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="loadedituser">

                </div>

            </div>
        </div>
    </div>
@endsection

@push('myscript')
    <script>
        $(function() {
            $("#btnTambahUser").click(function() {
                $("#modal-inputuser").modal("show");
            });

            $(".edit").click(function() {
                var id_user = $(this).attr(
                    'id_user');
                $.ajax({
                    type: 'POST',
                    url: '/konfigurasi/users/edit',
                    cache: false,
                    data: {
                        _token: "{{ csrf_token() }}",
                        id_user: id_user
                    },
                    success: function(respond) {
                        $("#loadedituser").html(respond);
                    }
                });
                $("#modal-edituser").modal("show");
            });

            $("#frmUser").submit(function() {
                var nama_user = $("#nama_user").val();
                var email = $("#email").val();
                var kode_cabang = $("#kode_cabang").val();
                var role = $("#role").val();

                if (nama_user == "") {
                    Swal.fire({
                        title: 'Warning!',
                        text: 'Nama Harus Diisi',
                        icon: 'warning',
                        confirmButtonText: 'Ok'
                    }).then(() => {
                        $("#nama_user").focus();
                    });

                    return false;
                } else if (email == "") {
                    Swal.fire({
                        title: 'Warning!',
                        text: 'E-mail Harus Diisi',
                        icon: 'warning',
                        confirmButtonText: 'Ok'
                    }).then(() => {
                        $("#email").focus();
                    });

                    return false;
                } else if (kode_cabang == "") {
                    Swal.fire({
                        title: 'Warning!',
                        text: 'Cabang Harus Diisi',
                        icon: 'warning',
                        confirmButtonText: 'Ok'
                    }).then(() => {
                        $("#kode_cabang").focus();
                    });

                    return false;
                } else if (role == "") {
                    Swal.fire({
                        title: 'Warning!',
                        text: 'Role Harus Diisi',
                        icon: 'warning',
                        confirmButtonText: 'Ok'
                    }).then(() => {
                        $("#role").focus();
                    });

                    return false;
                }
            });

            $(".delete-confirm").click(function(e) {
                var form = $(this).closest('form');
                e.preventDefault();
                Swal.fire({
                    title: "Apakah Anda Yakin Menghapus Data Ini?",
                    text: "Data Akan Dihapus Permanen",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: "Delete!",
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                        Swal.fire('Deleted!', 'Data Berhasil Dihapus', 'success')
                    }
                });
            });
        });
    </script>
@endpush
