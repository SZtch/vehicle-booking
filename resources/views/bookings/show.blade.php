@extends('layouts.app')

@section('title', 'Detail Pemesanan')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('bookings.index') }}">Pemesanan</a></li>
    <li class="breadcrumb-item active">{{ $booking->booking_code }}</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-0">Detail Pemesanan</h4>
        <code class="text-muted">{{ $booking->booking_code }}</code>
    </div>
    <div class="d-flex gap-2">
        <span class="badge bg-{{ $booking->getStatusColor() }} fs-6 px-3 py-2">
            {{ $booking->getStatusLabel() }}
        </span>
        <a href="{{ route('bookings.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
    </div>
</div>

<div class="row g-3">
    <div class="col-md-8">
        <div class="card mb-3">
            <div class="card-header py-3"><i class="bi bi-info-circle me-2 text-primary"></i>Informasi Pemesanan</div>
            <div class="card-body">
                <div class="row g-2">
                    <div class="col-md-6">
                        <small class="text-muted">Kendaraan</small>
                        <div class="fw-semibold">{{ $booking->vehicle?->name }}</div>
                        <small class="text-muted">{{ $booking->vehicle?->plate_number }} &bull; {{ $booking->vehicle?->getTypeLabel() }}</small>
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted">Driver</small>
                        <div class="fw-semibold">{{ $booking->driver?->name }}</div>
                        <small class="text-muted">SIM: {{ $booking->driver?->license_number }}</small>
                    </div>
                    <div class="col-12 mt-3">
                        <small class="text-muted">Keperluan</small>
                        <div class="fw-semibold">{{ $booking->purpose }}</div>
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted">Asal</small>
                        <div>{{ $booking->origin }}</div>
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted">Destinasi</small>
                        <div>{{ $booking->destination }}</div>
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted">Tanggal Mulai</small>
                        <div>{{ $booking->start_date?->format('d M Y, H:i') }}</div>
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted">Tanggal Selesai</small>
                        <div>{{ $booking->end_date?->format('d M Y, H:i') }}</div>
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted">Jumlah Penumpang</small>
                        <div>{{ $booking->passenger_count }} orang</div>
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted">Dibuat oleh</small>
                        <div>{{ $booking->admin?->name }}</div>
                    </div>
                    @if($booking->notes)
                    <div class="col-12">
                        <small class="text-muted">Catatan</small>
                        <div class="p-2 bg-light rounded">{{ $booking->notes }}</div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        {{-- Approval timeline --}}
        <div class="card">
            <div class="card-header py-3"><i class="bi bi-diagram-3 me-2 text-primary"></i>Alur Persetujuan</div>
            <div class="card-body">
                <div class="position-relative">
                    @foreach($booking->approvals->sortBy('level') as $approval)
                    <div class="d-flex gap-3 mb-4">
                        <div class="d-flex flex-column align-items-center">
                            <div class="rounded-circle d-flex align-items-center justify-content-center"
                                style="width:36px;height:36px;min-width:36px;
                                background:{{ $approval->status === 'approved' ? '#16a34a' : ($approval->status === 'rejected' ? '#dc2626' : '#e5e7eb') }};
                                color:{{ $approval->status === 'pending' ? '#6b7280' : 'white' }};">
                                @if($approval->status === 'approved')
                                    <i class="bi bi-check-lg"></i>
                                @elseif($approval->status === 'rejected')
                                    <i class="bi bi-x-lg"></i>
                                @else
                                    <i class="bi bi-clock"></i>
                                @endif
                            </div>
                            @if(!$loop->last)
                            <div style="width:2px;flex:1;background:#e5e7eb;margin:4px 0;min-height:24px;"></div>
                            @endif
                        </div>
                        <div class="pb-2">
                            <div class="fw-semibold" style="font-size:.9rem;">
                                Level {{ $approval->level }} &mdash; {{ $approval->approver?->name }}
                            </div>
                            <small class="text-muted">{{ $approval->approver?->department }}</small>
                            <div>
                                <span class="badge bg-{{ $approval->status === 'approved' ? 'success' : ($approval->status === 'rejected' ? 'danger' : 'secondary') }}">
                                    {{ ucfirst($approval->status) }}
                                </span>
                            </div>
                            @if($approval->decided_at)
                            <small class="text-muted">{{ $approval->decided_at->format('d M Y, H:i') }}</small>
                            @endif
                            @if($approval->notes)
                            <div class="mt-1 p-2 bg-light rounded" style="font-size:.8rem;">
                                "{{ $approval->notes }}"
                            </div>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
