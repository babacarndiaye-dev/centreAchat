# Scorecard

1. Good design is innovative — Score: 1/3
   Evidence: standard e-commerce/marketing layout (hero, categories, featured, testimonials, newsletter — 01-evidence.md Structural).
   Justification: competent, conventional assembly of well-known patterns; no meaningful advance, but not a wholesale competitor copy either.

2. Good design makes a product useful — Score: 2/3
   Evidence: clear nav to produits/producteurs/contact, direct hero CTA to products, search and cart always reachable (01-evidence.md Structural; app.blade.php:62-92).
   Justification: primary task (browse → buy) completes with no decoys, but the stacked 96px-padded sections and 216KB gzipped chrome add friction before the task starts — not the leanest path.

3. Good design is aesthetic — Score: 1/3
   Evidence: self-documented unfinished two-framework migration (app.css:3-9), 20+ hardcoded colors alongside an unused token system, nine ad hoc type sizes (01-evidence.md Visual).
   Justification: real craft exists in isolated spots (hover transitions, font pairing) but there is no single visible system across the audited surface — a 3-5-inconsistency pattern, tipping to the "no clear system" end given the self-admitted split.

4. Good design makes a product understandable — Score: 2/3
   Evidence: nav labels are plain French nouns, CTAs are verb-led ("Découvrir nos produits →"), trust badges pair icon+text (01-evidence.md Visual/Structural).
   Justification: a first-time user names every primary control correctly; minor softness only in emoji-only category icons needing the adjacent label to disambiguate.

5. Good design is unobtrusive — Score: 2/3
   Evidence: sticky header is a quiet blurred bar, not a decorative object; decorative radial-gradient blobs appear in hero/producer sections (home.blade.php:19-21,205) but are restrained and low-opacity.
   Justification: chrome stays quiet; a couple of decorative flourishes are present but do not compete with content.

6. Good design is honest — Score: 1/3
   Evidence: admin-configurable color system exists and is documented (tailwind.config.js:19-21) but is bypassed by hardcoded hex on the homepage and footer (01-evidence.md Copy & Honesty).
   Justification: a real, systemic promise-to-behavior mismatch (2+ instances, same root cause) toward the site administrator as a user of the system; no customer-facing dark pattern.

7. Good design is long-lasting — Score: 1/3
   Evidence: emoji-as-icon system throughout content sections (🚚💳🔒💬🏨🎁), generic gradient-blob decoration, unresolved framework migration (01-evidence.md Visual/Structural).
   Justification: multiple dated/trend-driven markers (2-3) that will read as of-their-moment rather than a durable identity.

8. Good design is thorough down to the last detail — Score: 1/3
   Evidence: prefers-reduced-motion correctly handled twice (app.css:247-252, 266-272) — a genuine strength; but empty states are undesigned (sections just vanish, home.blade.php:90,110,130,184,202,221,247) and the framework/class duplication itself is an unfinished detail at the system level.
   Justification: one clear area of care (motion accessibility) is outweighed by multiple unconsidered states/edges (empty states, duplicate classes, unresolved migration) — 2-3 gaps.

9. Good design is environmentally friendly — Score: 2/3
   Evidence: ≈216KB combined gzipped JS+CSS (measured build output), motion gated behind prefers-reduced-motion for the two motion mechanisms present (01-evidence.md Weight & Friction).
   Justification: under 500KB and motion is gated — solidly in the "2" band; the redundant second framework keeps it from reaching 3.

10. Good design is as little design as possible — Score: 1/3
    Evidence: two full CSS/JS frameworks loaded together by explicit, documented design (app.css:3-9); duplicate `.section-title`/`.section-eyebrow` vs `.section-title-uk`/`.section-eyebrow-uk` classes (app.css:141-157 vs 191-197); hardcoded colors duplicating an existing token system.
    Justification: multiple systemic, named redundancies (framework, component classes, color source of truth) — well past the "≤2 removable elements" band for a 2.

**Total: 14/30**
