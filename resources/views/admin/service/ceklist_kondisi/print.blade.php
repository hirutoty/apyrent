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
            size: 21.5cm 33cm;
            margin: 0.3cm;
        }

        @media print {
            body {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 9pt;
            line-height: 1.2;
            color: #003366;
        }

        .container {
            width: 100%;
            max-width: 19.5cm;
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
            padding: 3px 5px;
            border: 1px solid #87CEEB;
            font-size: 8pt;
        }

        .info-label {
            font-weight: bold;
            width: 25%;
        }

        /* Photos Section */
        .photos-section {
            margin: 10px 0;
        }

        .photos-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 5px;
            margin-bottom: 8px;
        }

        .photo-item {
            border: 1px solid #87CEEB;
            padding: 3px;
            text-align: center;
        }

        .photo-item img {
            width: 100%;
            height: 120px;
            object-fit: contain;
            display: block;
            margin-bottom: 2px;
        }

        .photo-label {
            font-size: 7pt;
            font-weight: bold;
            margin-bottom: 2px;
        }

        .photo-note {
            font-size: 7pt;
            min-height: 20px;
            text-align: left;
            padding: 2px;
            word-wrap: break-word;
        }

        /* Kondisi Table */
        .kondisi-section {
            margin: 10px 0;
        }

        .kondisi-title {
            background: #ff6600;
            color: white;
            font-weight: bold;
            padding: 4px;
            text-align: center;
            font-size: 10pt;
            margin-bottom: 5px;
        }

        table.kondisi-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 7pt;
        }

        .kondisi-table th,
        .kondisi-table td {
            border: 1px solid #87CEEB;
            padding: 2px 3px;
            text-align: left;
        }

        .kondisi-table th {
            background: #f0f0f0;
            font-weight: bold;
            text-align: center;
        }

        .kondisi-table td.check {
            text-align: center;
            width: 25px;
            font-weight: bold;
        }

        .kondisi-table td.number {
            text-align: center;
            width: 20px;
            font-weight: bold;
        }

        .check-b { color: green; }
        .check-r { color: red; }

        /* TTD Section */
        .ttd-section {
            display: table;
            width: 100%;
            margin-top: 10px;
            border: 1px solid #87CEEB;
        }

        .ttd-row {
            display: table-row;
        }

        .ttd-cell {
            display: table-cell;
            border: 1px solid #87CEEB;
            padding: 5px;
            vertical-align: top;
            width: 50%;
        }

        .ttd-title {
            font-weight: bold;
            text-align: center;
            margin-bottom: 3px;
            font-size: 9pt;
        }

        .ttd-img {
            text-align: center;
            margin: 5px 0;
        }

        .ttd-img img {
            max-width: 150px;
            max-height: 60px;
            object-fit: contain;
        }

        .ttd-name {
            text-align: center;
            font-size: 8pt;
            margin-top: 3px;
        }

        /* Keterangan */
        .keterangan-section {
            margin-top: 8px;
            border: 1px solid #87CEEB;
            padding: 5px;
        }

        .keterangan-title {
            font-weight: bold;
            font-size: 8pt;
            margin-bottom: 3px;
        }

        .keterangan-text {
            font-size: 7pt;
            min-height: 30px;
        }
    </style>
