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
                    <li className="flex items-start gap-3"><span className="material-symbols-outlined text-xl text-terroir-green">location_on</span> {contactInfo.address}</li>
                    <li className="flex items-start gap-3"><span className="material-symbols-outlined text-xl text-terroir-green">call</span> {contactInfo.phone}</li>
                    <li className="flex items-start gap-3"><span className="material-symbols-outlined text-xl text-terroir-green">mail</span> {contactInfo.email}</li>
                    <li className="flex items-start gap-3"><span className="material-symbols-outlined text-xl text-terroir-green">schedule</span> {contactInfo.opening_hours}</li>
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

function HotelsExcellenceSection() {
    const engagements = [
        ['verified', 'Sélection exigeante'],
        ['fact_check', 'Contrôle qualité'],
        ['route', 'Traçabilité'],
        ['local_shipping', 'Approvisionnement fiable'],
        ['support_agent', 'Service professionnel'],
    ];

    return (
        <div className="mt-16">
            <Reveal>
                <span className="section-eyebrow">Notre engagement</span>
                <h2 className="mt-3 font-display text-4xl font-bold leading-[1.05] tracking-tight text-terroir-dark sm:text-5xl">
                    L'excellence locale<br className="hidden sm:block" /> au service de l'hôtellerie
                </h2>
                <span className="mt-6 block h-px w-16 bg-terroir-gold" />
            </Reveal>

            <div className="mt-10 grid gap-10 lg:grid-cols-[1.3fr_1fr] lg:gap-14">
                <Reveal delay={0.08} className="space-y-5 text-[17px] leading-relaxed text-terroir-dark/75">
                    <p className="font-display text-xl font-semibold leading-snug text-terroir-dark">
                        Chez DIABA HOTEL, nous mettons le meilleur des produits locaux à la disposition des hôtels, restaurants et établissements touristiques.
                    </p>
                    <p>
                        Nous sélectionnons nos produits avec exigence auprès de fournisseurs et producteurs partenaires, en accordant une attention particulière à leur{' '}
                        <strong className="font-semibold text-terroir-dark">qualité, leur fraîcheur, leur conformité et leur traçabilité</strong>.
                    </p>
                    <p>
                        Chaque produit est soumis à un <strong className="font-semibold text-terroir-dark">processus de contrôle rigoureux</strong> avant d'être proposé à nos clients.
                        Notre objectif : garantir une qualité constante et répondre aux standards les plus exigeants du secteur hôtelier.
                    </p>
                </Reveal>

                <Reveal delay={0.16} className="relative rounded-[2px] border border-terroir-gold/25 bg-terroir-gold/5 p-8">
                    <span className="pointer-events-none absolute -top-7 left-7 font-display text-8xl leading-none text-terroir-gold/30">“</span>
                    <p className="relative font-display text-xl font-semibold leading-snug text-terroir-dark">
                        De la sélection à la livraison, nous maîtrisons chaque étape pour vous offrir une expérience d'achat fiable, professionnelle et sans compromis sur la qualité.
                    </p>
                    <span className="mt-5 block h-px w-10 bg-terroir-gold/60" />
                </Reveal>
            </div>

            <Reveal delay={0.22} className="mt-16">
                <span className="section-eyebrow">Nos engagements</span>
                <div className="mt-6 grid gap-x-8 gap-y-6 sm:grid-cols-2">
                    {engagements.map(([icon, label], i) => (
                        <motion.div
                            key={label}
                            initial={{ opacity: 0, x: -12 }}
                            whileInView={{ opacity: 1, x: 0 }}
                            viewport={{ once: true, margin: '-60px' }}
                            transition={{ duration: 0.4, delay: i * 0.06, ease: [0.16, 1, 0.3, 1] }}
                            whileHover={{ x: 4 }}
                            className="flex items-center gap-4"
                        >
                            <span className="flex h-12 w-12 shrink-0 items-center justify-center rounded-[2px] bg-terroir-green/10 text-2xl text-terroir-green">
                                <span className="material-symbols-outlined">{icon}</span>
                            </span>
                            <span className="font-display text-lg font-semibold text-terroir-dark">{label}</span>
                        </motion.div>
                    ))}
                </div>
            </Reveal>

            <Reveal delay={0.3} className="relative mt-16 w-screen overflow-hidden bg-terroir-dark px-6 py-16 text-center sm:py-20" style={{ marginLeft: 'calc(50% - 50vw)', marginRight: 'calc(50% - 50vw)' }}>
                <div className="pointer-events-none absolute -right-20 -top-20 h-72 w-72 rounded-full bg-terroir-gold/10 blur-3xl" />
                <div className="pointer-events-none absolute -bottom-20 -left-20 h-72 w-72 rounded-full bg-terroir-green/25 blur-3xl" />
                <div
                    className="pointer-events-none absolute inset-0 opacity-[0.06]"
                    style={{ backgroundImage: 'repeating-linear-gradient(45deg, #1DBF63 0, #1DBF63 1px, transparent 1px, transparent 16px)' }}
                />
                <p className="relative font-display text-3xl font-bold leading-snug text-white sm:text-4xl">DIABA HOTEL</p>
                <p className="relative mx-auto mt-4 max-w-lg text-lg text-terroir-gold">L'excellence locale, au service de vos établissements.</p>
            </Reveal>
        </div>
    );
}

