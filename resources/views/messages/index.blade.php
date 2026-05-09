@extends('layouts.app')

@section('title', 'Mesajlarım')

@section('content')
<h4 class="mb-4">Mesajlarım</h4>
<div class="card border-0 shadow-sm">
    <div class="list-group list-group-flush">
        @forelse($conversations as $msg)
            @php
                $other = $msg->sender_id === Auth::id() ? $msg->receiver : $msg->sender;
            @endphp
            {{-- Rota ismi ve parametreleri senin yeni sistemine göre güncellendi --}}
            <a href="{{ route('messages.chat', ['receiver_id' => $other->id, 'listing_id' => $msg->listing_id]) }}"
               class="list-group-item list-group-item-action {{ !$msg->is_read && $msg->receiver_id === Auth::id() ? 'fw-bold' : '' }}">
                <div class="d-flex justify-content-between">
                    <span>{{ $other->name }}</span>
                    <small class="text-muted">{{ $msg->created_at->diffForHumans() }}</small>
                </div>
                <small class="text-muted d-block">İlan: {{ Str::limit($msg->listing->title, 40) }}</small>
                <small class="text-truncate d-block">{{ Str::limit($msg->body, 60) }}</small>
            </a>
        @empty
            <div class="list-group-item text-center text-muted py-5">
                <i class="bi bi-chat-dots" style="font-size:2rem"></i>
                <p class="mt-2 mb-0">Henüz mesajınız yok.</p>
            </div>
        @endforelse
    </div>
</div>
@endsection