@php
    $groups = [
        'Vue d\'ensemble' => [
            ['route' => 'admin.dashboard', 'label' => 'Tableau de bord', 'icon' => 'dashboard'],
            ['route' => 'admin.analytics.index', 'label' => 'Statistiques (BI)', 'icon' => 'monitoring', 'permission' => 'analytics.voir'],
        ],
        'Catalogue' => [
            ['route' => 'admin.categories.index', 'label' => 'Catégories', 'icon' => 'category', 'permission' => ['produits.voir', 'stock.voir']],
            ['route' => 'admin.producteurs.index', 'label' => 'Producteurs', 'icon' => 'agriculture', 'permission' => ['produits.voir', 'stock.voir']],
            ['route' => 'admin.produits.index', 'label' => 'Produits', 'icon' => 'inventory_2', 'permission' => ['produits.voir', 'stock.voir']],
            ['route' => 'admin.produits-parametres.unites.index', 'label' => 'Unités', 'icon' => 'straighten', 'permission' => ['produits.voir', 'stock.voir']],
            ['route' => 'admin.produits-parametres.conditionnements.index', 'label' => 'Conditionnements', 'icon' => 'package_2', 'permission' => ['produits.voir', 'stock.voir']],
            ['route' => 'admin.produits-parametres.attributs.index', 'label' => 'Attributs', 'icon' => 'sell', 'permission' => ['produits.voir', 'stock.voir']],
        ],
        'Ventes & clients' => [
            ['route' => 'admin.commandes.index', 'label' => 'Commandes clients', 'icon' => 'receipt_long', 'permission' => 'commandes.voir'],
            ['route' => 'admin.b2b.index', 'label' => 'Clients professionnels', 'icon' => 'business_center', 'permission' => 'clients_pro.voir'],
            ['route' => 'admin.devis.index', 'label' => 'Devis', 'icon' => 'description', 'permission' => 'clients_pro.voir'],
            ['route' => 'admin.commandes-recurrentes.index', 'label' => 'Commandes récurrentes', 'icon' => 'autorenew', 'permission' => 'clients_pro.voir'],
            ['route' => 'admin.messagerie.index', 'label' => 'Messagerie', 'icon' => 'forum', 'permission' => 'messagerie.voir'],
            ['route' => 'admin.messagerie.faq.index', 'label' => 'Base de connaissances (FAQ)', 'icon' => 'menu_book', 'permission' => 'messagerie.voir'],
        ],
        'Point de vente' => [
            ['route' => 'admin.pos.caisse.index', 'label' => 'Caisse (POS)', 'icon' => 'point_of_sale', 'permission' => 'pos.voir'],
            ['route' => 'admin.pos.retours.index', 'label' => 'Retours', 'icon' => 'undo', 'permission' => 'pos.voir'],
        ],
        'Achats & fournisseurs' => [
            ['route' => 'admin.fournisseurs.index', 'label' => 'Fournisseurs', 'icon' => 'handshake', 'permission' => 'fournisseurs.voir'],
            ['route' => 'admin.demandes-achat.index', 'label' => 'Demandes d\'achat', 'icon' => 'edit_note', 'permission' => 'fournisseurs.voir'],
            ['route' => 'admin.bons-commande.index', 'label' => 'Bons de commande', 'icon' => 'inventory', 'permission' => 'fournisseurs.voir'],
        ],
        'Finance' => [
            ['route' => 'admin.tresorerie.index', 'label' => 'Trésorerie', 'icon' => 'payments', 'permission' => 'finance.voir'],
            ['route' => 'admin.comptes-paiement.index', 'label' => 'Comptes de paiement', 'icon' => 'account_balance', 'permission' => 'finance.voir'],
            ['route' => 'admin.depenses.index', 'label' => 'Dépenses', 'icon' => 'calculate', 'permission' => 'finance.voir'],
            ['route' => 'admin.immobilisations.index', 'label' => 'Immobilisations', 'icon' => 'apartment', 'permission' => 'immobilisations.voir'],
        ],
        'Comptabilité' => [
            ['route' => 'admin.comptabilite.plan-comptable.index', 'label' => 'Plan comptable', 'icon' => 'account_tree', 'permission' => 'comptabilite.voir'],
            ['route' => 'admin.comptabilite.journaux.index', 'label' => 'Journaux', 'icon' => 'book', 'permission' => 'comptabilite.voir'],
            ['route' => 'admin.comptabilite.ecritures.index', 'label' => 'Écritures', 'icon' => 'edit_square', 'permission' => 'comptabilite.voir'],
            ['route' => 'admin.comptabilite.grand-livre.index', 'label' => 'Grand livre', 'icon' => 'menu_book', 'permission' => 'comptabilite.voir'],
            ['route' => 'admin.comptabilite.balance.index', 'label' => 'Balance', 'icon' => 'balance', 'permission' => 'comptabilite.voir'],
            ['route' => 'admin.comptabilite.rapprochement.index', 'label' => 'Rapprochement bancaire', 'icon' => 'sync_alt', 'permission' => 'comptabilite.voir'],
        ],
        'Contenu & marketing' => [
            ['route' => 'admin.pages.index', 'label' => 'Pages', 'icon' => 'article', 'permission' => 'contenu.voir'],
            ['route' => 'admin.articles.index', 'label' => 'Actualités', 'icon' => 'newspaper', 'permission' => 'contenu.voir'],
            ['route' => 'admin.avis.index', 'label' => 'Avis clients', 'icon' => 'star', 'permission' => 'contenu.voir'],
            ['route' => 'admin.avis-produits.index', 'label' => 'Avis produits', 'icon' => 'reviews', 'permission' => 'contenu.voir'],
            ['route' => 'admin.messages.index', 'label' => 'Messages', 'icon' => 'mail', 'permission' => 'contenu.voir'],
            ['route' => 'admin.newsletter.index', 'label' => 'Newsletter', 'icon' => 'campaign', 'permission' => 'contenu.voir'],
        ],
        'Commercial' => [
            ['route' => 'admin.commercial.taxes.index', 'label' => 'Taxes', 'icon' => 'percent', 'permission' => 'commercial.voir'],
            ['route' => 'admin.commercial.paiements.index', 'label' => 'Modes de paiement', 'icon' => 'credit_card', 'permission' => 'commercial.voir'],
            ['route' => 'admin.commercial.zones.index', 'label' => 'Zones de livraison', 'icon' => 'local_shipping', 'permission' => 'commercial.voir'],
        ],
        'Système' => [
            ['route' => 'admin.parametres.edit', 'label' => 'Paramètres', 'icon' => 'settings', 'permission' => 'systeme.modifier'],
            ['route' => 'admin.roles.index', 'label' => 'Rôles', 'icon' => 'admin_panel_settings', 'permission' => 'systeme.modifier'],
            ['route' => 'admin.utilisateurs.index', 'label' => 'Utilisateurs', 'icon' => 'group', 'permission' => 'systeme.modifier'],
            ['route' => 'admin.notifications.index', 'label' => 'Notifications', 'icon' => 'notifications'],
            ['route' => 'admin.notifications.templates.index', 'label' => 'Modèles de messages', 'icon' => 'forum', 'permission' => 'systeme.modifier'],
        ],
    ];

    // Links with no 'permission' key (dashboard, personal notifications) aren't gated by
    // any route middleware, so they stay visible to any staff member. A 'permission' can
    // be a single string or an array of alternatives (any one grants visibility) — mirrors
    // the 'permission:a,b' OR-syntax used on the routes themselves.
    $linkVisible = function ($link) {
        if (! isset($link['permission'])) {
            return true;
        }

        return collect((array) $link['permission'])->contains(fn ($permission) => auth()->user()->hasPermission($permission));
    };

    $groups = collect($groups)->map(
        fn ($items) => array_values(array_filter($items, $linkVisible))
    )->filter(fn ($items) => ! empty($items))->all();

    // A link is active on its exact route, or on a sibling action at the SAME nesting
    // depth (e.g. admin.messagerie.index / admin.messagerie.show). Comparing only the
    // route name's prefix up to its last segment — rather than a broad "starts with"
    // wildcard — stops a nested sub-resource like admin.messagerie.faq.* from also
    // lighting up the parent admin.messagerie.* link.
    $routeBase = fn (string $route) => \Illuminate\Support\Str::beforeLast($route, '.');
    $currentRoute = request()->route()?->getName();
    $isLinkActive = fn ($link) => $currentRoute && (
        $currentRoute === $link['route'] || $routeBase($currentRoute) === $routeBase($link['route'])
    );

    $sidebarLogoPath = \App\Models\Setting::get('logo_path');
    $sidebarSiteName = \App\Models\Setting::get('site_name') ?: "Central d'Achat";
