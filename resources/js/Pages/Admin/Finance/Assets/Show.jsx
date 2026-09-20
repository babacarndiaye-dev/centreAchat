import { Head, Link, useForm } from '@inertiajs/react';
import AdminLayout from '../../../../Layouts/AdminLayout';

function fcfa(value) {
    return new Intl.NumberFormat('fr-FR').format(Math.round(value)) + ' FCFA';
}

function DisposeForm({ asset }) {
    const { data, setData, post, processing } = useForm({
        status: 'cede',
        disposal_date: new Date().toISOString().slice(0, 10),
        disposal_value: '',
    });

    function handleSubmit(e) {
        e.preventDefault();
        if (!confirm('Confirmer la mise hors service de cette immobilisation ?')) return;
        post(route('admin.immobilisations.dispose', asset.id));
    }

    return (
        <form onSubmit={handleSubmit} className="mt-4 flex flex-col gap-3">
            <select value={data.status} onChange={(e) => setData('status', e.target.value)} required className="input">
                <option value="cede">Cédé (vendu)</option>
                <option value="reforme">Réformé (mis au rebut)</option>
            </select>
            <input
                type="date"
                value={data.disposal_date}
                onChange={(e) => setData('disposal_date', e.target.value)}
                required
                className="input"
            />
            <input
                type="number"
                step="0.01"
                value={data.disposal_value}
                onChange={(e) => setData('disposal_value', e.target.value)}
                placeholder="Valeur de cession (FCFA, optionnel)"
                className="input"
            />
            <button type="submit" disabled={processing} className="btn-outline w-full justify-center disabled:opacity-50">Confirmer</button>
        </form>
    );
}

export default function Show({ asset, schedule, currentYear }) {
    return (
        <AdminLayout title={asset.name}>
            <Head title={`${asset.name} — Administration`} />

            <div className="flex flex-wrap items-center justify-between gap-4">
                <div>
                    <span className="text-xs font-semibold uppercase tracking-wide text-terroir-dark/50">{asset.category_label}</span>
                    <h2 className="mt-1 font-display text-2xl font-semibold">{asset.name}</h2>
                </div>
                <div className="flex items-center gap-3">
                    <span className={asset.status_badge_class + ' px-4 py-1.5 text-sm'}>{asset.status_label}</span>
                    <Link href={route('admin.immobilisations.edit', asset.id)} className="btn-outline">Modifier</Link>
                </div>
            </div>

            <div className="mt-6 grid grid-cols-2 gap-4 lg:grid-cols-4">
                <div className="admin-card">
                    <p className="text-xs text-terroir-dark/50">Valeur d'acquisition</p>
                    <p className="mt-1.5 text-xl font-bold">{fcfa(asset.acquisition_value)}</p>
                </div>
                <div className="admin-card">
                    <p className="text-xs text-terroir-dark/50">Amortissement annuel</p>
                    <p className="mt-1.5 text-xl font-bold">{fcfa(asset.annual_depreciation)}</p>
                </div>
                <div className="admin-card">
                    <p className="text-xs text-terroir-dark/50">Amorti à ce jour</p>
                    <p className="mt-1.5 text-xl font-bold text-terroir-terracotta">{fcfa(asset.accumulated_depreciation)}</p>
                </div>
                <div className="admin-card">
                    <p className="text-xs text-terroir-dark/50">Valeur nette comptable</p>
                    <p className="mt-1.5 text-xl font-bold text-terroir-green">{fcfa(asset.net_book_value)}</p>
                </div>
            </div>

            <div className="mt-6 grid gap-6 lg:grid-cols-3">
                <div className="lg:col-span-2">
                    <div className="admin-card">
                        <h3 className="font-display text-base font-semibold">Tableau d'amortissement ({asset.depreciation_method_label})</h3>
                        <table className="admin-table mt-3">
                            <thead>
                                <tr>
                                    <th>Année</th>
                                    <th className="text-right">Dotation annuelle</th>
                                    <th className="text-right">Cumul</th>
                                    <th className="text-right">VNC fin d'année</th>
                                </tr>
                            </thead>
                            <tbody>
                                {schedule.map((row) => (
                                    <tr
                                        key={row.year}
                                        className={currentYear === row.year && asset.status === 'en_service' ? 'bg-terroir-cream font-semibold' : ''}
                                    >
                                        <td>{row.year}</td>
                                        <td className="text-right">{row.annual.toLocaleString('fr-FR')}</td>
                                        <td className="text-right">{row.accumulated.toLocaleString('fr-FR')}</td>
                                        <td className="text-right">{row.net_value.toLocaleString('fr-FR')}</td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    </div>
                </div>

                <div>
                    <div className="admin-card">
                        <h3 className="font-display text-base font-semibold">Informations</h3>
                        <dl className="mt-3 space-y-2 text-sm">
                            <div>
                                <dt className="text-terroir-dark/50">Date d'acquisition</dt>
                                <dd className="mt-0.5 font-semibold">{asset.acquisition_date}</dd>
                            </div>
                            <div>
                                <dt className="text-terroir-dark/50">Durée d'amortissement</dt>
                                <dd className="mt-0.5 font-semibold">{asset.useful_life_years} ans</dd>
                            </div>
                            {asset.payment_account_name && (
                                <div>
                                    <dt className="text-terroir-dark/50">Financé par</dt>
                                    <dd className="mt-0.5 font-semibold">{asset.payment_account_name}</dd>
                                </div>
                            )}
                            {asset.supplier_name && (
                                <div>
                                    <dt className="text-terroir-dark/50">Fournisseur</dt>
                                    <dd className="mt-0.5 font-semibold">{asset.supplier_name}</dd>
                                </div>
                            )}
                            {asset.notes && (
                                <div>
                                    <dt className="text-terroir-dark/50">Notes</dt>
                                    <dd className="mt-0.5 font-semibold">{asset.notes}</dd>
                                </div>
                            )}
                        </dl>
                    </div>

                    {asset.status === 'en_service' ? (
                        <div className="admin-card mt-6">
                            <h3 className="font-display text-base font-semibold">Mettre hors service</h3>
                            <DisposeForm asset={asset} />
                        </div>
                    ) : (
                        <div className="admin-card mt-6 text-sm">
                            <p className="text-terroir-dark/50">{asset.status_label} le</p>
                            <p className="font-semibold">{asset.disposal_date ?? ''}</p>
                            {asset.disposal_value != null && (
                                <>
                                    <p className="mt-1.5 text-terroir-dark/50">Valeur de cession</p>
                                    <p className="font-semibold">{fcfa(asset.disposal_value)}</p>
                                </>
                            )}
                        </div>
                    )}
                </div>
            </div>
        </AdminLayout>
    );
}
