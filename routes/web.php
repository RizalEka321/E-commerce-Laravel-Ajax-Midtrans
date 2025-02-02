<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\LogController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\KontakController;
use App\Http\Controllers\Admin\ProdukController;
use App\Http\Controllers\Admin\ProyekController;
use App\Http\Controllers\Pembeli\PageController;
use App\Http\Controllers\Admin\LaporanController;
use App\Http\Controllers\Admin\PesananController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Pembeli\ProfileController;
use App\Http\Controllers\Pembeli\CheckoutController;
use App\Http\Controllers\Pembeli\KeranjangController;
use App\Http\Controllers\Pembeli\PembayaranController;
use App\Http\Controllers\Admin\DetailPesananController;
use App\Http\Controllers\Pembeli\PesananSayaController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Auth
Route::get('/login', [AuthController::class, "login"])->name('login');
Route::post('/dologin', [AuthController::class, "dologin"])->name('dologin');
Route::get('/register', [AuthController::class, "register"])->name('register');
Route::post('/doregister', [AuthController::class, "doregister"])->name('doregister');
Route::get('/logout', [AuthController::class, "logout"])->name('logout');
// Verifikasi Email
Route::get('/email/verify', [AuthController::class, 'notice'])->name('verification.notice');
Route::get('/email/verify/{id}/{hash}', [AuthController::class, 'verify'])->name('verification.verify');
Route::post('/email/resend', [AuthController::class, 'resend'])->name('verification.resend');

// Pemilik
Route::middleware(['auth:web', 'admin'])->group(function () {
    // Dashboard
    Route::get('/admin', [DashboardController::class, "index"])->name('admin.dashboard');
    // Produk
    Route::get('/admin/produk', [ProdukController::class, "index"])->name('admin.produk');
    Route::get('/admin/produk/list', [ProdukController::class, "get_produk"])->name('admin.get-produk');
    Route::post('/admin/produk/create', [ProdukController::class, "store"])->name('admin.produk.create');
    Route::post('/admin/produk/edit', [ProdukController::class, "edit"])->name('admin.produk.edit');
    Route::post('/admin/produk/update', [ProdukController::class, "update"])->name('admin.produk.update');
    Route::post('/admin/produk/delete', [ProdukController::class, "destroy"])->name('admin.produk.delete');
    Route::post('/admin/produk/detail', [ProdukController::class, "detail"])->name('admin.produk.detail');
    // Proyek
    Route::get('/admin/proyek', [ProyekController::class, "index"])->name('admin.proyek');
    Route::get('/admin/proyek/list', [ProyekController::class, "get_proyek"])->name('admin.get-proyek');
    Route::post('/admin/proyek/create', [ProyekController::class, "store"])->name('admin.proyek.create');
    Route::post('/admin/proyek/edit', [ProyekController::class, "edit"])->name('admin.proyek.edit');
    Route::post('/admin/proyek/update', [ProyekController::class, "update"])->name('admin.proyek.update');
    Route::post('/admin/proyek/delete', [ProyekController::class, "destroy"])->name('admin.proyek.delete');
    Route::post('/admin/proyek/update-pengerjaan', [ProyekController::class, "update_pengerjaan"])->name('admin.proyek.update-pengerjaan');
    Route::post('/admin/proyek/update-pembayaran', [ProyekController::class, "update_pembayaran"])->name('admin.proyek.update-pembayaran');
    Route::post('/admin/proyek/detail', [ProyekController::class, "detail"])->name('admin.proyek.detail');
    // Pesanan
    Route::get('/admin/pesanan', [PesananController::class, "index"])->name('admin.pesanan');
    Route::get('/admin/pesanan/list', [PesananController::class, "get_pesanan"])->name('admin.get-pesanan');
    Route::post('/admin/pesanan/edit', [PesananController::class, "edit"])->name('admin.pesanan.edit');
    Route::post('/admin/pesanan/update', [PesananController::class, "update"])->name('admin.pesanan.update');
    Route::post('/admin/pesanan/update-status', [PesananController::class, "update_status"])->name('admin.pesanan.update-status');
    // Detail Pesanan
    Route::get('/admin/pesanan/detail/{id}', [DetailPesananController::class, "detail"])->name('admin.pesanan.detail');
    Route::get('/admin/pesanan/detail/list/{id}', [DetailPesananController::class, "get_detailpesanan"])->name('admin.get-detailpesanan');
    // Laporan Pendapatan
    Route::get('/admin/laporan', [LaporanController::class, "index"])->name('admin.laporan');
    Route::post('/admin/laporan/cetak', [LaporanController::class, "cetak"])->name('admin.laporan.cetak');
    // Profil dan Kontak Perusahaan
    Route::get('/admin/profil', [KontakController::class, "index"])->name('admin.profil');
    Route::post('/admin/profil/update', [KontakController::class, "update_profil"])->name('admin.profil.update');
    Route::post('/admin/kontak/update', [KontakController::class, "update_kontak"])->name('admin.kontak.update');
    // User Manajemen
    Route::get('/admin/user-manajemen', [UserController::class, "index"])->name('admin.user-manajemen');
    Route::get('/admin/user-manajemen/list', [UserController::class, "get_user"])->name('admin.get-user');
    Route::post('/admin/user-manajemen/create', [UserController::class, "store"])->name('admin.user-manajemen.create');
    Route::post('/admin/user-manajemen/edit', [UserController::class, "edit"])->name('admin.user-manajemen.edit');
    Route::post('/admin/user-manajemen/update', [UserController::class, "update"])->name('admin.user-manajemen.update');
    Route::post('/admin/user-manajemen/delete', [UserController::class, "destroy"])->name('admin.user-manajemen.delete');
    // Log Aktivitas
    Route::get('/admin/log', [LogController::class, "index"])->name('admin.log');
    Route::get('/admin/log/list', [LogController::class, "get_log"])->name('admin.get-log');
    Route::post('/admin/log/detail', [LogController::class, "detail"])->name('admin.log.detail');
});

