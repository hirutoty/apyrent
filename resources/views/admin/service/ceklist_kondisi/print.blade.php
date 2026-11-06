<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ceklist Kondisi Kendaraan - {{ $ceklist->kendaraan->nopol }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        @page {
            size: A4 portrait;
            margin: 0.5cm 0.5cm 0.3cm 0.5cm;
        }

        @media print {
            body {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 7pt;
            line-height: 1.1;
            color: #003366;
        }

        .container {
            width: 100%;
            max-width: 20cm;
            margin: 0 auto;
        }

        /* Info Section */
        .info-section {
            display: table;
            width: 100%;
            margin: 0;
            border: 1px solid #87CEEB;
        }

        .info-row {
            display: table-row;
        }

        .info-cell {
            display: table-cell;
            padding: 2px 4px;
            border: 1px solid #87CEEB;
            font-size: 6pt;
        }

        .info-label {
            font-weight: bold;
            width: 25%;
        }

        /* Photos Section */
        .photos-section {
            margin: 5px 0;
        }

        .photos-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 3px;
            margin-bottom: 5px;
        }

        .photo-item {
            border: 1px solid #87CEEB;
            padding: 2px;
            text-align: center;
        }

        .photo-item img {
            width: 100%;
            height: 80px;
            object-fit: contain;
            display: block;
            margin-bottom: 1px;
        }

        .photo-label {
            font-size: 5pt;
            font-weight: bold;
            margin-bottom: 1px;
        }

        .photo-note {
            font-size: 5pt;
            min-height: 12px;
            text-align: left;
            padding: 1px;
            word-wrap: break-word;
        }

        /* Kondisi Table */
        .kondisi-section {
            margin: 5px 0;
        }

        .kondisi-title {
            background: #ff6600;
            color: white;
            font-weight: bold;
            padding: 2px;
            text-align: center;
            font-size: 8pt;
            margin-bottom: 3px;
        }

        table.kondisi-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 6pt;
        }

        .kondisi-table th,
        .kondisi-table td {
            border: 1px solid #87CEEB;
            padding: 1px 2px;
            text-align: left;
        }

        .kondisi-table th {
            background: #f0f0f0;
            font-weight: bold;
            text-align: center;
        }

        .kondisi-table td.check {
            text-align: center;
            width: 20px;
            font-weight: bold;
        }

        .kondisi-table td.number {
            text-align: center;
            width: 18px;
            font-weight: bold;
        }

        .check-b { color: green; }
        .check-r { color: red; }

        /* TTD Section */
        .ttd-section {
            display: table;
            width: 100%;
            margin-top: 5px;
            border: 1px solid #87CEEB;
        }

        .ttd-row {
            display: table-row;
        }

        .ttd-cell {
            display: table-cell;
            border: 1px solid #87CEEB;
            padding: 3px;
            vertical-align: top;
            width: 50%;
        }

        .ttd-title {
            font-weight: bold;
            text-align: center;
            margin-bottom: 2px;
            font-size: 7pt;
        }

        .ttd-img {
            text-align: center;
            margin: 3px 0;
        }

        .ttd-img img {
            max-width: 100px;
            max-height: 40px;
            object-fit: contain;
        }

        .ttd-name {
            text-align: center;
            font-size: 6pt;
            margin-top: 2px;
        }

        /* Keterangan */
        .keterangan-section {
            margin-top: 5px;
            border: 1px solid #87CEEB;
            padding: 3px;
        }

        .keterangan-title {
            font-weight: bold;
            font-size: 6pt;
            margin-bottom: 2px;
        }

        .keterangan-text {
            font-size: 6pt;
            min-height: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        {{-- HEADER dengan Logo dan Info Perusahaan --}}
        <div style="border: 2px solid #87CEEB; padding: 3px; margin-bottom: 3px;">
            <table style="width: 100%; border-collapse: collapse;">
                <tr>
                    <td style="width: 75%; vertical-align: top;">
                        <table style="width: 100%; border-collapse: collapse;">
                            <tr>
                                <td style="width: 30%; vertical-align: top;">
                                    @if(!empty($logoSrc))
                                        <img src="{{ $logoSrc }}" 
                                             style="max-width: 100px; max-height: 80px; object-fit: contain;" 
                                             alt="Logo">
                                    @endif
                                </td>
                                <td style="width: 70%; vertical-align: middle; padding-left: 8px;">
                                    <div style="font-size: 8pt; font-weight: bold; margin-bottom: 2px;">
                                        {{ $setting->nama_perusahaan ?? 'PT. ANUGERAH PANCA YOGA' }}
                                    </div>
                                    <div style="font-size: 5pt; line-height: 1.2;">
                                        @if($setting)
                                            {{-- Baris 1: Head Office & Branch --}}
                                            <div style="margin-bottom: 1px;">
                                                <strong>Head Office:</strong> {{ $setting->alamat ?? '' }}
                                                @if($setting->alamat_cabang)
                                                    | <strong>Branch:</strong> {{ $setting->alamat_cabang }}
                                                @endif
                                            </div>
                                            {{-- Baris 2: Telp, Email, Website --}}
                                            <div>
                                                <strong>Telp:</strong> {{ $setting->telepon ?? '' }} | 
                                                <strong>Email:</strong> {{ $setting->email ?? '' }} | 
                                                <strong>Website:</strong> {{ $setting->website ?? '' }}
                                            </div>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </td>
                    <td style="width: 25%; vertical-align: top; text-align: right; padding-left: 8px;">
                        <div style="border: 2px solid #003366; padding: 3px; text-align: center;">
                            <div style="font-size: 9pt; font-weight: bold; color: #003366;">CHECKLIST</div>
                            <div style="font-size: 7pt; margin-top: 1px; color: #ff6600;">Pool Kendaraan</div>
                        </div>
                        
                        {{-- Checkbox Jenis --}}
                        <div style="margin-top: 3px; font-size: 5pt; text-align: left; display: flex; flex-wrap: nowrap; gap: 2px; align-items: center;">
                            <span style="font-weight: bold; margin-right: 2px;">Status:</span>
                            @php
                            $jenisOptions = ['status', 'Leasing', 'GS', 'Replace', 'Expedisi'];
                            @endphp
                            @foreach($jenisOptions as $jenis)
                            <div style="display: inline-block; white-space: nowrap;">
                                <span style="display: inline-block; width: 8px; height: 8px; border: 1px solid #87CEEB; margin-right: 1px; text-align: center; line-height: 8px; vertical-align: middle; font-size: 5pt;">
                                    @if(strtolower($ceklist->status ?? '') === strtolower($jenis))
                                        ✓
                                    @endif
                                </span>
                                <span style="vertical-align: middle;">{{ $jenis }}</span>
                            </div>
                            @endforeach
                        </div>
                    </td>
                </tr>
            </table>
        </div>

        {{-- Info Kendaraan (2 kolom besar) --}}
        <div class="info-section">
            <div class="info-row">
                {{-- KOLOM 1 --}}
                <div class="info-cell" style="width: 50%; vertical-align: top; padding: 3px;">
                    <div style="margin-bottom: 3px; padding-bottom: 3px; border-bottom: 1px solid #ddd;">
                        <div style="font-weight: bold; font-size: 5pt; color: #666; margin-bottom: 1px;">Nama</div>
                        <div style="font-size: 6pt;">{{ $ceklist->nama }}</div>
                    </div>
                    <div style="margin-bottom: 3px; padding-bottom: 3px; border-bottom: 1px solid #ddd;">
                        <div style="font-weight: bold; font-size: 5pt; color: #666; margin-bottom: 1px;">Perusahaan</div>
                        <div style="font-size: 6pt;">{{ $ceklist->perusahaan }}</div>
                    </div>
                    <div style="margin-bottom: 3px; padding-bottom: 3px; border-bottom: 1px solid #ddd;">
                        <div style="font-weight: bold; font-size: 5pt; color: #666; margin-bottom: 1px;">Alamat</div>
                        <div style="font-size: 6pt;">{{ $ceklist->alamat }}</div>
                    </div>
                    <div>
                        <div style="font-weight: bold; font-size: 5pt; color: #666; margin-bottom: 1px;">No Telephone</div>
                        <div style="font-size: 6pt;">{{ $ceklist->no_telephone }}</div>
                    </div>
                </div>
                
                {{-- KOLOM 2 --}}
                <div class="info-cell" style="width: 50%; vertical-align: top; padding: 3px; border-left: 1px solid #000;">
                    <div style="margin-bottom: 3px; padding-bottom: 3px; border-bottom: 1px solid #ddd;">
                        <div style="font-weight: bold; font-size: 5pt; color: #666; margin-bottom: 1px;">Tanggal</div>
                        <div style="font-size: 6pt;">{{ \Carbon\Carbon::parse($ceklist->tanggal)->format('d/m/Y') }}</div>
                    </div>
                    <div style="margin-bottom: 3px; padding-bottom: 3px; border-bottom: 1px solid #ddd;">
                        <div style="font-weight: bold; font-size: 5pt; color: #666; margin-bottom: 1px;">Kilometer</div>
                        <div style="font-size: 6pt;">{{ number_format($ceklist->kilometer, 0, ',', '.') }} km</div>
                    </div>
                    <div style="margin-bottom: 3px; padding-bottom: 3px; border-bottom: 1px solid #ddd;">
                        <div style="font-weight: bold; font-size: 5pt; color: #666; margin-bottom: 1px;">Jam</div>
                        <div style="font-size: 6pt;">{{ $ceklist->jam }}</div>
                    </div>
                    <div style="margin-bottom: 3px; padding-bottom: 3px; border-bottom: 1px solid #ddd;">
                        <div style="font-weight: bold; font-size: 5pt; color: #666; margin-bottom: 1px;">Fuel</div>
                        <div style="font-size: 6pt;">{{ $ceklist->fuel }}</div>
                    </div>
                    <div>
                        <div style="font-weight: bold; font-size: 5pt; color: #666; margin-bottom: 1px;">Pemeriksa</div>
                        <div style="font-size: 6pt;">{{ $ceklist->pemeriksa }}</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Info Tambahan (2 baris dengan grouping) --}}
        @if($ceklist->kendaraan->merk || $ceklist->fuel || $ceklist->pemakaian_bbm || $ceklist->ratio || $ceklist->status)
        <div style="margin-top: 3px; border: 1px solid #87CEEB; font-size: 5pt;">
            {{-- Baris 1: Info Kendaraan --}}
            <div style="padding: 3px; display: flex; gap: 10px; flex-wrap: wrap; border-bottom: 1px solid #87CEEB;">
                @if($ceklist->kendaraan->merk || $ceklist->kendaraan->nopol)
                <div>
                    <strong>Nama Kendaraan:</strong> 
                    {{ $ceklist->kendaraan->merk ?? '-' }}
                    @if($ceklist->kendaraan->nopol)
                        - {{ $ceklist->kendaraan->nopol }}
                    @endif
                </div>
                @endif
                @if($ceklist->kendaraan->warna)
                <div>
                    <strong>Warna:</strong> {{ $ceklist->kendaraan->warna }}
                </div>
                @endif
                @if($ceklist->kendaraan->no_rangka)
                <div>
                    <strong>No. Rangka:</strong> {{ $ceklist->kendaraan->no_rangka }}
                </div>
                @endif
                @if($ceklist->kendaraan->no_mesin)
                <div>
                    <strong>No. Mesin:</strong> {{ $ceklist->kendaraan->no_mesin }}
                </div>
                @endif
            </div>
            
            {{-- Baris 2: Info BBM --}}
            <div style="padding: 3px; display: flex; gap: 10px; flex-wrap: wrap;">
                @if($ceklist->kendaraan->bahan_bakar)
                <div>
                    <strong>Bahan Bakar:</strong> {{ $ceklist->kendaraan->bahan_bakar }}
                </div>
                @endif
                @if($ceklist->fuel)
                <div>
                    <strong>Fuel:</strong> {{ $ceklist->fuel }}
                </div>
                @endif
                @if($ceklist->ratio)
                <div>
                    <strong>Ratio:</strong> {{ $ceklist->ratio }}
                </div>
                @endif
            </div>
        </div>
        @endif

        {{-- Photos --}}
        <div class="photos-section">
            <div class="photos-grid">
                @if($ceklist->foto_depan)
                <div class="photo-item">
                    <div class="photo-label">A. DEPAN</div>
                    <img src="{{ asset('storage/' . $ceklist->foto_depan) }}" alt="Foto Depan">
                    <div class="photo-note">{{ $ceklist->ket_depan ?? '-' }}</div>
                </div>
                @endif

                @if($ceklist->foto_atas)
                <div class="photo-item">
                    <div class="photo-label">B. ATAS</div>
                    <img src="{{ asset('storage/' . $ceklist->foto_atas) }}" alt="Foto Atas">
                    <div class="photo-note">{{ $ceklist->ket_atas ?? '-' }}</div>
                </div>
                @endif

                @if($ceklist->foto_belakang)
                <div class="photo-item">
                    <div class="photo-label">C. BELAKANG</div>
                    <img src="{{ asset('storage/' . $ceklist->foto_belakang) }}" alt="Foto Belakang">
                    <div class="photo-note">{{ $ceklist->ket_belakang ?? '-' }}</div>
                </div>
                @endif

                @if($ceklist->foto_sampingkiri)
                <div class="photo-item">
                    <div class="photo-label">D. SAMPING KIRI</div>
                    <img src="{{ asset('storage/' . $ceklist->foto_sampingkiri) }}" alt="Foto Samping Kiri">
                    <div class="photo-note">{{ $ceklist->ket_sampingkiri ?? '-' }}</div>
                </div>
                @endif

                @if($ceklist->foto_sampingkanan)
                <div class="photo-item">
                    <div class="photo-label">E. SAMPING KANAN</div>
                    <img src="{{ asset('storage/' . $ceklist->foto_sampingkanan) }}" alt="Foto Samping Kanan">
                    <div class="photo-note">{{ $ceklist->ket_sampingkanan ?? '-' }}</div>
                </div>
                @endif
            </div>
            
            {{-- Accesoris Tambahan (langsung text tanpa ruang foto) --}}
            @if($ceklist->accecoris_tambahan)
            <div style="margin-top: 3px; padding: 3px; border: 1px solid #87CEEB; border-radius: 2px;">
                <div style="font-size: 5pt; font-weight: bold; margin-bottom: 1px;">F. ACCESORIS TAMBAHAN:</div>
                <div style="font-size: 5pt; color: #333;">{{ $ceklist->accecoris_tambahan }}</div>
            </div>
            @endif
        </div>

        {{-- Kondisi Kendaraan Table --}}
        <div class="kondisi-section">
            <div class="kondisi-title">II. KONDISI KENDARAAN</div>
            <table class="kondisi-table">
                <thead>
                    <tr>
                        <th style="width: 20px;">NO</th>
                        <th>KETERANGAN</th>
                        <th style="width: 25px;">B</th>
                        <th style="width: 25px;">R</th>
                        <th style="width: 20px;">NO</th>
                        <th>KETERANGAN</th>
                        <th style="width: 25px;">B</th>
                        <th style="width: 25px;">R</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                    // LAYOUT FINAL:
                    // KIRI: Section A partial (1-13) = 26 items (sampai karpet lantai)
                    // KANAN: Section A partial (lanjutan perlengkapan + 14-15) + Section B (16-36) + Section C (37-46) = 40 items
                    
                    // SECTION A KIRI: Kondisi Dasar (26 items) - KIRI
                    $sectionA = [
                        [1, 'Kondisi mesin', 'kondisi_mesin'],
                        [2, 'Kondisi rem', 'kondisi_rem'],
                        [3, 'Kondisi kopling', 'kondisi_kopling'],
                        [4, 'Kondisi ban', 'kondisi_ban'],
                        [null, '- Ban depan kanan', 'ban_depan_kanan'],
                        [null, '- Ban depan kiri', 'ban_depan_kiri'],
                        [null, '- Ban belakang kanan', 'ban_belakang_kanan'],
                        [null, '- Ban belakang kiri', 'ban_belakang_kiri'],
                        [null, '- ban ganda depan kanan', 'ban_ganda_depan_kanan'],
                        [null, '- ban ganda depan kiri', 'ban_ganda_depan_kiri'],
                        [null, '- ban ganda belakang kanan', 'ban_ganda_belakang_kanan'],
                        [null, '- ban ganda belakang kiri', 'ban_ganda_belakang_kiri'],
                        [5, 'Kondisi AC', 'kondisi_ac'],
                        [6, 'Lampu', 'kondisi_lampu'],
                        [null, '- Lampu sein', 'lampu_sein'],
                        [null, '- Lampu bik / plat', 'lampu_bik_plat'],
                        [null, '- Lampu hazard', 'lampu_hazard'],
                        [null, '- Lampu kota', 'lampu_kota'],
                        [null, '- Lampu besar / kecil', 'lampu_besar_kecil'],
                        [null, '- Lampu mundur', 'lampu_mundur'],
                        [null, '- Lampu rem belakang', 'lampu_rem_belakang'],
                        [null, '- Lampu rem depan', 'lampu_rem_depan'],
                        [7, 'Accu', 'accu'],
                        [8, 'Air radiator', 'airradiator'],
                        [9, 'Oli mesin / rem / kopling', 'olimesin_remkopling'],
                        [10, 'Oli power steering', 'olipower_steering'],
                        [11, 'Kebersihan interior', 'kebersihan_interior'],
                        [12, 'Kerapihan body / cat', 'kerapihanbody_cat'],
                        [13, 'Perlengkapan', 'perlengkapan'],
                        [null, '- Ban cadang', 'ban_cadang'],
                        [null, '- Dongkrak', 'dongkrak'],
                        [null, '- Kunci roda', 'kunciroda'],
                        [null, '- Radio / Tape', 'radio_tape'],
                        [null, '- Merk', 'merk'],
                        [null, '- Karpet lantai', 'karpet_lantai'],
                    ];
                    
                    // SECTION A KANAN + B + C: (40 items) - KANAN
                    $sectionAKanan = [
                        [null, '- Dop roda', 'doproda'],
                        [null, '- Pematik api', 'pematikapi'],
                        [null, '- Segitiga pengaman', 'segitiga_pengaman'],
                        [null, '- Kotak obat', 'kotakobat'],
                        [14, 'Kunci pintu / kontak', 'kuncipintu'],
                        [15, 'Speedometer', 'spedoometer'],
                    ];
                    
                    // SECTION B: Pemeriksaan Khusus (21 items) - KANAN
                    $sectionB = [
                        [16, 'Sistem rem angin', 'sistem_rem_angin'],
                        [17, 'Tekanan angin tabung', 'tekanan_angin_tabung'],
                        [18, 'Kompresor angin dan saluran', 'kompresor_angin_saluran'],
                        [19, 'Brake chamber dan slack adjuster', 'brake_chamber_slack_adjuster'],
                        [20, 'Rem parkir / tangan', 'rem_parkir_tangan'],
                        [21, 'Transmisi / persneling', 'transmisi_persneling'],
                        [22, 'Gardan dan kebocoran oli', 'gardan_kebocoran_oli'],
                        [23, 'Kopel / propeller / universal joint', 'kopel_propeller_universal'],
                        [24, 'Suspensi / per daun', 'suspensi_per_daun'],
                        [25, 'Shock absorber', 'shock_absorber'],
                        [26, 'Chassis / rangka dan baut', 'chassis_rangka_baut'],
                        [27, 'Mur / baut roda dan velg', 'mur_baut_roda_velg'],
                        [28, 'Bak / box / karoseri / lantai', 'bak_box_karoseri_lantai'],
                        [29, 'Pintu bak dan sistem pengunci', 'pintu_bak_sistem_pengunci'],
                        [30, 'Engsel / pengait / pengaman', 'engsel_pengait_pengaman'],
                        [31, 'Terpal / tali pengikat', 'terpal_tali_pengikat'],
                        [32, 'Wiper dan washer', 'wiper_washer'],
                        [33, 'Kaca spion dan kaca kendaraan', 'kaca_spion_kendaraan'],
                        [34, 'Sabuk pengaman', 'sabuk_pengaman'],
                        [35, 'Ganjal roda', 'ganjal_roda'],
                        [36, 'APAR (alat pemadam api ringan)', 'apar'],
                    ];
                    
                    // SECTION C: Kelengkapan Dokumen (10 items) - SEMUA DI KANAN
                    $sectionC = [
                        [37, 'STNK kendaraan', 'stnk'],
                        [38, 'Buku / kartu uji berkala (KIR)', 'kir_berlaku'],
                        [39, 'SIM pengemudi sesuai golongan', 'sim_pengemudi'],
                        [40, '- SIM A', 'sim_a'],
                        [41, '- SIM B1', 'sim_b1'],
                        [42, '- SIM B1 Umum', 'sim_b1_umum'],
                        [43, '- SIM B2', 'sim_b2'],
                        [44, '- SIM B2 Umum', 'sim_b2_umum'],
                        [45, 'Dokumen pengangkutan / surat jalan', 'dokumen_surat_jalan'],
                        [46, 'Kesesuaian jenis dan kapasitas muatan', 'kesesuaian_muatan'],
                    ];
                    
                    // Merge sections
                    // KIRI: Section A partial (26 items)
                    $allLeft = $sectionA;
                    
                    // KANAN: Section A partial (6 items) + Section B (21 items) + Section C (10 items) = 37 items
                    $allRight = array_merge($sectionAKanan, $sectionB, $sectionC);
                    
                    $maxRows = max(count($allLeft), count($allRight));
                    @endphp

                    {{-- Generate rows --}}
                    @for($i = 0; $i < $maxRows; $i++)
                        <tr>
                            {{-- Kolom Kiri --}}
                            @if($i < count($allLeft))
                                <td class="number">{{ $allLeft[$i][0] }}</td>
                                <td>{{ $allLeft[$i][1] }}</td>
                                <td class="check">
                                    @if($allLeft[$i][2] && $ceklist->{$allLeft[$i][2]} === 'B')
                                        <span class="check-b">✓</span>
                                    @endif
                                </td>
                                <td class="check">
                                    @if($allLeft[$i][2] && $ceklist->{$allLeft[$i][2]} === 'R')
                                        <span class="check-r">✗</span>
                                    @endif
                                </td>
                            @else
                                <td colspan="4"></td>
                            @endif

                            {{-- Kolom Kanan --}}
                            @if($i < count($allRight))
                                <td class="number">{{ $allRight[$i][0] }}</td>
                                <td>{{ $allRight[$i][1] }}</td>
                                <td class="check">
                                    @if($allRight[$i][2] && $ceklist->{$allRight[$i][2]} === 'B')
                                        <span class="check-b">✓</span>
                                    @endif
                                </td>
                                <td class="check">
                                    @if($allRight[$i][2] && $ceklist->{$allRight[$i][2]} === 'R')
                                        <span class="check-r">✗</span>
                                    @endif
                                </td>
                            @else
                                <td colspan="4"></td>
                            @endif
                        </tr>
                    @endfor
                </tbody>
            </table>
        </div>

        {{-- Keterangan --}}
        @if($ceklist->keterangan)
        <div class="keterangan-section">
            <div class="keterangan-title">KETERANGAN:</div>
            <div class="keterangan-text">{{ $ceklist->keterangan }}</div>
        </div>
        @endif

        {{-- TTD Section --}}
        <div class="ttd-section">
            <div class="ttd-row">
                {{-- Sopir --}}
                <div class="ttd-cell">
                    <div class="ttd-title">SOPIR</div>
                    @if($ceklist->ttd_supir)
                    <div class="ttd-img">
                        <img src="{{ asset('storage/' . $ceklist->ttd_supir) }}" alt="TTD Sopir">
                    </div>
                    @endif
                    <div class="ttd-name">
                        ( {{ $ceklist->sopir ?? '_______________' }} )
                    </div>
                </div>

                {{-- Mekanik --}}
                <div class="ttd-cell">
                    <div class="ttd-title">MEKANIK</div>
                    @if($ceklist->ttd_mekanik)
                    <div class="ttd-img">
                        <img src="{{ asset('storage/' . $ceklist->ttd_mekanik) }}" alt="TTD Mekanik">
                    </div>
                    @endif
                    <div class="ttd-name">
                        ( {{ $ceklist->mekanis ?? '_______________' }} )
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Force cache bust - v2.0
        console.log('Ceklist Kondisi Print - Version 2.0 - <?php echo time(); ?>');
        
        // Auto print on load
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>
