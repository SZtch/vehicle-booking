@extends('layouts.app')

@section('title', 'Tambah Driver')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('drivers.index') }}">Driver</a></li>
    <li class="breadcrumb-item active">Tambah</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Tambah Driver</h4>
    <a href="{{ route('drivers.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i>Kembali
    </a>
</div>

<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-body p-4">
                <form method="POST" action="{{ route('drivers.store') }}">
                    @csrf
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" name="name" value="{{ old('name') }}"
                                class="form-control @error('name') is-invalid @enderror"
                                placeholder="Nama lengkap driver">
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nomor SIM <span class="text-danger">*</span></label>
                            <input type="text" name="license_number" value="{{ old('license_number') }}"
                                class="form-control @error('license_number') is-invalid @enderror"
                                placeholder="SIM123456">
                            @error('license_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Telepon</label>
                            <input type="text" name="phone" value="{{ old('phone') }}"
                                class="form-control @error('phone') is-invalid @enderror"
                                placeholder="08xxxxxxxxxx">
                            @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Alamat</label>
                            <textarea name="address" rows="2"
                                class="form-control @error('address') is-invalid @enderror"
                                placeholder="Alamat lengkap driver">{{ old('address') }}</textarea>
                            @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
                            <select name="status" class="form-select @error('status') is-invalid @enderror">
                                <option value="available" @selected(old('status', 'available') === 'available')>Tersedia</option>
                                <option value="on_duty" @selected(old('status') === 'on_duty')>Bertugas</option>
                            </select>
                            @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <hr class="my-4">
                    <div class="d-flex gap-2 justify-content-end">
                        <a href="{{ route('drivers.index') }}" class="btn btn-outline-secondary">Batal</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-plus-lg me-1"></i>Tambah Driver
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
