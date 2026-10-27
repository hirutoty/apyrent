@extends('admin.layouts.app')

@section('content')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Ceklist Kondisi Kendaraan</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item active">Ceklist Kondisi</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Data Ceklist Kondisi Kendaraan</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.ceklist-kondisi.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> Tambah Ceklist Baru
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="ceklistTable" class="table table-bordered table-striped table-hover">
                            <thead>
                                <tr>
                                    <th width="5%">No</th>
                                    <th>Nama</th>
                                    <th>Perusahaan</th>
                                    <th>No. Telephone</th>
                                    <th>Tanggal</th>
                                    <th>Kilometer</th>
                                    <th>Jam</th>
                                    <th>Kendaraan</th>
                                    <th>Status</th>
                                    <th width="12%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($ceklists as $index => $ceklist)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $ceklist->nama }}</td>
                                    <td>{{ $ceklist->perusahaan }}</td>
                                    <td>{{ $ceklist->no_telephone }}</td>
                                    <td>{{ \Carbon\Carbon::parse($ceklist->tanggal)->format('d/m/Y') }}</td>
                                    <td>{{ number_format($ceklist->kilometer) }} km</td>
                                    <td>{{ $ceklist->jam }}</td>
                                    <td>
                                        @if($ceklist->kendaraan)
                                            {{ $ceklist->kendaraan->no_polisi ?? '-' }}
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($ceklist->status == 'Baik')
                                            <span class="badge badge-success">{{ $ceklist->status }}</span>
                                        @elseif($ceklist->status == 'Rusak')
                                            <span class="badge badge-danger">{{ $ceklist->status }}</span>
                                        @else
                                            <span class="badge badge-secondary">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="{{ route('admin.ceklist-kondisi.show', $ceklist->id) }}" 
                                               class="btn btn-info btn-sm" title="Lihat Detail">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.ceklist-kondisi.edit', $ceklist->id) }}" 
                                               class="btn btn-warning btn-sm" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="{{ route('admin.ceklist-kondisi.pdf', $ceklist->id) }}" 
                                               class="btn btn-secondary btn-sm" title="Download PDF" target="_blank">
                                                <i class="fas fa-file-pdf"></i>
                                            </a>
                                            <form action="{{ route('admin.ceklist-kondisi.destroy', $ceklist->id) }}" 
                                                  method="POST" class="d-inline" 
                                                  onsubmit="return confirm('Yakin ingin menghapus data ceklist ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm" title="Hapus">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="10" class="text-center">Tidak ada data ceklist kondisi</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('#ceklistTable').DataTable({
        "responsive": true,
        "lengthChange": true,
        "autoWidth": false,
        "order": [[4, 'desc']], // Sort by tanggal descending
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json"
        }
    });
});
</script>
@endpush
