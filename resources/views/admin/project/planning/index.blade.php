@extends('admin.layouts.app')
@section('title', 'Project Planning')
@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Project Planning</h1>
            <p class="text-sm text-gray-500 mt-0.5">Kelola tahapan perencanaan proyek</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('project.planning.pdf') }}" target="_blank"
                class="inline-flex items-center gap-2 bg-red-500 hover:bg-red-600 text-white text-sm font-medium px-4 py-2.5 rounded-xl shadow-sm transition-colors">
                <i class="fa fa-file-pdf text-sm"></i> Export PDF
            </a>
            <button onclick="openModal()"
                class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2.5 rounded-xl shadow-sm transition-colors">
                <i class="fa fa-plus text-sm"></i> Tambah Planning
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center text-blue-500 text-lg flex-shrink-0"><i class="fa fa-list-check"></i></div>
            <div><p class="text-xl font-bold text-gray-800 leading-none">{{ $total }}</p><p class="text-xs text-gray-500 mt-1">Total Tahapan</p></div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-green-50 flex items-center justify-center text-green-500 text-lg flex-shrink-0"><i class="fa fa-circle-check"></i></div>
            <div><p class="text-xl font-bold text-gray-800 leading-none">{{ $totalSelesai }}</p><p class="text-xs text-gray-500 mt-1">Selesai</p></div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-indigo-50 flex items-center justify-center text-indigo-500 text-lg flex-shrink-0"><i class="fa fa-spinner"></i></div>
            <div><p class="text-xl font-bold text-gray-800 leading-none">{{ $totalBerjalan }}</p><p class="text-xs text-gray-500 mt-1">Berjalan</p></div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-yellow-50 flex items-center justify-center text-yellow-500 text-lg flex-shrink-0"><i class="fa fa-clock"></i></div>
            <div><p class="text-xl font-bold text-gray-800 leading-none">{{ $totalPlan }}</p><p class="text-xs text-gray-500 mt-1">Plan</p></div>
        </div>
    </div>

    @if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl text-sm flex items-center gap-2">
        <i class="fa fa-circle-check"></i> {{ session('success') }}
    </div>
    @endif

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 px-5 py-4 border-b border-gray-100">
            <div><h2 class="font-semibold text-gray-800 text-base">Daftar Project Planning</h2><p class="text-xs text-gray-400 mt-0.5">{{ $data->count() }} total data</p></div>
            <div class="relative">
                <i class="fa fa-search absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                <input type="text" placeholder="Cari..." oninput="onSearchInput(this.value)" class="pl-8 pr-3 py-1.5 text-xs border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400 w-44">
            </div>
        </div>
        <div class="flex items-center gap-2 px-5 py-3 border-b border-gray-100 text-xs text-gray-500">
            <span>Show</span>
            <select onchange="onPerPageChange(this.value)" class="border border-gray-200 rounded-lg px-2 py-1 text-xs focus:outline-none">
                <option value="5">5</option><option value="10" selected>10</option><option value="25">25</option><option value="all">All</option>
            </select>
            <span>entries</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100">
                        <th class="text-left text-xs font-semibold uppercase text-gray-500 px-4 py-3">No</th>
                        <th class="text-left text-xs font-semibold uppercase text-gray-500 px-4 py-3">Kode Proyek</th>
                        <th class="text-left text-xs font-semibold uppercase text-gray-500 px-4 py-3">Tahapan</th>
                        <th class="text-left text-xs font-semibold uppercase text-gray-500 px-4 py-3">Tgl Mulai</th>
                        <th class="text-left text-xs font-semibold uppercase text-gray-500 px-4 py-3">Tgl Selesai</th>
                        <th class="text-center text-xs font-semibold uppercase text-gray-500 px-4 py-3">Durasi</th>
                        <th class="text-left text-xs font-semibold uppercase text-gray-500 px-4 py-3">PIC</th>
                        <th class="text-left text-xs font-semibold uppercase text-gray-500 px-4 py-3">Status</th>
                        <th class="text-center text-xs font-semibold uppercase text-gray-500 px-4 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody id="tableBody">
                    @forelse($data as $d)
                    <tr class="border-t border-gray-50 hover:bg-gray-50 transition-colors"
                        data-search="{{ strtolower($d->kode_proyek.' '.$d->tahapan.' '.$d->pic.' '.$d->status) }}">
                        <td class="px-4 py-3.5 text-gray-400 text-xs">{{ $loop->iteration }}</td>
                        <td class="px-4 py-3.5 text-xs font-mono text-blue-600 font-semibold">{{ $d->kode_proyek }}</td>
                        <td class="px-4 py-3.5 font-semibold text-gray-800 text-xs">{{ $d->tahapan }}</td>
                        <td class="px-4 py-3.5 text-xs text-gray-500">{{ \Carbon\Carbon::parse($d->tgl_mulai)->format('d M Y') }}</td>
                        <td class="px-4 py-3.5 text-xs text-gray-500">{{ \Carbon\Carbon::parse($d->tgl_selesai)->format('d M Y') }}</td>
                        <td class="px-4 py-3.5 text-center text-xs text-gray-700">{{ $d->durasi }} hari</td>
                        <td class="px-4 py-3.5 text-xs text-gray-700">{{ $d->pic }}</td>
                        <td class="px-4 py-3.5">
                            @php $sc = match($d->status) { 'Selesai'=>'bg-green-100 text-green-700','Berjalan'=>'bg-indigo-100 text-indigo-700','Plan'=>'bg-yellow-100 text-yellow-700', default=>'bg-gray-100 text-gray-600' }; @endphp
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium {{ $sc }}"><i class="fa fa-circle text-[6px]"></i> {{ $d->status }}</span>
                        </td>
                        <td class="px-4 py-3.5">
                            <div class="flex items-center justify-center gap-1.5">
                                <button onclick="openEditModal({{ $d->id }})" class="w-7 h-7 rounded-lg bg-yellow-50 hover:bg-yellow-100 text-yellow-600 flex items-center justify-center transition-colors"><i class="fa fa-pen text-xs"></i></button>
                                <form action="{{ route('project.planning.destroy', $d->id) }}" method="POST" onsubmit="return confirm('Hapus data ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="w-7 h-7 rounded-lg bg-red-50 hover:bg-red-100 text-red-500 flex items-center justify-center transition-colors"><i class="fa fa-trash text-xs"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="9" class="px-4 py-10 text-center text-gray-400 text-sm"><i class="fa fa-inbox text-2xl mb-2 block"></i> Belum ada data planning</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div id="paginationInfo" class="px-5 py-3 border-t border-gray-100 text-xs text-gray-400"></div>
    </div>
