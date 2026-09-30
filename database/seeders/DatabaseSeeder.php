<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\ChartAccount;
use App\Models\ContactMessage;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\Journal;
use App\Models\NewsletterSubscriber;
use App\Models\PaymentAccount;
use App\Models\PaymentAccountTransaction;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Page;
use App\Models\Post;
use App\Models\Producer;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Quote;
use App\Models\QuoteItem;
use App\Models\RecurringOrder;
use App\Models\RecurringOrderItem;
use App\Models\Setting;
use App\Models\Supplier;
use App\Models\SupplierProduct;
use App\Models\Testimonial;
use App\Models\User;
use App\Support\Chat\Intent;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedSettings();
        $this->seedUsers();
        $categories = $this->seedCategories();
        $producers = $this->seedProducers();
        $this->seedProducts($categories, $producers);
        $this->seedPages();
        $this->seedPosts();
        $this->seedTestimonials();
        $this->seedSuppliers();
        $this->seedSample();
        $this->seedB2bDemo();
        $this->seedFinance();
        $this->seedAccounting();
        $this->seedFixedAssets();
        $this->seedCommercial();
        $this->seedProductSettings();
        $this->seedRolesAndPermissions();
        $this->seedNotificationTemplates();
        $this->seedFaqEntries();
    }

    protected function seedSettings(): void
    {
        $settings = [
            'site_name' => 'DIABA HOTEL',
            'tagline' => 'Du terroir local à votre table',
            'phone' => '+221 77 000 00 00',
            'whatsapp' => '+221 77 000 00 00',
            'email' => 'contact@centraldachat.sn',
            'address' => 'Rond-Point Malicounda, Mbour – Sénégal',
            'opening_hours' => 'Lundi - Samedi : 8h00 - 19h00',
            'facebook_url' => 'https://facebook.com',
            'instagram_url' => 'https://instagram.com',
            'color_primary' => '#009C4A',
            'color_secondary' => '#C62828',
            'color_accent' => '#1DBF63',
            'currency_symbol' => 'FCFA',
            'timezone' => 'Africa/Dakar',
            'lang_fr_active' => '1',
            'lang_en_active' => '0',
            'show_featured_products' => '1',
            'show_producers' => '1',
            'show_testimonials' => '1',
            'show_newsletter' => '1',
            'announcement_active' => '0',
        ];

        foreach ($settings as $key => $value) {
            Setting::set($key, $value);
        }
    }

    protected function seedUsers(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@centraldachat.sn'],
            [
                'name' => 'Administrateur DIABA HOTEL',
                'password' => bcrypt('password'),
                'is_admin' => true,
                'user_type' => 'particulier',
            ]
        );

        $hotel = User::firstOrCreate(
            ['email' => 'client@centraldachat.sn'],
            [
                'name' => 'Aïssatou Diop',
                'phone' => '+221 77 111 22 33',
                'password' => bcrypt('password'),
                'user_type' => 'hotel',
                'company_name' => 'Hôtel Teranga Mbour',
            ]
        );
        $hotel->update(['b2b_status' => 'valide', 'credit_limit' => 500000]);

        $restaurant = User::firstOrCreate(
            ['email' => 'restaurant@centraldachat.sn'],
            [
                'name' => 'Moussa Fall',
                'phone' => '+221 77 444 55 66',
                'password' => bcrypt('password'),
                'user_type' => 'restaurant',
                'company_name' => 'Restaurant Le Baobab',
            ]
        );
        $restaurant->update(['b2b_status' => 'en_attente']);
    }

    protected function seedCategories(): array
    {
        $names = [
            'Fruits & Légumes' => '🥭',
            'Céréales & Légumineuses' => '🌾',
            'Épices & Condiments' => '🌶️',
            'Huiles & Miel' => '🍯',
            'Produits de la mer' => '🐟',
            'Coffrets & Cadeaux' => '🎁',
        ];

        $categories = [];

        foreach ($names as $name => $icon) {
            $categories[] = Category::firstOrCreate(
                ['slug' => \Illuminate\Support\Str::slug($name)],
                ['name' => $name, 'is_active' => true, 'position' => count($categories)]
            );
        }

        return $categories;
    }

    protected function seedProducers(): array
    {
        $producers = [
            ['name' => 'Coopérative de Malicounda', 'region' => 'Mbour, Thiès', 'description' => 'Coopérative de producteurs maraîchers spécialisée dans les fruits et légumes de saison, cultivés sans pesticides.'],
            ['name' => 'Ferme Ndiaye & Fils', 'region' => 'Fatick', 'description' => 'Exploitation familiale produisant céréales et légumineuses selon des méthodes traditionnelles.'],
            ['name' => 'Sel & Épices de Casamance', 'region' => 'Casamance', 'description' => 'Producteurs d\'épices et de condiments authentiques de la région naturelle de Casamance.'],
            ['name' => 'Ruchers de la Petite Côte', 'region' => 'Mbour', 'description' => 'Apiculteurs locaux proposant un miel pur, récolté artisanalement sur la Petite Côte.'],
        ];

        return collect($producers)->map(function ($data) {
            return Producer::firstOrCreate(
                ['slug' => \Illuminate\Support\Str::slug($data['name'])],
                array_merge($data, ['is_active' => true, 'is_featured' => true])
            );
        })->all();
    }

    protected function seedProducts(array $categories, array $producers): void
    {
        $products = [
            ['name' => 'Mangue Kent du Sénégal', 'category' => 0, 'producer' => 0, 'price' => 1500, 'unit' => 'kg', 'origin' => 'Casamance', 'featured' => true, 'new' => false],
            ['name' => 'Bissap séché bio', 'category' => 0, 'producer' => 0, 'price' => 1200, 'unit' => 'sachet 250g', 'origin' => 'Thiès', 'featured' => true, 'new' => true],
            ['name' => 'Riz brisé local Sédhiou', 'category' => 1, 'producer' => 1, 'price' => 8500, 'unit' => 'sac 5kg', 'origin' => 'Sédhiou', 'featured' => true, 'new' => false],
            ['name' => 'Niébé blanc', 'category' => 1, 'producer' => 1, 'price' => 3200, 'unit' => 'kg', 'origin' => 'Fatick', 'featured' => false, 'new' => false],
            ['name' => 'Piment séché Casamance', 'category' => 2, 'producer' => 2, 'price' => 2500, 'unit' => 'sachet 100g', 'origin' => 'Casamance', 'featured' => true, 'new' => false],
            ['name' => 'Sel artisanal de Casamance', 'category' => 2, 'producer' => 2, 'price' => 1800, 'unit' => 'sachet 500g', 'origin' => 'Casamance', 'featured' => false, 'new' => true],
            ['name' => 'Huile de palme rouge', 'category' => 3, 'producer' => 2, 'price' => 4500, 'unit' => 'litre', 'origin' => 'Casamance', 'featured' => false, 'new' => false],
            ['name' => 'Miel toutes fleurs Petite Côte', 'category' => 3, 'producer' => 3, 'price' => 5500, 'unit' => 'pot 500g', 'origin' => 'Mbour', 'featured' => true, 'new' => false],
            ['name' => 'Thiof fumé', 'category' => 4, 'producer' => 0, 'price' => 6500, 'unit' => 'kg', 'origin' => 'Mbour', 'featured' => false, 'new' => true],
            ['name' => 'Crevettes séchées', 'category' => 4, 'producer' => 0, 'price' => 7000, 'unit' => 'kg', 'origin' => 'Joal-Fadiouth', 'featured' => false, 'new' => false],
            ['name' => "Coffret Découverte du Terroir", 'category' => 5, 'producer' => null, 'price' => 15000, 'unit' => 'coffret', 'origin' => 'Sénégal', 'featured' => true, 'new' => true],
            ['name' => 'Coffret Saveurs de Casamance', 'category' => 5, 'producer' => null, 'price' => 18000, 'unit' => 'coffret', 'origin' => 'Casamance', 'featured' => false, 'new' => false],
        ];

        foreach ($products as $i => $data) {
            Product::firstOrCreate(
                ['reference' => 'CA-'.str_pad($i + 1, 4, '0', STR_PAD_LEFT)],
                [
                    'category_id' => $categories[$data['category']]->id,
                    'producer_id' => $data['producer'] !== null ? $producers[$data['producer']]->id : null,
                    'name' => $data['name'],
                    'slug' => \Illuminate\Support\Str::slug($data['name']),
                    'short_description' => 'Produit local authentique, sélectionné avec soin par DIABA HOTEL.',
                    'description' => "Ce produit est issu de la sélection rigoureuse de DIABA HOTEL auprès de producteurs locaux. Fraîcheur, qualité et authenticité garanties.",
                    'origin' => $data['origin'],
                    'unit' => $data['unit'],
                    'price' => $data['price'],
                    'professional_price' => round($data['price'] * 0.9),
                    'wholesale_price' => round($data['price'] * 0.8),
                    'stock_quantity' => rand(10, 120),
                    'stock_alert_threshold' => 10,
                    'is_featured' => $data['featured'],
                    'is_new' => $data['new'],
                    'is_active' => true,
                ]
            );
        }
    }

    protected function seedPages(): void
    {
        $pages = [
            ['slug' => 'a-propos', 'title' => 'À propos de DIABA HOTEL', 'content' => "DIABA HOTEL est une structure spécialisée dans l'achat, l'approvisionnement, la valorisation, le stockage et la commercialisation de produits locaux.\n\nBasés au Rond-Point Malicounda à Mbour, nous travaillons chaque jour avec des producteurs et fournisseurs locaux pour proposer des produits authentiques, frais et de qualité aux particuliers, hôtels, restaurants et professionnels du Sénégal."],
            ['slug' => 'notre-histoire', 'title' => 'Notre histoire', 'content' => "DIABA HOTEL est né de la volonté de valoriser le terroir sénégalais et de faciliter l'accès à des produits locaux de qualité, en soutenant l'économie locale."],
            ['slug' => 'notre-mission', 'title' => 'Notre mission', 'content' => "Faciliter l'approvisionnement en produits frais et de qualité issus du terroir sénégalais, pour tous : particuliers, professionnels, hôtels et institutions."],
            ['slug' => 'nos-engagements', 'title' => 'Nos engagements', 'content' => "Qualité, traçabilité, soutien aux producteurs locaux et développement durable sont au cœur de nos engagements."],
            ['slug' => 'hotels-professionnels', 'title' => 'Hôtels & Professionnels', 'content' => "DIABA HOTEL accompagne les hôtels, restaurants et professionnels avec des tarifs dégressifs, des commandes en gros, des devis personnalisés et une facturation adaptée.\n\nCréez votre compte professionnel en ligne ci-dessous : après validation par notre équipe, vous accédez immédiatement aux tarifs professionnels et de gros."],
            ['slug' => 'espace-touristes', 'title' => 'Espace Touristes', 'content' => "Découvrez l'authenticité du terroir sénégalais à travers nos produits et nos coffrets souvenirs. Livraison possible directement à votre hôtel."],
            ['slug' => 'coffrets-cadeaux', 'title' => 'Coffrets & Cadeaux', 'content' => "Nos coffrets rassemblent une sélection de produits Anfa Agro, prêts à offrir. Ajoutez un message personnalisé lors de votre commande."],
            ['slug' => 'faq', 'title' => 'Questions fréquentes', 'content' => "Comment passer commande ?\nVous pouvez commander directement en ligne ou nous contacter par téléphone.\n\nQuels sont les délais de livraison ?\nEn général sous 24 à 48h dans la région de Mbour.\n\nProposez-vous des tarifs professionnels ?\nOui, contactez-nous pour ouvrir un compte professionnel."],
            ['slug' => 'livraison', 'title' => 'Livraison', 'content' => "Nous livrons à domicile, en entreprise ou à l'hôtel dans la région de Mbour et au-delà. Retrait en boutique également possible au Rond-Point Malicounda."],
            ['slug' => 'devenir-fournisseur', 'title' => 'Devenir fournisseur', 'content' => "Vous êtes producteur ou fournisseur de produits locaux ? Rejoignez notre réseau de partenaires et bénéficiez d'un accès à nos canaux de distribution."],
            ['slug' => 'contact', 'title' => 'Contactez-nous', 'content' => "Une question, une demande de devis ou simplement envie d'en savoir plus ? Notre équipe vous répond rapidement."],
            ['slug' => 'mentions-legales', 'title' => 'Mentions légales', 'content' => "DIABA HOTEL — Rond-Point Malicounda, Mbour, Sénégal. Ce site est édité et exploité par DIABA HOTEL."],
            ['slug' => 'politique-de-confidentialite', 'title' => 'Politique de confidentialité', 'content' => "DIABA HOTEL s'engage à protéger la confidentialité des données personnelles de ses utilisateurs conformément à la réglementation en vigueur."],
            ['slug' => 'conditions-generales', 'title' => 'Conditions générales de vente', 'content' => "Les présentes conditions générales de vente régissent les relations contractuelles entre DIABA HOTEL et ses clients."],
        ];

        foreach ($pages as $page) {
            Page::firstOrCreate(['slug' => $page['slug']], array_merge($page, ['is_published' => true]));
        }
    }

    protected function seedPosts(): void
    {
        $posts = [
            ['title' => 'La mangue Kent, star de la saison', 'type' => 'actualite', 'excerpt' => 'Découvrez pourquoi la mangue Kent du Sénégal est très appréciée cette saison.'],
            ['title' => 'Recette : Thiéboudienne traditionnel', 'type' => 'recette', 'excerpt' => 'Notre recette pas à pas du plat national sénégalais avec nos produits du terroir.'],
            ['title' => "DIABA HOTEL ouvre son portail B2B", 'type' => 'actualite', 'excerpt' => 'Hôtels et restaurants peuvent désormais commander en ligne avec des tarifs dédiés.'],
        ];

        foreach ($posts as $post) {
            Post::firstOrCreate(
                ['slug' => \Illuminate\Support\Str::slug($post['title'])],
                array_merge($post, [
                    'content' => $post['excerpt']."\n\nContenu complet à venir.",
                    'is_published' => true,
                    'published_at' => now()->subDays(rand(1, 20)),
                ])
            );
        }
    }

    protected function seedTestimonials(): void
    {
        $testimonials = [
            ['author_name' => 'Aïssatou Diop', 'author_role' => 'Hôtel Teranga Mbour', 'content' => "Des produits d'une fraîcheur remarquable et un service très professionnel. DIABA HOTEL est devenu notre partenaire de confiance.", 'rating' => 5],
            ['author_name' => 'Moussa Fall', 'author_role' => 'Restaurant Le Baobab', 'content' => "Livraison rapide et produits de qualité constante. Je recommande vivement pour les professionnels.", 'rating' => 5],
            ['author_name' => 'Fatou Sarr', 'author_role' => 'Particulière', 'content' => "J'adore pouvoir commander des produits locaux authentiques directement en ligne, ça soutient nos producteurs !", 'rating' => 4],
        ];

        foreach ($testimonials as $testimonial) {
            Testimonial::firstOrCreate(
                ['author_name' => $testimonial['author_name'], 'content' => $testimonial['content']],
                array_merge($testimonial, ['is_published' => true])
            );
        }
    }

    protected function seedSuppliers(): void
    {
        $suppliers = [
            ['name' => 'Grossiste Ndiaye Import', 'company_name' => 'Ndiaye Import SARL', 'contact_name' => 'Cheikh Ndiaye', 'phone' => '+221 77 222 33 44', 'email' => 'contact@ndiayeimport.sn', 'city' => 'Dakar', 'region' => 'Dakar', 'payment_terms' => '30 jours', 'delivery_delay_days' => 5, 'status' => 'actif', 'rating' => 4.5],
            ['name' => 'Distribution Casamance', 'company_name' => 'Distri Casamance SUARL', 'contact_name' => 'Marie Diatta', 'phone' => '+221 77 333 44 55', 'email' => 'commercial@districasamance.sn', 'city' => 'Ziguinchor', 'region' => 'Casamance', 'payment_terms' => '15 jours', 'delivery_delay_days' => 7, 'status' => 'actif', 'rating' => 4.0],
            ['name' => 'Coopérative Fatick Frais', 'company_name' => null, 'contact_name' => 'Ousmane Sene', 'phone' => '+221 76 555 66 77', 'email' => null, 'city' => 'Fatick', 'region' => 'Fatick', 'payment_terms' => 'Comptant', 'delivery_delay_days' => 2, 'status' => 'en_attente', 'rating' => null],
        ];

        $created = collect($suppliers)->map(fn ($data) => Supplier::firstOrCreate(['name' => $data['name']], $data));

        $products = Product::take(6)->get();

        foreach ($products as $i => $product) {
            $supplier = $created[$i % $created->count()];

            SupplierProduct::firstOrCreate(
                ['supplier_id' => $supplier->id, 'product_id' => $product->id],
                [
                    'supplier_price' => round($product->price * 0.7),
                    'supplier_reference' => 'REF-'.strtoupper(substr(md5($product->id.$supplier->id), 0, 6)),
                    'lead_time_days' => $supplier->delivery_delay_days,
                    'is_preferred' => true,
                ]
            );
        }

        $this->seedSupplierPortalDemo($created->first(), $products);
    }

    protected function seedSupplierPortalDemo(Supplier $supplier, $products): void
    {
        if (! $supplier->user_id) {
            $user = User::firstOrCreate(
                ['email' => 'fournisseur@centraldachat.sn'],
                [
                    'name' => $supplier->name,
                    'phone' => $supplier->phone,
                    'user_type' => 'fournisseur',
                    'company_name' => $supplier->company_name,
                    'password' => Hash::make('password'),
                    'is_active' => true,
                ]
            );

            $supplier->update(['user_id' => $user->id]);
        }

        if ($supplier->purchaseOrders()->exists()) {
            return;
        }

        $items = $products->take(3);

        if ($items->isEmpty()) {
            return;
        }

        $subtotal = $items->sum(fn ($p) => round($p->price * 0.7) * 5);

        $order = PurchaseOrder::create([
            'order_number' => PurchaseOrder::generateOrderNumber(),
            'supplier_id' => $supplier->id,
            'status' => 'envoyee',
            'order_date' => now()->subDays(2),
            'expected_date' => now()->addDays(3),
            'subtotal' => $subtotal,
            'total' => $subtotal,
        ]);

        foreach ($items as $product) {
            $unitPrice = round($product->price * 0.7);

            PurchaseOrderItem::create([
                'purchase_order_id' => $order->id,
                'product_id' => $product->id,
                'quantity_ordered' => 5,
                'unit_price' => $unitPrice,
                'total' => $unitPrice * 5,
            ]);
        }
    }

    protected function seedSample(): void
    {
        if (Order::count() > 0) {
            return;
        }

        $product = Product::first();

        if (! $product) {
            return;
        }

        $order = Order::create([
            'order_number' => Order::generateOrderNumber(),
            'customer_name' => 'Client Démo',
            'customer_phone' => '+221 70 000 00 00',
            'delivery_address' => 'Quartier Diamaguene',
            'city' => 'Mbour',
            'status' => 'nouvelle',
            'subtotal' => $product->price * 2,
            'delivery_fee' => 2000,
            'total' => $product->price * 2 + 2000,
            'payment_method' => 'especes',
            'payment_status' => 'en_attente',
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'product_name' => $product->name,
            'unit_price' => $product->price,
            'quantity' => 2,
            'total' => $product->price * 2,
        ]);

        ContactMessage::create([
            'name' => 'Ibrahima Sy',
            'email' => 'ibrahima.sy@example.com',
            'subject' => 'Demande de devis professionnel',
            'message' => "Bonjour, je souhaiterais obtenir un devis pour un approvisionnement hebdomadaire pour notre restaurant.",
        ]);

        NewsletterSubscriber::create([
            'email' => 'abonne.demo@example.com',
            'subscribed_at' => now(),
        ]);
    }

    protected function seedB2bDemo(): void
    {
        $hotel = User::where('email', 'client@centraldachat.sn')->first();
        $products = Product::take(3)->get();

        if (! $hotel || $products->isEmpty()) {
            return;
        }

        if (! Quote::where('user_id', $hotel->id)->exists()) {
            $quote = Quote::create([
                'quote_number' => Quote::generateNumber(),
                'user_id' => $hotel->id,
                'status' => 'en_attente',
                'notes' => 'Besoin pour un événement de 80 couverts ce week-end.',
            ]);

            foreach ($products as $product) {
                QuoteItem::create([
                    'quote_id' => $quote->id,
                    'product_id' => $product->id,
                    'quantity' => 15,
                ]);
            }
        }

        if (! RecurringOrder::where('user_id', $hotel->id)->exists()) {
            $recurringOrder = RecurringOrder::create([
                'user_id' => $hotel->id,
                'frequency' => 'hebdomadaire',
                'delivery_address' => 'Hôtel Teranga, Route de la Petite Côte',
                'city' => 'Mbour',
                'payment_method' => 'credit',
                'status' => 'active',
                'next_run_date' => now()->addDays(2),
            ]);

            foreach ($products->take(2) as $product) {
                RecurringOrderItem::create([
                    'recurring_order_id' => $recurringOrder->id,
                    'product_id' => $product->id,
                    'quantity' => 10,
                ]);
            }
        }
    }

    protected function seedFinance(): void
    {
        $accounts = [
            ['name' => 'Caisse principale', 'type' => 'caisse', 'initial_balance' => 50000],
            ['name' => 'Compte Ecobank', 'type' => 'banque', 'provider' => 'Ecobank', 'account_number' => 'SN012345678', 'initial_balance' => 1500000],
            ['name' => 'Wave Business', 'type' => 'mobile_money', 'provider' => 'Wave', 'initial_balance' => 200000],
            ['name' => 'Orange Money Pro', 'type' => 'mobile_money', 'provider' => 'Orange Money', 'initial_balance' => 150000],
        ];

        $created = collect($accounts)->mapWithKeys(fn ($data) => [$data['name'] => PaymentAccount::firstOrCreate(['name' => $data['name']], $data)]);

        $categories = ['Loyer', 'Eau', 'Électricité', 'Internet', 'Transport', 'Carburant', 'Salaires', 'Marketing', 'Communication', 'Entretien', 'Fournitures', 'Frais bancaires'];

        $categoryModels = collect($categories)->mapWithKeys(fn ($name) => [$name => ExpenseCategory::firstOrCreate(['name' => $name])]);

        if (Expense::count() > 0) {
            return;
        }

        $admin = User::where('is_admin', true)->first();
        $caisse = $created['Caisse principale'];
        $banque = $created['Compte Ecobank'];

        $demoExpenses = [
            ['category' => 'Électricité', 'account' => $banque, 'amount' => 45000, 'beneficiary' => "SENELEC", 'status' => 'validee'],
            ['category' => 'Carburant', 'account' => $caisse, 'amount' => 25000, 'beneficiary' => 'Station Total Mbour', 'status' => 'validee'],
            ['category' => 'Internet', 'account' => $banque, 'amount' => 35000, 'beneficiary' => 'Orange Sénégal', 'status' => 'en_attente'],
        ];

        foreach ($demoExpenses as $data) {
            $expense = Expense::create([
                'expense_category_id' => $categoryModels[$data['category']]->id,
                'payment_account_id' => $data['account']->id,
                'expense_date' => now()->subDays(rand(1, 15)),
                'amount' => $data['amount'],
                'beneficiary' => $data['beneficiary'],
                'status' => $data['status'],
                'created_by' => $admin?->id,
                'validated_by' => $data['status'] === 'validee' ? $admin?->id : null,
                'validated_at' => $data['status'] === 'validee' ? now() : null,
            ]);

            if ($data['status'] === 'validee') {
                PaymentAccountTransaction::create([
                    'payment_account_id' => $data['account']->id,
                    'type' => 'sortie',
                    'amount' => $data['amount'],
                    'category' => $data['category'],
                    'description' => 'Dépense : '.$data['category'],
                    'reference' => 'DEP-'.$expense->id,
                    'transaction_date' => $expense->expense_date,
                    'created_by' => $admin?->id,
                ]);
            }
        }
    }

    protected function seedAccounting(): void
    {
        $accounts = [
            ['code' => '101', 'name' => 'Capital social', 'class' => 1],
            ['code' => '211', 'name' => 'Immobilisations corporelles', 'class' => 2],
            ['code' => '311', 'name' => 'Stocks de marchandises', 'class' => 3],
            ['code' => '401', 'name' => 'Fournisseurs', 'class' => 4],
            ['code' => '411', 'name' => 'Clients', 'class' => 4],
            ['code' => '421', 'name' => 'Personnel', 'class' => 4],
            ['code' => '512', 'name' => 'Banques', 'class' => 5],
            ['code' => '514', 'name' => 'Mobile Money', 'class' => 5],
            ['code' => '571', 'name' => 'Caisse', 'class' => 5],
            ['code' => '601', 'name' => 'Achats de marchandises', 'class' => 6],
            ['code' => '605', 'name' => 'Fournitures non stockables (eau, énergie)', 'class' => 6],
            ['code' => '606', 'name' => 'Fournitures de bureau', 'class' => 6],
            ['code' => '613', 'name' => 'Locations', 'class' => 6],
            ['code' => '615', 'name' => 'Entretien et réparations', 'class' => 6],
            ['code' => '623', 'name' => 'Publicité et relations publiques', 'class' => 6],
            ['code' => '624', 'name' => 'Transports', 'class' => 6],
            ['code' => '626', 'name' => 'Frais de télécommunications', 'class' => 6],
            ['code' => '627', 'name' => 'Frais bancaires', 'class' => 6],
            ['code' => '641', 'name' => 'Rémunérations du personnel', 'class' => 6],
            ['code' => '701', 'name' => 'Ventes de marchandises', 'class' => 7],
        ];

        $created = collect($accounts)->mapWithKeys(fn ($data) => [$data['code'] => ChartAccount::firstOrCreate(['code' => $data['code']], $data)]);

        $journals = [
            ['code' => 'VE', 'name' => 'Journal des ventes', 'type' => 'ventes'],
            ['code' => 'AC', 'name' => 'Journal des achats', 'type' => 'achats'],
            ['code' => 'BQ', 'name' => 'Journal de banque', 'type' => 'banque'],
            ['code' => 'CA', 'name' => 'Journal de caisse', 'type' => 'caisse'],
            ['code' => 'OD', 'name' => 'Opérations diverses', 'type' => 'od'],
        ];

        foreach ($journals as $data) {
            Journal::firstOrCreate(['code' => $data['code']], $data);
        }

        $accountMap = [
            'Caisse principale' => '571',
            'Compte Ecobank' => '512',
            'Wave Business' => '514',
            'Orange Money Pro' => '514',
        ];

        foreach ($accountMap as $name => $code) {
            PaymentAccount::where('name', $name)->update(['chart_account_id' => $created[$code]->id]);
        }

        $categoryMap = [
            'Loyer' => '613',
            'Eau' => '605',
            'Électricité' => '605',
            'Internet' => '626',
            'Transport' => '624',
            'Carburant' => '624',
            'Salaires' => '641',
            'Marketing' => '623',
            'Communication' => '626',
            'Entretien' => '615',
            'Fournitures' => '606',
            'Frais bancaires' => '627',
        ];

        foreach ($categoryMap as $name => $code) {
            ExpenseCategory::where('name', $name)->update(['chart_account_id' => $created[$code]->id]);
        }
    }

    protected function seedFixedAssets(): void
    {
        if (\App\Models\FixedAsset::count() > 0) {
            return;
        }

        $assets = [
            ['category' => 'vehicule', 'name' => 'Camionnette de livraison Renault', 'acquisition_date' => now()->subYears(2), 'acquisition_value' => 8500000, 'useful_life_years' => 5, 'depreciation_method' => 'degressif'],
            ['category' => 'ordinateur', 'name' => 'Ordinateur portable — Bureau', 'acquisition_date' => now()->subMonths(8), 'acquisition_value' => 650000, 'useful_life_years' => 3, 'depreciation_method' => 'lineaire'],
            ['category' => 'mobilier', 'name' => 'Mobilier boutique (comptoir, étagères)', 'acquisition_date' => now()->subYear(), 'acquisition_value' => 1200000, 'useful_life_years' => 8, 'depreciation_method' => 'lineaire'],
            ['category' => 'equipement', 'name' => 'Chambre froide', 'acquisition_date' => now()->subMonths(4), 'acquisition_value' => 3200000, 'useful_life_years' => 10, 'depreciation_method' => 'lineaire'],
        ];

        foreach ($assets as $data) {
            \App\Models\FixedAsset::create($data);
        }
    }

    protected function seedCommercial(): void
    {
        \App\Models\TaxRate::firstOrCreate(
            ['name' => 'TVA standard'],
            ['rate' => 18, 'is_default' => true, 'is_active' => true]
        );
        \App\Models\TaxRate::firstOrCreate(
            ['name' => 'Exonéré'],
            ['rate' => 0, 'is_default' => false, 'is_active' => true]
        );

        $accountIdByName = fn (string $name) => \App\Models\PaymentAccount::where('name', $name)->value('id');

        $paymentMethods = [
            ['code' => 'especes', 'name' => 'Espèces', 'payment_account_id' => $accountIdByName('Caisse principale'), 'available_online' => true, 'available_pos' => true, 'requires_b2b' => false, 'position' => 1],
            ['code' => 'wave', 'name' => 'Wave', 'payment_account_id' => $accountIdByName('Wave Business'), 'available_online' => true, 'available_pos' => true, 'requires_b2b' => false, 'position' => 2],
            ['code' => 'orange_money', 'name' => 'Orange Money', 'payment_account_id' => $accountIdByName('Orange Money Pro'), 'available_online' => true, 'available_pos' => true, 'requires_b2b' => false, 'position' => 3],
            ['code' => 'carte', 'name' => 'Carte bancaire', 'payment_account_id' => $accountIdByName('Compte Ecobank'), 'available_online' => false, 'available_pos' => true, 'requires_b2b' => false, 'position' => 4],
        ];

        foreach ($paymentMethods as $data) {
            \App\Models\PaymentMethod::updateOrCreate(['code' => $data['code']], $data);
        }

        $zones = [
            ['name' => 'Mbour centre', 'cities' => 'Mbour', 'fee' => 1000, 'free_above' => 25000, 'delay_days' => 1, 'position' => 1],
            ['name' => 'Petite Côte', 'cities' => 'Saly, Nianing, Somone', 'fee' => 2000, 'free_above' => 50000, 'delay_days' => 1, 'position' => 2],
            ['name' => 'Dakar & environs', 'cities' => 'Dakar, Rufisque, Thiès', 'fee' => 3500, 'free_above' => 75000, 'delay_days' => 2, 'position' => 3],
        ];

        foreach ($zones as $data) {
            \App\Models\DeliveryZone::firstOrCreate(['name' => $data['name']], $data);
        }
    }

    protected function seedProductSettings(): void
    {
        $units = [
            ['name' => 'Kilogramme', 'abbreviation' => 'kg'],
            ['name' => 'Gramme', 'abbreviation' => 'g'],
            ['name' => 'Litre', 'abbreviation' => 'L'],
            ['name' => 'Unité', 'abbreviation' => 'u'],
            ['name' => 'Sachet', 'abbreviation' => null],
            ['name' => 'Pot', 'abbreviation' => null],
        ];

        foreach ($units as $data) {
            \App\Models\Unit::firstOrCreate(['name' => $data['name']], $data);
        }

        $packagingTypes = ['Sachet 100g', 'Sachet 250g', 'Sachet 500g', 'Sac 5kg', 'Pot 500g', 'Carton de 12', 'Coffret'];

        foreach ($packagingTypes as $name) {
            \App\Models\PackagingType::firstOrCreate(['name' => $name]);
        }

        $attributes = ['Bio', 'Origine précise', 'Couleur', 'Saison'];

        foreach ($attributes as $name) {
            \App\Models\ProductAttribute::firstOrCreate(['name' => $name]);
        }
    }

    protected function seedRolesAndPermissions(): void
    {
        $all = \App\Support\Permissions::MODULES;
        $build = function (array $map) use ($all) {
            $permissions = [];
            foreach ($map as $module => $actions) {
                if (! isset($all[$module])) {
                    continue;
                }
                foreach ($actions as $action) {
                    $permissions[] = "{$module}.{$action}";
                }
            }
            return $permissions;
        };

        $allActions = array_keys(\App\Support\Permissions::ACTIONS);
        $allModules = array_keys($all);

        $roles = [
            'Administrateur' => [
                'description' => 'Accès complet à l\'administration.',
                'permissions' => $build(array_fill_keys($allModules, $allActions)),
            ],
            'Directeur' => [
                'description' => 'Vision globale et validation des opérations sensibles.',
                'permissions' => $build([
                    ...array_fill_keys($allModules, ['voir', 'exporter']),
                    'finance' => ['voir', 'valider', 'exporter'],
                    'comptabilite' => ['voir', 'valider', 'exporter'],
                    'fournisseurs' => ['voir', 'valider', 'exporter'],
                ]),
            ],
            'Responsable commercial' => [
                'description' => 'Gestion des ventes, clients pro et devis.',
                'permissions' => $build([
                    'produits' => ['voir', 'creer', 'modifier'],
                    'commandes' => ['voir', 'creer', 'modifier', 'valider', 'exporter'],
                    'clients_pro' => ['voir', 'creer', 'modifier', 'valider'],
                    'contenu' => ['voir', 'creer', 'modifier'],
                ]),
            ],
            'Responsable des achats' => [
                'description' => 'Pilotage des achats fournisseurs.',
                'permissions' => $build([
                    'fournisseurs' => ['voir', 'creer', 'modifier', 'valider', 'exporter'],
                    'stock' => ['voir'],
                    'produits' => ['voir'],
                ]),
            ],
            'Gestionnaire des fournisseurs' => [
                'description' => 'Suivi du référencement et des fiches fournisseurs.',
                'permissions' => $build([
                    'fournisseurs' => ['voir', 'creer', 'modifier', 'exporter'],
                ]),
            ],
            'Responsable logistique' => [
                'description' => 'Coordination des flux de stock et livraisons.',
                'permissions' => $build([
                    'stock' => ['voir', 'creer', 'modifier', 'exporter'],
                    'fournisseurs' => ['voir'],
                    'commandes' => ['voir', 'modifier'],
                ]),
            ],
            'Gestionnaire de stock' => [
                'description' => 'Gestion des niveaux de stock et inventaires.',
                'permissions' => $build([
                    'stock' => ['voir', 'creer', 'modifier', 'valider'],
                ]),
            ],
            'Magasinier' => [
                'description' => 'Mouvements de stock au quotidien.',
                'permissions' => $build([
                    'stock' => ['voir', 'modifier'],
                ]),
            ],
            'Caissier' => [
                'description' => 'Opérations de vente au point de vente.',
                'permissions' => $build([
                    'pos' => ['voir', 'creer', 'modifier'],
                    'commandes' => ['voir', 'creer'],
                ]),
            ],
            'Comptable' => [
                'description' => 'Tenue de la comptabilité générale.',
                'permissions' => $build([
                    'comptabilite' => ['voir', 'creer', 'modifier', 'valider', 'exporter'],
                    'finance' => ['voir', 'exporter'],
                ]),
            ],
            'Responsable financier' => [
                'description' => 'Trésorerie, financements et immobilisations.',
                'permissions' => $build([
                    'finance' => ['voir', 'creer', 'modifier', 'valider', 'exporter'],
                    'comptabilite' => ['voir', 'exporter'],
                    'immobilisations' => ['voir', 'creer', 'modifier', 'exporter'],
                ]),
            ],
            'Responsable marketing' => [
                'description' => 'Contenu éditorial et communication.',
                'permissions' => $build([
                    'contenu' => ['voir', 'creer', 'modifier', 'supprimer', 'exporter'],
                    'produits' => ['voir'],
                ]),
            ],
            'Service client' => [
                'description' => 'Support et suivi des commandes clients.',
                'permissions' => $build([
                    'commandes' => ['voir', 'modifier'],
                    'clients_pro' => ['voir'],
                    'contenu' => ['voir'],
                    'messagerie' => ['voir', 'creer', 'modifier'],
                ]),
            ],
            'Livreur' => [
                'description' => 'Consultation des commandes à livrer.',
                'permissions' => $build([
                    'commandes' => ['voir', 'modifier'],
                ]),
            ],
        ];

        foreach ($roles as $name => $data) {
            $role = \App\Models\Role::firstOrCreate(
                ['slug' => \Illuminate\Support\Str::slug($name)],
                ['name' => $name, 'description' => $data['description']]
            );

            foreach ($data['permissions'] as $permission) {
                \App\Models\RolePermission::firstOrCreate(['role_id' => $role->id, 'permission' => $permission]);
            }
        }
    }

    protected function seedNotificationTemplates(): void
    {
        $templates = [
            'commande_creee' => [
                'name' => 'Confirmation de commande',
                'subject' => 'Confirmation de votre commande {{commande_numero}}',
                'body' => 'Bonjour {{client_nom}}, nous avons bien reçu votre commande {{commande_numero}} d\'un montant de {{commande_total}} FCFA. Nous vous tiendrons informé de son avancement.',
                'sms' => 'DIABA HOTEL : commande {{commande_numero}} reçue ({{commande_total}} FCFA). Merci !',
            ],
            'commande_statut' => [
                'name' => 'Mise à jour du statut de commande',
                'subject' => 'Mise à jour de votre commande {{commande_numero}}',
                'body' => 'Bonjour {{client_nom}}, le statut de votre commande {{commande_numero}} est désormais : {{commande_statut}}.',
                'sms' => 'DIABA HOTEL : commande {{commande_numero}} → {{commande_statut}}.',
            ],
            'paiement_recu' => [
                'name' => 'Paiement reçu',
                'subject' => 'Paiement reçu — commande {{commande_numero}}',
                'body' => 'Bonjour {{client_nom}}, nous avons bien reçu votre paiement de {{paiement_montant}} FCFA pour la commande {{commande_numero}}. Solde restant : {{commande_solde}} FCFA.',
                'sms' => 'DIABA HOTEL : paiement de {{paiement_montant}} FCFA reçu pour la commande {{commande_numero}}.',
            ],
            'livraison_statut' => [
                'name' => 'Mise à jour de livraison',
                'subject' => 'Livraison — commande {{commande_numero}}',
                'body' => 'Bonjour {{client_nom}}, votre commande {{commande_numero}} est maintenant : {{commande_statut}}.',
                'sms' => 'DIABA HOTEL : livraison {{commande_numero}} → {{commande_statut}}.',
            ],
            'stock_faible' => [
                'name' => 'Alerte stock faible',
                'subject' => 'Stock faible : {{produit_nom}}',
                'body' => 'Le produit {{produit_nom}} est en stock faible ({{produit_stock}} restant, seuil {{produit_seuil}}). Merci de procéder au réapprovisionnement.',
                'sms' => 'Stock faible : {{produit_nom}} ({{produit_stock}} restant).',
            ],
            'produit_expirant' => [
                'name' => 'Alerte produit expirant',
                'subject' => 'Produit bientôt expiré : {{produit_nom}}',
                'body' => 'Le produit {{produit_nom}} arrive à expiration le {{produit_expiration}}. Merci de vérifier le stock concerné.',
                'sms' => 'Expiration proche : {{produit_nom}} le {{produit_expiration}}.',
            ],
            'facture_echue' => [
                'name' => 'Alerte facture échue',
                'subject' => 'Facture échue — commande {{commande_numero}}',
                'body' => 'La facture de la commande {{commande_numero}} ({{client_nom}}, {{commande_total}} FCFA) est échue depuis le {{commande_echeance}}. Merci de relancer le client.',
                'sms' => 'Facture échue : commande {{commande_numero}} ({{client_nom}}).',
            ],
            'demande_fournisseur' => [
                'name' => 'Nouvelle demande d\'achat à valider',
                'subject' => 'Nouvelle demande d\'achat à valider : {{demande_reference}}',
                'body' => 'La demande d\'achat {{demande_reference}} soumise par {{demande_demandeur}} attend votre validation.',
                'sms' => 'Demande d\'achat {{demande_reference}} à valider (par {{demande_demandeur}}).',
            ],
            'nouvelle_conversation' => [
                'name' => 'Nouveau message de chat',
                'subject' => 'Nouveau message de chat de {{client_nom}}',
                'body' => '{{client_nom}} vient de démarrer une conversation sur le chat du site : « {{message_extrait}} »',
                'sms' => 'Nouveau chat de {{client_nom}} : {{message_extrait}}',
            ],
            'b2b_compte_valide' => [
                'name' => 'Compte professionnel validé',
                'subject' => 'Votre compte professionnel est validé',
                'body' => 'Bonjour {{client_nom}}, votre compte {{compte_type}} est validé. Vous bénéficiez désormais des tarifs professionnels, pouvez demander des devis et programmer des commandes récurrentes.',
                'sms' => 'DIABA HOTEL : votre compte {{compte_type}} est validé, les tarifs pro s\'appliquent désormais.',
            ],
            'b2b_compte_refuse' => [
                'name' => 'Compte professionnel refusé',
                'subject' => 'Votre demande de compte professionnel',
                'body' => 'Bonjour {{client_nom}}, votre demande de compte {{compte_type}} n\'a pas pu être validée en l\'état. Contactez-nous pour en savoir plus ou compléter votre dossier.',
                'sms' => 'DIABA HOTEL : votre demande de compte {{compte_type}} n\'a pas été validée. Contactez-nous pour plus d\'informations.',
            ],
        ];

        foreach ($templates as $eventKey => $data) {
            \App\Models\NotificationTemplate::firstOrCreate(
                ['event_key' => $eventKey, 'channel' => 'interne'],
                ['name' => $data['name'], 'subject' => $data['subject'], 'body' => $data['body']]
            );
            \App\Models\NotificationTemplate::firstOrCreate(
                ['event_key' => $eventKey, 'channel' => 'email'],
                ['name' => $data['name'], 'subject' => $data['subject'], 'body' => $data['body']]
            );
            \App\Models\NotificationTemplate::firstOrCreate(
                ['event_key' => $eventKey, 'channel' => 'push'],
                ['name' => $data['name'], 'subject' => $data['subject'], 'body' => $data['sms']]
            );
            \App\Models\NotificationTemplate::firstOrCreate(
                ['event_key' => $eventKey, 'channel' => 'sms'],
                ['name' => $data['name'], 'subject' => null, 'body' => $data['sms'], 'is_active' => false]
            );
            \App\Models\NotificationTemplate::firstOrCreate(
                ['event_key' => $eventKey, 'channel' => 'whatsapp'],
                ['name' => $data['name'], 'subject' => null, 'body' => $data['sms'], 'is_active' => false]
            );
        }
    }

    protected function seedFaqEntries(): void
    {
        $entries = [
            // --- Présentation générale ---
            [
                'question' => 'Qu\'est-ce que DIABA HOTEL ?',
                'answer' => 'DIABA HOTEL est une structure sénégalaise spécialisée dans l\'approvisionnement, la sélection et la vente de produits locaux de qualité, auprès de producteurs et fournisseurs du terroir. Nous servons particuliers, touristes, hôtels, restaurants et professionnels.',
                'keywords' => 'quest ce que, presentation, qui etes vous, diaba hotel, central achat, activite',
                'category' => 'Présentation',
                'position' => 1,
            ],
            [
                'question' => 'Que vendez-vous ?',
                'answer' => 'Nous proposons des fruits & légumes, céréales & légumineuses, épices & condiments, huiles & miel, produits de la mer, ainsi que des coffrets cadeaux — tous issus de producteurs sénégalais. Le catalogue complet est consultable dans la rubrique Produits.',
                'keywords' => 'vendez, catalogue, gamme, categories produits, quoi acheter',
                'category' => 'Présentation',
                'position' => 2,
            ],
            [
                'question' => 'Quels types de clients pouvez-vous servir ?',
                'answer' => 'Nous servons les particuliers, touristes, hôtels, restaurants, entreprises, institutions et revendeurs. Un profil professionnel avec tarifs dédiés est disponible après validation de votre compte.',
                'keywords' => 'types clients, qui peut acheter, particulier, touriste, institution',
                'category' => 'Présentation',
                'position' => 3,
            ],
            [
                'question' => 'Pourquoi acheter chez DIABA HOTEL ?',
                'answer' => 'Nous facilitons l\'accès à des produits locaux sélectionnés avec soin, à prix juste, tout en valorisant directement les producteurs et fournisseurs sénégalais.',
                'keywords' => 'pourquoi, avantage, interet, difference',
                'category' => 'Présentation',
                'position' => 4,
            ],

            // --- Localisation (groupe de paraphrases) ---
            [
                'question' => 'Où êtes-vous situés ?',
                'answer' => 'Notre centrale d\'achat est basée au Rond-Point Malicounda, sur la route de Mbour, au Sénégal.',
                'keywords' => 'adresse, situe, situes, localisation, mbour, malicounda, boutique, magasin, venir, ou etes vous, ou es',
                'category' => 'Contact',
                'intent' => Intent::LOCALISATION,
                'position' => 5,
            ],

            // --- Produits ---
            [
                'question' => 'Comment voir vos produits ?',
                'answer' => 'Consultez tous nos produits dans la rubrique "Nos produits" du site, avec filtres par catégorie.',
                'keywords' => 'voir produits, boutique, catalogue en ligne',
                'category' => 'Produits',
                'intent' => Intent::PRODUITS,
                'position' => 6,
            ],
            [
                'question' => 'Comment rechercher un produit ?',
                'answer' => 'Utilisez la barre de recherche en haut de la page "Nos produits" et saisissez le nom du produit ou un mot-clé — vous pouvez aussi filtrer par catégorie.',
                'keywords' => 'rechercher, recherche, trouver un produit, filtre',
                'category' => 'Produits',
                'intent' => Intent::RECHERCHE_PRODUIT,
                'position' => 7,
            ],
            [
                'question' => 'Comment connaître le prix d\'un produit ?',
                'answer' => 'Le prix est affiché directement sur la fiche du produit. Les comptes professionnels validés peuvent bénéficier d\'un tarif pro ou d\'un tarif de gros selon le produit.',
                'keywords' => 'prix produit, combien coute, tarif',
                'category' => 'Produits',
                'intent' => Intent::PRIX,
                'position' => 8,
            ],
            [
                'question' => 'Les produits sont-ils disponibles ?',
                'answer' => 'La disponibilité dépend du stock, indiqué sur chaque fiche produit. Si un produit est épuisé, n\'hésitez pas à nous demander sa prochaine date de réapprovisionnement via le chat.',
                'keywords' => 'disponible, disponibilite, en stock, rupture',
                'category' => 'Produits',
                'intent' => Intent::DISPONIBILITE,
                'position' => 9,
            ],
            [
                'question' => 'Vos produits sont-ils garantis frais et locaux ?',
                'answer' => 'Oui, tous nos produits proviennent de producteurs sénégalais sélectionnés. L\'origine et le producteur sont indiqués sur chaque fiche produit lorsque l\'information est disponible.',
                'keywords' => 'frais, local, origine, producteur, qualite, terroir',
                'category' => 'Produits',
                'position' => 10,
            ],
            [
                'question' => 'Puis-je voir les produits d\'un producteur spécifique ?',
                'answer' => 'Oui, la rubrique "Nos producteurs" présente chaque producteur avec les produits qu\'il fournit.',
                'keywords' => 'producteur specifique, profil producteur, nos producteurs',
                'category' => 'Produits',
                'position' => 11,
            ],
            [
                'question' => 'Proposez-vous des coffrets cadeaux ?',
                'answer' => 'Oui, retrouvez notre sélection de coffrets cadeaux mettant en valeur le meilleur du terroir sénégalais dans la rubrique dédiée du site.',
                'keywords' => 'coffret, cadeau, panier garni',
                'category' => 'Produits',
                'position' => 12,
            ],
            [
                'question' => 'Où voir les promotions ?',
                'answer' => 'Les produits en promotion affichent un prix barré sur leur fiche et dans le catalogue. Nous n\'avons pas encore de section promotions dédiée, mais l\'équipe peut vous signaler les meilleures offres du moment via le chat.',
                'keywords' => 'promotion, promo, offre, reduction, solde',
                'category' => 'Produits',
                'intent' => Intent::PROMOTION,
                'position' => 13,
            ],

            // --- Compte client ---
            [
                'question' => 'Comment créer un compte sur le site ?',
                'answer' => 'Cliquez sur "Créer un compte" en haut du site, renseignez vos informations et votre type de profil (particulier, hôtel, restaurant, entreprise...). C\'est gratuit et ne prend qu\'une minute.',
                'keywords' => 'creer, cree, crees, compte, inscription, inscrire, sinscrire, enregistrer, nouveau compte',
                'category' => 'Compte',
                'intent' => Intent::COMPTE,
                'position' => 14,
            ],
            [
                'question' => 'J\'ai oublié mon mot de passe, comment le réinitialiser ?',
                'answer' => 'La réinitialisation en libre-service n\'est pas encore disponible sur la plateforme. Contactez notre équipe via ce chat avec l\'e-mail de votre compte, nous vous aiderons à le récupérer.',
                'keywords' => 'mot de passe, oublie, mdp, reinitialiser, perdu, connexion impossible',
                'category' => 'Compte',
                'intent' => Intent::MOT_DE_PASSE,
                'position' => 15,
            ],
            [
                'question' => 'Comment modifier mes informations de compte ou mes adresses ?',
                'answer' => 'La modification en libre-service n\'est pas encore disponible. Indiquez-nous vos nouvelles informations via ce chat et notre équipe met votre compte à jour.',
                'keywords' => 'modifier profil, changer adresse, mettre a jour compte, coordonnees',
                'category' => 'Compte',
                'position' => 16,
            ],
            [
                'question' => 'Comment retrouver mes anciennes conversations avec vous ?',
                'answer' => 'Si vous êtes connecté, retrouvez tout l\'historique de vos échanges avec nous dans "Mon compte" > "Mes conversations".',
                'keywords' => 'ancienne conversation, historique chat, messages precedents',
                'category' => 'Messagerie',
                'position' => 17,
            ],

            // --- Favoris & fidélité (non disponibles) ---
            [
                'question' => 'Puis-je ajouter des produits à mes favoris ?',
                'answer' => 'Cette fonctionnalité n\'est pas encore disponible sur la plateforme. Vous pouvez toutefois retrouver facilement un produit via la recherche.',
                'keywords' => 'favori, favoris, liste envie, wishlist, coeur',
                'category' => 'Produits',
                'position' => 18,
            ],
            [
                'question' => 'Avez-vous un programme de fidélité ?',
                'answer' => 'Pas encore de programme de fidélité pour le moment. Suivez nos actualités pour être informé si nous en lançons un.',
                'keywords' => 'fidelite, points, avantage, recompense, programme',
                'category' => 'Présentation',
                'position' => 19,
            ],
            [
                'question' => 'Comment utiliser un code promo ?',
                'answer' => 'Nous ne proposons pas encore de système de codes promo au panier. Les remises éventuelles sont directement appliquées sur le prix affiché du produit.',
                'keywords' => 'code promo, coupon, code reduction, bon de reduction',
                'category' => 'Paiement',
                'intent' => Intent::CODE_PROMO,
                'position' => 20,
            ],

            // --- Commande ---
            [
                'question' => 'Comment passer une commande ?',
                'answer' => 'Choisissez vos produits, ajoutez-les au panier, vérifiez les quantités puis cliquez sur "Commander". Renseignez ensuite votre adresse de livraison et votre mode de paiement.',
                'keywords' => 'passer commande, commander, comment acheter',
                'category' => 'Commandes',
                'intent' => Intent::COMMANDE,
                'position' => 21,
            ],
            [
                'question' => 'Puis-je modifier les quantités dans mon panier ?',
                'answer' => 'Oui, augmentez ou diminuez les quantités directement depuis votre panier avant de valider la commande.',
                'keywords' => 'modifier quantite, changer quantite panier',
                'category' => 'Commandes',
                'position' => 22,
            ],
            [
                'question' => 'Puis-je supprimer un produit de mon panier ?',
                'answer' => 'Oui, ouvrez votre panier et cliquez sur l\'option de suppression à côté du produit concerné.',
                'keywords' => 'supprimer panier, retirer produit panier, enlever',
                'category' => 'Commandes',
                'position' => 23,
            ],
            [
                'question' => 'Puis-je annuler ma commande ?',
                'answer' => 'Tant que votre commande n\'est pas encore en préparation, contactez-nous immédiatement via ce chat pour l\'annuler. Une fois en préparation ou en livraison, l\'annulation n\'est plus possible.',
                'keywords' => 'annuler, annulation, changer avis',
                'category' => 'Commandes',
                'intent' => Intent::ANNULATION_COMMANDE,
                'position' => 24,
            ],
            [
                'question' => 'Comment savoir si ma commande est confirmée ?',
                'answer' => 'Vous recevez une notification dès la confirmation de votre commande, et pouvez consulter son statut à tout moment depuis "Mon compte".',
                'keywords' => 'commande confirmee, confirmation commande',
                'category' => 'Commandes',
                'position' => 25,
            ],
            [
                'question' => 'Comment suivre ma commande ?',
                'answer' => 'Connectez-vous à votre compte, ouvrez "Mon compte" et consultez la commande concernée pour voir son statut. Vous recevez aussi une notification à chaque changement.',
                'keywords' => 'suivre, suivi, statut, ou est ma commande, tracking, colis, partie, en cours livraison',
                'category' => 'Commandes',
                'intent' => Intent::SUIVI_COMMANDE,
                'position' => 26,
            ],
            [
                'question' => 'Quels sont les statuts possibles d\'une commande ?',
                'answer' => 'Une commande peut passer par : Nouvelle, Confirmée, Payée, En préparation, Prête, En livraison, Livrée, Terminée (ou Annulée/Remboursée si besoin).',
                'keywords' => 'statuts commande, etapes commande, cycle de vie',
                'category' => 'Commandes',
                'position' => 27,
            ],
            [
                'question' => 'Y a-t-il un montant minimum de commande ?',
                'answer' => 'Non, il n\'y a pas de montant minimum pour commander. Les frais de livraison dépendent uniquement de votre zone et du montant total de votre panier.',
                'keywords' => 'minimum, montant minimal, plafond, seuil',
                'category' => 'Commandes',
                'position' => 28,
            ],

            // --- Livraison ---
            [
                'question' => 'Quels sont vos délais et zones de livraison ?',
                'answer' => 'Nous livrons à Mbour centre (1000 FCFA, gratuit dès 25 000 FCFA d\'achat), sur la Petite Côte (2000 FCFA, gratuit dès 50 000 FCFA) et à Dakar & environs (3500 FCFA, gratuit dès 75 000 FCFA), sous 24 à 48h selon votre secteur.',
                'keywords' => 'livraison, delai, zone, transport, mbour, dakar, gratuite, frais, domicile',
                'category' => 'Livraison',
                'intent' => Intent::LIVRAISON,
                'position' => 29,
            ],
            [
                'question' => 'Puis-je être livré à mon hôtel ?',
                'answer' => 'Oui, si votre hôtel se trouve dans l\'une de nos zones de livraison. Lors de la commande, cochez « Livraison à l\'hôtel » et indiquez le nom de l\'hôtel et votre numéro de chambre.',
                'keywords' => 'livraison hotel, livrer hotel, chambre, numero chambre',
                'category' => 'Livraison',
                'intent' => Intent::HOTEL,
                'position' => 30,
            ],
            [
                'question' => 'Puis-je récupérer ma commande directement en boutique ?',
                'answer' => 'Le retrait en boutique n\'est pas encore proposé sur la plateforme, seule la livraison est disponible pour le moment.',
                'keywords' => 'retrait boutique, pickup, recuperer sur place, click and collect',
                'category' => 'Livraison',
                'position' => 31,
            ],
            [
                'question' => 'Comment connaître la date précise de livraison ?',
                'answer' => 'Nous ne fixons pas de créneau horaire précis, mais vous êtes notifié à chaque étape (préparation, en livraison, livrée). Pour une urgence, contactez-nous via le chat.',
                'keywords' => 'date livraison, quand livre, heure livraison, creneau',
                'category' => 'Livraison',
                'position' => 32,
            ],
            [
                'question' => 'Ma commande est en retard, que faire ?',
                'answer' => 'Consultez d\'abord le statut de votre commande dans "Mon compte". Si le délai vous semble anormal, contactez notre équipe directement via ce chat avec votre numéro de commande.',
                'keywords' => 'retard, pas encore livre, en retard',
                'category' => 'Livraison',
                'position' => 33,
            ],

            // --- Paiement ---
            [
                'question' => 'Quels moyens de paiement acceptez-vous ?',
                'answer' => 'Vous pouvez payer par Wave, Orange Money, carte bancaire, en espèces à la livraison, ou à crédit si vous disposez d\'un compte professionnel validé.',
                'keywords' => 'paiement, wave, orange money, carte, especes, credit, payer, mobile money',
                'category' => 'Paiement',
                'intent' => Intent::PAIEMENT,
                'position' => 34,
            ],
            [
                'question' => 'Mon paiement n\'a pas fonctionné, que dois-je faire ?',
                'answer' => 'Le règlement de votre commande est confirmé directement avec notre équipe (pas de paiement en ligne automatisé) : en cas de souci au moment de la commande, contactez-nous via ce chat, nous réglons ça rapidement.',
                'keywords' => 'paiement echoue, probleme paiement, ca ne marche pas payer',
                'category' => 'Paiement',
                'position' => 35,
            ],
            [
                'question' => 'Comment fonctionne le paiement à crédit pour les professionnels ?',
                'answer' => 'Une fois votre compte professionnel validé, un plafond de crédit vous est attribué. Vous pouvez alors commander sans payer immédiatement, avec un règlement selon les modalités convenues avec notre équipe.',
                'keywords' => 'credit, encours, plafond, paiement differe, facturation',
                'category' => 'Paiement',
                'position' => 36,
            ],
            [
                'question' => 'Vais-je recevoir une facture pour ma commande ?',
                'answer' => 'Le détail complet de votre commande (produits, montants, mode de paiement) reste consultable dans "Mon compte". Pour un document de facturation formel, demandez-le à notre équipe via ce chat.',
                'keywords' => 'facture, recu, justificatif, invoice',
                'category' => 'Paiement',
                'intent' => Intent::FACTURE,
                'position' => 37,
            ],
            [
                'question' => 'Les prix affichés incluent-ils la TVA ?',
                'answer' => 'Oui, les prix affichés sur le site s\'entendent toutes taxes comprises. Le détail de la TVA (18%) apparaît lors du paiement.',
                'keywords' => 'tva, taxe, prix ttc, hors taxe',
                'category' => 'Paiement',
                'position' => 38,
            ],

            // --- Hôtels & professionnels ---
            [
                'question' => 'Comment devenir client professionnel (hôtel, restaurant) ?',
                'answer' => 'Créez un compte en sélectionnant votre type d\'activité (hôtel, restaurant, entreprise...), puis notre équipe valide votre dossier sous 24h ouvrées pour vous donner accès aux tarifs pro et au paiement à crédit.',
                'keywords' => 'professionnel, b2b, hotel, restaurant, entreprise, compte pro, je represente un hotel',
                'category' => 'Compte professionnel',
                'intent' => Intent::PROFESSIONNEL,
                'position' => 39,
            ],
            [
                'question' => 'Proposez-vous des tarifs de gros pour les revendeurs et commandes en grande quantité ?',
                'answer' => 'Oui, les comptes professionnels validés (revendeurs, entreprises) bénéficient de tarifs préférentiels et d\'une option de vente en gros sur certains produits.',
                'keywords' => 'gros, revendeur, tarif professionnel, prix pro, wholesale, grande quantite',
                'category' => 'Compte professionnel',
                'position' => 40,
            ],
            [
                'question' => 'Puis-je obtenir un devis pour ma commande professionnelle ?',
                'answer' => 'Oui, les comptes professionnels validés peuvent demander un devis directement depuis "Mon compte" > "Mes devis", que nous validons ou ajustons avant de le transformer en commande.',
                'keywords' => 'devis, estimation, proposition commerciale',
                'category' => 'Compte professionnel',
                'position' => 41,
            ],
            [
                'question' => 'Proposez-vous des commandes récurrentes ou automatiques ?',
                'answer' => 'Oui, les comptes professionnels peuvent programmer des commandes récurrentes (hebdomadaires, mensuelles...) depuis "Mon compte" > "Commandes récurrentes", pour ne plus avoir à repasser commande manuellement.',
                'keywords' => 'recurrente, automatique, abonnement, programmer, reguliere',
                'category' => 'Compte professionnel',
                'position' => 42,
            ],

            // --- Fournisseurs & producteurs ---
            [
                'question' => 'Comment devenir fournisseur et vendre mes produits ?',
                'answer' => 'Contactez-nous via le formulaire "Devenir fournisseur" accessible depuis le site. Après étude de votre dossier, vous obtenez un accès à notre portail fournisseur pour gérer vos produits, commandes et paiements.',
                'keywords' => 'fournisseur, vendre, producteur, devenir partenaire, referencement, candidature',
                'category' => 'Fournisseurs',
                'intent' => Intent::FOURNISSEUR,
                'position' => 43,
            ],
            [
                'question' => 'En tant que fournisseur, puis-je suivre mes commandes et paiements ?',
                'answer' => 'Oui, une fois votre accès au portail fournisseur validé, vous pouvez y consulter vos bons de commande, confirmer les livraisons et suivre vos paiements.',
                'keywords' => 'portail fournisseur, suivre commande fournisseur, paiement fournisseur',
                'category' => 'Fournisseurs',
                'position' => 44,
            ],

            // --- Messagerie & assistance ---
            [
                'question' => 'Comment contacter DIABA HOTEL ?',
                'answer' => 'Le plus rapide est ce chat, disponible sur toutes les pages du site. Notre équipe est aussi disponible du lundi au samedi de 8h à 19h.',
                'keywords' => 'contacter, horaires, ouverture, disponibilite, heure, joindre',
                'category' => 'Contact',
                'intent' => Intent::HORAIRES,
                'position' => 45,
            ],
            [
                'question' => 'Puis-je parler à un conseiller ?',
                'answer' => 'Bien sûr — cliquez sur "Parler à un agent" en haut de ce chat, ou dites-le-moi directement, je vous mets en relation avec notre équipe.',
                'keywords' => 'conseiller, quelquun, vraie personne, service client, agent humain, contacter service client',
                'category' => 'Messagerie',
                'intent' => Intent::CONTACT_AGENT,
                'position' => 46,
            ],
            [
                'question' => 'J\'ai reçu un produit qui ne correspond pas à ma commande ou qui est endommagé.',
                'answer' => 'Contactez-nous rapidement via ce chat en indiquant votre numéro de commande et, si possible, une photo du problème — nous organisons un échange ou un remboursement.',
                'keywords' => 'retour, remboursement, echange, defaut, probleme, endommage, ne correspond pas, reclamation',
                'category' => 'Après-vente',
                'intent' => Intent::RECLAMATION,
                'position' => 47,
            ],
            [
                'question' => 'Le site ne fonctionne pas ou affiche une erreur',
                'answer' => 'Essayez d\'actualiser la page et vérifiez votre connexion. Si le problème persiste, décrivez-nous ce qui se passe via ce chat, avec si possible une capture d\'écran.',
                'keywords' => 'site ne fonctionne pas, erreur, page ne saffiche pas, site lent, bug, application bloque',
                'category' => 'Assistance',
                'position' => 48,
            ],
            [
                'question' => 'J\'ai été débité deux fois ou mon paiement n\'apparaît pas',
                'answer' => 'Le règlement de votre commande est validé manuellement par notre équipe (pas de paiement en ligne automatisé) : contactez-nous via ce chat avec la référence de la transaction, nous vérifions et corrigeons rapidement. Ne payez pas une seconde fois avant notre confirmation.',
                'keywords' => 'debite deux fois, double debit, paiement napparait pas, paye deux fois, transaction en attente',
                'category' => 'Paiement',
                'position' => 49,
            ],
            [
                'question' => 'Je pense que mon compte a été piraté',
                'answer' => 'Contactez-nous immédiatement via ce chat avec l\'e-mail de votre compte : nous sécurisons l\'accès de notre côté. La réinitialisation de mot de passe en libre-service n\'étant pas encore disponible, c\'est le moyen le plus rapide de reprendre le contrôle.',
                'keywords' => 'compte pirate, activite suspecte, connexion inconnue, quelquun utilise mon compte',
                'category' => 'Sécurité',
                'position' => 50,
            ],

            // --- Touristes ---
            [
                'question' => 'Je suis touriste, comment puis-je commander ?',
                'answer' => 'Aucun compte n\'est nécessaire : commandez directement en ligne, cochez « Livraison à l\'hôtel » lors de la commande et indiquez le nom de votre hôtel et votre numéro de chambre. Le paiement se fait en espèces à la livraison, ou par Wave / Orange Money.',
                'keywords' => 'touriste, vacances, visiteur, de passage, sejour, voyageur',
                'category' => 'Touristes',
                'intent' => Intent::TOURISTE,
                'position' => 51,
            ],
            [
                'question' => 'Proposez-vous des souvenirs à rapporter ?',
                'answer' => 'Oui, notre catégorie « Coffrets & Cadeaux » rassemble des sélections de produits locaux prêtes à offrir ou à emporter, idéales comme souvenirs du Sénégal.',
                'keywords' => 'souvenir, coffret, cadeau, rapporter, emporter, idee cadeau',
                'category' => 'Touristes',
                'intent' => Intent::TOURISTE,
                'position' => 52,
            ],
            [
                'question' => 'Puis-je payer en euro ou par carte bancaire en tant que touriste ?',
                'answer' => 'Les prix sont en FCFA, avec une conversion indicative en euro affichée sur chaque produit (le règlement se fait uniquement en FCFA). Le paiement en ligne se fait en espèces à la livraison, Wave ou Orange Money ; la carte bancaire n\'est acceptée qu\'en boutique, pas encore en ligne.',
                'keywords' => 'payer euro, carte bancaire, devise, taux de change, moyen de paiement touriste',
                'category' => 'Touristes',
                'intent' => Intent::TOURISTE,
                'position' => 53,
            ],
        ];

        foreach ($entries as $data) {
            \App\Models\FaqEntry::updateOrCreate(['question' => $data['question']], $data);
        }
    }
}
