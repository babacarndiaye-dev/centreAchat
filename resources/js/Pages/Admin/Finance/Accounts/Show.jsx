import { Head, Link, useForm } from '@inertiajs/react';
import AdminLayout from '../../../../Layouts/AdminLayout';

function fcfa(value) {
    return new Intl.NumberFormat('fr-FR').format(Math.round(value)) + ' FCFA';
}

export default function Show({ account }) {
    const { data, setData, post, processing, errors, reset } = useForm({
        type: 'entree',
        amount: '',
        transaction_date: new Date().toISOString().slice(0, 10),
        category: '',
        description: '',
    });

    function handleSubmit(e) {
        e.preventDefault();
        post(route('admin.comptes-paiement.mouvement', account.id), {
            preserveScroll: true,
            onSuccess: () => reset('amount', 'category', 'description'),
        });
    }

    return (
        <AdminLayout title={account.name}>
            <Head title={`${account.name} — Administration`} />

            <div className="flex flex-wrap items-center justify-between gap-4">
                <div>
                    <span className="text-xs font-semibold uppercase tracking-wide text-terroir-terracotta">{account.type_label}</span>
                    <h2 className="mt-1 font-display text-2xl font-semibold">{account.name}</h2>
                </div>
                <div className="flex items-center gap-3">
                    <span className="text-2xl font-bold text-terroir-green">{fcfa(account.balance)}</span>
                    <Link href={route('admin.comptes-paiement.edit', account.id)} className="btn-outline">Modifier</Link>
                </div>
            </div>

            <div className="admin-card mt-6">
                <h3 className="font-display text-base font-semibold">Ajouter un mouvement</h3>
                <form onSubmit={handleSubmit} className="mt-3 flex flex-wrap gap-3">
                    <select value={data.type} onChange={(e) => setData('type', e.target.value)} required className="input w-32">
                        <option value="entree">Entrée</option>
                        <option value="sortie">Sortie</option>
                    </select>
                    <input
                        type="number"
                        step="0.01"
                        value={data.amount}
                        onChange={(e) => setData('amount', e.target.value)}
                        placeholder="Montant"
                        required
                        className="input w-32"
                    />
                    <input
                        type="date"
                        value={data.transaction_date}
                        onChange={(e) => setData('transaction_date', e.target.value)}
                        required
                        className="input w-40"
                    />
                    <input
                        value={data.category}
                        onChange={(e) => setData('category', e.target.value)}
                        placeholder="Catégorie"
                        className="input w-40"
                    />
                    <input
                        value={data.description}
                        onChange={(e) => setData('description', e.target.value)}
                        placeholder="Description"
                        required
                        className="input min-w-[160px] flex-1"
                    />
                    <button type="submit" disabled={processing} className="btn-primary disabled:opacity-50">Ajouter</button>
                    {errors.description && <p className="w-full text-xs text-terroir-terracotta">{errors.description}</p>}
                </form>
            </div>

            <div className="admin-card mt-6 overflow-x-auto p-0">
                <table className="admin-table">
                    <thead>
                        <tr>
                            <th className="pl-6">Date</th>
                            <th>Description</th>
                            <th>Catégorie</th>
                            <th className="pr-6 text-right">Montant</th>
                        </tr>
                    </thead>
                    <tbody>
                        {account.transactions.length === 0 ? (
                            <tr><td colSpan={4} className="py-8 text-center text-terroir-dark/40">Aucun mouvement.</td></tr>
                        ) : account.transactions.map((t) => (
                            <tr key={t.id}>
                                <td className="pl-6 text-terroir-dark/60">{t.transaction_date}</td>
                                <td>{t.description}{t.reference ? ` (${t.reference})` : ''}</td>
                                <td className="text-terroir-dark/60">{t.category ?? '—'}</td>
                                <td className={'pr-6 text-right font-semibold ' + (t.type === 'entree' ? 'text-terroir-green' : 'text-terroir-terracotta')}>
                                    {t.type === 'entree' ? '+' : '-'}{fcfa(t.amount)}
                                </td>
                            </tr>
                        ))}
                    </tbody>
                </table>
            </div>
        </AdminLayout>
    );
}
