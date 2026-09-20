import { Head } from '@inertiajs/react';
import SiteLayout from '../../Layouts/SiteLayout';

function formatFcfa(amount) {
    return new Intl.NumberFormat('fr-FR').format(Math.round(amount)) + ' FCFA';
}

export default function CheckoutConfirmation({ order }) {
    return (
        <SiteLayout>
            <Head title="Commande confirmée — Central d'Achat" />

            <section className="mx-auto max-w-2xl px-4 py-24 text-center sm:px-6 lg:px-8">
                <div className="mx-auto flex h-20 w-20 items-center justify-center rounded-full bg-terroir-green text-4xl text-white">✓</div>
                <h1 className="section-title mt-6">Merci {order.customer_name} !</h1>
                <p className="mt-3 text-terroir-dark/70">
                    Votre commande <strong>{order.order_number}</strong> a bien été enregistrée. Nous vous contacterons rapidement au {order.customer_phone}.
                </p>

                <div className="card mt-10 p-6 text-left">
                    <ul className="space-y-3 text-sm">
                        {order.items.map((item) => (
                            <li key={item.id} className="flex justify-between">
                                <span>{item.quantity} × {item.product_name}</span>
                                <span className="font-medium">{formatFcfa(item.total)}</span>
                            </li>
                        ))}
                    </ul>
                    <div className="mt-6 space-y-2 border-t border-terroir-green/10 pt-4 text-sm">
                        <div className="flex justify-between"><span className="text-terroir-dark/60">Sous-total</span><span>{formatFcfa(order.subtotal)}</span></div>
                        <div className="flex justify-between"><span className="text-terroir-dark/60">Livraison</span><span>{Number(order.delivery_fee) > 0 ? formatFcfa(order.delivery_fee) : 'Offerte'}</span></div>
                        <div className="flex justify-between text-base font-bold text-terroir-green"><span>Total</span><span>{formatFcfa(order.total)}</span></div>
                    </div>
                </div>

                <a href={route('produits.index')} className="btn-primary mt-10 inline-block">Continuer mes achats</a>
            </section>
        </SiteLayout>
    );
}
