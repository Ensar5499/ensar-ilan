@extends('layouts.admin')

@section('title', 'İlan Yönetimi')

@section('content')
<div class="container-fluid px-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4 mt-2">
                <h4>İlan Yönetimi</h4>
                <span class="badge bg-primary">{{ $listings->total() }} Toplam İlan</span>
            </div>
            
            <div class="card border-0 shadow-sm">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 align-middle">
                            <thead class="table-dark">
                                <tr>
                                    <th class="ps-3">#</th>
                                    <th>İlan</th>
                                    <th>Kullanıcı</th>
                                    <th>Fiyat</th>
                                    <th>Durum</th>
                                    <th>Görüntülenme</th>
                                    <th>Tarih</th>
                                    <th class="pe-3 text-end">İşlem</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($listings as $listing)
                                    <tr>
                                        <td class="ps-3 text-muted">{{ $listing->id }}</td>
                                        <td>
                                            <a href="{{ route('listings.show', $listing) }}" target="_blank" class="text-decoration-none fw-bold text-primary">
                                                {{ Str::limit($listing->title, 40) }}
                                            </a>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <i class="bi bi-person-circle me-2 text-secondary"></i>
                                                {{ $listing->user->name }}
                                            </div>
                                        </td>
                                        <td class="fw-bold text-dark">{{ number_format($listing->price, 2) }} ₺</td>
                                        <td>
                                            <form method="POST"
                                                  action="{{ route('admin.listings.status', $listing) }}"
                                                  class="d-inline">
                                                @csrf @method('PUT')
                                                <select name="status" class="form-select form-select-sm w-auto border-secondary-subtle"
                                                        onchange="this.form.submit()">
                                                    <option value="active" {{ $listing->status==='active'?'selected':'' }}>Aktif</option>
                                                    <option value="passive" {{ $listing->status==='passive'?'selected':'' }}>Pasif</option>
                                                    <option value="sold" {{ $listing->status==='sold'?'selected':'' }}>Satıldı</option>
                                                </select>
                                            </form>
                                        </td>
                                        <td>
                                            <span class="badge rounded-pill bg-light text-dark border">
                                                <i class="bi bi-eye me-1"></i>{{ $listing->view_count }}
                                            </span>
                                        </td>
                                        <td class="text-muted small">{{ $listing->created_at->format('d.m.Y') }}</td>
                                        <td class="pe-3 text-end">
                                            {{-- SweetAlert2 Tetikleyici Form --}}
                                            <form method="POST" action="{{ route('admin.listings.destroy', $listing) }}" class="d-inline">
                                                @csrf @method('DELETE')
                                                <button type="button" class="btn btn-link text-danger p-0 delete-btn" title="Sil">
                                                    <i class="bi bi-trash3-fill fs-5"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="mt-4 d-flex justify-content-center">
                {{ $listings->links() }}
            </div>
        </div>
    </div>
</div>
@endsection