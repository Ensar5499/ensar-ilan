@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<h4 class="mb-4">Dashboard</h4>

{{-- İstatistik Kartları --}}
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm text-center p-3">
            <div class="fs-1 text-primary">{{ $stats['total_listings'] }}</div>
            <div class="text-muted">Toplam İlan</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm text-center p-3">
            <div class="fs-1 text-success">{{ $stats['active_listings'] }}</div>
            <div class="text-muted">Aktif İlan</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm text-center p-3">
            <div class="fs-1 text-info">{{ $stats['total_users'] }}</div>
            <div class="text-muted">Toplam Kullanıcı</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm text-center p-3">
            <div class="fs-1 text-danger">{{ $stats['total_complaints'] }}</div>
            <div class="text-muted">Bekleyen Şikayet</div>
        </div>
    </div>
</div>

{{-- Sistem Kontrol Paneli --}}
<div class="row">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-bold py-3">
                <i class="bi bi-sliders me-2"></i> Sistem Kontrol Anahtarları
            </div>
            <div class="card-body">
                {{-- Bakım Modu --}}
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <div class="fw-bold">Bakım Modu</div>
                        <div class="small text-muted">Siteyi ziyaretçilere kapatır, sadece adminler erişebilir.</div>
                    </div>
                    <div class="form-check form-switch">
                        <input class="form-check-input setting-toggle" type="checkbox" role="switch" 
                               data-key="maintenance_mode" {{ (isset($settings['maintenance_mode']) && $settings['maintenance_mode'] == '1') ? 'checked' : '' }} 
                               style="width: 2.5em; height: 1.25em; cursor: pointer;">
                    </div>
                </div>

                <hr class="text-muted opacity-25">

                {{-- İlan Girişi --}}
                <div class="d-flex justify-content-between align-items-center mt-4">
                    <div>
                        <div class="fw-bold">Yeni İlan Girişi</div>
                        <div class="small text-muted">Kullanıcıların yeni ilan oluşturma yetkisini askıya alır.</div>
                    </div>
                    <div class="form-check form-switch">
                        <input class="form-check-input setting-toggle" type="checkbox" role="switch" 
                               data-key="disable_listings" {{ (isset($settings['disable_listings']) && $settings['disable_listings'] == '1') ? 'checked' : '' }} 
                               style="width: 2.5em; height: 1.25em; cursor: pointer;">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Bildirim --}}
<div class="position-fixed bottom-0 end-0 p-3" style="z-index: 9999">
    <div id="settingToast" class="toast align-items-center text-white bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body" id="toastMessage">
                Ayar başarıyla güncellendi.
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>
</div>

{{-- Scriptleri garantiye almak için Section dışına da koyabiliriz ama şimdilik burada kalsın --}}
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    console.log("JQuery Hazır!");

    $('.setting-toggle').on('change', function() {
        const checkbox = $(this);
        const key = checkbox.data('key');
        const value = checkbox.is(':checked') ? '1' : '0';

        // İstek sırasında kilitle
        checkbox.prop('disabled', true);

        $.ajax({
            url: "{{ route('admin.settings.update') }}",
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                key: key,
                value: value
            },
            success: function(response) {
                checkbox.prop('disabled', false);
                
                // Toast göster
                const toastContent = document.getElementById('settingToast');
                const toast = new bootstrap.Toast(toastContent);
                $('#toastMessage').text(response.message);
                toast.show();
                console.log("Güncellendi: " + key + " = " + value);
            },
            error: function(xhr) {
                checkbox.prop('disabled', false);
                checkbox.prop('checked', !checkbox.is(':checked')); // Geri al
                
                console.error("Hata Detayı:", xhr.responseText);
                alert('Hata: ' + xhr.status + '. Detay konsolda (F12).');
            }
        });
    });
});
</script>

@endsection