@extends('layouts.admin')

@section('title', 'Rapprochement bancaire')

@section('content')
<form method="GET" class="flex gap-2">
    <select name="compte" class="input max-w-md" onchange="this.form.submit()">
        <option value="">Choisir un compte bancaire ou mobile money...</option>
        @foreach($accounts as $acc)
            <option value="{{ $acc->id }}" @selected($account?->id === $acc->id)>{{ $acc->name }}</option>
        @endforeach
    </select>
</form>

@if($account)
    <div class="mt-6 grid grid-cols-2 gap-4 lg:grid-cols-4">
        <div class="admin-card">
            <p class="text-sm text-terroir-dark/50">Solde comptable</p>
            <p class="mt-1 text-xl font-bold text-terroir-green">{{ number_format($stats['solde_comptable'], 0, ',', ' ') }} FCFA</p>
        </div>
        <div class="admin-card">
            <p class="text-sm text-terroir-dark/50">Total relevé importé</p>
            <p class="mt-1 text-xl font-bold">{{ number_format($stats['total_releve'], 0, ',', ' ') }} FCFA</p>
        </div>
        <div class="admin-card">
            <p class="text-sm text-terroir-dark/50">Lignes rapprochées</p>
            <p class="mt-1 text-xl font-bold text-terroir-green">{{ $reconciledCount }}</p>
        </div>
        <div class="admin-card">
            <p class="text-sm text-terroir-dark/50">En attente</p>
            <p class="mt-1 text-xl font-bold {{ $stats['lignes_en_attente'] > 0 ? 'text-terroir-terracotta' : '' }}">{{ $stats['lignes_en_attente'] }}</p>
        </div>
    </div>

    <div class="admin-card mt-6">
        <h3 class="font-display text-base font-semibold">Importer un relevé bancaire</h3>
        <p class="mt-1 text-sm text-terroir-dark/50">Fichier CSV : date;description;montant (montant positif = crédit, négatif = débit). Séparateur point-virgule ou virgule.</p>
        <form action="{{ route('admin.comptabilite.rapprochement.import', $account) }}" method="POST" enctype="multipart/form-data" class="mt-3 flex flex-wrap gap-3">
            @csrf
            <input type="file" name="file" accept=".csv,.txt" required class="input min-w-[240px] flex-1">
            <button type="submit" class="btn-primary">Importer et rapprocher</button>
        </form>
    </div>

    <div class="mt-6 grid gap-6 lg:grid-cols-2">
        <div class="admin-card">
            <h3 class="font-display text-base font-semibold">Lignes du relevé non rapprochées</h3>
            <div class="mt-3 flex flex-col gap-3">
                @forelse($unmatchedLines as $line)
                    <div class="rounded-lg bg-terroir-cream p-4 text-sm">
                        <div class="flex items-center justify-between">
                            <span class="font-semibold">{{ $line->statement_date->format('d/m/Y') }} — {{ $line->description }}</span>
                            <span class="font-semibold {{ $line->amount >= 0 ? 'text-terroir-green' : 'text-terroir-terracotta' }}">{{ number_format($line->amount, 0, ',', ' ') }} FCFA</span>
                        </div>
                        @if($line->status === 'ecart')
                            <span class="admin-badge-danger mt-1.5">Écart signalé</span>
                        @endif

                        <div class="mt-3 flex flex-wrap gap-2">
                            <form action="{{ route('admin.comptabilite.rapprochement.match', $line) }}" method="POST" class="flex flex-1 gap-2">
                                @csrf
                                <select name="transaction_id" class="input flex-1">
                                    <option value="">Associer à un mouvement système...</option>
                                    @foreach($unreconciledTransactions as $t)
                                        <option value="{{ $t->id }}">{{ $t->transaction_date->format('d/m/Y') }} — {{ $t->description }} ({{ number_format($t->signedAmount(), 0, ',', ' ') }})</option>
                                    @endforeach
                                </select>
                                <button type="submit" class="text-sm font-semibold text-terroir-green">Lier</button>
                            </form>
                        </div>
                        <div class="mt-3 flex gap-3">
                            <form action="{{ route('admin.comptabilite.rapprochement.create-transaction', $line) }}" method="POST">
                                @csrf
                                <button type="submit" class="text-sm font-semibold text-terroir-green">Créer le mouvement correspondant</button>
                            </form>
                            @if($line->status !== 'ecart')
                                <form action="{{ route('admin.comptabilite.rapprochement.discrepancy', $line) }}" method="POST">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="text-sm font-semibold text-terroir-terracotta">Marquer comme écart</button>
                                </form>
                            @endif
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-terroir-dark/50">Aucune ligne en attente — tout est rapproché.</p>
                @endforelse
            </div>
        </div>

        <div class="admin-card">
            <h3 class="font-display text-base font-semibold">Mouvements système non rapprochés</h3>
            <div class="mt-3 flex flex-col gap-2">
                @forelse($unreconciledTransactions as $t)
                    <div class="flex items-center justify-between rounded-lg bg-terroir-cream px-4 py-2.5 text-sm">
                        <span>{{ $t->transaction_date->format('d/m/Y') }} — {{ $t->description }}</span>
                        <span class="font-semibold {{ $t->type === 'entree' ? 'text-terroir-green' : 'text-terroir-terracotta' }}">{{ number_format($t->signedAmount(), 0, ',', ' ') }} FCFA</span>
                    </div>
                @empty
                    <p class="text-sm text-terroir-dark/50">Aucun mouvement en attente de rapprochement.</p>
                @endforelse
            </div>
        </div>
    </div>
@else
    <p class="mt-6 text-terroir-dark/50">Sélectionnez un compte bancaire ou mobile money pour démarrer le rapprochement.</p>
@endif
@endsection
