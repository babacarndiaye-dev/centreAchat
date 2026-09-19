<?php

namespace App\Support\Notifications;

class NotificationEvents
{
    public const CHANNELS = [
        'interne' => 'Interne',
        'email' => 'E-mail',
        'push' => 'Notification push',
        'sms' => 'SMS',
        'whatsapp' => 'WhatsApp',
    ];

    /**
     * event_key => [label, audience (client|staff), permission (module.action, for staff audience), placeholders]
     */
    public const EVENTS = [
        'commande_creee' => [
            'label' => 'Nouvelle commande',
            'audience' => 'client',
            'placeholders' => ['client_nom', 'commande_numero', 'commande_total', 'commande_lien'],
        ],
        'commande_statut' => [
            'label' => 'Statut de commande mis à jour',
            'audience' => 'client',
            'placeholders' => ['client_nom', 'commande_numero', 'commande_statut'],
        ],
        'paiement_recu' => [
            'label' => 'Paiement reçu',
            'audience' => 'client',
            'placeholders' => ['client_nom', 'commande_numero', 'paiement_montant', 'commande_solde'],
        ],
        'livraison_statut' => [
            'label' => 'Mise à jour de livraison',
            'audience' => 'client',
            'placeholders' => ['client_nom', 'commande_numero', 'commande_statut'],
        ],
        'stock_faible' => [
            'label' => 'Stock faible',
            'audience' => 'staff',
            'permission' => 'stock.voir',
            'placeholders' => ['produit_nom', 'produit_stock', 'produit_seuil'],
        ],
        'produit_expirant' => [
            'label' => 'Produit bientôt expiré',
            'audience' => 'staff',
            'permission' => 'stock.voir',
            'placeholders' => ['produit_nom', 'produit_expiration'],
        ],
        'facture_echue' => [
            'label' => 'Facture échue',
            'audience' => 'staff',
            'permission' => 'finance.voir',
            'placeholders' => ['commande_numero', 'client_nom', 'commande_total', 'commande_echeance'],
        ],
        'demande_fournisseur' => [
            'label' => 'Nouvelle demande d\'achat à valider',
            'audience' => 'staff',
            'permission' => 'fournisseurs.valider',
            'placeholders' => ['demande_reference', 'demande_demandeur', 'demande_lien'],
        ],
        'nouvelle_conversation' => [
            'label' => 'Nouveau message de chat',
            'audience' => 'staff',
            'permission' => 'messagerie.voir',
            'placeholders' => ['client_nom', 'message_extrait', 'conversation_lien'],
        ],
        'b2b_compte_valide' => [
            'label' => 'Compte professionnel validé',
            'audience' => 'client',
            'placeholders' => ['client_nom', 'compte_type', 'compte_lien'],
        ],
        'b2b_compte_refuse' => [
            'label' => 'Compte professionnel refusé',
            'audience' => 'client',
            'placeholders' => ['client_nom', 'compte_type'],
        ],
    ];

    public static function label(string $eventKey): string
    {
        return self::EVENTS[$eventKey]['label'] ?? $eventKey;
    }

    public static function placeholders(string $eventKey): array
    {
        return self::EVENTS[$eventKey]['placeholders'] ?? [];
    }
}
