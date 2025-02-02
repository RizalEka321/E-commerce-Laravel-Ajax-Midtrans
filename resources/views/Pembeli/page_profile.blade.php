@extends('Pembeli.layout.app')
@section('title', 'Profile')
@section('content')
    <section class="profile">
        <div class="container">
            <div class="row">
                <div class="col-md-5 col-lg-5 order-md-1">
                    <div class="card">
                        <div class="card-body">
                            <div class="card foto-profile">
                                <div class="card-body">
                                    @if (Auth::user()->foto == null)
                                        <img src="{{ asset('assets/pembeli/img/default.png') }}" class="preview" />
                                    @else
                                        <img src="{{ asset(Auth::user()->foto) }}?ts={{ time() }}" class="preview" />
                                    @endif
                                    <form id="form_foto" action="{{ url('/profile/update-foto') }}" method="post"
                                        enctype="multipart/form-data">
                                        <div class="d-flex justify-content-center mb-2">
                                            <input type="file" accept="image/*" class="input-kecil" name="foto"
                                                id="foto" style="display: none">
                                            <button type="button" class="btn-upload" id="ubahAvatarBtn">Ubah
                                                Avatar</button>
                                        </div>
                                    </form>
                                    <p>Besar file: maksimum 2.000 kilobytes (2 Megabytes). Ekstensi file yang
                                        diperbolehkan: .JPG .JPEG .PNG.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-7 col-lg-7 order-md-2">
                    <div class="card isi-profile">
                        <div class="card-body">
                            <form id="form_profile" action="{{ url('/profile/update') }}" method="POST">
                                <div class="mb-2">
                                    <div class="form-group">
                                        <label for="nama" style="font-weight: 700">Nama
                                            Lengkap</label>
                                        <input type="text" class="input-kecil" name="nama_lengkap" id="nama_lengkap"
                                            value="{{ Auth::user()->nama_lengkap }}">
                                        <span class="form-text text-danger error-message"></span>
                                    </div>
                                </div>
                                <div class="mb-2">
                                    <label for="username" style="font-weight: 700">Username</label>
                                    <input type="text" class="input-kecil" name="username" id="username"
                                        value="{{ Auth::user()->username }}" disabled>
                                    <span class="form-text text-danger error-message"></span>
                                </div>
                                <div class="mb-2">
                                    <label for="email" style="font-weight: 700">Email</label>
                                    <input type="email" class="input-kecil" name="email" id="email"
                                        value="{{ Auth::user()->email }}" disabled>
                                    <span class="form-text text-danger error-message"></span>
                                </div>
                                <div class="mb-2">
                                    <label for="password" style="font-weight: 700">Password</label>
                                    <button type="button" class="btn-password" data-bs-toggle="modal"
                                        data-bs-target="#passwordModal">
                                        Ubah Password <i class="fa-solid fa-chevron-up"></i>
                                    </button>
                                </div>
                                <div class="mb-2">
                                    <label for="no_hp" style="font-weight: 700">Telepon</label>
                                    <input type="no_hp" class="input-kecil" name="no_hp" id="no_hp"
                                        value="{{ Auth::user()->no_hp }}">
                                    <span class="form-text text-danger error-message"></span>
                                </div>
                                <div>
                                    <label for="alamat" style="font-weight: 700">Alamat</label>
                                    <textarea id="alamat" name="alamat" class="input-besar" placeholder="Masukkan Alamat" id="alamat">{{ Auth::user()->alamat }}</textarea>
                                    <span class="form-text text-danger error-message"></span>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="text-end me-3">
                <button type="submit" class="btn-profile" id="btn-update">
                    <i class="nav-icon fas fa-save"></i>
                    Simpan
                </button>
            </div>
        </div>
        <div class="modal fade" id="passwordModal" tabindex="-1" aria-labelledby="ModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="ModalLabel">
                            Ubah Password</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="form_password" action="{{ url('/profile/update-password') }}" method="POST">
                            <div class="mb-3">
                                <div class="form-group inputan">
                                    <label for="password">Password Baru</label>
                                    <input type="password" name="password" id="password" class="input-kecil"
                                        placeholder="Masukkan Password">
                                    <span class="form-text text-danger error-message"></span>
                                </div>
                            </div>
                            <div class="mb-3">
                                <div class="form-group inputan">
                                    <label for="password_confirmation">Konfirmasi
                                        Password :</label>
                                    <input type="password" id="password_confirmation" name="password_confirmation"
                                        class="input-kecil" placeholder="Masukkan Password">
                                    <span class="form-text text-danger error-message"></span>
                                </div>
                            </div>
                            <button type="submit" id="btn-simpan" class="btn-profile"><i
                                    class="nav-icon fas fa-save"></i>&nbsp;&nbsp;
                                Simpan</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