</div>

{{-- MODAL TAMBAH --}}
<div id="modalAdd" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40 backdrop-blur-sm">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg mx-4 overflow-hidden">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <h3 class="font-semibold text-gray-800">Tambah Project Planning</h3>
            <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600"><i class="fa fa-xmark text-lg"></i></button>
        </div>
        <form action="{{ route('project.planning.store') }}" method="POST" class="px-6 py-5 space-y-4">
            @csrf
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Kode Proyek <span class="text-red-500">*</span></label>
                    <select name="kode_proyek" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                        <option value="">-- Pilih --</option>
                        @foreach($proyeks as $kode => $nama)
                        <option value="{{ $kode }}">{{ $kode }} - {{ $nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Tahapan <span class="text-red-500">*</span></label>
                    <input type="text" name="tahapan" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Tgl Mulai <span class="text-red-500">*</span></label>
                    <input type="date" name="tgl_mulai" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Tgl Selesai <span class="text-red-500">*</span></label>
                    <input type="date" name="tgl_selesai" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">PIC <span class="text-red-500">*</span></label>
                    <input type="text" name="pic" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Status <span class="text-red-500">*</span></label>
                    <select name="status" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                        <option value="">-- Pilih --</option>
                        <option value="Plan">Plan</option>
                        <option value="Berjalan">Berjalan</option>
                        <option value="Selesai">Selesai</option>
                    </select>
                </div>
            </div>
            <div class="flex justify-end gap-2 pt-2">
                <button type="button" onclick="closeModal()" class="px-4 py-2 text-sm text-gray-600 border border-gray-200 rounded-lg hover:bg-gray-50">Batal</button>
                <button type="submit" class="px-4 py-2 text-sm bg-blue-600 hover:bg-blue-700 text-white rounded-lg">Simpan</button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL EDIT --}}
