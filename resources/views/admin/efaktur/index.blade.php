@extends('admin.layouts.app')
  
  @section('title', 'eFaktur & Bupot')
  
  @section('content')
  
  <link rel="stylesheet"
  href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
  
  {{-- TAB NAVIGATION --}}
  <div class="flex gap-1 border-b border-gray-200 mb-6">
      <button onclick="switchTab('efaktur')" id="tab-efaktur"
  class="tab-btn px-4 py-2 text-sm font-semibold border-b-2 border-blue-600 text-blue-600
  transition-all">
          <i class="bi bi-receipt mr-1"></i> E-Faktur
      </button>
      <button onclick="switchTab('bupot')" id="tab-bupot"
  class="tab-btn px-4 py-2 text-sm font-semibold border-b-2 border-transparent text-gray-400
  hover:text-gray-600 transition-all">
          <i class="bi bi-file-earmark-text-fill mr-1"></i> E-Bupot
      </button>
  </div>
  
  
  {{-- ======================================
       TAB EFAKTUR
  ====================================== --}}
  <div id="content-efaktur">
      <div class="space-y-6">
  
          <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
              <div>
                  <h1 class="text-2xl font-bold text-gray-800">Data eFaktur</h1>
                  <p class="text-sm text-gray-500 mt-0.5">Management data pajak eFaktur</p>
              </div>
              <button onclick="openModalTambah()"
  class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm
  font-medium px-4 py-2.5 rounded-xl shadow-sm transition-colors duration-150">
                  <i class="fa fa-plus text-sm"></i> Tambah eFaktur
              </button>
          </div>
  
          {{-- SUMMARY CARDS --}}
          <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-4">
              <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
                  <div class="flex items-center justify-between">
                      <div>
                          <p class="text-sm text-slate-500">Total eFaktur</p>
  <h3 class="text-3xl font-bold text-slate-800 mt-2">{{ $data->count() }}</h3>
                      </div>
  <div class="w-14 h-14 rounded-2xl bg-blue-100 text-blue-600 flex items-center justify-center">
                          <i class="fa-solid fa-receipt text-2xl"></i>
                      </div>
                  </div>
              </div>
              <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
                  <div class="flex items-center justify-between">
                      <div>
                          <p class="text-sm text-slate-500">Total DPP</p>
  <h3 class="text-lg font-bold text-indigo-600 mt-2 leading-tight">Rp {{
  number_format($data->sum('dpp'), 0, ',', '.') }}</h3>
                      </div>
  <div class="w-14 h-14 rounded-2xl bg-indigo-100 text-indigo-600 flex items-center
  justify-center">
                          <i class="fa-solid fa-wallet text-2xl"></i>
                      </div>
                  </div>
              </div>
              <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
                  <div class="flex items-center justify-between">
                      <div>
                          <p class="text-sm text-slate-500">Total PPN</p>
  <h3 class="text-lg font-bold text-green-600 mt-2 leading-tight">Rp {{
  number_format($data->sum('ppn'), 0, ',', '.') }}</h3>
                      </div>
  <div class="w-14 h-14 rounded-2xl bg-green-100 text-green-600 flex items-center
  justify-center">
                          <i class="fa-solid fa-money-bill-wave text-2xl"></i>
                      </div>
                  </div>
              </div>
              <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
                  <div class="flex items-center justify-between">
                      <div>
                          <p class="text-sm text-slate-500">Total PPNBM</p>
  <h3 class="text-lg font-bold text-orange-600 mt-2 leading-tight">Rp {{
  number_format($data->sum('ppnbm'), 0, ',', '.') }}</h3>
                      </div>
  <div class="w-14 h-14 rounded-2xl bg-orange-100 text-orange-600 flex items-center
  justify-center">
                          <i class="fa-solid fa-percent text-2xl"></i>
                      </div>
                  </div>
              </div>
              <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
                  <div class="flex items-center justify-between">
                      <div>
                          <p class="text-sm text-slate-500">Status Pending</p>
  <h3 class="text-3xl font-bold text-yellow-600 mt-2">{{ $data->where('status',
  'Pending')->count() }}</h3>
                      </div>
  <div class="w-14 h-14 rounded-2xl bg-yellow-100 text-yellow-600 flex items-center
  justify-center">
                          <i class="fa-solid fa-hourglass-half text-2xl"></i>
                      </div>
                  </div>
              </div>
              <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
                  <div class="flex items-center justify-between">
                      <div>
                          <p class="text-sm text-slate-500">Status Approve</p>
  <h3 class="text-3xl font-bold text-green-600 mt-2">{{ $data->where('status',
  'Approve')->count() }}</h3>
                      </div>
  <div class="w-14 h-14 rounded-2xl bg-green-100 text-green-600 flex items-center
  justify-center">
                          <i class="fa-solid fa-circle-check text-2xl"></i>
                      </div>
                  </div>
              </div>
              <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
                  <div class="flex items-center justify-between">
                      <div>
                          <p class="text-sm text-slate-500">Submit DJP</p>
  <h3 class="text-3xl font-bold text-purple-600 mt-2">{{ $data->where('status', 'Submit
  DJP')->count() }}</h3>
                      </div>
  <div class="w-14 h-14 rounded-2xl bg-purple-100 text-purple-600 flex items-center
  justify-center">
                          <i class="fa-solid fa-paper-plane text-2xl"></i>
                      </div>
                  </div>
              </div>
          </div>
  
          {{-- TABLE --}}
          <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
  <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 px-5 py-4
  border-b border-gray-100">
                  <div>
                      <h2 class="font-semibold text-gray-800 text-base">Daftar eFaktur</h2>
  <p class="text-xs text-gray-400 mt-0.5">{{ $data->count() }} total data eFaktur</p>
                  </div>
                  <div class="flex items-center gap-2">
  <a href="{{ route('efaktur.pdf') }}?search=" id="pdfLinkEfaktur" target="_blank"
  class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-white bg-red-500
  rounded-lg hover:bg-red-600">
                          <i class="fa fa-file-pdf"></i> PDF
                      </a>
                      <a href="{{ route('efaktur.export.excel') }}" id="excelLinkEfaktur"
  class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-white
  bg-green-600 rounded-lg hover:bg-green-700">
                          <i class="fa fa-file-excel"></i> Excel
                      </a>
                      <div class="relative">
  <i class="fa fa-search absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                          <input type="text" placeholder="Cari nomor faktur, nama lawan..."
                              oninput="filterTableEfaktur(this.value)"
  class="pl-8 pr-3 py-1.5 text-xs border border-gray-200 rounded-lg focus:outline-none
  focus:ring-2 focus:ring-blue-100 focus:border-blue-400 w-56">
                      </div>
                      <button onclick="window.location.reload()"
  class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-gray-600 border
  border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                          <i class="fa fa-sync text-xs"></i> Refresh
                      </button>
                  </div>
              </div>
              <div class="overflow-x-auto">
                  <table class="w-full text-sm">
                      <thead>
                          <tr class="bg-gray-50 border-b border-gray-100">
  <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4
  py-3">No</th>
  <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4
  py-3">Nomor Faktur</th>
  <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4
  py-3">Tanggal</th>
  <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4
  py-3">Tipe</th>
  <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4
  py-3">NPWP Lawan</th>
  <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4
  py-3">Nama Lawan</th>
  <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4
  py-3">DPP</th>
  <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4
  py-3">PPN</th>
  <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4
  py-3">PPNBM</th>
  <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4
  py-3">File</th>
  <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4
  py-3">Status</th>
  <th class="text-center text-xs font-semibold uppercase tracking-wide text-gray-500 px-4
  py-3">Aksi</th>
                          </tr>
                      </thead>
                      <tbody id="tableBodyEfaktur">
                          @forelse ($data as $item)
  <tr class="border-t border-gray-50 hover:bg-gray-50 transition-colors duration-100"
  data-search="{{ strtolower($item->nomor_faktur . ' ' . $item->nama_lawan . ' ' .
  $item->npwp_lawan . ' ' . $item->tipe . ' ' . $item->status) }}">
  <td class="px-4 py-3.5 text-gray-400 text-sm">{{ $loop->iteration }}</td>
  <td class="px-4 py-3.5"><span class="font-semibold text-gray-800">{{ $item->nomor_faktur
  }}</span></td>
  <td class="px-4 py-3.5 text-gray-600">{{ $item->tanggal_faktur }}</td>
                                  <td class="px-4 py-3.5">
                                      @if ($item->tipe == 'Keluaran')
  <span class="px-3 py-1 rounded-full text-xs font-semibold bg-green-100
  text-green-700">Keluaran</span>
                                      @else
  <span class="px-3 py-1 rounded-full text-xs font-semibold bg-blue-100
  text-blue-700">Masukan</span>
                                      @endif
                                  </td>
                                  <td class="px-4 py-3.5">
  <span class="font-mono text-xs text-gray-600 bg-gray-100 px-2 py-0.5 rounded">{{
  $item->npwp_lawan }}</span>
                                  </td>
  <td class="px-4 py-3.5 text-sm text-gray-700">{{ $item->nama_lawan }}</td>
  <td class="px-4 py-3.5 text-sm font-medium text-indigo-600">Rp {{ number_format($item->dpp, 0,
  ',', '.') }}</td>
  <td class="px-4 py-3.5 text-sm font-medium text-green-600">Rp {{ number_format($item->ppn, 0,
  ',', '.') }}</td>
  <td class="px-4 py-3.5 text-sm font-medium text-orange-600">Rp {{ number_format($item->ppnbm,
  0, ',', '.') }}</td>
                                  <td class="px-4 py-3.5">
                                      @if ($item->file_faktur)
                                          @php $filename = basename($item->file_faktur); @endphp
  <a href="{{ asset($item->file_faktur) }}" target="_blank"
  class="text-blue-600 underline text-xs hover:text-blue-800">{{ $filename }}</a>
                                      @else
                                          <span class="text-gray-400 text-xs">-</span>
                                      @endif
                                  </td>
                                  <td class="px-4 py-3.5">
                                      @if ($item->status == 'Approve')
  <span class="px-3 py-1 rounded-full text-xs font-semibold bg-green-100
  text-green-700">Approve</span>
                                      @elseif ($item->status == 'Submit DJP')
  <span class="px-3 py-1 rounded-full text-xs font-semibold bg-purple-100
  text-purple-700">Submit DJP</span>
                                      @else
  <span class="px-3 py-1 rounded-full text-xs font-semibold bg-yellow-100
  text-yellow-700">Pending</span>
                                      @endif
                                  </td>
                                  <td class="px-4 py-3.5">
                                      <div class="flex items-center justify-center gap-1.5">
                                          <button onclick="openEditModalEfaktur(
                                              '{{ $item->id }}',
                                              '{{ $item->nomor_faktur }}',
                                              '{{ $item->tanggal_faktur }}',
                                              '{{ $item->tipe }}',
                                              '{{ $item->npwp_lawan }}',
                                              '{{ addslashes($item->nama_lawan) }}',
                                              '{{ $item->dpp }}',
                                              '{{ $item->ppn }}',
                                              '{{ $item->ppnbm }}',
                                              '{{ $item->status }}',
  '{{ $item->file_faktur ? asset($item->file_faktur) : '' }}'
                                          )"
  class="inline-flex items-center gap-1 px-3 py-1 rounded-lg text-xs font-medium bg-yellow-100
  text-yellow-600 hover:bg-yellow-200 transition-colors">
                                              <i class="fa fa-edit text-xs"></i> Edit
                                          </button>
  <form action="{{ route('efaktur.destroy', $item->id) }}" method="POST"
  onsubmit="return confirm('Yakin ingin menghapus data ini?')" class="inline">
                                              @csrf
                                              @method('DELETE')
                                              <button type="submit"
  class="inline-flex items-center gap-1 px-3 py-1 rounded-lg text-xs font-medium bg-red-100
  text-red-600 hover:bg-red-200 transition-colors">
                                                  <i class="fa fa-trash text-xs"></i> Hapus
                                              </button>
                                          </form>
                                      </div>
                                  </td>
                              </tr>
                          @empty
                              <tr>
                                  <td colspan="12" class="px-5 py-12 text-center">
                                      <div class="flex flex-col items-center gap-3">
  <div class="w-14 h-14 rounded-full bg-gray-100 flex items-center justify-center">
  <i class="fa fa-receipt text-2xl text-gray-300"></i>
                                          </div>
  <p class="text-sm font-medium text-gray-500">Belum ada data eFaktur</p>
  <p class="text-xs text-gray-400">Klik "Tambah eFaktur" untuk menambahkan data baru</p>
                                      </div>
                                  </td>
                              </tr>
                          @endforelse
                      </tbody>
                  </table>
              </div>
          </div>
  
      </div>
  </div>
  {{-- /TAB EFAKTUR --}}
  
  
  {{-- ======================================
       TAB BUPOT
  ====================================== --}}
  <div id="content-bupot" class="hidden">
      <div class="space-y-6">
  
          <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
              <div>
                  <h1 class="text-2xl font-bold text-gray-800">Data Bupot</h1>
                  <p class="text-sm text-gray-500 mt-0.5">Data Bukti Potong Pajak</p>
              </div>
              <button onclick="openModalTambahBupot()"
  class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm
  font-medium px-4 py-2.5 rounded-xl shadow-sm transition-colors duration-150">
                  <i class="fa fa-plus text-sm"></i> Tambah Data
              </button>
          </div>
  
          {{-- SUMMARY CARDS --}}
          <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
              <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
                  <div class="flex items-center justify-between">
                      <div>
                          <p class="text-sm text-slate-500">Total Bupot</p>
  <h3 class="text-3xl font-bold text-slate-800 mt-2">{{ $dataBupot->count() }}</h3>
                      </div>
  <div class="w-14 h-14 rounded-2xl bg-blue-100 text-blue-600 flex items-center justify-center">
                          <i class="fa-solid fa-file-lines text-2xl"></i>
                      </div>
                  </div>
              </div>
              <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
                  <div class="flex items-center justify-between">
                      <div>
                          <p class="text-sm text-slate-500">Total Jumlah Bruto</p>
  <h3 class="text-lg font-bold text-green-600 mt-2 leading-tight">Rp {{
  number_format($dataBupot->sum('jumlah_bruto'), 0, ',', '.') }}</h3>
                      </div>
  <div class="w-14 h-14 rounded-2xl bg-green-100 text-green-600 flex items-center
  justify-center">
                          <i class="fa-solid fa-money-bill-wave text-2xl"></i>
                      </div>
                  </div>
              </div>
              <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
                  <div class="flex items-center justify-between">
                      <div>
                          <p class="text-sm text-slate-500">Total Pajak Dipotong</p>
  <h3 class="text-lg font-bold text-red-600 mt-2 leading-tight">Rp {{
  number_format($dataBupot->sum('jumlah_potong'), 0, ',', '.') }}</h3>
                      </div>
  <div class="w-14 h-14 rounded-2xl bg-red-100 text-red-600 flex items-center justify-center">
                          <i class="fa-solid fa-scissors text-2xl"></i>
                      </div>
                  </div>
              </div>
              <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
                  <div class="flex items-center justify-between">
                      <div>
                          <p class="text-sm text-slate-500">Status Draft</p>
  <h3 class="text-3xl font-bold text-yellow-600 mt-2">{{ $dataBupot->where('status',
  'Draft')->count() }}</h3>
                      </div>
  <div class="w-14 h-14 rounded-2xl bg-yellow-100 text-yellow-600 flex items-center
  justify-center">
                          <i class="fa-solid fa-hourglass-half text-2xl"></i>
                      </div>
                  </div>
              </div>
              <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
                  <div class="flex items-center justify-between">
                      <div>
                          <p class="text-sm text-slate-500">Status Approve</p>
  <h3 class="text-3xl font-bold text-emerald-600 mt-2">{{ $dataBupot->where('status',
  'Approve')->count() }}</h3>
                      </div>
  <div class="w-14 h-14 rounded-2xl bg-emerald-100 text-emerald-600 flex items-center
  justify-center">
                          <i class="fa-solid fa-circle-check text-2xl"></i>
                      </div>
                  </div>
              </div>
              <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
                  <div class="flex items-center justify-between">
                      <div>
                          <p class="text-sm text-slate-500">Status Submit DJP</p>
  <h3 class="text-3xl font-bold text-emerald-600 mt-2">{{ $dataBupot->where('status', 'Submit
  DJP')->count() }}</h3>
                      </div>
  <div class="w-14 h-14 rounded-2xl bg-emerald-100 text-emerald-600 flex items-center
  justify-center">
                          <i class="fa-solid fa-circle-check text-2xl"></i>
                      </div>
                  </div>
              </div>
              <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
                  <div class="flex items-center justify-between">
                      <div>
                          <p class="text-sm text-slate-500">Rata-rata Tarif Pajak</p>
  <h3 class="text-3xl font-bold text-purple-600 mt-2">{{
  number_format($dataBupot->avg('tarif_pajak'), 1) }}%</h3>
                      </div>
  <div class="w-14 h-14 rounded-2xl bg-purple-100 text-purple-600 flex items-center
  justify-center">
                          <i class="fa-solid fa-percent text-2xl"></i>
                      </div>
                  </div>
              </div>
          </div>
  
          {{-- TABLE --}}
          <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
  <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 px-5 py-4
  border-b border-gray-100">
                  <div>
                      <h2 class="font-semibold text-gray-800 text-base">Daftar Bukti Potong</h2>
  <p class="text-xs text-gray-400 mt-0.5">{{ $dataBupot->count() }} total data bupot</p>
                  </div>
                  <div class="flex items-center gap-2">
  <a href="{{ route('bupot.pdf') }}?search=" id="pdfLinkBupot" target="_blank"
  class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-white bg-red-500
  rounded-lg hover:bg-red-600">
                          <i class="fa fa-file-pdf"></i> PDF
                      </a>
                      <a href="{{ route('bupot.export.excel') }}" id="excelLinkBupot"
  class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-white
  bg-green-600 rounded-lg hover:bg-green-700">
                          <i class="fa fa-file-excel"></i> Excel
                      </a>
                      <div class="relative">
  <i class="fa fa-search absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                          <input type="text" placeholder="Cari nomor, pemotong, dipotong..."
                              oninput="filterTableBupot(this.value)"
  class="pl-8 pr-3 py-1.5 text-xs border border-gray-200 rounded-lg focus:outline-none
  focus:ring-2 focus:ring-blue-100 focus:border-blue-400 w-56">
                      </div>
                      <button onclick="window.location.reload()"
  class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-gray-600 border
  border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                          <i class="fa fa-sync text-xs"></i> Refresh
                      </button>
                  </div>
              </div>
              <div class="overflow-x-auto">
                  <table class="w-full text-sm">
                      <thead>
                          <tr class="bg-gray-50 border-b border-gray-100">
  <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4
  py-3">No</th>
 <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4
  py-3">Nomor Bukti</th>
  <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4
  py-3">Tanggal</th>
  <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4
  py-3">Tipe</th>
  <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4
  py-3">Pemotong</th>
  <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4
  py-3">Dipotong</th>
  <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4
  py-3">Bruto</th>
  <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4
  py-3">Tarif</th>
  <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4
  py-3">Jml Potong</th>
  <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4
  py-3">File</th>
  <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4
  py-3">Status</th>
  <th class="text-center text-xs font-semibold uppercase tracking-wide text-gray-500 px-4
  py-3">Aksi</th>
                          </tr>
                      </thead>
                      <tbody id="tableBodyBupot">
                          @forelse ($dataBupot as $item)
  <tr class="border-t border-gray-50 hover:bg-gray-50 transition-colors duration-100"
  data-search="{{ strtolower($item->nomor_bukti . ' ' . $item->nama_pemotong . ' ' .
  $item->nama_dipotong . ' ' . $item->tipe . ' ' . $item->status) }}">
  <td class="px-4 py-3.5 text-gray-400">{{ $loop->iteration }}</td>
  <td class="px-4 py-3.5"><span class="font-semibold text-gray-800">{{ $item->nomor_bukti
  }}</span></td>
  <td class="px-4 py-3.5 text-gray-600">{{ $item->tanggal_bukti }}</td>
                                  <td class="px-4 py-3.5">
  <span class="font-mono text-xs text-gray-600 bg-gray-100 px-2 py-0.5 rounded">{{ $item->tipe
  }}</span>
                                  </td>
  <td class="px-4 py-3.5 text-sm text-gray-700">{{ $item->nama_pemotong }}</td>
  <td class="px-4 py-3.5 text-sm text-gray-700">{{ $item->nama_dipotong }}</td>
  <td class="px-4 py-3.5 text-sm font-medium text-green-600">Rp {{
  number_format($item->jumlah_bruto, 0, ',', '.') }}</td>
  <td class="px-4 py-3.5 text-sm text-purple-600 font-medium">{{ $item->tarif_pajak }}%</td>
  <td class="px-4 py-3.5 text-sm font-bold text-red-500">Rp {{
  number_format($item->jumlah_potong, 0, ',', '.') }}</td>
                                  <td class="px-4 py-3.5">
                                      @if ($item->file_bupot)
                                          @php $filename = basename($item->file_bupot); @endphp
  <a href="{{ asset($item->file_bupot) }}" target="_blank"
  class="text-blue-600 underline text-xs hover:text-blue-800">{{ $filename }}</a>
                                      @else
                                          <span class="text-gray-400 text-xs">-</span>
                                      @endif
                                  </td>
                                  <td class="px-4 py-3.5">
                                      @if ($item->status == 'Approve')
  <span class="px-3 py-1 rounded-full text-xs font-semibold bg-green-100
  text-green-700">Approve</span>
                                      @elseif ($item->status == 'Submit DJP')
  <span class="px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-700">Submit
  DJP</span>
                                      @else
  <span class="px-3 py-1 rounded-full text-xs font-semibold bg-yellow-100
  text-yellow-700">Draft</span>
                                      @endif
                                  </td>
                                  <td class="px-4 py-3.5">
                                      <div class="flex items-center justify-center gap-1.5">
                                          <button onclick="openEditModalBupot(
                                              '{{ $item->id }}',
                                              '{{ $item->nomor_bukti }}',
                                              '{{ $item->tanggal_bukti }}',
                                              '{{ $item->tipe }}',
                                              '{{ $item->npwp_pemotong }}',
                                              '{{ addslashes($item->nama_pemotong) }}',
                                              '{{ $item->npwp_dipotong }}',
                                              '{{ addslashes($item->nama_dipotong) }}',
                                              '{{ $item->jumlah_bruto }}',
                                              '{{ $item->tarif_pajak }}',
                                              '{{ $item->status }}'
                                          )"
  class="inline-flex items-center gap-1 px-3 py-1 rounded-lg text-xs font-medium bg-yellow-100
  text-yellow-600 hover:bg-yellow-200 transition-colors">
                                              <i class="fa fa-edit text-xs"></i> Edit
                                          </button>
  <form action="{{ route('bupot.destroy', $item->id) }}" method="POST"
  onsubmit="return confirm('Yakin ingin menghapus data ini?')" class="inline">
                                              @csrf
                                              @method('DELETE')
                                              <button type="submit"
  class="inline-flex items-center gap-1 px-3 py-1 rounded-lg text-xs font-medium bg-red-100
  text-red-600 hover:bg-red-200 transition-colors">
                                                  <i class="fa fa-trash text-xs"></i> Hapus
                                              </button>
                                          </form>
                                      </div>
                                  </td>
                              </tr>
                          @empty
                              <tr>
                                  <td colspan="12" class="px-5 py-12 text-center">
                                      <div class="flex flex-col items-center gap-3">
  <div class="w-14 h-14 rounded-full bg-gray-100 flex items-center justify-center">
  <i class="fa fa-file-lines text-2xl text-gray-300"></i>
                                          </div>
  <p class="text-sm font-medium text-gray-500">Belum ada data Bupot</p>
  <p class="text-xs text-gray-400">Klik "Tambah Data" untuk menambahkan data baru</p>
                                      </div>
                                  </td>
                              </tr>
                          @endforelse
                      </tbody>
                  </table>
              </div>
          </div>
  
      </div>
  </div>
  {{-- /TAB BUPOT --}}
  
  
  {{-- ======================================
       MODAL TAMBAH EFAKTUR
  ====================================== --}}
  <div id="modalTambahEfaktur" class="fixed inset-0 z-50 hidden items-center justify-center
  bg-black/30 p-4" style="backdrop-filter:blur(2px)">
  <div class="bg-white rounded-2xl shadow-xl w-full max-w-2xl max-h-[90vh] overflow-y-auto"
  style="animation:slideUp .2s ease">
          <div class="flex items-start justify-between px-6 py-5 border-b border-gray-100">
              <div>
                  <h2 class="text-base font-bold text-gray-800">Tambah eFaktur</h2>
                  <p class="text-xs text-gray-500 mt-0.5">Isi data pajak eFaktur</p>
              </div>
  <button onclick="closeModalTambah()" class="text-gray-400 hover:text-red-500 transition-colors
  text-lg leading-none mt-0.5">
                  <i class="fa fa-times"></i>
              </button>
          </div>
  <form action="{{ route('efaktur.store') }}" method="POST" enctype="multipart/form-data"
              class="px-6 py-5 grid grid-cols-1 md:grid-cols-2 gap-4">
              @csrf
              <div>
  <label class="block text-xs font-semibold text-gray-600 mb-1.5">Nomor Faktur <span
  class="text-red-500">*</span></label>
  <input type="text" name="nomor_faktur" required placeholder="Masukkan nomor faktur"
  class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none
  focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
              </div>
              <div>
  <label class="block text-xs font-semibold text-gray-600 mb-1.5">Tanggal Faktur <span
  class="text-red-500">*</span></label>
                  <input type="date" name="tanggal_faktur" required
  class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none
  focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
              </div>
              <div>
  <label class="block text-xs font-semibold text-gray-600 mb-1.5">Tipe <span
  class="text-red-500">*</span></label>
                  <select name="tipe" required
  class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none
  focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                      <option value="">-- Pilih Tipe --</option>
                      <option value="Keluaran">Keluaran</option>
                      <option value="Masukan">Masukan</option>
                  </select>
              </div>
              <div>
  <label class="block text-xs font-semibold text-gray-600 mb-1.5">NPWP Lawan <span
  class="text-red-500">*</span></label>
  <input type="text" name="npwp_lawan" required placeholder="xx.xxx.xxx.x-xxx.xxx"
  class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none
  focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
              </div>
              <div class="md:col-span-2">
  <label class="block text-xs font-semibold text-gray-600 mb-1.5">Nama Lawan <span
  class="text-red-500">*</span></label>
  <input type="text" name="nama_lawan" required placeholder="Nama perusahaan / individu"
  class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none
  focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
              </div>
              <div>
  <label class="block text-xs font-semibold text-gray-600 mb-1.5">DPP <span
  class="text-red-500">*</span></label>
                  <input type="number" name="dpp" required placeholder="0"
  class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none
  focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
              </div>
              <div>
  <label class="block text-xs font-semibold text-gray-600 mb-1.5">PPN <span
  class="text-red-500">*</span></label>
                  <input type="number" name="ppn" required placeholder="0"
  class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none
  focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
              </div>
              <div>
  <label class="block text-xs font-semibold text-gray-600 mb-1.5">PPNBM <span
  class="text-red-500">*</span></label>
                  <input type="number" name="ppnbm" value="0" required placeholder="0"
  class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none
  focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
              </div>
              <div>
  <label class="block text-xs font-semibold text-gray-600 mb-1.5">Status <span
  class="text-red-500">*</span></label>
                  <select name="status" required
  class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none
  focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                      <option value="Pending">Pending</option>
                      <option value="Approve">Approve</option>
                      <option value="Submit DJP">Submit DJP</option>
                  </select>
              </div>
              <div class="md:col-span-2">
  <label class="block text-xs font-semibold text-gray-600 mb-2">File e-Faktur</label>
                  <div id="previewFakturTambah" class="hidden mb-3"></div>
                  <label for="file_faktur"
  class="flex flex-col items-center justify-center w-full h-32 border-2 border-dashed
  border-gray-300 rounded-xl cursor-pointer hover:border-blue-400 hover:bg-blue-50 transition">
                      <i class="fa-solid fa-cloud-arrow-up text-3xl text-gray-400 mb-2"></i>
  <span class="text-sm text-gray-600 font-medium">Klik atau drag file ke sini</span>
  <span class="text-[11px] text-gray-400 mt-1">JPG, PNG, PDF (max 2MB)</span>
                  </label>
  <input type="file" id="file_faktur" name="file_faktur" class="hidden" accept="image/*,.pdf"
                      onchange="previewFile(event, 'previewFakturTambah', 'file_faktur')">
              </div>
              <div class="md:col-span-2 flex gap-3 pt-1">
                  <button type="button" onclick="closeModalTambah()"
  class="flex-1 border border-gray-200 text-gray-600 text-sm font-medium py-2.5 rounded-xl
  hover:bg-gray-50 transition-colors">Batal</button>
                  <button type="submit"
  class="flex-1 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold py-2.5 rounded-xl
  transition-colors duration-150 flex items-center justify-center gap-2">
                      <i class="fa fa-save text-sm"></i> Simpan
                  </button>
              </div>
          </form>
      </div>
  </div>
  
  
  {{-- ======================================
       MODAL EDIT EFAKTUR
  ====================================== --}}
  <div id="modalEditEfaktur" class="fixed inset-0 z-50 hidden items-center justify-center
  bg-black/30 p-4" style="backdrop-filter:blur(2px)">
  <div class="bg-white rounded-2xl shadow-xl w-full max-w-2xl max-h-[90vh] overflow-y-auto"
  style="animation:slideUp .2s ease">
          <div class="flex items-start justify-between px-6 py-5 border-b border-gray-100">
              <div>
                  <h2 class="text-base font-bold text-gray-800">Edit eFaktur</h2>
                  <p class="text-xs text-gray-500 mt-0.5">Perbarui data pajak eFaktur</p>
              </div>
  <button onclick="closeEditModalEfaktur()" class="text-gray-400 hover:text-red-500
  transition-colors text-lg leading-none mt-0.5">
                  <i class="fa fa-times"></i>
              </button>
          </div>
          <form id="formEditEfaktur" method="POST" enctype="multipart/form-data"
              class="px-6 py-5 grid grid-cols-1 md:grid-cols-2 gap-4">
              @csrf
              @method('PUT')
              <div>
  <label class="block text-xs font-semibold text-gray-600 mb-1.5">Nomor Faktur <span
  class="text-red-500">*</span></label>
                  <input type="text" name="nomor_faktur" id="ef_nomor_faktur" required
  class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none
  focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
              </div>
              <div>
  <label class="block text-xs font-semibold text-gray-600 mb-1.5">Tanggal Faktur <span
  class="text-red-500">*</span></label>
                  <input type="date" name="tanggal_faktur" id="ef_tanggal_faktur" required
  class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none
  focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
              </div>
              <div>
  <label class="block text-xs font-semibold text-gray-600 mb-1.5">Tipe <span
  class="text-red-500">*</span></label>
                  <select name="tipe" id="ef_tipe" required
  class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none
  focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                      <option value="Keluaran">Keluaran</option>
                      <option value="Masukan">Masukan</option>
                  </select>
              </div>
              <div>
  <label class="block text-xs font-semibold text-gray-600 mb-1.5">NPWP Lawan <span
  class="text-red-500">*</span></label>
                  <input type="text" name="npwp_lawan" id="ef_npwp_lawan" required
  class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none
  focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
              </div>
              <div class="md:col-span-2">
  <label class="block text-xs font-semibold text-gray-600 mb-1.5">Nama Lawan <span
  class="text-red-500">*</span></label>
                  <input type="text" name="nama_lawan" id="ef_nama_lawan" required
  class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none
  focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
              </div>
              <div>
  <label class="block text-xs font-semibold text-gray-600 mb-1.5">DPP <span
  class="text-red-500">*</span></label>
                  <input type="number" name="dpp" id="ef_dpp" required
  class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none
  focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
              </div>
              <div>
  <label class="block text-xs font-semibold text-gray-600 mb-1.5">PPN <span
  class="text-red-500">*</span></label>
                  <input type="number" name="ppn" id="ef_ppn" required
  class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none
  focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
              </div>
              <div>
  <label class="block text-xs font-semibold text-gray-600 mb-1.5">PPNBM <span
  class="text-red-500">*</span></label>
                  <input type="number" name="ppnbm" id="ef_ppnbm" required
  class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none
  focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
              </div>
              <div>
  <label class="block text-xs font-semibold text-gray-600 mb-1.5">Status <span
  class="text-red-500">*</span></label>
                  <select name="status" id="ef_status" required
  class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none
  focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                      <option value="Pending">Pending</option>
                      <option value="Approve">Approve</option>
                      <option value="Submit DJP">Submit DJP</option>
                  </select>
              </div>
              <div class="md:col-span-2">
  <label class="block text-xs font-semibold text-gray-600 mb-2">File e-Faktur</label>
                  <div id="previewFakturEdit" class="hidden mb-3"></div>
                  <label for="file_faktur_edit"
  class="flex flex-col items-center justify-center w-full h-32 border-2 border-dashed
  border-gray-300 rounded-xl cursor-pointer hover:border-blue-400 hover:bg-blue-50 transition">
                      <i class="fa-solid fa-cloud-arrow-up text-3xl text-gray-400 mb-2"></i>
  <span class="text-sm text-gray-600 font-medium">Klik atau drag file ke sini</span>
  <span class="text-[11px] text-gray-400 mt-1">JPG, PNG, PDF (max 2MB)</span>
                  </label>
  <input type="file" id="file_faktur_edit" name="file_faktur" class="hidden"
  accept="image/*,.pdf"
                      onchange="previewFile(event, 'previewFakturEdit', 'file_faktur_edit')">
  <p class="text-xs text-gray-400 mt-1">Kosongkan jika tidak ingin mengganti file</p>
              </div>
              <div class="md:col-span-2 flex gap-3 pt-1">
                  <button type="button" onclick="closeEditModalEfaktur()"
  class="flex-1 border border-gray-200 text-gray-600 text-sm font-medium py-2.5 rounded-xl
  hover:bg-gray-50 transition-colors">Batal</button>
                  <button type="submit"
  class="flex-1 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold py-2.5 rounded-xl
  transition-colors duration-150 flex items-center justify-center gap-2">
                      <i class="fa fa-save text-sm"></i> Update
                  </button>
              </div>
          </form>
      </div>
  </div>
  
  
  {{-- ======================================
       MODAL TAMBAH BUPOT
  ====================================== --}}
  <div id="modalTambahBupot" class="fixed inset-0 z-50 hidden items-center justify-center
  bg-black/30 p-4" style="backdrop-filter:blur(2px)">
  <div class="bg-white rounded-2xl shadow-xl w-full max-w-3xl max-h-[90vh] overflow-y-auto"
  style="animation:slideUp .2s ease">
          <div class="flex items-start justify-between px-6 py-5 border-b border-gray-100">
              <div>
                  <h2 class="text-base font-bold text-gray-800">Tambah Data Bupot</h2>
                  <p class="text-xs text-gray-500 mt-0.5">Isi data bukti potong pajak</p>
              </div>
  <button onclick="closeModalTambahBupot()" class="text-gray-400 hover:text-red-500
  transition-colors text-lg leading-none mt-0.5">
                  <i class="fa fa-times"></i>
              </button>
          </div>
          <form action="{{ route('bupot.store') }}" method="POST" enctype="multipart/form-data"
              class="px-6 py-5 grid grid-cols-1 md:grid-cols-2 gap-4">
              @csrf
              <div>
  <label class="block text-xs font-semibold text-gray-600 mb-1.5">Tanggal Bukti <span
  class="text-red-500">*</span></label>
                  <input type="date" name="tanggal_bukti" required
  class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none
  focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
              </div>
              <div>
  <label class="block text-xs font-semibold text-gray-600 mb-1.5">Tipe <span
  class="text-red-500">*</span></label>
                  <select name="tipe" required
  class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none
  focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                      <option value="PPh21">PPh21</option>
                      <option value="PPh22">PPh22</option>
                      <option value="PPh23">PPh23</option>
                      <option value="PPh26">PPh26</option>
                  </select>
              </div>
              <div>
  <label class="block text-xs font-semibold text-gray-600 mb-1.5">Status <span
  class="text-red-500">*</span></label>
                  <select name="status" required
  class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none
  focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                      <option value="Draft">Draft</option>
                      <option value="Approve">Approve</option>
                      <option value="Submit DJP">Submit DJP</option>
                  </select>
              </div>
              <div>
  <label class="block text-xs font-semibold text-gray-600 mb-1.5">NPWP Pemotong <span
  class="text-red-500">*</span></label>
  <input type="text" name="npwp_pemotong" required placeholder="xx.xxx.xxx.x-xxx.xxx"
  class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none
  focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
              </div>
              <div>
  <label class="block text-xs font-semibold text-gray-600 mb-1.5">Nama Pemotong <span
  class="text-red-500">*</span></label>
                  <input type="text" name="nama_pemotong" required placeholder="Nama pemotong"
  class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none
  focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
              </div>
              <div>
  <label class="block text-xs font-semibold text-gray-600 mb-1.5">NPWP Dipotong <span
  class="text-red-500">*</span></label>
  <input type="text" name="npwp_dipotong" required placeholder="xx.xxx.xxx.x-xxx.xxx"
  class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none
  focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
              </div>
            <div>
  <label class="block text-xs font-semibold text-gray-600 mb-1.5">Nama Dipotong <span
  class="text-red-500">*</span></label>
      <input type="text" name="nama_dipotong" required placeholder="Nama dipotong"
  class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none
  focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
  </div>
              <div>
  <label class="block text-xs font-semibold text-gray-600 mb-1.5">Jumlah Bruto <span
  class="text-red-500">*</span></label>
                  <input type="number" name="jumlah_bruto" required placeholder="0"
  class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none
  focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
              </div>
              <div>
  <label class="block text-xs font-semibold text-gray-600 mb-1.5">Tarif Pajak % <span
  class="text-red-500">*</span></label>
  <input type="number" step="0.01" name="tarif_pajak" required placeholder="0.00"
  class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none
  focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
              </div>
              <div class="md:col-span-2">
  <label class="block text-xs font-semibold text-gray-600 mb-2">File Bupot</label>
                  <div id="previewBupotTambah" class="hidden mb-3"></div>
                  <label for="file_bupot"
  class="flex flex-col items-center justify-center w-full h-32 border-2 border-dashed
  border-gray-300 rounded-xl cursor-pointer hover:border-blue-400 hover:bg-blue-50 transition">
                      <i class="fa-solid fa-cloud-arrow-up text-3xl text-gray-400 mb-2"></i>
  <span class="text-sm text-gray-600 font-medium">Klik atau drag file ke sini</span>
                      <span class="text-[11px] text-gray-400 mt-1">(max 2MB)</span>
                  </label>
                  <input type="file" id="file_bupot" name="file_bupot" class="hidden"
  onchange="previewFile(event, 'previewBupotTambah', 'file_bupot')" required>
              </div>
              <div class="md:col-span-2 flex gap-3 pt-1">
                  <button type="button" onclick="closeModalTambahBupot()"
  class="flex-1 border border-gray-200 text-gray-600 text-sm font-medium py-2.5 rounded-xl
  hover:bg-gray-50 transition-colors">Batal</button>
                  <button type="submit"
  class="flex-1 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold py-2.5 rounded-xl
  transition-colors duration-150 flex items-center justify-center gap-2">
                      <i class="fa fa-save text-sm"></i> Simpan
                  </button>
              </div>
          </form>
      </div>
  </div>
  
  
  {{-- ======================================
       MODAL EDIT BUPOT
  ====================================== --}}
  <div id="modalEditBupot" class="fixed inset-0 z-50 hidden items-center justify-center
  bg-black/30 p-4" style="backdrop-filter:blur(2px)">
  <div class="bg-white rounded-2xl shadow-xl w-full max-w-3xl max-h-[90vh] overflow-y-auto"
  style="animation:slideUp .2s ease">
          <div class="flex items-start justify-between px-6 py-5 border-b border-gray-100">
              <div>
                  <h2 class="text-base font-bold text-gray-800">Edit Data Bupot</h2>
                  <p class="text-xs text-gray-500 mt-0.5">Perbarui data bukti potong pajak</p>
              </div>
  <button onclick="closeEditModalBupot()" class="text-gray-400 hover:text-red-500
  transition-colors text-lg leading-none mt-0.5">
                  <i class="fa fa-times"></i>
              </button>
          </div>
          <form id="formEditBupot" method="POST" enctype="multipart/form-data"
              class="px-6 py-5 grid grid-cols-1 md:grid-cols-2 gap-4">
              @csrf
              @method('PUT')
              <div>
  <label class="block text-xs font-semibold text-gray-600 mb-1.5">Nomor Bukti</label>
                  <input type="text" id="bp_nomor_bukti" readonly
  class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm bg-gray-50 text-gray-400
  cursor-not-allowed">
              </div>
              <div>
  <label class="block text-xs font-semibold text-gray-600 mb-1.5">Tanggal Bukti <span
  class="text-red-500">*</span></label>
                  <input type="date" name="tanggal_bukti" id="bp_tanggal_bukti" required
  class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none
  focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
              </div>
              <div>
  <label class="block text-xs font-semibold text-gray-600 mb-1.5">Tipe <span
  class="text-red-500">*</span></label>
                  <select name="tipe" id="bp_tipe" required
  class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none
  focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                      <option value="PPh21">PPh21</option>
                      <option value="PPh22">PPh22</option>
                      <option value="PPh23">PPh23</option>
                      <option value="PPh26">PPh26</option>
                  </select>
              </div>
              <div>
  <label class="block text-xs font-semibold text-gray-600 mb-1.5">Status <span
  class="text-red-500">*</span></label>
                  <select name="status" id="bp_status" required
  class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none
  focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                      <option value="Draft">Draft</option>
                      <option value="Approve">Approve</option>
                      <option value="Submit DJP">Submit DJP</option>
                  </select>
              </div>
              <div>
  <label class="block text-xs font-semibold text-gray-600 mb-1.5">NPWP Pemotong <span
  class="text-red-500">*</span></label>
                  <input type="text" name="npwp_pemotong" id="bp_npwp_pemotong" required
  class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none
  focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
              </div>
              <div>
  <label class="block text-xs font-semibold text-gray-600 mb-1.5">Nama Pemotong <span
  class="text-red-500">*</span></label>
                  <input type="text" name="nama_pemotong" id="bp_nama_pemotong" required
  class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none
  focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
              </div>
              <div>
  <label class="block text-xs font-semibold text-gray-600 mb-1.5">NPWP Dipotong <span
  class="text-red-500">*</span></label>
                  <input type="text" name="npwp_dipotong" id="bp_npwp_dipotong" required
  class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none
  focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
              </div>
              <div>
  <label class="block text-xs font-semibold text-gray-600 mb-1.5">Nama Dipotong <span
  class="text-red-500">*</span></label>
                  <input type="text" name="nama_dipotong" id="bp_nama_dipotong" required
  class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none
  focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
              </div>
              <div>
  <label class="block text-xs font-semibold text-gray-600 mb-1.5">Jumlah Bruto <span
  class="text-red-500">*</span></label>
                  <input type="number" name="jumlah_bruto" id="bp_jumlah_bruto" required
  class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none
  focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
              </div>
              <div>
  <label class="block text-xs font-semibold text-gray-600 mb-1.5">Tarif Pajak % <span
  class="text-red-500">*</span></label>
  <input type="number" step="0.01" name="tarif_pajak" id="bp_tarif_pajak" required
  class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none
  focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
              </div>
              <div class="md:col-span-2">
  <label class="block text-xs font-semibold text-gray-600 mb-2">File Bupot</label>
                  <div id="previewBupotEdit" class="hidden mb-3"></div>
                  <label for="file_bupot_edit"
  class="flex flex-col items-center justify-center w-full h-32 border-2 border-dashed
  border-gray-300 rounded-xl cursor-pointer hover:border-blue-400 hover:bg-blue-50 transition">
                      <i class="fa-solid fa-cloud-arrow-up text-3xl text-gray-400 mb-2"></i>
  <span class="text-sm text-gray-600 font-medium">Klik atau drag file ke sini</span>
                      <span class="text-[11px] text-gray-400 mt-1">(max 2MB)</span>
                  </label>
                  <input type="file" id="file_bupot_edit" name="file_bupot" class="hidden"
                      onchange="previewFile(event, 'previewBupotEdit', 'file_bupot_edit')">
  <p class="text-xs text-gray-400 mt-1">Kosongkan jika tidak ingin mengganti file</p>
              </div>
              <div class="md:col-span-2 flex gap-3 pt-1">
                  <button type="button" onclick="closeEditModalBupot()"
  class="flex-1 border border-gray-200 text-gray-600 text-sm font-medium py-2.5 rounded-xl
  hover:bg-gray-50 transition-colors">Batal</button>
                  <button type="submit"
  class="flex-1 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold py-2.5 rounded-xl
  transition-colors duration-150 flex items-center justify-center gap-2">
                      <i class="fa fa-save text-sm"></i> Update
                  </button>
              </div>
          </form>
      </div>
  </div>
  
  
  {{-- ======================================
       POPUP ALERT
  ====================================== --}}
  @if (session('success') || session('error') || $errors->any())
      <div id="alertOverlay" class="fixed inset-0 z-[9999] flex items-start justify-center pt-6"
  style="background:rgba(0,0,0,0.18);opacity:0;transition:opacity 0.2s;pointer-events:none">
          <div id="alertBox"
  class="bg-white rounded-xl shadow-xl border border-gray-100 px-5 py-4 flex items-start gap-3
  w-full max-w-md mx-4"
              style="transform:translateY(-16px);transition:transform 0.25s">
              @if (session('success'))
  <div class="w-10 h-10 rounded-xl bg-green-50 flex items-center justify-center flex-shrink-0
  text-green-600 text-xl">
                      <i class="fa fa-check-circle"></i>
                  </div>
                  <div class="flex-1 min-w-0">
                      <p class="text-sm font-bold text-gray-800">Berhasil!</p>
  <p class="text-xs text-gray-500 mt-0.5 leading-relaxed">{{ session('success') }}</p>
                  </div>
              @elseif (session('error'))
  <div class="w-10 h-10 rounded-xl bg-red-50 flex items-center justify-center flex-shrink-0
  text-red-500 text-xl">
                      <i class="fa fa-exclamation-circle"></i>
                  </div>
                  <div class="flex-1 min-w-0">
                      <p class="text-sm font-bold text-gray-800">Terjadi Kesalahan!</p>
  <p class="text-xs text-gray-500 mt-0.5 leading-relaxed">{{ session('error') }}</p>
                  </div>
              @else
  <div class="w-10 h-10 rounded-xl bg-red-50 flex items-center justify-center flex-shrink-0
  text-red-500 text-xl">
                      <i class="fa fa-exclamation-circle"></i>
                  </div>
                  <div class="flex-1 min-w-0">
                      <p class="text-sm font-bold text-gray-800">Terjadi Kesalahan!</p>
  <ul class="text-xs text-gray-500 mt-0.5 leading-relaxed list-disc ml-4 space-y-0.5">
                          @foreach ($errors->all() as $error)
                              <li>{{ $error }}</li>
                          @endforeach
                      </ul>
                  </div>
              @endif
              <button onclick="closeAlert()"
  class="text-gray-400 hover:text-gray-600 transition-colors text-lg leading-none mt-0.5
  flex-shrink-0">
                  <i class="fa fa-times"></i>
              </button>
          </div>
      </div>
  @endif
  
  
  <style>
      @keyframes slideUp {
          from { opacity: 0; transform: translateY(16px); }
                          }
  </style>
  
  <script>
      // ── SWITCH TAB ─────────────────────────────────────────
      function switchTab(tab) {
          document.getElementById('content-efaktur').classList.add('hidden');
          document.getElementById('content-bupot').classList.add('hidden');
          document.querySelectorAll('.tab-btn').forEach(btn => {
              btn.classList.remove('border-blue-600', 'text-blue-600');
              btn.classList.add('border-transparent', 'text-gray-400');
          });
          document.getElementById('content-' + tab).classList.remove('hidden');
          const active = document.getElementById('tab-' + tab);
          active.classList.add('border-blue-600', 'text-blue-600');
          active.classList.remove('border-transparent', 'text-gray-400');
      }
  
      // ── MODAL TAMBAH EFAKTUR ───────────────────────────────
      function openModalTambah() {
          var m = document.getElementById('modalTambahEfaktur');
          m.classList.remove('hidden'); m.classList.add('flex');
      }
      function closeModalTambah() {
          var m = document.getElementById('modalTambahEfaktur');
          m.classList.add('hidden'); m.classList.remove('flex');
      }
      document.getElementById('modalTambahEfaktur').addEventListener('click', function(e) {
          if (e.target === this) closeModalTambah();
      });
  
      // ── MODAL EDIT EFAKTUR ─────────────────────────────────
      function openEditModalEfaktur(id, nomor, tanggal, tipe, npwp, nama, dpp, ppn, ppnbm,
  status, fileUrl) {
          var m = document.getElementById('modalEditEfaktur');
          m.classList.remove('hidden'); m.classList.add('flex');
  
          document.getElementById('formEditEfaktur').action = '/admin/efaktur/' + id;
          document.getElementById('ef_nomor_faktur').value = nomor;
          document.getElementById('ef_tanggal_faktur').value = tanggal;
          document.getElementById('ef_tipe').value = tipe;
          document.getElementById('ef_npwp_lawan').value = npwp;
          document.getElementById('ef_nama_lawan').value = nama;
          document.getElementById('ef_dpp').value = dpp;
          document.getElementById('ef_ppn').value = ppn;
          document.getElementById('ef_ppnbm').value = ppnbm;
  
          var statusEl = document.getElementById('ef_status');
          var normalized = (status || '').trim();
          statusEl.value = [...statusEl.options].some(o => o.value === normalized) ? normalized
  : 'Pending';
  
          document.getElementById('file_faktur_edit').value = '';
          var preview = document.getElementById('previewFakturEdit');
          preview.innerHTML = '';
  
          if (fileUrl) {
              preview.classList.remove('hidden');
              var ext = fileUrl.split('.').pop().toLowerCase();
              if (['jpg','jpeg','png','webp'].includes(ext)) {
                  preview.innerHTML = `
                      <div class="relative inline-block">
                          <img src="${fileUrl}" class="w-32 h-32 object-cover rounded-lg border
  shadow-sm">
                      </div>
                      <p class="text-[11px] text-gray-400 mt-1">File saat ini — upload baru
  untuk mengganti</p>`;
              } else {
                  var fileName = fileUrl.split('/').pop();
                  preview.innerHTML = `
                      <div class="inline-flex items-center gap-2 px-3 py-2 border rounded-lg
  bg-gray-50">
                          <i class="fa-solid fa-file-pdf text-red-500 text-lg"></i>
                          <a href="${fileUrl}" target="_blank" class="text-xs text-blue-600
  underline">${fileName}</a>
                      </div>`;
              }
          } else {
              preview.classList.add('hidden');
          }
      }
      function closeEditModalEfaktur() {
          var m = document.getElementById('modalEditEfaktur');
          m.classList.add('hidden'); m.classList.remove('flex');
      }
      document.getElementById('modalEditEfaktur').addEventListener('click', function(e) {
          if (e.target === this) closeEditModalEfaktur();
      });
  
      // ── MODAL TAMBAH BUPOT ─────────────────────────────────
      function openModalTambahBupot() {
          var m = document.getElementById('modalTambahBupot');
          m.classList.remove('hidden'); m.classList.add('flex');
      }
      function closeModalTambahBupot() {
          var m = document.getElementById('modalTambahBupot');
          m.classList.add('hidden'); m.classList.remove('flex');
      }
      document.getElementById('modalTambahBupot').addEventListener('click', function(e) {
          if (e.target === this) closeModalTambahBupot();
      });
  
      // ── MODAL EDIT BUPOT ───────────────────────────────────
      function openEditModalBupot(id, nomor, tanggal, tipe, npwp_pemotong, nama_pemotong,
  npwp_dipotong, nama_dipotong, bruto, tarif, status) {
          var m = document.getElementById('modalEditBupot');
          m.classList.remove('hidden'); m.classList.add('flex');
  
          document.getElementById('formEditBupot').action = '/admin/bupot/' + id;
          document.getElementById('bp_nomor_bukti').value = nomor;
          document.getElementById('bp_tanggal_bukti').value = tanggal;
          document.getElementById('bp_tipe').value = tipe;
          document.getElementById('bp_npwp_pemotong').value = npwp_pemotong;
          document.getElementById('bp_nama_pemotong').value = nama_pemotong;
          document.getElementById('bp_npwp_dipotong').value = npwp_dipotong;
          document.getElementById('bp_nama_dipotong').value = nama_dipotong;
          document.getElementById('bp_jumlah_bruto').value = bruto;
          document.getElementById('bp_tarif_pajak').value = tarif;
          document.getElementById('bp_status').value = status;
  
          document.getElementById('file_bupot_edit').value = '';
          document.getElementById('previewBupotEdit').innerHTML = '';
          document.getElementById('previewBupotEdit').classList.add('hidden');
      }
      function closeEditModalBupot() {
          var m = document.getElementById('modalEditBupot');
          m.classList.add('hidden'); m.classList.remove('flex');
      }
      document.getElementById('modalEditBupot').addEventListener('click', function(e) {
          if (e.target === this) closeEditModalBupot();
      });
  
      // ── ESCAPE KEY ─────────────────────────────────────────
      document.addEventListener('keydown', function(e) {
          if (e.key === 'Escape') {
              closeModalTambah();
              closeEditModalEfaktur();
              closeModalTambahBupot();
              closeEditModalBupot();
          }
      });
  
      // ── SEARCH EFAKTUR ─────────────────────────────────────
      function filterTableEfaktur(q) {
          var num = 0;
          document.querySelectorAll('#tableBodyEfaktur tr[data-search]').forEach(function(row) {
              var show = row.dataset.search.includes(q.toLowerCase());
              row.style.display = show ? '' : 'none';
              if (show) { num++; row.querySelector('td:first-child').textContent = num; }
          });
          const query = '?search=' + encodeURIComponent(q);
          document.getElementById('pdfLinkEfaktur').href = "{{ route('efaktur.pdf') }}" + query;
          document.getElementById('excelLinkEfaktur').href = "{{ route('efaktur.export.excel')
  }}" + query;
      }
  
      // ── SEARCH BUPOT ───────────────────────────────────────
      function filterTableBupot(q) {
          var num = 0;
          document.querySelectorAll('#tableBodyBupot tr[data-search]').forEach(function(row) {
              var show = row.dataset.search.includes(q.toLowerCase());
              row.style.display = show ? '' : 'none';
              if (show) { num++; row.querySelector('td:first-child').textContent = num; }
          });
          const query = '?search=' + encodeURIComponent(q);
          document.getElementById('pdfLinkBupot').href = "{{ route('bupot.pdf') }}" + query;
          document.getElementById('excelLinkBupot').href = "{{ route('bupot.export.excel') }}" +
  query;
      }
  
      // ── PREVIEW FILE (UNIVERSAL) ───────────────────────────
      function previewFile(event, previewId, inputId) {
          const file = event.target.files[0];
          const preview = document.getElementById(previewId);
          if (!file) return;
  
          preview.classList.remove('hidden');
          preview.innerHTML = '';
          const ext = file.name.split('.').pop().toLowerCase();
  
          if (['jpg','jpeg','png','webp'].includes(ext)) {
              const reader = new FileReader();
              reader.onload = function(e) {
                  preview.innerHTML = `
                      <div class="relative inline-block">
                          <img src="${e.target.result}" class="w-32 h-32 object-cover rounded-lg
  border shadow-sm">
                          <button type="button"
  onclick="removeFile('${inputId}','${previewId}')"
                              class="absolute -top-2 -right-2 bg-red-500 text-white w-6 h-6
  rounded-full text-xs">×</button>
                      </div>`;
              };
              reader.readAsDataURL(file);
          } else {
              let icon = ext === 'pdf' ? 'fa-file-pdf text-red-500'
                       : (ext === 'doc' || ext === 'docx') ? 'fa-file-word text-blue-500'
                       : 'fa-file text-gray-500';
              preview.innerHTML = `
                  <div class="inline-flex items-center gap-2 px-3 py-2 border rounded-lg
  bg-gray-50">
                      <i class="fa-solid ${icon} text-lg"></i>
                      <span class="text-xs text-gray-600">${file.name}</span>
                      <button type="button" onclick="removeFile('${inputId}','${previewId}')"
                          class="ml-2 text-red-500 text-sm">×</button>
                  </div>`;
          }
      }
  
      function removeFile(inputId, previewId) {
          document.getElementById(inputId).value = '';
          document.getElementById(previewId).innerHTML = '';
          document.getElementById(previewId).classList.add('hidden');
      }
  
      // ── POPUP ALERT ────────────────────────────────────────
      (function() {
          var overlay = document.getElementById('alertOverlay');
          var box = document.getElementById('alertBox');
          if (!overlay) return;
          setTimeout(function() {
              overlay.style.opacity = '1';
              overlay.style.pointerEvents = 'auto';
              box.style.transform = 'translateY(0)';
          }, 80);
          var timer = setTimeout(closeAlert, 4500);
          overlay.addEventListener('click', function(e) { if (e.target === overlay)
  closeAlert(); });
          function closeAlert() {
              clearTimeout(timer);
              overlay.style.opacity = '0';
              overlay.style.pointerEvents = 'none';
              box.style.transform = 'translateY(-16px)';
          }
          window.closeAlert = closeAlert;
      })();
  
      // ── PERTAHANKAN TAB AKTIF SETELAH SUBMIT ───────────────
      (function() {
          const lastTab = sessionStorage.getItem('activeTab');
          if (lastTab) switchTab(lastTab);
  
          document.querySelectorAll('.tab-btn').forEach(btn => {
              btn.addEventListener('click', function() {
                  const tab = this.id.replace('tab-', '');
                  sessionStorage.setItem('activeTab', tab);
              });
          });
      })();
  </script>
  
  @endsection