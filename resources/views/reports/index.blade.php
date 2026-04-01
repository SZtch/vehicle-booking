@extends('layouts.app')

@section('title', 'Laporan')

@section('breadcrumb')
    <li class="breadcrumb-item active">Laporan</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Laporan Pemesanan Kendaraan</h4>
</div>

<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header py-3">
                <i class="bi bi-file-earmark-excel me-2 text-success"></i>Export ke Excel
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('reports.export') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Dari Tanggal</label>
                        <input type="date" name="date_from" value="{{ old('date_from') }}" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Sampai Tanggal</label>
                        <input type="date" name="date_to" value="{{ old('date_to') }}" class="form-control">
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Status</label>
                        <select name="status" class="form-select">
                            <option value="">Semua Status</option>
                            <option value="pending">Pending</option>
                            <option value="approved_l1">Disetujui L1</option>
                            <option value="approved">Disetujui</option>
                            <option value="rejected">Ditolak</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-success w-100">
                        <i class="bi bi-download me-2"></i>Download Laporan Excel
                    </button>
                    <p class="text-muted small mt-2 mb-0 text-center">
                        Kosongkan filter tanggal untuk export semua data
                    </p>
                </form>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-body">
                <h6 class="fw-bold mb-2"><i class="bi bi-info-circle me-2 text-primary"></i>Kolom yang akan diexport:</h6>
                <div class="row g-1" style="font-size:.85rem;color:#6b7280;">
                    @foreach(['Kode Booking', 'Kendaraan', 'Plat Nomor', 'Tipe', 'Driver', 'Keperluan', 'Asal', 'Destinasi', 'Tgl Mulai', 'Tgl Selesai', 'Penumpang', 'Status', 'Pemohon', 'Approver L1', 'Status L1', 'Approver L2', 'Status L2', 'Dibuat Pada'] as $col)
                    <div class="col-6">
                        <i class="bi bi-check text-success me-1"></i>{{ $col }}
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
