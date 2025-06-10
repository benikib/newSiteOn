 @if ($etablissement->services->count() > 0)
                                                            <div class="table-responsive">
                                                                <table class="table table-striped">
                                                                    <thead>
                                                                        <tr>
                                                                            <th>Nom</th>
                                                                            <th>Description</th>
                                                                            <th>Prix</th>
                                                                            <th>Actions</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        @foreach ($etablissement->services as $service)
                                                                            <tr>
                                                                                <td>{{ $service->nom }}</td>
                                                                                <td>{{ Str::limit($service->description, 50) }}
                                                                                </td>
                                                                                <td>{{ number_format($service->prix, 0, ',', ' ') }}
                                                                                    FCFA</td>
                                                                                <td>
                                                                                    <button class="btn btn-sm btn-info"
                                                                                        data-bs-toggle="modal"
                                                                                        data-bs-target="#editServiceModal{{ $service->id }}">
                                                                                        <i class="fas fa-edit"></i>
                                                                                    </button>
                                                                                    <form
                                                                                        action="{{ route('services.destroy', $service->id) }}"
                                                                                        method="POST" class="d-inline">
                                                                                        @csrf
                                                                                        @method('DELETE')
                                                                                        <button type="submit"
                                                                                            class="btn btn-sm btn-danger"
                                                                                            onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce service?')">
                                                                                            <i class="fas fa-trash"></i>
                                                                                        </button>
                                                                                    </form>
                                                                                </td>
                                                                            </tr>
                                                                        @endforeach
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        @else
                                                            <div class="alert alert-info">
                                                                Aucun service disponible pour cet établissement.
                                                            </div>
                                                        @endif