<form action="/konfigurasi/users/{{ $user->id }}/update" method="POST" id="frmUser">
    @csrf

    <div class="row">
        <div class="col-12">
            <div class="input-icon">
                <span class="input-icon-addon">
                    <!-- Download SVG icon from http://tabler-icons.io/i/user -->
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-user">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" />
                        <path d="M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" />
                    </svg>
                </span>
                <input type="text" id="nama_user" value="{{ $user->name }}" class="form-control" name="nama_user"
                    placeholder="Nama User">
            </div>
        </div>
    </div>
    <div class="row mt-2">
        <div class="col-12">
            <div class="input-icon">
                <span class="input-icon-addon">
                    <!-- Download SVG icon from http://tabler-icons.io/i/user -->
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-mail">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M3 7a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v10a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2v-10z" />
                        <path d="M3 7l9 6l9 -6" />
                    </svg>
                </span>
                <input type="text" id="email" value="{{ $user->email }}" class="form-control" name="email"
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
                        <option {{ $user->kode_cabang == $d->kode_cabang ? 'selected' : '' }}
                            value="{{ $d->kode_cabang }}">{{ $d->nama_cabang }}</option>
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
                        <option {{ $user->role_id == $d->id ? 'selected' : '' }} value="{{ $d->id }}">
                            {{ ucwords($d->name) }}</option>
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
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-password-user">
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
                <input type="password" id="password" class="form-control" name="password" placeholder="Password">
            </div>
        </div>
    </div>
    <div class="row mt-2">
        <div class="col-12">
            <div class="form-group">
                <button class="btn  btn-primary w-100">
                    Update
                </button>
            </div>
        </div>
    </div>
</form>
