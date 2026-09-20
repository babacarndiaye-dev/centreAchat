import { Head, Link } from '@inertiajs/react';
import SiteLayout from '../../../Layouts/SiteLayout';

function formatFcfa(amount) {
    return new Intl.NumberFormat('fr-FR').format(Math.round(amount)) + ' FCFA';
}

export default function QuotesIndex({ quotes }) {
    return (
        <SiteLayout>
            <Head title="Mes devis — Centrale d'achat" />

            <section className="mx-auto max-w-4xl px-4 py-16 sm:px-6 lg:px-8">
                <div className="flex flex-wrap items-center justify-between gap-4">
                    <div>
                        <span className="section-eyebrow">Espace professionnel</span>
                        <h1 className="section-title mt-2">Mes devis</h1>
                    </div>
                    <Link href={route('compte.devis.create')} className="btn-primary">+ Demander un devis</Link>
                </div>

                <Link href={route('compte.index')} className="mt-4 inline-flex items-center gap-1 text-sm text-terroir-dark/60 hover:text-terroir-terracotta">
                    <span className="material-symbols-outlined text-base">arrow_back</span> Retour à mon compte
                </Link>

                {quotes.data.length === 0 ? (
                    <p className="mt-10 text-terroir-dark/60">Vous n'avez pas encore de devis.</p>
                ) : (
                    <>
                        <div className="mt-8 space-y-4">
                            {quotes.data.map((quote) => (
                                <Link
                                    key={quote.id}
                                    href={route('compte.devis.show', quote.id)}
                                    className="card flex flex-wrap items-center justify-between gap-4 p-5 transition hover:-translate-y-0.5"
                                >
                                    <div>
                                        <p className="font-semibold">{quote.quote_number}</p>
                                        <p className="text-sm text-terroir-dark/50">{quote.created_at}</p>
                                    </div>
                                    <span className="rounded-full bg-terroir-cream px-4 py-1.5 text-xs font-semibold text-terroir-green">{quote.status_label}</span>
                                    {quote.total > 0 && <span className="font-bold text-terroir-green">{formatFcfa(quote.total)}</span>}
                                </Link>
                            ))}
                        </div>

                        {quotes.links.length > 3 && (
                            <div className="mt-8 flex flex-wrap gap-2">
                                {quotes.links.map((link, i) =>
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
                                        <span key={i} className="rounded-full px-4 py-2 text-sm font-medium text-terroir-dark/30" dangerouslySetInnerHTML={{ __html: link.label }} />
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
