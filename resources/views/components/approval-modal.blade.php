{{-- 
    Approval Modal Component for Pengeluaran Kendaraan
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

            {{-- Body --}}
            <div class="overflow-y-auto max-h-[calc(90vh-180px)] px-6 py-6 space-y-6">
                
                {{-- Section 1: Detail Pengeluaran --}}
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
                            <p class="text-gray-500 mb-1">Nominal</p>
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

                    {{-- Dynamic fields berdasarkan source_type --}}
                    <div x-show="sourceData" class="mt-4 pt-4 border-t border-gray-300">
                        <div x-show="data?.source_type === 'asuransi_kendaraan'" class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <p class="text-gray-500 mb-1">Kendaraan</p>
                                <p class="font-semibold text-gray-800" x-text="relatedData?.kendaraan?.nopol || '-'"></p>
                            </div>
                            <div>
                                <p class="text-gray-500 mb-1">Perusahaan Asuransi</p>
                                <p class="font-semibold text-gray-800" x-text="relatedData?.asuransi?.nama_asuransi || '-'"></p>
                            </div>
                            <div>
                                <p class="text-gray-500 mb-1">Jenis Asuransi</p>
                                <p class="font-semibold text-gray-800" x-text="relatedData?.jenis_asuransi?.nama_jenis || '-'"></p>
                            </div>
                            <div>
                                <p class="text-gray-500 mb-1">Periode</p>
                                <p class="font-semibold text-gray-800" x-text="formatDate(sourceData?.tgl_mulai) + ' - ' + formatDate(sourceData?.tgl_berakhir)"></p>
                            </div>
                        </div>

                        <div x-show="data?.source_type === 'pajak'" class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <p class="text-gray-500 mb-1">Kendaraan</p>
                                <p class="font-semibold text-gray-800" x-text="relatedData?.kendaraan?.nopol || '-'"></p>
                            </div>
                            <div>
                                <p class="text-gray-500 mb-1">Jenis Pajak</p>
                                <p class="font-semibold text-gray-800" x-text="sourceData?.jenis_pajak || '-'"></p>
                            </div>
                            <div>
                                <p class="text-gray-500 mb-1">Jatuh Tempo</p>
                                <p class="font-semibold text-gray-800" x-text="formatDate(sourceData?.jatuh_tempo)"></p>
                            </div>
                            <div>
                                <p class="text-gray-500 mb-1">Status</p>
                                <p class="font-semibold text-gray-800" x-text="sourceData?.status || '-'"></p>
                            </div>
                        </div>
                    </div>

                    <div x-show="sourceData?.keterangan" class="mt-4 pt-4 border-t border-gray-300">
                        <p class="text-gray-500 text-sm mb-1">Keterangan</p>
                        <p class="text-gray-700 text-sm" x-text="sourceData?.keterangan"></p>
                    </div>
                </div>

                {{-- Section 1.5: Rekening Bank Info --}}
                <div x-show="data?.nama_bank || data?.no_rekening || data?.nama_rekening || data?.informasi" 
                     class="bg-amber-50 rounded-xl p-5 border border-amber-200">
                    <h4 class="font-bold text-gray-800 mb-3 flex items-center gap-2">
                        <i class="bi bi-bank text-amber-600"></i>
                        Informasi Rekening Bank
                    </h4>
                    
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div x-show="data?.nama_bank">
                            <p class="text-gray-500 mb-1">Nama Bank</p>
                            <p class="font-semibold text-gray-800" x-text="data?.nama_bank || '-'"></p>
                        </div>
                        <div x-show="data?.no_rekening">
                            <p class="text-gray-500 mb-1">No. Rekening</p>
                            <p class="font-semibold text-gray-800" x-text="data?.no_rekening || '-'"></p>
                        </div>
                        <div x-show="data?.nama_rekening" class="col-span-2">
                            <p class="text-gray-500 mb-1">Nama Pemilik Rekening</p>
                            <p class="font-semibold text-gray-800" x-text="data?.nama_rekening || '-'"></p>
                        </div>
                    </div>

                    <div x-show="data?.informasi" class="mt-4 pt-4 border-t border-amber-300">
                        <p class="text-gray-500 text-sm mb-1">Informasi Tambahan</p>
                        <p class="text-gray-700 text-sm" x-text="data?.informasi"></p>
                    </div>
                </div>

                {{-- Section 2: Files dari User (Temp) --}}
                <div x-show="tempFiles?.bukti?.length > 0 || tempFiles?.attachments?.length > 0" class="bg-blue-50 rounded-xl p-5 border border-blue-200">
                    <h4 class="font-bold text-gray-800 mb-3 flex items-center gap-2">
                        <i class="bi bi-paperclip text-blue-600"></i>
                        File dari Pengajuan Awal
                    </h4>
                    <div class="space-y-2">
                        <template x-for="file in tempFiles?.bukti || []" :key="file.path">
                            <div class="flex items-center gap-2 text-sm bg-white rounded-lg px-3 py-2 border border-blue-200">
                                <i class="bi bi-file-earmark text-blue-600"></i>
                                <span class="flex-1 text-gray-700" x-text="file.original_name"></span>
                                <span class="text-xs text-gray-500" x-text="formatFileSize(file.size)"></span>
                                <a :href="'/storage/' + file.path" target="_blank" 
                                   class="text-blue-600 hover:text-blue-700 text-xs font-medium">
                                    Preview
                                </a>
                            </div>
                        </template>
                        <template x-for="file in tempFiles?.attachments || []" :key="file.path">
                            <div class="flex items-center gap-2 text-sm bg-white rounded-lg px-3 py-2 border border-blue-200">
                                <i class="bi bi-file-earmark-text text-gray-600"></i>
                                <span class="flex-1 text-gray-700" x-text="file.original_name"></span>
                                <span class="text-xs text-gray-500" x-text="formatFileSize(file.size)"></span>
                                <a :href="'/storage/' + file.path" target="_blank" 
                                   class="text-blue-600 hover:text-blue-700 text-xs font-medium">
                                    Preview
                                </a>
                            </div>
                        </template>
                    </div>
                </div>

                {{-- Action Type Selector --}}
                <div class="flex gap-3 p-2 bg-gray-100 rounded-xl">
                    <button 
                        @click="actionType = 'approve'"
                        :class="actionType === 'approve' ? 'bg-green-600 text-white shadow-lg' : 'bg-white text-gray-700 hover:bg-gray-50'"
                        class="flex-1 py-3 px-4 rounded-lg font-semibold transition-all"
                    >
                        <i class="bi bi-check-circle mr-2"></i>
                        Approve
                    </button>
                    <button 
                        @click="actionType = 'reject'"
                        :class="actionType === 'reject' ? 'bg-red-600 text-white shadow-lg' : 'bg-white text-gray-700 hover:bg-gray-50'"
                        class="flex-1 py-3 px-4 rounded-lg font-semibold transition-all"
                    >
                        <i class="bi bi-x-circle mr-2"></i>
                        Reject
                    </button>
                </div>

                {{-- Section 3: Upload Bukti (for Approve) --}}
                <div x-show="actionType === 'approve'">
                    <div class="bg-green-50 rounded-xl p-5 border-2 border-green-200">
                        <h4 class="font-bold text-gray-800 mb-1 flex items-center gap-2">
                            <i class="bi bi-cloud-upload text-green-600"></i>
                            Upload Bukti Pembayaran
                            <span class="text-red-500">*</span>
                        </h4>
                        <p class="text-sm text-gray-600 mb-4">Wajib upload minimal 1 file bukti pembayaran</p>
                        
                        <div class="border-2 border-dashed border-green-300 rounded-lg p-6 text-center bg-white hover:bg-green-50 transition-colors cursor-pointer"
                             @click="$refs.buktiInput.click()"
                             @dragover.prevent="isDragging = true"
                             @dragleave.prevent="isDragging = false"
                             @drop.prevent="handleDrop($event, 'bukti')"
                             :class="isDragging ? 'border-green-500 bg-green-100' : ''">
                            <input 
                                type="file" 
                                x-ref="buktiInput"
                                @change="handleFileSelect($event, 'bukti')"
                                accept=".jpg,.jpeg,.png,.pdf,.doc,.docx,.xls,.xlsx,.zip"
                                multiple
                                class="hidden"
                            >
                            <i class="bi bi-cloud-arrow-up text-4xl text-green-600 mb-2"></i>
                            <p class="text-sm font-medium text-gray-700">Klik atau drag & drop file</p>
                            <p class="text-xs text-gray-500 mt-1">JPG, PNG, PDF, DOC, XLS, ZIP (Max 5MB per file)</p>
                        </div>

                        <div x-show="buktiFiles.length > 0" class="mt-4 space-y-2">
                            <template x-for="(file, index) in buktiFiles" :key="index">
                                <div class="flex items-center gap-3 bg-white rounded-lg px-4 py-3 border border-green-200">
                                    <i class="bi bi-file-check text-green-600"></i>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-700 truncate" x-text="file.name"></p>
                                        <p class="text-xs text-gray-500" x-text="formatFileSize(file.size)"></p>
                                    </div>
                                    <button 
                                        @click="removeFile(index, 'bukti')"
                                        class="text-red-500 hover:text-red-700 hover:bg-red-50 rounded p-1.5"
                                    >
                                        <i class="bi bi-x-lg"></i>
                                    </button>
                                </div>
                            </template>
                        </div>
                    </div>

                    {{-- Section 4: Upload Attachment (Optional for Approve) --}}
                    <div class="bg-gray-50 rounded-xl p-5 border border-gray-200 mt-4">
                        <h4 class="font-bold text-gray-800 mb-1 flex items-center gap-2">
                            <i class="bi bi-paperclip text-gray-600"></i>
                            Attachment Tambahan
                            <span class="text-xs text-gray-500 font-normal">(Opsional)</span>
                        </h4>
                        <p class="text-sm text-gray-600 mb-4">Upload dokumen pendukung lainnya</p>
                        
                        <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center bg-white hover:bg-gray-50 transition-colors cursor-pointer"
                             @click="$refs.attachmentInput.click()">
                            <input 
                                type="file" 
                                x-ref="attachmentInput"
                                @change="handleFileSelect($event, 'attachment')"
                                accept=".jpg,.jpeg,.png,.pdf,.doc,.docx,.xls,.xlsx,.zip"
                                multiple
                                class="hidden"
                            >
                            <i class="bi bi-file-plus text-2xl text-gray-400 mb-1"></i>
                            <p class="text-xs text-gray-600">Klik untuk upload attachment</p>
                        </div>

                        <div x-show="attachmentFiles.length > 0" class="mt-3 space-y-2">
                            <template x-for="(file, index) in attachmentFiles" :key="index">
                                <div class="flex items-center gap-3 bg-white rounded-lg px-3 py-2 border border-gray-200">
                                    <i class="bi bi-file-earmark text-gray-600 text-sm"></i>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm text-gray-700 truncate" x-text="file.name"></p>
                                        <p class="text-xs text-gray-500" x-text="formatFileSize(file.size)"></p>
                                    </div>
                                    <button 
                                        @click="removeFile(index, 'attachment')"
                                        class="text-red-500 hover:text-red-700 hover:bg-red-50 rounded p-1"
                                    >
                                        <i class="bi bi-x-lg text-xs"></i>
                                    </button>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                {{-- Section 5: Catatan --}}
                <div>
                    <label class="block font-bold text-gray-800 mb-2 flex items-center gap-2">
                        <i class="bi bi-chat-left-text" :class="actionType === 'approve' ? 'text-green-600' : 'text-red-600'"></i>
                        Catatan
                        <span x-show="actionType === 'reject'" class="text-red-500">*</span>
                        <span x-show="actionType === 'approve'" class="text-xs text-gray-500 font-normal">(Opsional)</span>
                    </label>
                    <textarea 
                        x-model="catatan"
                        rows="3"
                        :placeholder="actionType === 'reject' ? 'Wajib isi alasan penolakan...' : 'Catatan tambahan (opsional)...'"
                        class="w-full px-4 py-3 border-2 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 resize-none"
                        :class="actionType === 'reject' ? 'border-red-300' : 'border-gray-300'"
                    ></textarea>
                    <p x-show="actionType === 'reject'" class="text-xs text-red-600 mt-1">
                        <i class="bi bi-exclamation-circle mr-1"></i>
                        Alasan penolakan wajib diisi agar pemohon dapat melakukan perbaikan
                    </p>
                </div>

            </div>

            {{-- Footer Actions --}}
            <div class="sticky bottom-0 bg-white border-t border-gray-200 px-6 py-4 flex justify-end gap-3">
                <button 
                    @click="closeModal()"
                    class="px-6 py-2.5 text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg font-medium transition-colors"
                >
                    Batal
                </button>
                <button 
                    x-show="actionType === 'approve'"
                    @click="submitApprove()"
                    :disabled="loading || buktiFiles.length === 0"
                    :class="loading || buktiFiles.length === 0 ? 'opacity-50 cursor-not-allowed' : 'hover:bg-green-700'"
                    class="px-6 py-2.5 bg-green-600 text-white rounded-lg font-medium transition-colors flex items-center gap-2"
                >
                    <i x-show="!loading" class="bi bi-check-circle"></i>
                    <svg x-show="loading" class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span x-text="loading ? 'Processing...' : 'Approve'"></span>
                </button>
                <button 
                    x-show="actionType === 'reject'"
                    @click="submitReject()"
                    :disabled="loading || !catatan.trim()"
                    :class="loading || !catatan.trim() ? 'opacity-50 cursor-not-allowed' : 'hover:bg-red-700'"
                    class="px-6 py-2.5 bg-red-600 text-white rounded-lg font-medium transition-colors flex items-center gap-2"
                >
                    <i x-show="!loading" class="bi bi-x-circle"></i>
                    <svg x-show="loading" class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span x-text="loading ? 'Processing...' : 'Reject'"></span>
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function approvalModal() {
    return {
        show: false,
        loading: false,
        data: null,
        sourceData: null,
        relatedData: null,
        tempFiles: null,
        actionType: 'approve', // 'approve' or 'reject'
        buktiFiles: [],
        attachmentFiles: [],
        catatan: '',
        isDragging: false,

        openModal(purchaseroData) {
            this.show = true;
            this.loading = true;
            this.resetForm();

            // Fetch full data from backend
            fetch(`/admin/purchasero/${purchaseroData.id}/approval-modal`)
                .then(res => res.json())
                .then(response => {
                    if (response.success) {
                        this.data = response.data.purchasero;
                        this.sourceData = response.data.source_data;
                        this.relatedData = response.data.related_data;
                        this.tempFiles = response.data.temp_files;
                    }
                })
                .catch(err => {
                    console.error('Error loading approval data:', err);
                    alert('Gagal memuat data. Silakan coba lagi.');
                    this.closeModal();
                })
                .finally(() => {
                    this.loading = false;
                });
        },

        closeModal() {
            this.show = false;
            setTimeout(() => this.resetForm(), 300);
        },

        resetForm() {
            this.data = null;
            this.sourceData = null;
            this.relatedData = null;
            this.tempFiles = null;
            this.actionType = 'approve';
            this.buktiFiles = [];
            this.attachmentFiles = [];
            this.catatan = '';
            this.isDragging = false;
        },

        handleFileSelect(event, type) {
            const files = Array.from(event.target.files);
            this.addFiles(files, type);
            event.target.value = ''; // Reset input
        },

        handleDrop(event, type) {
            this.isDragging = false;
            const files = Array.from(event.dataTransfer.files);
            this.addFiles(files, type);
        },

        addFiles(files, type) {
            const maxSize = 5 * 1024 * 1024; // 5MB
            const validFiles = files.filter(file => {
                if (file.size > maxSize) {
                    alert(`File ${file.name} terlalu besar. Max 5MB.`);
                    return false;
                }
                return true;
            });

            if (type === 'bukti') {
                this.buktiFiles.push(...validFiles);
            } else {
                this.attachmentFiles.push(...validFiles);
            }
        },

        removeFile(index, type) {
            if (type === 'bukti') {
                this.buktiFiles.splice(index, 1);
            } else {
                this.attachmentFiles.splice(index, 1);
            }
        },

        async submitApprove() {
            if (this.buktiFiles.length === 0) {
                alert('Wajib upload minimal 1 bukti pembayaran!');
                return;
            }

            if (!confirm('Yakin ingin menyetujui pengeluaran ini?')) {
                return;
            }

            this.loading = true;

            const formData = new FormData();
            this.buktiFiles.forEach((file, index) => {
                formData.append(`bukti[${index}]`, file);
            });
            this.attachmentFiles.forEach((file, index) => {
                formData.append(`attachment[${index}]`, file);
            });
            if (this.catatan.trim()) {
                formData.append('catatan', this.catatan);
            }
            formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);

            try {
                const response = await fetch(`/admin/purchasero/${this.data.id}/approve`, {
                    method: 'POST',
                    body: formData
                });

                if (response.redirected) {
                    window.location.href = response.url;
                } else {
                    const result = await response.json();
                    if (result.success) {
                        window.location.reload();
                    } else {
                        alert(result.message || 'Terjadi kesalahan');
                    }
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Terjadi kesalahan. Silakan coba lagi.');
            } finally {
                this.loading = false;
            }
        },

        async submitReject() {
            if (!this.catatan.trim()) {
                alert('Alasan penolakan wajib diisi!');
                return;
            }

            if (!confirm('Yakin ingin menolak pengeluaran ini? User dapat mengajukan ulang setelah memperbaiki.')) {
                return;
            }

            this.loading = true;

            const formData = new FormData();
            formData.append('catatan', this.catatan);
            formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);

            try {
                const response = await fetch(`/admin/purchasero/${this.data.id}/reject`, {
                    method: 'POST',
                    body: formData
                });

                if (response.redirected) {
                    window.location.href = response.url;
                } else {
                    const result = await response.json();
                    if (result.success) {
                        window.location.reload();
                    } else {
                        alert(result.message || 'Terjadi kesalahan');
                    }
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Terjadi kesalahan. Silakan coba lagi.');
            } finally {
                this.loading = false;
            }
        },

        formatNumber(num) {
            return new Intl.NumberFormat('id-ID').format(num);
        },

        formatDate(dateStr) {
            if (!dateStr) return '-';
            const date = new Date(dateStr);
            return date.toLocaleDateString('id-ID', { 
                day: '2-digit', 
                month: 'short', 
                year: 'numeric' 
            });
        },

        formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
        }
    };
}
</script>
@endpush
