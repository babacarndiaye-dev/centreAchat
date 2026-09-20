import { Head, Link, useForm } from '@inertiajs/react';
import { motion } from 'framer-motion';
import SiteLayout from '../Layouts/SiteLayout';
import Reveal from '../Components/Reveal';
import ProductCard from '../Components/ProductCard';

function ContactForm() {
    const { data, setData, post, processing, errors } = useForm({
        name: '', email: '', phone: '', subject: '', message: '',
    });

    function submit(e) {
        e.preventDefault();
        post(route('contact.store'), { preserveScroll: true });
    }

    return (
        <form onSubmit={submit} className="card space-y-4 p-8">
            <div>
                <label className="label" htmlFor="name">Nom</label>
                <input type="text" id="name" value={data.name} onChange={(e) => setData('name', e.target.value)} required className="input" />
                {errors.name && <p className="mt-1 text-xs text-terroir-terracotta">{errors.name}</p>}
            </div>
            <div>
                <label className="label" htmlFor="email">E-mail</label>
                <input type="email" id="email" value={data.email} onChange={(e) => setData('email', e.target.value)} required className="input" />
                {errors.email && <p className="mt-1 text-xs text-terroir-terracotta">{errors.email}</p>}
            </div>
            <div>
                <label className="label" htmlFor="phone">Téléphone (optionnel)</label>
                <input type="text" id="phone" value={data.phone} onChange={(e) => setData('phone', e.target.value)} className="input" />
            </div>
            <div>
                <label className="label" htmlFor="subject">Sujet</label>
                <input type="text" id="subject" value={data.subject} onChange={(e) => setData('subject', e.target.value)} className="input" />
            </div>
            <div>
                <label className="label" htmlFor="message">Message</label>
                <textarea id="message" rows="4" value={data.message} onChange={(e) => setData('message', e.target.value)} required className="input" />
                {errors.message && <p className="mt-1 text-xs text-terroir-terracotta">{errors.message}</p>}
            </div>
            <motion.button whileTap={{ scale: 0.98 }} type="submit" disabled={processing} className="btn-primary w-full justify-center disabled:opacity-50">
                Envoyer le message
            </motion.button>
        </form>
    );
}

function ContactSection({ contactInfo }) {
    return (
        <div className="mt-16 grid gap-10 lg:grid-cols-2">
            <Reveal>
                <h2 className="font-display text-xl font-semibold">Nos coordonnées</h2>
                <ul className="mt-6 space-y-4 text-sm text-terroir-dark/80">
                    <li className="flex items-start gap-3"><span className="text-xl">📍</span> {contactInfo.address}</li>
                    <li className="flex items-start gap-3"><span className="text-xl">📞</span> {contactInfo.phone}</li>
                    <li className="flex items-start gap-3"><span className="text-xl">✉️</span> {contactInfo.email}</li>
                    <li className="flex items-start gap-3"><span className="text-xl">🕒</span> {contactInfo.opening_hours}</li>
                </ul>
            </Reveal>
            <Reveal delay={0.1}>
                <ContactForm />
            </Reveal>
        </div>
    );
}

function DevenirFournisseurSection() {
    return (
        <Reveal className="mt-16 card p-8 text-center">
            <p className="text-terroir-dark/70">Vous êtes producteur ou fournisseur et souhaitez rejoindre notre réseau ?</p>
            <Link href={route('pages.show', 'contact')} className="btn-primary mt-6 inline-block">Nous contacter</Link>
        </Reveal>
    );
}

function HotelsProfessionnelsSection() {
    const cards = [
        ['🏷️', 'Tarifs professionnels & de gros', 'Prix dégressifs automatiquement appliqués sur nos produits éligibles dès validation de votre compte, et tarif de gros à partir de 10 unités.'],
        ['📄', 'Devis personnalisés', 'Demandez un devis pour vos commandes importantes ou récurrentes, directement depuis votre espace client.'],
        ['🔁', 'Commandes récurrentes', 'Programmez vos réapprovisionnements réguliers (hebdomadaires, mensuels...) et laissez-nous nous en occuper.'],
        ['💳', 'Paiement à crédit', 'Un plafond de crédit adapté à votre activité peut vous être accordé, avec facturation à échéance de 30 jours.'],
    ];

    return (
        <div className="mt-16">
            <div className="grid gap-6 sm:grid-cols-2">
                {cards.map(([icon, title, text], i) => (
                    <Reveal key={title} delay={i * 0.06} className="card p-6">
                        <span className="text-2xl">{icon}</span>
                        <h3 className="mt-3 font-display text-lg font-semibold">{title}</h3>
                        <p className="mt-2 text-sm text-terroir-dark/70">{text}</p>
                    </Reveal>
                ))}
            </div>

            <Reveal delay={0.2} className="card mt-8 p-8 text-center">
                <h2 className="font-display text-xl font-semibold">Créer mon compte professionnel</h2>
                <p className="mx-auto mt-2 max-w-xl text-sm text-terroir-dark/70">
                    Choisissez votre profil : votre compte est créé immédiatement et passe en revue par notre équipe. Les tarifs professionnels s'activent dès validation.
                </p>
                <div className="mt-6 flex flex-wrap justify-center gap-3">
                    <Link href={`${route('register')}?type=hotel`} className="btn-primary">🏨 Je suis un hôtel</Link>
                    <Link href={`${route('register')}?type=restaurant`} className="btn-primary">🍽️ Je suis un restaurant</Link>
                    <Link href={`${route('register')}?type=entreprise`} className="btn-primary">🏢 Je suis une entreprise</Link>
                    <Link href={`${route('register')}?type=professionnel`} className="btn-outline">Autre profil professionnel</Link>
                </div>
                <p className="mt-6 text-xs text-terroir-dark/50">
                    Déjà client particulier et souhaitez passer en compte professionnel ?{' '}
                    <Link href={route('pages.show', 'contact')} className="font-medium text-terroir-green hover:underline">Contactez-nous</Link>.
                </p>
            </Reveal>
        </div>
    );
}

