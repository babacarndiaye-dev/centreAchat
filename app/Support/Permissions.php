<?php

namespace App\Support;

class Permissions
{
    public const ACTIONS = [
        'voir' => 'Voir',
        'creer' => 'Créer',
        'modifier' => 'Modifier',
        'supprimer' => 'Supprimer',
        'valider' => 'Valider',
        'exporter' => 'Exporter',
    ];

    public const MODULES = [
        'produits' => 'Produits & catalogue',
        'commandes' => 'Commandes clients',
        'clients_pro' => 'Clients professionnels & devis',
        'fournisseurs' => 'Fournisseurs & achats',
        'stock' => 'Stock & inventaire',
        'pos' => 'Point de vente (caisse)',
        'finance' => 'Finance & trésorerie',
        'comptabilite' => 'Comptabilité',
        'immobilisations' => 'Immobilisations',
        'contenu' => 'Contenu & marketing',
        'messagerie' => 'Messagerie & support client',
        'commercial' => 'Paramétrage commercial (taxes, paiements, livraison)',
        'analytics' => 'Statistiques & tableaux de bord avancés',
        'systeme' => 'Système (utilisateurs, rôles, paramètres)',
    ];

    /**
     * All valid "module.action" permission keys.
     */
    public static function all(): array
    {
        $permissions = [];

        foreach (array_keys(self::MODULES) as $module) {
            foreach (array_keys(self::ACTIONS) as $action) {
                $permissions[] = "{$module}.{$action}";
            }
        }

        return $permissions;
    }

    public static function label(string $permission): string
    {
        [$module, $action] = array_pad(explode('.', $permission), 2, null);

        return (self::MODULES[$module] ?? $module).' — '.(self::ACTIONS[$action] ?? $action);
    }
}
