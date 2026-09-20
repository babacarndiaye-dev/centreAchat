import { Head, Link } from '@inertiajs/react';
import { motion } from 'framer-motion';
import AdminLayout from '../../../../Layouts/AdminLayout';

function fcfa(value) {
    return new Intl.NumberFormat('fr-FR').format(Math.round(value)) + ' FCFA';
}

export default function Index({ accounts }) {
    return (
        <AdminLayout title="Comptes de paiement">
            <Head title="Comptes de paiement — Administration" />

            <div className="flex flex-wrap items-center justify-between gap-3">
                <p className="text-sm text-terroir-dark/50">Banque, Mobile Money, Caisse.</p>
                <Link href={route('admin.comptes-paiement.create')} className="btn-primary">
                    <span className="material-symbols-outlined text-lg">add</span>
                    Nouveau compte
                </Link>
            </div>

            <motion.div
                initial={{ opacity: 0, y: 12 }}
                animate={{ opacity: 1, y: 0 }}
                transition={{ duration: 0.35 }}
                className="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-3"
            >
                {accounts.length === 0 ? (
                    <p className="text-terroir-dark/50">Aucun compte de paiement.</p>
                ) : accounts.map((account) => (
                    <Link
                        key={account.id}
                        href={route('admin.comptes-paiement.show', account.id)}
                        className="admin-card block transition hover:-translate-y-0.5 hover:shadow-lg"
                    >
                        <span className="text-xs font-semibold uppercase tracking-wide text-terroir-terracotta">{account.type_label}</span>
                        <h3 className="mt-1.5 font-display text-lg font-semibold">{account.name}</h3>
                        {account.provider && (
                            <p className="mt-1 text-sm text-terroir-dark/50">
                                {account.provider} {account.account_number ? `— ${account.account_number}` : ''}
                            </p>
                        )}
                        <p className="mt-1.5 text-2xl font-bold text-terroir-green">{fcfa(account.balance)}</p>
                        {!account.is_active && <span className="admin-badge-neutral mt-1.5">Inactif</span>}
                    </Link>
                ))}
            </motion.div>
        </AdminLayout>
    );
}
