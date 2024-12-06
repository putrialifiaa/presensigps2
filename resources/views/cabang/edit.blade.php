<form action="/cabang/update" method="POST" id="frmCabangEdit">
    @csrf
    <input type="hidden" name="kode_cabang_lama" value="{{ $cabang->kode_cabang }}">
    <div class="row">
        <div class="col-12">
            <div class="input-icon mb-3">
                <span class="input-icon-addon">
                    <!-- Download SVG icon from http://tabler-icons.io/i/user -->
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-barcode">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M4 7v-1a2 2 0 0 1 2 -2h2" />
                        <path d="M4 17v1a2 2 0 0 0 2 2h2" />
                        <path d="M16 4h2a2 2 0 0 1 2 2v1" />
                        <path d="M16 20h2a2 2 0 0 0 2 -2v-1" />
                        <path d="M5 11h1v2h-1z" />
                        <path d="M10 11l0 2" />
                        <path d="M14 11h1v2h-1z" />
                        <path d="M19 11l0 2" />
                    </svg>
                </span>
                <input type="text" value="{{ $cabang->kode_cabang }}" id="kode_cabang" class="form-control"
                    placeholder="Kode Lokasi" name="kode_cabang">
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="input-icon mb-3">
                <span class="input-icon-addon">
                    <!-- Download SVG icon from http://tabler-icons.io/i/user -->
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-buildings">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M4 21v-15c0 -1 1 -2 2 -2h5c1 0 2 1 2 2v15" />
                        <path d="M16 8h2c1 0 2 1 2 2v11" />
                        <path d="M3 21h18" />
                        <path d="M10 12v0" />
                        <path d="M10 16v0" />
                        <path d="M10 8v0" />
                        <path d="M7 12v0" />
                        <path d="M7 16v0" />
                        <path d="M7 8v0" />
                        <path d="M17 12v0" />
                        <path d="M17 16v0" />
                    </svg>
                </span>
                <input type="text" id="nama_cabang" value="{{ $cabang->nama_cabang }}" class="form-control"
                    name="nama_cabang" placeholder="Nama Lokasi">
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="input-icon mb-3">
                <span class="input-icon-addon">
                    <!-- Download SVG icon from http://tabler-icons.io/i/user -->
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-map-pin-pin">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M9 11a3 3 0 1 0 6 0a3 3 0 0 0 -6 0" />
                        <path d="M12.783 21.326a2 2 0 0 1 -2.196 -.426l-4.244 -4.243a8 8 0 1 1 13.657 -5.62" />
                        <path
                            d="M21.121 20.121a3 3 0 1 0 -4.242 0c.418 .419 1.125 1.045 2.121 1.879c1.051 -.89 1.759 -1.516 2.121 -1.879z" />
                        <path d="M19 18v.01" />
                    </svg>
                </span>
                <input type="text" id="lokasi_cabang" value="{{ $cabang->lokasi_cabang }}" class="form-control"
                    name="lokasi_cabang" placeholder="Titik Koordinat">
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="input-icon mb-3">
                <span class="input-icon-addon">
                    <!-- Download SVG icon from http://tabler-icons.io/i/user -->
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-radar-2">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M12 12m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" />
                        <path d="M15.51 15.56a5 5 0 1 0 -3.51 1.44" />
                        <path d="M18.832 17.86a9 9 0 1 0 -6.832 3.14" />
                        <path d="M12 12v9" />
                    </svg>
                </span>
                <input type="text" id="radius_cabang" value="{{ $cabang->radius_cabang }}" class="form-control"
                    name="radius_cabang" placeholder="Radius Cabang">
            </div>
        </div>
    </div>
    <div class="row mt-2">
        <div class="col-12">
            <div class="form-group">
                <button class="btn  btn-primary w-100">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-send">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M10 14l11 -11" />
                        <path d="M21 3l-6.5 18a.55 .55 0 0 1 -1 0l-3.5 -7l-7 -3.5a.55 .55 0 0 1 0 -1l18 -6.5" />
                    </svg>
                    Simpan
                </button>
            </div>
        </div>
    </div>
</form>
<script>
    $(function() {
        $("#frmCabangEdit").submit(function(e) {
            var kode_cabang = $("#frmCabangEdit").find("#kode_cabang").val();
            var nama_cabang = $("#frmCabangEdit").find("#nama_cabang").val();
            var lokasi_cabang = $("#frmCabangEdit").find("#lokasi_cabang").val();
            var radius_cabang = $("#frmCabangEdit").find("#radius_cabang").val();

            if (kode_cabang == "") {
                Swal.fire({
                    title: 'Warning!',
                    text: 'Kode Cabang Harus Diisi',
                    icon: 'warning',
                    confirmButtonText: 'Ok'
                }).then(() => {
                    $("#kode_cabang").focus();
                });

                e.preventDefault(); // Mencegah form dikirim jika ada kesalahan
                return false;
            } else if (nama_cabang == "") {
                Swal.fire({
                    title: 'Warning!',
                    text: 'Nama Cabang Harus Diisi',
                    icon: 'warning',
                    confirmButtonText: 'Ok'
                }).then(() => {
                    $("#nama_cabang").focus();
                });

                e.preventDefault(); // Mencegah form dikirim jika ada kesalahan
                return false;
            } else if (lokasi_cabang == "") {
                Swal.fire({
                    title: 'Warning!',
                    text: 'Lokasi Cabang Harus Diisi',
                    icon: 'warning',
                    confirmButtonText: 'Ok'
                }).then(() => {
                    $("#lokasi_cabang").focus();
                });

                e.preventDefault(); // Mencegah form dikirim jika ada kesalahan
                return false;
            } else if (radius_cabang == "") {
                Swal.fire({
                    title: 'Warning!',
                    text: 'Radius Cabang Harus Diisi',
                    icon: 'warning',
                    confirmButtonText: 'Ok'
                }).then(() => {
                    $("#radius_cabang").focus();
                });

                e.preventDefault(); // Mencegah form dikirim jika ada kesalahan
                return false;
            }
        });
    });
</script>
