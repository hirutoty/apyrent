{{--
    Approval Modal Component for Pengeluaran Kendaraan
    GPS: per-item approve/reject dengan bukti & catatan masing-masing
    Non-GPS: approve/reject global (existing flow)
    Usage: <x-approval-modal />
--}}

<div
    x-data="approvalModal()"
    x-show="show"
    x-cloak
    @open-approval-modal.window="openModal($event.detail)"
    @keydown.escape.window="closeModal()"
    class="fixed inset-0 z-[9999] overflow-y-auto"
    style="display: none;"
>
    {{-- Backdrop --}}
    <div
        x-show="show"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-gray-900/75 backdrop-blur-sm transition-opacity"
        @click="closeModal()"
    ></div>

    {{-- Modal Content --}}
    <div class="flex min-h-screen items-center justify-center p-4">
        <div
            x-show="show"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            class="relative bg-white rounded-2xl shadow-2xl max-w-4xl w-full max-h-[90vh] overflow-hidden"
            @click.stop
        >
            {{-- Header --}}
            <div class="sticky top-0 bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4 flex items-center justify-between border-b border-blue-500/20 z-10">
                <div>
                    <h3 class="text-xl font-bold text-white">Approval Pengeluaran</h3>
                    <p class="text-sm text-blue-100 mt-0.5" x-text="'No PR: ' + (data?.no_pr || '-')"></p>
                </div>
                <button
                    @click="closeModal()"
                    class="text-white/80 hover:text-white hover:bg-white/10 rounded-lg p-2 transition-colors"
                >
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            {{-- Loading state --}}
            <div x-show="loading" class="flex items-center justify-center py-20">
                <div class="flex flex-col items-center gap-3">
                    <svg class="animate-spin h-8 w-8 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <p class="text-sm text-gray-500">Memuat data...</p>
                </div>
            </div>

            {{-- Body --}}
            <div x-show="!loading" class="overflow-y-auto max-h-[calc(90vh-130px)] px-6 py-6 space-y-5">

                {{-- Section 1: Info Pengeluaran --}}
                <div class="bg-gray-50 rounded-xl p-5 border border-gray-200">
                    <h4 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
                        <i class="bi bi-info-circle text-blue-600"></i>
                        Detail Pengeluaran
                    </h4>

                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <p class="text-gray-500 mb-1">Jenis Pengeluaran</p>
                            <p class="font-semibold text-gray-800" x-text="data?.source_type_name || '-'"></p>
                        </div>
                        <div>
                            <p class="text-gray-500 mb-1">Total Nominal</p>
                            <p class="font-bold text-green-600" x-text="'Rp ' + formatNumber(data?.nominal || 0)"></p>
                        </div>
                        <div>
                            <p class="text-gray-500 mb-1">Diajukan Oleh</p>
                            <p class="font-semibold text-gray-800" x-text="data?.pemohon || '-'"></p>
                        </div>
                        <div>
                            <p class="text-gray-500 mb-1">Tanggal Pengajuan</p>
                            <p class="font-semibold text-gray-800" x-text="formatDate(data?.tanggal)"></p>
                        </div>
                    </div>

                    {{-- Info kendaraan & keterangan --}}
                    <div x-show="relatedData?.kendaraan" class="mt-4 pt-4 border-t border-gray-200">
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <p class="text-gray-500 mb-1">Kendaraan</p>
                                <p class="font-semibold text-gray-800"
                                   x-text="(relatedData?.kendaraan?.nopol || '-') + ' — ' + (relatedData?.kendaraan?.merk || '')"></p>
                            </div>
                            <div x-show="sourceData?.tanggal_habis">
                                <p class="text-gray-500 mb-1">Berlaku s/d</p>
                                <p class="font-semibold text-gray-800" x-text="formatDate(sourceData?.tanggal_habis)"></p>
                            </div>
                        </div>
                    </div>

                    <div x-show="sourceData?.keterangan" class="mt-3 pt-3 border-t border-gray-200">
                        <p class="text-gray-500 text-xs mb-1">Keterangan</p>
                        <p class="text-gray-700 text-sm" x-text="sourceData?.keterangan"></p>
                    </div>
                </div>

                {{-- ============================================================
                     GPS: TABEL PER-ITEM dengan Approve/Reject inline
                ============================================================ --}}
                <div x-show="data?.source_type === 'gps' || data?.source_type === 'gps_perpanjang'">
                    <div class="flex items-center justify-between mb-3">
                        <h4 class="font-bold text-gray-800 flex items-center gap-2 text-sm">
                            <i class="fa-solid fa-satellite-dish text-purple-600"></i>
                            GPS Items — Tentukan keputusan per item
                        </h4>
                        {{-- Progress badge --}}
                        <span class="text-xs px-2.5 py-1 rounded-full bg-blue-50 text-blue-700 font-semibold"
                              x-text="decidedCount() + ' / ' + (relatedData?.gps_items?.length || 0) + ' diputuskan'">
                        </span>
                    </div>

                    <div class="space-y-3">
                        <template x-for="(item, idx) in relatedData?.gps_items || []" :key="idx">
                            <div class="border rounded-xl overflow-hidden transition-all"
                                 :class="{
                                     'border-green-300 bg-green-50/30' : itemDecisions[idx]?.action === 'approved',
                                     'border-red-300 bg-red-50/30'    : itemDecisions[idx]?.action === 'rejected',
                                     'border-gray-200 bg-white'       : !itemDecisions[idx]?.action
                                 }">

                                {{-- Row utama --}}
                                <div class="flex items-start gap-3 px-4 py-3">

                                    {{-- Status badge kiri --}}
                                    <div class="flex-shrink-0 mt-0.5">
                                        <span x-show="!itemDecisions[idx]?.action"
                                              class="inline-flex items-center gap-1 text-[10px] font-semibold px-2 py-0.5 rounded-full bg-gray-100 text-gray-500">
                                            <i class="fa-solid fa-circle text-[6px]"></i> Pending
                                        </span>
                                        <span x-show="itemDecisions[idx]?.action === 'approved'"
                                              class="inline-flex items-center gap-1 text-[10px] font-semibold px-2 py-0.5 rounded-full bg-green-100 text-green-700">
                                            <i class="fa-solid fa-circle-check text-[10px]"></i> Approved
                                        </span>
                                        <span x-show="itemDecisions[idx]?.action === 'rejected'"
                                              class="inline-flex items-center gap-1 text-[10px] font-semibold px-2 py-0.5 rounded-full bg-red-100 text-red-700">
                                            <i class="fa-solid fa-circle-xmark text-[10px]"></i> Rejected
                                        </span>
                                    </div>

                                    {{-- Info GPS --}}
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <span class="text-xs text-gray-400 font-medium" x-text="'#' + (idx + 1)"></span>
                                            <span class="font-semibold text-gray-800 text-sm" x-text="item.nama_gps"></span>
                                            <span class="text-xs text-gray-500 bg-gray-100 px-1.5 py-0.5 rounded font-mono" x-text="item.type"></span>
                                            <span class="text-xs font-bold text-emerald-600 ml-auto" x-text="'Rp ' + formatNumber(item.biaya_sewa)"></span>
                                        </div>

                                        {{-- Bank info per item (subtle) --}}
                                        <div x-show="item.nama_bank || item.no_rekening || item.nama_pemilik"
                                             class="mt-1.5 flex flex-wrap items-center gap-x-3 gap-y-0.5">
                                            <span x-show="item.nama_bank"
                                                  class="inline-flex items-center gap-1 text-[11px] text-blue-600">
                                                <i class="fa-solid fa-building-columns text-[9px]"></i>
                                                <span x-text="item.nama_bank"></span>
                                            </span>
                                            <span x-show="item.no_rekening"
                                                  class="text-[11px] font-mono text-gray-500" x-text="item.no_rekening"></span>
                                            <span x-show="item.nama_pemilik"
                                                  class="text-[11px] text-gray-400" x-text="'a/n ' + item.nama_pemilik"></span>
                                        </div>

                                        {{-- File dari pengaju --}}
                                        <div x-show="item.bukti_bayar?.path" class="mt-1.5 flex items-center gap-1.5">
                                            <i class="fa-solid fa-file-arrow-up text-[10px] text-blue-400"></i>
                                            <a :href="'/storage/' + item.bukti_bayar?.path"
                                               target="_blank"
                                               class="text-[11px] text-blue-500 underline hover:text-blue-700"
                                               x-text="item.bukti_bayar?.original_name || 'Lihat bukti pengaju'"></a>
                                        </div>
                                    </div>

                                    {{-- Tombol Aksi --}}
                                    <div class="flex items-center gap-2 flex-shrink-0">
                                        {{-- Approve --}}
                                        <button type="button"
                                                @click="setAction(idx, 'approved')"
                                                :class="itemDecisions[idx]?.action === 'approved'
                                                    ? 'bg-green-600 text-white border-green-600 shadow-sm'
                                                    : 'bg-white text-green-600 border-green-300 hover:bg-green-50'"
                                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold border transition-all">
                                            <i class="fa-solid fa-check text-[10px]"></i> Approve
                                        </button>
                                        {{-- Reject --}}
                                        <button type="button"
                                                @click="setAction(idx, 'rejected')"
                                                :class="itemDecisions[idx]?.action === 'rejected'
                                                    ? 'bg-red-600 text-white border-red-600 shadow-sm'
                                                    : 'bg-white text-red-500 border-red-300 hover:bg-red-50'"
                                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold border transition-all">
                                            <i class="fa-solid fa-times text-[10px]"></i> Reject
                                        </button>
                                    </div>
                                </div>

                                {{-- Panel expand: Upload Bukti (approved) + Catatan (rejected) --}}
                                <div x-show="itemDecisions[idx]?.action"
                                     x-transition:enter="transition ease-out duration-150"
                                     x-transition:enter-start="opacity-0 -translate-y-1"
                                     x-transition:enter-end="opacity-100 translate-y-0"
                                     class="px-4 pb-3 pt-1 border-t"
                                     :class="itemDecisions[idx]?.action === 'approved' ? 'border-green-200' : 'border-red-200'">

                                    <div class="flex flex-col gap-2.5">

                                        {{-- Upload bukti (tampil untuk APPROVE) --}}
                                        <div x-show="itemDecisions[idx]?.action === 'approved'">
                                            <p class="text-[11px] font-semibold text-gray-500 mb-1.5 uppercase tracking-wide">
                                                <i class="fa-solid fa-cloud-arrow-up mr-1 text-green-500"></i>
                                                Upload Bukti Bayar <span class="text-red-500">*</span>
                                            </p>

                                            <div x-show="!itemDecisions[idx]?.buktiFile">
                                                <label :for="'bukti-item-' + idx"
                                                       class="flex items-center gap-2 px-3 py-2 border border-dashed rounded-lg cursor-pointer hover:bg-green-50 transition-colors"
                                                       :class="itemDecisions[idx]?.action === 'approved' && !itemDecisions[idx]?.buktiFile
                                                           ? 'border-red-400 bg-red-50/30'
                                                           : 'border-green-300'">
                                                    <i class="fa-solid fa-paperclip text-xs"
                                                       :class="itemDecisions[idx]?.action === 'approved' && !itemDecisions[idx]?.buktiFile
                                                           ? 'text-red-400' : 'text-green-500'"></i>
                                                    <span class="text-xs"
                                                          :class="itemDecisions[idx]?.action === 'approved' && !itemDecisions[idx]?.buktiFile
                                                              ? 'text-red-500' : 'text-gray-500'">
                                                        Klik untuk pilih file (JPG, PNG, PDF, max 5MB) — Wajib
                                                    </span>
                                                </label>
                                                <input :id="'bukti-item-' + idx"
                                                       type="file"
                                                       accept=".jpg,.jpeg,.png,.pdf,.doc,.docx"
                                                       class="hidden"
                                                       @change="handleBuktiItem($event, idx)">
                                                <p x-show="itemDecisions[idx]?.action === 'approved' && !itemDecisions[idx]?.buktiFile"
                                                   class="text-[10px] text-red-500 mt-0.5">
                                                    <i class="fa-solid fa-circle-exclamation mr-0.5"></i> Bukti pembayaran wajib diupload
                                                </p>
                                            </div>

                                            <div x-show="itemDecisions[idx]?.buktiFile"
                                                 class="flex items-center gap-2 px-3 py-2 bg-green-50 border border-green-200 rounded-lg">
                                                <i class="fa-solid fa-file-check text-green-600 text-xs"></i>
                                                <span class="text-xs text-gray-700 flex-1 truncate"
                                                      x-text="itemDecisions[idx]?.buktiFile?.name"></span>
                                                <button type="button"
                                                        @click="removeItemBukti(idx)"
                                                        class="text-red-400 hover:text-red-600 text-xs p-0.5">
                                                    <i class="fa-solid fa-times"></i>
                                                </button>
                                            </div>
                                        </div>

                                        {{-- Catatan wajib (tampil untuk REJECT) --}}
                                        <div x-show="itemDecisions[idx]?.action === 'rejected'">
                                            <p class="text-[11px] font-semibold text-red-500 mb-1.5 uppercase tracking-wide">
                                                <i class="fa-solid fa-comment-dots mr-1"></i>
                                                Alasan Penolakan <span class="text-red-500">*</span>
                                            </p>
                                            <textarea
                                                :id="'catatan-item-' + idx"
                                                x-model="itemDecisions[idx].catatan"
                                                rows="2"
                                                placeholder="Tulis alasan penolakan item ini..."
                                                class="w-full text-xs px-3 py-2 border rounded-lg resize-none focus:outline-none focus:ring-2 focus:ring-red-300"
                                                :class="itemDecisions[idx]?.action === 'rejected' && !itemDecisions[idx]?.catatan?.trim()
                                                    ? 'border-red-300 bg-red-50'
                                                    : 'border-red-200'"
                                            ></textarea>
                                            <p x-show="itemDecisions[idx]?.action === 'rejected' && !itemDecisions[idx]?.catatan?.trim()"
                                               class="text-[10px] text-red-500 mt-0.5">
                                                <i class="fa-solid fa-circle-exclamation mr-0.5"></i>
                                                Wajib diisi
                                            </p>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>

                    {{-- Ringkasan keputusan --}}
                    <div x-show="decidedCount() > 0"
                         class="mt-3 px-4 py-3 bg-slate-50 rounded-xl border border-slate-200 flex items-center gap-4 text-xs">
                        <span class="text-gray-500">Ringkasan:</span>
                        <span x-show="approvedCount() > 0"
                              class="inline-flex items-center gap-1 font-semibold text-green-700">
                            <i class="fa-solid fa-circle-check"></i>
                            <span x-text="approvedCount() + ' disetujui'"></span>
                        </span>
                        <span x-show="rejectedCount() > 0"
                              class="inline-flex items-center gap-1 font-semibold text-red-600">
                            <i class="fa-solid fa-circle-xmark"></i>
                            <span x-text="rejectedCount() + ' ditolak'"></span>
                        </span>
                        <span class="ml-auto text-gray-400 italic" x-show="approvedCount() > 0 && rejectedCount() > 0">
                            PR akan berstatus "Disetujui Sebagian"
                        </span>
                    </div>
                </div>

                {{-- ============================================================
                     NON-GPS: Approval global (existing flow)
                ============================================================ --}}
                <div x-show="data?.source_type !== 'gps' && data?.source_type !== 'gps_perpanjang'">

                    {{-- Info tambahan non-GPS --}}
                    <div x-show="data?.source_type === 'asuransi_kendaraan'" class="bg-gray-50 rounded-xl p-4 border border-gray-200 text-sm">
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <p class="text-gray-500 mb-1">Perusahaan Asuransi</p>
                                <p class="font-semibold text-gray-800" x-text="relatedData?.asuransi?.nama_asuransi || '-'"></p>
                            </div>
                            <div>
                                <p class="text-gray-500 mb-1">Jenis Asuransi</p>
                                <p class="font-semibold text-gray-800" x-text="relatedData?.jenis_asuransi?.nama_jenis || '-'"></p>
                            </div>
                        </div>
                    </div>

                    <div x-show="data?.source_type === 'pajak'" class="bg-gray-50 rounded-xl p-4 border border-gray-200 text-sm">
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <p class="text-gray-500 mb-1">Jenis Pajak</p>
                                <p class="font-semibold text-gray-800" x-text="sourceData?.jenis_pajak || '-'"></p>
                            </div>
                        </div>
                    </div>

                    {{-- ── PAJAK PERPANJANG: Sebelum vs Sesudah ── --}}
                    <div x-show="data?.source_type === 'pajak_perpanjang'"
                         class="bg-amber-50 rounded-xl p-4 border border-amber-200 text-sm">
                        <h4 class="font-bold text-amber-800 mb-3 flex items-center gap-2 text-sm">
                            <i class="bi bi-arrow-repeat text-amber-600"></i>
                            Perpanjangan Pajak Kendaraan
                        </h4>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="bg-white rounded-lg p-3 border border-amber-200">
                                <p class="text-[10px] font-semibold text-amber-600 uppercase tracking-wide mb-2">Sebelum</p>
                                <div class="space-y-1.5 text-xs">
                                    <div><span class="text-gray-500">Jenis Pajak:</span> <span class="font-medium text-gray-800" x-text="relatedData?.pajak_lama?.jenis_pajak || '-'"></span></div>
                                    <div><span class="text-gray-500">Nominal:</span> <span class="font-medium text-gray-800" x-text="relatedData?.pajak_lama?.nominal ? 'Rp ' + formatNumber(relatedData.pajak_lama.nominal) : '-'"></span></div>
                                    <div><span class="text-gray-500">Jatuh Tempo:</span> <span class="font-medium text-gray-800" x-text="formatDate(relatedData?.pajak_lama?.jatuh_tempo)"></span></div>
                                </div>
                            </div>
                            <div class="bg-white rounded-lg p-3 border border-green-200">
                                <p class="text-[10px] font-semibold text-green-600 uppercase tracking-wide mb-2">Sesudah</p>
                                <div class="space-y-1.5 text-xs">
                                    <div><span class="text-gray-500">Jenis Pajak:</span> <span class="font-medium text-gray-800" x-text="sourceData?.jenis_pajak || relatedData?.pajak_lama?.jenis_pajak || '-'"></span></div>
                                    <div><span class="text-gray-500">Nominal:</span> <span class="font-medium text-green-700" x-text="sourceData?.nominal ? 'Rp ' + formatNumber(sourceData.nominal) : '-'"></span></div>
                                    <div><span class="text-gray-500">Jatuh Tempo:</span> <span class="font-medium text-green-700" x-text="formatDate(sourceData?.jatuh_tempo)"></span></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- ── ASURANSI PERPANJANG: Sebelum vs Sesudah ── --}}
                    <div x-show="data?.source_type === 'asuransi_kendaraan_perpanjang'"
                         class="bg-amber-50 rounded-xl p-4 border border-amber-200 text-sm">
                        <h4 class="font-bold text-amber-800 mb-3 flex items-center gap-2 text-sm">
                            <i class="bi bi-arrow-repeat text-amber-600"></i>
                            Perpanjangan Asuransi Kendaraan
                        </h4>
                        <div class="mb-3 text-xs">
                            <span class="text-gray-500">Perusahaan Asuransi:</span>
                            <span class="font-semibold text-gray-800 ml-1" x-text="relatedData?.asuransi?.nama_asuransi || '-'"></span>
                            <span class="mx-1 text-gray-400">·</span>
                            <span class="text-gray-500">Jenis:</span>
                            <span class="font-semibold text-gray-800 ml-1" x-text="relatedData?.jenis_asuransi?.nama_jenis || '-'"></span>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="bg-white rounded-lg p-3 border border-amber-200">
                                <p class="text-[10px] font-semibold text-amber-600 uppercase tracking-wide mb-2">Sebelum</p>
                                <div class="space-y-1.5 text-xs">
                                    <div><span class="text-gray-500">Mulai:</span> <span class="font-medium text-gray-800" x-text="formatDate(relatedData?.asuransi_lama?.tgl_mulai)"></span></div>
                                    <div><span class="text-gray-500">Berakhir:</span> <span class="font-medium text-gray-800" x-text="formatDate(relatedData?.asuransi_lama?.tgl_berakhir)"></span></div>
                                    <div><span class="text-gray-500">Biaya:</span> <span class="font-medium text-gray-800" x-text="relatedData?.asuransi_lama?.biaya ? 'Rp ' + formatNumber(relatedData.asuransi_lama.biaya) : '-'"></span></div>
                                </div>
                            </div>
                            <div class="bg-white rounded-lg p-3 border border-green-200">
                                <p class="text-[10px] font-semibold text-green-600 uppercase tracking-wide mb-2">Sesudah</p>
                                <div class="space-y-1.5 text-xs">
                                    <div><span class="text-gray-500">Mulai:</span> <span class="font-medium text-green-700" x-text="formatDate(sourceData?.tgl_mulai)"></span></div>
                                    <div><span class="text-gray-500">Berakhir:</span> <span class="font-medium text-green-700" x-text="formatDate(sourceData?.tgl_berakhir)"></span></div>
                                    <div><span class="text-gray-500">Biaya:</span> <span class="font-medium text-green-700" x-text="sourceData?.biaya ? 'Rp ' + formatNumber(sourceData.biaya) : '-'"></span></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- ── KIR PERPANJANG: Sebelum vs Sesudah ── --}}
                    <div x-show="data?.source_type === 'kir_perpanjang'"
                         class="bg-amber-50 rounded-xl p-4 border border-amber-200 text-sm">
                        <h4 class="font-bold text-amber-800 mb-3 flex items-center gap-2 text-sm">
                            <i class="bi bi-arrow-repeat text-amber-600"></i>
                            Perpanjangan KIR Kendaraan
                        </h4>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="bg-white rounded-lg p-3 border border-amber-200">
                                <p class="text-[10px] font-semibold text-amber-600 uppercase tracking-wide mb-2">Sebelum</p>
                                <div class="space-y-1.5 text-xs">
                                    <div><span class="text-gray-500">No Uji:</span> <span class="font-medium text-gray-800" x-text="relatedData?.kir_lama?.no_uji || '-'"></span></div>
                                    <div><span class="text-gray-500">Masa Berlaku:</span> <span class="font-medium text-gray-800" x-text="formatDate(relatedData?.kir_lama?.masa_berlaku)"></span></div>
                                    <div><span class="text-gray-500">Biaya:</span> <span class="font-medium text-gray-800" x-text="relatedData?.kir_lama?.biaya ? 'Rp ' + formatNumber(relatedData.kir_lama.biaya) : '-'"></span></div>
                                </div>
                            </div>
                            <div class="bg-white rounded-lg p-3 border border-green-200">
                                <p class="text-[10px] font-semibold text-green-600 uppercase tracking-wide mb-2">Sesudah (+6 bulan)</p>
                                <div class="space-y-1.5 text-xs">
                                    <div><span class="text-gray-500">No Uji:</span> <span class="font-medium text-green-700" x-text="sourceData?.no_uji || '-'"></span></div>
                                    <div><span class="text-gray-500">Masa Berlaku:</span>
                                        <span class="font-medium text-green-700">
                                            <template x-if="relatedData?.kir_lama?.masa_berlaku">
                                                <span x-text="formatDate(addMonths(relatedData.kir_lama.masa_berlaku, 6))"></span>
                                            </template>
                                            <template x-if="!relatedData?.kir_lama?.masa_berlaku">
                                                <span>-</span>
                                            </template>
                                        </span>
                                    </div>
                                    <div><span class="text-gray-500">Biaya:</span> <span class="font-medium text-green-700" x-text="sourceData?.biaya ? 'Rp ' + formatNumber(sourceData.biaya) : '-'"></span></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- File dari pengaju (non-GPS) --}}
                    <div x-show="tempFiles?.bukti?.length > 0 || tempFiles?.attachments?.length > 0"
                         class="bg-blue-50 rounded-xl p-4 border border-blue-200">
                        <h4 class="font-bold text-gray-800 mb-3 flex items-center gap-2 text-sm">
                            <i class="bi bi-paperclip text-blue-600"></i>
                            File dari Pengajuan
                        </h4>
                        <div class="space-y-2">
                            <template x-for="file in tempFiles?.bukti || []" :key="file.path">
                                <div class="flex items-center gap-2 text-sm bg-white rounded-lg px-3 py-2 border border-blue-200">
                                    <i class="bi bi-file-earmark text-blue-600"></i>
                                    <span class="flex-1 text-gray-700" x-text="file.original_name"></span>
                                    <a :href="'/storage/' + file.path" target="_blank"
                                       class="text-blue-600 hover:text-blue-700 text-xs font-medium">Preview</a>
                                </div>
                            </template>
                        </div>
                    </div>

                    {{-- Toggle Approve / Reject (non-GPS) --}}
                    <div class="flex gap-3 p-2 bg-gray-100 rounded-xl">
                        <button @click="actionType = 'approve'"
                                :class="actionType === 'approve' ? 'bg-green-600 text-white shadow-lg' : 'bg-white text-gray-700 hover:bg-gray-50'"
                                class="flex-1 py-3 px-4 rounded-lg font-semibold transition-all text-sm">
                            <i class="bi bi-check-circle mr-2"></i> Approve
                        </button>
                        <button @click="actionType = 'reject'"
                                :class="actionType === 'reject' ? 'bg-red-600 text-white shadow-lg' : 'bg-white text-gray-700 hover:bg-gray-50'"
                                class="flex-1 py-3 px-4 rounded-lg font-semibold transition-all text-sm">
                            <i class="bi bi-x-circle mr-2"></i> Reject
                        </button>
                    </div>

                    {{-- Upload Bukti (Approve, non-GPS) --}}
                    <div x-show="actionType === 'approve'"
                         class="bg-green-50 rounded-xl p-5 border-2 border-green-200">
                        <h4 class="font-bold text-gray-800 mb-1 flex items-center gap-2 text-sm">
                            <i class="bi bi-cloud-upload text-green-600"></i>
                            Upload Bukti Pembayaran <span class="text-red-500">*</span>
                        </h4>
                        <p class="text-xs text-gray-500 mb-3">Wajib upload minimal 1 file</p>
                        <div class="border-2 border-dashed border-green-300 rounded-lg p-5 text-center bg-white cursor-pointer hover:bg-green-50 transition-colors"
                             @click="$refs.buktiInput.click()"
                             @dragover.prevent="isDragging = true"
                             @dragleave.prevent="isDragging = false"
                             @drop.prevent="handleDrop($event, 'bukti')"
                             :class="isDragging ? 'border-green-500 bg-green-100' : ''">
                            <input type="file" x-ref="buktiInput"
                                   @change="handleFileSelect($event, 'bukti')"
                                   accept=".jpg,.jpeg,.png,.pdf,.doc,.docx,.xls,.xlsx,.zip"
                                   multiple class="hidden">
                            <i class="bi bi-cloud-arrow-up text-3xl text-green-600 mb-1"></i>
                            <p class="text-xs font-medium text-gray-700">Klik atau drag & drop</p>
                            <p class="text-[11px] text-gray-400 mt-0.5">JPG, PNG, PDF, DOC, XLS, ZIP (Max 5MB)</p>
                        </div>
                        <div x-show="buktiFiles.length > 0" class="mt-3 space-y-2">
                            <template x-for="(file, index) in buktiFiles" :key="index">
                                <div class="flex items-center gap-3 bg-white rounded-lg px-3 py-2 border border-green-200">
                                    <i class="bi bi-file-check text-green-600 text-sm"></i>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-xs font-medium text-gray-700 truncate" x-text="file.name"></p>
                                        <p class="text-[11px] text-gray-400" x-text="formatFileSize(file.size)"></p>
                                    </div>
                                    <button @click="removeFile(index, 'bukti')" class="text-red-400 hover:text-red-600 rounded p-1">
                                        <i class="bi bi-x-lg text-xs"></i>
                                    </button>
                                </div>
                            </template>
                        </div>
                    </div>

                    {{-- Catatan (non-GPS) --}}
                    <div>
                        <label class="block font-bold text-gray-800 mb-2 flex items-center gap-2 text-sm">
                            <i class="bi bi-chat-left-text"
                               :class="actionType === 'approve' ? 'text-green-600' : 'text-red-600'"></i>
                            Catatan
                            <span x-show="actionType === 'reject'" class="text-red-500">*</span>
                            <span x-show="actionType === 'approve'" class="text-xs text-gray-400 font-normal">(Opsional)</span>
                        </label>
                        <textarea x-model="catatan" rows="3"
                                  :placeholder="actionType === 'reject' ? 'Wajib isi alasan penolakan...' : 'Catatan tambahan (opsional)...'"
                                  class="w-full px-4 py-3 border-2 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 resize-none text-sm"
                                  :class="actionType === 'reject' ? 'border-red-300' : 'border-gray-300'"></textarea>
                    </div>
                </div>

            </div>

            {{-- Footer --}}
            <div class="sticky bottom-0 bg-white border-t border-gray-200 px-6 py-4 flex justify-end gap-3">
                <button @click="closeModal()"
                        class="px-5 py-2.5 text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg font-medium transition-colors text-sm">
                    Batal
                </button>

                {{-- GPS: Simpan Keputusan --}}
                <button
                    x-show="data?.source_type === 'gps' || data?.source_type === 'gps_perpanjang'"
                    @click="submitItemDecisions()"
                    :disabled="submitting || decidedCount() === 0 || !allRejectedHaveCatatan() || !allApprovedHaveBukti()"
                    :class="submitting || decidedCount() === 0 || !allRejectedHaveCatatan() || !allApprovedHaveBukti()
                        ? 'opacity-50 cursor-not-allowed bg-blue-400'
                        : 'bg-blue-600 hover:bg-blue-700'"
                    class="px-5 py-2.5 text-white rounded-lg font-medium transition-colors text-sm flex items-center gap-2">
                    <svg x-show="submitting" class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <i x-show="!submitting" class="fa-solid fa-floppy-disk text-sm"></i>
                    <span x-text="submitting ? 'Menyimpan...' : 'Simpan Keputusan'"></span>
                </button>

                {{-- Non-GPS: Approve --}}
                <button
                    x-show="data?.source_type !== 'gps' && data?.source_type !== 'gps_perpanjang' && actionType === 'approve'"
                    @click="submitApprove()"
                    :disabled="submitting || buktiFiles.length === 0"
                    :class="submitting || buktiFiles.length === 0 ? 'opacity-50 cursor-not-allowed' : 'hover:bg-green-700'"
                    class="px-5 py-2.5 bg-green-600 text-white rounded-lg font-medium transition-colors text-sm flex items-center gap-2">
                    <i x-show="!submitting" class="bi bi-check-circle"></i>
                    <svg x-show="submitting" class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span x-text="submitting ? 'Processing...' : 'Approve'"></span>
                </button>

                {{-- Non-GPS: Reject --}}
                <button
                    x-show="data?.source_type !== 'gps' && data?.source_type !== 'gps_perpanjang' && actionType === 'reject'"
                    @click="submitReject()"
                    :disabled="submitting || !catatan.trim()"
                    :class="submitting || !catatan.trim() ? 'opacity-50 cursor-not-allowed' : 'hover:bg-red-700'"
                    class="px-5 py-2.5 bg-red-600 text-white rounded-lg font-medium transition-colors text-sm flex items-center gap-2">
                    <i x-show="!submitting" class="bi bi-x-circle"></i>
                    <svg x-show="submitting" class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span x-text="submitting ? 'Processing...' : 'Reject'"></span>
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function approvalModal() {
    return {
        show:          false,
        loading:       false,
        submitting:    false,
        data:          null,
        sourceData:    null,
        relatedData:   null,
        tempFiles:     null,

        // Non-GPS state
        actionType:    'approve',
        buktiFiles:    [],
        catatan:       '',
        isDragging:    false,

        // GPS per-item decisions: { action: null|'approved'|'rejected', catatan: '', buktiFile: null }
        itemDecisions: [],

        // ── Open / Close ──────────────────────────────────────────────────────
        openModal(pembayaranData) {
            this.show    = true;
            this.loading = true;
            this.resetForm();

            fetch(`/admin/pembayaran/${pembayaranData.id}/approval-modal`)
                .then(res => res.json())
                .then(response => {
                    if (response.success) {
                        this.data        = response.data.pembayaran;
                        this.sourceData  = response.data.source_data;
                        this.relatedData = response.data.related_data;
                        this.tempFiles   = response.data.temp_files;
                        this.initItemDecisions();
                    }
                })
                .catch(err => {
                    console.error('Error loading approval data:', err);
                    alert('Gagal memuat data. Silakan coba lagi.');
                    this.closeModal();
                })
                .finally(() => { this.loading = false; });
        },

        closeModal() {
            this.show = false;
            setTimeout(() => this.resetForm(), 300);
        },

        resetForm() {
            this.data          = null;
            this.sourceData    = null;
            this.relatedData   = null;
            this.tempFiles     = null;
            this.actionType    = 'approve';
            this.buktiFiles    = [];
            this.catatan       = '';
            this.isDragging    = false;
            this.itemDecisions = [];
            this.submitting    = false;
        },

        // ── GPS per-item helpers ──────────────────────────────────────────────
        initItemDecisions() {
            const count = this.relatedData?.gps_items?.length || 0;
            this.itemDecisions = Array.from({ length: count }, () => ({
                action:    null,
                catatan:   '',
                buktiFile: null,
            }));
        },

        setAction(idx, action) {
            if (!this.itemDecisions[idx]) return;
            // Toggle off jika klik ulang
            if (this.itemDecisions[idx].action === action) {
                this.itemDecisions[idx].action = null;
            } else {
                this.itemDecisions[idx].action = action;
            }
            // Reactivity: replace array element
            this.itemDecisions = [...this.itemDecisions];
        },

        handleBuktiItem(event, idx) {
            const file = event.target.files[0];
            if (!file) return;
            if (file.size > 5 * 1024 * 1024) {
                alert('File terlalu besar. Max 5MB.');
                return;
            }
            this.itemDecisions[idx].buktiFile = file;
            this.itemDecisions = [...this.itemDecisions];
            event.target.value = '';
        },

        removeItemBukti(idx) {
            this.itemDecisions[idx].buktiFile = null;
            this.itemDecisions = [...this.itemDecisions];
        },

        decidedCount() {
            return this.itemDecisions.filter(d => d.action !== null).length;
        },

        approvedCount() {
            return this.itemDecisions.filter(d => d.action === 'approved').length;
        },

        rejectedCount() {
            return this.itemDecisions.filter(d => d.action === 'rejected').length;
        },

        allRejectedHaveCatatan() {
            return this.itemDecisions
                .filter(d => d.action === 'rejected')
                .every(d => d.catatan.trim() !== '');
        },

        allApprovedHaveBukti() {
            return this.itemDecisions
                .filter(d => d.action === 'approved')
                .every(d => d.buktiFile !== null);
        },

        // ── Submit GPS per-item ───────────────────────────────────────────────
        async submitItemDecisions() {
            if (this.decidedCount() === 0) {
                alert('Minimal satu item harus diberi keputusan.');
                return;
            }
            if (!this.allRejectedHaveCatatan()) {
                alert('Semua item yang ditolak harus memiliki alasan penolakan.');
                return;
            }
            if (!this.allApprovedHaveBukti()) {
                alert('Bukti pembayaran wajib diupload untuk setiap item yang disetujui.');
                return;
            }

            const decided = this.itemDecisions.filter(d => d.action !== null);
            if (!confirm(`Simpan keputusan: ${this.approvedCount()} disetujui, ${this.rejectedCount()} ditolak?`)) return;

            this.submitting = true;

            const formData = new FormData();
            formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);

            this.itemDecisions.forEach((decision, idx) => {
                if (decision.action === null) return;
                formData.append(`items[${idx}][action]`,  decision.action);
                formData.append(`items[${idx}][catatan]`, decision.catatan || '');
                if (decision.buktiFile) {
                    formData.append(`items[${idx}][bukti]`, decision.buktiFile);
                }
            });

            try {
                const response = await fetch(`/admin/pembayaran/${this.data.id}/approve-items`, {
                    method: 'POST',
                    body:   formData,
                });
                if (response.redirected) {
                    window.location.href = response.url;
                } else {
                    const result = await response.json().catch(() => null);
                    if (result?.success) {
                        window.location.reload();
                    } else {
                        alert(result?.message || 'Terjadi kesalahan, silakan coba lagi.');
                    }
                }
            } catch (error) {
                console.error('Submit error:', error);
                alert('Terjadi kesalahan jaringan. Silakan coba lagi.');
            } finally {
                this.submitting = false;
            }
        },

        // ── Non-GPS: Global Approve ───────────────────────────────────────────
        async submitApprove() {
            if (this.buktiFiles.length === 0) {
                alert('Wajib upload minimal 1 bukti pembayaran!');
                return;
            }
            if (!confirm('Yakin ingin menyetujui pengeluaran ini?')) return;

            this.submitting = true;
            const formData  = new FormData();
            this.buktiFiles.forEach((file, i) => formData.append(`bukti[${i}]`, file));
            if (this.catatan.trim()) formData.append('catatan', this.catatan);
            formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);

            try {
                const response = await fetch(`/admin/pembayaran/${this.data.id}/approve`, {
                    method: 'POST', body: formData,
                });
                if (response.redirected) {
                    window.location.href = response.url;
                } else {
                    const result = await response.json().catch(() => null);
                    if (result?.success) window.location.reload();
                    else alert(result?.message || 'Terjadi kesalahan');
                }
            } catch (e) {
                alert('Terjadi kesalahan. Silakan coba lagi.');
            } finally {
                this.submitting = false;
            }
        },

        // ── Non-GPS: Global Reject ────────────────────────────────────────────
        async submitReject() {
            if (!this.catatan.trim()) {
                alert('Alasan penolakan wajib diisi!');
                return;
            }
            if (!confirm('Yakin ingin menolak pengeluaran ini?')) return;

            this.submitting = true;
            const formData  = new FormData();
            formData.append('catatan', this.catatan);
            formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);

            try {
                const response = await fetch(`/admin/pembayaran/${this.data.id}/reject`, {
                    method: 'POST', body: formData,
                });
                if (response.redirected) {
                    window.location.href = response.url;
                } else {
                    const result = await response.json().catch(() => null);
                    if (result?.success) window.location.reload();
                    else alert(result?.message || 'Terjadi kesalahan');
                }
            } catch (e) {
                alert('Terjadi kesalahan. Silakan coba lagi.');
            } finally {
                this.submitting = false;
            }
        },

        // ── Non-GPS File Handlers ─────────────────────────────────────────────
        handleFileSelect(event, type) {
            const files = Array.from(event.target.files);
            this.addFiles(files, type);
            event.target.value = '';
        },

        handleDrop(event, type) {
            this.isDragging = false;
            this.addFiles(Array.from(event.dataTransfer.files), type);
        },

        addFiles(files, type) {
            const maxSize    = 5 * 1024 * 1024;
            const validFiles = files.filter(f => {
                if (f.size > maxSize) { alert(`File ${f.name} terlalu besar. Max 5MB.`); return false; }
                return true;
            });
            if (type === 'bukti') this.buktiFiles.push(...validFiles);
        },

        removeFile(index, type) {
            if (type === 'bukti') this.buktiFiles.splice(index, 1);
        },

        // ── Formatters ────────────────────────────────────────────────────────
        formatNumber(num) {
            return new Intl.NumberFormat('id-ID').format(num);
        },

        formatDate(dateStr) {
            if (!dateStr) return '-';
            return new Date(dateStr).toLocaleDateString('id-ID', {
                day: '2-digit', month: 'short', year: 'numeric'
            });
        },

        formatFileSize(bytes) {
            if (!bytes) return '0 B';
            const k = 1024, sizes = ['B','KB','MB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return (bytes / Math.pow(k, i)).toFixed(1) + ' ' + sizes[i];
        },

        // ── Helpers ───────────────────────────────────────────────────────────
        addMonths(dateStr, months) {
            if (!dateStr) return null;
            const d = new Date(dateStr);
            d.setMonth(d.getMonth() + months);
            return d.toISOString().split('T')[0];
        },

        isGpsType() {
            return this.data?.source_type === 'gps' || this.data?.source_type === 'gps_perpanjang';
        },
    };
}
</script>
@endpush
