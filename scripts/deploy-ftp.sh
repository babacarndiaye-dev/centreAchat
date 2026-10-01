#!/usr/bin/env bash
# =============================================================================
# Met à jour DIABA HOTEL Produits du Sénégal (D.H.P.S) en ligne depuis votre poste, par FTPS, en une commande :
# prépare les fichiers de production puis n'envoie que ce qui a changé.
#
# Prérequis (macOS) : brew install lftp node   (+ php et composer, sauf avec --sans-vendor)
#   PHP 8.3 minimum, avec l'extension « gmp » (notifications push).
#
# Usage :
#   scripts/deploy-ftp.sh --dry-run   # affiche ce qui serait envoyé, sans rien envoyer
#   scripts/deploy-ftp.sh             # envoie réellement
#   scripts/deploy-ftp.sh --sans-vendor   # n'envoie ni vendor ni PHP/Composer local : le dossier
#                                     # vendor est construit sur le serveur avec Composer
#   scripts/deploy-ftp.sh --full      # répare un envoi incomplet : renvoie tout fichier dont
#                                     # la taille diffère du serveur (fichier tronqué, vendor
#                                     # incomplet → « Class ... not found »)
#   FTP_DIR=public_html scripts/deploy-ftp.sh   # si l'application est dans un sous-dossier
#
# Le mot de passe FTP est demandé au clavier : il n'est jamais écrit sur disque.
# Ne sont JAMAIS envoyés ni supprimés sur le serveur : .env, storage/ (fichiers
# téléversés, journaux, sessions), public/storage, la base de données.
# =============================================================================
set -euo pipefail

FTP_HOST="${FTP_HOST:-ftp.hotelcentraleachat.com}"
FTP_USER="${FTP_USER:-Hotelcentraleachat@hotelcentraleachat.com}"
# Dossier distant de l'application, vu depuis le compte FTP. Le dossier qui
# contient « artisan », « app » et « bootstrap » (pas seulement « public »).
FTP_DIR="${FTP_DIR:-.}"
DRY_RUN=""
COMPARE="--only-newer"
SANS_VENDOR=""
for arg in "$@"; do
    case "$arg" in
        --dry-run) DRY_RUN="--dry-run" ;;
        --full) COMPARE="--ignore-time" ;;
        --sans-vendor) SANS_VENDOR=1 ;;
        *) echo "✗ Option inconnue : $arg (options : --dry-run, --full, --sans-vendor)" >&2; exit 1 ;;
    esac
done

ROOT="$(cd "$(dirname "$0")/.." && pwd)"
cd "$ROOT"

TOOLS="lftp php composer npm"
[[ -n "$SANS_VENDOR" ]] && TOOLS="lftp npm"
for tool in $TOOLS; do
    command -v "$tool" >/dev/null || { echo "✗ « $tool » est introuvable (macOS : brew install $tool)." >&2; exit 1; }
done
if [[ -z "$SANS_VENDOR" ]]; then
    php -r 'exit(version_compare(PHP_VERSION, "8.3.0", ">=") ? 0 : 1);' || {
        echo "✗ PHP $(php -r 'echo PHP_VERSION;') détecté : PHP 8.3 minimum est requis." >&2
        echo "  (ou relancez avec --sans-vendor pour ne pas utiliser PHP en local)" >&2
        exit 1
    }
fi

if [[ -n "$(git status --porcelain --untracked-files=no)" ]]; then
    echo "⚠ Vous avez des modifications non commitées : elles seront envoyées telles quelles."
    read -r -p "Continuer ? [o/N] " answer
    [[ "$answer" =~ ^[oOyY]$ ]] || exit 1
fi

if [[ -z "$DRY_RUN" ]]; then
    echo "⚠ La migration de cette version modifie le nom du site, les couleurs, le logo et"
    echo "  certains textes en base. Avez-vous exporté la base (cPanel › phpMyAdmin › Exporter) ?"
    read -r -p "Sauvegarde faite ? [o/N] " answer
    [[ "$answer" =~ ^[oOyY]$ ]] || { echo "Faites d'abord la sauvegarde, puis relancez."; exit 1; }
fi

# Le mot de passe passe par la variable LFTP_PASSWORD (--env-password) : il
# n'apparaît ni dans la ligne de commande ni dans l'historique.
read -r -s -p "Mot de passe FTP de ${FTP_USER} : " LFTP_PASSWORD
echo
export LFTP_PASSWORD
trap 'unset LFTP_PASSWORD' EXIT

# FTPS explicite (port 21) obligatoire : le mot de passe ne circule jamais en
# clair. Le certificat n'est pas vérifié, car l'hébergement mutualisé présente
# souvent le certificat de son serveur plutôt que celui de ce nom de domaine ;
# la connexion reste chiffrée.
lftp_session() {
    lftp --env-password -u "$FTP_USER" "$FTP_HOST" <<LFTP
set ftp:ssl-allow yes
set ftp:ssl-force yes
set ftp:ssl-protect-data yes
set ssl:verify-certificate no
set net:max-retries 3
set net:timeout 30
cd "$FTP_DIR"
$1
bye
LFTP
}

