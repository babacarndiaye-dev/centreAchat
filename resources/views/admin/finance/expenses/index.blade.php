@extends('layouts.admin')

@section('title', 'Dépenses')

@section('content')
<div class="uk-flex uk-flex-between uk-flex-middle uk-flex-wrap" style="gap:12px;">
    <form method="GET" class="uk-flex uk-flex-wrap" style="gap:8px;">
        <select name="status" class="uk-select" style="max-width:200px;" onchange="this.form.submit()">
            <option value="">Tous les statuts</option>
            @foreach(\App\Models\Expense::STATUSES as $value => $label)
                <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
            @endforeach
        </select>
        <select name="category" class="uk-select" style="max-width:220px;" onchange="this.form.submit()">
            <option value="">Toutes catégories</option>
            @foreach($categories as $category)
                <option value="{{ $category->id }}" @selected((string) request('category') === (string) $category->id)>{{ $category->name }}</option>
            @endforeach
        </select>
    </form>
    <div class="uk-flex" style="gap:12px;">
        <a href="{{ route('admin.categories-depenses.index') }}" class="uk-button uk-button-default">Catégories</a>
        <a href="{{ route('admin.depenses.create') }}" class="uk-button uk-button-primary">+ Nouvelle dépense</a>
    </div>
</div>

<div class="uk-card uk-card-default uk-margin-top" style="overflow-x:auto; padding:0;">
    <table class="uk-table uk-table-divider uk-table-middle" style="margin:0;">
        <thead>
            <tr>
                <th>Date</th>
                <th>Catégorie</th>
                <th>Bénéficiaire</th>
                <th class="uk-text-right">Montant</th>
                <th>Statut</th>
                <th class="uk-text-right">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($expenses as $expense)
                <tr>
                    <td class="uk-text-muted">{{ $expense->expense_date->format('d/m/Y') }}</td>
                    <td style="font-weight:600;">{{ $expense->category->name }}</td>
                    <td class="uk-text-muted">{{ $expense->beneficiary ?? '—' }}</td>
                    <td class="uk-text-right" style="font-weight:600;">{{ number_format($expense->amount, 0, ',', ' ') }} FCFA</td>
                    <td>
                        @php $badge = ['validee' => 'background:rgba(29,138,78,.12); color:#1D8A4E;', 'en_attente' => 'background:rgba(240,169,59,.18); color:#8a5a1a;', 'rejetee' => 'background:rgba(232,96,79,.12); color:#E8604F;'][$expense->status]; @endphp
                        <span class="uk-label" style="{{ $badge }}">{{ \App\Models\Expense::STATUSES[$expense->status] }}</span>
                    </td>
                    <td class="uk-text-right">
                        @if($expense->status === 'en_attente')
                            <form action="{{ route('admin.depenses.validate', $expense) }}" method="POST" style="display:inline;">
                                @csrf @method('PATCH')
                                <button type="submit" style="font-weight:600; color:#1D8A4E; background:none; border:none; cursor:pointer;">Valider</button>
                            </form>
                            <form action="{{ route('admin.depenses.reject', $expense) }}" method="POST" style="display:inline;">
                                @csrf @method('PATCH')
                                <button type="submit" style="margin-left:12px; font-weight:600; color:#E8604F; background:none; border:none; cursor:pointer;">Rejeter</button>
                            </form>
                        @else
                            @if($expense->receipt_path)
                                <a href="{{ asset('fichiers/'.$expense->receipt_path) }}" target="_blank" style="font-weight:600; color:#1D8A4E;">Justificatif</a>
                            @endif
                            <form action="{{ route('admin.depenses.destroy', $expense) }}" method="POST" style="display:inline;" onsubmit="return confirm('Supprimer cette dépense ?')">
                                @csrf @method('DELETE')
                                <button type="submit" style="margin-left:12px; font-weight:600; color:#E8604F; background:none; border:none; cursor:pointer;">Supprimer</button>
                            </form>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="uk-text-center uk-text-muted" style="padding:32px 0;">Aucune dépense.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="uk-margin-top">{{ $expenses->links() }}</div>
@endsection
