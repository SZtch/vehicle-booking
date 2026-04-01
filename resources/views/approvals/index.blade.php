@extends('layouts.app')

@section('title', 'Persetujuan')

@section('breadcrumb')
    <li class="breadcrumb-item active">Persetujuan</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Daftar Persetujuan</h4>
    <span class="badge bg-primary">Level {{ auth()->user()->approval_level }}</span>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Kode Booking</th>
                        <th>Kendaraan</th>
                        <th>Tujuan</th>
                        <th>Pemohon</th>
                        <th>Tanggal Mulai</th>
                        <th>Status Saya</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($approvals as $approval)
                    <tr>
                        <td><code class="small">{{ $approval->booking?->booking_code }}</code></td>
                        <td>
                            <div class="fw-semibold" style="font-size:.9rem;">{{ $approval->booking?->vehicle?->name }}</div>
                            <small class="text-muted">{{ $approval->booking?->vehicle?->plate_number }}</small>
                        </td>
                        <td style="font-size:.85rem;">{{ $approval->booking?->purpose }}</td>
                        <td>{{ $approval->booking?->admin?->name }}</td>
                        <td style="font-size:.85rem;">{{ $approval->booking?->start_date?->format('d M Y') }}</td>
                        <td>
                            <span class="badge bg-{{ $approval->status === 'approved' ? 'success' : ($approval->status === 'rejected' ? 'danger' : 'warning') }}">
                                {{ ucfirst($approval->status) }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('approvals.show', $approval) }}" class="btn btn-sm btn-outline-primary">
                                @if($approval->status === 'pending')
                                    <i class="bi bi-check-square me-1"></i> Proses
                                @else
                                    <i class="bi bi-eye me-1"></i> Detail
                                @endif
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-5">
                            <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                            Tidak ada persetujuan yang ditugaskan
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($approvals->hasPages())
        <div class="p-3">{{ $approvals->links() }}</div>
        @endif
    </div>
</div>
@endsection
