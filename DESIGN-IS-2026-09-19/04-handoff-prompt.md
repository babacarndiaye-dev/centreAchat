/make-plan Redesign the Central d'Achat homepage and shared layout (resources/views/home.blade.php, resources/views/layouts/app.blade.php, resources/css/app.css). Current design failed audit at 14/30 with critical gaps in principles #3 (aesthetic), #6 (honest), #7 (long-lasting), #8 (thorough), #10 (as little design as possible).

Verdict paragraph (quoted from the Dieter Rams audit):
> Total score 14/30 (below the REFINE threshold of 20) — the homepage runs two complete, self-admittedly unfinished styling systems side by side (Tailwind, tokenized and correct, vs. UIkit, hardcoded and untokenized), which alone drags down aesthetic (#3), honest (#6), long-lasting (#7), thorough (#8), and as-little-as-possible (#10); this is a systemic split, not a handful of cosmetic nits a refine pass could patch.

Why redesign and not refine: no principle scored 0, but the total is well under 20, and the root cause — two parallel, incompletely-migrated CSS/JS frameworks with duplicate component classes and a bypassed color-token system — is structural, not cosmetic.

Preserve from current design:
- The `terroir-green` / `terroir-terracotta` / `terroir-gold` brand color tokens and the admin-configurable theming mechanism (`App\Support\Theme::cssVariables()`, wired at `resources/views/layouts/app.blade.php:21`, defined in `tailwind.config.js:17-23`) — the architecture is correct, it just needs to be the ONLY color source used, everywhere.
- The Fraunces (display) + Figtree (body) font pairing (`tailwind.config.js:12-15`).
- The already-tokenized header/nav/mobile-menu (`resources/views/layouts/app.blade.php:46-116`) — this is the one part of the current UI that already does it right; use it as the reference standard for the rest of the redesign, not something to also redo.
- The `prefers-reduced-motion` handling already correctly implemented for the page-loader pulse and scroll-reveal (`resources/css/app.css:247-252` and `:266-272`) — carry this discipline into every new motion pattern added.

Discard:
- UIkit entirely (`uk-container`, `uk-grid`, `uk-card`, `uk-slideshow`, `uk-button` classes and the `@import "uikit/dist/css/uikit.min.css"` at `resources/css/app.css:10`). Evidence: `resources/css/app.css:3-9` self-documents this as an unfinished, in-progress migration running two frameworks at once. Caused failure on #10 and #3.
- All inline hardcoded hex/rgba color values in `resources/views/home.blade.php` (20+ instances, e.g. `#1E7A4E`, `#F0A93B`, `#1F2328`, `#5B6470`, `#9CA3AF`, `#E8604F`) and in the "UIkit reskin" layer of `resources/css/app.css:31-70`. Evidence: these bypass the working admin-configurable token system. Caused failure on #6 and #3.
- The duplicate `.section-title-uk` / `.section-eyebrow-uk` classes (`resources/css/app.css:141-157`) once UIkit is dropped — keep only the Tailwind `.section-title` / `.section-eyebrow` pair (`resources/css/app.css:191-197`).
- Emoji used as functional UI icons (🚚💳🔒💬🏨🎁🌿🍵🌾🥭🍲) throughout `resources/views/home.blade.php`. Caused failure on #7.

Top 5 moves from the audit (verbatim):
1. [#10, #3] Unify on one framework — drop UIkit, standardize on the already-tokenized Tailwind system. Evidence: `app.css:3-9`, duplicate section classes at `app.css:191-197` vs `app.css:141-157`.
2. [#6, #3] Route every color through the existing `terroir-*` tokens instead of hardcoded hex/rgba. Evidence: 20+ hardcoded values in `home.blade.php` and `app.css:31-70` vs. the working token system in `tailwind.config.js:17-23`.
3. [#7, #3] Replace emoji-as-icon with the real inline-SVG icon pattern already used in the header. Evidence: emoji at `home.blade.php:74-83,100,165,173` vs. proper SVG icons at `app.blade.php:74,88,95-96`.
4. [#7, #8] Design real, restrained motion beyond the single uniform scroll-reveal (`.reveal` applied identically to all 8 sections). Must preserve the existing `prefers-reduced-motion` handling.
5. [#3, #8] Define and apply one spacing scale and one type scale, replacing the current ad hoc values (96/64/56/44/40/36/22/14/8px and nine unrelated font sizes).

Redesign principles in priority order:
1. #3 Aesthetic — one visible system (one framework, one color source, one spacing/type scale) with no orphan styles.
2. #7 Long-lasting — a durable icon and motion language that won't read as a specific year's trend.
3. #6 Honest — every color on screen must trace back to the admin-configurable tokens, with no exceptions, so the "change branding without a rebuild" promise actually holds site-wide.

Deliverables for the plan:
- New homepage information architecture (keep the existing section order — hero, trust badges, categories, featured products, promo, B2B/gift spaces, new arrivals, producer spotlight, testimonials, newsletter, location — this content structure is not in question, only its visual execution)
- New primary flow wireframe (low-fi, labeled) for the hero + first two sections, compared side-by-side to current
- Token decisions: single spacing scale, single type scale, confirmation that every color reference is a `terroir-*` class (zero hardcoded hex remaining)
- States checklist: empty states for categories/featured-products/testimonials/producer sections (currently silently omitted), hover/focus states, motion states (respecting prefers-reduced-motion)
- Icon inventory: one SVG icon per current emoji use, sourced or drawn consistently
- Migration path: this is a visual/CSS-layer redesign only, no route/URL/data changes, so no user migration path is needed — cutover is a single deploy once the new homepage/layout render correctly locally
- Cutover criteria: old UIkit-based styling fully removed from `app.css` and `home.blade.php`/`app.blade.php` (not left behind a flag), homepage visually reviewed by the user before production deploy

Anti-patterns to guard against:
- Porting the old inline-style structure under new class names (must actually move to token-based utility classes, not just rename)
- Keeping UIkit "just for the slideshow" or any single component — replace it fully, including the carousel
- Redesigning to chase a trend rather than the principles above (durable > fashionable, per #7)
- Treating the Preserve list as optional — the brand tokens, font pairing, existing header, and reduced-motion handling must all survive this redesign unchanged
