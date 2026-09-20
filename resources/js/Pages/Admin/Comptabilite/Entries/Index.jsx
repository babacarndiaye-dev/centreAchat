import { Head, Link, router } from '@inertiajs/react';
import { useState } from 'react';
import { motion } from 'framer-motion';
import AdminLayout from '../../../../Layouts/AdminLayout';

export default function Index({ entries, journals, filters }) {
    const [journal, setJournal] = useState(filters?.journal ?? '');
    const [from, setFrom] = useState(filters?.from ?? '');
    const [to, setTo] = useState(filters?.to ?? '');

    function applyFilters(next) {
        router.get(route('admin.comptabilite.ecritures.index'), { journal, from, to, ...next }, { preserveState: true });
    }

    return (
        <AdminLayout title="Écritures comptables">
            <Head title="Écritures comptables — Administration" />

            <div className="flex flex-wrap items-center justify-between gap-3">
                <div className="flex flex-wrap gap-2">
                    <select
                        value={journal}
                        onChange={(e) => { setJournal(e.target.value); applyFilters({ journal: e.target.value }); }}
                        className="input max-w-[220px]"
                    >
                        <option value="">Tous les journaux</option>
                        {journals.map((j) => <option key={j.id} value={j.id}>{j.code} — {j.name}</option>)}
                    </select>
                    <input
                        type="date"
                        value={from}
                        onChange={(e) => { setFrom(e.target.value); applyFilters({ from: e.target.value }); }}
                        className="input"
                    />
                    <input
                        type="date"
                        value={to}
                        onChange={(e) => { setTo(e.target.value); applyFilters({ to: e.target.value }); }}
                        className="input"
                    />
                </div>
                <Link href={route('admin.comptabilite.ecritures.create')} className="btn-primary">
                    <span className="material-symbols-outlined text-lg">add</span>
                    Nouvelle écriture
                </Link>
            </div>

            <motion.div
                initial={{ opacity: 0, y: 12 }}
                animate={{ opacity: 1, y: 0 }}
                transition={{ duration: 0.35 }}
                className="admin-card mt-6 overflow-x-auto p-0"
            >
                <table className="admin-table">
                    <thead>
                        <tr>
                            <th className="pl-6">Date</th>
                            <th>Journal</th>
                            <th>Libellé</th>
                            <th>Référence</th>
                            <th className="pr-6 text-right">Montant</th>
                        </tr>
                    </thead>
                    <tbody>
                        {entries.data.length === 0 ? (
                            <tr><td colSpan={5} className="py-8 text-center text-terroir-dark/40">Aucune écriture.</td></tr>
                        ) : entries.data.map((entry) => (
                            <tr key={entry.id}>
                                <td className="pl-6 text-terroir-dark/60">{entry.entry_date}</td>
                                <td><span className="admin-badge-neutral">{entry.journal_code}</span></td>
                                <td><Link href={route('admin.comptabilite.ecritures.show', entry.id)} className="admin-link">{entry.description}</Link></td>
                                <td className="text-terroir-dark/60">{entry.reference}</td>
                                <td className="pr-6 text-right font-semibold">{entry.total_debit.toLocaleString('fr-FR')} FCFA</td>
                            </tr>
                        ))}
                    </tbody>
                </table>
            </motion.div>

            {entries.links.length > 3 && (
                <div className="mt-6 flex flex-wrap gap-1">
                    {entries.links.map((link, i) =>
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
