@extends('layouts.admin')

@section('title', 'Nouvelle écriture')

@section('content')
<div class="uk-card uk-card-default" style="max-width:64rem; padding:32px;" x-data="{
    lines: [{ chart_account_id: '', debit: 0, credit: 0, label: '' }, { chart_account_id: '', debit: 0, credit: 0, label: '' }],
    accounts: {{ $accounts->map(fn ($a) => ['id' => $a->id, 'label' => $a->code.' — '.$a->name])->toJson() }},
    addLine() { this.lines.push({ chart_account_id: '', debit: 0, credit: 0, label: '' }) },
    removeLine(i) { this.lines.splice(i, 1) },
    totalDebit() { return this.lines.reduce((s, l) => s + (Number(l.debit) || 0), 0) },
    totalCredit() { return this.lines.reduce((s, l) => s + (Number(l.credit) || 0), 0) },
    balanced() { return Math.abs(this.totalDebit() - this.totalCredit()) < 0.01 && this.totalDebit() > 0 }
}">
    <form action="{{ route('admin.comptabilite.ecritures.store') }}" method="POST">
        @csrf

        <div class="uk-grid-small uk-child-width-1-3@s" uk-grid>
            <div>
                <label class="uk-form-label" for="journal_id">Journal</label>
                <select id="journal_id" name="journal_id" required class="uk-select">
                    @foreach($journals as $journal)
                        <option value="{{ $journal->id }}">{{ $journal->code }} — {{ $journal->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="uk-form-label" for="entry_date">Date</label>
                <input type="date" id="entry_date" name="entry_date" value="{{ now()->format('Y-m-d') }}" required class="uk-input">
            </div>
            <div>
                <label class="uk-form-label" for="reference">Référence (optionnel)</label>
                <input type="text" id="reference" name="reference" class="uk-input">
            </div>
        </div>

        <div class="uk-margin-top">
            <label class="uk-form-label" for="description">Libellé de l'écriture</label>
            <input type="text" id="description" name="description" required class="uk-input">
        </div>

        <div class="uk-margin-top" style="border-top:1px solid rgba(31,35,40,.08); padding-top:24px;">
            <h3 style="font-family:'Fraunces',serif; font-weight:600; font-size:1rem;">Lignes d'écriture</h3>

            <div class="uk-margin-small-top" style="display:flex; flex-direction:column; gap:12px;">
                <template x-for="(line, i) in lines" :key="i">
                    <div class="uk-flex uk-flex-wrap uk-flex-middle" style="gap:8px;">
                        <select :name="'chart_account_id[' + i + ']'" x-model="line.chart_account_id" required class="uk-select" style="flex:1; min-width:200px;">
                            <option value="">Compte...</option>
                            <template x-for="a in accounts" :key="a.id">
                                <option :value="a.id" x-text="a.label"></option>
                            </template>
                        </select>
                        <input type="text" :name="'label[' + i + ']'" x-model="line.label" placeholder="Libellé ligne" class="uk-input" style="width:10rem;">
                        <input type="number" step="0.01" :name="'debit[' + i + ']'" x-model.number="line.debit" placeholder="Débit" class="uk-input" style="width:7rem;">
                        <input type="number" step="0.01" :name="'credit[' + i + ']'" x-model.number="line.credit" placeholder="Crédit" class="uk-input" style="width:7rem;">
                        <button type="button" @click="removeLine(i)" style="color:#E8604F; font-weight:600; background:none; border:none; cursor:pointer;" aria-label="Retirer">✕</button>
                    </div>
                </template>
            </div>

            <button type="button" @click="addLine()" class="uk-button uk-button-default uk-margin-top">+ Ajouter une ligne</button>

            <div class="uk-flex uk-margin-top uk-text-small" style="gap:32px; justify-content:flex-end;">
                <span>Débit : <strong x-text="totalDebit().toLocaleString('fr-FR')"></strong></span>
                <span>Crédit : <strong x-text="totalCredit().toLocaleString('fr-FR')"></strong></span>
                <span :style="balanced() ? 'color:#1D8A4E; font-weight:600;' : 'color:#E8604F; font-weight:600;'" x-text="balanced() ? 'Équilibrée ✓' : 'Non équilibrée'"></span>
            </div>
        </div>

        <div class="uk-flex uk-margin-top" style="gap:12px;">
            <button type="submit" class="uk-button uk-button-primary">Enregistrer l'écriture</button>
            <a href="{{ route('admin.comptabilite.ecritures.index') }}" class="uk-button uk-button-default">Annuler</a>
        </div>
    </form>
</div>
@endsection
