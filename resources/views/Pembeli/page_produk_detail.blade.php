@extends('Pembeli.layout.app')
@section('title', 'Detail Produk')
@section('content')
    {{-- Detail Produk --}}
    <section class="detail-produk">
        <div class="container">
            <div class="row mt-3 py-2">
                <div class="col-md-3 col-lg-5">
                    <img src="{{ asset($produk_detail->foto) }}" class="card-img-top" alt="...">
                </div>
                <div class="col-md-5 col-lg-7 pt-2">
                    <h2 class="title">{{ $produk_detail->judul }}</h2>
                    <div class="price">
                        <h4>Rp. {{ number_format($produk_detail->harga, 0, '.', '.') }}</h4>
                    </div>
                    <div>
                        <form id="form_tambah" action="#" method="POST" role="form">
                            <input type="hidden" name="produk_id" value="{{ $produk_detail->id_produk }}">
                            <div class="mb-3">
                                <label for="ukuran">Ukuran</label>
                                <div class="radio-toolbar">
                                    @foreach ($produk_detail->ukuran as $u)
                                        @if ($u->stok != null)
                                            <input type="radio" id="radio{{ $u->id_ukuran }}" name="ukuran"
                                                value="{{ $u->jenis_ukuran }}">
                                            <label for="radio{{ $u->id_ukuran }}">{{ $u->jenis_ukuran }}</label>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="ukuran">Kuantitas</label>
                                <div class="qty-container">
                                    <button class="qty-btn-minus btn-light" type="button"><i
                                            class="fa fa-minus"></i></button>
                                    <input type="text" name="jumlah" value="0" class="input-qty" />
                                    <button class="qty-btn-plus btn-light" type="button"><i
                                            class="fa fa-plus"></i></button>
                                </div>
                            </div>
                            <div class="button-container">
                                <button type="submit" id="btn_masukkan_keranjang" class="btn-keranjang"><i
                                        class="fa-solid fa-plus"></i> Keranjang</button>
                                <button class="btn-beli" id="btn_beli_sekarang">Beli Sekarang</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="description row my-2 py-2">
                <h4 class="title">Deskripsi Produk</h4>
                <div class="mx-2">
                    {!! $produk_detail->deskripsi !!}
                </div>
            </div>
        </div>
    </section>
    {{-- End Detail Produk --}}
@endsection
@section('script')
    <script>
        // Global Setup
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        function reset_form() {
            $('#form_tambah')[0].reset();
        }

        function reset_errors() {
            $('.error-message').empty();
        }

        $(document).ready(function() {
            $(document).on('click', '.qty-btn-plus', function() {
                var $n = $(this).parent(".qty-container").find(".input-qty");
                $n.val(Number($n.val()) + 1);
            });

            $(document).on('click', '.qty-btn-minus', function() {
                var $n = $(this).parent(".qty-container").find(".input-qty");
                var amount = Number($n.val());
                if (amount > 0) {
                    $n.val(amount - 1);
                }
            });
        });

        $('#btn_masukkan_keranjang').click(function(event) {
            event.preventDefault();

            // Check if user is logged in
            var isLoggedIn = "{{ Auth::check() }}";
            if (!isLoggedIn) {
                window.location.href = "{{ route('login') }}";
                return;
            } else {
                // Ubah action form ke URL endpoint checkout
                var checkoutUrl = "{{ url('/keranjang/create') }}";
                $('#form_tambah').attr('action', checkoutUrl);

                var url = $('#form_tambah').attr('action');
                var formData = new FormData($('#form_tambah')[0]);

                // Lakukan AJAX request
                $.ajax({
                    url: url,
                    type: "POST",
                    dataType: "JSON",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(data) {
                        $('.error-message').empty();
                        if (data.errors) {
                            $.each(data.errors, function(key, value) {
                                // Tampilkan pesan error di bawah setiap input
                                $('#' + key).next('.error-message').text(
                                    '*' + value);
                            });
                            Swal.fire(
                                'Error',
                                'Ada Yang Salah',
                                'error'
                            );
                        } else {
                            reset_form();
                            Swal.fire(
                                'Sukses',
                                'Produk Berhasil Dimasukkan Keranjang',
                                'success'
                            );
                        }
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        console.log(jqXHR);
                    },
                    complete: function() {
                        // Lakukan sesuatu setelah request selesai (jika diperlukan)
                    }
                });
            }
        });
        // Event handler untuk tombol "Beli Sekarang"
        $('#btn_beli_sekarang').click(function(event) {
            event.preventDefault(); // Mencegah tindakan default tombol

            // Check if user is logged in
            var isLoggedIn = "{{ Auth::check() }}";
            if (!isLoggedIn) {
                window.location.href = "{{ route('login') }}";
                return;
            } else {
                var checkoutUrl = "{{ url('/checkout-langsung') }}"; // Ganti URL sesuai kebutuhan
                $('#form_tambah').attr('action', checkoutUrl);

                var url = $('#form_tambah').attr('action');
                var formData = new FormData($('#form_tambah')[0]);

                // Lakukan AJAX request
                $.ajax({
                    url: url,
                    type: "POST",
                    dataType: "JSON",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(data) {
                        $('.error-message').empty();
                        console.log(data.errors);
                        if (data.errors) {
                            $.each(data.errors, function(key, value) {
                                // Tampilkan pesan error di bawah setiap input
                                $('#' + key).next('.error-message').text('*' +
                                    value);
                            });
                        } else {
                            Swal.fire({
                                title: 'Checkout Sekarang?',
                                text: "Anda akan langsung checkout, lanjutkan?",
                                icon: 'warning',
                                showCancelButton: true,
                                confirmButtonColor: '#3085d6',
                                cancelButtonColor: '#d33',
                                confirmButtonText: 'Ya, checkout!',
                                cancelButtonText: 'Batal' // Tambahkan teks untuk tombol batal
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    // Ubah action form ke URL endpoint checkout
                                    window.location.href = "{{ url('/checkout') }}";
                                } else if (result.dismiss === Swal.DismissReason.cancel) {
                                    // Pengguna memilih untuk membatalkan aksi, tidak perlu melakukan apapun
                                    Swal.fire('Batal', 'Anda membatalkan checkout.', 'info');
                                    reset_form();
                                }
                            });
                        }
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        console.log(jqXHR);
                    },
                    complete: function() {
                        // Lakukan sesuatu setelah request selesai (jika diperlukan)
                    }
                });
            }
        });
    </script>
@endsection
