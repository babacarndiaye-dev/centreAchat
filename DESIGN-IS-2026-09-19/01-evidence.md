# Evidence

Gathered by direct source inspection (orchestrator), not subagents — small enough surface (2 view files, 1 CSS file, 1 JS file, ~530 total lines) to read directly rather than fan out.

## Structural

- `resources/views/home.blade.php`: 278 lines, 8 top-level `<section>` blocks, virtually every element carries an inline `style="..."` attribute (hero: `home.blade.php:19-64` alone has 10+ inline styles). No shared section/card component beyond two CSS classes.
- Duplicate concept classes in the same stylesheet: `.section-title` / `.section-eyebrow` (Tailwind, `app.css:191-197`) vs `.section-title-uk` / `.section-eyebrow-uk` (UIkit, `app.css:141-157`) — same purpose, two implementations, only the `-uk` variants are used on the homepage.
- Two full CSS/JS frameworks loaded simultaneously and documented as an unfinished, in-progress migration: `app.css:3-9` — *"Migration to UIkit in progress: pages already converted use only `uk-*` classes... both frameworks are loaded together on purpose... Remove the @tailwind block once every view is converted."*
- Header/nav (`app.blade.php:46-116`) uses clean, tokenized Tailwind utility classes (`text-terroir-dark/80`, `bg-terroir-green`, etc.) — the one part of the UI already following the token system.
- Homepage content and footer (`home.blade.php` entire file; `app.blade.php:130-189`) instead use UIkit classes (`uk-container`, `uk-grid`, `uk-card`) layered with dozens of inline hardcoded hex/rgba values.

## Visual (read from source; INFERRED, no rendered screenshot)

- **Color tokens vs hardcoded colors**: `tailwind.config.js:17-23` defines `terroir.green/terracotta/gold` as `rgb(var(--terroir-green) / <alpha-value>)` — CSS custom properties injected server-side from admin `Setting` values (comment: *"admins can change the brand palette without a rebuild or code change"*, `tailwind.config.js:19-21`). Yet `home.blade.php` hardcodes raw values instead of these tokens in at least 20 places, e.g. `#1E7A4E`, `#0F3D28`, `#0B2E1F` (`home.blade.php:19`), `#F0A93B` (`:26,34,49`), `#1F2328` (`:27,34,49,101...`), `#5B6470` (`:79,137,233`), `#9CA3AF` (`:140,143,236`), `#E8604F` (`:139`), `#1D8A4E` (`:118,145,164...`). Same hardcoding recurs in `app.css:31-70` (the "UIkit reskin" layer) and the footer (`app.blade.php:130-189`).
- **Spacing**: no defined scale — observed px/rem values used ad hoc across the homepage: `96px` (near-universal section padding), plus `64px, 56px, 44px, 40px, 36px, 22px, 14px, 8px` used inconsistently for internal padding/gaps (`home.blade.php` throughout).
- **Type scale**: `2.75rem` (hero h1), `1.9rem` (section titles), `1.4rem`, `1.35rem`, `1.25rem`, `1.08rem`, `1.05rem`, `.9rem`, `.75rem`, `.72rem` — nine distinct sizes with no visible ratio/system (`home.blade.php` throughout; `.section-title-uk` at `app.css:150-157`).
- **Distinct colors**: 20+ unique hex/rgba values counted across `home.blade.php` and the `app.css` UIkit-reskin layer combined — well beyond a typical single-source palette, despite a working token system existing in parallel.
- **Iconography**: emoji used as functional UI icons — 🚚💳🔒💬 (trust badges, `home.blade.php:74-83`), 🍵🌾🥭🍲🎁🌿 (category icons, `:9-15,100`), 🏨🎁 (dedicated-space cards, `:165,173`) — vs. real inline SVG icons already used in the header for search/cart/menu (`app.blade.php:74,88,95-96`).
- **Motion/states checklist**: hover states present (`.uk-card-hover`, button lift, `app.css:83-107`) — present. `prefers-reduced-motion` explicitly handled for both the page-loader pulse (`app.css:247-252`) and the `.reveal` scroll-fade utility (`app.css:266-272`) — present and correctly implemented (a genuine strength, cite as Keep). Empty states — absent: every conditional section (`home.blade.php:90,110,130,184,202,221,247`) simply omits itself with an `@if`, no designed empty/fallback treatment. Loading/error states — not applicable to this static content page; not evaluated here.
- **Animation inventory on the page**: one autoplay UIkit slideshow in the hero (`home.blade.php:42`, 4.5s interval), one uniform scroll-reveal effect (`.reveal`, applied identically to all 8 sections), one loader pulse on first load, and CSS-transition hover lifts on cards/buttons. No other motion.

## Copy & Honesty

- No dark patterns found (no forced continuity, hidden costs, fake scarcity, confirmshaming).
- No inflated marketing superlatives found in body copy (copy is plain and descriptive, e.g. `home.blade.php:31,137,167`).
- One clear **label→behavior mismatch**: the codebase documents and implements a server-side admin-configurable color system (`tailwind.config.js:19-21`, `app.blade.php:21` calling `\App\Support\Theme::cssVariables()`) but the homepage — the highest-traffic page — bypasses it almost entirely via hardcoded hex values (see Visual section above). An admin changing the brand color in Réglages > Design would see the header update but the homepage and footer stay the old color. This is the standout finding for principle #6.

## Weight & Friction

- Production build output (`npm run build`, measured this session): `app-*.css` 331KB (39.5KB gzip), `app-*.js` 331.6KB (114.2KB gzip), `analytics-*.js` 177.8KB (61.9KB gzip). Combined gzipped JS+CSS ≈ 216KB for framework/site chrome alone, before content images.
- Render-blocking external `@import url(fonts.googleapis.com...)` at `app.css:1` (Fraunces + Figtree, multiple weights) — not preloaded/self-hosted.
- Two full UI frameworks (Tailwind + UIkit) shipped together by design, per `app.css:3-9` comment — direct, self-admitted redundant weight.
- Motion is gated behind `prefers-reduced-motion` for the two motion mechanisms that matter most (loader, reveal) — a mitigating factor for principle #9.

## Known gaps

- No rendered screenshot / computed-style / contrast-ratio measurement was taken (no browser automation tool available this session); color-on-color contrast risk (e.g. `rgba(255,255,255,.7)` text on `#1D8A4E` backgrounds, `home.blade.php:210,213`; `opacity:.5` footer legal line on `#1F2328`, `app.blade.php:184`) is flagged as a risk from source inspection only, not measured.
- Accessibility subagent (landmarks, focus order, keyboard reachability) was not deployed — homepage is largely non-interactive besides links/forms already using native elements; deprioritized given scope and effort budget for this request.
- Other site pages (products, checkout, admin) were not audited — scope was locked to homepage + layout per the user's stated complaint ("notre design classique").
