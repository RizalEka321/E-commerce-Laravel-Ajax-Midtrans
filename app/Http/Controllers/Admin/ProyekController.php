<?php

namespace App\Http\Controllers\Admin;

use App\Models\Proyek;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;

class ProyekController extends Controller
{
    public function index()
    {
        return view('Admin.proyek');
    }
    public function get_proyek()
    {
        $data = Proyek::select('id_proyek', 'instansi', 'jumlah', 'harga_satuan', 'nominal_dp', 'total', 'status_pembayaran', 'status_pengerjaan')->get();
        return Datatables::of($data)
            ->addColumn('action', function ($row) {
                $actionBtn = '<div class="btn-group">';
                $actionBtn .= '<a href="javascript:void(0)" type="button" id="btn-edit" class="btn-edit" onClick="edit_data(' . "'" . $row->id_proyek . "'" . ')"><i class="fa-solid fa-pen-to-square"></i></a>';
                $actionBtn .= '<a href="javascript:void(0)" type="button" id="btn-del" class="btn-hapus" onClick="delete_data(' . "'" . $row->id_proyek . "'" . ')"><i class="fa-solid fa-trash-can"></i></a>';
                $actionBtn .= '<a href="javascript:void(0)" type="button" id="btn-detail" class="btn-ubah" onClick="detail_data(' . "'" . $row->id_proyek . "'" . ')" data-bs-toggle="modal" data-bs-target="#proyekModal"><i class="fa-solid fa-eye"></i> Detail</a>';
                $actionBtn .= '</div>';
                return $actionBtn;
            })
            ->addColumn('pembayaran', function ($row) {
                $dropdown = '<select class="form-control pembayaran-dropdown" data-id="' . $row->id_proyek . '"';
                switch ($row->status_pembayaran) {
                    case 'Belum':
                        $pembayaranOptions = ['Belum', 'DP', 'Lunas'];
                        $dropdown .= ' style="background-color: #C51605; color: white;"';
                        break;
                    case 'DP':
                        $pembayaranOptions = ['DP', 'Lunas'];
                        $dropdown .= ' style="background-color: #0D1282; color: white;"';
                        break;
                    case 'Lunas':
                        $pembayaranOptions = ['Lunas'];
                        $dropdown .= ' style="background-color: #009100; color: white;"';
                        break;
                    default:
                        break;
                }

                $dropdown .= '>';

                foreach ($pembayaranOptions as $option) {
                    $selected = ($row->status_pembayaran == $option) ? 'selected' : '';
                    $dropdown .= '<option value="' . $option . '" ' . $selected . '>' . $option . '</option>';
                }
                $dropdown .= '</select>';
                return $dropdown;
            })

            ->addColumn('pengerjaan', function ($row) {
                $dropdown = '<select class="form-control pengerjaan-dropdown" data-id="' . $row->id_proyek . '"';

                switch ($row->status_pengerjaan) {
                    case 'Diproses':
                        $pengerjaanOptions = ['Diproses', 'Selesai', 'Dibatalkan'];
                        $dropdown .= ' style="background-color: #0D1282; color: white;"';
                        break;
                    case 'Selesai':
                        $pengerjaanOptions = ['Selesai', 'Dibatalkan'];
                        $dropdown .= ' style="background-color: #009100; color: white;"';
                        break;
                    case 'Dibatalkan':
                        $pengerjaanOptions = ['Dibatalkan'];
                        $dropdown .= ' style="background-color: #C51605; color: white;"';
                        break;
                    default:
                        break;
                }

                $dropdown .= '>';

                foreach ($pengerjaanOptions as $option) {
                    $selected = ($row->status_pengerjaan == $option) ? 'selected' : '';
                    $dropdown .= '<option value="' . $option . '" ' . $selected . '>' . $option . '</option>';
                }
                $dropdown .= '</select>';
                return $dropdown;
            })

            ->rawColumns(['action', 'pengerjaan', 'pembayaran'])
            ->make(true);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_pemesan' => 'required|string|min:8|max:25',
            'instansi' => 'required|string|min:5|max:25',
            'no_hp' => 'required|min:12|max:15',
            'alamat' => 'required|string|min:25|max:75',
            'item' => 'required|string|min:12|max:25',
            'foto_logo' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'foto_desain' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'deskripsi_proyek' => 'required|string|min:75',
            'jumlah' => 'required|integer',
            'harga_satuan' => 'required|integer',
            'nominal_dp' => 'integer|nullable',
            'deadline' => 'required',
        ], [
            'nama_pemesan.required' => 'Nama wajib diisi.',
            'nama_pemesan.string' => 'Nama harus berupa teks.',
            'nama_pemesan.min' => 'Nama pemesan harus memiliki panjang minimal 8 karakter.',
            'nama_pemesan.max' => 'Nama pemesan harus memiliki panjang minimal 25 karakter.',
            'instansi.required' => 'Instansi wajib diisi.',
            'instansi.string' => 'Instansi harus berupa teks.',
            'instansi.min' => 'Nama Instansi harus memiliki panjang minimal 5 karakter.',
            'instansi.max' => 'Nama Instansi harus memiliki panjang minimal 25 karakter.',
            'no_hp.required' => 'No HP wajib diisi.',
            'no_hp.regex' => 'No Handphone hanya boleh berisikan angka.',
            'no_hp.min' => 'No Handphone harus memiliki panjang minimal 12 karakter.',
            'no_hp.max' => 'No Handphone harus memiliki panjang maksimal 15 karakter.',
            'alamat.required' => 'Alamat wajib diisi.',
            'alamat.string' => 'Alamat harus berupa teks.',
            'alamat.min' => 'Alamat harus memiliki panjang minimal 25 karakter.',
            'alamat.max' => 'Alamat harus memiliki panjang maksimal 75 karakter.',
            'item.required' => 'Item wajib diisi.',
            'item.string' => 'Item harus berupa teks.',
            'item.min' => 'Nama item harus memiliki panjang minimal 12 karakter.',
            'item.max' => 'Nama item harus memiliki panjang minimal 25 karakter.',
            'foto_logo.required' => 'Foto Logo wajib diisi.',
            'foto_logo.image' => 'Foto Logo harus berupa file gambar.',
            'foto_logo.mimes' => 'Foto Logo harus dalam format jpeg, png, atau jpg.',
            'foto_logo.max' => 'Foto Logo tidak boleh lebih dari 2048 KB.',
            'foto_desain.required' => 'Foto Desain wajib diisi.',
            'foto_desain.image' => 'Foto Desain harus berupa file gambar.',
            'foto_desain.mimes' => 'Foto Desain harus dalam format jpeg, png, atau jpg.',
            'foto_desain.max' => 'Foto Desain tidak boleh lebih dari 2048 KB.',
            'deskripsi_proyek.required' => 'Deskripsi wajib diisi.',
            'deskripsi_proyek.string' => 'Deskripsi harus berupa teks.',
            'deskripsi_proyel.max' => 'Deskripsi harus memiliki panjang minimal 75 karakter.',
            'jumlah.required' => 'Jumlah wajib diisi.',
            'jumlah.integer' => 'Jumlah harus berupa angka.',
            'jumlah.min' => 'Jumlah tidak boleh kurang dari 0.',
            'harga_satuan.required' => 'Harga Satuan wajib diisi.',
            'harga_satuan.integer' => 'Harga Satuan harus berupa angka.',
            'harga_satuan.min' => 'Harga tidak boleh kurang dari 0.',
            'nominal_dp.integer' => 'Nominal DP harus berupa angka.',
            'nominal_dp.min' => 'Nominal DP tidak boleh kurang dari 0.',
            'deadline.required' => 'Deadline Proyek wajib diisi.',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'errors' => $validator->errors()]);
        } else {
            $id_proyek = Proyek::generateID();

            $path = 'data/Proyek';
            if ($request->hasFile('foto_logo')) {
                $foto_logo = $request->file('foto_logo');
                $file_logo = $id_proyek . '_logo' . '.' . $foto_logo->extension();
                $foto_logo->move($path, $file_logo);
            }

            if ($request->hasFile('foto_desain')) {
                $foto_desain = $request->file('foto_desain');
                $file_desain = $id_proyek . '_desain' . '.' . $foto_desain->extension();
                $foto_desain->move($path, $file_desain);
            }

            $total = $request->harga_satuan * $request->jumlah;
            $dp = ($total * 50) / 100;
            if ($request->nominal_dp < $dp) {
                return response()->json(['errors' => ['nominal_dp' => ['Nominal DP proyek tidak bisa lebih dari harga total keseluruhan proyek.']]]);
            }

            $proyekData = [
                'id_proyek' => $id_proyek,
                'nama_pemesan' => Str::title($request->nama_pemesan),
                'instansi' => Str::title($request->instansi),
                'no_hp' => $request->no_hp,
                'alamat' => $request->alamat,
                'item' => $request->item,
                'foto_logo' => "$path/$file_logo",
                'foto_desain' => "$path/$file_desain",
                'deskripsi_proyek' => $request->deskripsi_proyek,
                'jumlah' => $request->jumlah,
                'harga_satuan' => $request->harga_satuan,
                'deadline' => $request->deadline,
                'nominal_dp' => $request->nominal_dp,
                'total' => $request->jumlah * $request->harga_satuan,
                'status_pembayaran' => 'dp',
                'status_pengerjaan' => 'diproses',
            ];

            Proyek::create($proyekData);
            aktivitas('Menambahkan Proyek Baru');

            return response()->json(['status' => true]);
        }
    }

    public function edit(Request $request)
    {
        $id = $request->input('q');
        $proyek = Proyek::find($id);

        return response()->json(['status' => true, 'proyek' => $proyek]);
    }

    public function detail(Request $request)
    {
        $id = $request->input('q');
        $proyek = Proyek::find($id);

        return response()->json(['status' => true, 'proyek' => $proyek]);
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_pemesan' => 'required|string|min:8|max:25',
            'instansi' => 'required|string|min:5|max:25',
            'no_hp' => 'required|min:12|max:15',
            'alamat' => 'required|string|min:25|max:75',
            'item' => 'required|string|min:12|max:25',
            'foto_logo' => 'nullable|mimes:jpeg,png,jpg|max:2048',
            'foto_desain' => 'nullable|mimes:jpeg,png,jpg|max:2048',
            'deskripsi_proyek' => 'required|string|min:75',
            'jumlah' => 'required|integer',
            'harga_satuan' => 'required|integer',
            'nominal_dp' => 'integer|nullable',
            'deadline' => 'required',
        ], [
            'nama_pemesan.required' => 'Nama wajib diisi.',
            'nama_pemesan.string' => 'Nama harus berupa teks.',
            'nama_pemesan.min' => 'Nama pemesan harus memiliki panjang minimal 8 karakter.',
            'nama_pemesan.max' => 'Nama pemesan harus memiliki panjang minimal 25 karakter.',
            'instansi.required' => 'Instansi wajib diisi.',
            'instansi.string' => 'Instansi harus berupa teks.',
            'instansi.min' => 'Nama Instansi harus memiliki panjang minimal 5 karakter.',
            'instansi.max' => 'Nama Instansi harus memiliki panjang minimal 25 karakter.',
            'no_hp.required' => 'No HP wajib diisi.',
            'no_hp.regex' => 'No Handphone hanya boleh berisikan angka.',
            'no_hp.min' => 'No Handphone harus memiliki panjang minimal 12 karakter.',
            'no_hp.max' => 'No Handphone harus memiliki panjang maksimal 15 karakter.',
            'alamat.required' => 'Alamat wajib diisi.',
            'alamat.string' => 'Alamat harus berupa teks.',
            'alamat.min' => 'Alamat harus memiliki panjang minimal 25 karakter.',
            'alamat.max' => 'Alamat harus memiliki panjang maksimal 75 karakter.',
            'item.required' => 'Item wajib diisi.',
            'item.string' => 'Item harus berupa teks.',
            'item.min' => 'Nama item harus memiliki panjang minimal 12 karakter.',
            'item.max' => 'Nama item harus memiliki panjang minimal 25 karakter.',
            'foto_logo.required' => 'Foto Logo wajib diisi.',
            'foto_logo.image' => 'Foto Logo harus berupa file gambar.',
            'foto_logo.mimes' => 'Foto Logo harus dalam format jpeg, png, atau jpg.',
            'foto_logo.max' => 'Foto Logo tidak boleh lebih dari 2048 KB.',
            'foto_desain.required' => 'Foto Desain wajib diisi.',
            'foto_desain.image' => 'Foto Desain harus berupa file gambar.',
            'foto_desain.mimes' => 'Foto Desain harus dalam format jpeg, png, atau jpg.',
            'foto_desain.max' => 'Foto Desain tidak boleh lebih dari 2048 KB.',
            'deskripsi_proyek.required' => 'Deskripsi wajib diisi.',
            'deskripsi_proyek.string' => 'Deskripsi harus berupa teks.',
            'deskripsi_proyel.max' => 'Deskripsi harus memiliki panjang minimal 75 karakter.',
            'jumlah.required' => 'Jumlah wajib diisi.',
            'jumlah.integer' => 'Jumlah harus berupa angka.',
            'jumlah.min' => 'Jumlah tidak boleh kurang dari 0.',
            'harga_satuan.required' => 'Harga Satuan wajib diisi.',
            'harga_satuan.integer' => 'Harga Satuan harus berupa angka.',
            'harga_satuan.min' => 'Harga tidak boleh kurang dari 0.',
            'nominal_dp.integer' => 'Nominal DP harus berupa angka.',
            'nominal_dp.min' => 'Nominal DP tidak boleh kurang dari 0.',
            'deadline.required' => 'Deadline Proyek wajib diisi.',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'errors' => $validator->errors()]);
        } else {
            $id = $request->query('q');
            $proyek = Proyek::find($id);

            $proyek->nama_pemesan = Str::title($request->nama_pemesan);
            $proyek->instansi = Str::title($request->instansi);
            $proyek->no_hp = $request->no_hp;
            $proyek->alamat = $request->alamat;
            $proyek->item = $request->item;
            $proyek->deskripsi_proyek = $request->deskripsi_proyek;
            $proyek->jumlah = $request->jumlah;
            $proyek->harga_satuan = $request->harga_satuan;
            $proyek->deadline = $request->deadline;
            $proyek->total = $request->jumlah * $request->harga_satuan;

            $path = 'data/Proyek';

            if ($request->hasFile('foto_logo')) {
                if ($proyek->foto_logo) {
                    if (file_exists($proyek->foto_logo)) {
                        unlink($proyek->foto_logo);
                    }
                }

                $foto_logo = $request->file('foto_logo');
                $file_logo = $id . '_logo' . '.' . $foto_logo->extension();
                $foto_logo->move($path, $file_logo);
                $proyek->foto_logo = "$path/$file_logo";
            }

            if ($request->hasFile('foto_desain')) {
                if ($proyek->foto_desain) {
                    if (file_exists($proyek->foto_desain)) {
                        unlink($proyek->foto_desain);
                    }
                }

                $foto_desain = $request->file('foto_desain');
                $file_desain = $id . '_desain' . '.' . $foto_desain->extension();
                $foto_desain->move($path, $file_desain);
                $proyek->foto_desain = "$path/$file_desain";
            }

            $proyek->save();
            aktivitas('Mengupdate Data Proyek Dengan ID ' . $proyek->id_proyek);

            return response()->json(['status' => true]);
        }
    }

    public function update_pengerjaan(Request $request)
    {

        $id = $request->id;
        $status_pengerjaan = $request->status_pengerjaan;

        $data = Proyek::findOrFail($id);
        $data->status_pengerjaan = $status_pengerjaan;
        $data->save();

        aktivitas('Mengudate Status Pengerjaan Proyek Dengan ID ' . $data->id_proyek);

        return response()->json(['status' => true]);
    }

    public function update_pembayaran(Request $request)
    {
        $id = $request->id;
        $status_pembayaran = $request->status_pembayaran;

        $data = Proyek::findOrFail($id);
        $data->status_pembayaran = $status_pembayaran;
        $data->save();

        aktivitas('Mengudate Status Pembayaran Proyek Dengan ID ' . $data->id_proyek);

        return response()->json(['status' => true]);
    }

    public function destroy(Request $request)
    {
        $id = $request->input('q');
        $proyek = Proyek::find($id);

        $fotoPath_logo = $proyek->foto_logo;
        if (file_exists($fotoPath_logo)) {
            unlink($fotoPath_logo);
        }

        $fotoPath_desain = $proyek->foto_desain;
        if (file_exists($fotoPath_desain)) {
            unlink($fotoPath_desain);
        }

        aktivitas('Menghapus Data Proyek Dengan ID ' . $proyek->id_proyek);
        $proyek->delete();

        echo json_encode(['status' => true]);
    }
}
