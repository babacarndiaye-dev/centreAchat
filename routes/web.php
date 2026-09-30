<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\Admin\AnalyticsController as AdminAnalyticsController;
use App\Http\Controllers\Admin\B2bController as AdminB2bController;
use App\Http\Controllers\Admin\BankReconciliationController as AdminBankReconciliationController;
use App\Http\Controllers\Admin\CashRegisterController as AdminCashRegisterController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\ChartAccountController as AdminChartAccountController;
use App\Http\Controllers\Admin\ContactMessageController as AdminContactMessageController;
use App\Http\Controllers\Admin\ConversationController as AdminConversationController;
use App\Http\Controllers\Admin\PackagingTypeController as AdminPackagingTypeController;
use App\Http\Controllers\Admin\ProductAttributeController as AdminProductAttributeController;
use App\Http\Controllers\Admin\UnitController as AdminUnitController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\DeliveryZoneController as AdminDeliveryZoneController;
use App\Http\Controllers\Admin\TrafficController as AdminTrafficController;
use App\Http\Controllers\Admin\ExpenseCategoryController as AdminExpenseCategoryController;
use App\Http\Controllers\Admin\ExpenseController as AdminExpenseController;
use App\Http\Controllers\Admin\FaqController as AdminFaqController;
use App\Http\Controllers\Admin\FinanceDashboardController as AdminFinanceDashboardController;
use App\Http\Controllers\Admin\FixedAssetController as AdminFixedAssetController;
use App\Http\Controllers\Admin\JournalController as AdminJournalController;
use App\Http\Controllers\Admin\JournalEntryController as AdminJournalEntryController;
use App\Http\Controllers\Admin\LedgerController as AdminLedgerController;
use App\Http\Controllers\Admin\NewsletterController as AdminNewsletterController;
use App\Http\Controllers\Admin\NotificationController as AdminNotificationController;
use App\Http\Controllers\Admin\NotificationTemplateController as AdminNotificationTemplateController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\PageController as AdminPageController;
use App\Http\Controllers\Admin\PaymentAccountController as AdminPaymentAccountController;
use App\Http\Controllers\Admin\PaymentMethodController as AdminPaymentMethodController;
use App\Http\Controllers\Admin\PosReturnController as AdminPosReturnController;
use App\Http\Controllers\Admin\PosSaleController as AdminPosSaleController;
use App\Http\Controllers\Admin\PostController as AdminPostController;
use App\Http\Controllers\Admin\ProducerController as AdminProducerController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\PurchaseOrderController as AdminPurchaseOrderController;
use App\Http\Controllers\Admin\PurchaseReceptionController as AdminPurchaseReceptionController;
use App\Http\Controllers\Admin\PurchaseRequestController as AdminPurchaseRequestController;
use App\Http\Controllers\Admin\QuoteController as AdminQuoteController;
use App\Http\Controllers\Admin\RecurringOrderController as AdminRecurringOrderController;
use App\Http\Controllers\Admin\RoleController as AdminRoleController;
use App\Http\Controllers\Admin\SettingController as AdminSettingController;
use App\Http\Controllers\Admin\StaffUserController as AdminStaffUserController;
use App\Http\Controllers\Admin\TaxRateController as AdminTaxRateController;
use App\Http\Controllers\Admin\SupplierAccessController as AdminSupplierAccessController;
use App\Http\Controllers\Admin\SupplierController as AdminSupplierController;
use App\Http\Controllers\Admin\SupplierPaymentController as AdminSupplierPaymentController;
use App\Http\Controllers\Admin\SupplierProductController as AdminSupplierProductController;
use App\Http\Controllers\Admin\TestimonialController as AdminTestimonialController;
use App\Http\Controllers\Admin\ProductReviewController as AdminProductReviewController;
use App\Http\Controllers\Admin\TrialBalanceController as AdminTrialBalanceController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\NewsletterController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\PushSubscriptionController;
use App\Http\Controllers\Portal\DashboardController as PortalDashboardController;
use App\Http\Controllers\Portal\PurchaseOrderController as PortalPurchaseOrderController;
use App\Http\Controllers\ProducerController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\WishlistController;
use App\Http\Controllers\QuoteController;
use App\Http\Controllers\RecurringOrderController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('accueil');

