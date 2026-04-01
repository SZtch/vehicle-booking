@extends('layouts.app')

@section('title', 'Edit Kendaraan')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('vehicles.index') }}">Kendaraan</a></li>
    <li class="breadcrumb-item active">Edit</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Edit Kendaraan</h4>
    <a href="{{ route('vehicles.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i>Kembali
    </a>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body p-4">
                <form method="POST" action="{{ route('vehicles.update', $vehicle) }}">
                    @csrf @method('PUT')
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label fw-semibold">Nama Kendaraan <span class="text-danger">*</span></label>
                            <input type="text" name="name" value="{{ old('name', $vehicle->name) }}"
                                class="form-control @error('name') is-invalid @enderror">
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Plat Nomor <span class="text-danger">*</span></label>
                            <input type="text" name="plate_number" value="{{ old('plate_number', $vehicle->plate_number) }}"
                                class="form-control @error('plate_number') is-invalid @enderror"
                                style="text-transform:uppercase">
                            @error('plate_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Merek</label>
                            <input type="text" name="brand" value="{{ old('brand', $vehicle->brand) }}"
                                class="form-control @error('brand') is-invalid @enderror">
                            @error('brand')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Model</label>
                            <input type="text" name="model" value="{{ old('model', $vehicle->model) }}"
                                class="form-control @error('model') is-invalid @enderror">
                            @error('model')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Tahun</label>
                            <input type="number" name="year" value="{{ old('year', $vehicle->year) }}"
                                class="form-control @error('year') is-invalid @enderror"
                                min="2000" max="{{ date('Y') + 1 }}">
                            @error('year')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Tipe Kendaraan <span class="text-danger">*</span></label>
                            <select name="type" class="form-select @error('type') is-invalid @enderror">
                                <option value="angkutan_orang" @selected(old('type', $vehicle->type) === 'angkutan_orang')>Angkutan Orang</option>
                                <option value="angkutan_barang" @selected(old('type', $vehicle->type) === 'angkutan_barang')>Angkutan Barang</option>
                            </select>
                            @error('type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Kepemilikan <span class="text-danger">*</span></label>
                            <select name="ownership" class="form-select @error('ownership') is-invalid @enderror">
                                <option value="owned" @selected(old('ownership', $vehicle->ownership) === 'owned')>Milik Perusahaan</option>
                                <option value="rented" @selected(old('ownership', $vehicle->ownership) === 'rented')>Sewa</option>
                            </select>
                            @error('ownership')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
                            <select name="status" class="form-select @error('status') is-invalid @enderror">
                                <option value="available" @selected(old('status', $vehicle->status) === 'available')>Tersedia</option>
                                <option value="in_use" @selected(old('status', $vehicle->status) === 'in_use')>Digunakan</option>
                                <option value="maintenance" @selected(old('status', $vehicle->status) === 'maintenance')>Maintenance</option>
                            </select>
                            @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Odometer (km)</label>
                            <input type="number" name="odometer" value="{{ old('odometer', $vehicle->odometer) }}"
                                class="form-control @error('odometer') is-invalid @enderror" min="0">
                            @error('odometer')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Service Terakhir</label>
                            <input type="date" name="last_service"
                                value="{{ old('last_service', $vehicle->last_service?->format('Y-m-d')) }}"
                                class="form-control @error('last_service') is-invalid @enderror">
                            @error('last_service')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Service Berikutnya</label>
                            <input type="date" name="next_service"
                                value="{{ old('next_service', $vehicle->next_service?->format('Y-m-d')) }}"
                                class="form-control @error('next_service') is-invalid @enderror">
                            @error('next_service')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <hr class="my-4">
                    <div class="d-flex gap-2 justify-content-end">
                        <a href="{{ route('vehicles.index') }}" class="btn btn-outline-secondary">Batal</a>
                        <button type="submit" class="btn btn-warning">
                            <i class="bi bi-save me-1"></i>Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
