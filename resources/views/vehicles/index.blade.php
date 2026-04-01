@extends('layouts.app')

@section('title', 'Data Kendaraan')

@section('breadcrumb')
    <li class="breadcrumb-item active">Kendaraan</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Data Kendaraan</h4>
    <a href="{{ route('vehicles.create') }}" class="btn btn-primary btn-sm">
        <i class="bi bi-plus-lg me-1"></i>Tambah Kendaraan
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show py-2" role="alert">
        <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif
@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show py-2" role="alert">
        <i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="card">
    <div class="card-header py-3">
        <form method="GET" class="row g-2 align-items-center">
            <div class="col-md-5">
                <input type="text" name="search" value="{{ request('search') }}"
                    class="form-control form-control-sm" placeholder="Cari nama, plat, merek...">
            </div>
            <div class="col-md-3">
                <select name="type" class="form-select form-select-sm">
                    <option value="">Semua Tipe</option>
                    <option value="angkutan_orang" @selected(request('type') === 'angkutan_orang')>Angkutan Orang</option>
                    <option value="angkutan_barang" @selected(request('type') === 'angkutan_barang')>Angkutan Barang</option>
                </select>
            </div>
            <div class="col-md-2">
                <select name="status" class="form-select form-select-sm">
                    <option value="">Semua Status</option>
                    <option value="available" @selected(request('status') === 'available')>Tersedia</option>
                    <option value="in_use" @selected(request('status') === 'in_use')>Digunakan</option>
                    <option value="maintenance" @selected(request('status') === 'maintenance')>Maintenance</option>
                </select>
            </div>
            <div class="col-md-2 d-flex gap-1">
                <button type="submit" class="btn btn-primary btn-sm flex-fill">
                    <i class="bi bi-search"></i>
                </button>
                <a href="{{ route('vehicles.index') }}" class="btn btn-outline-secondary btn-sm flex-fill">
                    <i class="bi bi-x-lg"></i>
                </a>
            </div>
        </form>
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th>Kendaraan</th>
                    <th>Plat Nomor</th>
                    <th>Tipe</th>
                    <th>Kepemilikan</th>
                    <th>Odometer</th>
                    <th>Service Berikutnya</th>
                    <th>Status</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($vehicles as $vehicle)
                <tr>
                    <td>
                        <div class="fw-semibold">{{ $vehicle->name }}</div>
                        <small class="text-muted">{{ $vehicle->brand }} {{ $vehicle->model }} {{ $vehicle->year }}</small>
                    </td>
                    <td><span class="badge bg-secondary">{{ $vehicle->plate_number }}</span></td>
                    <td>{{ $vehicle->getTypeLabel() }}</td>
                    <td>
                        @if($vehicle->ownership === 'owned')
                            <span class="badge bg-primary-subtle text-primary">Milik</span>
                        @else
                            <span class="badge bg-warning-subtle text-warning">Sewa</span>
                        @endif
                    </td>
                    <td>{{ number_format($vehicle->odometer) }} km</td>
                    <td>
                        @if($vehicle->next_service)
                            @if($vehicle->next_service->isPast())
                                <span class="text-danger fw-semibold">
                                    <i class="bi bi-exclamation-triangle me-1"></i>{{ $vehicle->next_service->format('d/m/Y') }}
                                </span>
                            @elseif($vehicle->next_service->diffInDays() <= 7)
                                <span class="text-warning fw-semibold">{{ $vehicle->next_service->format('d/m/Y') }}</span>
                            @else
                                <span class="text-muted">{{ $vehicle->next_service->format('d/m/Y') }}</span>
                            @endif
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>
                    <td>{!! $vehicle->getStatusBadge() !!}</td>
                    <td class="text-center">
                        <div class="btn-group btn-group-sm">
                            <a href="{{ route('vehicles.show', $vehicle) }}" class="btn btn-outline-info" title="Detail">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="{{ route('vehicles.edit', $vehicle) }}" class="btn btn-outline-warning" title="Edit">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form method="POST" action="{{ route('vehicles.destroy', $vehicle) }}"
                                onsubmit="return confirm('Hapus kendaraan {{ $vehicle->name }}?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger" title="Hapus">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center py-4 text-muted">
                        <i class="bi bi-truck fs-2 d-block mb-2 opacity-25"></i>
                        Belum ada data kendaraan
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($vehicles->hasPages())
    <div class="card-footer">
        {{ $vehicles->links() }}
    </div>
    @endif
</div>
@endsection
