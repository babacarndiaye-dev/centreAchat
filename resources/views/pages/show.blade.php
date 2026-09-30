@extends('layouts.app')

@section('title', $page->meta_title ?: $page->title." — DIABA HOTEL")
@section('meta_description', $page->meta_description ?? '')

@section('content')
<section class="mx-auto max-w-4xl px-4 py-16 sm:px-6 lg:px-8">
    <div class="text-center">
        <span class="section-eyebrow">DIABA HOTEL</span>
        <h1 class="section-title mt-2">{{ $page->title }}</h1>
    </div>

    @if($page->content)
        <div class="prose prose-terroir mx-auto mt-10 max-w-none leading-relaxed text-terroir-dark/80">
            {!! nl2br(e($page->content)) !!}
        </div>
    @endif

    @if($page->slug === 'contact')
        <div class="mt-16 grid gap-10 lg:grid-cols-2">
            <div>
                <h2 class="font-display text-xl font-semibold">Nos coordonnées</h2>
                <ul class="mt-6 space-y-4 text-sm text-terroir-dark/80">
                    <li class="flex items-start gap-3"><span class="text-xl">📍</span> {{ \App\Models\Setting::get('address', 'Rond-Point Malicounda, Mbour – Sénégal') }}</li>
                    <li class="flex items-start gap-3"><span class="text-xl">📞</span> {{ \App\Models\Setting::get('phone', '+221 XX XXX XX XX') }}</li>
                    <li class="flex items-start gap-3"><span class="text-xl">✉️</span> {{ \App\Models\Setting::get('email', 'contact@centraldachat.sn') }}</li>
                    <li class="flex items-start gap-3"><span class="text-xl">🕒</span> {{ \App\Models\Setting::get('opening_hours', 'Lun - Sam : 8h - 19h') }}</li>
                </ul>
            </div>

            <form action="{{ route('contact.store') }}" method="POST" class="card space-y-4 p-8">
                @csrf
                <div>
                    <label class="label" for="name">Nom</label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" required class="input">
                    @error('name') <p class="mt-1 text-xs text-terroir-terracotta">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="label" for="email">E-mail</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required class="input">
                    @error('email') <p class="mt-1 text-xs text-terroir-terracotta">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="label" for="phone">Téléphone (optionnel)</label>
                    <input type="text" id="phone" name="phone" value="{{ old('phone') }}" class="input">
                </div>
                <div>
                    <label class="label" for="subject">Sujet</label>
                    <input type="text" id="subject" name="subject" value="{{ old('subject') }}" class="input">
                </div>
                <div>
                    <label class="label" for="message">Message</label>
                    <textarea id="message" name="message" rows="4" required class="input">{{ old('message') }}</textarea>
                    @error('message') <p class="mt-1 text-xs text-terroir-terracotta">{{ $message }}</p> @enderror
                </div>
                <button type="submit" class="btn-primary w-full justify-center">Envoyer le message</button>
            </form>
        </div>
    @endif

    @if($page->slug === 'devenir-fournisseur')
        <div class="mt-16 card p-8 text-center">
            <p class="text-terroir-dark/70">Vous êtes producteur ou fournisseur et souhaitez rejoindre notre réseau ?</p>
            <a href="{{ route('pages.show', 'contact') }}" class="btn-primary mt-6">Nous contacter</a>
        </div>
    @endif

    @if($page->slug === 'hotels-professionnels')
        <div class="mt-16">
            <div class="grid gap-6 sm:grid-cols-2">
                <div class="card p-6">
                    <span class="text-2xl">🏷️</span>
                    <h3 class="mt-3 font-display text-lg font-semibold">Tarifs professionnels & de gros</h3>
                    <p class="mt-2 text-sm text-terroir-dark/70">Prix dégressifs automatiquement appliqués sur nos produits éligibles dès validation de votre compte, et tarif de gros à partir de 10 unités.</p>
                </div>
                <div class="card p-6">
                    <span class="text-2xl">📄</span>
                    <h3 class="mt-3 font-display text-lg font-semibold">Devis personnalisés</h3>
                    <p class="mt-2 text-sm text-terroir-dark/70">Demandez un devis pour vos commandes importantes ou récurrentes, directement depuis votre espace client.</p>
                </div>
                <div class="card p-6">
                    <span class="text-2xl">🔁</span>
                    <h3 class="mt-3 font-display text-lg font-semibold">Commandes récurrentes</h3>
                    <p class="mt-2 text-sm text-terroir-dark/70">Programmez vos réapprovisionnements réguliers (hebdomadaires, mensuels...) et laissez-nous nous en occuper.</p>
                </div>
                <div class="card p-6">
                    <span class="text-2xl">💳</span>
                    <h3 class="mt-3 font-display text-lg font-semibold">Paiement à crédit</h3>
                    <p class="mt-2 text-sm text-terroir-dark/70">Un plafond de crédit adapté à votre activité peut vous être accordé, avec facturation à échéance de 30 jours.</p>
                </div>
            </div>

            <div class="card mt-8 p-8 text-center">
                <h2 class="font-display text-xl font-semibold">Créer mon compte professionnel</h2>
                <p class="mx-auto mt-2 max-w-xl text-sm text-terroir-dark/70">Choisissez votre profil : votre compte est créé immédiatement et passe en revue par notre équipe. Les tarifs professionnels s'activent dès validation.</p>
                <div class="mt-6 flex flex-wrap justify-center gap-3">
                    <a href="{{ route('register') }}?type=hotel" class="btn-primary">🏨 Je suis un hôtel</a>
                    <a href="{{ route('register') }}?type=restaurant" class="btn-primary">🍽️ Je suis un restaurant</a>
                    <a href="{{ route('register') }}?type=entreprise" class="btn-primary">🏢 Je suis une entreprise</a>
                    <a href="{{ route('register') }}?type=professionnel" class="btn-outline">Autre profil professionnel</a>
                </div>
                <p class="mt-6 text-xs text-terroir-dark/50">Déjà client particulier et souhaitez passer en compte professionnel ? <a href="{{ route('pages.show', 'contact') }}" class="font-medium text-terroir-green hover:underline">Contactez-nous</a>.</p>
            </div>
        </div>
    @endif

    @if($page->slug === 'espace-touristes')
        @php
            $souvenirProducts = \App\Models\Product::where('is_active', true)
                ->whereHas('category', fn ($q) => $q->where('slug', 'coffrets-cadeaux'))
                ->with('images')->limit(4)->get();
        @endphp
        <div class="mt-16">
            <div class="grid gap-6 sm:grid-cols-3">
                <div class="card p-6">
                    <span class="text-2xl">🎁</span>
                    <h3 class="mt-3 font-display text-lg font-semibold">Coffrets souvenirs</h3>
                    <p class="mt-2 text-sm text-terroir-dark/70">Des sélections prêtes à emporter ou à offrir, représentatives du terroir sénégalais.</p>
                </div>
                <div class="card p-6">
                    <span class="text-2xl">🏨</span>
                    <h3 class="mt-3 font-display text-lg font-semibold">Livraison à l'hôtel</h3>
                    <p class="mt-2 text-sm text-terroir-dark/70">Indiquez le nom de votre hôtel et votre numéro de chambre au moment de la commande, nous vous livrons directement.</p>
                </div>
                <div class="card p-6">
                    <span class="text-2xl">💶</span>
                    <h3 class="mt-3 font-display text-lg font-semibold">Prix en euro (indicatif)</h3>
                    <p class="mt-2 text-sm text-terroir-dark/70">Les prix sont affichés en FCFA avec une conversion en euro à titre indicatif sur chaque produit.</p>
                </div>
            </div>

            @if($souvenirProducts->isNotEmpty())
                <div class="mt-12">
                    <h2 class="font-display text-xl font-semibold text-center">Nos coffrets à emporter</h2>
                    <div class="mt-6 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
                        @foreach($souvenirProducts as $item)
                            @include('partials.product-card', ['product' => $item])
                        @endforeach
                    </div>
                </div>
            @endif

            <div class="card mt-12 p-8 text-center">
                <p class="text-terroir-dark/70">Paiement à la livraison en espèces, ou par Wave / Orange Money. Le paiement par carte bancaire n'est pas encore disponible en ligne — uniquement en boutique.</p>
                <a href="{{ route('produits.index', ['categorie' => 'coffrets-cadeaux']) }}" class="btn-primary mt-6">Voir tous les coffrets</a>
            </div>
        </div>
    @endif

    @if($page->slug === 'coffrets-cadeaux')
        @php
            $coffrets = \App\Models\Product::where('is_active', true)
                ->whereHas('category', fn ($q) => $q->where('slug', 'coffrets-cadeaux'))
                ->with('images')->get();
        @endphp
        <div class="mt-16">
            @if($coffrets->isNotEmpty())
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach($coffrets as $item)
                        @include('partials.product-card', ['product' => $item])
                    @endforeach
                </div>
            @endif

            <div class="card mt-10 p-8 text-center">
                <span class="text-2xl">🎁</span>
                <h2 class="mt-2 font-display text-xl font-semibold">Un cadeau à offrir ?</h2>
                <p class="mx-auto mt-2 max-w-xl text-sm text-terroir-dark/70">Chaque coffret est déjà prêt à offrir. Lors de votre commande, vous pouvez ajouter un message personnalisé qui sera joint à la préparation.</p>
            </div>
        </div>
    @endif
</section>
@endsection
