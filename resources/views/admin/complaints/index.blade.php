@extends('layouts.admin')

@section('title', 'Şikayet Yönetimi')

@section('content')
<h4 class="mb-4">Şikayetler</h4>
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>İlan</th>
                    <th>Şikayet Eden</th>
                    <th>Sebep</th>
                    <th>Durum</th>
                    <th>Tarih</th>
                    <th>İşlem</th>
                </tr>
            </thead>
            <tbody>
                @foreach($complaints as $complaint)
                    <tr>
                        <td>{{ $complaint->id }}</td>
                        <td>
                            <a href="{{ route('listings.show', $complaint->listing) }}" target="_blank">
                                {{ Str::limit($complaint->listing->title, 30) }}
                            </a>
                        </td>
                        <td>{{ $complaint->user->name }}</td>
                        <td>{{ Str::limit($complaint->reason, 50) }}</td>
                        <td>
                            @if($complaint->status === 'pending')
                                <span class="badge bg-warning text-dark">Bekliyor</span>
                            @elseif($complaint->status === 'resolved')
                                <span class="badge bg-success">Çözüldü</span>
                            @endif
                        </td>
                        <td>{{ $complaint->created_at->format('d.m.Y') }}</td>
                        <td>
                            @if($complaint->status === 'pending')
                                <form method="POST"
                                      action="{{ route('admin.complaints.resolve', $complaint) }}">
                                    @csrf @method('PUT')
                                    <button class="btn btn-success btn-sm">Çözüldü İşaretle</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">{{ $complaints->links() }}</div>
@endsection
