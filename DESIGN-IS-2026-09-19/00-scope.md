# Scope

**Audited:** Homepage (`resources/views/home.blade.php`) and main layout (`resources/views/layouts/app.blade.php`), styled by `resources/css/app.css`, `tailwind.config.js`, behavior in `resources/js/app.js`. Repo: `/Applications/XAMPP/xamppfiles/htdocs/Centre achat` (Laravel 13, Blade views, Tailwind + UIkit + Alpine.js).

**Primary user:** A consumer, hotel/restaurant buyer, or tourist in Senegal browsing/buying local "terroir" products online.

**Primary task:** Understand what Central d'Achat sells, browse products/producers, and reach checkout or contact with minimal friction.

**Constraints:** Existing brand tokens (`terroir-green`, `terroir-terracotta`, `terroir-gold`, Fraunces/Figtree fonts) are admin-configurable via `App\Support\Theme::cssVariables()` and must remain configurable. Stack is Laravel/Blade (server-rendered, full page loads), no SPA framework. No hard deadline given.

**Reference designs / competitors:** None supplied by user.

**Trigger for this audit:** User request — "je veux un design plus attrayant et moderne professionnel je trouve notre design classique sa doit etre dynamique" (wants a more modern, professional, dynamic design; finds current design too classic/static).

**Evidence method:** No running browser-screenshot tool available in this session; evidence gathered by direct source reading (marked INFERRED where visual rendering wasn't observed directly, though the local dev server at 127.0.0.1:8123 was reachable via curl for basic checks).
