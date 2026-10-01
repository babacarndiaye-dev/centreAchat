import { Head, Link, useForm } from '@inertiajs/react';
import SiteLayout from '../../../Layouts/SiteLayout';

export default function QuotesCreate({ products }) {
    const { data, setData, post, processing, errors } = useForm({
        products: Object.fromEntries(products.map((p) => [p.id, 0])),
        notes: '',
    });

    function setQuantity(productId, quantity) {
        setData('products', { ...data.products, [productId]: quantity });
    }

    function submit(e) {
        e.preventDefault();
        post(route('compte.devis.store'));
    }

    return (
        <SiteLayout>
            <Head title="Demander un devis — DIABA HOTEL Produits du Sénégal (D.H.P.S)" />

            <section className="mx-auto max-w-3xl px-4 py-16 sm:px-6 lg:px-8">
                <span className="section-eyebrow">Espace professionnel</span>
                <h1 className="section-title mt-2">Demander un devis</h1>
                <p className="mt-3 text-terroir-dark/70">Sélectionnez les produits et quantités souhaités, notre équipe vous enverra une offre tarifaire adaptée.</p>

                <form onSubmit={submit} className="card mt-8 p-8">
                    {errors.products && <p className="mb-4 text-sm text-terroir-terracotta">{errors.products}</p>}

                    <div className="max-h-[420px] space-y-2 overflow-y-auto pr-2">
                        {products.map((product) => (
                            <div key={product.id} className="flex items-center justify-between gap-4 rounded-lg bg-terroir-cream/60 px-4 py-2.5">
                                <p className="text-sm font-medium">{product.name}</p>
                                <input
                                    type="number"
                                    value={data.products[product.id] ?? 0}
                                    min="0"
                                    onChange={(e) => setQuantity(product.id, Number(e.target.value))}
                                    className="input w-28 text-right"
                                />
                            </div>
                        ))}
                    </div>

                    <div className="mt-6">
                        <label className="label" htmlFor="notes">Précisions (optionnel)</label>
                        <textarea
                            id="notes"
                            rows="3"
                            value={data.notes}
                            onChange={(e) => setData('notes', e.target.value)}
                            className="input"
                        />
                    </div>

                    <div className="mt-8 flex gap-3">
                        <button type="submit" disabled={processing} className="btn-primary disabled:opacity-50">Envoyer la demande</button>
                        <Link href={route('compte.devis.index')} className="btn-outline">Annuler</Link>
                    </div>
                </form>
            </section>
        </SiteLayout>
    );
}
