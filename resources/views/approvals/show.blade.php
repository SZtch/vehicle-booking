@extends('layouts.app')

@section('title', 'Proses Persetujuan')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('approvals.index') }}">Persetujuan</a></li>
    <li class="breadcrumb-item active">Proses</li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold mb-0">Proses Persetujuan</h4>
            <a href="{{ route('approvals.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left me-1"></i> Kembali
            </a>
        </div>

        {{-- Booking detail --}}
        <div class="card mb-3">
            <div class="card-header py-3">
                <i class="bi bi-file-text me-2 text-primary"></i>Detail Pemesanan
                <code class="ms-2">{{ $approval->booking?->booking_code }}</code>
            </div>
            <div class="card-body">
                <div class="row g-2">
                    <div class="col-md-6">
                        <small class="text-muted d-block">Kendaraan</small>
                        <span class="fw-semibold">{{ $approval->booking?->vehicle?->name }}</span>
                        <span class="text-muted small">({{ $approval->booking?->vehicle?->plate_number }})</span>
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted d-block">Driver</small>
                        <span>{{ $approval->booking?->driver?->name }}</span>
                    </div>
                    <div class="col-12">
                        <small class="text-muted d-block">Keperluan</small>
                        <span class="fw-semibold">{{ $approval->booking?->purpose }}</span>
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted d-block">Asal &rarr; Destinasi</small>
                        <span>{{ $approval->booking?->origin }} &rarr; {{ $approval->booking?->destination }}</span>
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted d-block">Penumpang</small>
                        <span>{{ $approval->booking?->passenger_count }} orang</span>
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted d-block">Tanggal Mulai</small>
                        <span>{{ $approval->booking?->start_date?->format('d M Y, H:i') }}</span>
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted d-block">Tanggal Selesai</small>
                        <span>{{ $approval->booking?->end_date?->format('d M Y, H:i') }}</span>
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted d-block">Pemohon</small>
                        <span>{{ $approval->booking?->admin?->name }}</span>
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted d-block">Level Persetujuan Anda</small>
                        <span class="badge bg-primary fs-6">Level {{ $approval->level }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Status L1 jika ini L2 --}}
        @if($approval->level === 2)
        @php $l1 = $approval->booking?->approvalLevel1; @endphp
        <div class="alert {{ $l1?->status === 'approved' ? 'alert-success' : 'alert-warning' }} py-2 mb-3">
            <i class="bi bi-info-circle me-1"></i>
            Level 1 ({{ $l1?->approver?->name }}):
            <strong>{{ $l1?->status === 'approved' ? 'Sudah disetujui' : 'Belum disetujui' }}</strong>
        </div>
        @endif

        {{-- Action form --}}
        @if($approval->status === 'pending')
        <div class="card">
            <div class="card-header py-3">
                <i class="bi bi-pencil-square me-2 text-primary"></i>Keputusan Anda
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('approvals.process', $approval) }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Catatan (opsional)</label>
                        <textarea name="notes" rows="3" class="form-control"
                            placeholder="Tambahkan catatan untuk pemohon...">{{ old('notes') }}</textarea>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" name="action" value="approve"
                            class="btn btn-success flex-fill"
                            onclick="return confirm('Setujui pemesanan ini?')">
                            <i class="bi bi-check-lg me-1"></i> Setujui
                        </button>
                        <button type="submit" name="action" value="reject"
                            class="btn btn-danger flex-fill"
                            onclick="return confirm('Tolak pemesanan ini?')">
                            <i class="bi bi-x-lg me-1"></i> Tolak
                        </button>
                    </div>
                </form>
            </div>
        </div>
        @else
        <div class="alert {{ $approval->status === 'approved' ? 'alert-success' : 'alert-danger' }}">
            <i class="bi bi-{{ $approval->status === 'approved' ? 'check-circle' : 'x-circle' }} me-2"></i>
            Anda sudah <strong>{{ $approval->status === 'approved' ? 'menyetujui' : 'menolak' }}</strong>
            pemesanan ini pada {{ $approval->decided_at?->format('d M Y, H:i') }}.
            @if($approval->notes)
            <div class="mt-2 fst-italic">"{{ $approval->notes }}"</div>
            @endif
        </div>
        @endif
    </div>
</div>
@endsection
