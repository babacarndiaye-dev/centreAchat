@extends('layouts.admin')

@section('title', 'Nouvelle écriture')

@section('content')
<div class="admin-card max-w-4xl" x-data="{
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

        <div class="grid gap-4 sm:grid-cols-3">
            <div>
                <label class="label" for="journal_id">Journal</label>
                <select id="journal_id" name="journal_id" required class="input">
                    @foreach($journals as $journal)
                        <option value="{{ $journal->id }}">{{ $journal->code }} — {{ $journal->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="label" for="entry_date">Date</label>
                <input type="date" id="entry_date" name="entry_date" value="{{ now()->format('Y-m-d') }}" required class="input">
            </div>
            <div>
                <label class="label" for="reference">Référence (optionnel)</label>
                <input type="text" id="reference" name="reference" class="input">
            </div>
        </div>

        <div class="mt-4">
            <label class="label" for="description">Libellé de l'écriture</label>
            <input type="text" id="description" name="description" required class="input">
        </div>

        <div class="mt-6 border-t border-terroir-dark/10 pt-6">
            <h3 class="font-display text-base font-semibold">Lignes d'écriture</h3>

            <div class="mt-3 flex flex-col gap-3">
                <template x-for="(line, i) in lines" :key="i">
                    <div class="flex flex-wrap items-center gap-2">
                        <select :name="'chart_account_id[' + i + ']'" x-model="line.chart_account_id" required class="input min-w-[200px] flex-1">
                            <option value="">Compte...</option>
                            <template x-for="a in accounts" :key="a.id">
                                <option :value="a.id" x-text="a.label"></option>
                            </template>
                        </select>
                        <input type="text" :name="'label[' + i + ']'" x-model="line.label" placeholder="Libellé ligne" class="input w-40">
                        <input type="number" step="0.01" :name="'debit[' + i + ']'" x-model.number="line.debit" placeholder="Débit" class="input w-28">
                        <input type="number" step="0.01" :name="'credit[' + i + ']'" x-model.number="line.credit" placeholder="Crédit" class="input w-28">
                        <button type="button" @click="removeLine(i)" class="font-semibold text-terroir-terracotta" aria-label="Retirer"><span class="material-symbols-outlined text-lg">close</span></button>
                    </div>
                </template>
            </div>

            <button type="button" @click="addLine()" class="btn-outline mt-4">+ Ajouter une ligne</button>

            <div class="mt-4 flex justify-end gap-8 text-sm">
                <span>Débit : <strong x-text="totalDebit().toLocaleString('fr-FR')"></strong></span>
                <span>Crédit : <strong x-text="totalCredit().toLocaleString('fr-FR')"></strong></span>
                <span :class="balanced() ? 'flex items-center gap-1 text-terroir-green font-semibold' : 'flex items-center gap-1 text-terroir-terracotta font-semibold'">
                    <span class="material-symbols-outlined is-filled text-base" x-show="balanced()">check_circle</span>
                    <span x-text="balanced() ? 'Équilibrée' : 'Non équilibrée'"></span>
                </span>
            </div>
        </div>

        <div class="mt-6 flex gap-3">
            <button type="submit" class="btn-primary">Enregistrer l'écriture</button>
            <a href="{{ route('admin.comptabilite.ecritures.index') }}" class="btn-outline">Annuler</a>
        </div>
    </form>
</div>
@endsection
