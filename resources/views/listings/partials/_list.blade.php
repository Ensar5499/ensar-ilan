<div class="row g-3">
    @forelse($listings as $listing)
        <div class="col-md-4 col-lg-3">
            <div class="card listing-card h-100 position-relative"> 
                @auth
                    <div class="position-absolute" style="top: 10px; right: 10px; z-index: 10;">
                        <form action="{{ route('favorites.toggle', $listing) }}" method="POST" class="favorite-ajax-form">
                            @csrf
                            <button type="submit" class="btn btn-light btn-sm rounded-circle shadow-sm border-0" style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;">
                                @if(Auth::user()->favorites()->where('listing_id', $listing->id)->exists())
                                    <i class="bi bi-heart-fill text-danger"></i>
                                @else
                                    <i class="bi bi-heart text-secondary"></i>
                                @endif
                            </button>
                        </form>
                    </div>
                @endauth

                <a href="{{ route('listings.show', $listing) }}" class="text-decoration-none">
                    <div class="bg-light d-flex align-items-center justify-content-center" style="height: 200px; overflow: hidden;">
                        @if($listing->photos->isNotEmpty())
                            {{-- DEĞİŞİKLİK BURADA: Artık Storage::url kullanmıyoruz, direkt path basıyoruz --}}
                            <img src="{{ $listing->photos->first()->path }}" class="mw-100 mh-100" style="object-fit: contain;" alt="{{ $listing->title }}">
                        @else
                            <div class="d-flex align-items-center justify-content-center w-100 h-100 bg-secondary text-white-50">
                                <i class="bi bi-image" style="font-size:3rem"></i>
                            </div>
                        @endif
                    </div>
                </a>

                <div class="card-body">
                    <h6 class="card-title mb-1 text-truncate">
                        <a href="{{ route('listings.show', $listing) }}" class="text-decoration-none text-dark">{{ $listing->title }}</a>
                    </h6>
                    <div class="text-primary fw-bold mb-2">{{ number_format($listing->price, 2) }} ₺</div>
                    <div class="d-flex justify-content-between text-muted" style="font-size:.8rem">
                        <span><i class="bi bi-geo-alt"></i> {{ $listing->city }}</span>
                        <span><i class="bi bi-eye"></i> {{ $listing->view_count }}</span>
                    </div>
                    <div class="text-muted mt-1" style="font-size:.75rem">
                        {{ $listing->created_at->diffForHumans() }}
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12 text-center py-5 text-muted">
            <i class="bi bi-inbox" style="font-size:3rem"></i>
            <p class="mt-2">Hiç ilan bulunamadı.</p>
        </div>
    @endforelse
</div>

<div class="mt-4 d-flex justify-content-center" id="pagination-links">
    {{ $listings->withQueryString()->links() }}
</div>