function HotelsProfessionnelsSection() {
    const cards = [
        ['sell', 'Tarifs professionnels & de gros', 'Prix dégressifs automatiquement appliqués sur nos produits éligibles dès validation de votre compte, et tarif de gros à partir de 10 unités.'],
        ['description', 'Devis personnalisés', 'Demandez un devis pour vos commandes importantes ou récurrentes, directement depuis votre espace client.'],
        ['autorenew', 'Commandes récurrentes', 'Programmez vos réapprovisionnements réguliers (hebdomadaires, mensuels...) et laissez-nous nous en occuper.'],
        ['credit_card', 'Paiement à crédit', 'Un plafond de crédit adapté à votre activité peut vous être accordé, avec facturation à échéance de 30 jours.'],
    ];

    return (
        <div>
            <HotelsExcellenceSection />

            <div className="mt-16 grid gap-6 sm:grid-cols-2">
                {cards.map(([icon, title, text], i) => (
                    <Reveal key={title} delay={i * 0.06} className="card p-6">
                        <span className="material-symbols-outlined text-2xl text-terroir-green">{icon}</span>
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
                    <Link href={`${route('register')}?type=hotel`} className="btn-primary">
                        <span className="material-symbols-outlined text-lg">hotel</span> Je suis un hôtel
                    </Link>
                    <Link href={`${route('register')}?type=restaurant`} className="btn-primary">
                        <span className="material-symbols-outlined text-lg">restaurant</span> Je suis un restaurant
                    </Link>
                    <Link href={`${route('register')}?type=entreprise`} className="btn-primary">
                        <span className="material-symbols-outlined text-lg">business</span> Je suis une entreprise
                    </Link>
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
        ['card_giftcard', 'Coffrets souvenirs', 'Des sélections prêtes à emporter ou à offrir, représentatives du terroir sénégalais.'],
        ['hotel', "Livraison à l'hôtel", 'Indiquez le nom de votre hôtel et votre numéro de chambre au moment de la commande, nous vous livrons directement.'],
        ['euro_symbol', 'Prix en euro (indicatif)', 'Les prix sont affichés en FCFA avec une conversion en euro à titre indicatif sur chaque produit.'],
    ];

    return (
        <div className="mt-16">
            <div className="grid gap-6 sm:grid-cols-3">
                {cards.map(([icon, title, text], i) => (
                    <Reveal key={title} delay={i * 0.06} className="card p-6">
                        <span className="material-symbols-outlined text-2xl text-terroir-green">{icon}</span>
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
                <span className="material-symbols-outlined text-2xl text-terroir-green">card_giftcard</span>
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
            <Head title={`${page.meta_title || page.title} — DIABA HOTEL`}>
                {page.meta_description && <meta name="description" content={page.meta_description} />}
            </Head>

            <section className="mx-auto max-w-4xl overflow-x-hidden px-4 py-16 sm:px-6 lg:px-8">
                <Reveal className="text-center">
                    <span className="section-eyebrow">DIABA HOTEL</span>
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
