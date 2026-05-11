@extends('layouts.app')

@section('title', 'Favori İlanlarım')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">Favori İlanlarım</h4>
    <span class="badge bg-primary">{{ $favorites->count() }} İlan</span>
</div>

<div class="row g-3">
    @forelse($favorites as $favorite)
        <div class="col-md-4 col-lg-3">
            <div class="card listing-card h-100 position-relative">
                
                {{-- Favoriden Çıkar Butonu (Zıplama Engelleyici AJAX Eklendi) --}}
                @auth
                    <div class="position-absolute" style="top: 10px; right: 10px; z-index: 10;">
                        <form action="{{ route('favorites.toggle', $favorite->listing) }}" method="POST" class="favorite-ajax-form">
                            @csrf
                            <button type="submit" class="btn btn-light btn-sm rounded-circle shadow-sm border-0" style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;">
                                <i class="bi bi-heart-fill text-danger"></i>
                            </button>
                        </form>
                    </div>
                @endauth

                {{-- Resim Alanı (Cloudinary Düzeltmesi Yapıldı) --}}
                <a href="{{ route('listings.show', $favorite->listing) }}" class="text-decoration-none">
                    <div class="bg-light d-flex align-items-center justify-content-center" style="height: 200px; overflow: hidden;">
                        @if($favorite->listing->photos->isNotEmpty())
                            <img src="{{ $favorite->listing->photos->first()->path }}"
                                 class="mw-100 mh-100" 
                                 style="object-fit: contain;" 
                                 alt="{{ $favorite->listing->title }}">
                        @else
                            <div class="d-flex align-items-center justify-content-center w-100 h-100 bg-secondary">
                                <i class="bi bi-image text-white" style="font-size:3rem"></i>
                            </div>
                        @endif
                    </div>
                </a>

                <div class="card-body">
                    <h6 class="card-title mb-1">
                        <a href="{{ route('listings.show', $favorite->listing) }}"
                           class="text-decoration-none text-dark text-truncate d-block">{{ $favorite->listing->title }}</a>
                    </h6>
                    <div class="text-primary fw-bold mb-2">{{ number_format($favorite->listing->price, 2) }} ₺</div>
                    <div class="d-flex justify-content-between text-muted" style="font-size:.8rem">
                        <span><i class="bi bi-geo-alt"></i> {{ $favorite->listing->city }}</span>
                        <span><i class="bi bi-eye"></i> {{ $favorite->listing->view_count }}</span>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12 text-center py-5 text-muted">
            <i class="bi bi-heart text-secondary" style="font-size:3rem"></i>
            <p class="mt-2">Henüz favori ilanınız bulunmuyor.</p>
            <a href="{{ route('home') }}" class="btn btn-outline-primary btn-sm mt-2">İlanlara Göz At</a>
        </div>
    @endforelse
</div>

{{-- Zıplama Engelleyici Script --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.favorite-ajax-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const card = this.closest('.col-md-4'); 
            
            fetch(this.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': this.querySelector('input[name="_token"]').value,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            }).then(response => {
                if (response.ok) {
                    card.style.transition = '0.3s';
                    card.style.opacity = '0';
                    setTimeout(() => card.remove(), 300);
                }
            });
        });
    });
});
</script>
@endsection