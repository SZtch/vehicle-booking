<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistem Pemesanan Kendaraan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #1e3a5f 0%, #2d5f9e 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
        }
        .login-card {
            border: none;
            border-radius: 1rem;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }
        .login-brand {
            background: linear-gradient(135deg, #1e3a5f, #2d5f9e);
            border-radius: 1rem 1rem 0 0;
            padding: 2rem;
            text-align: center;
        }
        .btn-login {
            background: #1e3a5f;
            border: none;
            border-radius: .5rem;
            padding: .75rem;
            font-weight: 600;
            letter-spacing: .02em;
        }
        .btn-login:hover { background: #2d5f9e; }
        .form-control:focus {
            border-color: #2d5f9e;
            box-shadow: 0 0 0 .2rem rgba(30,58,95,.15);
        }
    </style>
</head>
<body>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-5 col-lg-4">
            <div class="card login-card">
                <div class="login-brand">
                    <i class="bi bi-truck text-warning" style="font-size: 2.5rem;"></i>
                    <h4 class="text-white fw-bold mt-2 mb-0">Fleet App</h4>
                    <small class="text-white-50">Sistem Pemesanan Kendaraan</small>
                </div>
                <div class="card-body p-4">
                    @if(session('error'))
                        <div class="alert alert-danger py-2">{{ session('error') }}</div>
                    @endif

                    <form method="POST" action="{{ route('login.post') }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-muted small">EMAIL</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="bi bi-envelope text-muted"></i>
                                </span>
                                <input
                                    type="email"
                                    name="email"
                                    class="form-control border-start-0 @error('email') is-invalid @enderror"
                                    value="{{ old('email') }}"
                                    placeholder="email@perusahaan.com"
                                    required autofocus
                                >
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold text-muted small">PASSWORD</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="bi bi-lock text-muted"></i>
                                </span>
                                <input
                                    type="password"
                                    name="password"
                                    class="form-control border-start-0 @error('password') is-invalid @enderror"
                                    placeholder="••••••••"
                                    required
                                >
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" name="remember" id="remember">
                            <label class="form-check-label text-muted small" for="remember">
                                Ingat saya
                            </label>
                        </div>

                        <button type="submit" class="btn btn-login btn-primary w-100 text-white">
                            <i class="bi bi-box-arrow-in-right me-2"></i>Masuk
                        </button>
                    </form>

                    <div class="text-center mt-4 pt-3 border-top">
                        <small class="text-muted d-block mb-1">Demo Credentials:</small>
                        <small class="text-muted">Admin: admin@fleet.com</small><br>
                        <small class="text-muted">Approver: approver1@fleet.com</small><br>
                        <small class="text-muted">Password: 12345</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
