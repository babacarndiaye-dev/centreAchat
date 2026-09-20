<?php

namespace App\Support;

use App\Models\User;
use Illuminate\Support\Collection;

class AdminNav
{
    /**
     * Same grouped structure as the old sidebar-nav Blade partial, filtered by
     * the given user's permissions. Kept in one place so the React sidebar and
     * any future Blade fallback read from the same source of truth.
     */
    public static function forUser(User $user): array
    {
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

        $visible = fn (array $link) => ! isset($link['permission'])
            || Collection::make((array) $link['permission'])->contains(fn ($permission) => $user->hasPermission($permission));

        return Collection::make($groups)
            ->map(fn (array $items) => array_values(array_filter($items, $visible)))
            ->filter(fn (array $items) => ! empty($items))
            ->map(fn (array $items, string $label) => ['label' => $label, 'items' => $items])
            ->values()
            ->all();
    }
}