// Catalogue
Route::get('/produits', [ProductController::class, 'index'])->name('produits.index');
Route::get('/produits/{slug}', [ProductController::class, 'show'])->name('produits.show');
Route::post('/produits/{product}/avis', [ReviewController::class, 'store'])->middleware('auth')->name('produits.avis.store');
Route::post('/produits/{product}/favori', [WishlistController::class, 'toggle'])->middleware('auth')->name('produits.favori.toggle');

// Producteurs
Route::get('/nos-producteurs', [ProducerController::class, 'index'])->name('producteurs.index');
Route::get('/nos-producteurs/{slug}', [ProducerController::class, 'show'])->name('producteurs.show');

// Blog / Actualités / Recettes
Route::get('/actualites', [BlogController::class, 'index'])->name('blog.index');
Route::get('/actualites/{slug}', [BlogController::class, 'show'])->name('blog.show');

// Panier
Route::prefix('panier')->name('panier.')->group(function () {
    Route::get('/', [CartController::class, 'index'])->name('index');
    Route::post('/ajouter/{product}', [CartController::class, 'add'])->name('add');
    Route::patch('/modifier/{product}', [CartController::class, 'update'])->name('update');
    Route::delete('/retirer/{product}', [CartController::class, 'remove'])->name('remove');
});

// Commande / Checkout
Route::prefix('commande')->name('commande.')->group(function () {
    Route::get('/', [CheckoutController::class, 'index'])->name('index');
    Route::post('/', [CheckoutController::class, 'store'])->name('store');
    Route::get('/confirmation/{orderNumber}', [CheckoutController::class, 'confirmation'])->name('confirmation');
});

// PWA — page de repli hors ligne (mise en cache par le service worker)
Route::get('/hors-ligne', fn () => view('offline'))->name('pwa.offline');

// PWA manifest — dynamic so it follows the site's branding settings
Route::get('/manifest.webmanifest', function () {
    $siteName = \App\Models\Setting::get('site_name') ?: 'DIABA HOTEL';

    return response()->json([
        'name' => $siteName,
        'short_name' => strlen($siteName) <= 15 ? $siteName : \Illuminate\Support\Str::limit($siteName, 12),
        'description' => "Produits locaux du Sénégal — boutique, professionnels et fournisseurs.",
        'start_url' => '/?source=pwa',
        'scope' => '/',
        'display' => 'standalone',
        'orientation' => 'portrait-primary',
        'background_color' => '#F0F0E8',
        'theme_color' => '#101818',
        'icons' => [
            ['src' => '/icons/icon-192.png', 'sizes' => '192x192', 'type' => 'image/png', 'purpose' => 'any'],
            ['src' => '/icons/icon-512.png', 'sizes' => '512x512', 'type' => 'image/png', 'purpose' => 'any'],
            ['src' => '/icons/icon-maskable-192.png', 'sizes' => '192x192', 'type' => 'image/png', 'purpose' => 'maskable'],
            ['src' => '/icons/icon-maskable-512.png', 'sizes' => '512x512', 'type' => 'image/png', 'purpose' => 'maskable'],
        ],
    ])->header('Content-Type', 'application/manifest+json');
})->name('pwa.manifest');

// Sert les fichiers publics (photos produits, logo...) directement via PHP, sans
// dépendre d'un lien symbolique `public/storage` — certains hébergements mutualisés
// bloquent ce préfixe d'URL (FollowSymLinks désactivé, ou règle de sécurité dédiée).
Route::get('/fichiers/{path}', function (string $path) {
    $base = storage_path('app/public');
    $fullPath = realpath($base.'/'.$path);

    abort_unless($fullPath && str_starts_with($fullPath, $base) && is_file($fullPath), 404);

    return response()->file($fullPath, ['Cache-Control' => 'public, max-age=604800']);
})->where('path', '.*')->name('fichiers.fallback');

// Contact & Newsletter
Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');
Route::post('/newsletter', [NewsletterController::class, 'store'])->name('newsletter.store');

