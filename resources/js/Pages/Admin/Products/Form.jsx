import { Link, router } from '@inertiajs/react';

export default function ProductForm({
    data,
    setData,
    errors,
    processing,
    onSubmit,
    submitLabel,
    categories,
    producers,
    units,
    packagingTypes,
    attributes,
    product,
}) {
    function handleDeleteImage(imageId) {
        if (!confirm('Supprimer cette image ?')) return;
        router.delete(route('admin.produits.images.destroy', imageId), { preserveScroll: true });
    }

    return (
        <form onSubmit={onSubmit}>
            <div className="grid gap-4 sm:grid-cols-2">
                <div>
                    <label className="label" htmlFor="name">Nom du produit</label>
                    <input
                        type="text"
                        id="name"
                        value={data.name}
                        onChange={(e) => setData('name', e.target.value)}
                        required
                        className="input"
                    />
                    {errors.name && <p className="mt-1 text-xs text-terroir-terracotta">{errors.name}</p>}
                </div>
                <div>
                    <label className="label">Référence</label>
                    <input
                        type="text"
                        value={product?.reference ?? 'Générée automatiquement à la création'}
                        disabled
                        className={'input bg-terroir-cream text-terroir-dark/60' + (product?.reference ? '' : ' italic text-terroir-dark/40')}
                    />
                </div>
            </div>

            <div className="mt-6 grid gap-4 sm:grid-cols-2">
                <div>
                    <label className="label" htmlFor="category_id">Catégorie</label>
                    <select
                        id="category_id"
                        value={data.category_id}
                        onChange={(e) => setData('category_id', e.target.value)}
                        required
                        className="input"
                    >
                        <option value="">Choisir...</option>
                        {categories.map((category) => (
                            <option key={category.id} value={category.id}>{category.name}</option>
                        ))}
                    </select>
                    {errors.category_id && <p className="mt-1 text-xs text-terroir-terracotta">{errors.category_id}</p>}
                </div>
                <div>
                    <label className="label" htmlFor="producer_id">Producteur (optionnel)</label>
                    <select
                        id="producer_id"
                        value={data.producer_id ?? ''}
                        onChange={(e) => setData('producer_id', e.target.value)}
                        className="input"
                    >
                        <option value="">Aucun</option>
                        {producers.map((producer) => (
                            <option key={producer.id} value={producer.id}>{producer.name}</option>
                        ))}
                    </select>
                </div>
            </div>

            <div className="mt-6">
                <label className="label" htmlFor="short_description">Description courte</label>
                <input
                    type="text"
                    id="short_description"
                    value={data.short_description}
                    onChange={(e) => setData('short_description', e.target.value)}
                    className="input"
                />
            </div>

            <div className="mt-6">
                <label className="label" htmlFor="description">Description complète</label>
                <textarea
                    id="description"
                    rows={5}
                    value={data.description}
                    onChange={(e) => setData('description', e.target.value)}
                    className="input"
                />
            </div>

            <div className="mt-6 grid gap-4 sm:grid-cols-2 md:grid-cols-4">
                <div>
                    <label className="label" htmlFor="origin">Origine</label>
                    <input
                        type="text"
                        id="origin"
                        value={data.origin}
                        onChange={(e) => setData('origin', e.target.value)}
                        className="input"
                    />
                </div>
                <div>
                    <label className="label" htmlFor="unit">Unité</label>
                    <input
                        type="text"
                        id="unit"
                        list="units-datalist"
                        value={data.unit}
                        onChange={(e) => setData('unit', e.target.value)}
                        required
                        className="input"
                    />
                    <datalist id="units-datalist">
                        {units.map((unitOption) => (
                            <option key={unitOption.id} value={unitOption.name}>
                                {unitOption.abbreviation ? `(${unitOption.abbreviation})` : ''}
                            </option>
                        ))}
                    </datalist>
                    <Link href={route('admin.produits-parametres.unites.index')} className="mt-2 block text-sm text-terroir-green">
                        Gérer les unités
                    </Link>
                </div>
                <div>
                    <label className="label" htmlFor="packaging_type_id">Conditionnement (optionnel)</label>
                    <select
                        id="packaging_type_id"
                        value={data.packaging_type_id ?? ''}
                        onChange={(e) => setData('packaging_type_id', e.target.value)}
                        className="input"
                    >
                        <option value="">Aucun</option>
                        {packagingTypes.map((packaging) => (
                            <option key={packaging.id} value={packaging.id}>{packaging.name}</option>
                        ))}
                    </select>
                </div>
                <div>
                    <label className="label" htmlFor="weight">Poids (kg)</label>
                    <input
                        type="number"
                        step="0.001"
                        id="weight"
                        value={data.weight ?? ''}
                        onChange={(e) => setData('weight', e.target.value)}
                        className="input"
                    />
                </div>
            </div>

            {attributes.length > 0 && (
                <div className="mt-6 border-t border-terroir-dark/10 pt-6">
                    <h3 className="font-display text-base font-semibold">Attributs personnalisés</h3>
                    <div className="mt-3 grid gap-4 sm:grid-cols-2">
                        {attributes.map((attribute) => (
                            <div key={attribute.id}>
                                <label className="label" htmlFor={`attribute-${attribute.id}`}>{attribute.name}</label>
                                <input
                                    type="text"
                                    id={`attribute-${attribute.id}`}
                                    value={data.attributes[attribute.id] ?? ''}
                                    onChange={(e) => setData('attributes', { ...data.attributes, [attribute.id]: e.target.value })}
                                    className="input"
                                />
                            </div>
                        ))}
                    </div>
                    <Link href={route('admin.produits-parametres.attributs.index')} className="mt-2 block text-sm text-terroir-green">
                        Gérer les attributs
                    </Link>
                </div>
            )}

            <div className="mt-6 border-t border-terroir-dark/10 pt-6">
                <h3 className="font-display text-base font-semibold">Tarification</h3>
                <div className="mt-3 grid gap-4 sm:grid-cols-2 md:grid-cols-4">
                    <div>
                        <label className="label" htmlFor="price">Prix public (FCFA)</label>
                        <input
                            type="number"
                            step="0.01"
                            id="price"
                            value={data.price}
                            onChange={(e) => setData('price', e.target.value)}
                            required
                            className="input"
                        />
                        {errors.price && <p className="mt-1 text-xs text-terroir-terracotta">{errors.price}</p>}
                    </div>
                    <div>
                        <label className="label" htmlFor="professional_price">Prix professionnel</label>
                        <input
                            type="number"
                            step="0.01"
                            id="professional_price"
                            value={data.professional_price ?? ''}
                            onChange={(e) => setData('professional_price', e.target.value)}
                            className="input"
                        />
                    </div>
                    <div>
                        <label className="label" htmlFor="wholesale_price">Prix en gros</label>
                        <input
                            type="number"
                            step="0.01"
                            id="wholesale_price"
                            value={data.wholesale_price ?? ''}
                            onChange={(e) => setData('wholesale_price', e.target.value)}
                            className="input"
                        />
                    </div>
                    <div>
                        <label className="label" htmlFor="promo_price">Prix promotionnel</label>
                        <input
                            type="number"
                            step="0.01"
                            id="promo_price"
                            value={data.promo_price ?? ''}
                            onChange={(e) => setData('promo_price', e.target.value)}
                            className="input"
                        />
                    </div>
                    <div>
                        <label className="label" htmlFor="promo_starts_at">Début promo</label>
                        <input
                            type="datetime-local"
                            id="promo_starts_at"
                            value={data.promo_starts_at ?? ''}
                            onChange={(e) => setData('promo_starts_at', e.target.value)}
                            className="input"
                        />
                    </div>
                    <div>
                        <label className="label" htmlFor="promo_ends_at">Fin promo</label>
                        <input
                            type="datetime-local"
                            id="promo_ends_at"
                            value={data.promo_ends_at ?? ''}
                            onChange={(e) => setData('promo_ends_at', e.target.value)}
                            className="input"
                        />
                    </div>
                </div>
            </div>

            <div className="mt-6 border-t border-terroir-dark/10 pt-6">
                <h3 className="font-display text-base font-semibold">Stock</h3>
                <div className="mt-3 grid gap-4 sm:grid-cols-3">
                    <div>
                        <label className="label" htmlFor="stock_quantity">Quantité en stock</label>
                        <input
                            type="number"
                            id="stock_quantity"
                            value={data.stock_quantity}
                            onChange={(e) => setData('stock_quantity', e.target.value)}
                            required
                            className="input"
                        />
                    </div>
                    <div>
                        <label className="label" htmlFor="stock_alert_threshold">Seuil d'alerte</label>
                        <input
                            type="number"
                            id="stock_alert_threshold"
                            value={data.stock_alert_threshold}
                            onChange={(e) => setData('stock_alert_threshold', e.target.value)}
                            required
                            className="input"
                        />
                    </div>
                    <div>
                        <label className="label" htmlFor="expiry_date">Date d'expiration</label>
                        <input
                            type="date"
                            id="expiry_date"
                            value={data.expiry_date ?? ''}
                            onChange={(e) => setData('expiry_date', e.target.value)}
                            className="input"
                        />
                    </div>
                </div>
            </div>

            <div className="mt-6 border-t border-terroir-dark/10 pt-6">
                <h3 className="font-display text-base font-semibold">Images</h3>
                <input
                    type="file"
                    multiple
                    accept="image/*"
                    onChange={(e) => setData('images', Array.from(e.target.files))}
                    className="input mt-3 file:mr-3 file:rounded-full file:border-0 file:bg-terroir-green file:px-4 file:py-1.5 file:text-sm file:font-semibold file:text-white file:transition hover:file:bg-terroir-dark"
                />
                {errors.images && <p className="mt-1 text-xs text-terroir-terracotta">{errors.images}</p>}

                {product?.images?.length > 0 && (
                    <div className="mt-3 flex flex-wrap gap-3">
                        {product.images.map((image) => (
                            <div key={image.id} className="relative">
                                <img src={image.url} alt="" className="h-20 w-20 rounded-lg object-cover" />
                                <button
                                    type="button"
                                    onClick={() => handleDeleteImage(image.id)}
                                    className="absolute -right-2 -top-2 flex h-[22px] w-[22px] items-center justify-center rounded-full bg-terroir-terracotta text-white"
                                >
                                    <span className="material-symbols-outlined" style={{ fontSize: '14px' }}>close</span>
                                </button>
                            </div>
                        ))}
                    </div>
                )}
            </div>

            <div className="mt-6 flex flex-wrap gap-6 border-t border-terroir-dark/10 pt-6">
                <label className="flex items-center gap-2 text-sm">
                    <input
                        type="checkbox"
                        checked={data.is_featured}
                        onChange={(e) => setData('is_featured', e.target.checked)}
                        className="rounded border-terroir-green/30 text-terroir-green focus:ring-terroir-green/20"
                    />
                    Produit vedette
                </label>
                <label className="flex items-center gap-2 text-sm">
                    <input
                        type="checkbox"
                        checked={data.is_new}
                        onChange={(e) => setData('is_new', e.target.checked)}
                        className="rounded border-terroir-green/30 text-terroir-green focus:ring-terroir-green/20"
                    />
                    Nouveauté
                </label>
                <label className="flex items-center gap-2 text-sm">
                    <input
                        type="checkbox"
                        checked={data.is_active}
                        onChange={(e) => setData('is_active', e.target.checked)}
                        className="rounded border-terroir-green/30 text-terroir-green focus:ring-terroir-green/20"
                    />
                    Actif
                </label>
            </div>

            <div className="mt-6 flex gap-3">
                <button type="submit" disabled={processing} className="btn-primary disabled:opacity-50">{submitLabel}</button>
                <Link href={route('admin.produits.index')} className="btn-outline">Annuler</Link>
            </div>
        </form>
    );
}
