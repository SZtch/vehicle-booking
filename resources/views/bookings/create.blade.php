@extends('layouts.app')

@section('title', 'Buat Pemesanan')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('bookings.index') }}">Pemesanan</a></li>
    <li class="breadcrumb-item active">Buat Baru</li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold mb-0">Buat Pemesanan Kendaraan</h4>
            <a href="{{ route('bookings.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left me-1"></i> Kembali
            </a>
        </div>

        <form method="POST" action="{{ route('bookings.store') }}">
            @csrf

            {{-- Kendaraan & Driver --}}
            <div class="card mb-3">
                <div class="card-header py-3">
                    <i class="bi bi-truck me-2 text-primary"></i>Informasi Kendaraan
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Kendaraan <span class="text-danger">*</span></label>
                            <select name="vehicle_id" class="form-select @error('vehicle_id') is-invalid @enderror" required>
                                <option value="">-- Pilih Kendaraan --</option>
                                @foreach($vehicles as $vehicle)
                                <option value="{{ $vehicle->id }}" {{ old('vehicle_id') == $vehicle->id ? 'selected' : '' }}>
                                    {{ $vehicle->name }} ({{ $vehicle->plate_number }}) - {{ $vehicle->getTypeLabel() }}
                                </option>
                                @endforeach
                            </select>
                            @error('vehicle_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Driver <span class="text-danger">*</span></label>
                            <select name="driver_id" class="form-select @error('driver_id') is-invalid @enderror" required>
                                <option value="">-- Pilih Driver --</option>
                                @foreach($drivers as $driver)
                                <option value="{{ $driver->id }}" {{ old('driver_id') == $driver->id ? 'selected' : '' }}>
                                    {{ $driver->name }} ({{ $driver->license_number }})
                                </option>
                                @endforeach
                            </select>
                            @error('driver_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>
            </div>

            {{-- Detail Perjalanan --}}
            <div class="card mb-3">
                <div class="card-header py-3">
                    <i class="bi bi-map me-2 text-primary"></i>Detail Perjalanan
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold">Keperluan / Tujuan <span class="text-danger">*</span></label>
                            <input type="text" name="purpose" value="{{ old('purpose') }}"
                                class="form-control @error('purpose') is-invalid @enderror"
                                placeholder="Contoh: Kunjungan ke lokasi tambang Sulawesi" required>
                            @error('purpose') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Asal <span class="text-danger">*</span></label>
                            <input type="text" name="origin" value="{{ old('origin') }}"
                                class="form-control @error('origin') is-invalid @enderror"
                                placeholder="Contoh: Kantor Pusat Jakarta" required>
                            @error('origin') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Destinasi <span class="text-danger">*</span></label>
                            <input type="text" name="destination" value="{{ old('destination') }}"
                                class="form-control @error('destination') is-invalid @enderror"
                                placeholder="Contoh: Tambang Morowali, Sulteng" required>
                            @error('destination') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Tanggal Mulai <span class="text-danger">*</span></label>
                            <input type="datetime-local" name="start_date" value="{{ old('start_date') }}"
                                class="form-control @error('start_date') is-invalid @enderror" required>
                            @error('start_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Tanggal Selesai <span class="text-danger">*</span></label>
                            <input type="datetime-local" name="end_date" value="{{ old('end_date') }}"
                                class="form-control @error('end_date') is-invalid @enderror" required>
                            @error('end_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Jumlah Penumpang <span class="text-danger">*</span></label>
                            <input type="number" name="passenger_count" value="{{ old('passenger_count', 1) }}"
                                class="form-control @error('passenger_count') is-invalid @enderror" min="1" required>
                            @error('passenger_count') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Catatan</label>
                            <textarea name="notes" rows="2" class="form-control" placeholder="Catatan tambahan (opsional)">{{ old('notes') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Approvers --}}
            <div class="card mb-4">
                <div class="card-header py-3">
                    <i class="bi bi-person-check me-2 text-primary"></i>Pihak yang Menyetujui
                </div>
                <div class="card-body">
                    <div class="alert alert-info py-2 mb-3" style="font-size:.85rem;">
                        <i class="bi bi-info-circle me-1"></i>
                        Persetujuan berjenjang: Level 1 harus approve dulu sebelum Level 2 bisa memproses.
                    </div>
                    <div class="row g-3">
                        @php
                            $l1Approvers = $approvers->where('approval_level', 1);
                            $l2Approvers = $approvers->where('approval_level', 2);
                        @endphp
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                <span class="badge bg-info me-1">Level 1</span>
                                Approver Pertama <span class="text-danger">*</span>
                            </label>
                            <select name="approver_l1_id" class="form-select @error('approver_l1_id') is-invalid @enderror" required>
                                <option value="">-- Pilih Approver L1 --</option>
                                @foreach($l1Approvers as $approver)
                                <option value="{{ $approver->id }}" {{ old('approver_l1_id') == $approver->id ? 'selected' : '' }}>
                                    {{ $approver->name }} ({{ $approver->department }})
                                </option>
                                @endforeach
                            </select>
                            @error('approver_l1_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                <span class="badge bg-primary me-1">Level 2</span>
                                Approver Kedua <span class="text-danger">*</span>
                            </label>
                            <select name="approver_l2_id" class="form-select @error('approver_l2_id') is-invalid @enderror" required>
                                <option value="">-- Pilih Approver L2 --</option>
                                @foreach($l2Approvers as $approver)
                                <option value="{{ $approver->id }}" {{ old('approver_l2_id') == $approver->id ? 'selected' : '' }}>
                                    {{ $approver->name }} ({{ $approver->department }})
                                </option>
                                @endforeach
                            </select>
                            @error('approver_l2_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex gap-2 justify-content-end">
                <a href="{{ route('bookings.index') }}" class="btn btn-outline-secondary">Batal</a>
                <button type="submit" class="btn btn-primary px-4">
                    <i class="bi bi-send me-1"></i> Buat Pemesanan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
