import { Head, useForm } from '@inertiajs/react';
import AdminLayout from '../../../Layouts/AdminLayout';
import ProducerForm from './Form';

export default function Edit({ producer }) {
    const { data, setData, post, processing, errors } = useForm({
        name: producer.name,
        region: producer.region ?? '',
        description: producer.description ?? '',
        photo: null,
        is_featured: producer.is_featured,
        is_active: producer.is_active,
        _method: 'put',
    });

    function handleSubmit(e) {
        e.preventDefault();
        post(route('admin.producteurs.update', producer.id));
    }

    return (
        <AdminLayout title="Modifier le producteur">
            <Head title="Modifier le producteur — Administration" />
            <div className="admin-card max-w-[56rem]">
                <ProducerForm
                    data={data}
                    setData={setData}
                    errors={errors}
                    processing={processing}
                    onSubmit={handleSubmit}
                    submitLabel="Enregistrer"
                    photoUrl={producer.photo_url}
                />
            </div>
        </AdminLayout>
    );
}
