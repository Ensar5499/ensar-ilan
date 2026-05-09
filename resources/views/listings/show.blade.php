@extends('layouts.app')

@section('title', $listing->title)

@section('content')
<div class="row">
    <div class="col-lg-8">
        {{-- Fotoğraf Galerisi --}}
        @if($listing->photos->isNotEmpty())
            <div id="photoCarousel" class="carousel slide mb-4 rounded overflow-hidden shadow-sm bg-light"
                 data-bs-ride="carousel">
                <div class="carousel-inner">
                    @foreach($listing->photos as $i => $photo)
                        <div class="carousel-item {{ $i === 0 ? 'active' : '' }}">
                            <div class="d-flex align-items-center justify-content-center" style="height: 500px;">
                                <img src="{{ Storage::url($photo->path) }}"
                                     class="mw-100 mh-100 d-block shadow-sm" 
                                     style="width: auto; height: auto; object-fit: contain;"
                                     alt="Fotoğraf {{ $i+1 }}">
                            </div>
                        </div>
                    @endforeach
                </div>
                @if($listing->photos->count() > 1)
                    <button class="carousel-control-prev" type="button"
                            data-bs-target="#photoCarousel" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon bg-dark rounded-circle px-3 py-3"></span>
                    </button>
                    <button class="carousel-control-next" type="button"
                            data-bs-target="#photoCarousel" data-bs-slide="next">
                        <span class="carousel-control-next-icon bg-dark rounded-circle px-3 py-3"></span>
                    </button>
                @endif
            </div>
        @endif

        {{-- İlan Bilgileri --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <h2 class="mb-0">{{ $listing->title }}</h2>
                    <span class="badge badge-{{ $listing->status }} rounded-pill px-3 py-2">
                        @if($listing->status === 'active') Aktif
                        @elseif($listing->status === 'passive') Pasif
                        @else Satıldı @endif
                    </span>
                </div>
                <div class="h3 text-primary mb-3">{{ number_format($listing->price, 2) }} ₺</div>
                <div class="row text-muted mb-3">
                    <div class="col-auto"><i class="bi bi-geo-alt"></i> {{ $listing->city }}
                        {{ $listing->district ? '/ '.$listing->district : '' }}</div>
                    <div class="col-auto"><i class="bi bi-eye"></i> {{ $listing->view_count }} görüntülenme</div>
                    <div class="col-auto"><i class="bi bi-clock"></i> {{ $listing->created_at->format('d.m.Y') }}</div>
                </div>
                <hr>
                <p class="mb-0" style="white-space: pre-line">{{ $listing->description }}</p>
            </div>
        </div>

        {{-- İlan sahibinin kendi ilanıysa düzenleme ve silme butonları --}}
        @auth
            @if(Auth::id() === $listing->user_id)
                <div class="d-flex gap-2 mb-4">
                    <a href="{{ route('listings.edit', $listing) }}"
                       class="btn btn-warning">
                       <i class="bi bi-pencil"></i> Düzenle
                    </a>
                    
                    {{-- Onaylı Silme Butonu --}}
                    <form method="POST" action="{{ route('listings.destroy', $listing) }}"
                          onsubmit="return confirm('Bu ilanı silmek istediğinize emin misiniz? Bu işlem geri alınamaz.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="bi bi-trash"></i> İlanı Sil
                        </button>
                    </form>
                </div>
            @endif
        @endauth

        {{-- Yorumlar --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white fw-bold">
                <i class="bi bi-chat-left-text"></i> Yorumlar ({{ $listing->comments->count() }})
            </div>
            <div class="card-body">
                @foreach($listing->comments as $comment)
                    <div class="mb-3 pb-3 border-bottom">
                        <div class="d-flex justify-content-between">
                            <strong>{{ $comment->user->name }}</strong>
                            <small class="text-muted">{{ $comment->created_at->diffForHumans() }}</small>
                        </div>
                        <p class="mb-0 mt-1">{{ $comment->body }}</p>
                    </div>
                @endforeach

                @auth
                    <form method="POST" action="{{ route('comments.store', $listing) }}">
                        @csrf
                        <textarea name="body" class="form-control mb-2"
                                  rows="2" placeholder="Yorum yaz..."></textarea>
                        <button class="btn btn-primary btn-sm">Yorum Gönder</button>
                    </form>
                @endauth
            </div>
        </div>

        {{-- Şikayet --}}
        @auth
            @if(Auth::id() !== $listing->user_id)
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <button class="btn btn-outline-danger btn-sm" data-bs-toggle="collapse"
                                data-bs-target="#complaintForm">
                            <i class="bi bi-flag"></i> Bu İlanı Şikayet Et
                        </button>
                        <div class="collapse mt-3" id="complaintForm">
                            <form method="POST" action="{{ route('complaints.store', $listing) }}">
                                @csrf
                                <textarea name="reason" class="form-control mb-2"
                                          rows="2" placeholder="Şikayet sebebi..."></textarea>
                                <button class="btn btn-danger btn-sm">Şikayet Gönder</button>
                            </form>
                        </div>
                    </div>
                </div>
            @endif
        @endauth
    </div>

    {{-- Sağ Panel: Satıcı Bilgisi --}}
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body text-center">
                <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center mx-auto mb-3"
                     style="width:64px;height:64px">
                    <span class="text-white fw-bold fs-4">
                        {{ strtoupper(substr($listing->user->name, 0, 1)) }}
                    </span>
                </div>
                <h5 class="mb-0">{{ $listing->user->name }}</h5>
                <p class="text-muted small">{{ $listing->user->city }}</p>
                @if($listing->user->phone)
                    <a href="tel:{{ $listing->user->phone }}" class="btn btn-success w-100 mb-2">
                        <i class="bi bi-telephone"></i> {{ $listing->user->phone }}
                    </a>
                @endif
                @auth
                    @if(Auth::id() !== $listing->user_id)
                        <a href="{{ route('messages.chat', ['receiver_id' => $listing->user_id, 'listing_id' => $listing->id]) }}"
                           class="btn btn-outline-primary w-100 mb-2">
                            <i class="bi bi-chat"></i> Mesaj Gönder
                        </a>

                        {{-- Ödeme Butonu Eklendi --}}
                        <form method="POST" action="{{ route('checkout.pay') }}" class="mb-2">
                            @csrf
                            <input type="hidden" name="amount" value="{{ $listing->price }}">
                            <input type="hidden" name="description" value="{{ $listing->title }} - İlan Ödemesi">
                            <button type="submit" class="btn btn-success w-100">
                                <i class="bi bi-credit-card"></i> Satın Al / Ödeme Yap
                            </button>
                        </form>
                        
                        <form method="POST" action="{{ route('favorites.toggle', $listing) }}">
                            @csrf
                            <button class="btn btn-outline-danger w-100">
                                <i class="bi bi-heart"></i>
                                @auth
                                    {{ $listing->isFavoritedByUser(Auth::id()) ? 'Favoriden Çıkar' : 'Favorilere Ekle' }}
                                @endauth
                            </button>
                        </form>
                    @endif
                @endauth
            </div>
        </div>

        {{-- HARİTA EKLEMESİ --}}
        @if($listing->lat && $listing->lng)
        <div class="card border-0 shadow-sm mb-3 overflow-hidden">
            <div class="card-header bg-white fw-bold border-0 pt-3">
                <i class="bi bi-geo-alt-fill text-danger"></i> İlan Konumu
            </div>
            <div class="card-body p-0">
                <div id="detailMap" style="height: 300px; width: 100%; z-index: 1;"></div>
            </div>
        </div>

        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                var lat = parseFloat("{{ $listing->lat }}");
                var lng = parseFloat("{{ $listing->lng }}");
                
                var map = L.map('detailMap').setView([lat, lng], 14);

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; OpenStreetMap'
                }).addTo(map);

                L.marker([lat, lng]).addTo(map);

                setTimeout(function(){ map.invalidateSize(); }, 400);
            });
        </script>
        @endif
    </div>
</div>
@endsection