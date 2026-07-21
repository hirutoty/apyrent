@extends('admin.layouts.app')
@section('title', 'Sosial Media Performance')
@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Sosial Media Performance</h1>
            <p class="text-sm text-gray-500 mt-0.5">Pantau performa kampanye di sosial media</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('sosmedp.pdf') }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium border border-red-500 text-red-500 rounded-lg bg-transparent hover:bg-red-500 hover:text-white transition-colors">
                <i class="fa fa-file-pdf text-sm"></i> Export PDF
            </a>
            <button onclick="openModal()" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2.5 rounded-xl shadow-sm transition-colors">
                <i class="fa fa-plus text-sm"></i> Tambah Data
            </button>
        </div>
    </div>
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center text-blue-500 text-lg flex-shrink-0"><i class="fa fa-share-nodes"></i></div>
            <div><p class="text-xl font-bold text-gray-800 leading-none">{{ $total }}</p><p class="text-xs text-gray-500 mt-1">Total Kampanye</p></div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-green-50 flex items-center justify-center text-green-500 text-lg flex-shrink-0"><i class="fa fa-mouse-pointer"></i></div>
            <div><p class="text-xl font-bold text-gray-800 leading-none">{{ number_format($totalKlik) }}</p><p class="text-xs text-gray-500 mt-1">Total Klik</p></div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-purple-50 flex items-center justify-center text-purple-500 text-lg flex-shrink-0"><i class="fa fa-chart-line"></i></div>
            <div><p class="text-xl font-bold text-gray-800 leading-none">{{ number_format($totalKonversi) }}</p><p class="text-xs text-gray-500 mt-1">Total Konversi</p></div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-yellow-50 flex items-center justify-center text-yellow-500 text-lg flex-shrink-0"><i class="fa fa-coins"></i></div>
            <div><p class="text-xl font-bold text-gray-800 leading-none text-sm">Rp {{ number_format($totalBiaya,0,',','.') }}</p><p class="text-xs text-gray-500 mt-1">Total Biaya</p></div>
        </div>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 px-5 py-4 border-b border-gray-100">
            <div><h2 class="font-semibold text-gray-800 text-base">Data Sosial Media</h2><p class="text-xs text-gray-400 mt-0.5">{{ $data->count() }} total data</p></div>
            <div class="relative"><i class="fa fa-search absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
            <input type="text" placeholder="Cari kampanye..." oninput="onSearchInput(this.value)" class="pl-8 pr-3 py-1.5 text-xs border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400 w-44"></div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100">
                        <th class="text-left text-xs font-semibold uppercase text-gray-500 px-4 py-3">No</th>
                        <th class="text-left text-xs font-semibold uppercase text-gray-500 px-4 py-3">ID Kampanye</th>
                        <th class="text-left text-xs font-semibold uppercase text-gray-500 px-4 py-3">Channel</th>
                        <th class="text-left text-xs font-semibold uppercase text-gray-500 px-4 py-3">UTM Source</th>
                        <th class="text-left text-xs font-semibold uppercase text-gray-500 px-4 py-3">UTM Campaign</th>
                        <th class="text-right text-xs font-semibold uppercase text-gray-500 px-4 py-3">Klik</th>
                        <th class="text-right text-xs font-semibold uppercase text-gray-500 px-4 py-3">Konversi</th>
                        <th class="text-right text-xs font-semibold uppercase text-gray-500 px-4 py-3">Biaya</th>
                        <th class="text-right text-xs font-semibold uppercase text-gray-500 px-4 py-3">ROI %</th>
                        <th class="text-center text-xs font-semibold uppercase text-gray-500 px-4 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody id="tableBody">
                    @forelse($data as $d)
                    <tr class="border-t border-gray-50 odd:bg-white even:bg-gray-100 hover:bg-blue-50/50 transition-colors">
                        <td class="px-4 py-3.5 text-gray-400">{{ $1->firstItem() + $loop->index }}</td>
                        <td class="px-4 py-3.5 text-xs font-mono text-blue-600">{{ $d->id_kampanye }}</td>
                        <td class="px-4 py-3.5"><span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-700">{{ $d->channel }}</span></td>
                        <td class="px-4 py-3.5 text-xs text-gray-600">{{ $d->utm_source }}</td>
                        <td class="px-4 py-3.5 text-xs text-gray-600">{{ $d->utm_campaign }}</td>
                        <td class="px-4 py-3.5 text-right font-semibold text-gray-800">{{ number_format($d->klik) }}</td>
                        <td class="px-4 py-3.5 text-right font-semibold text-green-600">{{ number_format($d->konversi) }}</td>
                        <td class="px-4 py-3.5 text-right text-xs text-gray-600">Rp {{ number_format($d->total_biaya,0,',','.') }}</td>
                        <td class="px-4 py-3.5 text-right">
                            <span class="font-semibold {{ $d->roi >= 100 ? 'text-green-600' : 'text-red-500' }}">{{ number_format($d->roi,1) }}%</span>
                        </td>
                        <td class="px-4 py-3.5">
                            <div class="flex items-center justify-center gap-1.5">
                                <button class="inline-flex items-center gap-1 px-3 py-1 rounded-lg text-xs font-medium bg-yellow-100 text-yellow-600 hover:bg-yellow-200 transition-colors"
                                    data-action="{{ route('sosmedp.update', $d->id) }}" data-id_kampanye="{{ $d->id_kampanye }}"
                                    data-channel="{{ $d->channel }}" data-utm_source="{{ $d->utm_source }}"
                                    data-utm_campaign="{{ $d->utm_campaign }}" data-klik="{{ $d->klik }}"
                                    data-konversi="{{ $d->konversi }}" data-total_biaya="{{ $d->total_biaya }}"
                                    data-total_penjualan="{{ $d->total_penjualan }}" data-roi="{{ $d->roi }}"
                                    onclick="triggerEdit(this)"><i class="fa fa-edit text-xs"></i> Edit</button>
                                <button type="button" class="inline-flex items-center gap-1 px-3 py-1 rounded-lg text-xs font-medium bg-red-100 text-red-600 hover:bg-red-200 transition-colors"
                                    data-action="{{ route('sosmedp.destroy', $d->id) }}" data-name="{{ $d->id_kampanye }}" onclick="triggerDelete(this)">
                                    <i class="fa fa-trash text-xs"></i> Hapus</button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="10" class="px-5 py-12 text-center"><p class="text-sm text-gray-400">Belum ada data</p></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="py-3 border-t border-gray-100">{{ $data->links() }}</div>
    </div>
