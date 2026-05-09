<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ensar İlan — @yield('title', 'Ana Sayfa')</title>

    {{-- Bootstrap --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    {{-- Bootstrap Icons --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">

    {{-- Select2 CSS --}}
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    {{-- Select2 Bootstrap Theme --}}
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />

    {{-- Leaflet CSS --}}
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

    {{-- SweetAlert2 --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        body{
            background:#f8f9fa;
        }

        .navbar-brand{
            font-weight:800;
            color:#1a56db !important;
            font-size:1.5rem;
        }

        .listing-card{
            transition:0.2s;
            border:none;
            box-shadow:0 2px 8px rgba(0,0,0,.08);
        }

        .listing-card:hover{
            transform:translateY(-4px);
            box-shadow:0 8px 20px rgba(0,0,0,.12);
        }

        .listing-card img{
            height:200px;
            object-fit:cover;
        }

        .badge-active{
            background:#d1fae5;
            color:#065f46;
        }

        .badge-passive{
            background:#fef3c7;
            color:#92400e;
        }

        .badge-sold{
            background:#fee2e2;
            color:#991b1b;
        }

        .swal2-container{
            z-index:9999 !important;
        }

        .select2-container--bootstrap-5 .select2-selection{
            min-height:38px;
            border-radius:8px;
        }

        #map{
            height:400px;
            width:100%;
            border-radius:12px;
        }
    </style>
</head>

<body>

{{-- NAVBAR --}}
<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
    <div class="container">

        <a class="navbar-brand" href="{{ route('home') }}">
            <i class="bi bi-house-heart-fill"></i>
            Ensar İlan
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#nav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="nav">

            <ul class="navbar-nav ms-auto align-items-center gap-2">

                @auth

                    @if(Auth::user()->role === 'admin')
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('admin.dashboard') }}">
                                <i class="bi bi-shield-check"></i>
                                Admin Panel
                            </a>
                        </li>
                    @endif

                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('messages.index') }}">
                            <i class="bi bi-chat-dots"></i>
                            Mesajlar
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('favorites.index') }}">
                            <i class="bi bi-heart"></i>
                            Favoriler
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('profile.show') }}">
                            <i class="bi bi-person-circle"></i>
                            {{ Auth::user()->name }}
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="btn btn-primary btn-sm" href="{{ route('listings.create') }}">
                            <i class="bi bi-plus-circle"></i>
                            İlan Ver
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="btn btn-outline-secondary btn-sm"
                           href="{{ route('logout') }}"
                           onclick="event.preventDefault(); document.getElementById('logout-form').submit();">

                            Çıkış
                        </a>

                        <form id="logout-form"
                              action="{{ route('logout') }}"
                              method="POST"
                              class="d-none">

                            @csrf
                        </form>
                    </li>

                @else

                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('login') }}">
                            Giriş
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="btn btn-primary btn-sm" href="{{ route('register') }}">
                            Kayıt Ol
                        </a>
                    </li>

                @endauth

            </ul>

        </div>
    </div>
</nav>

{{-- CONTENT --}}
<div class="container py-4">

    @if(session('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Başarılı',
                text: "{{ session('success') }}",
                timer: 2500,
                showConfirmButton: false,
                toast: true,
                position: 'top-end'
            });
        </script>
    @endif

    @if(session('error'))
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Hata',
                text: "{{ session('error') }}"
            });
        </script>
    @endif

    @yield('content')

</div>

{{-- FOOTER --}}
<footer class="bg-white border-top py-4 mt-5">
    <div class="container text-center text-muted">
        <small>
            © {{ date('Y') }} Ensar İlan. Tüm hakları saklıdır.
        </small>
    </div>
</footer>

{{-- JQUERY --}}
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

{{-- BOOTSTRAP --}}
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

{{-- SELECT2 --}}
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

{{-- LEAFLET --}}
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
    $(document).ready(function () {

        // SELECT2 AKTİF ET
        $('.select2-searchable').select2({
            theme: 'bootstrap-5',
            width: '100%',
            placeholder: 'Seçiniz...',
            allowClear: true
        });

        // DELETE SWEETALERT
        document.addEventListener('click', function (e) {

            if (e.target.closest('.delete-btn')) {

                e.preventDefault();

                const button = e.target.closest('.delete-btn');
                const form = button.closest('form');

                Swal.fire({
                    title: 'Emin misiniz?',
                    text: 'Bu işlem geri alınamaz!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Evet, sil',
                    cancelButtonText: 'İptal',
                    confirmButtonColor: '#dc3545'
                }).then((result) => {

                    if (result.isConfirmed) {
                        form.submit();
                    }

                });

            }

        });

    });
</script>

@stack('scripts')

</body>
</html>