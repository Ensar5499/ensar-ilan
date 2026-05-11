@extends('layouts.app')

@section('title', 'Profilim')

@section('content')
<div class="row">
    <div class="col-md-4">
        {{-- Profil Bilgileri --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white fw-bold">Profil Bilgileri</div>
            <div class="card-body">
                @if(!$user->iban)
                    <div class="alert alert-warning small">
                        <i class="bi bi-exclamation-triangle"></i>
                        İlan verebilmek için IBAN numaranızı eklemeniz zorunludur.
                    </div>
                @endif
                <form method="POST" action="{{ route('profile.update') }}">
                    @csrf @method('PUT')
                    <div class="mb-3">
                        <label class="form-label">Ad Soyad</label>
                        <input type="text" name="name" class="form-control" value="{{ $user->name }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">E-posta</label>
                        <input type="email" class="form-control" value="{{ $user->email }}" disabled>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Telefon</label>
                        <input type="text" name="phone" class="form-control"
                               value="{{ $user->phone }}" placeholder="05XX XXX XX XX">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">IBAN <span class="text-danger">*</span></label>
                        <input type="text" name="iban" class="form-control"
                               value="{{ $user->iban }}" placeholder="TR...">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Şehir</label>
                        <input type="text" name="city" class="form-control" value="{{ $user->city }}">
                    </div>
                    <button class="btn btn-primary w-100">Güncelle</button>
                </form>
            </div>
        </div>

        {{-- Şifre Değiştir --}}
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-bold">Şifre Değiştir</div>
            <div class="card-body">
                <form method="POST" action="{{ route('profile.password') }}">
                    @csrf @method('PUT')
                    <div class="mb-3">
                        <label class="form-label">Mevcut Şifre</label>
                        <input type="password" name="current_password" class="form-control">
                        @error('current_password')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Yeni Şifre</label>
                        <input type="password" name="password" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Yeni Şifre Tekrar</label>
                        <input type="password" name="password_confirmation" class="form-control">
                    </div>
                    <button class="btn btn-warning w-100">Şifreyi Değiştir</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        {{-- Benim İlanlarım --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white fw-bold">
                İlanlarım ({{ $listings->count() }})
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>İlan</th>
                                <th>Fiyat</th>
                                <th>Durum</th>
                                <th>Görüntülenme</th>
                                <th>İşlem</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($listings as $listing)
                                <tr>
                                    <td>
                                        <a href="{{ route('listings.show', $listing) }}"
                                           class="text-decoration-none">{{ Str::limit($listing->title, 40) }}</a>
                                    </td>
                                    <td>{{ number_format($listing->price, 2) }} ₺</td>
                                    <td>
                                        <span class="badge badge-{{ $listing->status }}">
                                            {{ ['active'=>'Aktif','passive'=>'Pasif','sold'=>'Satıldı'][$listing->status] }}
                                        </span>
                                    </td>
                                    <td>{{ $listing->view_count }}</td>
                                    <td>
                                        <div class="d-flex gap-1">
                                            <a href="{{ route('listings.edit', $listing) }}"
                                               class="btn btn-sm btn-warning">Düzenle</a>
                                            
                                            {{-- SweetAlert2 Uyumlu Silme Formu --}}
                                            <form method="POST" action="{{ route('listings.destroy', $listing) }}" class="delete-form">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn btn-sm btn-danger delete-btn">Sil</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="text-center text-muted py-3">Henüz ilan yok.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Favorilerim --}}
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-bold">
                Favorilerim ({{ $favorites->count() }})
            </div>
            <div class="card-body">
                <div class="row g-2">
                    @forelse($favorites as $fav)
                        <div class="col-md-4">
                            <div class="card listing-card h-100">
                                <a href="{{ route('listings.show', $fav->listing) }}" class="text-decoration-none">
                                    <div class="bg-light d-flex align-items-center justify-content-center" style="height: 100px; overflow: hidden;">
                                        @if($fav->listing->photos->isNotEmpty())
                                            <img src="{{ $fav->listing->photos->first()->path }}"
                                                 class="mw-100 mh-100" 
                                                 style="object-fit: contain;" 
                                                 alt="{{ $fav->listing->title }}">
                                        @else
                                            <div class="d-flex align-items-center justify-content-center w-100 h-100 bg-secondary">
                                                <i class="bi bi-image text-white" style="font-size:1.5rem"></i>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="card-body p-2 text-dark">
                                        <small class="fw-bold d-block text-truncate">{{ $fav->listing->title }}</small>
                                        <small class="text-primary">{{ number_format($fav->listing->price, 2) }} ₺</small>
                                    </div>
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="col-12 text-muted text-center py-3">Favori ilan yok.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.querySelectorAll('.delete-btn').forEach(button => {
    button.addEventListener('click', function(e) {
        const form = this.closest('.delete-form');
        
        Swal.fire({
            title: 'Emin misiniz?',
            text: "Bu ilanı sildiğinizde geri getiremezsiniz!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Evet, sil!',
            cancelButtonText: 'İptal'
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });
});
</script>
@endpush

@endsection