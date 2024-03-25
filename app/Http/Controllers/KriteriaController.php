<?php

namespace App\Http\Controllers;

use App\Models\Kriteria;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class KriteriaController extends Controller
{
    public function index()
    {
        $title = "Kriteria";
        if (request()->ajax()) {
            $kriteria = Kriteria::orderBy('kode', 'ASC')->get();
            return DataTables::of($kriteria)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    // Karena tidak ada URL khusus untuk 'destroy' dalam route resource, kita gunakan 'kriteria.destroy' tapi dengan method DELETE
                    $editUrl = route('kriteria.edit', $row->id);
                    $deleteUrl = route('kriteria.destroy', $row->id);
                    $id = $row->id; // Ambil ID dari baris/kriteria

                    $button = "<div class=\"dropdown\">
                                <button type=\"button\" class=\"btn p-0 dropdown-toggle hide-arrow\" data-bs-toggle=\"dropdown\">
                                    <i class=\"mdi mdi-dots-vertical\"></i>
                                </button>
                                <div class=\"dropdown-menu\">
                                    <a class=\"dropdown-item waves-effect edit_kriteria\" href='javascript:void(0)' data-url=\"{$editUrl} \" data-id=\"{$row->id}\"><i class=\"mdi mdi-pencil-outline me-1\"></i> Edit</a>
                                    <a class=\"dropdown-item waves-effect hapus_kriteria\" href='javascript:void(0);' data-url=\"('{$deleteUrl}')\" data-id=\"{$row->id}\"><i class=\"mdi mdi-trash-can-outline me-1\"></i> Delete</a>
                                </div>
                            </div>";

                    return $button;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('pages.kriteria.index', compact('title'));

        // $kriterias = Kriteria::all();
        // return view('pages.kriteria.index', compact('kriterias'));
    }

    public function store(Request $request)
    {
        // Aturan validasi
        $rules = [
            'kode' => [
                'required',
                'string',
                Rule::unique('kriterias')->ignore($request->id)
            ],
            'kriteria_name' => 'required|string',
            'bobot' => 'required|numeric',
            'tipe' => 'required',
        ];

        // Pesan kesalahan kustom (opsional)
        $messages = [
            'required' => 'Kolom :attribute harus diisi.',
            'string' => 'Kolom :attribute harus berupa teks.',
            'numeric' => 'Kolom :attribute harus berupa angka.',
            'unique' => 'Kolom :attribute sudah dipakai.',
        ];

        // Melakukan validasi
        $validatedData = $request->validate($rules, $messages);

        $kriteria = Kriteria::updateOrCreate(
            [
                'id' => $request->id
            ],
            [
                'kode' => $request->kode,
                'kriteria_name' => $request->kriteria_name,
                'bobot' => $request->bobot,
                'tipe' => $request->tipe
            ]
        );

        if ($kriteria->wasRecentlyCreated) {
            return response()->json(['success' => true, 'message' => 'Kriteria berhasil di input.', 'data' => $kriteria]);
        } else {
            return response()->json(['success' => true, 'message' => 'Kriteria berhasil di ubah.', 'data' => $kriteria]);
        }
    }

    public function edit($id)
    {
        $kriteria = Kriteria::findOrFail($id);
        return response()->json($kriteria);
    }

    public function destroy($id)
    {
        try {
            $kriteria = Kriteria::findOrFail($id);
            $kriteria->delete();

            // Mengembalikan response sukses
            return response()->json([
                'status' => 'success',
                'message' => 'Kriteria berhasil di hapus.'
            ]);
        } catch (\Exception $e) {
            // Mengembalikan response error
            return response()->json([
                'status' => 'error',
                'message' => 'Error deleting kriteria.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
