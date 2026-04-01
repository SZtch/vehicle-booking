@extends('layouts.app')

@section('title', 'Detail Kendaraan')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('vehicles.index') }}">Kendaraan</a></li>
    <li class="breadcrumb-item active">{{ $vehicle->plate_number }}</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Detail Kendaraan</h4>
    <div class="d-flex gap-2">
        <a href="{{ route('vehicles.edit', $vehicle) }}" class="btn btn-warning btn-sm">
            <i class="bi bi-pencil me-1"></i>Edit
        </a>
        <a href="{{ route('vehicles.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i>Kembali
        </a>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-5">
        <div class="card h-100">
            <div class="card-header py-3">
                <i class="bi bi-truck me-2 text-primary"></i>Informasi Kendaraan
            </div>
            <div class="card-body">
                <table class="table table-sm table-borderless mb-0">
                    <tr><th class="text-muted" width="40%">Nama</th><td class="fw-semibold">{{ $vehicle->name }}</td></tr>
                    <tr><th class="text-muted">Plat Nomor</th><td><span class="badge bg-secondary">{{ $vehicle->plate_number }}</span></td></tr>
                    <tr><th class="text-muted">Merek / Model</th><td>{{ $vehicle->brand }} {{ $vehicle->model }}</td></tr>
                    <tr><th class="text-muted">Tahun</th><td>{{ $vehicle->year ?? '-' }}</td></tr>
                    <tr><th class="text-muted">Tipe</th><td>{{ $vehicle->getTypeLabel() }}</td></tr>
                    <tr><th class="text-muted">Kepemilikan</th>
                        <td>
                            @if($vehicle->ownership === 'owned')
                                <span class="badge bg-primary-subtle text-primary">Milik Perusahaan</span>
                            @else
                                <span class="badge bg-warning-subtle text-warning">Sewa</span>
                            @endif
                        </td>
                    </tr>
                    <tr><th class="text-muted">Status</th><td>{!! $vehicle->getStatusBadge() !!}</td></tr>
                    <tr><th class="text-muted">Odometer</th><td>{{ number_format($vehicle->odometer) }} km</td></tr>
                    <tr><th class="text-muted">Service Terakhir</th><td>{{ $vehicle->last_service?->format('d/m/Y') ?? '-' }}</td></tr>
                    <tr><th class="text-muted">Service Berikutnya</th>
                        <td>
                            @if($vehicle->next_service)
                                @if($vehicle->next_service->isPast())
                                    <span class="text-danger fw-semibold">
                                        <i class="bi bi-exclamation-triangle me-1"></i>{{ $vehicle->next_service->format('d/m/Y') }} (Terlambat)
                                    </span>
                                @else
                                    {{ $vehicle->next_service->format('d/m/Y') }}
                                @endif
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-7">
        <div class="row g-3 mb-3">
            <div class="col-6">
                <div class="card text-center p-3">
                    <div class="text-muted small mb-1">Total Pemesanan</div>
                    <div class="fw-bold fs-3 text-primary">{{ $usageStats['total_bookings'] }}</div>
                </div>
            </div>
            <div class="col-6">
                <div class="card text-center p-3">
                    <div class="text-muted small mb-1">Pemesanan Selesai</div>
                    <div class="fw-bold fs-3 text-success">{{ $usageStats['approved_bookings'] }}</div>
                </div>
            </div>
            <div class="col-6">
                <div class="card text-center p-3">
                    <div class="text-muted small mb-1">Total BBM (liter)</div>
                    <div class="fw-bold fs-3 text-warning">{{ number_format($usageStats['total_fuel'], 1) }}</div>
                </div>
            </div>
            <div class="col-6">
                <div class="card text-center p-3">
                    <div class="text-muted small mb-1">Total Biaya BBM</div>
                    <div class="fw-bold fs-4 text-danger">Rp {{ number_format($usageStats['total_fuel_cost']) }}</div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header py-3">
                <i class="bi bi-clock-history me-2 text-primary"></i>Riwayat Pemesanan Terakhir
            </div>
            <div class="table-responsive">
                <table class="table table-sm mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Kode</th>
                            <th>Driver</th>
                            <th>Tgl Mulai</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($vehicle->bookings->take(5) as $booking)
                        <tr>
                            <td><small class="font-monospace">{{ $booking->booking_code }}</small></td>
                            <td>{{ $booking->driver?->name ?? '-' }}</td>
                            <td>{{ $booking->start_date?->format('d/m/Y') }}</td>
                            <td>
                                <span class="badge bg-{{ $booking->getStatusColor() }}">
                                    {{ $booking->getStatusLabel() }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-3 text-muted">Belum ada riwayat pemesanan</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
