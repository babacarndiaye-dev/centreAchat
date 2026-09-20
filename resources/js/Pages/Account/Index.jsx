import { useEffect, useState } from 'react';
import { Head, Link, usePage } from '@inertiajs/react';
import SiteLayout from '../../Layouts/SiteLayout';

function formatFcfa(amount) {
    return new Intl.NumberFormat('fr-FR').format(Math.round(amount)) + ' FCFA';
}

function PushNotificationButton({ vapidPublicKey }) {
    const [status, setStatus] = useState('unsupported');

    async function refresh() {
        setStatus(window.CentralAchatPush ? await window.CentralAchatPush.status() : 'unsupported');
    }

    useEffect(() => {
        const timeout = setTimeout(() => refresh(), 300);
        return () => clearTimeout(timeout);
    }, []);

    async function toggle() {
        if (!window.CentralAchatPush) return;
        if (status === 'subscribed') {
            await window.CentralAchatPush.unsubscribe();
        } else {
            await window.CentralAchatPush.subscribe(vapidPublicKey);
        }
        await refresh();
    }

    if (status === 'unsupported') return null;

    return (
        <button onClick={toggle} type="button" className="btn-outline">
            {status === 'subscribed' ? '🔔 Notifications activées' : '🔕 Activer les notifications'}
        </button>
    );
}

export default function AccountIndex({ orders, isProfessional, isApprovedB2b, creditLimit, creditUsed, creditAvailable, vapidPublicKey }) {
    const { props } = usePage();
    const user = props.auth?.user;

    return (
        <SiteLayout>
            <Head title="Mon compte — Central d'Achat" />

            <section className="mx-auto max-w-4xl px-4 py-16 sm:px-6 lg:px-8">
                <div className="flex flex-wrap items-center justify-between gap-4">
                    <div>
                        <span className="section-eyebrow">Bienvenue</span>
                        <h1 className="section-title mt-2">{user?.name}</h1>
                    </div>
                    <div className="flex flex-wrap gap-3">
                        {vapidPublicKey && <PushNotificationButton vapidPublicKey={vapidPublicKey} />}
                        <a href={route('compte.messages.index')} className="btn-outline">💬 Mes conversations</a>
                        <form action={route('logout')} method="POST">
                            <input type="hidden" name="_token" value={document.querySelector('meta[name=csrf-token]')?.content} />
                            <button type="submit" className="btn-outline">Se déconnecter</button>
                        </form>
                    </div>
                </div>

                {isProfessional && (
                    <div className="card mt-8 p-6">
                        <div className="flex flex-wrap items-center justify-between gap-4">
                            <div>
                                <span className="section-eyebrow">Compte professionnel</span>
                                {user?.b2b_status === 'en_attente' && (
                                    <p className="mt-2 text-sm text-terroir-brown">Votre compte est en attente de validation par notre équipe. Certaines fonctionnalités (devis, commandes récurrentes, tarifs pro) seront disponibles après validation.</p>
                                )}
                                {user?.b2b_status === 'refuse' && (
                                    <p className="mt-2 text-sm text-terroir-terracotta">Votre demande de compte professionnel n'a pas été validée. Contactez-nous pour plus d'informations.</p>
                                )}
                                {user?.b2b_status === 'valide' && (
                                    <p className="mt-2 text-sm text-terroir-green">Compte professionnel validé — vous bénéficiez des tarifs pro et des services dédiés.</p>
                                )}
                            </div>
                            {isApprovedB2b && (
                                <div className="flex gap-3">
                                    <a href={route('compte.devis.index')} className="btn-outline">Mes devis</a>
                                    <a href={route('compte.commandes-recurrentes.index')} className="btn-outline">Commandes récurrentes</a>
                                </div>
                            )}
                        </div>

                        {isApprovedB2b && creditLimit && (
                            <div className="mt-5 grid grid-cols-3 gap-4 border-t border-terroir-green/10 pt-5 text-sm">
                                <div><p className="text-terroir-dark/50">Plafond de crédit</p><p className="font-semibold">{formatFcfa(creditLimit)}</p></div>
                                <div><p className="text-terroir-dark/50">Utilisé</p><p className="font-semibold text-terroir-terracotta">{formatFcfa(creditUsed)}</p></div>
                                <div><p className="text-terroir-dark/50">Disponible</p><p className="font-semibold text-terroir-green">{formatFcfa(creditAvailable)}</p></div>
                            </div>
                        )}
                    </div>
                )}

                <h2 className="mt-12 font-display text-xl font-semibold">Mes commandes</h2>

                {orders.data.length === 0 ? (
                    <p className="mt-4 text-terroir-dark/60">Vous n'avez pas encore passé de commande.</p>
                ) : (
                    <>
                        <div className="mt-6 space-y-4">
                            {orders.data.map((order) => (
                                <div key={order.id} className="card flex flex-wrap items-center justify-between gap-4 p-5">
                                    <div>
                                        <p className="font-semibold">{order.order_number}</p>
                                        <p className="text-sm text-terroir-dark/50">{order.created_at}</p>
                                    </div>
                                    <span className="rounded-full bg-terroir-cream px-4 py-1.5 text-xs font-semibold text-terroir-green">{order.status_label}</span>
                                    <span className="font-bold text-terroir-green">{formatFcfa(order.total)}</span>
                                </div>
                            ))}
                        </div>

                        {orders.links.length > 3 && (
                            <div className="mt-8 flex flex-wrap gap-2">
                                {orders.links.map((link, i) =>
                                    link.url ? (
                                        <Link
                                            key={i}
                                            href={link.url}
                                            preserveScroll
                                            className={
                                                'rounded-full px-4 py-2 text-sm font-medium ' +
                                                (link.active ? 'bg-terroir-green text-white' : 'bg-terroir-cream text-terroir-dark hover:bg-terroir-cream/70')
                                            }
                                            dangerouslySetInnerHTML={{ __html: link.label }}
                                        />
                                    ) : (
                                        <span
                                            key={i}
                                            className="rounded-full px-4 py-2 text-sm font-medium text-terroir-dark/30"
                                            dangerouslySetInnerHTML={{ __html: link.label }}
                                        />
                                    )
                                )}
                            </div>
                        )}
                    </>
                )}
            </section>
        </SiteLayout>
    );
}