// Chat en direct (widget public)
Route::get('/chat/etat', [ChatController::class, 'state'])->name('chat.state');
Route::post('/chat/envoyer', [ChatController::class, 'send'])->name('chat.send');
Route::post('/chat/transfert', [ChatController::class, 'transfer'])->name('chat.transfer');

// Notifications push (PWA)
Route::post('/push/abonnement', [PushSubscriptionController::class, 'store'])->name('push.subscribe');
Route::delete('/push/abonnement', [PushSubscriptionController::class, 'destroy'])->name('push.unsubscribe');

// Pages statiques (CMS)
Route::get('/{slug}', [PageController::class, 'show'])->name('pages.show')->where('slug', 'a-propos|notre-histoire|notre-mission|nos-engagements|hotels-professionnels|espace-touristes|coffrets-cadeaux|faq|livraison|devenir-fournisseur|contact|mentions-legales|politique-de-confidentialite|conditions-generales');

// Authentification
Route::middleware('guest')->group(function () {
    Route::get('/connexion', [LoginController::class, 'show'])->name('login');
    Route::post('/connexion', [LoginController::class, 'login']);
    Route::get('/inscription', [RegisterController::class, 'show'])->name('register');
    Route::post('/inscription', [RegisterController::class, 'register']);
});
Route::post('/deconnexion', [LoginController::class, 'logout'])->middleware('auth')->name('logout');

// Espace client
Route::middleware('auth')->prefix('mon-compte')->name('compte.')->group(function () {
    Route::get('/', [AccountController::class, 'index'])->name('index');

    Route::get('messages', [ChatController::class, 'history'])->name('messages.index');
    Route::get('messages/{conversation}', [ChatController::class, 'historyShow'])->name('messages.show');

    Route::get('favoris', [WishlistController::class, 'index'])->name('favoris.index');

    Route::middleware('b2b')->group(function () {
        Route::get('devis', [QuoteController::class, 'index'])->name('devis.index');
        Route::get('devis/creer', [QuoteController::class, 'create'])->name('devis.create');
        Route::post('devis', [QuoteController::class, 'store'])->name('devis.store');
        Route::get('devis/{devis}', [QuoteController::class, 'show'])->name('devis.show');
        Route::patch('devis/{devis}/accepter', [QuoteController::class, 'accept'])->name('devis.accept');
        Route::patch('devis/{devis}/refuser', [QuoteController::class, 'refuse'])->name('devis.refuse');

        Route::get('commandes-recurrentes', [RecurringOrderController::class, 'index'])->name('commandes-recurrentes.index');
        Route::get('commandes-recurrentes/creer', [RecurringOrderController::class, 'create'])->name('commandes-recurrentes.create');
        Route::post('commandes-recurrentes', [RecurringOrderController::class, 'store'])->name('commandes-recurrentes.store');
        Route::patch('commandes-recurrentes/{commandeRecurrente}/basculer', [RecurringOrderController::class, 'toggle'])->name('commandes-recurrentes.toggle');
        Route::delete('commandes-recurrentes/{commandeRecurrente}', [RecurringOrderController::class, 'destroy'])->name('commandes-recurrentes.destroy');
    });
});

// Portail fournisseurs
Route::middleware(['auth', 'supplier'])->prefix('portail-fournisseur')->name('portail.')->group(function () {
    Route::get('/', [PortalDashboardController::class, 'index'])->name('dashboard');

    Route::get('commandes', [PortalPurchaseOrderController::class, 'index'])->name('commandes.index');
    Route::get('commandes/{purchaseOrder}', [PortalPurchaseOrderController::class, 'show'])->name('commandes.show');
    Route::patch('commandes/{purchaseOrder}/confirmer', [PortalPurchaseOrderController::class, 'confirm'])->name('commandes.confirm');
    Route::get('commandes/{purchaseOrder}/document', [PortalPurchaseOrderController::class, 'document'])->name('commandes.document');
});

