import { Head, useForm } from '@inertiajs/react';
import AdminLayout from '../../../Layouts/AdminLayout';
import ProductForm from './Form';

export default function Edit({ product, attributeValues, categories, producers, units, packagingTypes, attributes }) {
    const { data, setData, post, processing, errors } = useForm({
        category_id: product.category_id ?? '',
        producer_id: product.producer_id ?? '',
        name: product.name,
        short_description: product.short_description ?? '',
        description: product.description ?? '',
        origin: product.origin ?? '',
        unit: product.unit ?? 'kg',
        packaging_type_id: product.packaging_type_id ?? '',
        weight: product.weight ?? '',
        price: product.price,
        professional_price: product.professional_price ?? '',
        wholesale_price: product.wholesale_price ?? '',
        promo_price: product.promo_price ?? '',
        promo_starts_at: product.promo_starts_at ?? '',
        promo_ends_at: product.promo_ends_at ?? '',
        stock_quantity: product.stock_quantity,
        stock_alert_threshold: product.stock_alert_threshold,
        expiry_date: product.expiry_date ?? '',
        images: [],
        attributes: { ...attributeValues },
        is_featured: product.is_featured,
        is_new: product.is_new,
        is_active: product.is_active,
        _method: 'put',
    });

    function handleSubmit(e) {
        e.preventDefault();
        post(route('admin.produits.update', product.id));
    }

    return (
        <AdminLayout title="Modifier le produit">
            <Head title="Modifier le produit — Administration" />
            <div className="admin-card max-w-[56rem]">
                <ProductForm
                    data={data}
                    setData={setData}
                    errors={errors}
                    processing={processing}
                    onSubmit={handleSubmit}
                    submitLabel="Enregistrer"
                    categories={categories}
                    producers={producers}
                    units={units}
                    packagingTypes={packagingTypes}
                    attributes={attributes}
                    product={product}
                />
            </div>
        </AdminLayout>
    );
}