@section('script')
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $('#btn-update').click(function(e) {
            e.preventDefault();
            var url = $('#form_profile').attr('action');
            var formData = new FormData($('#form_profile')[0]);

            Swal.fire({
                title: "Sedang memproses",
                html: "Mohon tunggu sebentar...",
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                willOpen: () => {
                    Swal.showLoading();
                }
            });

            $.ajax({
                url: url,
                type: "POST",
                dataType: "JSON",
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    Swal.close();
                    $('.error-message').empty();
                    if (response.errors) {
                        $.each(response.errors, function(key, value) {
                            $('#' + key).next('.error-message').text('*' + value);
                        });
                    } else {
                        Swal.fire({
                            title: 'Sukses',
                            text: 'Profile berhasil diperbarui',
                            icon: 'success',
                            showConfirmButton: false,
                            timer: 3000,
                            toast: false
                        });
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    Swal.close();
                    Swal.fire({
                        title: 'Error',
                        text: 'Terjadi kesalahan saat memperbarui profil.',
                        icon: 'error',
                        position: 'center',
                        showConfirmButton: false,
                        timer: 3000,
                        toast: false
                    });
                }
            });
        });

        $(function() {
            $('#form_password').submit(function(event) {
                event.preventDefault();
                event.stopImmediatePropagation();
                var url = $(this).attr('action');
                var formData = new FormData($(this)[0]);

                Swal.fire({
                    title: "Sedang memproses",
                    html: "Mohon tunggu sebentar...",
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showConfirmButton: false,
                    willOpen: () => {
                        Swal.showLoading();
                    }
                });

                $.ajax({
                    url: url,
                    type: "POST",
                    dataType: "JSON",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        Swal.close();
                        $('.error-message').empty();
                        if (response.errors) {
                            $.each(response.errors, function(key, value) {
                                $('#' + key).next('.error-message').text('*' + value);
                            });
                        } else {
                            Swal.fire({
                                title: 'Sukses',
                                text: 'Password berhasil diubah',
                                icon: 'success',
                                showConfirmButton: false,
                                timer: 3000,
                                toast: false
                            });
                            $('#form_password')[0].reset();
                            $('#passwordModal').modal('hide');
                            $('.modal-backdrop').remove();
                        }
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        Swal.close();
                        Swal.fire({
                            title: 'Error',
                            text: 'Terjadi kesalahan saat memperbarui password.',
                            icon: 'error',
                            position: 'center',
                            showConfirmButton: false,
                            timer: 3000,
                            toast: false
                        });
                    }
                });
            });
        });

        $('#ubahAvatarBtn').click(function() {
            $('#foto').click();
        });

        $(function() {
            $('#foto').change(function(event) {
                event.preventDefault();
                event.stopImmediatePropagation();
                var url = $('#form_foto').attr('action');
                var formData = new FormData($('#form_foto')[0]);

                Swal.fire({
                    title: "Sedang memproses",
                    html: "Mohon tunggu sebentar...",
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showConfirmButton: false,
                    willOpen: () => {
                        Swal.showLoading();
                    }
                });

                $.ajax({
                    url: url,
                    type: "POST",
                    dataType: "JSON",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.errors) {
                            Swal.close();
                            let errorMessages = '';
                            $.each(response.errors, function(key, value) {
                                errorMessages += value + '<br>';
                            });
                            Swal.fire({
                                title: 'Upss..!',
                                html: errorMessages,
                                icon: 'error',
                                position: 'center',
                                showConfirmButton: false,
                                timer: 3000,
                                toast: false
                            });
                        } else {
                            location.reload();
                        }
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        Swal.close();
                        Swal.fire({
                            title: 'Error',
                            text: 'Terjadi kesalahan saat memperbarui foto profil.',
                            icon: 'error',
                            position: 'center',
                            showConfirmButton: false,
                            timer: 3000,
                            toast: false
                        });
                    }
                });
            });
        });
    </script>
@endsection
