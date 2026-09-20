import { Head, Link, router, useForm } from '@inertiajs/react';
import { motion } from 'framer-motion';
import AdminLayout from '../../../Layouts/AdminLayout';

export default function Create({ products }) {
    const initialProducts = Object.fromEntries(products.map((p) => [p.id, 0]));
    const { data, setData, processing, errors } = useForm({
        needed_by_date: '',
        reason: '',
        products: initialProducts,
    });

    function setQuantity(productId, value) {
        setData('products', { ...data.products, [productId]: value });
    }

    function handleSubmit(e, submitForValidation) {
        e.preventDefault();
        router.post(route('admin.demandes-achat.store'), { ...data, submit_for_validation: submitForValidation }, {
            preserveScroll: true,
        });
    }

    return (
        <AdminLayout title="Nouvelle demande d'achat">
            <Head title="Nouvelle demande d'achat — Administration" />

            <motion.div
                initial={{ opacity: 0, y: 12 }}
                animate={{ opacity: 1, y: 0 }}
                transition={{ duration: 0.35 }}
                className="admin-card max-w-3xl"
            >
                <form onSubmit={(e) => handleSubmit(e, false)}>
                    <div className="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label className="label">Besoin pour le (optionnel)</label>
                            <input
                                type="date"
                                value={data.needed_by_date}
                                onChange={(e) => setData('needed_by_date', e.target.value)}
                                className="input"
                            />
                        </div>
                    </div>

                    <div className="mt-4">
                        <label className="label">Motif de la demande</label>
                        <textarea rows={2} value={data.reason} onChange={(e) => setData('reason', e.target.value)} className="input" />
                    </div>

                    <div className="mt-6 border-t border-terroir-dark/10 pt-6">
                        <h3 className="font-display text-base font-semibold">Produits nécessaires</h3>
                        <p className="text-sm text-terroir-dark/50">Indiquez une quantité pour chaque produit concerné, laissez à 0 sinon.</p>

                        {errors.products && <p className="mt-1.5 text-xs text-terroir-terracotta">{errors.products}</p>}

                        <div className="mt-4 flex max-h-[420px] flex-col gap-2 overflow-y-auto pr-2">
                            {products.map((product) => (
                                <div key={product.id} className="flex items-center justify-between gap-4 rounded-lg bg-terroir-cream/80 px-4 py-2.5">
                                    <div>
                                        <p className="text-sm font-semibold">{product.name}</p>
                                        <p className="text-sm text-terroir-dark/50">Stock actuel : {product.stock_quantity} {product.unit}</p>
                                    </div>
                                    <input
                                        type="number"
                                        min={0}
                                        value={data.products[product.id]}
                                        onChange={(e) => setQuantity(product.id, e.target.value)}
                                        className="input w-28 text-right"
                                    />
                                </div>
                            ))}
                        </div>
                    </div>

                    <div className="mt-6 flex flex-wrap gap-3">
                        <button type="submit" disabled={processing} className="btn-outline disabled:opacity-50">Enregistrer en brouillon</button>
                        <button type="button" disabled={processing} onClick={(e) => handleSubmit(e, true)} className="btn-primary disabled:opacity-50">
                            Soumettre pour validation
                        </button>
                        <Link href={route('admin.demandes-achat.index')} className="btn-outline">Annuler</Link>
                    </div>
                </form>
            </motion.div>
        </AdminLayout>
    );
}
