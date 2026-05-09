@extends('layouts.app')

@section('title', 'Kayıt Ol')

@section('content')
<div class="row justify-content-center align-items-center" style="min-height: 70vh;">
    <div class="col-md-5">
        <div class="card border-0 shadow-lg rounded-4">
            <div class="card-body p-5">
                {{-- Logo veya Başlık Alanı --}}
                <div class="text-center mb-4">
                    <div class="display-6 text-primary mb-2">
                        <i class="bi bi-person-plus-fill"></i>
                    </div>
                    <h3 class="fw-bold text-dark">Yeni Hesap Oluştur</h3>
                    <p class="text-muted small">Ensar İlan dünyasına hoş geldiniz!</p>
                </div>

                <form method="POST" action="{{ route('register') }}">
                    @csrf

                    <div class="mb-3">
                        <label for="name" class="form-label small fw-bold text-secondary">Ad Soyad</label>
                        <div class="input-group shadow-sm">
                            <span class="input-group-text bg-white border-end-0 text-primary">
                                <i class="bi bi-person"></i>
                            </span>
                            <input id="name" type="text" name="name" 
                                   class="form-control border-start-0 ps-0 @error('name') is-invalid @enderror" 
                                   value="{{ old('name') }}" required autofocus placeholder="Adınız Soyadınız">
                        </div>
                        @error('name') <div class="invalid-feedback d-block mt-1 small">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label small fw-bold text-secondary">E-posta Adresi</label>
                        <div class="input-group shadow-sm">
                            <span class="input-group-text bg-white border-end-0 text-primary">
                                <i class="bi bi-envelope"></i>
                            </span>
                            <input id="email" type="email" name="email" 
                                   class="form-control border-start-0 ps-0 @error('email') is-invalid @enderror" 
                                   value="{{ old('email') }}" required placeholder="ornek@mail.com">
                        </div>
                        @error('email') <div class="invalid-feedback d-block mt-1 small">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label small fw-bold text-secondary">Şifre</label>
                        <div class="input-group shadow-sm">
                            <span class="input-group-text bg-white border-end-0 text-primary">
                                <i class="bi bi-lock"></i>
                            </span>
                            <input id="password" type="password" name="password" 
                                   class="form-control border-start-0 ps-0 @error('password') is-invalid @enderror" 
                                   required placeholder="••••••••">
                        </div>
                        @error('password') <div class="invalid-feedback d-block mt-1 small">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-4">
                        <label for="password_confirmation" class="form-label small fw-bold text-secondary">Şifreyi Onayla</label>
                        <div class="input-group shadow-sm">
                            <span class="input-group-text bg-white border-end-0 text-primary">
                                <i class="bi bi-shield-lock"></i>
                            </span>
                            <input id="password_confirmation" type="password" name="password_confirmation" 
                                   class="form-control border-start-0 ps-0" required placeholder="••••••••">
                        </div>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary btn-lg rounded-3 fw-bold shadow-sm py-2">
                            Kayıt Olmayı Tamamla
                        </button>
                    </div>

                    <div class="text-center mt-4 border-top pt-3">
                        <p class="mb-0 text-muted small">Zaten üye misiniz?</p>
                        <a href="{{ route('login') }}" class="text-primary fw-bold text-decoration-none">
                            <i class="bi bi-box-arrow-in-right me-1"></i> Giriş Yapın
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection