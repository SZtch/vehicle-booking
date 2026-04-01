<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Sistem Pemesanan Kendaraan') - Sistem Pemesanan Kendaraan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            --sidebar-width: 260px;
            --primary: #1e3a5f;
            --accent: #e8a020;
        }
        body { background: #f4f6f9; font-family: 'Segoe UI', sans-serif; }
        .sidebar {
            width: var(--sidebar-width);
            min-height: 100vh;
            background: var(--primary);
            position: fixed;
            top: 0; left: 0;
            z-index: 100;
            transition: transform .3s;
        }
        .sidebar-brand {
            padding: 1.5rem 1.25rem;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        .sidebar-brand h5 { color: var(--accent); font-weight: 700; margin: 0; }
        .sidebar-brand small { color: rgba(255,255,255,0.5); font-size: .75rem; }
        .sidebar .nav-link {
            color: rgba(255,255,255,0.7);
            padding: .65rem 1.25rem;
            border-radius: .375rem;
            margin: .15rem .75rem;
            font-size: .9rem;
            transition: all .2s;
        }
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            color: #fff;
            background: rgba(255,255,255,0.12);
        }
        .sidebar .nav-link.active { border-left: 3px solid var(--accent); }
        .sidebar .nav-link i { width: 20px; }
        .main-content {
            margin-left: var(--sidebar-width);
            padding: 0;
            min-height: 100vh;
        }
        .topbar {
            background: #fff;
            padding: .875rem 1.5rem;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 99;
        }
        .page-content { padding: 1.5rem; }
        .card { border: none; box-shadow: 0 1px 4px rgba(0,0,0,0.08); border-radius: .75rem; }
        .card-header { background: #fff; border-bottom: 1px solid #f0f0f0; font-weight: 600; }
        .stat-card { border-radius: .75rem; padding: 1.25rem; color: #fff; }
        .badge { font-size: .75rem; }
        .table th { font-size: .8rem; text-transform: uppercase; letter-spacing: .05em; color: #6b7280; }
        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.show { transform: translateX(0); }
            .main-content { margin-left: 0; }
        }
    </style>
    @stack('styles')
</head>
<body>

<div class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <h5><i class="bi bi-truck me-2"></i>Fleet App</h5>
        <small>Sistem Pemesanan Kendaraan</small>
    </div>
    <nav class="py-3">
        <ul class="nav flex-column">
            <li class="nav-item">
                <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="bi bi-grid-fill me-2"></i> Dashboard
                </a>
            </li>
            @if(auth()->user()->isAdmin())
            <li class="nav-item">
                <a href="{{ route('bookings.index') }}" class="nav-link {{ request()->routeIs('bookings.*') ? 'active' : '' }}">
                    <i class="bi bi-calendar-check me-2"></i> Pemesanan
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('vehicles.index') }}" class="nav-link {{ request()->routeIs('vehicles.*') ? 'active' : '' }}">
                    <i class="bi bi-truck me-2"></i> Kendaraan
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('drivers.index') }}" class="nav-link {{ request()->routeIs('drivers.*') ? 'active' : '' }}">
                    <i class="bi bi-person-badge me-2"></i> Driver
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('reports.index') }}" class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                    <i class="bi bi-file-earmark-excel me-2"></i> Laporan
                </a>
            </li>
            @endif
            @if(auth()->user()->isApprover())
            <li class="nav-item">
                <a href="{{ route('approvals.index') }}" class="nav-link {{ request()->routeIs('approvals.*') ? 'active' : '' }}">
                    <i class="bi bi-check-circle me-2"></i> Persetujuan
                    @php $pending = \App\Models\BookingApproval::where('approver_id', auth()->id())->where('status','pending')->count() @endphp
                    @if($pending > 0)
                        <span class="badge bg-danger ms-1">{{ $pending }}</span>
                    @endif
                </a>
            </li>
            @endif
        </ul>
        <hr class="border-secondary my-2">
        <div class="px-3 py-2">
            <div class="d-flex align-items-center gap-2 mb-3">
                <div class="rounded-circle bg-warning d-flex align-items-center justify-content-center" style="width:36px;height:36px;">
                    <span class="fw-bold text-dark" style="font-size:.8rem;">{{ substr(auth()->user()->name, 0, 2) }}</span>
                </div>
                <div>
                    <div class="text-white" style="font-size:.85rem;font-weight:600;">{{ auth()->user()->name }}</div>
                    <div style="font-size:.72rem;color:rgba(255,255,255,0.5);">{{ ucfirst(auth()->user()->role) }}</div>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn btn-sm w-100" style="background:rgba(255,255,255,0.1);color:#fff;">
                    <i class="bi bi-box-arrow-right me-1"></i> Logout
                </button>
            </form>
        </div>
    </nav>
</div>

<div class="main-content">
    <div class="topbar">
        <div class="d-flex align-items-center gap-3">
            <button class="btn btn-sm d-md-none" onclick="document.getElementById('sidebar').classList.toggle('show')">
                <i class="bi bi-list fs-5"></i>
            </button>
            <nav aria-label="breadcrumb" class="mb-0">
                <ol class="breadcrumb mb-0">
                    @yield('breadcrumb')
                </ol>
            </nav>
        </div>
        <div class="text-muted" style="font-size:.85rem;">
            {{ now()->format('d F Y') }}
        </div>
    </div>

    <div class="page-content">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
@stack('scripts')
</body>
</html>
