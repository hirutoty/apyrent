{{-- MODAL CEKLIST KONDISI --}}
<div id="ceklistKondisiModal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-5 mx-auto p-4 border w-full max-w-5xl shadow-lg rounded-2xl bg-white my-8">
        
        {{-- HEADER --}}
        <div class="flex items-center justify-between p-4 border-b border-gray-200 sticky top-0 bg-white z-10 rounded-t-2xl">
            <h3 class="text-xl font-bold text-gray-800">
                <i class="bi bi-clipboard-check-fill text-blue-500 mr-2"></i>
                Ceklist Kondisi Kendaraan
            </h3>
            <button type="button" onclick="closeCeklistModal()" class="text-gray-400 hover:text-gray-600 transition">
                <i class="bi bi-x-lg text-xl"></i>
            </button>
        </div>

        {{-- FORM --}}
        <form id="ceklistKondisiForm" enctype="multipart/form-data" class="p-5">
            @csrf
            <input type="hidden" name="kendaraan_id" id="ceklistKendaraanId">

            <div class="space-y-5">

                {{-- INFORMASI UMUM --}}
                <div class="bg-gray-50 rounded-xl p-4 border border-gray-200">
                    <h4 class="text-base font-bold text-gray-800 mb-3">
                        <i class="bi bi-info-circle-fill text-blue-500 mr-2"></i>Informasi Umum
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1.5">Nama <span class="text-red-500">*</span></label>
                            <input type="text" name="nama" required
                                class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1.5">Perusahaan <span class="text-red-500">*</span></label>
                            <input type="text" name="perusahaan" required
                                class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1.5">No. Telephone <span class="text-red-500">*</span></label>
                            <input type="text" name="no_telephone" required
                                class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        <div class="md:col-span-3">
                            <label class="block text-xs font-semibold text-gray-700 mb-1.5">Alamat <span class="text-red-500">*</span></label>
                            <textarea name="alamat" required rows="2"
                                class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
                        </div>
                    </div>
                </div>

                {{-- INFORMASI PEMERIKSAAN --}}
                <div class="bg-gray-50 rounded-xl p-4 border border-gray-200">
                    <h4 class="text-base font-bold text-gray-800 mb-3">
                        <i class="bi bi-calendar-check-fill text-green-500 mr-2"></i>Informasi Pemeriksaan
                    </h4>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1.5">Tanggal <span class="text-red-500">*</span></label>
                            <input type="date" name="tanggal" required
                                class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1.5">Kilometer <span class="text-red-500">*</span></label>
                            <input type="number" name="kilometer" required
                                class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1.5">Jam <span class="text-red-500">*</span></label>
                            <input type="time" name="jam" required
                                class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1.5">Fuel <span class="text-red-500">*</span></label>
                            <select name="fuel" required
                                class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">Pilih</option>
                                <option value="E">E (Empty)</option>
                                <option value="1/8">1/8</option>
                                <option value="1/4">1/4</option>
                                <option value="1/2">1/2</option>
                                <option value="3/4">3/4</option>
                                <option value="F">F (Full)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1.5">Pemeriksa <span class="text-red-500">*</span></label>
                            <input type="text" name="pemeriksa" required
                                class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1.5">Status</label>
                            <select name="status"
                                class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">Pilih (Optional)</option>
                                <option value="status">Status</option>
                                <option value="leasing">Leasing</option>
                                <option value="gs">GS</option>
                                <option value="replace">Replace</option>
                                <option value="expedisi">Expedisi</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1.5">Ratio</label>
                            <input type="text" name="ratio"
                                class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                    </div>
                </div>

                

                {{-- FOTO KENDARAAN --}}
                <div class="bg-gray-50 rounded-xl p-4 border border-gray-200">
                    <h4 class="text-base font-bold text-gray-800 mb-3">
                        <i class="bi bi-camera-fill text-purple-500 mr-2"></i>Foto Kendaraan <span class="text-xs font-normal text-red-500">(Semua foto wajib)</span>
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                        @foreach([
                            'depan' => 'Foto Depan',
                            'atas' => 'Foto Atas',
                            'belakang' => 'Foto Belakang',
                            'sampingkiri' => 'Foto Samping Kiri',
                            'sampingkanan' => 'Foto Samping Kanan'
                        ] as $key => $label)
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1.5">{{ $label }} <span class="text-red-500">*</span></label>
                            <input type="file" name="foto_{{ $key }}" required accept="image/*"
                                class="w-full px-2 py-1.5 border border-gray-300 rounded-lg text-xs file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                            <input type="text" name="ket_{{ $key }}" placeholder="Keterangan (opsional)" 
                                class="w-full mt-2 px-2 py-1.5 border border-gray-300 rounded-lg text-xs focus:ring-2 focus:ring-blue-500">
                        </div>
                        @endforeach
                    </div>
                </div>

                {{-- FOTO TAMBAHAN (Multiple Upload) --}}
                <div class="bg-gradient-to-br from-orange-50 to-amber-50 rounded-xl p-4 border-2 border-orange-200">
                    <div class="flex items-center justify-between mb-3">
                        <h4 class="text-base font-bold text-gray-800">
                            <i class="bi bi-images text-orange-500 mr-2"></i>Foto Tambahan <span class="text-xs font-normal text-orange-600">(Min. 1 foto wajib)</span>
                        </h4>
                        <button type="button" onclick="addFotoTambahan()" 
                            class="px-3 py-1.5 bg-orange-500 text-white rounded-lg hover:bg-orange-600 transition-colors text-xs font-semibold flex items-center gap-1.5">
                            <i class="bi bi-plus-circle"></i> Tambah Foto
                        </button>
                    </div>
                    
                    <div id="fotoTambahanContainer" class="space-y-3">
                        {{-- First foto tambahan (wajib) --}}
                        <div class="foto-tambahan-item bg-white rounded-lg p-3 border border-orange-200 relative">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">
                                        Foto <span class="text-red-500">*</span>
                                    </label>
                                    <input type="file" name="foto_tambahan[]" required accept="image/*"
                                        class="w-full px-2 py-1.5 border border-gray-300 rounded-lg text-xs file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-orange-50 file:text-orange-700 hover:file:bg-orange-100">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">
                                        Keterangan
                                    </label>
                                    <input type="text" name="ket_tambahan[]" placeholder="Keterangan foto..."
                                        class="w-full px-3 py-1.5 border border-gray-300 rounded-lg text-xs focus:ring-2 focus:ring-orange-500">
                                </div>
                            </div>
                            <button type="button" onclick="removeFotoTambahan(this)" 
                                class="absolute top-2 right-2 w-6 h-6 bg-red-100 text-red-600 rounded-full hover:bg-red-200 transition-colors flex items-center justify-center remove-foto-btn hidden">
                                <i class="bi bi-x text-sm font-bold"></i>
                            </button>
                        </div>
                    </div>
                    
                    <p class="text-[10px] text-orange-600 mt-2 flex items-center gap-1">
                        <i class="bi bi-info-circle-fill"></i>
                        <span>Minimal 1 foto tambahan wajib diisi. Klik "Tambah Foto" untuk menambah lebih banyak foto.</span>
                    </p>
                </div>

                {{-- ACCESORIS TAMBAHAN --}}
                <div class="bg-gray-50 rounded-xl p-4 border border-gray-200">
                    <h4 class="text-base font-bold text-gray-800 mb-3">
                        <i class="bi bi-plus-circle-fill text-indigo-500 mr-2"></i>Accesoris Tambahan
                    </h4>
                    <textarea name="accecoris_tambahan" rows="2" placeholder="Tuliskan accesoris tambahan..."
                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
                </div>

                {{-- KONDISI KENDARAAN --}}
                <div class="bg-gray-50 rounded-xl p-4 border border-gray-200">
                    <h4 class="text-base font-bold text-gray-800 mb-3">
                        <i class="bi bi-clipboard-check-fill text-orange-500 mr-2"></i>Kondisi Kendaraan
                        <span class="text-xs font-normal text-gray-600">(Pilih <i class="bi bi-check-lg text-green-600"></i> Baik atau <i class="bi bi-x-lg text-red-600"></i> Rusak)</span>
                    </h4>
                    
                    @php
                        // Section A: Kondisi Kendaraan & Pemeriksaan Khusus (58 items - merge dari Section A + B lama)
                        $sectionA = [
                            'Mesin' => 'kondisi_mesin', 
                            'Rem' => 'kondisi_rem', 
                            'Kopling' => 'kondisi_kopling', 
                            'Ban' => 'kondisi_ban',
                            '- Ban Depan Kanan' => 'ban_depan_kanan',
                            '- Ban Depan Kiri' => 'ban_depan_kiri',
                            '- Ban Belakang Kanan' => 'ban_belakang_kanan',
                            '- Ban Belakang Kiri' => 'ban_belakang_kiri',
                            '- ban ganda depan kanan' => 'ban_ganda_depan_kanan',
                            '- ban ganda depan kiri' => 'ban_ganda_depan_kiri',
                            '- ban ganda belakang kanan' => 'ban_ganda_belakang_kanan',
                            '- ban ganda belakang kiri' => 'ban_ganda_belakang_kiri',
                            'AC' => 'kondisi_ac',
                            '- Lampu Sein' => 'lampu_sein',
                            '- Lampu Plat Belakang' => 'lampu_bik_plat',
                            '- Lampu Hazard' => 'lampu_hazard',
                            '- Lampu Kota' => 'lampu_kota',
                            '- Lampu Besar/Kecil' => 'lampu_besar_kecil',
                            '- Lampu Mundur' => 'lampu_mundur',
                            '- Lampu Rem Belakang' => 'lampu_rem_belakang',
                            '- Lampu Rem Depan' => 'lampu_rem_depan',
                            'Accu' => 'accu', 
                            'Air Radiator' => 'airradiator', 
                            'Oli Mesin/Rem' => 'olimesin_remkopling',
                            'Oli Power Steering' => 'olipower_steering', 
                            'Interior' => 'kebersihan_interior',
                            'Body/Cat' => 'kerapihanbody_cat', 
                            'Perlengkapan' => 'perlengkapan',
                            '- Ban Cadang' => 'ban_cadang', 
                            '- Dongkrak' => 'dongkrak', 
                            '- Kunci Roda' => 'kunciroda',
                            '- Radio/Tape' => 'radio_tape', 
                            '- Merk' => 'merk', 
                            '- Karpet' => 'karpet_lantai',
                            '- Dop Roda' => 'doproda',
                            '- Pematik Api' => 'pematikapi',
                            '- Segitiga Pengaman' => 'segitiga_pengaman',
                            '- Kotak Obat' => 'kotakobat',
                            'Kunci Pintu' => 'kuncipintu',
                            'Speedometer' => 'spedoometer',
                            // Pemeriksaan Khusus Truck & Keselamatan (23 items)
                            'Sistem Rem Angin' => 'sistem_rem_angin',
                            'Tekanan Angin Tabung' => 'tekanan_angin_tabung',
                            'Kompresor Angin & Saluran' => 'kompresor_angin_saluran',
                            'Brake Chamber & Slack Adjuster' => 'brake_chamber_slack_adjuster',
                            'Rem Parkir/Tangan' => 'rem_parkir_tangan',
                            'Transmisi/Persneling' => 'transmisi_persneling',
                            'Gardan & Kebocoran Oli' => 'gardan_kebocoran_oli',
                            'Kopel/Propeller/Universal' => 'kopel_propeller_universal',
                            'Suspensi/Per Daun' => 'suspensi_per_daun',
                            'Shock Absorber' => 'shock_absorber',
                            'Chassis/Rangka/Baut' => 'chassis_rangka_baut',
                            'Mur/Baut Roda & Velg' => 'mur_baut_roda_velg',
                            'Bak/Box/Karoseri/Lantai' => 'bak_box_karoseri_lantai',
                            'Pintu Bak & Sistem Pengunci' => 'pintu_bak_sistem_pengunci',
                            'Engsel/Pengait/Pengaman' => 'engsel_pengait_pengaman',
                            'Terpal/Tali Pengikat' => 'terpal_tali_pengikat',
                            'Wiper & Washer' => 'wiper_washer',
                            'Kaca Spion & Kendaraan' => 'kaca_spion_kendaraan',
                            'Sabuk Pengaman' => 'sabuk_pengaman',
                            'Ganjal Roda' => 'ganjal_roda',
                            'APAR' => 'apar',
                        ];
                        
                        // Section B: Kelengkapan Dokumen (10 items)
                        $sectionB = [
                            'STNK Kendaraan' => 'stnk',
                            'KIR Berlaku' => 'kir_berlaku',
                            'SIM Pengemudi' => 'sim_pengemudi',
                            '- SIM A' => 'sim_a',
                            '- SIM B1' => 'sim_b1',
                            '- SIM B1 Umum' => 'sim_b1_umum',
                            '- SIM B2' => 'sim_b2',
                            '- SIM B2 Umum' => 'sim_b2_umum',
                            'Dokumen Surat Jalan' => 'dokumen_surat_jalan',
                            'Kesesuaian Muatan' => 'kesesuaian_muatan',
                        ];
                    @endphp
                    
                    {{-- SECTION A: KONDISI KENDARAAN & PEMERIKSAAN KHUSUS --}}
                    <div class="mb-4">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="px-2 py-0.5 bg-blue-500 text-white text-xs font-bold rounded">A</span>
                            <h5 class="text-sm font-bold text-gray-700">Kondisi Kendaraan & Pemeriksaan Khusus</h5>
                            <span class="text-xs text-gray-500">(57 items)</span>
                        </div>
                        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-2">
                            @foreach($sectionA as $label => $field)
                            @if($field !== 'kondisi_ban')
                            <div class="flex items-center justify-between p-2 bg-white rounded-lg border border-gray-200">
                                <span class="text-xs font-medium text-gray-700">{{ $label }}</span>
                                <div class="flex gap-1.5">
                                    <label class="flex items-center cursor-pointer">
                                        <input type="radio" name="{{ $field }}" value="B" class="hidden kondisi-radio">
                                        <span class="kondisi-btn w-7 h-7 flex items-center justify-center rounded-lg border-2 border-gray-300 text-gray-400 hover:border-green-500 hover:bg-green-50 hover:text-green-600 transition">
                                            <i class="bi bi-check-lg font-bold text-sm"></i>
                                        </span>
                                    </label>
                                    <label class="flex items-center cursor-pointer">
                                        <input type="radio" name="{{ $field }}" value="R" class="hidden kondisi-radio">
                                        <span class="kondisi-btn w-7 h-7 flex items-center justify-center rounded-lg border-2 border-gray-300 text-gray-400 hover:border-red-500 hover:bg-red-50 hover:text-red-600 transition">
                                            <i class="bi bi-x-lg font-bold text-sm"></i>
                                        </span>
                                    </label>
                                </div>
                            </div>
                            @endif
                            @endforeach
                        </div>
                    </div>

                    {{-- SECTION B: KELENGKAPAN DOKUMEN --}}
                    <div class="mb-4">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="px-2 py-0.5 bg-green-500 text-white text-xs font-bold rounded">B</span>
                            <h5 class="text-sm font-bold text-gray-700">Kelengkapan Dokumen</h5>
                            <span class="text-xs text-gray-500">(9 items)</span>
                        </div>
                        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-2">
                            @foreach($sectionB as $label => $field)
                            @if($field !== 'sim_pengemudi')
                            <div class="flex items-center justify-between p-2 bg-white rounded-lg border border-gray-200">
                                <span class="text-xs font-medium text-gray-700">{{ $label }}</span>
                                <div class="flex gap-1.5">
                                    <label class="flex items-center cursor-pointer">
                                        <input type="radio" name="{{ $field }}" value="B" class="hidden kondisi-radio">
                                        <span class="kondisi-btn w-7 h-7 flex items-center justify-center rounded-lg border-2 border-gray-300 text-gray-400 hover:border-green-500 hover:bg-green-50 hover:text-green-600 transition">
                                            <i class="bi bi-check-lg font-bold text-sm"></i>
                                        </span>
                                    </label>
                                    <label class="flex items-center cursor-pointer">
                                        <input type="radio" name="{{ $field }}" value="R" class="hidden kondisi-radio">
                                        <span class="kondisi-btn w-7 h-7 flex items-center justify-center rounded-lg border-2 border-gray-300 text-gray-400 hover:border-red-500 hover:bg-red-50 hover:text-red-600 transition">
                                            <i class="bi bi-x-lg font-bold text-sm"></i>
                                        </span>
                                    </label>
                                </div>
                            </div>
                            @endif
                            @endforeach
                        </div>
                    </div>

                </div>

                

                {{-- PENANGGUNG JAWAB --}}
                <div class="bg-gray-50 rounded-xl p-4 border border-gray-200">
                    <h4 class="text-base font-bold text-gray-800 mb-3">
                        <i class="bi bi-people-fill text-cyan-500 mr-2"></i>Penanggung Jawab & Tanda Tangan
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1.5">Sopir</label>
                            <input type="text" name="sopir"
                                class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <label class="block text-xs font-semibold text-gray-700 mt-2 mb-1.5">TTD Sopir <span class="text-red-500">*</span></label>
                            <input type="file" name="ttd_supir" required accept="image/*"
                                class="w-full px-2 py-1.5 border border-gray-300 rounded-lg text-xs file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-cyan-50 file:text-cyan-700 hover:file:bg-cyan-100">
                            <p class="text-[10px] text-gray-500 mt-1">JPG, PNG, max 2MB</p>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1.5">Mekanis</label>
                            <input type="text" name="mekanis"
                                class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <label class="block text-xs font-semibold text-gray-700 mt-2 mb-1.5">TTD Mekanik <span class="text-red-500">*</span></label>
                            <input type="file" name="ttd_mekanik" required accept="image/*"
                                class="w-full px-2 py-1.5 border border-gray-300 rounded-lg text-xs file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-cyan-50 file:text-cyan-700 hover:file:bg-cyan-100">
                            <p class="text-[10px] text-gray-500 mt-1">JPG, PNG, max 2MB</p>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-xs font-semibold text-gray-700 mb-1.5">Keterangan</label>
                            <textarea name="keterangan" rows="2" placeholder="Catatan tambahan..."
                                class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
                        </div>
                    </div>
                </div>

            </div>

            {{-- FOOTER BUTTONS --}}
            <div class="flex items-center justify-end gap-3 mt-5 pt-4 border-t border-gray-200 sticky bottom-0 bg-white">
                <button type="button" onclick="closeCeklistModal()"
                    class="px-5 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 font-semibold transition text-sm">
                    Batal
                </button>
                <button type="submit" id="submitCeklistBtn"
                    class="px-5 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-semibold transition text-sm">
                    <i class="bi bi-save mr-1.5"></i>Simpan Ceklist
                </button>
            </div>
        </form>

    </div>
