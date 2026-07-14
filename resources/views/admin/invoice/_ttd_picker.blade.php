{{--
    TTD Picker Component
    Props:
    - $uid    : unique ID prefix (misal: tambah_staff, edit_direktur)
    - $field  : nama field (ttd_staff atau ttd_direktur)
--}}
<div class="ttd-picker" data-uid="{{ $uid }}" data-field="{{ $field }}">

    {{-- Tab Switch --}}
    <div class="flex rounded-lg overflow-hidden border border-gray-200 mb-2 text-xs font-semibold">
        <button type="button"
            class="ttd-tab-lib flex-1 py-1.5 bg-blue-600 text-white transition-colors"
            data-target="lib">
            <i class="fa fa-images mr-1"></i> Pilih Library
        </button>
        <button type="button"
            class="ttd-tab-upload flex-1 py-1.5 bg-white text-gray-500 odd:bg-white even:bg-gray-100 hover:bg-blue-50/50 transition-colors"
            data-target="upload">
            <i class="fa fa-upload mr-1"></i> Upload Baru
        </button>
    </div>

    {{-- Panel: Library --}}
    <div class="ttd-panel-lib">
        <div class="ttd-library-grid grid grid-cols-3 gap-2 max-h-40 overflow-y-auto p-1">
            <div class="col-span-3 text-center text-gray-400 text-xs py-3 ttd-loading">
                <i class="fa fa-spinner fa-spin mr-1"></i> Memuat...
            </div>
        </div>
    </div>

    {{-- Panel: Upload --}}
    <div class="ttd-panel-upload hidden">
        <input type="file" name="{{ $field }}" accept="image/*"
            class="ttd-file-input w-full border border-gray-200 rounded-lg px-3 py-2 text-sm bg-white">
        <p class="text-xs text-gray-400 mt-1">
            <i class="fa fa-magic mr-1 text-purple-400"></i>
            Background otomatis dihapus saat upload
        </p>
    </div>

    {{-- Preview terpilih --}}
    <div class="ttd-preview mt-2 hidden">
        <div class="flex items-center gap-2 bg-blue-50 border border-blue-200 rounded-lg px-3 py-2">
            <img class="ttd-preview-img h-10 rounded" src="" alt="TTD">
            <span class="text-xs text-blue-700 flex-1">TTD terpilih</span>
            <button type="button" class="ttd-clear text-gray-400 hover:text-red-500 text-xs">
                <i class="fa fa-times"></i>
            </button>
        </div>
    </div>

</div>
