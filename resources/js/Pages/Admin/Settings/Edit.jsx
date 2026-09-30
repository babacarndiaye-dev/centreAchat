import { Head, useForm } from '@inertiajs/react';
import { motion } from 'framer-motion';
import AdminLayout from '../../../Layouts/AdminLayout';

const TIMEZONES = ['Africa/Dakar', 'Africa/Abidjan', 'Europe/Paris', 'UTC'];

const COLOR_FIELDS = [
    { key: 'color_primary', label: 'Couleur primaire (vert)' },
    { key: 'color_secondary', label: 'Couleur alerte (erreurs, ruptures)' },
    { key: 'color_accent', label: 'Couleur accent (vert clair)' },
];

const SECTION_TOGGLES = [
    { key: 'show_featured_products', label: 'Produits en vedette' },
    { key: 'show_producers', label: 'Nos producteurs' },
    { key: 'show_testimonials', label: 'Avis clients' },
    { key: 'show_newsletter', label: 'Bloc newsletter' },
];

export default function Edit({ settings, logoUrl, heroImageUrl }) {
    const { data, setData, post, processing, errors } = useForm({
        site_name: settings.site_name ?? '',
        tagline: settings.tagline ?? '',
        currency_symbol: settings.currency_symbol ?? 'FCFA',
        timezone: settings.timezone ?? 'Africa/Dakar',
        lang_en_active: settings.lang_en_active ?? false,
        logo: null,
        phone: settings.phone ?? '',
        whatsapp: settings.whatsapp ?? '',
        email: settings.email ?? '',
        opening_hours: settings.opening_hours ?? '',
        address: settings.address ?? '',
        facebook_url: settings.facebook_url ?? '',
        instagram_url: settings.instagram_url ?? '',
        color_primary: settings.color_primary ?? '#009C4A',
        color_secondary: settings.color_secondary ?? '#C62828',
        color_accent: settings.color_accent ?? '#1DBF63',
        hero_title: settings.hero_title ?? '',
        hero_subtitle: settings.hero_subtitle ?? '',
        hero_image: null,
        announcement_active: settings.announcement_active ?? false,
        announcement_text: settings.announcement_text ?? '',
        show_featured_products: settings.show_featured_products ?? true,
        show_producers: settings.show_producers ?? true,
        show_testimonials: settings.show_testimonials ?? true,
        show_newsletter: settings.show_newsletter ?? true,
        _method: 'patch',
    });

    function handleSubmit(e) {
        e.preventDefault();
        post(route('admin.parametres.update'), { forceFormData: true, preserveScroll: true });
    }

    return (
        <AdminLayout title="Paramètres">
            <Head title="Paramètres — Administration" />

            <motion.form
                initial={{ opacity: 0, y: 12 }}
                animate={{ opacity: 1, y: 0 }}
                transition={{ duration: 0.35 }}
                onSubmit={handleSubmit}
                className="admin-card max-w-3xl"
            >
                <div>
                    <h2 className="font-display text-lg font-semibold">Général</h2>
                    <div className="mt-3 grid gap-4 sm:grid-cols-2">
                        <div>
                            <label className="label">Nom du site</label>
                            <input value={data.site_name} onChange={(e) => setData('site_name', e.target.value)} className="input" />
                            {errors.site_name && <p className="mt-1 text-xs text-terroir-terracotta">{errors.site_name}</p>}
                        </div>
                        <div>
                            <label className="label">Slogan</label>
                            <input value={data.tagline} onChange={(e) => setData('tagline', e.target.value)} className="input" />
                        </div>
                        <div>
                            <label className="label">Devise</label>
                            <input value={data.currency_symbol} onChange={(e) => setData('currency_symbol', e.target.value)} className="input" />
                        </div>
                        <div>
                            <label className="label">Fuseau horaire</label>
                            <select value={data.timezone} onChange={(e) => setData('timezone', e.target.value)} className="input">
                                {TIMEZONES.map((tz) => <option key={tz} value={tz}>{tz}</option>)}
                            </select>
                        </div>
                    </div>

                    <div className="mt-4">
                        <span className="label block">Langues actives</span>
                        <div className="flex gap-6">
                            <label className="flex items-center gap-2 text-sm">
                                <input type="checkbox" checked disabled className="rounded border-terroir-green/30 text-terroir-green focus:ring-terroir-green/20" />
                                Français (toujours active)
                            </label>
                            <label className="flex items-center gap-2 text-sm">
                                <input
                                    type="checkbox"
                                    checked={data.lang_en_active}
                                    onChange={(e) => setData('lang_en_active', e.target.checked)}
                                    className="rounded border-terroir-green/30 text-terroir-green focus:ring-terroir-green/20"
                                />
                                Anglais
                            </label>
                        </div>
                        <p className="mt-1.5 text-sm text-terroir-dark/50">L'anglais est réservé pour l'espace touristes ; la traduction complète du site reste un chantier séparé.</p>
                    </div>

                    <div className="mt-4">
                        <label className="label">Logo</label>
                        <div className="flex items-center gap-4">
                            {logoUrl && <img src={logoUrl} alt="Logo actuel" className="h-12 w-12 rounded-lg object-cover" />}
                            <input
                                type="file"
                                accept="image/*"
                                onChange={(e) => setData('logo', e.target.files[0] ?? null)}
                                className="input file:mr-3 file:rounded-lg file:border-0 file:bg-terroir-green file:px-3 file:py-1.5 file:text-sm file:font-semibold file:text-white"
                            />
                        </div>
                        {errors.logo && <p className="mt-1 text-xs text-terroir-terracotta">{errors.logo}</p>}
                    </div>
                </div>

                <div className="mt-8 border-t border-terroir-green/10 pt-8">
                    <h2 className="font-display text-lg font-semibold">Coordonnées</h2>
                    <div className="mt-3 grid gap-4 sm:grid-cols-2">
                        <div>
                            <label className="label">Téléphone</label>
                            <input value={data.phone} onChange={(e) => setData('phone', e.target.value)} className="input" />
                        </div>
                        <div>
                            <label className="label">WhatsApp</label>
                            <input value={data.whatsapp} onChange={(e) => setData('whatsapp', e.target.value)} className="input" />
                        </div>
                        <div>
                            <label className="label">E-mail</label>
                            <input type="email" value={data.email} onChange={(e) => setData('email', e.target.value)} className="input" />
                        </div>
                        <div>
                            <label className="label">Horaires d'ouverture</label>
                            <input value={data.opening_hours} onChange={(e) => setData('opening_hours', e.target.value)} className="input" />
                        </div>
                        <div className="sm:col-span-2">
                            <label className="label">Adresse</label>
                            <input value={data.address} onChange={(e) => setData('address', e.target.value)} className="input" />
                        </div>
                    </div>
                </div>

                <div className="mt-8 border-t border-terroir-green/10 pt-8">
                    <h2 className="font-display text-lg font-semibold">Réseaux sociaux</h2>
                    <div className="mt-3 grid gap-4 sm:grid-cols-2">
                        <div>
                            <label className="label">Facebook</label>
                            <input type="url" value={data.facebook_url} onChange={(e) => setData('facebook_url', e.target.value)} className="input" />
                        </div>
                        <div>
                            <label className="label">Instagram</label>
                            <input type="url" value={data.instagram_url} onChange={(e) => setData('instagram_url', e.target.value)} className="input" />
                        </div>
                    </div>
                </div>

                <div className="mt-8 border-t border-terroir-green/10 pt-8">
                    <h2 className="font-display text-lg font-semibold">Couleurs du site</h2>
                    <p className="mt-1.5 text-sm text-terroir-dark/50">Appliquées immédiatement sur tout le site, sans build ni déploiement.</p>
                    <div className="mt-3 grid gap-4 sm:grid-cols-3">
                        {COLOR_FIELDS.map(({ key, label }) => (
                            <div key={key}>
                                <label className="label">{label}</label>
                                <div className="flex items-center gap-2">
                                    <input
                                        type="color"
                                        value={data[key] || '#009C4A'}
                                        onChange={(e) => setData(key, e.target.value)}
                                        className="h-10 w-16 shrink-0 rounded-lg border border-terroir-green/20 p-0.5"
                                    />
                                    <input value={data[key]} onChange={(e) => setData(key, e.target.value)} className="input" />
                                </div>
                            </div>
                        ))}
                    </div>
                </div>

                <div className="mt-8 border-t border-terroir-green/10 pt-8">
                    <h2 className="font-display text-lg font-semibold">Bannière d'accueil</h2>
                    <div className="mt-3 flex flex-col gap-5">
                        <div>
                            <label className="label">Titre principal</label>
                            <input
                                value={data.hero_title}
                                onChange={(e) => setData('hero_title', e.target.value)}
                                placeholder="Le meilleur du terroir local, sélectionné pour vous."
                                className="input"
                            />
                        </div>
                        <div>
                            <label className="label">Sous-titre</label>
                            <textarea rows={2} value={data.hero_subtitle} onChange={(e) => setData('hero_subtitle', e.target.value)} className="input" />
                        </div>
                        <div>
                            <label className="label">Image de fond (optionnel)</label>
                            <div className="flex items-center gap-4">
                                {heroImageUrl && <img src={heroImageUrl} alt="" className="h-16 w-28 rounded-lg object-cover" />}
                                <input
                                    type="file"
                                    accept="image/*"
                                    onChange={(e) => setData('hero_image', e.target.files[0] ?? null)}
                                    className="input file:mr-3 file:rounded-lg file:border-0 file:bg-terroir-green file:px-3 file:py-1.5 file:text-sm file:font-semibold file:text-white"
                                />
                            </div>
                            {errors.hero_image && <p className="mt-1 text-xs text-terroir-terracotta">{errors.hero_image}</p>}
                        </div>
                    </div>

                    <div className="mt-6 border-t border-terroir-green/10 pt-6">
                        <label className="flex items-center gap-2 text-sm font-semibold">
                            <input
                                type="checkbox"
                                checked={data.announcement_active}
                                onChange={(e) => setData('announcement_active', e.target.checked)}
                                className="rounded border-terroir-green/30 text-terroir-green focus:ring-terroir-green/20"
                            />
                            Afficher un bandeau d'annonce en haut du site
                        </label>
                        <input
                            value={data.announcement_text}
                            onChange={(e) => setData('announcement_text', e.target.value)}
                            placeholder="Ex : Livraison offerte dès 50 000 FCFA d'achat"
                            className="input mt-3"
                        />
                    </div>
                </div>

                <div className="mt-8 border-t border-terroir-green/10 pt-8">
                    <h2 className="font-display text-lg font-semibold">Sections de la page d'accueil</h2>
                    <div className="mt-3 grid gap-3 sm:grid-cols-2">
                        {SECTION_TOGGLES.map(({ key, label }) => (
                            <label key={key} className="flex items-center gap-2 text-sm">
                                <input
                                    type="checkbox"
                                    checked={data[key]}
                                    onChange={(e) => setData(key, e.target.checked)}
                                    className="rounded border-terroir-green/30 text-terroir-green focus:ring-terroir-green/20"
                                />
                                {label}
                            </label>
                        ))}
                    </div>
                </div>

                <button type="submit" disabled={processing} className="btn-primary mt-8 disabled:opacity-50">Enregistrer les paramètres</button>
            </motion.form>
        </AdminLayout>
    );
}
