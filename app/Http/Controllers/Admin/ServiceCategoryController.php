<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ServiceCategory;
use App\Models\ServiceCategoryLimit;
use App\Models\Kendaraan;

class ServiceCategoryController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | INDEX — tampilkan semua kategori + limit rules (flat table)
    |--------------------------------------------------------------------------
    */
    public function index(Request $request)
    {
        $filterCategory  = $request->category_id;
        $filterKendaraan = $request->kendaraan_id;

        $categories = ServiceCategory::withCount('parts')
            ->orderBy('nama')
            ->get();

        $limitsQuery = ServiceCategoryLimit::with(['category', 'kendaraan'])
            ->when($filterCategory,  fn($q) => $q->where('category_id',  $filterCategory))
            ->when($filterKendaraan, fn($q) => $q->where('kendaraan_id', $filterKendaraan))
            ->orderBy('category_id')
            ->orderBy('kendaraan_id');

        $limits = $limitsQuery->get();

        $kendaraans = Kendaraan::orderBy('merk')->get();

        return view('admin.service.service_categories.index', compact(
            'categories',
            'limits',
            'kendaraans',
            'filterCategory',
            'filterKendaraan'
        ));
    }

    /*
    |--------------------------------------------------------------------------
    | STORE — tambah kategori baru
    |--------------------------------------------------------------------------
    */
    public function store(Request $request)
    {
        $request->validate([
            'nama' => [
                'required',
                'string',
                'max:100',
                \Illuminate\Validation\Rule::unique('service_categories', 'nama'),
            ],
        ]);

        $cat = ServiceCategory::create(['nama' => $request->nama]);

        if ($request->expectsJson()) {
            return response()->json(['id' => $cat->id, 'nama' => $cat->nama]);
        }

        return back()->with('success', "Kategori \"{$cat->nama}\" berhasil ditambahkan.");
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE — rename kategori
    |--------------------------------------------------------------------------
    */
    public function update(Request $request, $id)
    {
        $category = ServiceCategory::findOrFail($id);

        $request->validate([
            'nama' => [
                'required',
                'string',
                'max:100',
                \Illuminate\Validation\Rule::unique('service_categories', 'nama')
                    ->ignore($category->id),
            ],
        ]);

        $category->update(['nama' => $request->nama]);

        return back()->with('success', "Kategori berhasil diperbarui menjadi \"{$category->nama}\".");
    }

    /*
    |--------------------------------------------------------------------------
    | DESTROY — hard delete kategori
    |--------------------------------------------------------------------------
    */
    public function destroy($id)
    {
        $category = ServiceCategory::findOrFail($id);

        // Tolak hapus jika ada parts aktif
        $activeParts = $category->parts()
            ->whereIn('status', ['Terpasang', 'Limit'])
            ->with('serviceHistory.kendaraan')
            ->get();

        if ($activeParts->isNotEmpty()) {
            $list = $activeParts->take(5)->map(function ($p) {
                $nopol = optional(optional($p->serviceHistory)->kendaraan)->nopol ?? '-';
                return "{$p->nama_part} ({$nopol})";
            })->implode(', ');

            $more = $activeParts->count() > 5
                ? ' dan ' . ($activeParts->count() - 5) . ' lainnya'
                : '';

            return back()->with('error',
                "Tidak bisa menghapus kategori \"{$category->nama}\" karena masih dipakai oleh part aktif: {$list}{$more}."
            );
        }

        $category->delete(); // hard delete

        return back()->with('success', "Kategori \"{$category->nama}\" berhasil dihapus.");
    }

    /*
    |--------------------------------------------------------------------------
    | STORE LIMIT RULE — tambah aturan limit untuk kendaraan + kategori
    |--------------------------------------------------------------------------
    */
    public function storeLimitRule(Request $request, $categoryId)
    {
        $category = ServiceCategory::findOrFail($categoryId);

        $request->validate([
            'kendaraan_id' => 'required|exists:kendaraan,id',
            'limit_nilai'  => 'required|integer|min:1',
            'limit_satuan' => 'required|in:hari,minggu,bulan,tahun',
            'limit_price'  => 'nullable|numeric|min:0',
        ]);

        $exists = ServiceCategoryLimit::where('kendaraan_id', $request->kendaraan_id)
            ->where('category_id', $categoryId)
            ->exists();

        if ($exists) {
            return back()->with('error',
                "Limit untuk kendaraan ini pada kategori \"{$category->nama}\" sudah ada. Gunakan tombol Edit."
            );
        }

        ServiceCategoryLimit::create([
            'kendaraan_id' => $request->kendaraan_id,
            'category_id'  => $categoryId,
            'limit_nilai'  => $request->limit_nilai,
            'limit_satuan' => $request->limit_satuan,
            'limit_price'  => $request->limit_price ? (int)$request->limit_price : null,
        ]);

        return back()->with('success', "Limit rule berhasil ditambahkan untuk kategori \"{$category->nama}\".");
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE LIMIT RULE — edit aturan limit
    |--------------------------------------------------------------------------
    */
    public function updateLimitRule(Request $request, $limitId)
    {
        $limit = ServiceCategoryLimit::with('category')->findOrFail($limitId);

        $request->validate([
            'limit_nilai'  => 'required|integer|min:1',
            'limit_satuan' => 'required|in:hari,minggu,bulan,tahun',
            'limit_price'  => 'nullable|numeric|min:0',
        ]);

        $limit->update([
            'limit_nilai'  => $request->limit_nilai,
            'limit_satuan' => $request->limit_satuan,
            'limit_price'  => $request->limit_price ? (int)$request->limit_price : null,
        ]);

        return back()->with('success', "Limit rule berhasil diperbarui.");
    }

    /*
    |--------------------------------------------------------------------------
    | DESTROY LIMIT RULE — hapus aturan limit
    |--------------------------------------------------------------------------
    */
    public function destroyLimitRule($limitId)
    {
        $limit = ServiceCategoryLimit::with('category')->findOrFail($limitId);
        $nama  = optional($limit->category)->nama ?? 'kategori';

        $limit->delete();

        return back()->with('success', "Limit rule untuk kategori \"{$nama}\" berhasil dihapus.");
    }

    /*
    |--------------------------------------------------------------------------
    | GET LIMIT FOR — Ajax: ambil limit rule untuk kendaraan + kategori
    |--------------------------------------------------------------------------
    */
    public function getLimitFor(Request $request)
    {
        $request->validate([
            'kendaraan_id' => 'required|integer',
            'category_id'  => 'required|integer',
        ]);

        $limit = ServiceCategoryLimit::where('kendaraan_id', $request->kendaraan_id)
            ->where('category_id', $request->category_id)
            ->first();

        if (!$limit) {
            return response()->json(null);
        }

        return response()->json([
            'limit_nilai'           => $limit->limit_nilai,
            'limit_satuan'          => $limit->limit_satuan,
            'limit_price'           => $limit->limit_price,
            'limit_price_formatted' => $limit->limitPriceFormatted(),
        ]);
    }
}
