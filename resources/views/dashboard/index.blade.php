@extends('layouts.app')

@section('title', 'Dashboard')

@section('breadcrumb')
    <li class="breadcrumb-item active">Dashboard</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Dashboard</h4>
    <span class="text-muted">Selamat datang, {{ auth()->user()->name }}</span>
</div>

{{-- Stats --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="stat-card" style="background: linear-gradient(135deg, #1e3a5f, #2d5f9e);">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div style="font-size:.8rem;opacity:.8;">Total Kendaraan</div>
                    <div class="fw-bold" style="font-size:2rem;">{{ $stats['total_vehicles'] }}</div>
                </div>
                <i class="bi bi-truck fs-2 opacity-50"></i>
            </div>
            <div style="font-size:.8rem;opacity:.7;">{{ $stats['available_vehicles'] }} tersedia</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card" style="background: linear-gradient(135deg, #e8a020, #f4c242);">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div style="font-size:.8rem;opacity:.8;">Pending</div>
                    <div class="fw-bold" style="font-size:2rem;">{{ $stats['pending_bookings'] }}</div>
                </div>
                <i class="bi bi-clock-history fs-2 opacity-50"></i>
            </div>
            <div style="font-size:.8rem;opacity:.7;">Menunggu persetujuan</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card" style="background: linear-gradient(135deg, #16a34a, #22c55e);">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div style="font-size:.8rem;opacity:.8;">Disetujui</div>
                    <div class="fw-bold" style="font-size:2rem;">{{ $stats['approved_bookings'] }}</div>
                </div>
                <i class="bi bi-check-circle fs-2 opacity-50"></i>
            </div>
            <div style="font-size:.8rem;opacity:.7;">Total disetujui</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card" style="background: linear-gradient(135deg, #7c3aed, #a855f7);">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div style="font-size:.8rem;opacity:.8;">Total Pemesanan</div>
                    <div class="fw-bold" style="font-size:2rem;">{{ $stats['total_bookings'] }}</div>
                </div>
                <i class="bi bi-calendar-check fs-2 opacity-50"></i>
            </div>
            <div style="font-size:.8rem;opacity:.7;">Semua waktu</div>
        </div>
    </div>
</div>

@if(auth()->user()->isApprover() && $pendingApprovals > 0)
<div class="alert alert-warning d-flex align-items-center gap-2 mb-4">
    <i class="bi bi-bell-fill"></i>
    <div>Ada <strong>{{ $pendingApprovals }} pemesanan</strong> yang menunggu persetujuan Anda.
    <a href="{{ route('approvals.index') }}" class="alert-link">Lihat sekarang</a></div>
</div>
@endif

<div class="row g-3 mb-4">
    {{-- Monthly chart --}}
    <div class="col-md-8">
        <div class="card h-100">
            <div class="card-header py-3">
                <i class="bi bi-bar-chart me-2 text-primary"></i>Pemakaian Kendaraan 6 Bulan Terakhir
            </div>
            <div class="card-body">
                <canvas id="monthlyChart" height="100"></canvas>
            </div>
        </div>
    </div>

    {{-- By status donut --}}
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-header py-3">
                <i class="bi bi-pie-chart me-2 text-primary"></i>Status Pemesanan
            </div>
            <div class="card-body d-flex align-items-center justify-content-center">
                <canvas id="statusChart" height="200"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="row g-3">
    {{-- Top vehicles --}}
    <div class="col-md-6">
        <div class="card">
            <div class="card-header py-3">
                <i class="bi bi-trophy me-2 text-warning"></i>Kendaraan Paling Sering Dipakai
            </div>
            <div class="card-body p-0">
                <table class="table mb-0">
                    <thead><tr><th>Kendaraan</th><th>Plat</th><th class="text-end">Pemakaian</th></tr></thead>
                    <tbody>
                        @forelse($topVehicles as $v)
                        <tr>
                            <td>{{ $v->name }}</td>
                            <td><code>{{ $v->plate_number }}</code></td>
                            <td class="text-end"><span class="badge bg-primary">{{ $v->usage_count }}x</span></td>
                        </tr>
                        @empty
                        <tr><td colspan="3" class="text-center text-muted py-3">Belum ada data</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Recent activity --}}
    <div class="col-md-6">
        <div class="card">
            <div class="card-header py-3">
                <i class="bi bi-activity me-2 text-success"></i>Aktivitas Terbaru
            </div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush">
                    @forelse($recentLogs as $log)
                    <li class="list-group-item py-2 px-3" style="font-size:.85rem;">
                        <div class="d-flex justify-content-between">
                            <span>{{ $log->description }}</span>
                            <small class="text-muted ms-2 text-nowrap">{{ $log->created_at->diffForHumans() }}</small>
                        </div>
                        <small class="text-muted">{{ $log->user?->name ?? 'System' }}</small>
                    </li>
                    @empty
                    <li class="list-group-item text-center text-muted py-3">Belum ada aktivitas</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const monthlyData = @json($monthlyBookings);
const statusData  = @json($byStatus);

// Monthly bar chart
new Chart(document.getElementById('monthlyChart'), {
    type: 'bar',
    data: {
        labels: monthlyData.map(d => d.label),
        datasets: [{
            label: 'Jumlah Pemesanan',
            data: monthlyData.map(d => d.total),
            backgroundColor: '#1e3a5f',
            borderRadius: 6,
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
    }
});

// Status donut chart
const statusColors = {
    pending: '#e8a020', approved_l1: '#3b82f6',
    approved: '#16a34a', rejected: '#dc2626'
};
const statusLabels = {
    pending: 'Pending', approved_l1: 'Disetujui L1',
    approved: 'Disetujui', rejected: 'Ditolak'
};
new Chart(document.getElementById('statusChart'), {
    type: 'doughnut',
    data: {
        labels: statusData.map(d => statusLabels[d.status] || d.status),
        datasets: [{
            data: statusData.map(d => d.total),
            backgroundColor: statusData.map(d => statusColors[d.status] || '#6b7280'),
            borderWidth: 0,
        }]
    },
    options: {
        responsive: true,
        cutout: '65%',
        plugins: { legend: { position: 'bottom', labels: { font: { size: 12 } } } }
    }
});
</script>
@endpush
