import { Head, Link } from '@inertiajs/react';
import SiteLayout from '../../../Layouts/SiteLayout';

export default function MessagesShow({ conversation }) {
    return (
        <SiteLayout>
            <Head title="Conversation — DIABA HOTEL Produits du Sénégal (D.H.P.S)" />

            <section className="mx-auto max-w-2xl px-4 py-16 sm:px-6 lg:px-8">
                <span className="section-eyebrow">Espace client</span>
                <h1 className="section-title mt-2">Conversation du {conversation.created_at}</h1>

                <Link href={route('compte.messages.index')} className="mt-4 inline-flex items-center gap-1 text-sm text-terroir-dark/60 hover:text-terroir-terracotta">
                    <span className="material-symbols-outlined text-base">arrow_back</span> Retour à mes conversations
                </Link>

                <div className="card mt-8 space-y-3 p-6">
                    {conversation.messages.map((message) => (
                        <div key={message.id} className={'flex ' + (message.sender_type === 'client' ? 'justify-end' : 'justify-start')}>
                            <div
                                className={
                                    'max-w-[80%] rounded-2xl px-4 py-2.5 text-sm ' +
                                    (message.sender_type === 'client'
                                        ? 'bg-terroir-green text-white'
                                        : message.sender_type === 'bot'
                                            ? 'bg-terroir-gold/15 text-terroir-dark'
                                            : 'bg-terroir-cream text-terroir-dark')
                                }
                            >
                                {message.sender_type === 'bot' && (
                                    <p className="mb-0.5 flex items-center gap-1 text-[10px] font-semibold uppercase tracking-wide opacity-60">
                                        <span className="material-symbols-outlined text-xs">smart_toy</span> Assistant
                                    </p>
                                )}
                                <p className="whitespace-pre-line">{message.body}</p>
                                {(message.links ?? []).map((link) => (
                                    <a key={link.url} href={link.url} className="mt-1 block truncate text-xs font-medium underline opacity-80 hover:opacity-100">
                                        {link.label}
                                    </a>
                                ))}
                                <p className="mt-1 text-[10px] opacity-60">{message.created_at}</p>
                            </div>
                        </div>
                    ))}
                </div>

                {conversation.status !== 'fermee' && (
                    <p className="mt-4 text-sm text-terroir-dark/50">Cette conversation est toujours ouverte — utilisez le chat en bas à droite pour continuer à échanger.</p>
                )}
            </section>
        </SiteLayout>
    );
}