@endphp

<div class="flex h-16 shrink-0 items-center gap-2 border-b border-white/10 px-6">
    @if($sidebarLogoPath)
        <img src="{{ asset('fichiers/'.$sidebarLogoPath) }}" alt="{{ $sidebarSiteName }}" width="28" height="28" class="rounded-full object-cover">
    @else
        <span class="material-symbols-outlined text-xl">eco</span>
    @endif
    <span class="font-display text-base font-semibold text-white">{{ $sidebarSiteName }}</span>
</div>

<nav class="flex-1 space-y-1 overflow-y-auto px-3 py-4">
    @foreach($groups as $groupLabel => $items)
        @php $groupActive = collect($items)->contains($isLinkActive); @endphp
        <div x-data="{ open: {{ $groupActive ? 'true' : 'false' }} }">
            <button type="button" @click="open = !open" class="flex w-full items-center justify-between rounded-lg px-3 py-2 text-left text-[11px] font-bold uppercase tracking-wider text-white/40 hover:text-white/70">
                <span>{{ $groupLabel }}</span>
                <span class="material-symbols-outlined text-sm transition" :class="open ? 'rotate-180' : ''">expand_more</span>
            </button>
            <ul x-show="open" x-transition class="mt-0.5 space-y-0.5">
                @foreach($items as $link)
                    @php $active = $isLinkActive($link); @endphp
                    <li>
                        <a href="{{ route($link['route']) }}" class="flex items-center gap-2.5 rounded-lg px-3 py-2 text-sm transition {{ $active ? 'bg-terroir-green text-white' : 'text-white/65 hover:bg-white/5 hover:text-white' }}">
                            <span class="material-symbols-outlined text-lg">{{ $link['icon'] }}</span>
                            <span>{{ $link['label'] }}</span>
                            @if($link['route'] === 'admin.messagerie.index')
                                @php($unreadChat = \App\Models\Conversation::unreadForStaffCount())
                                @if($unreadChat > 0)
                                    <span class="ml-auto rounded-full bg-terroir-terracotta px-1.5 py-0.5 text-[10px] font-bold text-white">{{ $unreadChat }}</span>
                                @endif
                            @endif
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
    @endforeach
</nav>

<div class="shrink-0 space-y-0.5 border-t border-white/10 p-3">
    <a href="{{ route('accueil') }}" class="flex items-center gap-2.5 rounded-lg px-3 py-2 text-sm text-white/65 hover:bg-white/5 hover:text-white">
        <span class="material-symbols-outlined text-lg">arrow_back</span>
        Retour au site
    </a>
    <form action="{{ route('logout') }}" method="POST">
        @csrf
        <button type="submit" class="flex w-full items-center gap-2.5 rounded-lg px-3 py-2 text-left text-sm text-white/65 hover:bg-white/5 hover:text-white">
            <span class="material-symbols-outlined text-lg">logout</span>
            Se déconnecter
        </button>
    </form>
</div>
