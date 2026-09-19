@extends('layouts.admin')

@section('title', 'Rapprochement bancaire')

@section('content')
<form method="GET" class="uk-flex" style="gap:8px;">
    <select name="compte" class="uk-select" style="max-width:28rem;" onchange="this.form.submit()">
        <option value="">Choisir un compte bancaire ou mobile money...</option>
        @foreach($accounts as $acc)
            <option value="{{ $acc->id }}" @selected($account?->id === $acc->id)>{{ $acc->name }}</option>
        @endforeach
    </select>
</form>

@if($account)
    <div class="uk-grid-small uk-child-width-1-2 uk-child-width-1-4@l uk-margin-top" uk-grid>
        <div>
            <div class="uk-card uk-card-default" style="padding:20px;">
                <p class="uk-text-small uk-text-muted">Solde comptable</p>
                <p style="margin-top:4px; font-size:1.25rem; font-weight:700; color:#1D8A4E;">{{ number_format($stats['solde_comptable'], 0, ',', ' ') }} FCFA</p>
            </div>
        </div>
        <div>
            <div class="uk-card uk-card-default" style="padding:20px;">
                <p class="uk-text-small uk-text-muted">Total relevé importé</p>
                <p style="margin-top:4px; font-size:1.25rem; font-weight:700;">{{ number_format($stats['total_releve'], 0, ',', ' ') }} FCFA</p>
            </div>
        </div>
        <div>
            <div class="uk-card uk-card-default" style="padding:20px;">
                <p class="uk-text-small uk-text-muted">Lignes rapprochées</p>
                <p style="margin-top:4px; font-size:1.25rem; font-weight:700; color:#1D8A4E;">{{ $reconciledCount }}</p>
            </div>
        </div>
        <div>
            <div class="uk-card uk-card-default" style="padding:20px;">
                <p class="uk-text-small uk-text-muted">En attente</p>
                <p style="margin-top:4px; font-size:1.25rem; font-weight:700; {{ $stats['lignes_en_attente'] > 0 ? 'color:#E8604F;' : '' }}">{{ $stats['lignes_en_attente'] }}</p>
            </div>
        </div>
    </div>

    <div class="uk-card uk-card-default uk-margin-top" style="padding:24px;">
        <h3 style="font-family:'Fraunces',serif; font-weight:600; font-size:1rem;">Importer un relevé bancaire</h3>
        <p class="uk-text-small uk-text-muted" style="margin-top:4px;">Fichier CSV : date;description;montant (montant positif = crédit, négatif = débit). Séparateur point-virgule ou virgule.</p>
        <form action="{{ route('admin.comptabilite.rapprochement.import', $account) }}" method="POST" enctype="multipart/form-data" class="uk-flex uk-flex-wrap uk-margin-top" style="gap:12px;">
            @csrf
            <input type="file" name="file" accept=".csv,.txt" required class="uk-input" style="flex:1; min-width:240px;">
            <button type="submit" class="uk-button uk-button-primary">Importer et rapprocher</button>
        </form>
    </div>

    <div class="uk-grid-small uk-child-width-1-2@l uk-margin-top" uk-grid>
        <div>
            <div class="uk-card uk-card-default" style="padding:24px;">
                <h3 style="font-family:'Fraunces',serif; font-weight:600; font-size:1rem;">Lignes du relevé non rapprochées</h3>
                <div class="uk-margin-small-top" style="display:flex; flex-direction:column; gap:12px;">
                    @forelse($unmatchedLines as $line)
                        <div class="uk-text-small" style="border-radius:8px; background:#F7F8F5; padding:16px;">
                            <div class="uk-flex uk-flex-between uk-flex-middle">
                                <span style="font-weight:600;">{{ $line->statement_date->format('d/m/Y') }} — {{ $line->description }}</span>
                                <span style="font-weight:600; {{ $line->amount >= 0 ? 'color:#1D8A4E;' : 'color:#E8604F;' }}">{{ number_format($line->amount, 0, ',', ' ') }} FCFA</span>
                            </div>
                            @if($line->status === 'ecart')
                                <span class="uk-label uk-margin-small-top" style="background:rgba(232,96,79,.1); color:#E8604F;">Écart signalé</span>
                            @endif

                            <div class="uk-flex uk-flex-wrap uk-margin-small-top" style="gap:8px;">
                                <form action="{{ route('admin.comptabilite.rapprochement.match', $line) }}" method="POST" class="uk-flex" style="flex:1; gap:8px;">
                                    @csrf
                                    <select name="transaction_id" class="uk-select" style="flex:1;">
                                        <option value="">Associer à un mouvement système...</option>
                                        @foreach($unreconciledTransactions as $t)
                                            <option value="{{ $t->id }}">{{ $t->transaction_date->format('d/m/Y') }} — {{ $t->description }} ({{ number_format($t->signedAmount(), 0, ',', ' ') }})</option>
                                        @endforeach
                                    </select>
                                    <button type="submit" class="uk-text-small" style="font-weight:600; color:#1D8A4E; background:none; border:none; cursor:pointer;">Lier</button>
                                </form>
                            </div>
                            <div class="uk-flex uk-margin-small-top" style="gap:12px;">
                                <form action="{{ route('admin.comptabilite.rapprochement.create-transaction', $line) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="uk-text-small" style="font-weight:600; color:#1D8A4E; background:none; border:none; cursor:pointer;">Créer le mouvement correspondant</button>
                                </form>
                                @if($line->status !== 'ecart')
                                    <form action="{{ route('admin.comptabilite.rapprochement.discrepancy', $line) }}" method="POST">
                                        @csrf @method('PATCH')
                                        <button type="submit" class="uk-text-small" style="font-weight:600; color:#E8604F; background:none; border:none; cursor:pointer;">Marquer comme écart</button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    @empty
                        <p class="uk-text-small uk-text-muted">Aucune ligne en attente — tout est rapproché.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <div>
            <div class="uk-card uk-card-default" style="padding:24px;">
                <h3 style="font-family:'Fraunces',serif; font-weight:600; font-size:1rem;">Mouvements système non rapprochés</h3>
                <div class="uk-margin-small-top" style="display:flex; flex-direction:column; gap:8px;">
                    @forelse($unreconciledTransactions as $t)
                        <div class="uk-flex uk-flex-between uk-flex-middle uk-text-small" style="border-radius:8px; background:#F7F8F5; padding:10px 16px;">
                            <span>{{ $t->transaction_date->format('d/m/Y') }} — {{ $t->description }}</span>
                            <span style="font-weight:600; {{ $t->type === 'entree' ? 'color:#1D8A4E;' : 'color:#E8604F;' }}">{{ number_format($t->signedAmount(), 0, ',', ' ') }} FCFA</span>
                        </div>
                    @empty
                        <p class="uk-text-small uk-text-muted">Aucun mouvement en attente de rapprochement.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@else
    <p class="uk-margin-top uk-text-muted">Sélectionnez un compte bancaire ou mobile money pour démarrer le rapprochement.</p>
@endif
@endsection
