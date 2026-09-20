import { Head, Link, router, useForm } from '@inertiajs/react';
import { useState } from 'react';
import AdminLayout from '../../../../Layouts/AdminLayout';

function fcfa(value) {
    return new Intl.NumberFormat('fr-FR').format(Math.round(value)) + ' FCFA';
}

function MovementForm({ register }) {
    const { data, setData, post, processing, errors, reset } = useForm({ type: 'encaissement', amount: '', reason: '' });

    function handleSubmit(e) {
        e.preventDefault();
        post(route('admin.pos.caisse.mouvement', register.id), { preserveScroll: true, onSuccess: () => reset() });
    }

    return (
        <form onSubmit={handleSubmit} className="mt-3 flex flex-wrap gap-2">
            <select value={data.type} onChange={(e) => setData('type', e.target.value)} required className="input w-40">
                <option value="encaissement">Encaissement</option>
                <option value="decaissement">Décaissement</option>
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
                value={data.reason}
                onChange={(e) => setData('reason', e.target.value)}
                placeholder="Motif"
                required
                className="input min-w-[140px] flex-1"
            />
            <button type="submit" disabled={processing} className="btn-outline disabled:opacity-50">Ajouter</button>
            {errors.amount && <p className="w-full text-xs text-terroir-terracotta">{errors.amount}</p>}
        </form>
    );
}

function CloseForm({ register }) {
    const { data, setData, post, processing } = useForm({ actual_closing_amount: '' });

    function handleSubmit(e) {
        e.preventDefault();
        if (!confirm('Confirmer la clôture de caisse ?')) return;
        post(route('admin.pos.caisse.close', register.id));
    }

    return (
        <form onSubmit={handleSubmit} className="mt-4 flex flex-wrap items-end gap-3">
            <div>
                <label className="label">Montant compté physiquement</label>
                <input
                    type="number"
                    step="0.01"
                    value={data.actual_closing_amount}
                    onChange={(e) => setData('actual_closing_amount', e.target.value)}
                    required
                    className="input w-48"
                />
            </div>
            <button type="submit" disabled={processing} className="btn-primary disabled:opacity-50">Clôturer</button>
        </form>
    );
}

function CorrectionForm({ register }) {
    const { data, setData, patch, processing } = useForm({
        opening_float: register.opening_float,
        actual_closing_amount: register.actual_closing_amount ?? '',
    });

    function handleSubmit(e) {
        e.preventDefault();
        patch(route('admin.pos.caisse.correct', register.id));
    }

    return (
        <form onSubmit={handleSubmit} className="mt-4 flex flex-wrap items-end gap-3 rounded-lg bg-terroir-cream/60 p-4">
            <div>
                <label className="label">Fond de caisse initial</label>
                <input
                    type="number"
                    step="0.01"
                    value={data.opening_float}
                    onChange={(e) => setData('opening_float', e.target.value)}
                    required
                    className="input w-40"
                />
            </div>
            <div>
                <label className="label">Montant compté</label>
                <input
                    type="number"
                    step="0.01"
                    value={data.actual_closing_amount}
                    onChange={(e) => setData('actual_closing_amount', e.target.value)}
                    required
                    className="input w-40"
                />
            </div>
            <button type="submit" disabled={processing} className="btn-primary disabled:opacity-50">Enregistrer la correction</button>
        </form>
    );
}

