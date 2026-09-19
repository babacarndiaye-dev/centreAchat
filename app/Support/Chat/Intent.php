<?php

namespace App\Support\Chat;

use Illuminate\Support\Str;

class Intent
{
    public const BONJOUR = 'BONJOUR';
    public const IDENTITE_ASSISTANT = 'IDENTITE_ASSISTANT';
    public const REMERCIEMENT = 'REMERCIEMENT';
    public const AU_REVOIR = 'AU_REVOIR';
    public const INCOMPREHENSION = 'INCOMPREHENSION';
    public const LOCALISATION = 'LOCALISATION';
    public const HORAIRES = 'HORAIRES';
    public const PRODUITS = 'PRODUITS';
    public const RECHERCHE_PRODUIT = 'RECHERCHE_PRODUIT';
    public const PRIX = 'PRIX';
    public const DISPONIBILITE = 'DISPONIBILITE';
    public const COMMANDE = 'COMMANDE';
    public const SUIVI_COMMANDE = 'SUIVI_COMMANDE';
    public const ANNULATION_COMMANDE = 'ANNULATION_COMMANDE';
    public const LIVRAISON = 'LIVRAISON';
    public const PAIEMENT = 'PAIEMENT';
    public const FACTURE = 'FACTURE';
    public const PROMOTION = 'PROMOTION';
    public const CODE_PROMO = 'CODE_PROMO';
    public const COMPTE = 'COMPTE';
    public const MOT_DE_PASSE = 'MOT_DE_PASSE';
    public const PROFESSIONNEL = 'PROFESSIONNEL';
    public const HOTEL = 'HOTEL';
    public const TOURISTE = 'TOURISTE';
    public const FOURNISSEUR = 'FOURNISSEUR';
    public const RECLAMATION = 'RECLAMATION';
    public const CONTACT_AGENT = 'CONTACT_AGENT';

    public const LABELS = [
        self::BONJOUR => 'Salutation',
        self::IDENTITE_ASSISTANT => 'Identité de l\'assistant',
        self::REMERCIEMENT => 'Remerciement',
        self::AU_REVOIR => 'Au revoir',
        self::INCOMPREHENSION => 'Incompréhension',
        self::LOCALISATION => 'Localisation',
        self::HORAIRES => 'Horaires',
        self::PRODUITS => 'Produits',
        self::RECHERCHE_PRODUIT => 'Recherche produit',
        self::PRIX => 'Prix',
        self::DISPONIBILITE => 'Disponibilité',
        self::COMMANDE => 'Commande',
        self::SUIVI_COMMANDE => 'Suivi de commande',
        self::ANNULATION_COMMANDE => 'Annulation de commande',
        self::LIVRAISON => 'Livraison',
        self::PAIEMENT => 'Paiement',
        self::FACTURE => 'Facture',
        self::PROMOTION => 'Promotion',
        self::CODE_PROMO => 'Code promo',
        self::COMPTE => 'Compte',
        self::MOT_DE_PASSE => 'Mot de passe',
        self::PROFESSIONNEL => 'Professionnel',
        self::HOTEL => 'Hôtel',
        self::TOURISTE => 'Touriste',
        self::FOURNISSEUR => 'Fournisseur',
        self::RECLAMATION => 'Réclamation',
        self::CONTACT_AGENT => 'Transfert agent',
    ];

