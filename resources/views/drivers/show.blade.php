@extends('layouts.app')

@section('title', 'Detail Driver')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('drivers.index') }}">Driver</a></li>
    <li class="breadcrumb-item active">{{ $driver->name }}</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Detail Driver</h4>
    <div class="d-flex gap-2">
        <a href="{{ route('drivers.edit', $driver) }}" class="btn btn-warning btn-sm">
            <i class="bi bi-pencil me-1"></i>Edit
        </a>
        <a href="{{ route('drivers.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i>Kembali
        </a>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header py-3">
                <i class="bi bi-person-badge me-2 text-primary"></i>Profil Driver
            </div>
            <div class="card-body">
                <table class="table table-sm table-borderless mb-0">
                    <tr><th class="text-muted" width="40%">Nama</th><td class="fw-semibold">{{ $driver->name }}</td></tr>
                    <tr><th class="text-muted">No. SIM</th><td><span class="font-monospace">{{ $driver->license_number }}</span></td></tr>
                    <tr><th class="text-muted">Telepon</th><td>{{ $driver->phone ?? '-' }}</td></tr>
                    <tr><th class="text-muted">Alamat</th><td class="text-muted small">{{ $driver->address ?? '-' }}</td></tr>
                    <tr><th class="text-muted">Status</th>
                        <td>
                            @if($driver->status === 'available')
                                <span class="badge bg-success">Tersedia</span>
                            @else
                                <span class="badge bg-warning text-dark">Bertugas</span>
                            @endif
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="row g-3 mt-1">
            <div class="col-6">
                <div class="card text-center p-3">
                    <div class="text-muted small mb-1">Total Tugas</div>
                    <div class="fw-bold fs-3 text-primary">{{ $stats['total_bookings'] }}</div>
                </div>
            </div>
            <div class="col-6">
                <div class="card text-center p-3">
                    <div class="text-muted small mb-1">Selesai</div>
                    <div class="fw-bold fs-3 text-success">{{ $stats['approved_bookings'] }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card">
            <div class="card-header py-3">
                <i class="bi bi-clock-history me-2 text-primary"></i>Riwayat Penugasan
            </div>
            <div class="table-responsive">
                <table class="table table-sm mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Kode Booking</th>
                            <th>Kendaraan</th>
                            <th>Tujuan</th>
                            <th>Tgl Mulai</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($driver->bookings->take(10) as $booking)
                        <tr>
                            <td><small class="font-monospace">{{ $booking->booking_code }}</small></td>
                            <td>{{ $booking->vehicle?->name ?? '-' }}</td>
                            <td class="text-muted small">{{ Str::limit($booking->destination, 30) }}</td>
                            <td>{{ $booking->start_date?->format('d/m/Y') }}</td>
                            <td>
                                <span class="badge bg-{{ $booking->getStatusColor() }}">
                                    {{ $booking->getStatusLabel() }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-3 text-muted">Belum ada riwayat penugasan</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
