<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\PublicationRequest;
use App\Models\Message;
use App\Models\Publication;
use App\Models\Publicite;
use App\Models\User;
use App\Services\PublicationPublisher;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SocialController extends Controller
{
    public function advertisements(Request $request): JsonResponse
    {
        $filters = $request->validate([
            'ville' => ['nullable', 'string', 'max:255'],
            'commune' => ['nullable', 'string', 'max:255'],
        ]);
        $page = Publicite::query()
            ->with('etablissement:id,nom')
            ->actives()
            ->visiblesDans($filters['ville'] ?? null, $filters['commune'] ?? null)
            ->latest('id')
            ->paginate(20);

        return $this->paginated($page, fn (Publicite $advertisement) => [
            'id' => $advertisement->id,
            'title' => $advertisement->titre,
            'description' => $advertisement->description,
            'image_url' => asset('storage/'.$advertisement->image_path),
            'starts_at' => $advertisement->date,
            'duration_days' => $advertisement->dure,
            'targeting' => $advertisement->ciblage,
            'city' => $advertisement->ville,
            'commune' => $advertisement->commune,
            'establishment' => $advertisement->etablissement ? [
                'id' => $advertisement->etablissement->id,
                'name' => $advertisement->etablissement->nom,
            ] : null,
        ]);
    }

    public function feed(Request $request): JsonResponse
    {
        $user = $request->user();
        $establishments = $user->etablissements()->get(['etablissements.id', 'type_etablissement_id', 'ville', 'commune']);

        $page = Publication::query()
            ->with(['auteur:id,name,role', 'typesEtablissement:id,nom'])
            ->enLigne()
            ->where(function ($visible) use ($establishments) {
                if ($establishments->isEmpty()) {
                    $visible->where('cible', 'tous')->whereNull('ville')->whereNull('commune');

                    return;
                }

                foreach ($establishments as $establishment) {
                    $visible->orWhere(function ($scope) use ($establishment) {
                        $scope->where(function ($audience) use ($establishment) {
                            $audience->where('cible', 'tous');
                            if ($establishment->type_etablissement_id) {
                                $audience->orWhere(function ($domain) use ($establishment) {
                                    $domain->where('cible', 'domaines')
                                        ->whereHas('typesEtablissement', fn ($types) => $types->where(
                                            'type_etablissements.id',
                                            $establishment->type_etablissement_id
                                        ));
                                });
                            }
                        })->where(function ($location) use ($establishment) {
                            $location->where(function ($city) use ($establishment) {
                                $city->whereNull('ville');
                                if ($establishment->ville) {
                                    $city->orWhereRaw('LOWER(ville) = ?', [mb_strtolower($establishment->ville)]);
                                }
                            })->where(function ($commune) use ($establishment) {
                                $commune->whereNull('commune');
                                if ($establishment->commune) {
                                    $commune->orWhereRaw('LOWER(commune) = ?', [mb_strtolower($establishment->commune)]);
                                }
                            });
                        });
                    });
                }
            })
            ->latest('publie_le')
            ->latest('id')
            ->paginate(20);

        return $this->paginated($page, fn (Publication $publication) => [
            'id' => $publication->id,
            'title' => $publication->titre,
            'synopsis' => $publication->synopsis,
            'content' => $publication->contenu,
            'category' => $publication->categorie,
            'target' => $publication->cible,
            'city' => $publication->ville,
            'commune' => $publication->commune,
            'published_at' => $publication->publie_le,
            'expires_at' => $publication->expire_le,
            'author' => $publication->auteur ? [
                'id' => $publication->auteur->id,
                'name' => $publication->auteur->name,
                'role' => $publication->auteur->role,
            ] : null,
            'domains' => $publication->typesEtablissement->map(fn ($type) => [
                'id' => $type->id,
                'name' => $type->nom,
            ]),
        ]);
    }

    public function publish(PublicationRequest $request, PublicationPublisher $publisher): JsonResponse
    {
        $author = $request->user();
        abort_unless(
            $author->is_approved && (
                $author->role === 'admin'
                || $author->role === 'organisation_patronale'
                || $author->can_publish
            ),
            403,
            'Ce compte n’est pas autorisé à publier.'
        );

        $organisationId = $author->role === 'organisation_patronale'
            ? $author->organisationPatronale?->id
            : null;
        abort_if($author->role === 'organisation_patronale' && ! $organisationId, 403);

        $publication = $publisher->publish($author, $request->validated(), $organisationId)
            ->load(['auteur:id,name,role', 'typesEtablissement:id,nom']);

        return response()->json(['data' => $this->publicationData($publication)], 201);
    }

    public function contacts(Request $request): JsonResponse
    {
        $filters = $request->validate([
            'q' => ['required', 'string', 'min:2', 'max:100'],
        ]);
        $users = User::query()
            ->where('is_approved', true)
            ->whereKeyNot($request->user()->id)
            ->where('name', 'like', '%'.$filters['q'].'%')
            ->orderBy('name')
            ->paginate(20);

        return $this->paginated($users, fn (User $user) => $this->contactData($user));
    }

    public function conversations(Request $request): JsonResponse
    {
        $user = $request->user();
        $latestMessageIds = Message::query()
            ->where(fn ($query) => $query->where('sender_id', $user->id)->orWhere('receiver_id', $user->id))
            ->selectRaw('MAX(id)')
            ->groupByRaw('CASE WHEN sender_id = ? THEN receiver_id ELSE sender_id END', [$user->id]);
        $page = Message::query()
            ->whereIn('id', $latestMessageIds)
            ->latest('id')
            ->paginate(20);

        $otherIds = $page->getCollection()->map(fn (Message $message) => $message->sender_id === $user->id
            ? $message->receiver_id
            : $message->sender_id);
        $contacts = User::query()->whereIn('id', $otherIds)->where('is_approved', true)->get()->keyBy('id');
        $unreadCounts = Message::query()
            ->selectRaw('sender_id, COUNT(*) as unread_count')
            ->whereIn('sender_id', $otherIds)
            ->where('receiver_id', $user->id)
            ->whereNull('read_at')
            ->groupBy('sender_id')
            ->pluck('unread_count', 'sender_id');

        return $this->paginated($page, function (Message $message) use ($user, $contacts, $unreadCounts) {
            $contactId = $message->sender_id === $user->id ? $message->receiver_id : $message->sender_id;
            $contact = $contacts->get($contactId);
            if (! $contact) {
                return null;
            }

            return [
                'user' => $this->contactData($contact),
                'last_message' => $message->body,
                'last_message_at' => $message->created_at,
                'unread_count' => (int) ($unreadCounts[$contactId] ?? 0),
            ];
        });
    }

    public function messages(Request $request, User $user): JsonResponse
    {
        abort_if($user->id === $request->user()->id || ! $user->is_approved, 404);
        $currentUser = $request->user();
        $page = Message::query()
            ->where(function ($query) use ($currentUser, $user) {
                $query->where(fn ($pair) => $pair->where('sender_id', $currentUser->id)->where('receiver_id', $user->id))
                    ->orWhere(fn ($pair) => $pair->where('sender_id', $user->id)->where('receiver_id', $currentUser->id));
            })
            ->with('sender:id,name')
            ->oldest('id')
            ->paginate(50);
        $unreadIds = $page->getCollection()
            ->where('receiver_id', $currentUser->id)
            ->whereNull('read_at')
            ->pluck('id');
        if ($unreadIds->isNotEmpty()) {
            Message::whereKey($unreadIds)->update(['read_at' => now()]);
        }

        return $this->paginated($page, fn (Message $message) => [
            'id' => $message->id,
            'sender_id' => $message->sender_id,
            'receiver_id' => $message->receiver_id,
            'body' => $message->body,
            'read_at' => $message->read_at,
            'created_at' => $message->created_at,
        ]);
    }

    public function sendMessage(Request $request, User $user): JsonResponse
    {
        abort_if($user->id === $request->user()->id || ! $user->is_approved, 404);
        $data = $request->validate([
            'body' => ['required', 'string', 'max:5000'],
        ]);

        $message = $request->user()->messagesSent()->create([
            'receiver_id' => $user->id,
            'body' => $data['body'],
        ]);

        return response()->json([
            'data' => [
                'id' => $message->id,
                'sender_id' => $message->sender_id,
                'receiver_id' => $message->receiver_id,
                'body' => $message->body,
                'read_at' => null,
                'created_at' => $message->created_at,
            ],
        ], 201);
    }

    public function notifications(Request $request): JsonResponse
    {
        $page = $request->user()->notifications()->latest()->paginate(20);

        return $this->paginated($page, fn ($notification) => [
            'id' => $notification->id,
            'type' => $notification->type,
            'data' => $notification->data,
            'read_at' => $notification->read_at,
            'created_at' => $notification->created_at,
        ]);
    }

    public function markNotificationRead(Request $request, string $notification): JsonResponse
    {
        $item = $request->user()->notifications()->whereKey($notification)->firstOrFail();
        $item->markAsRead();

        return response()->json(['data' => ['id' => $item->id, 'read_at' => $item->fresh()->read_at]]);
    }

    public function markAllNotificationsRead(Request $request): JsonResponse
    {
        $request->user()->unreadNotifications()->update(['read_at' => now()]);

        return response()->json(['message' => 'Toutes les notifications ont été marquées comme lues.']);
    }

    private function publicationData(Publication $publication): array
    {
        return [
            'id' => $publication->id,
            'title' => $publication->titre,
            'synopsis' => $publication->synopsis,
            'content' => $publication->contenu,
            'category' => $publication->categorie,
            'target' => $publication->cible,
            'city' => $publication->ville,
            'commune' => $publication->commune,
            'published_at' => $publication->publie_le,
            'expires_at' => $publication->expire_le,
            'author' => $publication->auteur ? $this->contactData($publication->auteur) : null,
            'domains' => $publication->typesEtablissement->map(fn ($type) => [
                'id' => $type->id,
                'name' => $type->nom,
            ]),
        ];
    }

    private function contactData(User $user): array
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'role' => $user->role,
            'identifiant' => $user->identifiant,
        ];
    }

    private function paginated($page, callable $transform): JsonResponse
    {
        return response()->json([
            'data' => $page->getCollection()->map($transform)->values(),
            'meta' => [
                'current_page' => $page->currentPage(),
                'per_page' => $page->perPage(),
                'total' => $page->total(),
                'last_page' => $page->lastPage(),
            ],
        ]);
    }
}