</div>
<div id="mainModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/30" style="backdrop-filter:blur(2px)">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-2xl mx-4 max-h-[90vh] overflow-y-auto">
        <div class="flex items-start justify-between px-6 py-5 border-b border-gray-100 sticky top-0 bg-white">
            <h2 id="modalTitle" class="text-base font-bold text-gray-800">Tambah Data Sosial Media</h2>
            <button onclick="closeModal()" class="text-gray-400 hover:text-red-500 text-lg"><i class="fa fa-times"></i></button>
        </div>
        <form id="mainForm" action="{{ route('sosmedp.store') }}" method="POST" class="px-6 py-5 space-y-4">
            @csrf<div id="methodContainer"></div>
            <div class="grid grid-cols-2 gap-3">
                <div><label class="block text-xs font-semibold text-gray-600 mb-1.5">ID Kampanye <span class="text-red-500">*</span></label>
                <input type="text" name="id_kampanye" id="f_id_kampanye" required placeholder="SMP001" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400" value="{{ old('id_kampanye') }}"</div>
                <div><label class="block text-xs font-semibold text-gray-600 mb-1.5">Channel <span class="text-red-500">*</span></label>
                <select name="channel" id="f_channel" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                    <option value="">- Pilih -</option><option>Instagram</option><option>Facebook</option><option>TikTok</option><option>Twitter/X</option><option>YouTube</option>
                </select></div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div><label class="block text-xs font-semibold text-gray-600 mb-1.5">UTM Source <span class="text-red-500">*</span></label>
                <input type="text" name="utm_source" id="f_utm_source" required placeholder="instagram" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400" value="{{ old('utm_source') }}"</div>
                <div><label class="block text-xs font-semibold text-gray-600 mb-1.5">UTM Campaign <span class="text-red-500">*</span></label>
                <input type="text" name="utm_campaign" id="f_utm_campaign" required placeholder="promo_july_2026" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400" value="{{ old('utm_campaign') }}"</div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div><label class="block text-xs font-semibold text-gray-600 mb-1.5">Klik <span class="text-red-500">*</span></label>
                <input type="number" name="klik" id="f_klik" required min="0" placeholder="0" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400" value="{{ old('klik') }}"</div>
                <div><label class="block text-xs font-semibold text-gray-600 mb-1.5">Konversi <span class="text-red-500">*</span></label>
                <input type="number" name="konversi" id="f_konversi" required min="0" placeholder="0" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400" value="{{ old('konversi') }}"</div>
            </div>
            <div class="grid grid-cols-3 gap-3">
                <div><label class="block text-xs font-semibold text-gray-600 mb-1.5">Total Biaya <span class="text-red-500">*</span></label>
                <input type="number" name="total_biaya" id="f_total_biaya" required min="0" step="0.01" placeholder="0" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400" value="{{ old('total_biaya') }}"</div>
                <div><label class="block text-xs font-semibold text-gray-600 mb-1.5">Total Penjualan <span class="text-red-500">*</span></label>
                <input type="number" name="total_penjualan" id="f_total_penjualan" required min="0" step="0.01" placeholder="0" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400" value="{{ old('total_penjualan') }}"</div>
                <div><label class="block text-xs font-semibold text-gray-600 mb-1.5">ROI (%) <span class="text-red-500">*</span></label>
                <input type="number" name="roi" id="f_roi" required step="0.01" placeholder="0.00" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400" value="{{ old('roi') }}"</div>
            </div>
            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold py-2.5 rounded-xl transition-colors flex items-center justify-center gap-2"><i class="fa fa-save"></i> Simpan</button>
        </form>
    </div>