</div>

<script>
// Counter untuk unique ID foto tambahan
let fotoTambahanCount = 1;

// Add Foto Tambahan
function addFotoTambahan() {
    fotoTambahanCount++;
    const container = document.getElementById('fotoTambahanContainer');
    
    const newItem = document.createElement('div');
    newItem.className = 'foto-tambahan-item bg-white rounded-lg p-3 border border-orange-200 relative';
    newItem.innerHTML = `
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1.5">
                    Foto
                </label>
                <input type="file" name="foto_tambahan[]" accept="image/*"
                    class="w-full px-2 py-1.5 border border-gray-300 rounded-lg text-xs file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-orange-50 file:text-orange-700 hover:file:bg-orange-100">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1.5">
                    Keterangan
                </label>
                <input type="text" name="ket_tambahan[]" placeholder="Keterangan foto..."
                    class="w-full px-3 py-1.5 border border-gray-300 rounded-lg text-xs focus:ring-2 focus:ring-orange-500">
            </div>
        </div>
        <button type="button" onclick="removeFotoTambahan(this)" 
            class="absolute top-2 right-2 w-6 h-6 bg-red-100 text-red-600 rounded-full hover:bg-red-200 transition-colors flex items-center justify-center">
            <i class="bi bi-x text-sm font-bold"></i>
        </button>
    `;
    
    container.appendChild(newItem);
    updateRemoveButtons();
}

