# Verdict: REDESIGN

Total score 14/30 (below the REFINE threshold of 20) — the homepage runs two complete, self-admittedly unfinished styling systems side by side (Tailwind, tokenized and correct, vs. UIkit, hardcoded and untokenized), which alone drags down aesthetic (#3), honest (#6), long-lasting (#7), thorough (#8), and as-little-as-possible (#10); this is a systemic split, not a handful of cosmetic nits a refine pass could patch.

## Why REDESIGN and not REFINE
No principle scored 0, but the total (14) is well under 20, and the root cause — two parallel, incompletely-migrated frameworks with duplicate component classes and a bypassed color-token system — is structural, not cosmetic. Patching individual colors or adding a few animations on top of the current split would leave the underlying inconsistency in place and likely regress again on the next content change.

## Top 5 highest-leverage moves

1. **[#10, #3] Unify on one framework — drop UIkit, standardize on the already-tokenized Tailwind system.** Evidence: `app.css:3-9` (self-documented unfinished migration), duplicate `.section-title`/`.section-eyebrow` (`app.css:191-197`) vs `.section-title-uk`/`.section-eyebrow-uk` (`app.css:141-157`).
2. **[#6, #3] Route every color through the existing `terroir-*` tokens.** Evidence: 20+ hardcoded hex/rgba values in `home.blade.php` and `app.css:31-70`, vs. the working admin-configurable token system defined in `tailwind.config.js:17-23` and wired in `app.blade.php:21`.
3. **[#7, #3] Replace emoji-as-icon with the real SVG icon pattern already used in the header.** Evidence: 🚚💳🔒💬🏨🎁 in `home.blade.php:74-83,100,165,173` vs. proper inline SVG icons at `app.blade.php:74,88,95-96`.
4. **[#7, #8] Design real, restrained motion beyond the single uniform scroll-reveal.** Evidence: only `.reveal` (`app.css:254-273`) applied identically across all 8 sections, plus one autoplay slideshow and hover lifts (`01-evidence.md` Visual) — this uniformity is exactly what reads as "classic/static" to the user. Must preserve the existing `prefers-reduced-motion` handling (`app.css:247-252,266-272`) while adding this.
5. **[#3, #8] Define and apply one spacing scale and one type scale.** Evidence: ad hoc spacing (`96/64/56/44/40/36/22/14/8px`) and nine unrelated type sizes observed throughout `home.blade.php`, with no visible ratio or system.