</div>
<div id="deleteModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/30" style="backdrop-filter:blur(2px)">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm mx-4 px-6 py-6 text-center">
        <div class="w-14 h-14 rounded-full bg-red-50 flex items-center justify-center mx-auto text-red-500 text-2xl mb-4"><i class="fa fa-triangle-exclamation"></i></div>
        <h2 class="text-base font-bold text-gray-800">Hapus Data?</h2>
        <p class="text-xs text-gray-500 mt-1.5 mb-4">Hapus data kampanye <strong id="deleteName"></strong>?</p>
        <form id="deleteForm" action="" method="POST" class="flex items-center gap-2">
            @csrf @method('DELETE')
            <button type="button" onclick="closeDeleteModal()" class="flex-1 text-sm text-gray-600 border border-gray-200 rounded-xl py-2.5 hover:bg-gray-50">Batal</button>
            <button type="submit" class="flex-1 inline-flex items-center justify-center gap-2 text-sm font-semibold text-white bg-red-600 hover:bg-red-700 rounded-xl py-2.5"><i class="fa fa-trash text-xs"></i> Hapus</button>
        </form>
    </div>
</div>
@if(session('success')||$errors->any())
<div id="alertOverlay" class="fixed inset-0 z-[9999] flex items-start justify-center pt-6" style="background:rgba(0,0,0,0.18);opacity:0;transition:opacity 0.2s;pointer-events:none">
    <div id="alertBox" class="bg-white rounded-xl shadow-xl border border-gray-100 px-5 py-4 flex items-start gap-3 w-full max-w-md mx-4" style="transform:translateY(-16px);transition:transform 0.25s">
        @if(session('success'))<div class="w-10 h-10 rounded-xl bg-green-50 flex items-center justify-center flex-shrink-0 text-green-600 text-xl"><i class="fa fa-check-circle"></i></div><div class="flex-1"><p class="text-sm font-bold">Berhasil!</p><p class="text-xs text-gray-500">{{ session('success') }}</p></div>
        @else<div class="w-10 h-10 rounded-xl bg-red-50 flex items-center justify-center flex-shrink-0 text-red-500 text-xl"><i class="fa fa-exclamation-circle"></i></div><div class="flex-1"><p class="text-sm font-bold">Error!</p><ul class="text-xs text-gray-500 list-disc ml-4">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>@endif
        <button onclick="closeAlert()" class="text-gray-400 hover:text-gray-600 text-lg"><i class="fa fa-times"></i></button>
    </div>
</div>
@endif
<script>
const mainModal=document.getElementById('mainModal'),mainForm=document.getElementById('mainForm'),methodContainer=document.getElementById('methodContainer'),createUrl="{{ route('sosmedp.store') }}";
function openModal(){document.getElementById('modalTitle').innerText='Tambah Data Sosial Media';mainForm.action=createUrl;methodContainer.innerHTML='';mainForm.reset();mainModal.classList.remove('hidden');mainModal.classList.add('flex');}
function closeModal(){mainModal.classList.add('hidden');mainModal.classList.remove('flex');}
mainModal.addEventListener('click',e=>{if(e.target===mainModal)closeModal();});
function triggerEdit(btn){document.getElementById('modalTitle').innerText='Edit Data';mainForm.action=btn.dataset.action;methodContainer.innerHTML='<input type="hidden" name="_method" value="PUT">';
['id_kampanye','channel','utm_source','utm_campaign','klik','konversi','total_biaya','total_penjualan','roi'].forEach(k=>{const el=document.getElementById('f_'+k);if(el)el.value=btn.dataset[k]??'';});
mainModal.classList.remove('hidden');mainModal.classList.add('flex');}
const deleteModal=document.getElementById('deleteModal'),deleteForm=document.getElementById('deleteForm');
function triggerDelete(btn){deleteForm.action=btn.dataset.action;document.getElementById('deleteName').innerText=btn.dataset.name;deleteModal.classList.remove('hidden');deleteModal.classList.add('flex');}
function closeDeleteModal(){deleteModal.classList.add('hidden');deleteModal.classList.remove('flex');}
deleteModal.addEventListener('click',e=>{if(e.target===deleteModal)closeDeleteModal();});

(function(){var o=document.getElementById('alertOverlay'),b=document.getElementById('alertBox');if(!o)return;setTimeout(()=>{o.style.opacity='1';o.style.pointerEvents='auto';b.style.transform='translateY(0)';},80);var t=setTimeout(closeAlert,4500);function closeAlert(){clearTimeout(t);o.style.opacity='0';o.style.pointerEvents='none';b.style.transform='translateY(-16px)';}window.closeAlert=closeAlert;})();

        // Auto-reopen modal tambah on validation error
        @if ($errors->any() && !session('success'))
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof openModalTambah === 'function') {
                openModalTambah();
            } else if (typeof openModal === 'function') {
                openModal();
            }
        });
        @endif
</script>
@endsection