<div id="modalEdit" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40 backdrop-blur-sm">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg mx-4 overflow-hidden">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <h3 class="font-semibold text-gray-800">Edit Project Planning</h3>
            <button onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600"><i class="fa fa-xmark text-lg"></i></button>
        </div>
        <form id="editForm" method="POST" class="px-6 py-5 space-y-4">
            @csrf @method('PUT')
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Kode Proyek <span class="text-red-500">*</span></label>
                    <select name="kode_proyek" id="edit_kode_proyek" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                        @foreach($proyeks as $kode => $nama)
                        <option value="{{ $kode }}">{{ $kode }} - {{ $nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Tahapan <span class="text-red-500">*</span></label>
                    <input type="text" name="tahapan" id="edit_tahapan" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Tgl Mulai <span class="text-red-500">*</span></label>
                    <input type="date" name="tgl_mulai" id="edit_tgl_mulai" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Tgl Selesai <span class="text-red-500">*</span></label>
                    <input type="date" name="tgl_selesai" id="edit_tgl_selesai" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">PIC <span class="text-red-500">*</span></label>
                    <input type="text" name="pic" id="edit_pic" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Status <span class="text-red-500">*</span></label>
                    <select name="status" id="edit_status" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                        <option value="Plan">Plan</option>
                        <option value="Berjalan">Berjalan</option>
                        <option value="Selesai">Selesai</option>
                    </select>
                </div>
            </div>
            <div class="flex justify-end gap-2 pt-2">
                <button type="button" onclick="closeEditModal()" class="px-4 py-2 text-sm text-gray-600 border border-gray-200 rounded-lg hover:bg-gray-50">Batal</button>
                <button type="submit" class="px-4 py-2 text-sm bg-yellow-500 hover:bg-yellow-600 text-white rounded-lg">Update</button>
            </div>
        </form>
    </div>
</div>

<script>
function openModal() { document.getElementById('modalAdd').classList.replace('hidden','flex'); }
function closeModal() { document.getElementById('modalAdd').classList.replace('flex','hidden'); }
function openEditModal(id) {
    fetch(`/admin/project/planning/${id}`)
        .then(r => r.json())
        .then(d => {
            document.getElementById('editForm').action = `/admin/project/planning/${id}`;
            document.getElementById('edit_kode_proyek').value = d.kode_proyek;
            document.getElementById('edit_tahapan').value    = d.tahapan;
            document.getElementById('edit_tgl_mulai').value  = d.tgl_mulai;
            document.getElementById('edit_tgl_selesai').value= d.tgl_selesai;
            document.getElementById('edit_pic').value        = d.pic;
            document.getElementById('edit_status').value     = d.status;
            document.getElementById('modalEdit').classList.replace('hidden','flex');
        });
}
function closeEditModal() { document.getElementById('modalEdit').classList.replace('flex','hidden'); }
let perPage = 10;
function onSearchInput(val) { filterTable(val); }
function onPerPageChange(val) { perPage = val === 'all' ? 99999 : parseInt(val); filterTable(document.querySelector('input[placeholder]').value); }
function filterTable(search) {
    const rows = Array.from(document.querySelectorAll('#tableBody tr[data-search]'));
    const q = search.toLowerCase(); let visible = 0;
    rows.forEach(r => { const m = r.dataset.search.includes(q); r.style.display=(m&&visible<perPage)?'':'none'; if(m)visible++; });
    document.getElementById('paginationInfo').textContent=`Menampilkan ${Math.min(visible,perPage)} dari ${rows.filter(r=>r.dataset.search.includes(q)).length} data`;
}
filterTable('');
</script>
@endsection