// Administration
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('statistiques', [AdminAnalyticsController::class, 'index'])
        ->middleware('permission:analytics.voir')->name('analytics.index');

    Route::middleware('permission:trafic.voir')->prefix('trafic')->name('trafic.')->group(function () {
        Route::get('/', [AdminTrafficController::class, 'index'])->name('index');
        Route::get('/en-direct', [AdminTrafficController::class, 'live'])->name('live');
    });

    Route::middleware('permission:produits.voir,stock.voir')->group(function () {
        Route::resource('categories', AdminCategoryController::class)->except('show');
        Route::resource('producteurs', AdminProducerController::class)->except('show')->parameters(['producteurs' => 'producer']);
        Route::resource('produits', AdminProductController::class)->except('show')->parameters(['produits' => 'product']);
        Route::delete('produits/images/{image}', [AdminProductController::class, 'deleteImage'])->name('produits.images.destroy');

        Route::prefix('produits-parametres')->name('produits-parametres.')->group(function () {
            Route::get('unites', [AdminUnitController::class, 'index'])->name('unites.index');
            Route::post('unites', [AdminUnitController::class, 'store'])->name('unites.store');
            Route::patch('unites/{unit}', [AdminUnitController::class, 'update'])->name('unites.update');
            Route::delete('unites/{unit}', [AdminUnitController::class, 'destroy'])->name('unites.destroy');

            Route::get('conditionnements', [AdminPackagingTypeController::class, 'index'])->name('conditionnements.index');
            Route::post('conditionnements', [AdminPackagingTypeController::class, 'store'])->name('conditionnements.store');
            Route::patch('conditionnements/{packagingType}', [AdminPackagingTypeController::class, 'update'])->name('conditionnements.update');
            Route::delete('conditionnements/{packagingType}', [AdminPackagingTypeController::class, 'destroy'])->name('conditionnements.destroy');

            Route::get('attributs', [AdminProductAttributeController::class, 'index'])->name('attributs.index');
            Route::post('attributs', [AdminProductAttributeController::class, 'store'])->name('attributs.store');
            Route::patch('attributs/{productAttribute}', [AdminProductAttributeController::class, 'update'])->name('attributs.update');
            Route::delete('attributs/{productAttribute}', [AdminProductAttributeController::class, 'destroy'])->name('attributs.destroy');
        });
    });

    Route::middleware('permission:fournisseurs.voir')->group(function () {
        Route::resource('fournisseurs', AdminSupplierController::class);
        Route::post('fournisseurs/{supplier}/produits', [AdminSupplierProductController::class, 'store'])->name('fournisseurs.produits.store');
        Route::delete('fournisseurs/produits/{supplierProduct}', [AdminSupplierProductController::class, 'destroy'])->name('fournisseurs.produits.destroy');
        Route::post('fournisseurs/{supplier}/paiements', [AdminSupplierPaymentController::class, 'store'])->name('fournisseurs.paiements.store');
        Route::post('fournisseurs/{supplier}/acces', [AdminSupplierAccessController::class, 'store'])->name('fournisseurs.acces.store');
        Route::delete('fournisseurs/{supplier}/acces', [AdminSupplierAccessController::class, 'destroy'])->name('fournisseurs.acces.destroy');

        Route::get('demandes-achat', [AdminPurchaseRequestController::class, 'index'])->name('demandes-achat.index');
        Route::get('demandes-achat/creer', [AdminPurchaseRequestController::class, 'create'])->name('demandes-achat.create');
        Route::post('demandes-achat', [AdminPurchaseRequestController::class, 'store'])->name('demandes-achat.store');
        Route::get('demandes-achat/{demandeAchat}', [AdminPurchaseRequestController::class, 'show'])->name('demandes-achat.show');
        Route::patch('demandes-achat/{demandeAchat}/soumettre', [AdminPurchaseRequestController::class, 'submit'])->name('demandes-achat.submit');
        Route::patch('demandes-achat/{demandeAchat}/valider', [AdminPurchaseRequestController::class, 'validateRequest'])
            ->middleware('permission:fournisseurs.valider')->name('demandes-achat.validate');
        Route::patch('demandes-achat/{demandeAchat}/rejeter', [AdminPurchaseRequestController::class, 'reject'])
            ->middleware('permission:fournisseurs.valider')->name('demandes-achat.reject');
        Route::delete('demandes-achat/{demandeAchat}', [AdminPurchaseRequestController::class, 'destroy'])->name('demandes-achat.destroy');

        Route::get('bons-commande', [AdminPurchaseOrderController::class, 'index'])->name('bons-commande.index');
        Route::get('bons-commande/creer', [AdminPurchaseOrderController::class, 'create'])->name('bons-commande.create');
        Route::post('bons-commande', [AdminPurchaseOrderController::class, 'store'])->name('bons-commande.store');
        Route::get('bons-commande/{bonCommande}', [AdminPurchaseOrderController::class, 'show'])->name('bons-commande.show');
        Route::patch('bons-commande/{bonCommande}/statut', [AdminPurchaseOrderController::class, 'updateStatus'])->name('bons-commande.status');
        Route::post('bons-commande/{purchaseOrder}/receptions', [AdminPurchaseReceptionController::class, 'store'])->name('bons-commande.receptions.store');
    });

    Route::middleware('permission:commandes.voir')->group(function () {
        Route::get('commandes', [AdminOrderController::class, 'index'])->name('commandes.index');
        Route::get('commandes/{order}', [AdminOrderController::class, 'show'])->name('commandes.show');
        Route::patch('commandes/{order}/statut', [AdminOrderController::class, 'updateStatus'])
            ->middleware('permission:commandes.modifier')->name('commandes.status');
        Route::post('commandes/{order}/paiement', [AdminOrderController::class, 'recordPayment'])
            ->middleware('permission:commandes.modifier')->name('commandes.payment');
        Route::get('commandes/{order}/suggestion-ia', [AdminOrderController::class, 'suggestion'])->name('commandes.suggestion');
    });

    Route::middleware('permission:contenu.voir')->group(function () {
        Route::resource('pages', AdminPageController::class)->except('show');
        Route::resource('articles', AdminPostController::class)->except('show')->parameters(['articles' => 'post']);
        Route::resource('avis', AdminTestimonialController::class)->except('show')->parameters(['avis' => 'testimonial']);

        Route::get('avis-produits', [AdminProductReviewController::class, 'index'])->name('avis-produits.index');
        Route::patch('avis-produits/{avisProduit}/basculer', [AdminProductReviewController::class, 'toggle'])->name('avis-produits.toggle');
        Route::delete('avis-produits/{avisProduit}', [AdminProductReviewController::class, 'destroy'])->name('avis-produits.destroy');

        Route::get('messages', [AdminContactMessageController::class, 'index'])->name('messages.index');
        Route::get('messages/{message}', [AdminContactMessageController::class, 'show'])->name('messages.show');
        Route::post('messages/{message}/repondre', [AdminContactMessageController::class, 'reply'])->name('messages.reply');
        Route::delete('messages/{message}', [AdminContactMessageController::class, 'destroy'])->name('messages.destroy');

        Route::get('newsletter', [AdminNewsletterController::class, 'index'])->name('newsletter.index');
    });

    Route::get('parametres', [AdminSettingController::class, 'edit'])->name('parametres.edit');
    Route::patch('parametres', [AdminSettingController::class, 'update'])
        ->middleware('permission:systeme.modifier')
        ->name('parametres.update');

    Route::resource('roles', AdminRoleController::class)->except('show')
        ->middleware('permission:systeme.modifier')
        ->parameters(['roles' => 'role']);

    Route::resource('utilisateurs', AdminStaffUserController::class)->except('show')
        ->middleware('permission:systeme.modifier')
        ->parameters(['utilisateurs' => 'utilisateur']);

    Route::prefix('messagerie')->name('messagerie.')->middleware('permission:messagerie.voir')->group(function () {
        Route::get('/', [AdminConversationController::class, 'index'])->name('index');

        Route::prefix('faq')->name('faq.')->group(function () {
            Route::get('/', [AdminFaqController::class, 'index'])->name('index');
            Route::get('creer', [AdminFaqController::class, 'create'])
                ->middleware('permission:messagerie.creer')->name('create');
            Route::post('/', [AdminFaqController::class, 'store'])
                ->middleware('permission:messagerie.creer')->name('store');
            Route::get('{faq}/modifier', [AdminFaqController::class, 'edit'])
                ->middleware('permission:messagerie.modifier')->name('edit');
            Route::patch('{faq}', [AdminFaqController::class, 'update'])
                ->middleware('permission:messagerie.modifier')->name('update');
            Route::delete('{faq}', [AdminFaqController::class, 'destroy'])
                ->middleware('permission:messagerie.modifier')->name('destroy');
        });

        Route::get('{conversation}', [AdminConversationController::class, 'show'])->name('show');
        Route::get('{conversation}/messages', [AdminConversationController::class, 'messages'])->name('messages');
        Route::post('{conversation}/repondre', [AdminConversationController::class, 'reply'])
            ->middleware('permission:messagerie.creer')->name('reply');
        Route::patch('{conversation}/assigner', [AdminConversationController::class, 'assign'])
            ->middleware('permission:messagerie.modifier')->name('assign');
        Route::patch('{conversation}/fermer', [AdminConversationController::class, 'close'])
            ->middleware('permission:messagerie.modifier')->name('close');
        Route::patch('{conversation}/reouvrir', [AdminConversationController::class, 'reopen'])
            ->middleware('permission:messagerie.modifier')->name('reopen');
    });

    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [AdminNotificationController::class, 'index'])->name('index');
        Route::get('/en-direct', [AdminNotificationController::class, 'live'])->name('live');
        Route::patch('{notification}/lu', [AdminNotificationController::class, 'markAsRead'])->name('read');
        Route::patch('tout-lire', [AdminNotificationController::class, 'markAllAsRead'])->name('read-all');

        Route::get('modeles', [AdminNotificationTemplateController::class, 'index'])
            ->middleware('permission:systeme.modifier')
            ->name('templates.index');
        Route::get('modeles/{template}', [AdminNotificationTemplateController::class, 'edit'])
            ->middleware('permission:systeme.modifier')
            ->name('templates.edit');
        Route::patch('modeles/{template}', [AdminNotificationTemplateController::class, 'update'])
            ->middleware('permission:systeme.modifier')
            ->name('templates.update');
    });

    Route::middleware('permission:clients_pro.voir')->group(function () {
        Route::get('clients-pro', [AdminB2bController::class, 'index'])->name('b2b.index');
        Route::patch('clients-pro/{user}/valider', [AdminB2bController::class, 'approve'])
            ->middleware('permission:clients_pro.valider')->name('b2b.approve');
        Route::patch('clients-pro/{user}/refuser', [AdminB2bController::class, 'reject'])
            ->middleware('permission:clients_pro.valider')->name('b2b.reject');
        Route::patch('clients-pro/{user}/credit', [AdminB2bController::class, 'updateCredit'])->name('b2b.credit');

        Route::get('devis', [AdminQuoteController::class, 'index'])->name('devis.index');
        Route::get('devis/{devis}', [AdminQuoteController::class, 'show'])->name('devis.show');
        Route::post('devis/{devis}/envoyer', [AdminQuoteController::class, 'send'])->name('devis.send');
        Route::post('devis/{devis}/convertir', [AdminQuoteController::class, 'convert'])->name('devis.convert');

        Route::get('commandes-recurrentes', [AdminRecurringOrderController::class, 'index'])->name('commandes-recurrentes.index');
    });

    Route::middleware('permission:pos.voir')->prefix('point-de-vente')->name('pos.')->group(function () {
        Route::get('caisse', [AdminCashRegisterController::class, 'index'])->name('caisse.index');
        Route::get('caisse/ouvrir', [AdminCashRegisterController::class, 'create'])->name('caisse.create');
        Route::post('caisse', [AdminCashRegisterController::class, 'store'])->name('caisse.store');
        Route::get('caisse/{caisse}', [AdminCashRegisterController::class, 'show'])->name('caisse.show');
        Route::post('caisse/{caisse}/mouvement', [AdminCashRegisterController::class, 'addMovement'])->name('caisse.mouvement');
        Route::post('caisse/{caisse}/cloturer', [AdminCashRegisterController::class, 'close'])->name('caisse.close');
        Route::patch('caisse/{caisse}/corriger', [AdminCashRegisterController::class, 'updateClosure'])->name('caisse.correct');
        Route::post('caisse/{caisse}/rouvrir', [AdminCashRegisterController::class, 'reopen'])->name('caisse.reopen');

        Route::get('vente', [AdminPosSaleController::class, 'create'])->name('ventes.create');
        Route::post('vente/client', [AdminPosSaleController::class, 'setCustomer'])->name('ventes.customer');
        Route::post('vente/ajouter/{product}', [AdminPosSaleController::class, 'addToCart'])->name('ventes.add');
        Route::patch('vente/modifier/{product}', [AdminPosSaleController::class, 'updateCart'])->name('ventes.update');
        Route::delete('vente/retirer/{product}', [AdminPosSaleController::class, 'removeFromCart'])->name('ventes.remove');
        Route::post('vente', [AdminPosSaleController::class, 'store'])->name('ventes.store');
        Route::get('vente/{vente}', [AdminPosSaleController::class, 'show'])->name('ventes.show');

        Route::get('retours', [AdminPosReturnController::class, 'index'])->name('retours.index');
        Route::get('retours/creer', [AdminPosReturnController::class, 'create'])->name('retours.create');
        Route::post('retours', [AdminPosReturnController::class, 'store'])->name('retours.store');
    });

    Route::middleware('permission:finance.voir')->group(function () {
        Route::get('tresorerie', [AdminFinanceDashboardController::class, 'index'])->name('tresorerie.index');

        Route::resource('comptes-paiement', AdminPaymentAccountController::class)->parameters(['comptes-paiement' => 'compte']);
        Route::post('comptes-paiement/{compte}/mouvement', [AdminPaymentAccountController::class, 'addTransaction'])->name('comptes-paiement.mouvement');

        Route::get('categories-depenses', [AdminExpenseCategoryController::class, 'index'])->name('categories-depenses.index');
        Route::post('categories-depenses', [AdminExpenseCategoryController::class, 'store'])->name('categories-depenses.store');
        Route::patch('categories-depenses/{expenseCategory}', [AdminExpenseCategoryController::class, 'update'])->name('categories-depenses.update');
        Route::delete('categories-depenses/{expenseCategory}', [AdminExpenseCategoryController::class, 'destroy'])->name('categories-depenses.destroy');

        Route::get('depenses', [AdminExpenseController::class, 'index'])->name('depenses.index');
        Route::get('depenses/creer', [AdminExpenseController::class, 'create'])->name('depenses.create');
        Route::post('depenses', [AdminExpenseController::class, 'store'])->name('depenses.store');
        Route::patch('depenses/{depense}/valider', [AdminExpenseController::class, 'validateExpense'])
            ->middleware('permission:finance.valider')->name('depenses.validate');
        Route::patch('depenses/{depense}/rejeter', [AdminExpenseController::class, 'reject'])
            ->middleware('permission:finance.valider')->name('depenses.reject');
        Route::delete('depenses/{depense}', [AdminExpenseController::class, 'destroy'])->name('depenses.destroy');
    });

    Route::middleware('permission:comptabilite.voir')->prefix('comptabilite')->name('comptabilite.')->group(function () {
        Route::get('plan-comptable', [AdminChartAccountController::class, 'index'])->name('plan-comptable.index');
        Route::post('plan-comptable', [AdminChartAccountController::class, 'store'])->name('plan-comptable.store');
        Route::patch('plan-comptable/{chartAccount}', [AdminChartAccountController::class, 'update'])->name('plan-comptable.update');
        Route::delete('plan-comptable/{chartAccount}', [AdminChartAccountController::class, 'destroy'])->name('plan-comptable.destroy');

        Route::get('journaux', [AdminJournalController::class, 'index'])->name('journaux.index');
        Route::post('journaux', [AdminJournalController::class, 'store'])->name('journaux.store');
        Route::delete('journaux/{journal}', [AdminJournalController::class, 'destroy'])->name('journaux.destroy');

        Route::get('ecritures', [AdminJournalEntryController::class, 'index'])->name('ecritures.index');
        Route::get('ecritures/creer', [AdminJournalEntryController::class, 'create'])->name('ecritures.create');
        Route::post('ecritures', [AdminJournalEntryController::class, 'store'])->name('ecritures.store');
        Route::get('ecritures/{ecriture}', [AdminJournalEntryController::class, 'show'])->name('ecritures.show');
        Route::delete('ecritures/{ecriture}', [AdminJournalEntryController::class, 'destroy'])->name('ecritures.destroy');

        Route::get('grand-livre', [AdminLedgerController::class, 'index'])->name('grand-livre.index');
        Route::get('balance', [AdminTrialBalanceController::class, 'index'])->name('balance.index');

        Route::get('rapprochement', [AdminBankReconciliationController::class, 'index'])->name('rapprochement.index');
        Route::post('rapprochement/{compte}/import', [AdminBankReconciliationController::class, 'import'])->name('rapprochement.import');
        Route::post('rapprochement/lignes/{ligne}/matcher', [AdminBankReconciliationController::class, 'match'])->name('rapprochement.match');
        Route::post('rapprochement/lignes/{ligne}/annuler', [AdminBankReconciliationController::class, 'unmatch'])->name('rapprochement.unmatch');
        Route::patch('rapprochement/lignes/{ligne}/ecart', [AdminBankReconciliationController::class, 'markDiscrepancy'])->name('rapprochement.discrepancy');
        Route::post('rapprochement/lignes/{ligne}/creer-mouvement', [AdminBankReconciliationController::class, 'createTransaction'])->name('rapprochement.create-transaction');
    });

    Route::middleware('permission:immobilisations.voir')->group(function () {
        Route::resource('immobilisations', AdminFixedAssetController::class)->parameters(['immobilisations' => 'immobilisation']);
        Route::post('immobilisations/{immobilisation}/mise-hors-service', [AdminFixedAssetController::class, 'dispose'])->name('immobilisations.dispose');
    });

    Route::middleware('permission:commercial.voir')->prefix('commercial')->name('commercial.')->group(function () {
        Route::get('taxes', [AdminTaxRateController::class, 'index'])->name('taxes.index');
        Route::post('taxes', [AdminTaxRateController::class, 'store'])
            ->middleware('permission:commercial.modifier')->name('taxes.store');
        Route::patch('taxes/{taxRate}', [AdminTaxRateController::class, 'update'])
            ->middleware('permission:commercial.modifier')->name('taxes.update');
        Route::patch('taxes/{taxRate}/defaut', [AdminTaxRateController::class, 'setDefault'])
            ->middleware('permission:commercial.modifier')->name('taxes.default');
        Route::delete('taxes/{taxRate}', [AdminTaxRateController::class, 'destroy'])
            ->middleware('permission:commercial.supprimer')->name('taxes.destroy');

        Route::get('paiements', [AdminPaymentMethodController::class, 'index'])->name('paiements.index');
        Route::post('paiements', [AdminPaymentMethodController::class, 'store'])
            ->middleware('permission:commercial.modifier')->name('paiements.store');
        Route::patch('paiements/{paymentMethod}', [AdminPaymentMethodController::class, 'update'])
            ->middleware('permission:commercial.modifier')->name('paiements.update');
        Route::delete('paiements/{paymentMethod}', [AdminPaymentMethodController::class, 'destroy'])
            ->middleware('permission:commercial.supprimer')->name('paiements.destroy');

        Route::get('zones-livraison', [AdminDeliveryZoneController::class, 'index'])->name('zones.index');
        Route::post('zones-livraison', [AdminDeliveryZoneController::class, 'store'])
            ->middleware('permission:commercial.modifier')->name('zones.store');
        Route::patch('zones-livraison/{deliveryZone}', [AdminDeliveryZoneController::class, 'update'])
            ->middleware('permission:commercial.modifier')->name('zones.update');
        Route::delete('zones-livraison/{deliveryZone}', [AdminDeliveryZoneController::class, 'destroy'])
            ->middleware('permission:commercial.supprimer')->name('zones.destroy');
    });

});
