@extends('layouts.app')

@section('title', 'Mes commandes récurrentes')

@section('content')
<section class="mx-auto max-w-4xl px-4 py-16 sm:px-6 lg:px-8">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <span class="section-eyebrow">Espace professionnel</span>
            <h1 class="section-title mt-2">Commandes récurrentes</h1>
        </div>
        <a href="{{ route('compte.commandes-recurrentes.create') }}" class="btn-primary">+ Programmer une commande</a>
    </div>

    <a href="{{ route('compte.index') }}" class="mt-4 inline-block text-sm text-terroir-dark/60 hover:text-terroir-terracotta">← Retour à mon compte</a>

    @if($recurringOrders->isEmpty())
        <p class="mt-10 text-terroir-dark/60">Aucune commande récurrente programmée.</p>
    @else
        <div class="mt-8 space-y-4">
            @foreach($recurringOrders as $ro)
                <div class="card p-5">
                    <div class="flex flex-wrap items-center justify-between gap-4">
                        <div>
                            <p class="font-semibold">{{ \App\Models\RecurringOrder::FREQUENCIES[$ro->frequency] }}</p>
                            <p class="text-sm text-terroir-dark/50">Prochaine commande le {{ $ro->next_run_date->format('d/m/Y') }} — livraison {{ $ro->city }}</p>
                        </div>
                        <span class="rounded-full px-4 py-1.5 text-xs font-semibold {{ $ro->status === 'active' ? 'bg-terroir-green/10 text-terroir-green' : 'bg-terroir-dark/10 text-terroir-dark/60' }}">{{ $ro->status === 'active' ? 'Active' : 'Suspendue' }}</span>
                    </div>
                    <ul class="mt-3 text-xs text-terroir-dark/60">
                        @foreach($ro->items as $item)
                            <li>{{ $item->quantity }} × {{ $item->product->name }}</li>
                        @endforeach
                    </ul>
                    <div class="mt-4 flex gap-3">
                        <form action="{{ route('compte.commandes-recurrentes.toggle', $ro) }}" method="POST">
                            @csrf @method('PATCH')
                            <button type="submit" class="text-sm font-medium text-terroir-green hover:underline">{{ $ro->status === 'active' ? 'Suspendre' : 'Réactiver' }}</button>
                        </form>
                        <form action="{{ route('compte.commandes-recurrentes.destroy', $ro) }}" method="POST" onsubmit="return confirm('Supprimer cette commande récurrente ?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-sm font-medium text-terroir-terracotta hover:underline">Supprimer</button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</section>
@endsection
