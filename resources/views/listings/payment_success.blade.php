@extends('layouts.app')

@section('content')
<div class="container py-5 text-center">
    <div class="mb-4">
        <i class="bi bi-check-circle-fill text-success" style="font-size: 5rem;"></i>
    </div>
    <h2 class="fw-bold mb-3">Ödeme Başarılı!</h2>
    <p class="text-muted mb-4">Ödemeniz başarıyla gerçekleşti. Satıcı en kısa sürede sizinle iletişime geçecektir.</p>
    <a href="{{ route('home') }}" class="btn btn-primary">
        <i class="bi bi-house"></i> Ana Sayfaya Dön
    </a>
</div>
@endsection