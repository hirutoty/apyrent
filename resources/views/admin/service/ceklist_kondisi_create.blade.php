@extends('admin.layouts.app')

@section('content')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>{{ isset($ceklist) ? 'Edit' : 'Tambah' }} Ceklist Kondisi Kendaraan</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.ceklist-kondisi.index') }}">Ceklist Kondisi</a></li>
                        <li class="breadcrumb-item active">{{ isset($ceklist) ? 'Edit' : 'Tambah' }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <form action="{{ isset($ceklist) ? route('admin.ceklist-kondisi.update', $ceklist->id) : route('admin.ceklist-kondisi.store') }}" 
                  method="POST" enctype="multipart/form-data">
                @csrf
                @if(isset($ceklist))
                    @method('PUT')
                @endif

                <!-- Informasi Dasar -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Informasi Dasar</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Nama <span class="text-danger">*</span></label>
                                    <input type="text" name="nama" class="form-control @error('nama') is-invalid @enderror" 
                                           value="{{ old('nama', $ceklist->nama ?? '') }}" required>
                                    @error('nama')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Perusahaan <span class="text-danger">*</span></label>
                                    <input type="text" name="perusahaan" class="form-control @error('perusahaan') is-invalid @enderror" 
                                           value="{{ old('perusahaan', $ceklist->perusahaan ?? '') }}" required>
                                    @error('perusahaan')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Alamat <span class="text-danger">*</span></label>
                                    <textarea name="alamat" class="form-control @error('alamat') is-invalid @enderror" 
                                              rows="3" required>{{ old('alamat', $ceklist->alamat ?? '') }}</textarea>
                                    @error('alamat')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>No. Telephone <span class="text-danger">*</span></label>
                                    <input type="text" name="no_telephone" class="form-control @error('no_telephone') is-invalid @enderror" 
                                           value="{{ old('no_telephone', $ceklist->no_telephone ?? '') }}" required>
                                    @error('no_telephone')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Data Pemeriksaan -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Data Pemeriksaan</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Tanggal <span class="text-danger">*</span></label>
                                    <input type="date" name="tanggal" class="form-control @error('tanggal') is-invalid @enderror" 
                                           value="{{ old('tanggal', isset($ceklist) ? $ceklist->tanggal : date('Y-m-d')) }}" required>
                                    @error('tanggal')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Kilometer <span class="text-danger">*</span></label>
                                    <input type="text" name="kilometer" class="form-control @error('kilometer') is-invalid @enderror" 
                                           value="{{ old('kilometer', $ceklist->kilometer ?? '') }}" required>
                                    @error('kilometer')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Jam <span class="text-danger">*</span></label>
                                    <input type="time" name="jam" class="form-control @error('jam') is-invalid @enderror" 
                                           value="{{ old('jam', $ceklist->jam ?? '') }}" required>
                                    @error('jam')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Fuel <span class="text-danger">*</span></label>
                                    <select name="fuel" class="form-control @error('fuel') is-invalid @enderror" required>
                                        <option value="">Pilih...</option>
                                        <option value="E" {{ old('fuel', $ceklist->fuel ?? '') == 'E' ? 'selected' : '' }}>E (Empty)</option>
                                        <option value="1/8" {{ old('fuel', $ceklist->fuel ?? '') == '1/8' ? 'selected' : '' }}>1/8</option>
                                        <option value="1/4" {{ old('fuel', $ceklist->fuel ?? '') == '1/4' ? 'selected' : '' }}>1/4</option>
                                        <option value="1/2" {{ old('fuel', $ceklist->fuel ?? '') == '1/2' ? 'selected' : '' }}>1/2</option>
                                        <option value="3/4" {{ old('fuel', $ceklist->fuel ?? '') == '3/4' ? 'selected' : '' }}>3/4</option>
                                        <option value="F" {{ old('fuel', $ceklist->fuel ?? '') == 'F' ? 'selected' : '' }}>F (Full)</option>
                                    </select>
                                    @error('fuel')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Pemeriksa <span class="text-danger">*</span></label>
                                    <input type="text" name="pemeriksa" class="form-control @error('pemeriksa') is-invalid @enderror" 
                                           value="{{ old('pemeriksa', $ceklist->pemeriksa ?? '') }}" required>
                                    @error('pemeriksa')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Kendaraan <span class="text-danger">*</span></label>
                                    <select name="kendaraan_id" class="form-control select2 @error('kendaraan_id') is-invalid @enderror" required>
                                        <option value="">Pilih Kendaraan...</option>
                                        @foreach($kendaraans as $kendaraan)
                                            <option value="{{ $kendaraan->id }}" 
                                                {{ old('kendaraan_id', $ceklist->kendaraan_id ?? '') == $kendaraan->id ? 'selected' : '' }}>
                                                {{ $kendaraan->no_polisi }} - {{ $kendaraan->nama_asset }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('kendaraan_id')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Status</label>
                                    <select name="status" class="form-control @error('status') is-invalid @enderror">
                                        <option value="">Pilih...</option>
                                        <option value="Baik" {{ old('status', $ceklist->status ?? '') == 'Baik' ? 'selected' : '' }}>Baik</option>
                                        <option value="Rusak" {{ old('status', $ceklist->status ?? '') == 'Rusak' ? 'selected' : '' }}>Rusak</option>
                                    </select>
                                    @error('status')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Kondisi Kendaraan -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Kondisi Kendaraan (B = Baik, R = Rusak)</h3>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th width="5%">NO</th>
                                        <th>KETERANGAN</th>
                                        <th width="10%" class="text-center">B</th>
                                        <th width="10%" class="text-center">R</th>
                                        <th width="5%">NO</th>
                                        <th>KETERANGAN</th>
                                        <th width="10%" class="text-center">B</th>
                                        <th width="10%" class="text-center">R</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>1</td>
                                        <td>Kondisi mesin</td>
                                        <td class="text-center">
                                            <input type="radio" name="kondisi_mesin" value="B" 
                                                {{ old('kondisi_mesin', $ceklist->kondisi_mesin ?? '') == 'B' ? 'checked' : '' }}>
                                        </td>
                                        <td class="text-center">
                                            <input type="radio" name="kondisi_mesin" value="R" 
                                                {{ old('kondisi_mesin', $ceklist->kondisi_mesin ?? '') == 'R' ? 'checked' : '' }}>
                                        </td>
                                        <td>16</td>
                                        <td>Kunci pintu / kontak</td>
                                        <td class="text-center">
                                            <input type="radio" name="kuncipintu" value="B" 
                                                {{ old('kuncipintu', $ceklist->kuncipintu ?? '') == 'B' ? 'checked' : '' }}>
                                        </td>
                                        <td class="text-center">
                                            <input type="radio" name="kuncipintu" value="R" 
                                                {{ old('kuncipintu', $ceklist->kuncipintu ?? '') == 'R' ? 'checked' : '' }}>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>2</td>
                                        <td>Kondisi rem</td>
                                        <td class="text-center">
                                            <input type="radio" name="kondisi_rem" value="B" 
                                                {{ old('kondisi_rem', $ceklist->kondisi_rem ?? '') == 'B' ? 'checked' : '' }}>
                                        </td>
                                        <td class="text-center">
                                            <input type="radio" name="kondisi_rem" value="R" 
                                                {{ old('kondisi_rem', $ceklist->kondisi_rem ?? '') == 'R' ? 'checked' : '' }}>
                                        </td>
                                        <td>17</td>
                                        <td>Spedometer</td>
                                        <td class="text-center">
                                            <input type="radio" name="spedoometer" value="B" 
                                                {{ old('spedoometer', $ceklist->spedoometer ?? '') == 'B' ? 'checked' : '' }}>
                                        </td>
                                        <td class="text-center">
                                            <input type="radio" name="spedoometer" value="R" 
                                                {{ old('spedoometer', $ceklist->spedoometer ?? '') == 'R' ? 'checked' : '' }}>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>3</td>
                                        <td>Kondisi kopling</td>
                                        <td class="text-center">
                                            <input type="radio" name="kondisi_kopling" value="B" 
                                                {{ old('kondisi_kopling', $ceklist->kondisi_kopling ?? '') == 'B' ? 'checked' : '' }}>
                                        </td>
                                        <td class="text-center">
                                            <input type="radio" name="kondisi_kopling" value="R" 
                                                {{ old('kondisi_kopling', $ceklist->kondisi_kopling ?? '') == 'R' ? 'checked' : '' }}>
                                        </td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td>4</td>
                                        <td>Kondisi ban</td>
                                        <td class="text-center">
                                            <input type="radio" name="kondisi_ban" value="B" 
                                                {{ old('kondisi_ban', $ceklist->kondisi_ban ?? '') == 'B' ? 'checked' : '' }}>
                                        </td>
                                        <td class="text-center">
                                            <input type="radio" name="kondisi_ban" value="R" 
                                                {{ old('kondisi_ban', $ceklist->kondisi_ban ?? '') == 'R' ? 'checked' : '' }}>
                                        </td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td>5</td>
                                        <td>Kondisi AC</td>
                                        <td class="text-center">
                                            <input type="radio" name="kondisi_ac" value="B" 
                                                {{ old('kondisi_ac', $ceklist->kondisi_ac ?? '') == 'B' ? 'checked' : '' }}>
                                        </td>
                                        <td class="text-center">
                                            <input type="radio" name="kondisi_ac" value="R" 
                                                {{ old('kondisi_ac', $ceklist->kondisi_ac ?? '') == 'R' ? 'checked' : '' }}>
                                        </td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td>6</td>
                                        <td>Lampu sen</td>
                                        <td class="text-center">
                                            <input type="radio" name="lampu_lampusen" value="B" 
                                                {{ old('lampu_lampusen', $ceklist->lampu_lampusen ?? '') == 'B' ? 'checked' : '' }}>
                                        </td>
                                        <td class="text-center">
                                            <input type="radio" name="lampu_lampusen" value="R" 
                                                {{ old('lampu_lampusen', $ceklist->lampu_lampusen ?? '') == 'R' ? 'checked' : '' }}>
                                        </td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td>7</td>
                                        <td>Lampu besar / kecil</td>
                                        <td class="text-center">
                                            <input type="radio" name="lampu_lampubesarkecil" value="B" 
                                                {{ old('lampu_lampubesarkecil', $ceklist->lampu_lampubesarkecil ?? '') == 'B' ? 'checked' : '' }}>
                                        </td>
                                        <td class="text-center">
                                            <input type="radio" name="lampu_lampubesarkecil" value="R" 
                                                {{ old('lampu_lampubesarkecil', $ceklist->lampu_lampubesarkecil ?? '') == 'R' ? 'checked' : '' }}>
                                        </td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td>8</td>
                                        <td>Lampu rem / blik / plat no</td>
                                        <td class="text-center">
                                            <input type="radio" name="lampu_rembikplat" value="B" 
                                                {{ old('lampu_rembikplat', $ceklist->lampu_rembikplat ?? '') == 'B' ? 'checked' : '' }}>
                                        </td>
                                        <td class="text-center">
                                            <input type="radio" name="lampu_rembikplat" value="R" 
                                                {{ old('lampu_rembikplat', $ceklist->lampu_rembikplat ?? '') == 'R' ? 'checked' : '' }}>
                                        </td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td>9</td>
                                        <td>Accu</td>
                                        <td class="text-center">
                                            <input type="radio" name="accu" value="B" 
                                                {{ old('accu', $ceklist->accu ?? '') == 'B' ? 'checked' : '' }}>
                                        </td>
                                        <td class="text-center">
                                            <input type="radio" name="accu" value="R" 
                                                {{ old('accu', $ceklist->accu ?? '') == 'R' ? 'checked' : '' }}>
                                        </td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td>10</td>
                                        <td>Air radiator</td>
                                        <td class="text-center">
                                            <input type="radio" name="airradiator" value="B" 
                                                {{ old('airradiator', $ceklist->airradiator ?? '') == 'B' ? 'checked' : '' }}>
                                        </td>
                                        <td class="text-center">
                                            <input type="radio" name="airradiator" value="R" 
                                                {{ old('airradiator', $ceklist->airradiator ?? '') == 'R' ? 'checked' : '' }}>
                                        </td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td>11</td>
                                        <td>Oli mesin / rem / kopling</td>
                                        <td class="text-center">
                                            <input type="radio" name="olimesin_remkopling" value="B" 
                                                {{ old('olimesin_remkopling', $ceklist->olimesin_remkopling ?? '') == 'B' ? 'checked' : '' }}>
                                        </td>
                                        <td class="text-center">
                                            <input type="radio" name="olimesin_remkopling" value="R" 
                                                {{ old('olimesin_remkopling', $ceklist->olimesin_remkopling ?? '') == 'R' ? 'checked' : '' }}>
                                        </td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td>12</td>
                                        <td>Oli power steering</td>
                                        <td class="text-center">
                                            <input type="radio" name="olipower_steering" value="B" 
                                                {{ old('olipower_steering', $ceklist->olipower_steering ?? '') == 'B' ? 'checked' : '' }}>
                                        </td>
                                        <td class="text-center">
                                            <input type="radio" name="olipower_steering" value="R" 
                                                {{ old('olipower_steering', $ceklist->olipower_steering ?? '') == 'R' ? 'checked' : '' }}>
                                        </td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td>13</td>
                                        <td>Kebersihan interior</td>
                                        <td class="text-center">
                                            <input type="radio" name="kebersihan_interior" value="B" 
                                                {{ old('kebersihan_interior', $ceklist->kebersihan_interior ?? '') == 'B' ? 'checked' : '' }}>
                                        </td>
                                        <td class="text-center">
                                            <input type="radio" name="kebersihan_interior" value="R" 
                                                {{ old('kebersihan_interior', $ceklist->kebersihan_interior ?? '') == 'R' ? 'checked' : '' }}>
                                        </td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td>14</td>
                                        <td>Kerapihan body / cat</td>
                                        <td class="text-center">
                                            <input type="radio" name="kerapihanbody_cat" value="B" 
                                                {{ old('kerapihanbody_cat', $ceklist->kerapihanbody_cat ?? '') == 'B' ? 'checked' : '' }}>
                                        </td>
                                        <td class="text-center">
                                            <input type="radio" name="kerapihanbody_cat" value="R" 
                                                {{ old('kerapihanbody_cat', $ceklist->kerapihanbody_cat ?? '') == 'R' ? 'checked' : '' }}>
                                        </td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                    <!-- Baris 15: Perlengkapan di kiri, 16 & 17 di kanan dalam 1 baris -->
                                    <tr>
                                        <td rowspan="12">15</td>
                                        <td rowspan="12">Perlengkapan</td>
                                        <td rowspan="12" class="text-center">
                                            <input type="radio" name="perlengkapan" value="B" 
                                                {{ old('perlengkapan', $ceklist->perlengkapan ?? '') == 'B' ? 'checked' : '' }}>
                                        </td>
                                        <td rowspan="12" class="text-center">
                                            <input type="radio" name="perlengkapan" value="R" 
                                                {{ old('perlengkapan', $ceklist->perlengkapan ?? '') == 'R' ? 'checked' : '' }}>
                                        </td>
                                        <td>16</td>
                                        <td>Kunci pintu / kontak</td>
                                        <td class="text-center">
                                            <input type="radio" name="kuncipintu" value="B" 
                                                {{ old('kuncipintu', $ceklist->kuncipintu ?? '') == 'B' ? 'checked' : '' }}>
                                        </td>
                                        <td class="text-center">
                                            <input type="radio" name="kuncipintu" value="R" 
                                                {{ old('kuncipintu', $ceklist->kuncipintu ?? '') == 'R' ? 'checked' : '' }}>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>17</td>
                                        <td>Spedometer</td>
                                        <td class="text-center">
                                            <input type="radio" name="spedoometer" value="B" 
                                                {{ old('spedoometer', $ceklist->spedoometer ?? '') == 'B' ? 'checked' : '' }}>
                                        </td>
                                        <td class="text-center">
                                            <input type="radio" name="spedoometer" value="R" 
                                                {{ old('spedoometer', $ceklist->spedoometer ?? '') == 'R' ? 'checked' : '' }}>
                                        </td>
                                    </tr>
                                    <!-- Sub-items di kolom kanan -->
                                    <tr>
                                        <td></td>
                                        <td>- Ban cadang</td>
                                        <td class="text-center">
                                            <input type="radio" name="ban_cadang" value="B" 
                                                {{ old('ban_cadang', $ceklist->ban_cadang ?? '') == 'B' ? 'checked' : '' }}>
                                        </td>
                                        <td class="text-center">
                                            <input type="radio" name="ban_cadang" value="R" 
                                                {{ old('ban_cadang', $ceklist->ban_cadang ?? '') == 'R' ? 'checked' : '' }}>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td>- Dongkrak</td>
                                        <td class="text-center">
                                            <input type="radio" name="dongkrak" value="B" 
                                                {{ old('dongkrak', $ceklist->dongkrak ?? '') == 'B' ? 'checked' : '' }}>
                                        </td>
                                        <td class="text-center">
                                            <input type="radio" name="dongkrak" value="R" 
                                                {{ old('dongkrak', $ceklist->dongkrak ?? '') == 'R' ? 'checked' : '' }}>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td>- Kunci roda</td>
                                        <td class="text-center">
                                            <input type="radio" name="kunciroda" value="B" 
                                                {{ old('kunciroda', $ceklist->kunciroda ?? '') == 'B' ? 'checked' : '' }}>
                                        </td>
                                        <td class="text-center">
                                            <input type="radio" name="kunciroda" value="R" 
                                                {{ old('kunciroda', $ceklist->kunciroda ?? '') == 'R' ? 'checked' : '' }}>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td>- Radio / Tape</td>
                                        <td class="text-center">
                                            <input type="radio" name="radio_tape" value="B" 
                                                {{ old('radio_tape', $ceklist->radio_tape ?? '') == 'B' ? 'checked' : '' }}>
                                        </td>
                                        <td class="text-center">
                                            <input type="radio" name="radio_tape" value="R" 
                                                {{ old('radio_tape', $ceklist->radio_tape ?? '') == 'R' ? 'checked' : '' }}>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td>- Merk</td>
                                        <td class="text-center">
                                            <input type="radio" name="merk" value="B" 
                                                {{ old('merk', $ceklist->merk ?? '') == 'B' ? 'checked' : '' }}>
                                        </td>
                                        <td class="text-center">
                                            <input type="radio" name="merk" value="R" 
                                                {{ old('merk', $ceklist->merk ?? '') == 'R' ? 'checked' : '' }}>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td>- Karpet lantai</td>
                                        <td class="text-center">
                                            <input type="radio" name="karpet_lantai" value="B" 
                                                {{ old('karpet_lantai', $ceklist->karpet_lantai ?? '') == 'B' ? 'checked' : '' }}>
                                        </td>
                                        <td class="text-center">
                                            <input type="radio" name="karpet_lantai" value="R" 
                                                {{ old('karpet_lantai', $ceklist->karpet_lantai ?? '') == 'R' ? 'checked' : '' }}>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td>- Dop roda</td>
                                        <td class="text-center">
                                            <input type="radio" name="doproda" value="B" 
                                                {{ old('doproda', $ceklist->doproda ?? '') == 'B' ? 'checked' : '' }}>
                                        </td>
                                        <td class="text-center">
                                            <input type="radio" name="doproda" value="R" 
                                                {{ old('doproda', $ceklist->doproda ?? '') == 'R' ? 'checked' : '' }}>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td>- Pematik api</td>
                                        <td class="text-center">
                                            <input type="radio" name="pematikapi" value="B" 
                                                {{ old('pematikapi', $ceklist->pematikapi ?? '') == 'B' ? 'checked' : '' }}>
                                        </td>
                                        <td class="text-center">
                                            <input type="radio" name="pematikapi" value="R" 
                                                {{ old('pematikapi', $ceklist->pematikapi ?? '') == 'R' ? 'checked' : '' }}>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td>- Segitiga pengaman</td>
                                        <td class="text-center">
                                            <input type="radio" name="segitiga_pengaman" value="B" 
                                                {{ old('segitiga_pengaman', $ceklist->segitiga_pengaman ?? '') == 'B' ? 'checked' : '' }}>
                                        </td>
                                        <td class="text-center">
                                            <input type="radio" name="segitiga_pengaman" value="R" 
                                                {{ old('segitiga_pengaman', $ceklist->segitiga_pengaman ?? '') == 'R' ? 'checked' : '' }}>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td>- Kotak obat</td>
                                        <td class="text-center">
                                            <input type="radio" name="kotakobat" value="B" 
                                                {{ old('kotakobat', $ceklist->kotakobat ?? '') == 'B' ? 'checked' : '' }}>
                                        </td>
                                        <td class="text-center">
                                            <input type="radio" name="kotakobat" value="R" 
                                                {{ old('kotakobat', $ceklist->kotakobat ?? '') == 'R' ? 'checked' : '' }}>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td>- STNK</td>
                                        <td class="text-center">
                                            <input type="radio" name="stnk" value="B" 
                                                {{ old('stnk', $ceklist->stnk ?? '') == 'B' ? 'checked' : '' }}>
                                        </td>
                                        <td class="text-center">
                                            <input type="radio" name="stnk" value="R" 
                                                {{ old('stnk', $ceklist->stnk ?? '') == 'R' ? 'checked' : '' }}>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Foto Kendaraan -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Foto Kendaraan</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @foreach(['depan', 'atas', 'dalam', 'belakang', 'sampingkiri', 'sampingkanan'] as $posisi)
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Foto {{ ucfirst($posisi) }}</label>
                                    <input type="file" name="foto_{{ $posisi }}" class="form-control-file" accept="image/*">
                                    @if(isset($ceklist) && $ceklist->{'foto_'.$posisi})
                                        <img src="{{ asset('storage/'.$ceklist->{'foto_'.$posisi}) }}" 
                                             class="img-thumbnail mt-2" style="max-width: 200px">
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label>Keterangan Foto {{ ucfirst($posisi) }}</label>
                                    <textarea name="ket_{{ $posisi }}" class="form-control" rows="2">{{ old('ket_'.$posisi, $ceklist->{'ket_'.$posisi} ?? '') }}</textarea>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        <div class="form-group">
                            <label>Accecoris Tambahan</label>
                            <textarea name="accecoris_tambahan" class="form-control" rows="3">{{ old('accecoris_tambahan', $ceklist->accecoris_tambahan ?? '') }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Penanggung Jawab -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Penanggung Jawab</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Sopir</label>
                                    <input type="text" name="sopir" class="form-control" 
                                           value="{{ old('sopir', $ceklist->sopir ?? '') }}">
                                </div>
                                <div class="form-group">
                                    <label>TTD Supir</label>
                                    <canvas id="ttd_supir_canvas" width="400" height="200" style="border:1px solid #000;"></canvas>
                                    <input type="hidden" name="ttd_supir" id="ttd_supir">
                                    <br>
                                    <button type="button" class="btn btn-sm btn-secondary" onclick="clearCanvas('ttd_supir')">Clear</button>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Mekanis</label>
                                    <input type="text" name="mekanis" class="form-control" 
                                           value="{{ old('mekanis', $ceklist->mekanis ?? '') }}">
                                </div>
                                <div class="form-group">
                                    <label>TTD Mekanik</label>
                                    <canvas id="ttd_mekanik_canvas" width="400" height="200" style="border:1px solid #000;"></canvas>
                                    <input type="hidden" name="ttd_mekanik" id="ttd_mekanik">
                                    <br>
                                    <button type="button" class="btn btn-sm btn-secondary" onclick="clearCanvas('ttd_mekanik')">Clear</button>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Keterangan</label>
                                    <textarea name="keterangan" class="form-control" rows="3">{{ old('keterangan', $ceklist->keterangan ?? '') }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Simpan
                        </button>
                        <a href="{{ route('admin.ceklist-kondisi.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Batal
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </section>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('.select2').select2();
    
    // Initialize signature pads
    initSignaturePad('ttd_supir');
    initSignaturePad('ttd_mekanik');
});

function initSignaturePad(canvasId) {
    var canvas = document.getElementById(canvasId + '_canvas');
    var ctx = canvas.getContext('2d');
    var isDrawing = false;
    
    canvas.addEventListener('mousedown', startDrawing);
    canvas.addEventListener('mousemove', draw);
    canvas.addEventListener('mouseup', stopDrawing);
    canvas.addEventListener('mouseout', stopDrawing);
    
    function startDrawing(e) {
        isDrawing = true;
        ctx.beginPath();
        ctx.moveTo(e.offsetX, e.offsetY);
    }
    
    function draw(e) {
        if (!isDrawing) return;
        ctx.lineTo(e.offsetX, e.offsetY);
        ctx.stroke();
    }
    
    function stopDrawing() {
        isDrawing = false;
        document.getElementById(canvasId).value = canvas.toDataURL();
    }
}

function clearCanvas(canvasId) {
    var canvas = document.getElementById(canvasId + '_canvas');
    var ctx = canvas.getContext('2d');
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    document.getElementById(canvasId).value = '';
}
</script>
@endpush
