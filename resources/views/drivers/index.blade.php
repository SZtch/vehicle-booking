@extends('layouts.app')

@section('title', 'Data Driver')

@section('breadcrumb')
    <li class="breadcrumb-item active">Driver</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Data Driver</h4>
    <a href="{{ route('drivers.create') }}" class="btn btn-primary btn-sm">
        <i class="bi bi-plus-lg me-1"></i>Tambah Driver
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show py-2">
        <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif
@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show py-2">
        <i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="card">
    <div class="card-header py-3">
        <form method="GET" class="row g-2 align-items-center">
            <div class="col-md-6">
                <input type="text" name="search" value="{{ request('search') }}"
                    class="form-control form-control-sm" placeholder="Cari nama, nomor SIM, telepon...">
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select form-select-sm">
                    <option value="">Semua Status</option>
                    <option value="available" @selected(request('status') === 'available')>Tersedia</option>
                    <option value="on_duty" @selected(request('status') === 'on_duty')>Bertugas</option>
                </select>
            </div>
            <div class="col-md-3 d-flex gap-1">
                <button type="submit" class="btn btn-primary btn-sm flex-fill"><i class="bi bi-search"></i></button>
                <a href="{{ route('drivers.index') }}" class="btn btn-outline-secondary btn-sm flex-fill"><i class="bi bi-x-lg"></i></a>
            </div>
        </form>
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th>Nama Driver</th>
                    <th>No. SIM</th>
                    <th>Telepon</th>
                    <th>Alamat</th>
                    <th>Status</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($drivers as $driver)
                <tr>
                    <td class="fw-semibold">{{ $driver->name }}</td>
                    <td><span class="font-monospace small">{{ $driver->license_number }}</span></td>
                    <td>{{ $driver->phone ?? '-' }}</td>
                    <td class="text-muted small">{{ Str::limit($driver->address, 40) ?? '-' }}</td>
                    <td>
                        @if($driver->status === 'available')
                            <span class="badge bg-success">Tersedia</span>
                        @else
                            <span class="badge bg-warning text-dark">Bertugas</span>
                        @endif
                    </td>
                    <td class="text-center">
                        <div class="btn-group btn-group-sm">
                            <a href="{{ route('drivers.show', $driver) }}" class="btn btn-outline-info" title="Detail">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="{{ route('drivers.edit', $driver) }}" class="btn btn-outline-warning" title="Edit">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form method="POST" action="{{ route('drivers.destroy', $driver) }}"
                                onsubmit="return confirm('Hapus driver {{ $driver->name }}?')">
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
                    <td colspan="6" class="text-center py-4 text-muted">
                        <i class="bi bi-person-x fs-2 d-block mb-2 opacity-25"></i>
                        Belum ada data driver
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($drivers->hasPages())
    <div class="card-footer">{{ $drivers->links() }}</div>
    @endif
</div>
@endsection
