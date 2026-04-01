@extends('layouts.app')

@section('title', 'Daftar Pemesanan')

@section('breadcrumb')
    <li class="breadcrumb-item active">Pemesanan</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Pemesanan Kendaraan</h4>
    <a href="{{ route('bookings.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i> Buat Pemesanan
    </a>
</div>

{{-- Filter --}}
<div class="card mb-4">
    <div class="card-body py-3">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label small fw-semibold">Status</label>
                <select name="status" class="form-select form-select-sm">
                    <option value="">Semua Status</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="approved_l1" {{ request('status') === 'approved_l1' ? 'selected' : '' }}>Disetujui L1</option>
                    <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Disetujui</option>
                    <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Ditolak</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-semibold">Dari Tanggal</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}" class="form-control form-control-sm">
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-semibold">Sampai Tanggal</label>
                <input type="date" name="date_to" value="{{ request('date_to') }}" class="form-control form-control-sm">
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary btn-sm flex-fill">
                    <i class="bi bi-search me-1"></i> Filter
                </button>
                <a href="{{ route('bookings.index') }}" class="btn btn-outline-secondary btn-sm">Reset</a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Kode</th>
                        <th>Kendaraan</th>
                        <th>Driver</th>
                        <th>Tujuan</th>
                        <th>Tanggal Mulai</th>
                        <th>Status</th>
                        <th>Persetujuan</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bookings as $booking)
                    <tr>
                        <td><code class="small">{{ $booking->booking_code }}</code></td>
                        <td>
                            <div class="fw-semibold" style="font-size:.9rem;">{{ $booking->vehicle?->name }}</div>
                            <small class="text-muted">{{ $booking->vehicle?->plate_number }}</small>
                        </td>
                        <td>{{ $booking->driver?->name }}</td>
                        <td>
                            <div style="font-size:.85rem;">{{ $booking->purpose }}</div>
                            <small class="text-muted">{{ $booking->origin }} → {{ $booking->destination }}</small>
                        </td>
                        <td style="font-size:.85rem;">{{ $booking->start_date?->format('d M Y') }}</td>
                        <td>
                            <span class="badge bg-{{ $booking->getStatusColor() }}">
                                {{ $booking->getStatusLabel() }}
                            </span>
                        </td>
                        <td>
                            @foreach($booking->approvals->sortBy('level') as $approval)
                            <div style="font-size:.78rem;">
                                <span class="badge bg-{{ $approval->status === 'approved' ? 'success' : ($approval->status === 'rejected' ? 'danger' : 'secondary') }}">
                                    L{{ $approval->level }}
                                </span>
                                {{ $approval->approver?->name }}
                            </div>
                            @endforeach
                        </td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="{{ route('bookings.show', $booking) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-eye"></i>
                                </a>
                                @if($booking->status === 'pending')
                                <form method="POST" action="{{ route('bookings.destroy', $booking) }}"
                                    onsubmit="return confirm('Batalkan pemesanan ini?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger"><i class="bi bi-x-lg"></i></button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-5">
                            <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                            Belum ada pemesanan
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($bookings->hasPages())
        <div class="p-3">
            {{ $bookings->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
