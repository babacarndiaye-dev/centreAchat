import { Head, Link } from '@inertiajs/react';
import SiteLayout from '../../../Layouts/SiteLayout';

export default function MessagesIndex({ conversations }) {
    return (
        <SiteLayout>
            <Head title="Mes conversations — Centrale d'achat" />

            <section className="mx-auto max-w-4xl px-4 py-16 sm:px-6 lg:px-8">
                <span className="section-eyebrow">Espace client</span>
                <h1 className="section-title mt-2">Mes conversations</h1>

                <Link href={route('compte.index')} className="mt-4 inline-flex items-center gap-1 text-sm text-terroir-dark/60 hover:text-terroir-terracotta">
                    <span className="material-symbols-outlined text-base">arrow_back</span> Retour à mon compte
                </Link>

                {conversations.data.length === 0 ? (
                    <p className="mt-10 text-terroir-dark/60">Vous n'avez pas encore échangé avec nous via le chat.</p>
                ) : (
                    <>
                        <div className="mt-8 space-y-3">
                            {conversations.data.map((conversation) => (
                                <Link
                                    key={conversation.id}
                                    href={route('compte.messages.show', conversation.id)}
                                    className="card block p-5 hover:border-terroir-green/30"
                                >
                                    <div className="flex flex-wrap items-center justify-between gap-3">
                                        <p className="text-sm text-terroir-dark/70">{conversation.latest_message_body ?? 'Conversation vide'}</p>
                                        <span
                                            className={
                                                'rounded-full px-3 py-1 text-xs font-semibold ' +
                                                (conversation.status === 'fermee' ? 'bg-terroir-dark/10 text-terroir-dark/60' : 'bg-terroir-green/10 text-terroir-green')
                                            }
                                        >
                                            {conversation.status_label}
                                        </span>
                                    </div>
                                    <p className="mt-2 text-xs text-terroir-dark/40">{conversation.last_message_at}</p>
                                </Link>
                            ))}
                        </div>

                        {conversations.links.length > 3 && (
                            <div className="mt-6 flex flex-wrap gap-2">
                                {conversations.links.map((link, i) =>
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
