<?php

namespace App\Http\Controllers;

use App\Http\Requests\PublicationRequest;
use App\Models\Publication;
use App\Models\TypeEtablissement;
use App\Models\User;
use App\Services\PublicationPublisher;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PublicationController extends Controller
{
    public function index(Request $request): View
    {
        $typesEtablissement = TypeEtablissement::orderBy('nom')->get();
        $typeId = $request->integer('type_id') ?: null;
        abort_if($typeId && !$typesEtablissement->contains('id', $typeId), 404);
        $category = $request->query('categorie');
        abort_if($category && !in_array($category, ['Vulgarisation', 'Rappel', 'News'], true), 404);
        $selectedLocations = collect();
        if ($request->filled('ville')) {
            $selectedLocations->push([
                'ville' => trim((string) $request->query('ville')),
                'commune' => trim((string) $request->query('commune', '')),
            ]);
        } elseif ($request->user()) {
            $selectedLocations = $request->user()->usersEtablissements()
                ->with('etablissement:id,ville,commune')
                ->get()
                ->pluck('etablissement')
                ->filter(fn ($etablissement) => $etablissement?->ville)
                ->map(fn ($etablissement) => [
                    'ville' => $etablissement->ville,
                    'commune' => $etablissement->commune ?? '',
                ]);
        }
        $typeIds = $typeId
            ? collect([$typeId])
            : ($request->user()
                ? $request->user()->usersEtablissements()
                    ->with('etablissement:id,type_etablissement_id')
                    ->get()
                    ->pluck('etablissement.type_etablissement_id')
                    ->filter()
                    ->unique()
                : collect());

        $publications = Publication::enLigne()
            ->with(['organisationPatronale', 'auteur', 'typesEtablissement'])
            ->where(function ($query) use ($typeIds) {
                $query->where('cible', 'tous');
                if ($typeIds->isNotEmpty()) {
                    $query->orWhereHas('typesEtablissement', fn ($types) => $types->whereIn('type_etablissement_id', $typeIds));
                }
            })
            ->when($category, fn ($query) => $query->where('categorie', $category))
            ->where(function ($query) use ($selectedLocations) {
                $query->whereNull('ville')->whereNull('commune');
                foreach ($selectedLocations as $location) {
                    $query->orWhere(function ($match) use ($location) {
                        $match->whereRaw('LOWER(ville) = ?', [mb_strtolower($location['ville'])]);
                        if ($location['commune'] !== '') {
                            $match->where(function ($commune) use ($location) {
                                $commune->whereNull('commune')
                                    ->orWhereRaw('LOWER(commune) = ?', [mb_strtolower($location['commune'])]);
                            });
                        }
                    });
                }
            })
            ->latest('publie_le')
            ->paginate(12)
            ->withQueryString();

        return view('publications.index', compact('publications', 'typesEtablissement', 'typeId', 'category'));
    }

    public function adminIndex(): View
    {
        $this->authorizePublisher(request()->user());
        $publications = Publication::with(['organisationPatronale', 'auteur'])
            ->latest('created_at')
            ->paginate(20);

        return view('publications.admin-index', compact('publications'));
    }

    public function create(Request $request): View
    {
        $this->authorizePublisher($request->user());

        return view('publications.create', [
            'typesEtablissement' => TypeEtablissement::orderBy('nom')->get(),
        ]);
    }

    public function store(PublicationRequest $request, PublicationPublisher $publisher): RedirectResponse
    {
        $this->authorizePublisher($request->user());
        $publisher->publish($request->user(), $request->validated());

        return redirect()->route('publications.manage')->with('success', 'Publication enregistrée.');
    }

    public function destroy(Request $request, Publication $publication): RedirectResponse
    {
        $this->authorizePublisher($request->user());
        abort_unless(
            $request->user()->role === 'admin'
                || $publication->auteur_id === $request->user()->id
            || ($publication->organisation_patronale_id !== null
                && $request->user()->organisationPatronale?->id === $publication->organisation_patronale_id),
            404
        );
        $publication->delete();

        return back()->with('success', 'Publication supprimée.');
    }

    private function authorizePublisher(User $user): void
    {
        abort_unless(
            $user->role === 'admin' || $user->role === 'organisation_patronale' || $user->can_publish,
            403
        );
    }
}