echo "→ Vérification du dossier distant (${FTP_HOST}:${FTP_DIR})…"
REMOTE_FILES="$(lftp_session 'cls -1')" || { echo "✗ Connexion FTP impossible (hôte, identifiant ou mot de passe ?)." >&2; exit 1; }
if ! grep -qx 'artisan' <<<"$REMOTE_FILES"; then
    echo "⚠ Aucun fichier « artisan » dans ce dossier distant. Contenu trouvé :"
    sed 's/^/    /' <<<"$REMOTE_FILES" | head -20
    echo "  Si l'application est dans un sous-dossier, relancez avec : FTP_DIR=nom-du-dossier $0"
    read -r -p "Envoyer quand même à cet endroit ? [o/N] " answer
    [[ "$answer" =~ ^[oOyY]$ ]] || exit 1
fi

if [[ -z "$SANS_VENDOR" ]]; then
    echo "→ Dépendances PHP de production…"
    composer install --no-dev --optimize-autoloader --no-interaction --no-progress
fi

echo "→ Compilation du front (Vite)…"
npm ci --ignore-scripts --no-audit --no-fund
npm run build

# Remet les outils de développement sur le poste, même en cas d'échec de l'envoi.
restore_dev() {
    if [[ -z "$SANS_VENDOR" ]]; then
        echo "→ Réinstallation des dépendances de développement sur le poste…"
        composer install --no-interaction --no-progress >/dev/null 2>&1 || true
    fi
    unset LFTP_PASSWORD
}
trap restore_dev EXIT

# Avec --sans-vendor, le serveur garde son propre vendor et ses caches de paquets
# (bootstrap/cache/packages.php et services.php), générés par Composer sur place.
EXTRA_EXCLUDES=""
[[ -n "$SANS_VENDOR" ]] && EXTRA_EXCLUDES="--exclude-glob vendor/ --exclude-glob bootstrap/cache/packages.php --exclude-glob bootstrap/cache/services.php"

echo "→ Envoi (seuls les fichiers modifiés)…"
lftp_session "mirror --reverse $COMPARE --no-perms --verbose=1 $DRY_RUN \
  --exclude-glob .git/ \
  --exclude-glob .github/ \
  --exclude-glob .vscode/ \
  --exclude-glob .idea/ \
  --exclude-glob .claude/ \
  --exclude-glob node_modules/ \
  --exclude-glob tests/ \
  --exclude-glob dist/ \
  --exclude-glob DESIGN-IS-*/ \
  --exclude-glob storage/ \
  --exclude-glob public/storage \
  --exclude-glob public/storage.zip \
  --exclude-glob public.zip \
  --exclude-glob public/hot \
  --exclude-glob .env \
  --exclude-glob .env.backup \
  --exclude-glob .DS_Store \
  --exclude-glob database/database.sqlite \
  --exclude-glob bootstrap/cache/config.php \
  --exclude-glob bootstrap/cache/routes-v7.php \
  --exclude-glob .phpunit.result.cache \
  --exclude-glob .phpunit.cache/ \
  $EXTRA_EXCLUDES \
  ./ ./"

echo
if [[ -n "$DRY_RUN" ]]; then
    echo "✓ Simulation terminée : rien n'a été envoyé."
else
    # Supprime les caches Laravel obsolètes laissés sur le serveur (config ou routes
    # mises en cache par un ancien « optimize ») : ils sont régénérés automatiquement.
    lftp_session 'rm -f bootstrap/cache/routes-v7.php bootstrap/cache/config.php bootstrap/cache/events.php' >/dev/null 2>&1 || true

    # storage/ n'est jamais envoyé : on s'assure seulement que l'arborescence
    # attendue par Laravel existe (sans toucher à son contenu).
    lftp_session 'mkdir -p storage/app/public storage/app/private storage/framework/cache/data storage/framework/sessions storage/framework/views storage/framework/testing storage/logs bootstrap/cache' >/dev/null 2>&1 || true

    if [[ -z "$SANS_VENDOR" ]]; then
        # Contrôle qu'un fichier clé du dossier vendor est bien arrivé en entier.
        CHECK="$(lftp_session 'cls -1 vendor/laravel/serializable-closure/src/Serializers' 2>/dev/null || true)"
        if ! grep -q 'Native.php$' <<<"$CHECK"; then
            echo "⚠ vendor/laravel/serializable-closure est incomplet sur le serveur."
            echo "  Relancez : $0 --full"
        fi
    else
        echo "  Si composer.json ou composer.lock ont changé, reconstruisez vendor sur le serveur :"
        echo "    php -d allow_url_fopen=On composer.phar install --no-dev --optimize-autoloader"
    fi

    echo "✓ Fichiers envoyés."
    echo "  À lancer maintenant dans le Terminal cPanel (dossier de l'application) :"
    echo "    php artisan migrate --force && php artisan optimize:clear"
    echo "  N'utilisez pas « php artisan optimize » : les routes du projet utilisent des closures."
fi