function EspaceTouristesSection({ souvenirProducts }) {
    const cards = [
        ['🎁', 'Coffrets souvenirs', 'Des sélections prêtes à emporter ou à offrir, représentatives du terroir sénégalais.'],
        ['🏨', "Livraison à l'hôtel", 'Indiquez le nom de votre hôtel et votre numéro de chambre au moment de la commande, nous vous livrons directement.'],
        ['💶', 'Prix en euro (indicatif)', 'Les prix sont affichés en FCFA avec une conversion en euro à titre indicatif sur chaque produit.'],
    ];

    return (
        <div className="mt-16">
            <div className="grid gap-6 sm:grid-cols-3">
                {cards.map(([icon, title, text], i) => (
                    <Reveal key={title} delay={i * 0.06} className="card p-6">
                        <span className="text-2xl">{icon}</span>
                        <h3 className="mt-3 font-display text-lg font-semibold">{title}</h3>
                        <p className="mt-2 text-sm text-terroir-dark/70">{text}</p>
                    </Reveal>
                ))}
            </div>

            {souvenirProducts?.length > 0 && (
                <div className="mt-12">
                    <h2 className="text-center font-display text-xl font-semibold">Nos coffrets à emporter</h2>
                    <div className="mt-6 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
                        {souvenirProducts.map((item, i) => (
                            <Reveal key={item.id} delay={i * 0.05} y={16}>
                                <ProductCard product={item} showProPrice={false} />
                            </Reveal>
                        ))}
                    </div>
                </div>
            )}

            <Reveal delay={0.15} className="card mt-12 p-8 text-center">
                <p className="text-terroir-dark/70">Paiement à la livraison en espèces, ou par Wave / Orange Money. Le paiement par carte bancaire n'est pas encore disponible en ligne — uniquement en boutique.</p>
                <Link href={route('produits.index', { categorie: 'coffrets-cadeaux' })} className="btn-primary mt-6 inline-block">Voir tous les coffrets</Link>
            </Reveal>
        </div>
    );
}

function CoffretsCadeauxSection({ coffrets }) {
    return (
        <div className="mt-16">
            {coffrets?.length > 0 && (
                <div className="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    {coffrets.map((item, i) => (
                        <Reveal key={item.id} delay={i * 0.05} y={16}>
                            <ProductCard product={item} showProPrice={false} />
                        </Reveal>
                    ))}
                </div>
            )}

            <Reveal delay={0.15} className="card mt-10 p-8 text-center">
                <span className="text-2xl">🎁</span>
                <h2 className="mt-2 font-display text-xl font-semibold">Un cadeau à offrir ?</h2>
                <p className="mx-auto mt-2 max-w-xl text-sm text-terroir-dark/70">
                    Chaque coffret est déjà prêt à offrir. Lors de votre commande, vous pouvez ajouter un message personnalisé qui sera joint à la préparation.
                </p>
            </Reveal>
        </div>
    );
}

export default function StaticPage({ page, contactInfo, souvenirProducts, coffrets }) {
    return (
        <SiteLayout>
            <Head title={`${page.meta_title || page.title} — Central d'Achat`}>
                {page.meta_description && <meta name="description" content={page.meta_description} />}
            </Head>

            <section className="mx-auto max-w-4xl px-4 py-16 sm:px-6 lg:px-8">
                <Reveal className="text-center">
                    <span className="section-eyebrow">Central d'Achat</span>
                    <h1 className="section-title mt-2">{page.title}</h1>
                </Reveal>

                {page.content && (
                    <Reveal delay={0.1} className="prose prose-terroir mx-auto mt-10 max-w-none whitespace-pre-line leading-relaxed text-terroir-dark/80">
                        {page.content}
                    </Reveal>
                )}

                {page.slug === 'contact' && <ContactSection contactInfo={contactInfo} />}
                {page.slug === 'devenir-fournisseur' && <DevenirFournisseurSection />}
                {page.slug === 'hotels-professionnels' && <HotelsProfessionnelsSection />}
                {page.slug === 'espace-touristes' && <EspaceTouristesSection souvenirProducts={souvenirProducts} />}
                {page.slug === 'coffrets-cadeaux' && <CoffretsCadeauxSection coffrets={coffrets} />}
            </section>
        </SiteLayout>
    );
}
