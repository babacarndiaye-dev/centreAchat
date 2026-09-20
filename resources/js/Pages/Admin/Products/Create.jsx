import { Head, useForm } from '@inertiajs/react';
import AdminLayout from '../../../Layouts/AdminLayout';
import ProductForm from './Form';

export default function Create({ categories, producers, units, packagingTypes, attributes }) {
    const { data, setData, post, processing, errors } = useForm({
        category_id: '',
        producer_id: '',
        name: '',
        short_description: '',
        description: '',
        origin: '',
        unit: 'kg',
        packaging_type_id: '',
        weight: '',
        price: '',
        professional_price: '',
        wholesale_price: '',
        promo_price: '',
        promo_starts_at: '',
        promo_ends_at: '',
        stock_quantity: 0,
        stock_alert_threshold: 5,
        expiry_date: '',
        images: [],
        attributes: {},
        is_featured: false,
        is_new: false,
        is_active: true,
    });

    function handleSubmit(e) {
        e.preventDefault();
        post(route('admin.produits.store'));
    }

    return (
        <AdminLayout title="Nouveau produit">
            <Head title="Nouveau produit — Administration" />
            <div className="admin-card max-w-[56rem]">
                <ProductForm
                    data={data}
                    setData={setData}
                    errors={errors}
                    processing={processing}
                    onSubmit={handleSubmit}
                    submitLabel="Créer"
                    categories={categories}
                    producers={producers}
                    units={units}
                    packagingTypes={packagingTypes}
                    attributes={attributes}
                />
            </div>
        </AdminLayout>
    );
}