</head>
<body>
    <div class="container">
        {{-- HEADER dengan Logo dan Info Perusahaan --}}
        <div style="border: 2px solid #87CEEB; padding: 5px; margin-bottom: 5px;">
            <table style="width: 100%; border-collapse: collapse;">
                <tr>
                    <td style="width: 75%; vertical-align: top;">
                        <table style="width: 100%; border-collapse: collapse;">
                            <tr>
                                <td style="width: 30%; vertical-align: top;">
                                    @if(!empty($logoSrc))
                                        <img src="{{ $logoSrc }}" 
                                             style="max-width: 150px; max-height: 120px; object-fit: contain;" 
                                             alt="Logo">
                                    @endif
                                </td>
                                <td style="width: 70%; vertical-align: middle; padding-left: 10px;">
                                    <div style="font-size: 10pt; font-weight: bold; margin-bottom: 3px;">
                                        {{ $setting->nama_perusahaan ?? 'PT. ANUGERAH PANCA YOGA' }}
                                    </div>
                                    <div style="font-size: 7pt; line-height: 1.3;">
                                        @if($setting)
                                            {{-- Baris 1: Head Office & Branch --}}
                                            <div style="margin-bottom: 2px;">
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
                    <td style="width: 25%; vertical-align: top; text-align: right; padding-left: 10px;">
                        <div style="border: 2px solid #003366; padding: 5px; text-align: center;">
                            <div style="font-size: 11pt; font-weight: bold; color: #003366;">CHECKLIST</div>
                            <div style="font-size: 9pt; margin-top: 2px; color: #ff6600;">Pool Kendaraan</div>
                        </div>
                        
                        {{-- Checkbox Jenis --}}
                        <div style="margin-top: 5px; font-size: 6pt; text-align: left; display: flex; flex-wrap: nowrap; gap: 3px; align-items: center;">
                            <span style="font-weight: bold; margin-right: 3px;">Status:</span>
                            @php
                            $jenisOptions = ['status', 'Leasing', 'GS', 'Replace', 'Expedisi'];
                            @endphp
                            @foreach($jenisOptions as $jenis)
                            <div style="display: inline-block; white-space: nowrap;">
                                <span style="display: inline-block; width: 8px; height: 8px; border: 1px solid #87CEEB; margin-right: 2px; text-align: center; line-height: 8px; vertical-align: middle; font-size: 6pt;">
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
                <div class="info-cell" style="width: 50%; vertical-align: top; padding: 5px;">
                    <div style="margin-bottom: 5px; padding-bottom: 5px; border-bottom: 1px solid #ddd;">
                        <div style="font-weight: bold; font-size: 7pt; color: #666; margin-bottom: 2px;">Nama</div>
                        <div style="font-size: 8pt;">{{ $ceklist->nama }}</div>
                    </div>
                    <div style="margin-bottom: 5px; padding-bottom: 5px; border-bottom: 1px solid #ddd;">
                        <div style="font-weight: bold; font-size: 7pt; color: #666; margin-bottom: 2px;">Perusahaan</div>
                        <div style="font-size: 8pt;">{{ $ceklist->perusahaan }}</div>
                    </div>
                    <div style="margin-bottom: 5px; padding-bottom: 5px; border-bottom: 1px solid #ddd;">
                        <div style="font-weight: bold; font-size: 7pt; color: #666; margin-bottom: 2px;">Alamat</div>
                        <div style="font-size: 8pt;">{{ $ceklist->alamat }}</div>
                    </div>
                    <div>
                        <div style="font-weight: bold; font-size: 7pt; color: #666; margin-bottom: 2px;">No Telephone</div>
                        <div style="font-size: 8pt;">{{ $ceklist->no_telephone }}</div>
                    </div>
                </div>
                
                {{-- KOLOM 2 --}}
                <div class="info-cell" style="width: 50%; vertical-align: top; padding: 5px; border-left: 1px solid #000;">
                    <div style="margin-bottom: 5px; padding-bottom: 5px; border-bottom: 1px solid #ddd;">
                        <div style="font-weight: bold; font-size: 7pt; color: #666; margin-bottom: 2px;">Tanggal</div>
                        <div style="font-size: 8pt;">{{ \Carbon\Carbon::parse($ceklist->tanggal)->format('d/m/Y') }}</div>
                    </div>
                    <div style="margin-bottom: 5px; padding-bottom: 5px; border-bottom: 1px solid #ddd;">
                        <div style="font-weight: bold; font-size: 7pt; color: #666; margin-bottom: 2px;">Kilometer</div>
                        <div style="font-size: 8pt;">{{ number_format($ceklist->kilometer, 0, ',', '.') }} km</div>
                    </div>
                    <div style="margin-bottom: 5px; padding-bottom: 5px; border-bottom: 1px solid #ddd;">
                        <div style="font-weight: bold; font-size: 7pt; color: #666; margin-bottom: 2px;">Jam</div>
                        <div style="font-size: 8pt;">{{ $ceklist->jam }}</div>
                    </div>
                    <div style="margin-bottom: 5px; padding-bottom: 5px; border-bottom: 1px solid #ddd;">
                        <div style="font-weight: bold; font-size: 7pt; color: #666; margin-bottom: 2px;">Fuel</div>
                        <div style="font-size: 8pt;">{{ $ceklist->fuel }}</div>
                    </div>
                    <div>
                        <div style="font-weight: bold; font-size: 7pt; color: #666; margin-bottom: 2px;">Pemeriksa</div>
                        <div style="font-size: 8pt;">{{ $ceklist->pemeriksa }}</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Info Tambahan (2 baris dengan grouping) --}}
        @if($ceklist->kendaraan->merk || $ceklist->fuel || $ceklist->pemakaian_bbm || $ceklist->ratio || $ceklist->status)
        <div style="margin-top: 5px; border: 1px solid #87CEEB; font-size: 7pt;">
            {{-- Baris 1: Info Kendaraan --}}
            <div style="padding: 5px; display: flex; gap: 15px; flex-wrap: wrap; border-bottom: 1px solid #87CEEB;">
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
            <div style="padding: 5px; display: flex; gap: 15px; flex-wrap: wrap;">
                @if($ceklist->fuel)
                <div>
                    <strong>Fuel:</strong> {{ $ceklist->fuel }}
                </div>
                @endif
                @if($ceklist->pemakaian_bbm)
                <div>
                    <strong>Pemakaian BBM:</strong> {{ $ceklist->pemakaian_bbm }}
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
            <div style="margin-top: 5px; padding: 5px; border: 1px solid #87CEEB; border-radius: 3px;">
                <div style="font-size: 7pt; font-weight: bold; margin-bottom: 2px;">F. ACCESORIS TAMBAHAN:</div>
                <div style="font-size: 7pt; color: #333;">{{ $ceklist->accecoris_tambahan }}</div>
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
                    $kondisiLeft = [
                        [1, 'Kondisi mesin', 'kondisi_mesin'],
                        [2, 'Kondisi rem', 'kondisi_rem'],
                        [3, 'Kondisi kopling', 'kondisi_kopling'],
                        [4, 'Kondisi ban', 'kondisi_ban'],
                        [5, 'Kondisi AC', 'kondisi_ac'],
                        [6, 'Lampu sen', 'lampu_lampusen'],
                        [7, 'Lampu besar / kecil', 'lampu_lampubesarkecil'],
                        [8, 'Lampu rem / bik / plat no', 'lampu_rembikplat'],
                        [9, 'Accu', 'accu'],
                        [10, 'Air radiator', 'airradiator'],
                        [11, 'Oli mesin / rem / kopling', 'olimesin_remkopling'],
                        [12, 'Oli power steering', 'olipower_steering'],
                        [13, 'Kebersihan interior', 'kebersihan_interior'],
                        [14, 'Kerapihan body / cat', 'kerapihanbody_cat'],
                    ];
                    
                    $kondisiRight = [
                        [15, 'Perlengkapan', 'perlengkapan'],
                        [null, '- Ban cadang', 'ban_cadang'],
                        [null, '- Dongkrak', 'dongkrak'],
                        [null, '- Kunci roda', 'kunciroda'],
                        [null, '- Radio / Tape', 'radio_tape'],
                        [null, '- Merk', 'merk'],
                        [null, '- Karpet lantai', 'karpet_lantai'],
                        [null, '- Dop roda', 'doproda'],
                        [null, '- Pematik api', 'pematikapi'],
                        [null, '- Segitiga pengaman', 'segitiga_pengaman'],
                        [null, '- Kotak obat', 'kotakobat'],
                        [null, '- STNK', 'stnk'],
                        [16, 'Kunci pintu / kontak', 'kuncipintu'],
                        [17, 'Speedometer', 'spedoometer'],
                    ];
                    @endphp

                    {{-- Baris 1-14: Kolom kiri dengan items, kolom kanan dengan 16, 17, 15, dan sub-items --}}
                    @for($i = 0; $i < 14; $i++)
                        <tr>
                            {{-- Kolom Kiri --}}
                            <td class="number">{{ $kondisiLeft[$i][0] }}</td>
                            <td>{{ $kondisiLeft[$i][1] }}</td>
                            <td class="check">
                                @if($ceklist->{$kondisiLeft[$i][2]} === 'B')
                                    <span class="check-b">✓</span>
                                @endif
                            </td>
                            <td class="check">
                                @if($ceklist->{$kondisiLeft[$i][2]} === 'R')
                                    <span class="check-r">✗</span>
                                @endif
                            </td>

                            {{-- Kolom Kanan --}}
                            @if($i < count($kondisiRight))
                                <td class="number">{{ $kondisiRight[$i][0] }}</td>
                                <td>{{ $kondisiRight[$i][1] }}</td>
                                <td class="check">
                                    @if($kondisiRight[$i][2] && $ceklist->{$kondisiRight[$i][2]} === 'B')
                                        <span class="check-b">✓</span>
                                    @endif
                                </td>
                                <td class="check">
                                    @if($kondisiRight[$i][2] && $ceklist->{$kondisiRight[$i][2]} === 'R')
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
        // Auto print on load
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>
