import { Fragment } from 'react';
import { Head, Link } from '@inertiajs/react';

function methodLabel(method) {
    return method.replace(/_/g, ' ').replace(/^./, (c) => c.toUpperCase());
}

export default function Receipt({ order, siteAddress, sitePhone }) {
    return (
        <div className="mx-auto max-w-xs bg-white p-6 text-terroir-dark">
            <Head title={`Ticket ${order.order_number}`} />
            <style>{'@media print { .no-print { display: none; } }'}</style>

            <div className="no-print mb-4 flex items-center justify-between">
                <Link href={route('admin.pos.ventes.create')} className="text-sm font-semibold text-terroir-green">← Nouvelle vente</Link>
                <button type="button" onClick={() => window.print()} className="btn-primary px-4 py-1.5 text-xs">Imprimer</button>
            </div>

            <div className="text-center">
                <p className="font-display text-lg font-semibold text-terroir-green">Central d'Achat</p>
                {siteAddress && <p className="text-sm text-terroir-dark/50">{siteAddress}</p>}
                {sitePhone && <p className="text-sm text-terroir-dark/50">{sitePhone}</p>}
            </div>

            <div className="mt-3 border-t border-dashed border-terroir-dark/30 pt-3 text-sm">
                <p>Ticket : {order.order_number}</p>
                <p>Date : {order.created_at}</p>
                <p>Client : {order.customer_name}</p>
            </div>

            <table className="mt-3 w-full border-t border-dashed border-terroir-dark/30 pt-2 text-sm">
                <tbody>
                    {order.items.map((item) => (
                        <Fragment key={item.id}>
                            <tr>
                                <td className="py-1" colSpan={2}>{item.product_name}</td>
                            </tr>
                            <tr className="text-terroir-dark/50">
                                <td>{item.quantity} × {item.unit_price.toLocaleString('fr-FR')}</td>
                                <td className="text-right font-semibold text-terroir-dark">{item.total.toLocaleString('fr-FR')}</td>
                            </tr>
                        </Fragment>
                    ))}
                </tbody>
            </table>

            <div className="mt-3 flex justify-between border-t border-dashed border-terroir-dark/30 pt-2 text-base font-bold text-terroir-green">
                <span>Total</span><span>{order.total.toLocaleString('fr-FR')} FCFA</span>
            </div>

            <div className="mt-3 border-t border-dashed border-terroir-dark/30 pt-2 text-sm">
                {order.payments.map((payment) => (
                    <div key={payment.id} className="flex justify-between">
                        <span>{methodLabel(payment.method)}</span>
                        <span>{payment.amount.toLocaleString('fr-FR')} FCFA</span>
                    </div>
                ))}
            </div>

            <p className="mt-4 text-center text-sm text-terroir-dark/50">Merci de votre confiance !</p>
        </div>
    );
}
