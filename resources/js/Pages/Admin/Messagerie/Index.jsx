import { Head, Link } from '@inertiajs/react';
import { motion } from 'framer-motion';
import AdminLayout from '../../../Layouts/AdminLayout';

export default function Index({ conversations, statuses, filters }) {
    const activeStatus = filters?.status ?? '';

    return (
        <AdminLayout title="Messagerie">
            <Head title="Messagerie — Administration" />

            <div className="flex flex-wrap items-center justify-between gap-3">
                <div className="flex flex-wrap gap-2 text-sm">
                    <Link
                        href={route('admin.messagerie.index')}
                        className={'rounded-full px-3.5 py-1.5 ' + (activeStatus ? 'text-terroir-dark/60' : 'bg-terroir-green font-semibold text-white')}
                    >
                        Toutes
                    </Link>
                    {Object.entries(statuses).map(([key, label]) => (
                        <Link
                            key={key}
                            href={route('admin.messagerie.index', { status: key })}
                            className={'rounded-full px-3.5 py-1.5 ' + (activeStatus === key ? 'bg-terroir-green font-semibold text-white' : 'text-terroir-dark/60')}
                        >
                            {label}
                        </Link>
                    ))}
                </div>
                <Link href={route('admin.messagerie.faq.index')} className="admin-link inline-flex items-center gap-1">
                    <span className="material-symbols-outlined text-lg">menu_book</span> Base de connaissances
                </Link>
            </div>

            <motion.div
                initial={{ opacity: 0, y: 12 }}
                animate={{ opacity: 1, y: 0 }}
                transition={{ duration: 0.35 }}
                className="admin-card mt-6 p-0"
            >
                {conversations.data.length === 0 ? (
                    <p className="py-8 text-center text-terroir-dark/40">Aucune conversation.</p>
                ) : conversations.data.map((conversation) => (
                    <Link
                        key={conversation.id}
                        href={route('admin.messagerie.show', conversation.id)}
                        className={
                            'flex flex-wrap items-center justify-between gap-4 border-b border-terroir-dark/[0.08] px-5 py-4 ' +
                            (conversation.unread_count > 0 ? 'bg-terroir-green/5' : '')
                        }
                    >
                        <div className="min-w-0">
                            <p className="font-semibold">
                                {conversation.customer_name}
                                <span className="admin-badge-neutral ml-2">{conversation.status_label}</span>
                                {conversation.assignee_name && (
                                    <span className="text-sm text-terroir-dark/40"> — {conversation.assignee_name}</span>
                                )}
                            </p>
                            <p className="mt-1.5 truncate text-sm text-terroir-dark/50">{conversation.latest_message_body}</p>
                        </div>
                        <div className="flex shrink-0 items-center gap-3">
                            {conversation.unread_count > 0 && (
                                <span className="inline-flex h-5 min-w-[20px] items-center justify-center rounded-full bg-terroir-terracotta px-1.5 text-xs font-semibold text-white">
                                    {conversation.unread_count}
                                </span>
                            )}
                            <span className="text-sm text-terroir-dark/40">{conversation.last_message_at_human}</span>
                        </div>
                    </Link>
                ))}
            </motion.div>

            {conversations.links.length > 3 && (
                <div className="mt-6 flex flex-wrap gap-1">
                    {conversations.links.map((link, i) =>
                        link.url ? (
                            <Link
                                key={i}
                                href={link.url}
                                preserveScroll
                                className={
                                    'rounded-lg px-3 py-1.5 text-sm ' +
                                    (link.active ? 'bg-terroir-green text-white' : 'bg-white text-terroir-dark/70 hover:bg-terroir-cream')
                                }
                                dangerouslySetInnerHTML={{ __html: link.label }}
                            />
                        ) : (
                            <span key={i} className="rounded-lg px-3 py-1.5 text-sm text-terroir-dark/30" dangerouslySetInnerHTML={{ __html: link.label }} />
                        )
                    )}
                </div>
            )}
        </AdminLayout>
    );
}