// Pembeli
Route::middleware(['auth:web', 'pembeli', 'verified'])->group(function () {
    // Profile
    Route::get('/profile', [ProfileController::class, "page_profile"])->name('profile');
    Route::post('/profile/update', [ProfileController::class, "update"])->name('profile.update');
    Route::post('/profile/update-password', [ProfileController::class, "update_password"])->name('profile.update_password');
    Route::post('/profile/update-foto', [ProfileController::class, "update_foto"])->name('profile.update_foto');
    // Pesanan Saya
    Route::get('/pesanan-saya', [PesananSayaController::class, "page_pesanan_saya"])->name('pesanan_saya');
    Route::get('/detail-pesanan', [PesananSayaController::class, "detail_pesanan"])->name('detail_pesanan');
    Route::post('/pesanan-saya/cetak', [PesananSayaController::class, "cetak"])->name('pesanan_saya.cetak');
    // Keranjang
    Route::get('/keranjang', [KeranjangController::class, "page_keranjang"])->name('keranjang');
    Route::get('/keranjang/list', [KeranjangController::class, "get_keranjang"])->name('keranjang.list');
    Route::post('/keranjang/update', [KeranjangController::class, "update_keranjang"])->name('keranjang.update');
    Route::post('/keranjang/delete', [KeranjangController::class, "delete_keranjang"])->name('keranjang.delete');
    Route::post('/keranjang/delete-all', [KeranjangController::class, "delete_all_keranjang"])->name('keranjang.delete_all');
    Route::post('/keranjang/create', [KeranjangController::class, "add_keranjang"])->name('keranjang.add');
    // checkout
    Route::post('/checkout-keranjang', [CheckoutController::class, "checkout_keranjang"])->name('checkout.keranjang');
    Route::post('/checkout-langsung', [CheckoutController::class, "checkout_langsung"])->name('checkout.langsung');
    Route::get('/checkout', [CheckoutController::class, "checkout"])->name('checkout');
    Route::post('/checkout/update-alamat', [CheckoutController::class, "update_alamat"])->name('update_alamat');
    Route::post('/checkout/update-nohp', [CheckoutController::class, "update_nohp"])->name('update_nohp');
    Route::post('/checkout-store', [CheckoutController::class, "checkout_store"])->name('checkout.store');
    Route::post('/checkout-batalkan', [CheckoutController::class, "checkout_batalkan"])->name('checkout.batalkan');
    // pemesanan
    Route::get('/pembayaran-cash/{id}', [PembayaranController::class, "pembayaran_cash"])->name('pembayaran.cash');
    Route::get('/pembayaran-transfer/{id}', [PembayaranController::class, "pembayaran_transfer"])->name('pembayaran.transfer');
});

// Guest
Route::get('/', [PageController::class, "page_home"])->name('home');
Route::get('/produk/{slug}', [PageController::class, "page_detail_produk"])->name('detail_produk');
Route::post('/saran', [PageController::class, "saran"])->name('saran');
