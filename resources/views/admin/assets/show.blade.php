@extends('layouts.admin')

@section('title', $asset->name)

@section('content')
<div class="flex flex-wrap items-center justify-between gap-4">
    <div>
        <span class="text-xs font-semibold uppercase tracking-wide text-terroir-dark/50">{{ \App\Models\FixedAsset::CATEGORIES[$asset->category] }}</span>
        <h2 class="mt-1 font-display text-2xl font-semibold">{{ $asset->name }}</h2>
    </div>
    <div class="flex items-center gap-3">
        <span class="{{ $asset->statusBadgeClass() }} px-4 py-1.5 text-sm">{{ \App\Models\FixedAsset::STATUSES[$asset->status] }}</span>
        <a href="{{ route('admin.immobilisations.edit', $asset) }}" class="btn-outline">Modifier</a>
    </div>
</div>

<div class="mt-6 grid grid-cols-2 gap-4 lg:grid-cols-4">
    <div class="admin-card"><p class="text-xs text-terroir-dark/50">Valeur d'acquisition</p><p class="mt-1.5 text-xl font-bold">{{ number_format($asset->acquisition_value, 0, ',', ' ') }} FCFA</p></div>
    <div class="admin-card"><p class="text-xs text-terroir-dark/50">Amortissement annuel</p><p class="mt-1.5 text-xl font-bold">{{ number_format($asset->annualDepreciation(), 0, ',', ' ') }} FCFA</p></div>
    <div class="admin-card"><p class="text-xs text-terroir-dark/50">Amorti à ce jour</p><p class="mt-1.5 text-xl font-bold text-terroir-terracotta">{{ number_format($asset->accumulatedDepreciation(), 0, ',', ' ') }} FCFA</p></div>
    <div class="admin-card"><p class="text-xs text-terroir-dark/50">Valeur nette comptable</p><p class="mt-1.5 text-xl font-bold text-terroir-green">{{ number_format($asset->netBookValue(), 0, ',', ' ') }} FCFA</p></div>
</div>

<div class="mt-6 grid gap-6 lg:grid-cols-3">
    <div class="lg:col-span-2">
        <div class="admin-card">
            <h3 class="font-display text-base font-semibold">Tableau d'amortissement ({{ \App\Models\FixedAsset::METHODS[$asset->depreciation_method] }})</h3>
            <table class="admin-table mt-3">
                <thead>
                    <tr>
                        <th>Année</th>
                        <th class="text-right">Dotation annuelle</th>
                        <th class="text-right">Cumul</th>
                        <th class="text-right">VNC fin d'année</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($schedule as $row)
                        <tr class="{{ (int) floor($asset->yearsElapsed()) + 1 === $row['year'] && $asset->status === 'en_service' ? 'bg-terroir-cream font-semibold' : '' }}">
                            <td>{{ $row['year'] }}</td>
                            <td class="text-right">{{ number_format($row['annual'], 0, ',', ' ') }}</td>
                            <td class="text-right">{{ number_format($row['accumulated'], 0, ',', ' ') }}</td>
                            <td class="text-right">{{ number_format($row['net_value'], 0, ',', ' ') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div>
        <div class="admin-card">
            <h3 class="font-display text-base font-semibold">Informations</h3>
            <dl class="mt-3 space-y-2 text-sm">
                <div><dt class="text-terroir-dark/50">Date d'acquisition</dt><dd class="mt-0.5 font-semibold">{{ $asset->acquisition_date->format('d/m/Y') }}</dd></div>
                <div><dt class="text-terroir-dark/50">Durée d'amortissement</dt><dd class="mt-0.5 font-semibold">{{ $asset->useful_life_years }} ans</dd></div>
                @if($asset->paymentAccount)
                    <div><dt class="text-terroir-dark/50">Financé par</dt><dd class="mt-0.5 font-semibold">{{ $asset->paymentAccount->name }}</dd></div>
                @endif
                @if($asset->supplier)
                    <div><dt class="text-terroir-dark/50">Fournisseur</dt><dd class="mt-0.5 font-semibold">{{ $asset->supplier->name }}</dd></div>
                @endif
                @if($asset->notes)
                    <div><dt class="text-terroir-dark/50">Notes</dt><dd class="mt-0.5 font-semibold">{{ $asset->notes }}</dd></div>
                @endif
            </dl>
        </div>

        @if($asset->status === 'en_service')
            <div class="admin-card mt-6">
                <h3 class="font-display text-base font-semibold">Mettre hors service</h3>
                <form action="{{ route('admin.immobilisations.dispose', $asset) }}" method="POST" class="mt-4 flex flex-col gap-3" onsubmit="return confirm('Confirmer la mise hors service de cette immobilisation ?')">
                    @csrf
                    <select name="status" required class="input">
                        <option value="cede">Cédé (vendu)</option>
                        <option value="reforme">Réformé (mis au rebut)</option>
                    </select>
                    <input type="date" name="disposal_date" value="{{ now()->format('Y-m-d') }}" required class="input">
                    <input type="number" step="0.01" name="disposal_value" placeholder="Valeur de cession (FCFA, optionnel)" class="input">
                    <button type="submit" class="btn-outline w-full justify-center">Confirmer</button>
                </form>
            </div>
        @else
            <div class="admin-card mt-6 text-sm">
                <p class="text-terroir-dark/50">{{ \App\Models\FixedAsset::STATUSES[$asset->status] }} le</p>
                <p class="font-semibold">{{ optional($asset->disposal_date)->format('d/m/Y') }}</p>
                @if($asset->disposal_value)
                    <p class="mt-1.5 text-terroir-dark/50">Valeur de cession</p>
                    <p class="font-semibold">{{ number_format($asset->disposal_value, 0, ',', ' ') }} FCFA</p>
                @endif
            </div>
        @endif
    </div>
</div>
@endsection