export default function Show({ register, sales }) {
    const [correcting, setCorrecting] = useState(false);

    function handleReopen() {
        if (!confirm('Rouvrir cette caisse pour ajouter un mouvement ou une vente oubliée ? Vous devrez la re-clôturer ensuite.')) return;
        router.post(route('admin.pos.caisse.reopen', register.id));
    }

    return (
        <AdminLayout title={`Caisse #${register.id}`}>
            <Head title={`Caisse #${register.id} — Administration`} />

            <div className="flex flex-wrap items-center justify-between gap-4">
                <div>
                    <h2 className="font-display text-2xl font-semibold">Caisse #{register.id}</h2>
                    <p className="text-sm text-terroir-dark/50">Ouverte par {register.opened_by_name} le {register.created_at}</p>
                </div>
                <div className="flex items-center gap-3">
                    {register.status === 'ouverte' ? (
                        <>
                            <span className="admin-badge-success px-4 py-1.5 text-sm">Ouverte</span>
                            <Link href={route('admin.pos.ventes.create')} className="btn-primary">Nouvelle vente</Link>
                        </>
                    ) : (
                        <span className="admin-badge-neutral px-4 py-1.5 text-sm">Fermée</span>
                    )}
                </div>
            </div>

            <div className="mt-6 grid grid-cols-2 gap-4 lg:grid-cols-4">
                <div className="admin-card"><p className="text-sm text-terroir-dark/50">Fond initial</p><p className="mt-1.5 text-xl font-bold">{fcfa(register.opening_float)}</p></div>
                <div className="admin-card"><p className="text-sm text-terroir-dark/50">Ventes espèces</p><p className="mt-1.5 text-xl font-bold text-terroir-green">{fcfa(register.cash_sales_total)}</p></div>
                <div className="admin-card"><p className="text-sm text-terroir-dark/50">Mouvements (+/-)</p><p className="mt-1.5 text-xl font-bold">{fcfa(register.movements_net)}</p></div>
                <div className="admin-card"><p className="text-sm text-terroir-dark/50">Solde théorique</p><p className="mt-1.5 text-xl font-bold text-terroir-terracotta">{fcfa(register.theoretical_cash)}</p></div>
            </div>

            <div className="mt-8 grid gap-6 lg:grid-cols-2">
                <div className="admin-card">
                    <h3 className="font-display text-base font-semibold">Ventes de la session</h3>
                    <div className="mt-3 flex flex-col gap-2">
                        {sales.length === 0 ? (
                            <p className="text-sm text-terroir-dark/50">Aucune vente pour le moment.</p>
                        ) : sales.map((sale) => (
                            <Link
                                key={sale.id}
                                href={route('admin.pos.ventes.show', sale.id)}
                                className="flex items-center justify-between gap-3 rounded-lg bg-terroir-cream/60 px-4 py-2.5 text-sm"
                            >
                                <span className="font-semibold">{sale.order_number}</span>
                                <span className="text-terroir-dark/50">{sale.customer_name}</span>
                                <span className="font-semibold text-terroir-green">{fcfa(sale.total)}</span>
                            </Link>
                        ))}
                    </div>
                </div>

                <div className="admin-card">
                    <h3 className="font-display text-base font-semibold">Mouvements de caisse</h3>
                    {register.status === 'ouverte' && <MovementForm register={register} />}

                    <div className="mt-3 flex flex-col gap-2">
                        {register.movements.length === 0 ? (
                            <p className="text-sm text-terroir-dark/50">Aucun mouvement.</p>
                        ) : register.movements.map((movement) => (
                            <div key={movement.id} className="flex items-center justify-between gap-3 rounded-lg bg-terroir-cream/60 px-4 py-2.5 text-sm">
                                <span>{movement.reason}</span>
                                <span className={'font-semibold ' + (movement.type === 'encaissement' ? 'text-terroir-green' : 'text-terroir-terracotta')}>
                                    {movement.type === 'encaissement' ? '+' : '-'}{fcfa(movement.amount)}
                                </span>
                            </div>
                        ))}
                    </div>
                </div>
            </div>

            {register.status === 'ouverte' ? (
                <div className="admin-card mt-8">
                    <h3 className="font-display text-base font-semibold">Clôturer la caisse</h3>
                    <p className="mt-1.5 text-sm text-terroir-dark/50">
                        Solde théorique attendu : <strong>{fcfa(register.theoretical_cash)}</strong>
                    </p>
                    <CloseForm register={register} />
                </div>
            ) : (
                <div className="admin-card mt-8">
                    <h3 className="font-display text-base font-semibold">Clôture</h3>
                    <dl className="mt-3 grid grid-cols-3 gap-4 text-sm">
                        <div><dt className="text-terroir-dark/50">Théorique</dt><dd className="font-semibold">{fcfa(register.theoretical_cash)}</dd></div>
                        <div><dt className="text-terroir-dark/50">Compté</dt><dd className="font-semibold">{fcfa(register.actual_closing_amount ?? 0)}</dd></div>
                        <div>
                            <dt className="text-terroir-dark/50">Écart</dt>
                            <dd className={'font-semibold ' + (register.variance !== 0 ? 'text-terroir-terracotta' : 'text-terroir-green')}>
                                {fcfa(register.variance ?? 0)}
                            </dd>
                        </div>
                    </dl>
                    <p className="mt-1.5 text-sm text-terroir-dark/50">Clôturée par {register.closed_by_name} le {register.closed_at}</p>

                    <div className="mt-4 flex flex-wrap gap-3 border-t border-terroir-dark/10 pt-4">
                        <button type="button" onClick={() => setCorrecting((v) => !v)} className="btn-outline">
                            {correcting ? 'Annuler la correction' : 'Corriger les montants'}
                        </button>
                        <button type="button" onClick={handleReopen} className="btn-outline text-terroir-terracotta">Rouvrir la caisse</button>
                    </div>

                    {correcting && (
                        <>
                            <CorrectionForm register={register} />
                            <p className="mt-1.5 text-sm text-terroir-dark/50">
                                Corrige uniquement les montants saisis (ne rouvre pas la caisse aux nouveaux mouvements).
                            </p>
                        </>
                    )}
                </div>
            )}
        </AdminLayout>
    );
}
