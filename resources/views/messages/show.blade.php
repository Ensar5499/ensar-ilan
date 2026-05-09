@extends('layouts.app')

@section('title', 'Mesajlaşma')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <div>
                    <strong>{{ $user->name }}</strong>
                    <span class="text-muted ms-2">— İlan: {{ Str::limit($listing->title, 40) }}</span>
                </div>
                <a href="{{ route('listings.show', $listing) }}" class="btn btn-sm btn-outline-secondary">İlana Git</a>
            </div>
            
            <div class="card-body" style="height:400px;overflow-y:auto" id="messages-box">
                @foreach($messages as $msg)
                    <div class="d-flex mb-3 {{ $msg->sender_id === Auth::id() ? 'justify-content-end' : '' }}">
                        <div class="p-2 px-3 rounded-3 {{ $msg->sender_id === Auth::id() ? 'bg-primary text-white' : 'bg-light' }}"
                             style="max-width:70%">
                            {{ $msg->body }}
                            <div class="mt-1" style="font-size:.7rem;opacity:.7">
                                {{ $msg->created_at->format('H:i') }}
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="card-footer bg-white">
                <form method="POST" action="{{ route('messages.store') }}">
                    @csrf
                    <input type="hidden" name="receiver_id" value="{{ $user->id }}">
                    <input type="hidden" name="listing_id" value="{{ $listing->id }}">
                    <div class="d-flex gap-2">
                        <input type="text" name="body" class="form-control"
                               placeholder="Mesajınızı yazın..." required autocomplete="off">
                        <button class="btn btn-primary">
                            <i class="bi bi-send"></i> Gönder
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Sayfa yüklendiğinde mesaj kutusunu en alta kaydır
    document.addEventListener("DOMContentLoaded", function() {
        var box = document.getElementById('messages-box');
        if (box) {
            box.scrollTop = box.scrollHeight;
        }
    });
</script>
@endpush
@endsection