    /**
     * Ordered so more specific intents are tested before broader ones that share
     * words with them (e.g. ANNULATION_COMMANDE / SUIVI_COMMANDE before the
     * generic COMMANDE bucket). Phrase-based, not single-word, to keep false
     * positives low without any external NLP/AI call.
     */
    protected const PHRASES = [
        self::BONJOUR => ['bonjour', 'bonsoir', 'salut', 'coucou', 'bjr', 'slt', 'hello'],
        self::IDENTITE_ASSISTANT => [
            'qui es tu', 'tu es qui', 'quel est ton nom', 'tu es un robot', 'tu es une ia',
        ],
        self::CONTACT_AGENT => [
            'agent', 'humain', 'conseiller', 'quelquun', 'personne reelle', 'vrai personne',
            'vraie personne', 'service client', 'parler a',
        ],
        self::ANNULATION_COMMANDE => ['annuler ma commande', 'annuler commande', 'annulation', 'je veux annuler'],
        self::SUIVI_COMMANDE => [
            'ou est ma commande', 'suivre ma commande', 'suivre mon colis', 'statut de ma commande',
            'ma commande est ou', 'ou en est ma', 'mon colis', 'ma livraison est', 'commande est partie',
            'commande en retard', 'pas encore livre', 'quand vais-je recevoir', 'suivre ma livraison',
        ],
        self::RECLAMATION => [
            'reclamation', 'produit endommage', 'ne correspond pas', 'produit defectueux',
            'probleme avec ma commande', 'produit abime', 'mauvais produit',
        ],
        self::MOT_DE_PASSE => ['mot de passe', 'mdp oublie', 'mot de passe oublie'],
        self::CODE_PROMO => ['code promo', 'coupon', 'code de reduction', 'bon de reduction'],
        self::PROMOTION => ['promotion', 'en promo', 'offre speciale', 'les soldes'],
        self::FACTURE => ['facture', 'recu de commande', 'justificatif'],
        self::PAIEMENT => ['moyen de paiement', 'comment payer', 'payer par', 'mode de paiement', 'moyens de paiement'],
        self::LIVRAISON => [
            'livraison', 'livrer', 'delai de livraison', 'zone de livraison', 'frais de livraison',
            'livrez vous', 'livre a domicile',
        ],
        self::HOTEL => ['je represente un hotel', 'livraison hotel', 'livre a mon hotel', 'pour mon hotel'],
        self::TOURISTE => [
            'je suis touriste', 'je suis un touriste', 'je suis en vacances', 'je suis de passage',
            'visiteur etranger', 'souvenir a rapporter', 'coffret souvenir', 'prix en euro', 'payer en euro',
        ],
        self::PROFESSIONNEL => [
            'compte professionnel', 'devenir professionnel', 'tarif professionnel', 'client professionnel',
            'tarif pro', 'revendeur', 'prix de gros', 'devenir client pro',
        ],
        self::FOURNISSEUR => ['devenir fournisseur', 'je suis producteur', 'vendre mes produits', 'partenariat fournisseur'],
        self::COMPTE => ['creer un compte', 'ouvrir un compte', 'inscription', 'sinscrire', 'creer mon compte'],
        self::PRIX => ['prix dun produit', 'combien coute', 'quel est le prix', 'tarif du produit'],
        self::DISPONIBILITE => ['disponible', 'en stock', 'rupture de stock', 'est ce que vous avez'],
        self::RECHERCHE_PRODUIT => ['rechercher un produit', 'trouver un produit', 'comment chercher', 'barre de recherche'],
        self::PRODUITS => ['voir vos produits', 'vos produits', 'que vendez vous', 'catalogue'],
        self::HORAIRES => ['horaires', 'heures douverture', 'quand etes vous ouvert', 'vous etes ouvert'],
        self::LOCALISATION => ['ou etes vous', 'vous etes situe', 'situe ou', 'ou se trouve', 'votre boutique', 'votre magasin', 'comment venir chez vous'],
        self::COMMANDE => ['passer commande', 'comment commander', 'comment acheter', 'passer une commande', 'comment passer'],

        // Conversational closers — checked last, on purpose: many domain questions in the
        // wild are wrapped in filler ("Merci de m'expliquer : Comment créer un compte ?"),
        // so a real domain match must always win over a bare "merci"/"au revoir" substring.
        self::AU_REVOIR => [
            'au revoir', 'a bientot', 'bonne journee', 'bonne soiree', 'bonne nuit', 'a plus tard',
        ],
        self::INCOMPREHENSION => [
            'je ne comprends pas', 'je nai pas compris', 'explique moi', 'peux tu repeter',
            'parle plus simplement', 'expliquer autrement',
        ],
        // Bare "merci" is safe here specifically because this list is checked last:
        // any message that also carries real domain content ("merci de m'expliquer
        // comment créer un compte") will already have matched COMPTE etc. above.
        self::REMERCIEMENT => ['merci', 'je vous remercie', 'cest gentil'],
    ];

    public static function classify(string $message): ?string
    {
        $normalized = Str::lower(Str::ascii($message));
        $normalized = preg_replace('/[^a-z0-9\s]/', ' ', $normalized) ?? $normalized;
        $normalized = preg_replace('/\s+/', ' ', $normalized) ?? $normalized;

        foreach (self::PHRASES as $intent => $phrases) {
            foreach ($phrases as $phrase) {
                if (str_contains($normalized, $phrase)) {
                    return $intent;
                }
            }
        }

        return null;
    }

    public static function label(?string $intent): ?string
    {
        return $intent ? (self::LABELS[$intent] ?? $intent) : null;
    }

    public static function all(): array
    {
        return array_keys(self::LABELS);
    }
}
