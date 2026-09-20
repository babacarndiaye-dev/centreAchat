import { useState } from 'react';
import { Head, Link, router } from '@inertiajs/react';
import { motion } from 'framer-motion';
import AdminLayout from '../../../Layouts/AdminLayout';

export default function Index({ products, filters }) {
    const [q, setQ] = useState(filters?.q ?? '');

    function handleSearch(e) {
        e.preventDefault();
        router.get(route('admin.produits.index'), q ? { q } : {}, { preserveState: true, replace: true });
    }

    function handleDelete(product) {
        if (!confirm('Supprimer ce produit ?')) return;
        router.delete(route('admin.produits.destroy', product.id), { preserveScroll: true });
    }

    return (
        <AdminLayout title="Produits">
            <Head title="Produits — Administration" />

            <div className="flex flex-wrap items-center justify-between gap-3">
                <form onSubmit={handleSearch} className="relative max-w-xs flex-1">
                    <input
                        type="text"
                        value={q}
                        onChange={(e) => setQ(e.target.value)}
                        placeholder="Rechercher un produit..."
                        className="input pl-9"
                    />
                    <span className="material-symbols-outlined absolute left-2.5 top-1/2 -translate-y-1/2 text-lg text-terroir-dark/40">search</span>
                </form>
                <Link href={route('admin.produits.create')} className="btn-primary">
                    <span className="material-symbols-outlined text-lg">add</span>
                    Nouveau produit
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
                            <th className="pl-6">Produit</th>
                            <th>Catégorie</th>
                            <th className="text-right">Prix</th>
                            <th className="text-right">Stock</th>
                            <th>Statut</th>
                            <th className="pr-6 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        {products.data.length === 0 ? (
                            <tr><td colSpan={6} className="py-8 text-center text-terroir-dark/40">Aucun produit.</td></tr>
                        ) : products.data.map((product) => (
                            <tr key={product.id}>
                                <td className="pl-6 font-semibold text-terroir-dark">{product.name}</td>
                                <td className="text-terroir-dark/60">{product.category?.name ?? '—'}</td>
                                <td className="text-right">{Math.round(product.price).toLocaleString('fr-FR')} FCFA</td>
                                <td className={'text-right ' + (product.stock_quantity <= product.stock_alert_threshold ? 'font-semibold text-terroir-terracotta' : '')}>
                                    {product.stock_quantity}
                                </td>
                                <td>
                                    {product.is_active ? (
                                        <span className="admin-badge-success">Actif</span>
                                    ) : (
                                        <span className="admin-badge-neutral">Inactif</span>
                                    )}
                                </td>
                                <td className="pr-6 text-right">
                                    <Link href={route('admin.produits.edit', product.id)} className="admin-link">Modifier</Link>
                                    <button type="button" onClick={() => handleDelete(product)} className="admin-link-danger ml-3 bg-transparent">
                                        Supprimer
                                    </button>
                                </td>
                            </tr>
                        ))}
                    </tbody>
                </table>
            </motion.div>

            {products.links.length > 3 && (
                <div className="mt-6 flex flex-wrap gap-1">
                    {products.links.map((link, i) =>
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
