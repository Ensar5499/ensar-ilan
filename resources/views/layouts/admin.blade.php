<!DOCTYPE html>
<html lang="tr" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin — @yield('title')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    
    {{-- SweetAlert2 Kütüphanesi Buraya Eklendi --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        /* Sidebar ve İçeriği yan yana zorlayan yapı */
        body, html { height: 100%; margin: 0; transition: background .3s ease; }
        .admin-container { display: flex; min-height: 100vh; width: 100%; }
        
        .sidebar { 
            width: 240px; 
            min-width: 240px; 
            background: #1e293b; 
            color: #fff;
            position: sticky;
            top: 0;
            height: 100vh;
        }
        
        .sidebar .nav-link { color: #94a3b8; border-radius: 6px; margin-bottom: 4px; }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { background: #334155; color: #fff; }
        .sidebar .brand { color: #fff; font-size: 1.2rem; font-weight: 700; border-bottom: 1px solid #334155; }

        .main-content { 
            flex-grow: 1; 
            background: #f8fafc; 
            padding: 25px;
            overflow-x: hidden;
        }

        /* SweetAlert Bildirim Stilini Düzeltme */
        .swal2-container { z-index: 9999 !important; }

        /* DARK MODE UYARLAMALARI */
        [data-bs-theme="dark"] .main-content {
            background: #121212;
            color: #e2e8f0;
        }
        [data-bs-theme="dark"] .card {
            background: #1e1e1e;
            border-color: #333;
            color: #fff;
        }
        [data-bs-theme="dark"] .table {
            color: #e2e8f0;
            border-color: #333;
        }
    </style>

    <script>
        (function() {
            const savedTheme = localStorage.getItem('theme') || 'light';
            document.documentElement.setAttribute('data-bs-theme', savedTheme);
        })();
    </script>
</head>
<body class="bg-light">

<div class="admin-container">
    {{-- Sidebar --}}
    <aside class="sidebar p-3">
        <div class="brand mb-4 pb-3 px-2 d-flex align-items-center justify-content-between">
            <span><i class="bi bi-shield-check"></i> Admin Panel</span>
            
            {{-- Karanlık Mod Toggle --}}
            <a href="#" id="darkModeToggle" style="color: #94a3b8; text-decoration: none;">
                <i class="bi bi-moon-stars-fill" id="darkModeIcon"></i>
            </a>
        </div>
        
        <nav class="nav flex-column gap-1">
            <a href="{{ route('admin.dashboard') }}"
               class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>
            <a href="{{ route('admin.listings.index') }}"
               class="nav-link {{ request()->routeIs('admin.listings.*') ? 'active' : '' }}">
                <i class="bi bi-list-ul"></i> İlanlar
            </a>
            <a href="{{ route('admin.users.index') }}"
               class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                <i class="bi bi-people"></i> Kullanıcılar
            </a>
            <a href="{{ route('admin.complaints.index') }}"
               class="nav-link {{ request()->routeIs('admin.complaints.*') ? 'active' : '' }}">
                <i class="bi bi-flag"></i> Şikayetler
            </a>
            <hr style="border-color:#334155">
            <a href="{{ route('home') }}" class="nav-link">
                <i class="bi bi-house"></i> Siteye Dön
            </a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="nav-link w-100 text-start border-0 bg-transparent shadow-none">
                    <i class="bi bi-box-arrow-left"></i> Çıkış
                </button>
            </form>
        </nav>
    </aside>

    {{-- Ana İçerik --}}
    <main class="main-content">
        {{-- Session Bildirimlerini SweetAlert Toast Olarak Gösterme --}}
        @if(session('success'))
            <script>
                Swal.fire({
                    icon: 'success',
                    title: 'Başarılı!',
                    text: "{{ session('success') }}",
                    timer: 3000,
                    showConfirmButton: false,
                    toast: true,
                    position: 'top-end'
                });
            </script>
        @endif

        @yield('content')
    </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // Karanlık Mod Mantığı
    const darkModeToggle = document.getElementById('darkModeToggle');
    const darkModeIcon = document.getElementById('darkModeIcon');
    const htmlElement = document.documentElement;

    function updateAdminIcon(theme) {
        if (theme === 'dark') {
            darkModeIcon.classList.replace('bi-moon-stars-fill', 'bi-sun-fill');
            darkModeIcon.style.color = '#ffc107';
        } else {
            darkModeIcon.classList.replace('bi-sun-fill', 'bi-moon-stars-fill');
            darkModeIcon.style.color = '#94a3b8';
        }
    }

    // İlk yüklemede ikonu ayarla
    updateAdminIcon(htmlElement.getAttribute('data-bs-theme'));

    darkModeToggle.addEventListener('click', function (e) {
        e.preventDefault();
        const currentTheme = htmlElement.getAttribute('data-bs-theme');
        const newTheme = currentTheme === 'dark' ? 'light' : 'dark';

        htmlElement.setAttribute('data-bs-theme', newTheme);
        localStorage.setItem('theme', newTheme);
        updateAdminIcon(newTheme);
    });

    // Global Silme Onayı Scripti
    document.addEventListener('click', function (e) {
        const button = e.target.closest('.delete-btn');
        if (button) {
            e.preventDefault();
            const form = button.closest('form');

            Swal.fire({
                title: 'Emin misiniz?',
                text: "Bu ilanı sildiğinizde geri alamazsınız!",
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
        }
    });
</script>

@stack('scripts')
</body>
</html>