@extends('layouts.admin')

@section('title', 'Dépenses')

@section('content')
<div class="flex flex-wrap items-center justify-between gap-3">
    <form method="GET" class="flex flex-wrap gap-2">
        <select name="status" class="input max-w-[200px]" onchange="this.form.submit()">
            <option value="">Tous les statuts</option>
            @foreach(\App\Models\Expense::STATUSES as $value => $label)
                <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
            @endforeach
        </select>
        <select name="category" class="input max-w-[220px]" onchange="this.form.submit()">
            <option value="">Toutes catégories</option>
            @foreach($categories as $category)
                <option value="{{ $category->id }}" @selected((string) request('category') === (string) $category->id)>{{ $category->name }}</option>
            @endforeach
        </select>
    </form>
    <div class="flex gap-3">
        <a href="{{ route('admin.categories-depenses.index') }}" class="btn-outline">Catégories</a>
        <a href="{{ route('admin.depenses.create') }}" class="btn-primary">
            <span class="material-symbols-outlined text-lg">add</span>
            Nouvelle dépense
        </a>
    </div>
</div>

<div class="admin-card mt-6 overflow-x-auto p-0">
    <table class="admin-table">
        <thead>
            <tr>
                <th class="pl-6">Date</th>
                <th>Catégorie</th>
                <th>Bénéficiaire</th>
                <th class="text-right">Montant</th>
                <th>Statut</th>
                <th class="pr-6 text-right">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($expenses as $expense)
                <tr>
                    <td class="pl-6 text-terroir-dark/60">{{ $expense->expense_date->format('d/m/Y') }}</td>
                    <td class="font-semibold text-terroir-dark">{{ $expense->category->name }}</td>
                    <td class="text-terroir-dark/60">{{ $expense->beneficiary ?? '—' }}</td>
                    <td class="text-right font-semibold">{{ number_format($expense->amount, 0, ',', ' ') }} FCFA</td>
                    <td><span class="{{ $expense->statusBadgeClass() }}">{{ \App\Models\Expense::STATUSES[$expense->status] }}</span></td>
                    <td class="pr-6 text-right">
                        @if($expense->status === 'en_attente')
                            <form action="{{ route('admin.depenses.validate', $expense) }}" method="POST" class="inline">
                                @csrf @method('PATCH')
                                <button type="submit" class="admin-link bg-transparent">Valider</button>
                            </form>
                            <form action="{{ route('admin.depenses.reject', $expense) }}" method="POST" class="inline">
                                @csrf @method('PATCH')
                                <button type="submit" class="admin-link-danger ml-3 bg-transparent">Rejeter</button>
                            </form>
                        @else
                            @if($expense->receipt_path)
                                <a href="{{ asset('fichiers/'.$expense->receipt_path) }}" target="_blank" class="admin-link">Justificatif</a>
                            @endif
                            <form action="{{ route('admin.depenses.destroy', $expense) }}" method="POST" class="inline" onsubmit="return confirm('Supprimer cette dépense ?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="admin-link-danger ml-3 bg-transparent">Supprimer</button>
                            </form>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="py-8 text-center text-terroir-dark/40">Aucune dépense.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-6">{{ $expenses->links() }}</div>
@endsection
