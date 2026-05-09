@extends('layouts.admin')

@section('title', 'Kullanıcı Yönetimi')

@section('content')
<h4 class="mb-4">Kullanıcı Yönetimi</h4>
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>Ad Soyad</th>
                    <th>E-posta</th>
                    <th>Şehir</th>
                    <th>İlan Sayısı</th>
                    <th>Kayıt Tarihi</th>
                    <th>İşlem</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                    <tr>
                        <td>{{ $user->id }}</td>
                        <td>{{ $user->name }}
                            @if($user->hasRole('admin'))
                                <span class="badge bg-danger">Admin</span>
                            @endif
                        </td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->city ?? '-' }}</td>
                        <td>{{ $user->listings_count }}</td>
                        <td>{{ $user->created_at->format('d.m.Y') }}</td>
                        <td>
                            @if(!$user->hasRole('admin'))
                                <form method="POST" action="{{ route('admin.users.destroy', $user) }}" class="delete-form">
                                    @csrf @method('DELETE')
                                    <button type="button" class="btn btn-danger btn-sm delete-btn">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">{{ $users->links() }}</div>

{{-- SweetAlert2 Script --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Tüm silme butonlarını seç
    document.querySelectorAll('.delete-btn').forEach(button => {
        button.addEventListener('click', function(e) {
            const form = this.closest('.delete-form');
            
            Swal.fire({
                title: 'Emin misiniz?',
                text: "Bu kullanıcıyı sildiğinizde ona ait tüm veriler de silinebilir!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Evet, sil!',
                cancelButtonText: 'İptal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit(); // Sadece onay verilirse formu gönder
                }
            });
        });
    });
});
</script>
@endsection