// Remove Foto Tambahan
function removeFotoTambahan(button) {
    const item = button.closest('.foto-tambahan-item');
    item.remove();
    fotoTambahanCount--;
    updateRemoveButtons();
}

// Update visibility of remove buttons
function updateRemoveButtons() {
    const items = document.querySelectorAll('.foto-tambahan-item');
    items.forEach((item, index) => {
        const removeBtn = item.querySelector('.remove-foto-btn');
        if (removeBtn) {
            if (items.length > 1) {
                removeBtn.classList.remove('hidden');
            } else {
                removeBtn.classList.add('hidden');
            }
        }
        
        // Update tombol remove yang bukan first item
        const allRemoveBtns = item.querySelectorAll('button[onclick^="removeFotoTambahan"]');
        allRemoveBtns.forEach(btn => {
            if (!btn.classList.contains('remove-foto-btn')) {
                // Ini button yang baru ditambah
                if (items.length === 1) {
                    btn.classList.add('hidden');
                } else {
                    btn.classList.remove('hidden');
                }
            }
        });
    });
}

// Open Modal
function openCeklistModal(kendaraanId) {
    document.getElementById('ceklistKendaraanId').value = kendaraanId;
    document.getElementById('ceklistKondisiModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
    
    // Set tanggal otomatis ke hari ini (format YYYY-MM-DD)
    const today = new Date();
    const dateStr = today.getFullYear() + '-' + 
                    String(today.getMonth() + 1).padStart(2, '0') + '-' + 
                    String(today.getDate()).padStart(2, '0');
    document.querySelector('input[name="tanggal"]').value = dateStr;
    
    // Fetch data kendaraan untuk mendapatkan kilometer terbaru
    fetch(`/admin/api/kendaraan/${kendaraanId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.data) {
                // Set kilometer otomatis dari data kendaraan
                const kmInput = document.querySelector('input[name="kilometer"]');
                if (kmInput && data.data.kilometer_sekarang) {
                    kmInput.value = data.data.kilometer_sekarang;
                }
            }
        })
        .catch(error => {
            console.error('Error fetching kendaraan data:', error);
        });
    
    // Init kondisi radio handlers setelah modal terbuka
    initKondisiRadioHandlers();
}

// Close Modal
function closeCeklistModal() {
    document.getElementById('ceklistKondisiModal').classList.add('hidden');
    document.body.style.overflow = 'auto';
    document.getElementById('ceklistKondisiForm').reset();
    
    // Reset kondisi buttons
    const modal = document.getElementById('ceklistKondisiModal');
    if (modal) {
        modal.querySelectorAll('.kondisi-radio').forEach(radio => {
            radio.checked = false;
        });
        modal.querySelectorAll('.kondisi-btn').forEach(btn => {
            btn.classList.remove('border-green-500', 'bg-green-100', 'text-green-600', 'border-red-500', 'bg-red-100', 'text-red-600');
            btn.classList.add('border-gray-300', 'text-gray-400');
        });
    }
}

// Initialize Kondisi Radio Button Handlers with Toggle Support
function initKondisiRadioHandlers() {
    const modal = document.getElementById('ceklistKondisiModal');
    if (!modal) return;
    
    modal.querySelectorAll('.kondisi-btn').forEach(btn => {
        // Remove existing click listeners by cloning
        const newBtn = btn.cloneNode(true);
        btn.parentNode.replaceChild(newBtn, btn);
        
        // Add click listener to the button (not radio)
        newBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const radio = this.previousElementSibling; // hidden radio input
            const parentDiv = this.closest('.flex.gap-1\\.5');
            if (!parentDiv) return;
            
            const allRadios = parentDiv.querySelectorAll('.kondisi-radio');
            const allBtns = parentDiv.querySelectorAll('.kondisi-btn');
            
            // Check if this radio is already checked (for toggle functionality)
            const wasChecked = radio.checked;
            
            if (wasChecked) {
                // TOGGLE OFF: Klik 2x pada yang sama
                radio.checked = false;
                
                // Reset all buttons to gray
                allBtns.forEach(btn => {
                    btn.classList.remove('border-green-500', 'bg-green-100', 'text-green-600', 
                                        'border-red-500', 'bg-red-100', 'text-red-600');
                    btn.classList.add('border-gray-300', 'text-gray-400');
                });
            } else {
                // TOGGLE ON: Pilih yang baru
                // Uncheck all radios in this group first
                allRadios.forEach(r => r.checked = false);
                // Check this radio
                radio.checked = true;
                
                // Reset all buttons
                allBtns.forEach(btn => {
                    btn.classList.remove('border-green-500', 'bg-green-100', 'text-green-600', 
                                        'border-red-500', 'bg-red-100', 'text-red-600');
                    btn.classList.add('border-gray-300', 'text-gray-400');
                });
                
                // Highlight selected button
                if (radio.value === 'B') {
                    this.classList.remove('border-gray-300', 'text-gray-400');
                    this.classList.add('border-green-500', 'bg-green-100', 'text-green-600');
                } else if (radio.value === 'R') {
                    this.classList.remove('border-gray-300', 'text-gray-400');
                    this.classList.add('border-red-500', 'bg-red-100', 'text-red-600');
                }
            }
        });
    });
}

// Form Submission
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('ceklistKondisiForm');
    if (!form) return;
    
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const submitBtn = document.getElementById('submitCeklistBtn');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="bi bi-hourglass-split mr-1.5"></i>Menyimpan...';
        
        const formData = new FormData(this);
        
        // Hapus semua kondisi yang tidak dipilih (radio unchecked)
        const modal = document.getElementById('ceklistKondisiModal');
        const allRadios = modal.querySelectorAll('.kondisi-radio');
        
        // Get all kondisi field names
        const kondisiNames = [];
        allRadios.forEach(radio => {
            if (!kondisiNames.includes(radio.name)) {
                kondisiNames.push(radio.name);
            }
        });
        
        // Check each field - if no radio is checked, delete from formData
        kondisiNames.forEach(name => {
            const checkedRadio = modal.querySelector(`.kondisi-radio[name="${name}"]:checked`);
            if (!checkedRadio) {
                formData.delete(name);
            }
        });
        
        fetch('{{ route("ceklist-kondisi.store") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => {
            console.log('Response status:', response.status);
            console.log('Response headers:', response.headers.get('content-type'));
            
            // Check if response is JSON
            const contentType = response.headers.get('content-type');
            if (contentType && contentType.includes('application/json')) {
                return response.json();
            } else {
                // Response is HTML (probably an error page)
                return response.text().then(html => {
                    console.error('Server returned HTML instead of JSON:');
                    console.error(html.substring(0, 500)); // Show first 500 chars
                    throw new Error('Server error: Response was not JSON. Check console for details.');
                });
            }
        })
        .then(data => {
            if (data.success) {
                // Check if Swal is available
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: data.message,
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => {
                        closeCeklistModal();
                        location.reload();
                    });
                } else {
                    alert(data.message);
                    closeCeklistModal();
                    location.reload();
                }
            } else {
                throw new Error(data.message || 'Terjadi kesalahan');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: error.message || 'Gagal menyimpan data ceklist kondisi'
                });
            } else {
                alert('Error: ' + (error.message || 'Gagal menyimpan data ceklist kondisi'));
            }
        })
        .finally(() => {
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="bi bi-save mr-1.5"></i>Simpan Ceklist';
        });
    });
});
</script>
