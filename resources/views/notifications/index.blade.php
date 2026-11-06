@extends('layouts.public')

@section('title', 'Notifications BISIKA')

@section('content')
<div class="container py-4">
    <h1 class="h2 mb-3">Notifications</h1>
    <div class="list-group shadow-sm">
        @forelse ($notifications as $notification)
            <a class="list-group-item list-group-item-action" href="{{ $notification->data['url'] ?? route('publications.index') }}">
                <strong>{{ $notification->data['titre'] ?? 'Information BISIKA' }}</strong>
                <div>{{ $notification->data['message'] ?? 'Une nouvelle information est disponible.' }}</div>
                <small class="text-muted">{{ $notification->created_at->format('d/m/Y H:i') }}</small>
            </a>
        @empty
            <div class="list-group-item text-muted">Aucune notification.</div>
        @endforelse
    </div>
    <div class="mt-3">{{ $notifications->links() }}</div>
</div>
@endsection
