@extends('layouts.public')

@section('title', 'Messagerie privée')

@section('content')
<div class="container py-4">
    <h1 class="h2 mb-4">Messagerie privée</h1>

    <div class="row g-3">
        <aside class="col-lg-4">
            <section class="card shadow-sm mb-3">
                <div class="card-body">
                    <h2 class="h5">Trouver un compte</h2>
                    <form method="GET" action="{{ route('messages.index') }}" class="d-flex gap-2">
                        <label class="visually-hidden" for="contact-search">Nom du compte</label>
                        <input class="form-control" id="contact-search" name="q" value="{{ $search }}"
                            maxlength="255" placeholder="Rechercher par nom">
                        <button class="btn btn-primary" type="submit">Rechercher</button>
                    </form>

                    @if ($search !== '')
                        <ul class="list-group list-group-flush mt-3">
                            @forelse ($contacts as $contact)
                                <li class="list-group-item px-0">
                                    <a href="{{ route('messages.index', ['with' => $contact->id, 'q' => $search]) }}">
                                        {{ $contact->name }}
                                    </a>
                                    <span class="text-muted small d-block">{{ str_replace('_', ' ', $contact->role) }}</span>
                                </li>
                            @empty
                                <li class="list-group-item px-0 text-muted">Aucun compte approuvé trouvé.</li>
                            @endforelse
                        </ul>
                    @endif
                </div>
            </section>

            <section class="card shadow-sm">
                <div class="card-body">
                    <h2 class="h5">Conversations</h2>
                    <ul class="list-group list-group-flush">
                        @forelse ($conversations as $contact)
                            <li class="list-group-item px-0">
                                <a class="{{ $activeContact?->id === $contact->id ? 'fw-bold' : '' }}"
                                    href="{{ route('messages.index', ['with' => $contact->id]) }}">
                                    {{ $contact->name }}
                                </a>
                            </li>
                        @empty
                            <li class="list-group-item px-0 text-muted">Vos conversations apparaîtront ici.</li>
                        @endforelse
                    </ul>
                </div>
            </section>
        </aside>

        <section class="col-lg-8" aria-label="Conversation">
            @if ($activeContact)
                <div class="card shadow-sm">
                    <div class="card-header bg-white">
                        <h2 class="h5 mb-0">{{ $activeContact->name }}</h2>
                    </div>
                    <div class="card-body d-flex flex-column gap-3" style="min-height: 20rem; max-height: 55vh; overflow-y: auto;">
                        @forelse ($messages as $message)
                            <article class="align-self-{{ $message->sender_id === auth()->id() ? 'end' : 'start' }} rounded px-3 py-2"
                                style="max-width: 85%; background: {{ $message->sender_id === auth()->id() ? '#e5f2eb' : '#f1f3f5' }};">
                                <div class="small text-muted mb-1">
                                    {{ $message->sender->name }} · {{ $message->created_at->format('d/m/Y H:i') }}
                                </div>
                                <div>{!! nl2br(e($message->body)) !!}</div>
                            </article>
                        @empty
                            <p class="text-muted my-auto text-center">Aucun message pour le moment. Envoyez le premier message.</p>
                        @endforelse
                        {{ $messages->links() }}
                    </div>
                    <div class="card-footer bg-white">
                        <form method="POST" action="{{ route('messages.store', $activeContact) }}">
                            @csrf
                            <label class="form-label" for="message-body">Votre message</label>
                            <textarea class="form-control" id="message-body" name="body" rows="3" maxlength="5000"
                                required>{{ old('body') }}</textarea>
                            @error('body')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                            <div class="d-flex justify-content-between align-items-center mt-2">
                                <small class="text-muted">Maximum 5 000 caractères.</small>
                                <button class="btn btn-primary" type="submit">Envoyer</button>
                            </div>
                        </form>
                    </div>
                </div>
            @else
                <div class="card shadow-sm h-100">
                    <div class="card-body d-flex flex-column justify-content-center align-items-center text-center">
                        <i class="fa-regular fa-comments fa-3x text-muted mb-3" aria-hidden="true"></i>
                        <h2 class="h5">Échangez avec les acteurs de BISIKA</h2>
                        <p class="text-muted mb-0">Recherchez un compte approuvé ou choisissez une conversation.</p>
                    </div>
                </div>
            @endif
        </section>
    </div>
</div>
@endsection
